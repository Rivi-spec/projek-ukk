<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_products($limit = null, $offset = null)
    {
        if ($limit != null && $offset != null) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get('products')->result();
    }

    public function best_deal_product()
    {
        $data = $this->db->where('is_available', 1)
            ->order_by('current_discount', 'DESC')
            ->limit(1)
            ->get('products')
            ->row();

        return $data;
    }

    public function is_product_exist($id, $sku = null)
    {
        if ($sku == null) {
            return ($this->db->where('id', $id)->get('products')->num_rows() > 0) ? TRUE : FALSE;
        }
        return ($this->db->where(array('id' => $id, 'sku' => $sku))->get('products')->num_rows() > 0) ? TRUE : FALSE;
    }

    public function product_data($id)
    {
        $data = $this->db->query("
            SELECT p.*, pc.name as category_name
            FROM products p
            JOIN product_category pc
                ON pc.id = p.category_id
            WHERE p.id = '$id'
        ")->row();

        return $data;
    }

    public function related_products($current, $category)
    {
        return $this->db->where(array('id !=' => $current, 'category_id' => $category))->limit(4)->get('products')->result();
    }

    public function create_order($order)
    {
        $this->db->insert('orders', $order);
        return $this->db->insert_id();
    }

    public function create_order_items($items)
    {
        $this->db->insert_batch('order_items', $items);
    }

    public function update_product_stock($product_id, $product)
    {
        $this->db->where('id', $product_id);
        $this->db->update('products', [
            'stock_s' => $product->stock_s,
            'stock_m' => $product->stock_m,
            'stock_l' => $product->stock_l,
            'stock_xl' => $product->stock_xl
        ]);
    }

    public function update_product_total_stock($product_id, $quantity)
    {
        $this->db->set('stock', 'stock - ' . (int)$quantity, FALSE);
        $this->db->where('id', $product_id);
        $this->db->update('products');
    }

    public function add_new_product($product)
    {
        $this->db->insert('products', $product);
    }

    public function get_product_by_id($product_id)
    {
        return $this->db->get_where('products', array('id' => $product_id))->row();
    }

    public function get_stock_by_size($product_id, $size)
    {
        $stock_field = 'stock_' . strtolower($size);
        $this->db->select($stock_field);
        $this->db->from('products');
        $this->db->where('id', $product_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->$stock_field;
        }

        return 0; // Jika barang tidak ditemukan, kembalikan 0
    }

    public function update_stock($product_id, $size, $quantity)
    {
        $stock_field = 'stock_' . strtolower($size);
        $this->db->set($stock_field, "$stock_field - $quantity", FALSE);
        $this->db->where('id', $product_id);
        $this->db->update('products');
    }

    public function reduce_stock($product_id, $size, $quantity)
    {
        // Clean dan standardisasi ukuran
        $size = trim(strtolower($size));
        
        // Tentukan field stok berdasarkan ukuran
        $valid_sizes = ['s', 'm', 'l', 'xl']; // Daftar ukuran yang valid
        
        // Jika ukuran tidak valid, gunakan ukuran default
        if (!in_array($size, $valid_sizes)) {
            $size = 'xl'; // Atau ukuran default lainnya
            log_message('error', "Invalid size detected: '$size'. Using default size instead."); 
        }
        
        // Siapkan nama field stok
        $stock_field = 'stock_' . $size;
        
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
    }

    public function reduce_stock_auto($product_id, $quantity_xl)
    {
        // Kurangi stok XL
        $this->db->set('stock_xl', "stock_xl - $quantity_xl", FALSE);
        
        // Kurangi juga total stok dengan jumlah yang sama
        $this->db->set('stock', "stock - $quantity_xl", FALSE);
        
        $this->db->where('id', $product_id);
        $this->db->update('products');
        
        // Pastikan stok tidak negatif
        $this->db->query("
            UPDATE products 
            SET stock_xl = 0, stock = (stock + $quantity_xl - $quantity_xl) 
            WHERE id = $product_id AND stock_xl < 0
        ");
        
        return TRUE;
    }

    public function count_all_products()
    {
        return $this->db->count_all('products');
    }

    public function search_products($query, $limit, $offset)
    {
        $this->db->like('name', $query);
        $this->db->or_like('description', $query);
        $this->db->or_like('sku', $query);
        
        if ($limit != null && $offset != null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get('products')->result();
    }

    public function count_search($query)
    {
        $this->db->like('name', $query);
        $this->db->or_like('description', $query);
        $this->db->or_like('sku', $query);
        
        return $this->db->count_all_results('products');
    }

    public function get_all_categories()
    {
        return $this->db->get('product_category')->result();
    }

    public function category_data($id)
    {
        return $this->db->where('id', $id)->get('product_category')->row();
    }

    public function add_category($name)
    {
        $this->db->insert('product_category', array('name' => $name));
    }

    public function delete_category($id)
    {
        $this->db->where('id', $id)->delete('product_category');
    }

    public function edit_category($id, $name)
    {
        $this->db->where('id', $id)->update('product_category', array('name' => $name));
    }

    public function delete_product($id)
    {
        $this->db->where('id', $id)->delete('products');
    }

    public function edit_product($id, $product)
    {
        $this->db->where('id', $id)->update('products', $product);
    }

    public function is_product_have_image($id)
    {
        $data = $this->db->where('id', $id)->get('products')->row();
        
        return ($data && $data->picture_name) ? TRUE : FALSE;
    }

    public function delete_product_image($id)
    {
        $this->db->set('picture_name', NULL)
            ->where('id', $id)
            ->update('products');
    }

    public function get_all_coupons()
    {
        return $this->db->get('coupons')->result();
    }

    public function coupon_data($id)
    {
        return $this->db->where('id', $id)->get('coupons')->row();
    }

    public function add_coupon($coupon)
    {
        $this->db->insert('coupons', $coupon);
    }

    public function delete_coupon($id)
    {
        $this->db->where('id', $id)->delete('coupons');
    }

    public function edit_coupon($id, $coupon)
    {
        $this->db->where('id', $id)->update('coupons', $coupon);
    }
}