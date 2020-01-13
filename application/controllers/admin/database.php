<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('database_model');
        $this->load->helper('file');
    }

    public function get()
    {
        $flag = $this->input->get('flag');
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $arr = $this->database_model->get($flag, $start, $limit);
        $total_data = $this->database_model->get($flag);

        $return = array(
            'err' => 0,
            'data' => $arr['data'],
            'total' => count($total_data['data'])
        );
        if (count($total_data) == 2) {
            $return['total_size'] = $total_data['total_size'];
        }
        echo json_encode($return);
    }

    public function index()
    {
        $repair_flag = $this->checkUserButtonPrivilege('admin/database/repair');
        $optimize_flag = $this->checkUserButtonPrivilege('admin/database/optimize');
        $edit_flag = $this->checkUserButtonPrivilege('admin/database/edit_op');
        $backup_flag = $this->checkUserButtonPrivilege('admin/database/backup');
        $callback_flag = $this->checkUserButtonPrivilege('admin/database/callback');
        $download_flag = $this->checkUserButtonPrivilege('admin/database/download');
        $delete_flag = $this->checkUserButtonPrivilege('admin/database/delete_op');
        $data['repair_flag'] = $repair_flag;
        $data['edit_flag'] = $edit_flag;
        $data['optimize_flag'] = $optimize_flag;
        $data['backup_flag'] = $backup_flag;
        $data['callback_flag'] = $callback_flag;
        $data['download_flag'] = $download_flag;
        $data['delete_flag'] = $delete_flag;
        $htm["layout"] = $this->load->view('database/index', $data, true);
        $this->load->view('frame', $htm);
    }

    public function repair()
    {
        $table = $this->input->get("table");
        if ($table) {
            $this->database_model->repair($table);
            die(json_encode(array("code" => 0, "msg" => "修复表{$table}成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "修复表{$table}失败")));
    }

    public function optimize()
    {
        $table = $this->input->get("table");
        if ($table) {
            $this->database_model->optimize($table);
            die(json_encode(array("code" => 0, "msg" => "优化表{$table}成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "优化表{$table}失败")));
    }

    public function dict()
    {
        $table = $this->input->get("table");
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $dict = $this->database_model->dict($table);
        $data['csrf'] = $csrf;
        $data['dict'] = $dict;
        $data['table'] = $table;
        $html = $this->load->view('database/dict', $data, true);
        echo json_encode(array("title" => $table, "html" => $html));
    }

    public function sql()
    {
        $id = $this->input->get("id");
        $back = $this->database_model->getBackUpById($id);
        $file = fopen(FCPATH . $back['path'], 'rb');
        $content = "";
        while (($sql = fgets($file)) !== false) {
            $content .= $sql."<br/>";
        }
        fclose($file);
        $data['content'] = $content;
        $html = $this->load->view('database/sql', $data, true);
        echo json_encode(array("title" => $back['name'], "html" => $html));
    }

    public function edit_op()
    {
        $table = $this->input->post("table");
        $field = $this->input->post("Field");
        $type = $this->input->post("Type");
        if ($table) {
            //修改
            $result = $this->database_model->update($table, $field, $type);
            if ($result) {
                die(json_encode(array("code" => 0, "msg" => "修改字典成功")));
            }
            die(json_encode(array("code" => 1, "msg" => "修改字典失败")));
        }
        die(json_encode(array("code" => 1, "msg" => "数据表不存在")));
    }

    public function callback()
    {
        $id = $this->input->get("id");
        $back = $this->database_model->getBackUpById($id);
        $this->db->trans_start();
        $file = fopen(FCPATH . $back['path'], 'rb');
        $table_array = explode(",", $back['table']);
        foreach ($table_array as $table) {
            $this->db->query("truncate $table");
        }
        while (($sql = fgets($file, 4096)) !== false) {
            if (strpos($sql, '--') === false){
                $this->db->query($sql);
            }
        }
        fclose($file);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            die(json_encode(array("code" => 1, "msg" => "数据还原失败")));
        }
        $this->db->trans_commit();
        die(json_encode(array("code" => 0, "msg" => "数据还原成功")));

    }

    public function backup()
    {
        $table = $this->input->get("tables");
        if (!$table) {
            //获取所有的表
            $table = $this->database_model->getAllTable();
        } else {
            $table = explode(',', $table);
        }
        $result = $this->database_model->backup($table);
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "备份成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "备份失败")));
    }

    public function delete_op()
    {
        $id = $this->input->get("id");
        $result = $this->database_model->delete($id);
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "删除备份成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "删除备份失败")));
    }
}