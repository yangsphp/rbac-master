<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
        $this->load->model('log_model');
    }
    public function get() {
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $arr = $this->log_model->get($start, $limit);
        $count = count($this->log_model->get());
        echo json_encode(array(
            'err' => 0,
            'data' => $arr,
            'total' => $count
        ));
    }
    public function index()
    {
        $clear_flag = $this->checkUserButtonPrivilege('admin/log/clear');
        $data['clear_flag'] = $clear_flag;
        $htm["layout"] = $this->load->view('log/index', $data, true);
        $this->load->view('frame',$htm);
    }

    public function delete_op() {
        $result = $this->log_model->delete();
        if ($result) {
            die(json_encode(array("code" => 0, "msg" => "清理日志成功")));
        }
        die(json_encode(array("code" => 1, "msg" => "清理日志失败")));
    }

}