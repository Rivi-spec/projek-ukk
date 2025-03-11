<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Midtrans.php');

class Midtrans {
    public function __construct() {
        // Mengambil konfigurasi dari file config
        $this->CI =& get_instance();
        $this->CI->config->load('midtrans');

        // Mengonfigurasi Midtrans dengan Server Key dan Client Key
        \Midtrans\Config::$serverKey = $this->CI->config->item('SB-Mid-server-9y6u1E7r5-huWxmGkriXTNlK');
        \Midtrans\Config::$clientKey = $this->CI->config->item('SB-Mid-client-naj9eMmGSbrSc_-m');
        \Midtrans\Config::$isProduction = $this->CI->config->item('MIDTRANS_IS_PRODUCTION');
    }

    // Membuat transaksi untuk pembayaran
    public function create_transaction($order_id, $total_amount, $customer_name, $customer_email) {
        $transaction_details = array(
            'order_id' => $order_id,
            'gross_amount' => $total_amount,
        );

        // Informasi pelanggan
        $customer_details = array(
            'first_name'    => $customer_name,
            'email'         => $customer_email,
        );

        // Menghasilkan URL pembayaran
        $payment_type = 'credit_card';  // Bisa diganti dengan payment method lain jika diinginkan

        $transaction_data = array(
            'transaction_details' => $transaction_details,
            'customer_details'    => $customer_details,
            'payment_type'        => $payment_type,
        );

        // Melakukan transaksi ke Midtrans
        try {
            $snap_token = \Midtrans\Snap::getSnapToken($transaction_data);
            return $snap_token;  // Return token yang digunakan untuk pembayaran
        } catch (Exception $e) {
            return false;  // Jika terjadi error
        }
    }
}
