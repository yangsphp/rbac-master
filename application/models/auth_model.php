<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends Common_model
{
    private $_tableName=null;
    function __construct()
    {
        parent::__construct();
        $this->_tableName = $this->config->item("auth");
    }

    public function get()
    {
        $data = $this->db->order_by("sort", "asc")->get($this->_tableName)->result_array();
        return $this->getCategory2($data);
    }

    public function edit($id)
    {
        return $this->db->get_where($this->_tableName, "id=$id")->row_array();
    }

    public function insert($data)
    {
        $data['date_entered'] = date("Y-m-d H:i:s", time());
        return $this->db->insert($this->_tableName, $data);
    }

    public function delete($id)
    {
        //判断是否有子集
        $list = $this->db->get_where($this->_tableName, "parent_id=$id")->result_array();
        if (count($list) > 0) {
            return array("code" => 1, "msg" => "该权限下有子权限，不能删除");
        }
        $this->db->delete($this->_tableName, array("id" => $id));
        return array("code" => 0, "msg" => "删除权限成功");
    }

    public function update($post)
    {
        return $this->db->update($this->_tableName, $post, "id=" . $post['id']);
    }

    public function setMenuStatus($id, $status)
    {
        return $this->db->update($this->_tableName, array("status"=>$status), "id=$id");
    }
}