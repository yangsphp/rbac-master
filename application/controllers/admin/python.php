<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/5/4
 * Time: 14:30
 */

class Python extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_admin_view_dir();
    }

    public function pythonStart()
    {
        exec("calc");
    }
}