<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Role_model extends Common_model
{
    private $_role = null;
    private $_auth = null;
    private $_admin = null;
    function __construct()
    {
        parent::__construct();
        $this->_role = $this->config->item("role");
        $this->_auth = $this->config->item("auth");
        $this->_admin = $this->config->item("admin");
    }

    public function get($start = '', $limit = '')
    {
        $keyword = $this->input->post("keyword");
        //分页条件
        $condition = array($start, $limit);
        $where = '1=1';

        if ($keyword) {
            $where .= " and name like '%$keyword%'";
        }

        $select = '*';
        $order = array("id", " desc");
        $arr = $this->getAllCommon($this->_role, $where, $select, '', $order, $condition);
        if ($start != '') {
            foreach ($arr as $k => $v)
            {
                $arr[$k]['number'] = $this->db->where("role_id = {$v['id']} and status = 1")->count_all_results($this->_admin);
            }
        }
        return $arr;
    }

    public function getMenu()
    {
        $data = $this->db->where("status=1")->order_by("sort", "asc")->get($this->_auth)->result_array();
        $menuList = $this->getCategory1($data);
        return $menuList;
    }

    public function insert($post)
    {
        $post['date_entered'] = date("Y-m-d H:i:s", time());
        $post['auth'] = implode(",", $post['auth']);
        return $this->db->insert($this->_role, $post);
    }

    public function edit($id)
    {
        return $this->db->get_where($this->_role, "id=$id")->row_array();
    }

    public function update($post)
    {
        $post['auth'] = implode(",", $post['auth']);
        return $this->db->update($this->_role, $post, "id=" . $post['id']);
    }

    public function delete($id)
    {
        //判断是否有子集
        $list = $this->db->get_where($this->_admin, "role_id=$id")->result_array();
        if (count($list) > 0) {
            return array("code" => 1, "msg" => "该角色下有用户，不能删除");
        }
        $this->db->delete($this->_role, array("id" => $id));
        return array("code" => 0, "msg" => "删除角色成功");
    }
}