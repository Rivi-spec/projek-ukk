<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $title; ?> | <?php echo get_store_name(); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700&display=swap" rel="stylesheet">

    <!-- Logo sites -->
    <link rel="icon" href="<?php echo base_url('assets/uploads/sites/Logo.jpg'); ?>">

    <link rel="stylesheet" href="<?php echo get_theme_uri('css/open-iconic-bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/animate.css'); ?>">

    <link rel="stylesheet" href="<?php echo get_theme_uri('css/owl.carousel.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/owl.theme.default.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/magnific-popup.css'); ?>">
    <!-- bottstrap icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <!-- aos animation -->
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/aos.css'); ?>">
    <!-- ionicons -->
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/ionicons.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo get_theme_uri('js/plugins/@fortawesome/fontawesome-free/css/all.min.css', 'argon'); ?>">

    <link rel="stylesheet" href="<?php echo get_theme_uri('css/bootstrap-datepicker.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/jquery.timepicker.css'); ?>">

    <link rel="stylesheet" href="<?php echo get_theme_uri('css/flaticon.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/icomoon.css'); ?>">
    <link rel="stylesheet" href="<?php echo get_theme_uri('css/style.css'); ?>">
    <!-- toastr notification -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/toastr/toastr.min.css'); ?>">
    <!-- jquery -->
    <script src="<?php echo get_theme_uri('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo get_theme_uri('js/jquery-migrate-3.0.1.min.js'); ?>"></script>
</head>

<body class="goto-here">
    <div class="py-1 bg-primary">
        <div class="container">
            <div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
                <div class="col-lg-12 d-block">
                    <div class="row d-flex">
                        <div class="col-md pr-4 d-flex topper align-items-center">
                            <div class="icon mr-2 d-flex justify-content-center align-items-center"><span
                                    class="icon-phone2"></span></div>
                            <span class="text"><?php echo get_settings('store_phone_number'); ?></span>
                        </div>
                        <div class="col-md pr-4 d-flex topper align-items-center">
                            <div class="icon mr-2 d-flex justify-content-center align-items-center"><span
                                    class="icon-paper-plane"></span></div>
                            <span class="text"><?php echo get_settings('store_email'); ?></span>
                        </div>
                        <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
                            <span class="text"><?php echo get_settings('store_tagline'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>" style="color: #ff0000; font-size: 24px; font-weight: bold; text-transform: uppercase;">
                <?php echo get_store_name(); ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active"><a href="<?php echo base_url(); ?>" class="nav-link" style="color: #ff0000;">Home</a></li>
                    <li class=" nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Shop</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <a class="dropdown-item" href="<?php echo site_url('shop/cart'); ?>">Keranjang Belanja</a>
                            <a class="dropdown-item"
                                href="<?php echo site_url('customer/payments/confirm'); ?>">Konfirmasi Pembayaran</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="<?php echo site_url('pages/about'); ?>" class="nav-link">Tentang
                            Kami</a></li>
                    <li class="nav-item"><a href="<?php echo site_url('pages/contact'); ?>" class="nav-link">Kontak</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown05" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Akun</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown05">
                            <?php if (is_login() && is_customer()) : ?>
                                <a class="dropdown-item" href="<?php echo site_url('customer'); ?>">Akun saya</a>
                                <a class="dropdown-item" href="<?php echo site_url('customer/orders'); ?>">Order</a>
                                <div class="divider"></div>
                                <a class="dropdown-item" href="<?php echo site_url('auth/logout'); ?>">Logout</a>
                            <?php elseif (is_login() && is_admin()) : ?>
                                <a class="dropdown-item" href="<?php echo site_url('admin'); ?>">Dasbor</a>
                                <div class="divider"></div>
                                <a class="dropdown-item" href="<?php echo site_url('auth/logout'); ?>">Logout</a>
                            <?php else : ?>
                                <a class="dropdown-item" href="<?php echo site_url('auth/login'); ?>">Masuk Log</a>
                                <a class="dropdown-item" href="<?php echo site_url('auth/register'); ?>">Daftar</a>
                            <?php endif; ?>

                        </div>
                    </li>
                    <li class="nav-item cta cta-colored "><a href="<?php echo site_url('shop/cart'); ?>"
                            class="nav-link"><span class="icon-shopping_cart"></span>[<span
                                class="cart-item-total">0</span>]</a></li>

                </ul>
            </div>
        </div>
    </nav>
    <style type="text/css">
        .bg-primary {
            background-color: #ff0000 !important;
        }
    </style>

    <script>
        window.addEventListener("scroll", function() {
            var ctaItem = document.querySelector(".ftco-navbar-light .nav-item.cta.cta-colored span");
            var ctaLink = document.querySelector(".ftco-navbar-light .nav-item.cta > a");

            if (window.scrollY > 50) { // Jika scroll ke bawah lebih dari 50px
                if (ctaItem) {
                    ctaItem.style.borderColor = "#ff0000";
                    ctaItem.style.backgroundColor = "#ff0000";
                    ctaItem.style.color = "#fff";
                }

                if (ctaLink) {
                    ctaLink.style.backgroundColor = "#ff0000";
                    ctaLink.style.color = "#fff";
                }
            } else { // Jika scroll kembali ke atas
                if (ctaItem) {
                    ctaItem.style.borderColor = "transparent"; // Kembalikan ke normal
                    ctaItem.style.backgroundColor = "transparent";
                    ctaItem.style.color = ""; // Kembali ke warna default (CSS)
                }

                if (ctaLink) {
                    ctaLink.style.backgroundColor = "transparent"; // Hilangkan background merah
                    ctaLink.style.color = ""; // Kembali ke warna default
                }
            }
        });
    </script>