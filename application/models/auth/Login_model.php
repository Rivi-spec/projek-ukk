<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{
    protected $username;
    protected $password;

    public function __construct()
    {
        parent::__construct();
    }

    public function login($username = '', $password = '')
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function is_user_exist()
    {
        $username = $this->username;

        $check = $this->db
            ->where('username', $username)
            ->get('users')
            ->num_rows();

        return ($check > 0) ? TRUE : FALSE;
    }

    protected function _get($row = '')
    {
        $username = $this->username;

        $field = $this->db
            ->select($row)
            ->where('username', $username)
            ->get('users')
            ->row()
            ->$row;

        return $field;
    }

    public function get_role()
    {
        return $this->_get('role');
    }

    public function get_password()
    {
        return $this->_get('password');
    }

    public function logged_user_id()
    {
        return $this->_get('id');
    }

    public function get_user_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function update_password($email, $new_password)
    {
        $this->db->where('email', $email);
        $this->db->update('users', ['password' => $new_password]);
    }
}
