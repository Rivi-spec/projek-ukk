<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Email $email
 * @property Contact_model $contact
 * @property Review_model $review
 * @property Customer_model $customer
 */

class Shop extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('cart');
        $this->load->model('product_model', 'product');
        $this->load->model('customer_model', 'customer');

        $this->load->model(array(
            'product_model' => 'product',
            'customer_model' => 'customer'

        ));
    }

    public function product($id = 0, $sku = '')
    {
        if ($id == 0 || empty($sku)) {
            show_error('Akses tidak sah!');
        } else {
            if ($this->product->is_product_exist($id, $sku)) {
                $data = $this->product->product_data($id);

                $product['product'] = $data;
                $product['related_products'] = $this->product->related_products($data->id, $data->category_id);

                get_header($data->name . ' | ' . get_settings('store_tagline'));
                get_template_part('shop/view_single_product', $product);
                get_footer();
            } else {
                show_404();
            }
        }
    }

    public function cart()
    {
        $carts = $this->cart->contents();  // Mengambil isi keranjang belanja
        $total_cart = $this->cart->total();  // Mengambil total harga keranjang belanja

        // Menambahkan data ongkir (jika ada) atau biaya pengiriman
        $ongkir = ($total_cart >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');
        $total_price = $total_cart + $ongkir;

        // Meneruskan data ke view
        $cart['carts'] = $carts;
        $cart['total_cart'] = $total_cart;
        $cart['total_price'] = $total_price;

        // Memuat tampilan dengan data yang diteruskan
        get_header('Keranjang Belanja');
        get_template_part('shop/cart', $cart);
        get_footer();
    }

    public function add_to_cart()
    {
        $product_id = $this->input->post('product_id');
        $size = $this->input->post('size');
        $quantity = $this->input->post('quantity');

        $product = $this->product->get_product_by_id($product_id);

        // Debugging
        log_message('debug', 'add_to_cart: product_id=' . $product_id . ', size=' . $size . ', quantity=' . $quantity);

        // Check stock availability
        $stock_field = 'stock_' . strtolower($size);
        if ($product->$stock_field < $quantity) {
            $this->session->set_flashdata('error', 'Stok tidak mencukupi untuk ukuran yang dipilih.');
            redirect('shop/view_product/' . $product_id);
        }

        // Add to cart
        $data = array(
            'id'      => $product_id,
            'qty'     => $quantity,
            'price'   => $product->price,
            'name'    => $product->name,
            'options' => array('size' => $size)
        );

        $this->cart->insert($data);

        // Update stock
        $this->product->reduce_stock($product_id, $size, $quantity);

        redirect('shop/cart');
    }

    public function checkout($action = '')
    {
        if (!is_login()) {
            $coupon = $this->input->post('coupon_code');
            $quantity = $this->input->post('quantity');

            $this->session->set_userdata('_temp_coupon', $coupon);
            $this->session->set_userdata('_temp_quantity', $quantity);

            verify_session('customer');
        }

        switch ($action) {
            default:
                $coupon = $this->input->post('coupon_code') ? $this->input->post('coupon_code') : $this->session->userdata('_temp_coupon');
                $quantity = $this->input->post('quantity') ? $this->input->post('quantity') : $this->session->userdata('_temp_quantity');

                if ($this->session->userdata('_temp_quantity') || $this->session->userdata('_temp_coupon')) {
                    $this->session->unset_userdata('_temp_coupon');
                    $this->session->unset_userdata('_temp_quantity');
                }

                $items = [];

                foreach ($quantity as $rowid => $qty) {
                    $items['rowid'] = $rowid;
                    $items['qty'] = $qty;
                }

                $this->cart->update($items);

                if (empty($coupon)) {
                    $discount = 0;
                    $disc = 'Tidak menggunkan kupon';
                } else {
                    if ($this->customer->is_coupon_exist($coupon)) {
                        if ($this->customer->is_coupon_active($coupon)) {
                            if ($this->customer->is_coupon_expired($coupon)) {
                                $discount = 0;
                                $disc = 'Kupon kadaluarsa';
                            } else {
                                $coupon_id = $this->customer->get_coupon_id($coupon);
                                $this->session->set_userdata('coupon_id', $coupon_id);

                                $credit = $this->customer->get_coupon_credit($coupon);
                                $discount = $credit;
                                $disc = '<span class="badge badge-success">' . $coupon . '</span> Rp ' . format_rupiah($credit);
                            }
                        } else {
                            $discount = 0;
                            $disc = 'Kupon sudah tidak aktif';
                        }
                    } else {
                        $discount = 0;
                        $disc = 'Kupon tidak terdaftar';
                    }
                }

                $items = [];

                foreach ($this->cart->contents() as $item) {
                    $items[$item['id']]['qty'] = $item['qty'];
                    $items[$item['id']]['price'] = $item['price'];
                    $items[$item['id']]['size'] = isset($item['options']['size']) ? $item['options']['size'] : ''; // Ensure size is set
                }

                $subtotal = $this->cart->total();
                $ongkir = (int) ($subtotal >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');

                $params['customer'] = $this->customer->data();
                $params['subtotal'] = $subtotal;
                $params['ongkir'] = ($ongkir > 0) ? 'Rp' . format_rupiah($ongkir) : 'Gratis';
                $params['total'] = $subtotal + $ongkir - $discount;
                $params['discount'] = $disc;

                $this->session->set_userdata('order_quantity', $items);
                $this->session->set_userdata('total_price', $params['total']);

                get_header('Checkout');
                get_template_part('shop/checkout', $params);
                get_footer();
                break;
            case 'order':
                $quantity = $this->session->userdata('order_quantity');

                $user_id = get_current_user_id();
                $coupon_id = $this->session->userdata('coupon_id');
                $order_number = $this->_create_order_number($quantity, $user_id, $coupon_id);
                $order_date = date('Y-m-d H:i:s');
                $total_price = $this->session->userdata('total_price');
                $total_items = count($quantity);
                $payment = $this->input->post('payment');

                $name = $this->input->post('name');
                $phone_number = $this->input->post('phone_number');
                $address = $this->input->post('address');
                $note = $this->input->post('note');

                $delivery_data = array(
                    'customer' => array(
                        'name' => $name,
                        'phone_number' => $phone_number,
                        'address' => $address
                    ),
                    'note' => $note
                );

                $delivery_data = json_encode($delivery_data);

                $order = array(
                    'user_id' => $user_id,
                    'coupon_id' => $coupon_id,
                    'order_number' => $order_number,
                    'order_status' => 1,
                    'order_date' => $order_date,
                    'total_price' => $total_price,
                    'total_items' => $total_items,
                    'payment_method' => $payment,
                    'delivery_data' => $delivery_data
                );

                $order = $this->product->create_order($order);

                $n = 0;
                foreach ($quantity as $id => $data) {
                    $items[$n]['order_id'] = $order;
                    $items[$n]['product_id'] = $id;
                    $items[$n]['order_qty'] = $data['qty'];
                    $items[$n]['order_price'] = $data['price'];

                    $n++;
                }

                $this->product->create_order_items($items);

                // Reduce stock for each item in the order
                foreach ($quantity as $id => $data) {
                    $size = isset($data['size']) ? $data['size'] : ''; // Ensure size is set
                    $qty = $data['qty'];

                    // Debugging
                    log_message('debug', 'checkout: product_id=' . $id . ', size=' . $size . ', quantity=' . $qty);

                    $this->product->reduce_stock($id, $size, $qty);
                }

                $this->cart->destroy();
                $this->session->unset_userdata('order_quantity');
                $this->session->unset_userdata('total_price');
                $this->session->unset_userdata('coupon_id');

                $this->session->set_flashdata('order_flash', 'Order berhasil ditambahkan');

                redirect('customer/orders/view/' . $order);
                break;
        }
    }

    public function update_size()
    {
        $rowid = $this->input->post('rowid');
        $size = $this->input->post('size');

        $data = array(
            'rowid' => $rowid,
            'options' => array('size' => $size)
        );

        $this->cart->update($data);

        echo json_encode(array('code' => 200));
    }

    public function cart_api()
    {
        $action = $this->input->get('action');

        switch ($action) {
            case 'add_item':
                $id = $this->input->post('id');
                $qty = $this->input->post('qty');
                $sku = $this->input->post('sku');
                $name = $this->input->post('name');
                $price = $this->input->post('price');

                $item = array(
                    'id' => $id,
                    'qty' => $qty,
                    'price' => $price,
                    'name' => $name
                );
                $this->cart->insert($item);
                $total_item = count($this->cart->contents());

                $response = array('code' => 200, 'message' => 'Item dimasukkan dalam keranjang', 'total_item' => $total_item);
                break;
            case 'display_cart':
                $carts = [];

                foreach ($this->cart->contents() as $items) {
                    $carts[$items['rowid']]['id'] = $items['id'];
                    $carts[$items['rowid']]['name'] = $items['name'];
                    $carts[$items['rowid']]['qty'] = $items['qty'];
                    $carts[$items['rowid']]['price'] = $items['price'];
                    $carts[$items['rowid']]['subtotal'] = $items['subtotal'];
                }

                $response = array('code' => 200, 'carts' => $carts);
                break;
            case 'cart_info':
                $total_price = $this->cart->total();
                $total_item = count($this->cart->contents());

                $data['total_price'] = $total_price;
                $data['total_item'] = $total_item;

                $response['data'] = $data;
                break;
            case 'remove_item':
                $rowid = $this->input->post('rowid');

                $this->cart->remove($rowid);

                $total_price = $this->cart->total();
                $ongkir = (int) ($total_price >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');
                $data['code'] = 204;
                $data['message'] = 'Item dihapus dari keranjang';
                $data['total']['subtotal'] = 'Rp ' . format_rupiah($total_price);
                $data['total']['ongkir'] = ($ongkir > 0) ? 'Rp ' . format_rupiah($ongkir) : 'Gratis';
                $data['total']['total'] = 'Rp ' . format_rupiah($total_price + $ongkir);

                $response = $data;
                break;
        }

        $response = json_encode($response);
        $this->output->set_content_type('application/json')
            ->set_output($response);
    }

    public function _create_order_number($quantity, $user_id, $coupon_id)
    {
        $this->load->helper('string');

        $alpha = strtoupper(random_string('alpha', 3));
        $num = random_string('numeric', 3);
        $count_qty = count($quantity);


        $number = $alpha . date('j') . date('n') . date('y') . $count_qty . $user_id . $coupon_id . $num;
        //Random 3 letter . Date . Month . Year . Quantity . User ID . Coupon Used . Numeric

        return $number;
    }

    public function update_stock()
    {
        $rowid = $this->input->post('rowid');
        $size = $this->input->post('size');
        $quantity = $this->input->post('quantity');

        // Ambil item berdasarkan rowid
        $cart_item = $this->cart->get_item($rowid);

        // Update stok berdasarkan ukuran
        $product_id = $cart_item['id'];
        $this->load->model('product_model');
        $product = $this->product_model->get_product_by_id($product_id);

        // Kurangi stok berdasarkan ukuran
        if ($size == 'S') {
            $product->stock_s -= $quantity;
        } elseif ($size == 'M') {
            $product->stock_m -= $quantity;
        } elseif ($size == 'L') {
            $product->stock_l -= $quantity;
        } elseif ($size == 'XL') {
            $product->stock_xl -= $quantity;
        }

        // Simpan perubahan stok ke database
        $this->product_model->update_product_stock($product_id, $product);

        // Kirim respons
        echo json_encode(['code' => 200]);
    }


    public function process_order($order_id)
    {
        // Fetch the order details, make sure to include the product IDs and quantities
        $order = $this->order_model->get_order_details($order_id);

        if ($order) {
            foreach ($order as $item) {
                // Reduce the stock for each product ordered
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $this->product_model->reduce_stock($product_id, $quantity);
            }

            // Mark the order as completed (or do whatever final actions needed)
            $this->order_model->mark_order_as_completed($order_id);
        }
    }
    public function reduce_stock($product_id, $size, $quantity)
    {
        try {
            // Mulai transaction untuk operasi ini
            $this->db->trans_begin();

            // Tentukan field stok berdasarkan ukuran
            $valid_sizes = ['s', 'm', 'l', 'xl']; // Daftar ukuran yang valid
            if (!in_array(strtolower($size), $valid_sizes)) {
                throw new Exception("Invalid size: $size"); // Jika ukuran tidak valid, lempar error
            }

            // Siapkan nama field stok
            $stock_field = 'stock_' . strtolower($size);

            // Periksa apakah field stok ada di database
            if (!in_array($stock_field, ['stock_s', 'stock_m', 'stock_l', 'stock_xl'])) {
                throw new Exception("Invalid stock field: $stock_field");
            }

            // Mengurangi stok berdasarkan field dan quantity yang diberikan
            $this->db->set($stock_field, "$stock_field - $quantity", FALSE);

            // Mengurangi juga total stok
            $this->db->set('stock', "stock - $quantity", FALSE);

            $this->db->where('id', $product_id);
            $this->db->update('products');

            // Tambahkan pengecekan untuk memastikan stok tidak negatif
            $this->db->query("
                UPDATE products 
                SET $stock_field = 0, stock = (stock + $quantity - $quantity) 
                WHERE id = $product_id AND $stock_field < 0
            ");

            // Commit transaction jika semua berhasil
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Error updating stock for product ID: $product_id");
            } else {
                $this->db->trans_commit();
                return TRUE;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            throw $e; // Re-throw exception untuk ditangani di controller
        }
    }
}
