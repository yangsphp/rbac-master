<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_model extends Common_model
{
    private $_admin = null;
    private $_role = null;
    function __construct()
    {
        parent::__construct();
        $this->_admin = $this->config->item("admin");
        $this->_role = $this->config->item("role");
    }

    public function get($start = '', $limit = '')
    {
        $keyword = $this->input->post("keyword");
        //分页条件
        $condition = array($start, $limit);
        $where = '1=1 and a.status = 1';

        if ($keyword) {
            $where .= ' and a.username like "%'.$keyword.'%"';
        }

        $join[0] = array(
            $this->_role .' as r',
            'a.role_id = r.id',
            "left"
        );
        $select = 'a.*, r.name as role_name';
        $order = array("a.id", " desc");
        $arr = $this->getAllCommon($this->_admin.' as a', $where, $select, $join, $order, $condition);
        return $arr;
    }

    public function getRole() {
        return $this->db->get($this->_role)->result_array();
    }

    public function insert($post)
    {
        $post['date_entered'] = $post['login_entered'] = date("Y-m-d H:i:s", time());
        return $this->db->insert($this->_admin, $post);
    }

    public function edit($id)
    {
        return $this->db->get_where($this->_admin, "id=$id")->row_array();
    }

    public function update($post)
    {
        return $this->db->update($this->_admin, $post, "id=" . $post['id']);
    }

    public function delete($id)
    {
        return $this->db->delete($this->_admin, array("id" => $id));
    }

    public function login($post)
    {
        $admin = $this->db->get_where($this->_admin, "status = 1 and username='{$post['username']}'")->row_array();
        if ($admin && md5(md5($post['password']).$admin['salt']) == $admin['password']) {
            $user_info = array(
                "userid" => $admin['id'],
                "username" => $admin['username']
            );
            $this->session->set_userdata("user", $user_info);
            $update = array(
                "login_entered" => date("Y-m-d H:i:s"),
                "last_login_entered" => $admin['login_entered'],
                "login_ip" => $this->getClientIp()
            );
            //修改登录时间和ip地址与登录日志
            $this->db->update($this->_admin, $update, "id = ".$admin['id']);
            //插入登录日志
            $data = array(
                'user_id' => $admin['id'],
                '_id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : 0,
                'name' => "登录后台",
                'date_entered' => date("Y-m-d H:i:s")
            );
            $this->insertAdminLog($data);
            return true;
        }
        return false;
    }

    public function logout($user)
    {
        //插入退出登录日志
        $data = array(
            'user_id' => $user['userid'],
            '_id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : 0,
            'name' => "退出登录",
            'date_entered' => date("Y-m-d H:i:s")
        );
        return $this->insertAdminLog($data);
    }
}