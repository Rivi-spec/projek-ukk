<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="hero-wrap hero-bread"
    style="background-image: url('<?php echo get_theme_uri('images/background_01.jpg'); ?>');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs"><span class="mr-2"><?php echo anchor(base_url(), 'Home'); ?></span>
                    <span class="mr-2"><?php echo anchor('browse', 'Produk'); ?></span>
                    <span><?php echo $product->name; ?></span>
                </p>
                <h1 class="mb-0 bread"><?php echo $product->name; ?></h1>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 ftco-animate">
                <a href="<?php echo base_url('assets/uploads/products/' . $product->picture_name); ?>"
                    class="image-popup"><img
                        src="<?php echo base_url('assets/uploads/products/' . $product->picture_name); ?>"
                        class="img-fluid" alt="<?php echo $product->name; ?>"></a>
            </div>
            <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                <h3><?php echo $product->name; ?></h3>
                <div class="rating d-flex">
                    <p class="text-left mr-4">
                        <a href="#" style="color: #ff0000;" class="mr-2">5.0</a>
                        <a href="#" style="color: #ff0000;"><span class="ion-ios-star-outline"></span></a>
                        <a href="#" style="color: #ff0000;"><span class="ion-ios-star-outline"></span></a>
                        <a href="#" style="color: #ff0000;"><span class="ion-ios-star-outline"></span></a>
                        <a href="#" style="color: #ff0000;"><span class="ion-ios-star-outline"></span></a>
                        <a href="#" style="color: #ff0000;"><span class="ion-ios-star-outline"></span></a>
                    </p>
                    <p class="text-left mr-4">
                        <a href="#" class="mr-2" style="color:  #ff0000;">100 <span style="color: #bbb;">Rating</span></a>
                    </p>
                    <p class="text-left">
                        <a href="#" class="mr-2" style="color: #ff0000;">500 <span style="color: #bbb;">Sold</span></a>
                    </p>
                </div>
                <p class="price">
                    <?php if ($product->current_discount > 0) : ?>
                        <span class="mr-2 price-dc"><strike><small>Rp
                                    <?php echo format_rupiah($product->price); ?></small></strike></span>
                        <span class="price-sale text-success">Rp
                            <?php echo format_rupiah($product->price - $product->current_discount); ?></span>
                    <?php else : ?>
                        <span>Rp <?php echo format_rupiah($product->price); ?></span>
                    <?php endif; ?>
                </p>
                <p><?php echo $product->description; ?></p>
                <div class="row mt-4">
                    <div class="w-100"></div>
                    <div class="input-group col-md-6 d-flex mb-3 <?php echo ($product->stock <= 0) ? 'disabled' : ''; ?>">
                        <span class="input-group-btn mr-2">
                            <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="" <?php echo ($product->stock <= 0) ? 'disabled' : ''; ?>>
                                <i class="ion-ios-remove"></i>
                            </button>
                        </span>
                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="<?php echo ($product->stock > 0) ? '1' : '0'; ?>"
                            min="1" max="<?php echo $product->stock; ?>" <?php echo ($product->stock <= 0) ? 'disabled' : ''; ?>>
                        <span class="input-group-btn ml-2">
                            <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="" <?php echo ($product->stock <= 0) ? 'disabled' : ''; ?>>
                                <i class="ion-ios-add"></i>
                            </button>
                        </span>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md-12">
                        <p style="color: <?php echo ($product->stock <= 0) ? '#ff0000' : '#000'; ?>;">
                            <?php if ($product->stock <= 0) : ?>
                                <strong>Stok Habis</strong>
                            <?php else : ?>
                                Tersedia <?php echo $product->stock; ?> <?php echo $product->product_unit; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <p>
                    <?php if ($product->stock > 0) : ?>
                        <a href="#" class="btn btn-black btn-sm py-3 px-5 add-cart cart-btn"
                            data-sku="<?php echo $product->sku; ?>" data-name="<?php echo $product->name; ?>"
                            data-price="<?php echo ($product->current_discount > 0) ? ($product->price - $product->current_discount) : $product->price; ?>"
                            data-id="<?php echo $product->id; ?>" style="color: #ff0000;">Add to Cart</a>
                    <?php else : ?>
                        <a href="javascript:void(0)" class="btn btn-sm py-3 px-5" style="background-color: #aaaaaa; color: #ffffff; cursor: not-allowed;">Add to Cart</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center mb-3 pb-3">
            <div class="col-md-12 heading-section text-center ftco-animate">
                <span class="subheading" style="color: #ff0000;">Produk Lain</span>
                <h2 class="mb-4">Produk lain yang terkait</h2>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <?php if (count($related_products) > 0) : ?>
                <?php foreach ($related_products as $product) : ?>
                    <div class="col-md-6 col-lg-3 ftco-animate">
                        <div class="product">
                            <a href="#" class="img-prod"><img class="img-fluid"
                                    src="<?php echo base_url('assets/uploads/products/' . $product->picture_name); ?>"
                                    alt="<?php echo $product->name; ?>">
                                <?php if ($product->current_discount > 0) : ?>
                                    <span
                                        class="status"><?php echo count_percent_discount($product->current_discount, $product->price); ?>%</span>
                                <?php endif; ?>
                                <div class="overlay"></div>
                            </a>
                            <div class="text py-3 pb-4 px-3 text-center">
                                <h3><?php echo anchor('shop/product/' . $product->id . '/' . $product->sku . '/', $product->name); ?>
                                </h3>
                                <div class="d-flex">
                                    <div class="pricing">
                                        <p class="price">
                                            <?php if ($product->current_discount > 0) : ?>
                                                <span class="mr-2 price-dc">Rp <?php echo format_rupiah($product->price); ?></span>
                                                <span class="price-sale">Rp
                                                    <?php echo format_rupiah($product->price - $product->current_discount); ?></span>
                                        </p>
                                    <?php else : ?>
                                        <span class="price-sale" style="color: #ff0000;">Rp <?php echo format_rupiah($product->price); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="bottom-area d-flex px-3">
                                    <div class="m-auto d-flex">
                                        <a href="<?php echo site_url('shop/product/' . $product->id . '/' . $product->sku . '/'); ?>"
                                            class="buy-now d-flex justify-content-center align-items-center text-center"
                                            style="background-color: #ff0000; border-radius: 50%; padding: 10px;">
                                            <span><i class="ion-ios-menu"></i></span>
                                        </a>
                                        <?php if ($product->stock > 0) : ?>
                                            <a href="#"
                                                class="add-to-chart add-cart d-flex justify-content-center align-items-center mx-1"
                                                data-sku="<?php echo $product->sku; ?>" data-name="<?php echo $product->name; ?>"
                                                data-price="<?php echo ($product->current_discount > 0) ? ($product->price - $product->current_discount) : $product->price; ?>"
                                                data-id="<?php echo $product->id; ?>"
                                                style="background-color: #ff0000; border-radius: 50%; padding: 10px;">
                                                <span><i class="ion-ios-cart"></i></span>
                                            </a>
                                        <?php else : ?>
                                            <a href="javascript:void(0)"
                                                class="d-flex justify-content-center align-items-center mx-1"
                                                style="background-color: #aaaaaa; border-radius: 50%; padding: 10px; cursor: not-allowed;">
                                                <span><i class="ion-ios-cart"></i></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        var quantitiy = 0;
        $('.quantity-right-plus').click(function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get the field name
            var quantity = parseInt($('#quantity').val());
            var max = parseInt($('#quantity').attr('max'));

            // If is not undefined and not exceeding max stock
            if (quantity < max) {
                $('#quantity').val(quantity + 1);
                $('.cart-btn').attr('data-qty', quantity + 1);
            }
        });

        $('.quantity-left-minus').click(function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get the field name
            var quantity = parseInt($('#quantity').val());

            // If is not undefined
            // Increment
            if (quantity > 1) {
                $('#quantity').val(quantity - 1);
                $('.cart-btn').attr('data-qty', quantity - 1);
            }
        });
    });
</script>