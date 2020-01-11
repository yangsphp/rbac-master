<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/8
 * Time: 8:44
 */

defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);

//后台公共控制
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkUserLogin();
        //$this->session->unset_userdata("menuList");
        $this->load->model('common_model');
        $this->load->model('auth_model');
        $this->adminLog();
    }

    public function checkUserLogin()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            //重定向到登录页面
            redirect(site_url("admin/login/index"));
            exit();
        }
        $this->menu($user);
    }

    function random($length, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz')
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    protected function adminLog()
    {
        $uri = strtolower($this->uri->uri_string());
        //获取权限
        $auth = $this->auth_model->get(false);
        $uri_name = "";
        foreach ($auth as $k => $v) {
            if (strtolower($v['url']) == $uri) {
                $uri_name = $v['name'];
            }
        }
        if ($uri_name) {
            $this->load->helper('cookie');
            if ($uri != 'admin/log/index') {
                $log_string = get_cookie('log_string');
                $md5_string = md5($uri_name);
                if ($log_string != $md5_string) {
                    $user = $this->session->userdata("user");
                    $data = array(
                        'user_id' => $user['userid'],
                        '_id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : 0,
                        'name' => $uri_name,
                        'date_entered' => date("Y-m-d H:i:s")
                    );
                    if ($this->common_model->insertAdminLog($data)) {
                        set_cookie("log_string", $md5_string, 86500);
                    }
                }
            }
        }
    }

    protected function menu($user)
    {
        $_admin = $this->config->item("admin");
        $_auth = $this->config->item("auth");
        $_role = $this->config->item("role");

        //获取导航栏
        $role = $this->db->select("r.name as role_name, r.auth")->from($_admin . " as a")->join($_role . " as r", "r.id = a.role_id", "left")->where("a.status = 1 and a.id=" . $user['userid'])->get()->row_array();
        $where = "is_menu=1 and status=1";
        if ($user['userid'] != 1) {
            $where .= " and id in({$role['auth']})";
        }
        $data = $this->db->where($where)->order_by("sort", "asc")->get($_auth)->result_array();
        $menuList = $this->getMenuTree($data);
        $user['role_name'] = $role['role_name'];
        $this->session->set_userdata("menuList", $menuList);
        $this->session->set_userdata("user", $user);
    }

    protected function getMenuTree($data, $parent_id = 0)
    {
        $tree = array();
        foreach ($data as $k => $v) {
            if ($v["parent_id"] == $parent_id) {
                unset($data[$k]);
                if (!empty($data)) {
                    $children = $this->getMenuTree($data, $v["id"]);
                    if (!empty($children)) {
                        $v["submenu"] = $children;
                    }
                }
                $tree[] = $v;
            }
        }
        return $tree;
    }
}

//前台公共控制
class Home_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('memcached', null, 'memcache');
    }
}
