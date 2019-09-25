<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/27
 * Time: 14:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Index extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
    }

    public function index()
    {
        $htm["layout"] = $this->load->view('index', null, true);
        $this->load->view('frame',$htm);
    }
}