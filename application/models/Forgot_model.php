<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot_model extends CI_Model {
    
    public function check_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row_array();
    }

    public function store_reset_token($email, $token) {
        $data = [
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Hapus token lama jika ada
        $this->db->where('email', $email);
        $this->db->delete('password_resets');
        
        // Simpan token baru
        return $this->db->insert('password_resets', $data);
    }

    public function get_token($token) {
        return $this->db->get_where('password_resets', ['token' => $token])->row_array();
    }

    public function update_password($email, $new_password) {
        $this->db->where('email', $email);
        return $this->db->update('users', ['password' => $new_password]);
    }

    public function delete_token($token) {
        $this->db->where('token', $token);
        return $this->db->delete('password_resets');
    }
}