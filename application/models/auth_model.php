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
    private $_auth=null;
    function __construct()
    {
        parent::__construct();
        $this->_auth = $this->config->item("auth");
    }

    public function get($flag = true)
    {
        $data = $this->db->order_by("sort", "asc")->get($this->_auth)->result_array();
        if ($flag === true){
            return $this->getCategory2($data);
        }else{
            return $data;
        }
    }

    public function edit($id)
    {
        return $this->db->get_where($this->_auth, "id=$id")->row_array();
    }

    public function insert($data)
    {
        $data['date_entered'] = date("Y-m-d H:i:s", time());
        return $this->db->insert($this->_auth, $data);
    }

    public function delete($id)
    {
        //判断是否有子集
        $list = $this->db->get_where($this->_auth, "parent_id=$id")->result_array();
        if (count($list) > 0) {
            return array("code" => 1, "msg" => "该权限下有子权限，不能删除");
        }
        $this->db->delete($this->_auth, array("id" => $id));
        return array("code" => 0, "msg" => "删除权限成功");
    }

    public function update($post)
    {
        return $this->db->update($this->_auth, $post, "id=" . $post['id']);
    }

    public function setMenuStatus($id, $status)
    {
        return $this->db->update($this->_auth, array("status"=>$status), "id=$id");
    }
}