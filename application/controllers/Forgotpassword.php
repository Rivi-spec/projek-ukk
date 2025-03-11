<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Email $email
 */

require APPPATH . 'libraries/phpmailer/src/PHPMailer.php';
require APPPATH . 'libraries/phpmailer/src/Exception.php';
require APPPATH . 'libraries/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Forgotpassword extends CI_Controller
{
    /**
     * Class constructor
     * Loads required models, libraries and helpers
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Forgot_model');
        $this->load->library(['form_validation', 'session', 'email']);
        $this->load->helper(['url', 'form']);
        $this->load->config('email');
    }

    /**
     * Display forgot password form
     */
    public function index()
    {
        $this->load->view('auth/forgot_password');
    }

    /**
     * Process forgot password request
     * Validates email and sends reset link
     */
    public function forgot_password_process()
    {
        $email = $this->input->post('email', TRUE);

        // Cek apakah email ada di database
        $user = $this->Forgot_model->check_email($email);
        if (!$user) {
            $this->session->set_flashdata('error', 'Email tidak ditemukan.');
            redirect('/forgotpassword');
            return;
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $this->Forgot_model->store_reset_token($email, $token);
        $reset_link = base_url("index.php/forgotpassword/reset_password/$token");

        // Setup PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'murtonoa45@gmail.com'; // Ganti dengan email Anda
            $mail->Password = 'svwz iawz qmjm ycji'; // Ganti dengan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Konfigurasi Email
            $mail->setFrom('murtonoa45@gmail.com', 'Admin');
            $mail->addAddress($email); // Email penerima
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body = "<p>Klik link berikut untuk mereset password Anda:</p>
                          <p><a href='$reset_link'>$reset_link</a></p>
                          <p>Link ini berlaku selama 24 jam.</p>";

            // Kirim email
            if ($mail->send()) {
                $this->session->set_flashdata('success', 'Silakan cek email untuk mereset password.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengirim email.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Gagal mengirim email: ' . $mail->ErrorInfo);
        }

        redirect('/forgotpassword');
    }
    /**
     * Handle password reset page
     * Validates token and shows reset form
     * 
     * @param string $token Reset token
     */
    public function reset_password($token)
    {
        $reset_data = $this->Forgot_model->get_token($token);

        if (!$reset_data) {
            $this->session->set_flashdata('error', 'Token tidak valid atau sudah kedaluarsa.');
            redirect('/forgotpassword');
            return;
        }

        // Check token expiration (24 hours)
        $created = strtotime($reset_data['created_at']);
        $now = time();
        $diff = $now - $created;

        if ($diff > (24 * 60 * 60)) {
            $this->Forgot_model->delete_token($token);
            $this->session->set_flashdata('error', 'Token sudah kedaluarsa. Silakan request reset password kembali.');
            redirect('/forgotpassword');
            return;
        }

        $data['token'] = $token;
        $this->load->view('auth/reset_password', $data);
    }

    /**
     * Process password reset
     * Updates password if token is valid
     */
    public function reset_password_process()
    {
        // Validate password
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');

        $token = $this->input->post('token');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('/forgotpassword/reset_password/' . $token);
            return;
        }

        // Verify token
        $reset_data = $this->Forgot_model->get_token($token);

        if (!$reset_data) {
            $this->session->set_flashdata('error', 'Token tidak valid atau sudah kedaluarsa.');
            redirect('/forgotpassword');
            return;
        }

        // Update password
        $new_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

        if ($this->Forgot_model->update_password($reset_data['email'], $new_password)) {
            $this->Forgot_model->delete_token($token);
            $this->session->set_flashdata('success', 'Password berhasil diubah. Silakan login.');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui password.');
            redirect('/forgotpassword/reset_password/' . $token);
        }
    }

    /**
     * Prepare reset password email content
     * 
     * @param string $reset_link Reset password URL
     * @return string Formatted email message
     */
}
