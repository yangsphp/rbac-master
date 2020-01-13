<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('admin_model');
    }
    public function get() {
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $arr = $this->admin_model->get($start, $limit);
        $count = count($this->admin_model->get());
        echo json_encode(array(
            'err' => 0,
            'data' => $arr,
            'total' => $count
        ));
    }
    public function index()
    {
        $add_flag = $this->checkUserButtonPrivilege('admin/admin/add_op');
        $edit_flag = $this->checkUserButtonPrivilege('admin/admin/edit_op');
        $delete_flag = $this->checkUserButtonPrivilege('admin/admin/delete_op');
        $data['add_flag'] = $add_flag;
        $data['edit_flag'] = $edit_flag;
        $data['delete_flag'] = $delete_flag;
        $htm["layout"] = $this->load->view('admin/index', $data, true);
        $this->load->view('frame',$htm);
    }

    public function add()
    {
        $id = $this->input->get("id");
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data['csrf'] = $csrf;
        $info = array();
        $title = "添加管理员";
        if ($id) {
            $title = "修改管理员";
            //获取数据
            $info = $this->admin_model->edit($id);
        }
        $role = $this->admin_model->getRole();
        $data['data'] = $info;
        $data['role'] = $role;
        $data['id'] = $id;
        $html = $this->load->view('admin/add', $data, true);
        echo json_encode(array("title" => $title, "html" => $html));
    }

    public function validate()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[password]', '管理员密码', 'trim|required|min_length[5]', array("required" => "请输入%s", "min_length" => "%s长度最少5位"));
        $this->form_validation->set_rules('post[rpassword]', '管理员密码', 'required|trim|matches[post[password]]', array("required" => "请再次输入%s", "matches" => "两次输入%s不一致"));
        if ($this->form_validation->run() == FALSE) {
            $errors = explode("\n", validation_errors());
            die(json_encode(array("code" => -1, "msg" =>  strip_tags($errors[0]))));
        }
    }

    public function add_op()
    {
        $id = $this->input->post("id");
        $post = $this->input->post("post");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[role_id]', '角色', 'required|trim', array("required" => "请选择%s"));
        $this->form_validation->set_rules('post[username]', '管理员名称', 'required|trim', array("required" => "请输入%s"));
        if ($this->form_validation->run() == FALSE) {
            $errors = explode("\n", validation_errors());
            die(json_encode(array("code" => -1, "msg" =>  strip_tags($errors[0]))));
        }
        if ($id) {
            if ($post['password']) {
               $this->validate();
                //要求修改密码
                $post['salt'] = $this->random(5);
                $post['password'] = md5(md5($post['password']).$post['salt']);
            }else{
                unset($post['password']);
            }
            unset($post['rpassword']);
            //修改
            $post['id'] = $id;
            $result = $this->admin_model->update($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "修改管理员成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "修改管理员失败")));
        }else{
            $this->validate();
            //添加
            $post['salt'] = $this->random(5);
            $post['password'] = md5(md5($post['password']).$post['salt']);
            unset($post['rpassword']);
            $result = $this->admin_model->insert($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "添加管理员成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "添加管理员失败")));
        }
    }

    public function edit_op()
    {
        $this->add_op();
    }

    public function delete_op() {
        $id = $this->input->get("id");
        $result = $this->admin_model->delete($id);
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "删除账户成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "删除账户失败")));
    }

}