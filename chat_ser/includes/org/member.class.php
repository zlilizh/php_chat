<?php
if(!defined('CHT')){die();}
//这里只做写入COOKIE操作
 class member {
	
 	function __construct()
	{

	}
	
	public function _login($arr)
	{
		$this->clear();
		foreach($arr as $k=>$v)
		{
			$_time	= time()+1*3600;//设置cookie时间为1个小时
			setcookie($k,$v,$_time,'/');
		}
	
	}
	
	public function _getall()
	{
		foreach($_COOKIE as $k=>$v)
		{
			$arr[$k]	= $v;
		}
	
		return $arr;
	}
	
	public function is_login()
	{
		$arr	= $this->_getall();
  	   	$key	= md5(md5(md5($arr['uid'].$arr['username']).$arr['tstr']).$arr['t']); 
   		if($key!=$arr['k'])
   		{
			return false;
    	}else{
    		$this->_login($arr);//刷新登录时间
    		return true;
    	}
	}
	
	public function clear()
	{
		$_time	= time()-1000;
		foreach($_COOKIE as $k=>$v)
		{
			setcookie($k,'',$_time,'/');
		}
	}	
	
	function __destruct()
	{
		
	}
	
	}
	
	
	
