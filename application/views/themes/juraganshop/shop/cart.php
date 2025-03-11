<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="hero-wrap hero-bread" style="background-image: url('<?php echo get_theme_uri('images/background_01.jpg'); ?>');">
  <div class="container">
    <div class="row no-gutters slider-text align-items-center justify-content-center">
      <div class="col-md-9 ftco-animate text-center">
        <p class="breadcrumbs"><span class="mr-2"><?php echo anchor(base_url(), 'Home'); ?></span> <span>Keranjang Belanja</span></p>
        <h1 class="mb-0 bread">Keranjang Belanja Saya</h1>
      </div>
    </div>
  </div>
</div>

<section class="ftco-section ftco-Keranjang Belanja">
  <div class="container">
    <?php if (count($carts) > 0) : ?>
      <form action="<?php echo site_url('shop/checkout'); ?>" method="POST">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <div class="cart-list">
              <table class="table">
                <thead class="thead-primary">
                  <tr class="text-center" style="background-color: #ff0000;">
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Kuantitas</th>
                    <th>Ukuran</th>
                    <th>Sub Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($carts as $item) : ?>
                    <tr class="text-center cart-<?php echo $item['rowid']; ?>">
                      <td class="product-remove"><a href="#" class="remove-item" data-rowid="<?php echo $item['rowid']; ?>"><span class="ion-ios-close"></span></a></td>

                      <td class="image-prod">
                        <div class="img img-fluid rounded" style="background-image:url(<?php echo get_product_image($item['id']); ?>);"></div>
                      </td>

                      <td class="product-name" style="color: #ff0000;">
                        <h3><?php echo $item['name']; ?></h3>
                      </td>

                      <td class="price" style="color: #ff0000;">Rp <?php echo format_rupiah($item['price']); ?></td>

                      <td class="quantity" style="color: #ff0000;">
                        <div class="input-group mb-3">
                          <input type="text" name="quantity[<?php echo $item['rowid']; ?>]" class="quantity form-control input-number" value="<?php echo $item['qty']; ?>" min="1" max="100">
                        </div>
                      </td>

                      <td class="size-selection">
                        <select name="size[]" class="form-control">
                          <option value="XL">XL</option>
                        </select>

                      </td>

                      <td class="total" style="color: #ff0000;">Rp <?php echo format_rupiah($item['subtotal']); ?></td>
                    </tr><!-- END TR-->
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="row justify-content-end">
          <div class="col-lg-4 mt-5 cart-wrap ftco-animate">
            <div class="cart-total mb-3">
              <h3>Kode Kupon</h3>
              <p>Punya kode kupon? Gunakan kupon kamu untuk mendapatkan potongan harga menarik</p>

              <div class="form-group">
                <label for="code">Kode:</label>
                <input id="code" name="coupon_code" type="text" class="form-control text-left px-3" placeholder="">
              </div>

            </div>

          </div>

          <div class="col-lg-4 mt-5 cart-wrap ftco-animate">
            <div class="cart-total mb-3">
              <h3>Rincian Keranjang</h3>
              <p class="d-flex">
                <span>Subtotal</span>
                <span class="n-subtotal font-weight-bold">Rp <?php echo format_rupiah($total_cart); ?></span>
              </p>
              <p class="d-flex">
                <span>Biaya pengiriman</span>
                <?php if ($total_cart >= get_settings('min_shop_to_free_shipping_cost')) : ?>
                  <span class="n-ongkir font-weight-bold">Gratis</span>
                <?php else : ?>
                  <span class="n-ongkir font-weight-bold">Rp <?php echo format_rupiah(get_settings('shipping_cost')); ?></span>
                <?php endif; ?>
              </p>
              <hr>
              <p class="d-flex total-price">
                <span>Total</span>
                <span class="n-total font-weight-bold">Rp <?php echo format_rupiah($total_price); ?></span>
              </p>
            </div>
            <p><button type="submit" class="btn btn-primary py-3 px-4" style="background-color: #ff0000;">Checkout</button></p>
          </div>
        </div>
      </form>
    <?php else : ?>
      <div class="row">
        <div class="col-md-12 ftco-animate">
          <div class="alert alert-info">Tidak ada barang dalam keranjang.<br><?php echo anchor('browse', 'Jelajahi produk kami'); ?> dan mulailah berbelanja!</div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
  // Mendapatkan stok berdasarkan ukuran dan mengurangi stok ketika item ditambahkan ke keranjang
  document.addEventListener('DOMContentLoaded', function() {
    const sizeSelect = document.querySelector('[name="size"]');
    const quantityInput = document.querySelector('[name="quantity"]');
    const stockS = document.querySelector('[name="stock_s"]');
    const stockM = document.querySelector('[name="stock_m"]');
    const stockL = document.querySelector('[name="stock_l"]');
    const stockXL = document.querySelector('[name="stock_xl"]');

    // Fungsi untuk mengurangi stok berdasarkan ukuran yang dipilih
    function updateStockOnSizeChange() {
      const selectedSize = sizeSelect.value;
      let availableStock = 0;

      // Cek stok berdasarkan ukuran
      switch (selectedSize) {
        case 'S':
          availableStock = parseInt(stockS.value) || 0;
          break;
        case 'M':
          availableStock = parseInt(stockM.value) || 0;
          break;
        case 'L':
          availableStock = parseInt(stockL.value) || 0;
          break;
        case 'XL':
          availableStock = parseInt(stockXL.value) || 0;
          break;
      }

      // Update tampilan stok yang tersedia
      const remainingStock = document.querySelector('#remaining_stock');
      remainingStock.textContent = `Stok tersedia: ${availableStock}`;

      // Batasi input kuantitas sesuai dengan stok yang tersedia
      quantityInput.max = availableStock;
      if (parseInt(quantityInput.value) > availableStock) {
        quantityInput.value = availableStock;
      }
    }

    // Update stok ketika ukuran atau kuantitas diubah
    sizeSelect.addEventListener('change', updateStockOnSizeChange);
    quantityInput.addEventListener('input', updateStockOnSizeChange);

    // Fungsi untuk mengurangi stok saat item ditambahkan ke keranjang
    function updateCartStock(rowid, size, quantity) {
      $.ajax({
        method: 'POST',
        url: '<?php echo site_url('shop/cart_api?action=update_stock'); ?>',
        data: {
          rowid: rowid,
          size: size,
          quantity: quantity
        },
        success: function(res) {
          if (res.code == 200) {
            console.log('Stok berhasil diperbarui.');
            updateStockOnSizeChange();
          }
        }
      });
    }

    // Event listener untuk update stok ketika item ditambahkan ke keranjang
    document.querySelectorAll('.add-to-cart').forEach(function(button) {
      button.addEventListener('click', function(e) {
        const rowid = this.getAttribute('data-rowid');
        const size = sizeSelect.value;
        const quantity = quantityInput.value;

        updateCartStock(rowid, size, quantity);
      });
    });
  });
</script>
