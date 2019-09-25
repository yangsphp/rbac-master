<?php

/**
 * 一致性哈希memcache分布式，采用的是虚拟节点的方式解决分布均匀性问题,查找节点采用二分法快速查找
 */
class MemcacheCluster
{
    private $_node = array();
    private $_nodeData = array();
    private $_keyNode = 0;
    private $_memcache = null;

    //每个物理服务器生成虚拟节点个数 [注：节点数越多，cache分布的均匀性越好，同时set get操作时，也更耗资源，10台物理服务器，采用200较为合理]
    private $_virtualNodeNum = 2;

    private function __construct()
    {
        /* 放入配置文件 */
        $config = array(
            '127.0.0.1:11211',
        );

        if (!$config) throw new Exception('Cache config NULL');
        foreach ($config as $key => $value) {
            for ($i = 0; $i < $this->_virtualNodeNum; $i++) {
                $this->_node[sprintf("%u", crc32($value . '_' . $i))] = $value . '_' . $i;
            }
        }
        ksort($this->_node);
    }

    private function __clone()
    {
    }

    /**
     * 单例，保证只有一个实例
     */
    static public function getInstance()
    {
        static $memcacheObj = null;
        if (!is_object($memcacheObj)) {
            $memcacheObj = new self();
        }
        return $memcacheObj;
    }

    /**
     * 根据key做一致性hash后连接到一台物理memcache服务器
     * @param string $key
     */
    private function _connectMemcache($key)
    {
        $this->_nodeData = array_keys($this->_node);
        $this->_keyNode = sprintf("%u", crc32($key));
        $nodeKey = $this->_findServerNode();
        //如果超出环，从头再用二分法查找一个最近的，然后环的头尾做判断，取最接近的节点
        if ($this->_keyNode > end($this->_nodeData)) {
            $this->_keyNode -= end($this->_nodeData);
            $nodeKey2 = $this->_findServerNode();
            if (abs($nodeKey2 - $this->_keyNode) < abs($nodeKey - $this->_keyNode)) $nodeKey = $nodeKey2;
        }
        //var_dump($this->_node[$nodeKey]); 127.0.0.1:11211_20
        list($config, $num) = explode('_', $this->_node[$nodeKey]);
        if (!$config) throw new Exception('Cache config Error');
        if (!isset($this->_memcache[$config])) {
            $this->_memcache[$config] = new Memcache;
            list($host, $port) = explode(':', $config);
            $this->_memcache[$config]->connect($host, $port);
        }
        return $this->_memcache[$config];
    }

    /**
     * 采用二分法从虚拟memcache节点中查找最近的节点
     * @param unknown_type $m
     * @param unknown_type $b
     */
    private function _findServerNode($m = 0, $b = 0)
    {
        $total = count($this->_nodeData);
        if ($total != 0 && $b == 0) $b = $total - 1;
        if ($m < $b) {
            $avg = intval(($m + $b) / 2);
            if ($this->_nodeData[$avg] == $this->_keyNode) return $this->_nodeData[$avg];
            elseif ($this->_keyNode < $this->_nodeData[$avg] && ($avg - 1 >= 0)) return $this->_findServerNode($m, $avg - 1);
            else return $this->_findServerNode($avg + 1, $b);
        }
        if (abs($this->_nodeData[$b] - $this->_keyNode) < abs($this->_nodeData[$m] - $this->_keyNode)) return $this->_nodeData[$b];
        else return $this->_nodeData[$m];
    }

    public function set($key, $value, $expire = 0)
    {
        return $this->_connectMemcache($key)->set($key, json_encode($value), 0, $expire);
    }

    public function add($key, $value, $expire = 0)
    {
        return $this->_connectMemcache($key)->add($key, json_encode($value), 0, $expire);
    }

    public function get($key)
    {
        return json_decode($this->_connectMemcache($key)->get($key), true);
    }

    public function delete($key)
    {
        return $this->_connectMemcache($key)->delete($key);
    }

}