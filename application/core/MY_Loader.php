<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/3
 * Time: 12:12
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader
{
    /**
     * @Description: 设置前台模板页面路径
     * @Author: Yang
     */
    public function set_home_view_dir()
    {
        $this->_ci_view_paths = array(APPPATH . HOME_VIEW_DIR => TRUE);
    }

    /**
     * @Description: 设置后台模板页面路径
     * @Author: Yang
     */
    public function set_admin_view_dir()
    {
        $this -> _ci_view_paths = array(APPPATH . ADMIN_VIEW_DIR => TRUE);
    }
}