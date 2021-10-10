<?php

class redis_cache{
    
    private $redis;
    public $expire;
    
    public function __construct()
    {
        $r_cof      = C('r_cfg');
        $this->redis = new Redis;
        $this->redis->connect($r_cof['host'],$r_cof['port']);
        if(!empty($r_cof['password'])){
             $this->redis->auth($r_cof['password']);
         }

        $this->redis->select($r_cof['database']);
    }
    
    //设置字符串
    public function set($key, $val, $expire = false,$flags = '')
    {
        
        $val        = serialize($val);
        if($expire === false)
        {
            $expire = $this->expire;
        }
        
        if($expire >0)
        {
            return $this->redis->setex($key,$expire,$val);
        }else{
            return $this->redis->set($key,$val);
        }

    }

    //获取字符串
    public function get($key)
    {

        $res    = $this->redis->get($key);
        if($res)
        {
            return unserialize($res);
        }
        
        return  false;
    }
    
    public function delete($key='')
    {
        return $this->redis->delete($key);
    }
    
    //哈希操作 将数据写入哈希表
    public function hset($tableName,$field,$val)
    {
        $val    = serialize($val);
        return $this->redis->hSet($tableName,$field,$val);
    }
    //获取哈希表中field的值
    public function hget($tableName,$field)
    {
        $res    = $this->redis->hget($tableName,$field);

        if($res)
        {
            return unserialize($res);
        }
        
        return false;
    }
    
    public function hlen($tableName)
    {
        return $this->redis->hLen($tableName);
    }
    //删除哈希表中指定字段
    public function hdel($tableName,$field)
    {
        return $this->redis->hDel($tableName,$field);
    }

    //获取哈希表中指定表的所有值
    public function hvals($tableName)
    {
        $data = $this->redis->hvals($tableName);
        foreach($data as $key=>$val)
        {
            $data[$key] = unserialize($val);
        }
        return $data;
    }
    
    public function hkeys($tableName)
    {
        $res    = $this->redis->hKeys($tableName);
        return $res;
    }

    //批量添加
    public function hmset($tableName,$field,$value){
        return $this->redis->hMset($tableName,$field,$value);
    }

    //批量获取
    public function hmget($tableName,$field){
        return $this->redis->hmget($tableName,$field);
    }
    
    //列表
    public function lpush($key,$val,$expire = false)
    {
        $val        = serialize($val);
        if($expire === false)
        {
            $expire = $this->expire;
        }
        
        return $this->redis->lpush($key,$val,$expire);
    }
    
    public function lrange($key,$start,$limit)
    {
        $res    = $this->redis->lRange($key,$start,$limit);
        if($res)
        {
            return unserialize($res);
        }
        
        return  false;
    }
    
    public function flush()
    {
        //return $this->redis->flushAll();
    }

}

