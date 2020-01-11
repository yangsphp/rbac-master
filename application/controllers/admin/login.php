<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/9/23
 * Time: 14:35
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('common_model');
        $this->load->model('admin_model');
    }

    public function index()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        $this->load->view('login/index', $data);
    }

    public function doLogin()
    {
        $post = $this->input->post("post");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[username]', '账号', 'required|trim', array("required" => "请输入%s"));
        $this->form_validation->set_rules('post[password]', '密码', 'required', array("required" => "请输入%s"));
        if ($this->form_validation->run() == FALSE) {
            $errors = explode("\n", validation_errors());
            die(json_encode(array("code" => -1, "msg" => strip_tags($errors[0]))));
        }
        $result = $this->admin_model->login($post);
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "登录成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "登录失败")));
    }

    public function logout()
    {
        $this->admin_model->logout($this->session->userdata("user"));
        $this->session->set_userdata("user", "");
        redirect("admin/login/index");
    }
}