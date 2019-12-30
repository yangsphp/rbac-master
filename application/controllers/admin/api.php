<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/10/14
 * Time: 13:29
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    private $_customer = null;
    public function __construct()
    {
        parent::__construct();
        $this->_customer = $this->config->item("customer");
    }

    public function login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $res = $this->db->get_where($this->_customer, array("name" => $username, "password" => $password, "is_deleted" => 0))->row_array();
        if ($res) {
            die(json_encode(array("code" => 0, "msg" => "登录成功", "user" => $res)));
        }
        die(json_encode(array("code" => 1, "msg" => "账号或密码不正确")));
    }
}