<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model
{
    private $_admin_log = null;
    function __construct()
    {
        parent::__construct();
        $this->_admin_log = $this->config->item("admin_log");
    }

    /**
     * 无限分类
     * @param $data
     * @param int $parent_id
     * @return array
     */
    function getCategory1($data, $parent_id = 0)
    {
        $tree = array();
        foreach ($data as $k => $v) {
            if ($v["parent_id"] == $parent_id) {
                unset($data[$k]);
                if (!empty($data)) {
                    $children = $this->getCategory1($data, $v["id"]);
                    if (!empty($children)) {
                        $v["_child"] = $children;
                    }
                }
                $tree[] = $v;
            }
        }
        return $tree;
    }

    /**
     * @param $data
     * @param int $parent_id
     * @param int $level
     * @return array
     */
    function getCategory2($data, $parent_id = 0, $level = 0)
    {
        static $tree = array();
        foreach ($data as $k => $v) {
            if ($v["parent_id"] == $parent_id) {
                $v["level"] = $level;
                $tree[] = $v;
                $this->getCategory2($data, $v["id"], $level + 1);
            }
        }
        return $tree;
    }


    /**
     * @param $table
     * @param string $where
     * @param string $select
     * @param string $join
     * @param string $order
     * @param string $group
     * @return mixed
     */
    public function getAllCommon($table, $where = '', $select = '*', $join = '', $order = '', $limit = array(), $group = '')
    {
        $this->db->select($select);
        $this->db->from($table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($join) {
            if (count($join) == count($join, 1)) {
                $this->db->join($join[0], $join[1], $join[2]);
            } else {
                foreach ($join as $item) {
                    $this->db->join($item[0], $item[1], $item[2]);
                }
            }
        }
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        }
        if ($limit[0] || $limit[1]) {
            $this->db->limit((int)$limit[1], (int)$limit[0]);
        }
        if ($group) {
            $this->db->group_by($group);
        }
        $query = $this->db->get()->result_array();
        return $query;
    }

    public function getClientIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if ($this->is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            $ip = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
            if ($this->is_ip($ip)) {
                return $ip;
            }
        }
        if (isset($_SERVER['REMOTE_ADDR']) && $this->is_ip($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $this->is_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        return '0.0.0.0';
    }

    public function is_ip($ip)
    {
        return preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $ip);
    }

    public function insertAdminLog($data)
    {
        $data['ip'] = $this->getClientIp();
        return $this->db->insert($this->_admin_log, $data);
    }
}