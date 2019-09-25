<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/4/3
 * Time: 12:13
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends Home_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->set_home_view_dir();
    }

    public function index()
    {
        //要设置随机缓存时间，防止缓存失效，大的访问量造成数据库崩溃
        $time = rand(1, 10);
        $key = md5("yy");
        $cacheValue = $this->memcache->get($key);
        if (!$cacheValue) {
            //随机保存时间
            $flag = $this->memcache->set($key, "yang", $time);
            if ($flag) {
                echo "缓存时间：".$time."秒";
            }
        }
        echo $cacheValue;
    }
}