<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

/**
 * 采集系统
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Collect extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
    }

    public function web()
    {
        $this->load->view('web.php');
    }

}