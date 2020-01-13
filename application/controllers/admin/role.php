<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Role extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('role_model');
    }
    public function get() {
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $arr = $this->role_model->get($start, $limit);
        $count = count($this->role_model->get());
        echo json_encode(array(
            'err' => 0,
            'data' => $arr,
            'total' => $count
        ));
    }
    public function index()
    {
        $add_flag = $this->checkUserButtonPrivilege('admin/role/add_op');
        $edit_flag = $this->checkUserButtonPrivilege('admin/role/edit_op');
        $delete_flag = $this->checkUserButtonPrivilege('admin/role/delete_op');
        $data['add_flag'] = $add_flag;
        $data['edit_flag'] = $edit_flag;
        $data['delete_flag'] = $delete_flag;
        $htm["layout"] = $this->load->view('role/index', $data, true);
        $this->load->view('frame',$htm);
    }

    public function test()
    {
        $htm["layout"] = $this->load->view('role/index', null, true);
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

        $title = "添加角色";
        $info = array('is_menu' => 1);
        if ($id) {
            $title = "修改角色";
            //获取数据
            $info = $this->role_model->edit($id);
            $info['auth'] = explode(",", $info['auth']);
        }
        $menu = $this->role_model->getMenu();
        $data['data'] = $info;
        $data['menu'] = $menu;
        $data['id'] = $id;
        $html = $this->load->view('role/add', $data, true);
        echo json_encode(array("title" => $title, "html" => $html));
    }

    public function add_op()
    {
        $id = $this->input->post("id");
        $post = $this->input->post("post");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post[name]', '名称', 'required', array("required" => "请输入角色%s"));
        if ($this->form_validation->run() == FALSE) {
            die(json_encode(array("code" => -1, "msg" => strip_tags(validation_errors()))));
        }
        if (count($post['auth']) == 0) {
            die(json_encode(array("code" => -1, "msg" => "请选择菜单")));
        }
        if ($id) {
            //修改
            $post['id'] = $id;
            $result = $this->role_model->update($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "修改角色成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "修改角色失败")));
        }else{
            //添加
            $result = $this->role_model->insert($post);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "添加角色成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "添加角色失败")));
        }
    }

    public function edit_op()
    {
        $this->add_op();
    }

    public function delete_op() {
        $id = $this->input->get("id");
        $result = $this->role_model->delete($id);
        if ($result['code'] == 0) {
            die(json_encode(array("code" => 0, "msg" => $result['msg'])));
        }
        die(json_encode(array("code" => 1, "msg" => $result['msg'])));
    }
}