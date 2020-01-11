<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/8/19
 * Time: 9:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Database_model extends Common_model
{
    private $_back_up = null;

    function __construct()
    {
        parent::__construct();
        $this->_back_up = $this->config->item("back_up");
    }

    public function get($flag, $start = '', $limit = '')
    {
        if ($flag == 'table') {
            $database = $this->db->database;
            $tables = $T = array();
            $i = $total_size = 0;
            $res = $this->db->query("show tables from $database");
            foreach ($res->result_array() as $r) {
                if (!$r) {
                    continue;
                }
                $T[] = $r['Tables_in_' . $database];
            }
            uksort($T, 'strnatcasecmp');
            foreach ($T as $t) {
                $r = $this->db->query("show table status from $database like '$t'")->row_array();
                $tables[$i]['name'] = $r['Name'];
                $tables[$i]['rows'] = $r['Rows'];
                $tables[$i]['size'] = round($r['Data_length'] / 1024 / 1024, 2);
                $tables[$i]['index'] = round($r['Index_length'] / 1024 / 1024, 2);
                $tables[$i]['tsize'] = $tables[$i]['size'] + $tables[$i]['index'];
                $tables[$i]['auto'] = $r['Auto_increment'];
                $tables[$i]['update_time'] = $r['Update_time'];
                $tables[$i]['note'] = $r['Comment'];
                $tables[$i]['chip'] = $r['Data_free'];
                $total_size += $r['Data_length'] + $r['Index_length'];
                $i++;
            }
            if ($start) {
                return array("data" => array_slice($tables, $start, $limit), "total_size" => $total_size);
            }
            return array("data" => $tables, "total_size" => $total_size);
        } elseif ($flag == 'back_list') {
            $keyword = $this->input->post("keyword");
            //分页条件
            $condition = array($start, $limit);
            $where = '1=1';

            if ($keyword) {
                $where .= ' and name like "%' . $keyword . '%"';
            }
            $select = '*';
            $order = array("id", " desc");
            $arr = $this->getAllCommon($this->_back_up, $where, $select, '', $order, $condition);
            return array("data" => $arr);
        }

    }

    public function repair($table)
    {
        $this->db->query("REPAIR TABLE `$table`");
        return true;
    }

    public function optimize($table)
    {
        $this->db->query("OPTIMIZE TABLE `$table`");
        return true;
    }

    public function dict($table)
    {
        $columns = array();
        $res = $this->db->query("SHOW COLUMNS FROM $table");
        foreach ($res->result_array() as $r) {
            //获取字段注释
            $column = $this->db->query("select COLUMN_COMMENT as comment from INFORMATION_SCHEMA.COLUMNS where table_name = '{$table}' and column_name = '{$r['Field']}' and table_schema = '{$this->db->database}'")->result_array();
            $r['comment'] = $column[0]['comment'];
            $columns[] = $r;
        }
        return $columns;
    }

    public function update($table, $field, $type)
    {
        foreach ($field as $column => $v) {
            $column_type = $type[$column];
            $this->db->query("alter table $table modify column {$column} {$column_type} comment '{$v}'");
        }
        return true;
    }

    public function backup($tables)
    {
        $sql = "";
        foreach ($tables as $k => $table) {
            //获取数据
            $data = $this->db->query("select * from " . $table)->result_array();
            //获取表字段
            $fields_info = $this->db->query("desc " . $table)->result_array();
            $field_str = "";
            foreach ($fields_info as $ks => $vs) {
                $field_str .= "`" . $vs['Field'] . "`,";
            }
            $sql = $sql . "-- --------------------\r\n";
            $sql = $sql . "-- Records of " . $table . "\r\n";
            $sql = $sql . "-- --------------------\r\n";
            foreach ($data as $ks => $vs) {
                $rr = implode("','", array_values($vs));
                $sql .= "insert into `" . $table . "` (" . trim($field_str, ",") . ") values ('" . $rr . "');\r\n";
            }
        }
        $filename = date('YmdHis') . '.sql';
        $path = '/upload/db/' . $filename;
        write_file(FCPATH . $path, $sql);
        $date = date('Y-m-d H:i:s');
        $file_size = sprintf("%.2f", filesize(FCPATH . $path) / 1024);
        if ($file_size / 1024 > 1) {
            $file_size = ceil($file_size / 1024) . "MB";
        } else {
            $file_size = $file_size . "KB";
        }
        $data = array(
            'name' => $filename,
            'table' => implode(",", $tables),
            'path' => $path,
            'size' => $file_size,
            'date_entered' => $date
        );
        return $this->db->insert('back_up', $data);
    }

    public function getAllTable()
    {
        $table_list = array();
        $tables = $this->db->query("show tables")->result_array();
        foreach ($tables as $k => $v) {
            $table_list[] = $v['Tables_in_' . $this->db->database];
        }
        return $table_list;
    }

    public function getBackUpById($id)
    {
        return $this->db->get_where($this->_back_up, "id = $id")->row_array();
    }

    public function delete($id)
    {
        $back = $this->getBackUpById($id);
        $res = $this->db->delete($this->_back_up, array("id" => $id));
        if ($res) {
            @unlink(FCPATH.$back['path']);
            return true;
        }
        return false;
    }
}