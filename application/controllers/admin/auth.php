<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('auth_model');
    }

    public function index()
    {
        $htm["layout"] = $this->load->view('auth/index', null, true);
        $this->load->view('frame', $htm);
    }

    public function get()
    {
        $menu = $this->auth_model->get();
        foreach ($menu as $k => $v) {
            $menu[$k]['name'] = "|--" . str_repeat("--", $v['level'] * 2) . $v['name'];
            $menu[$k]['icon'] = "<i class='" . $v['icon'] . "'></i>";
            if (!$v['url']) {
                $menu[$k]['url'] = "#";
            } else {
                $menu[$k]['url'] = $v['url'];
            }
        }
        $count = count($menu);
        echo json_encode(array(
            'err' => 0,
            'data' => $menu,
            'total' => $count
        ));
    }

    public function add()
    {
        $id = $this->input->get("id");
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;

        $title = "添加权限";
        $info = array('is_menu' => 1);
        if ($id) {
            $title = "修改权限";
            //获取数据
            $info = $this->auth_model->edit($id);
        }
        $menu = $this->auth_model->get();
        $data['data'] = $info;
        $data['id'] = $id;
        $data['menu'] = $menu;
        $html = $this->load->view('auth/add', $data, true);
        echo json_encode(array("title" => $title, "html" => $html));
    }

    public function add_op()
    {
        $id = $this->input->post("id");
        $post = $this->input->post("post");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[name]', '名称', 'required', array("required" => "请输入权限%s"));
        if ($this->form_validation->run() == FALSE) {
            die(json_encode(array("code" => -1, "msg" => strip_tags(validation_errors()))));
        }
        if ($id) {
            //修改
            $post['id'] = $id;
            $result = $this->auth_model->update($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "修改权限成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "修改权限失败")));
        }else{
            //添加
            $result = $this->auth_model->insert($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "添加权限成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "添加权限失败")));
        }
    }

    public function edit_op()
    {
        $this->add_op();
    }

    public function delete_op() {
        $id = $this->input->get("id");
        $result = $this->auth_model->delete($id);
        if ($result['code'] == 0) {
            die(json_encode(array("code" => 0, "msg" => $result['msg'])));
        }
        die(json_encode(array("code" => 1, "msg" => $result['msg'])));
    }

    public function setMenuStatus() {
        $id = $this->input->get("id");
        $status = $this->input->get("status");
        $result = $this->auth_model->setMenuStatus($id, $status);
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "状态修改成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "状态修改失败")));
    }
}