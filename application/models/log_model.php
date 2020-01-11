<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Log_model extends Common_model
{
    private $_admin = null;
    private $_admin_log = null;
    function __construct()
    {
        parent::__construct();
        $this->_admin = $this->config->item("admin");
        $this->_admin_log = $this->config->item("admin_log");
    }

    public function get($start = '', $limit = '')
    {
        $keyword = $this->input->post("keyword");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        //分页条件
        $condition = array($start, $limit);
        $where = '1=1 and l.is_deleted = 0';

        if ($keyword) {
            $where .= ' and (l.name like "%'.$keyword.'%" or a.name like "%'.$keyword.'%" or l.ip like "'.$keyword.'")';
        }

        if ($start_date) {
            $where .= " and DATE_FORMAT(l.date_entered, '%Y-%m-%d') >= '$start_date'";
        }
        if ($end_date) {
            $where .= " and DATE_FORMAT(l.date_entered, '%Y-%m-%d') <= '$end_date'";
        }

        $join[0] = array(
            $this->_admin .' as a',
            'l.user_id = a.id',
            "left"
        );
        $select = 'l.*, a.username';
        $order = array("l.id", " desc");
        $arr = $this->getAllCommon($this->_admin_log.' as l', $where, $select, $join, $order, $condition);
        return $arr;
    }

    public function delete()
    {
        return $this->db->update($this->_admin_log, array("is_deleted" => 1),"date_entered < DATE_SUB(curdate(),interval 30 day)");
    }
}