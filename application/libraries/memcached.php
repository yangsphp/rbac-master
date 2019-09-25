<?php
/**
 * Created by PhpStorm.
 * User: 25754
 * Date: 2019/9/17
 * Time: 16:17
 */

include_once APPPATH . "/third_party/MemcacheCluster.php";

class Memcached
{
    public function set($key, $value, $expire = 0)
    {
        return MemcacheCluster::getInstance()->set($key, $value, $expire);
    }

    public function add($key, $value, $expire = 0)
    {
        return MemcacheCluster::getInstance()->add($key, $value, $expire);
    }

    public function delete($key)
    {
        return MemcacheCluster::getInstance()->delete($key);
    }

    public function get($key)
    {
        return MemcacheCluster::getInstance()->get($key);
    }
}