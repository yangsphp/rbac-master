<?php
/**
 * Created by PhpStorm.
 * User: Yang
 * Date: 2019/9/28
 * Time: 8:39
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Chat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_home_view_dir();
        $this->load->model('common_model');
        $this->load->model('chat_model');
        $this->load->model('customer_model');
    }

    public function index()
    {
        //echo $this->get_chat_id(1, 3);exit();
        $user = $this->session->userdata("chat_user");
        if (!$user) {
            //重定向到登录页面
            redirect(site_url("home/chat/login"));
        }
        $data["user"] = $user;
        $this->load->view('chat/index', $data);
    }

    public function get_chat_id($f, $t)
    {
        return md5(strcmp($f, $t) > 0 ? $f . '|' . $t : $t . '|' . $f);
    }


    public function addfriend()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        //获取用户
        $customer = $this->customer_model->get(0, 18);
        $data['friend'] = $customer;
        $this->load->view('chat/addfriend', $data);
    }

    public function newfriend()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        $user = $this->session->userdata("chat_user");
        //获取用户（需要我通过添加朋友请求的）
        $customer = $this->customer_model->getNewFriend($user['id']);
        $data['friend'] = $customer;
        $this->load->view('chat/newfriend', $data);
    }

    public function sendMsgToFriend(){
        $msg_id = $this->input->get("msg_id");
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        $user = $this->session->userdata("chat_user");
        $customer = $this->customer_model->getMyFriend($user['id']);
        $data['friend'] = $customer;
        $data['msg_id'] = $msg_id;
        $data['uid'] = $user['id'];
        $this->load->view('chat/sendMsgToFriend', $data);
    }

    public function login()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        $this->load->view('chat/login', $data);
    }

    public function doLogin()
    {
        $post = $this->input->post("post");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[username]', '用户名称', 'required|trim', array("required" => "请输入%s"));
        $this->form_validation->set_rules('post[password]', '密码', 'trim|required', array("required" => "请输入%s"));
        if ($this->form_validation->run() == FALSE) {
            $errors = explode("\n", validation_errors());
            die(json_encode(array("code" => -1, "msg" =>  strip_tags($errors[0]))));
        }
        $result = $this->chat_model->login_op($post);
        if ($result) {
            $this->session->set_userdata("chat_user", $result);
            die(json_encode(array("code" => 0, "msg" => "登录成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "登录失败")));
    }

    function logout()
    {
        $this->session->set_userdata("chat_user", "");
        redirect("home/chat/login");
    }
}