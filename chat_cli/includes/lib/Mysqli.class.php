<?php

/*
 * 
 *现在先这样放眘，有时间的话把DB层抽出来，mysql在数据量庞大的时候性能太低，
 *如果有可能的话到时换成nosql 
 *20130718
 *
 */

 class db {
	
 	public $link;
 	public $prefix;
 	public $queryID;
 	public $f_method;//返回类型
 	public $sql;//这个只是为测试使用的
 	
 	public function __construct()
	{
		$db_config			= C('db_config');
		$this->prefix		= $db_config['PREFIX'];
		$this->f_method		= mysql_fetch_object;//统一返回一个对象
		
		$this->link			= mysql_connect( $db_config['HOST'], $db_config['USER'], $db_config['PASSWORD'],true,131072);
		
		if ( !$this->link || (!empty($db_config['DB_NAME']) && !mysql_select_db($db_config['DB_NAME'], $this->link)) ) {
			die('数据库连接出错');
		}
		
		mysql_query("SET NAMES 'utf8'", $this->link);
		
		return $this->link;
	}
	
	public function _insert($arr,$table)
	{
		$table		= $this->prefix.$table;
		$d_arr		= $this->_insert_arr_to_sql($arr);
		$this->sql	= 'insert into '.$table.' '.$d_arr['filed'].' values '.$d_arr['val'];
		$res		= mysql_query($this->sql,$this->link);
		if($res)
		{
			return mysql_insert_id($this->link);
		}else{
			return false;
		}

	}
		
	public function _update($where,$arr,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql		= 'update '.$table.' set '.$up_str.$where;
		
		$query		= mysql_query($this->sql);
		
		if($query)
		{
			return true;
		}else{
			return false;
		}
	}
	
	//更新记录数,每次只能更新一张表中的一个字段
	public function _up_field($where,$field,$num,$table)
	{
		$table			= $this->prefix.$table;
		$where			= ' where '.$where;
		
		$this->sql		= 'update '.$table.' set '.$field.' = '.$field.' + '.$num.$where;
		
		$query			= mysql_query($this->sql);
		
		if($query)
		{
			return true;
		}else{
			return false;
		}
		
	}
	
	public function _setinc($where,$field,$num,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql		= 'update '.$table.' set '.$field.' = '.$field.' + '.$num.$where;
		
		$query		= mysql_query($this->sql);
		
		if($query)
		{
			return true;
		}else{
			return false;
		}	
	}
	
 	public function _setdec($where,$field,$num,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql		= 'update '.$table.' set '.$field.' = '.$field.' - '.$num.$where;
		
		$query		= mysql_query($this->sql);
		
		if($query)
		{
			return true;
		}else{
			return false;
		}	
	}
	
	public function query($sql)
	{
		$this->sql		= $sql;
		$this->query 	= mysql_query($this->sql, $this->link);
		
		if($this->query)
		{
			return true;
		}else{
			return false;
		}
	}
	

	//可用where的find方法 20131104 
	public function _find($where,$table,$field='')
	{
		$table	= $this->prefix.$table;
		
		if(empty($field))
		{
			$this->sql	= 'select * from '.$table.$where.' limit 1';
		}else{
			$this->sql	= 'select '.$field.' from '.$table.$where.' limit 1';
		}
		
		$query	= mysql_query($this->sql);
		if($query)
		{
			$res	= $this->fetch_method($query);
			return $res;
		}else{
			return FALSE;//出错
		}
	}
	
	public function _select($where,$table,$field='')
	{
		$table	= $this->prefix.$table;
		
		if(empty($field))
		{
			$this->sql	= 'select * from '.$table.$where;
		}else{
			$this->sql	= 'select '.$field.' from '.$table.$where;
		}

		$query	= mysql_query($this->sql);
		
		if($query)
		{
			while($rn	= $this->fetch_method($query))
			{
				$res[]	= $rn;
			}
			
			return $res;
		}else{
			return FALSE;//出错
		}
	}
	
	public function get($sql)
	{
		
	}
	
	public function _get_table_field($table)
	{
		$table		= $this->prefix.$table;
		$this->sql	= 'select * from '.$table;
		$res		= mysql_query($this->sql);

		while($rn = mysql_fetch_field($res))
		{
			$rns[]	= $rn;
		}
		
		return $rns;
		
	}
	
	/**
	 * 20130718
	 * @param unknown_type $where 传入的是字符串 
	 * @param unknown_type $order 传入数组
	 * @param unknown_type $limit 数组
	 * @param unknown_type $table 
	 */
	public function get_all($where='',$order='',$limit='',$table)
	{
		$table		= $this->prefix.$table;
		
		if(!empty($where))
		{
			$where		= ' where '.$where;
		}
		
		if(!empty($order))
		{
			foreach($order as $k=>$v)
			{
				$order_tmp_arr[]	= $k.' '.$v;
			}
			
			$order_tmp_str	= ' order by '.join(',',$order_tmp_arr);
		}
		
		if(!empty($limit))
		{
			list($m,$n)	= each($limit);
			
			$limit_tmp_str	= ' limit '.$m.','.$n;
		}
		
		$this->sql		= 'select * from '.$table.$where.$order_tmp_str.$limit_tmp_str;

		$rs			= false;
		
		$res		= mysql_query($this->sql);
		
		if($res)
		{
			$rs			= array();
			while($rn	= $this->fetch_method($res))
			{
				$rs[]	= $rn;
			}
		}
		
		return $rs;
	}
	
	//算了，这个函数到时分析下来会很长，先不这样做了，过些时候再说吧
	private function check_where($where)
	{
		$str	= '';
		foreach($where as $k=>$v)
		{
			switch($k)
			{
				case 'eq':
					foreach($v as $m=>$n)
					{
						$str[] 	= $m.'='.$n;	
					}
			}
		}
	}
	
	//这个函数先这样放着
	public function get_all_by_arr($arr)
	{
		$tmp_arr	= array();
		
		foreach($arr as $k=>$v)
		{
			if(is_numeric($v))
			{
				$tmp_arr[]	= $k.'='.$v;
			}else{
				$tmp_arr[]	= $k.'=\''.$v.'\'';
			}
		}
		
		//$sql	= 'select '
		
	}
	
	//通过传入sql获取所有结果集
	public function get_all_record($sql)
	{
		$res	= mysql_query($sql,$this->link);
		
		$rs		= false;
		
		if($res)
		{
			$rs			= array();
			while($rn	= $this->fetch_method($res))
			{
				$rs[]	= $rn;
			}
		}
		
		return $rs;
	}
	
	//插入操作
	public function _insert_arr_to_sql($arr)
	{
		$field	= array();
		$val	= array();
		//$arr不用判断
		foreach($arr as $k=>$v)
		{
			$field[]	= '`'.$k.'`';
			if(is_numeric($v))
			{
				$val[]		= $v;
			}else{
				$val[]		= '\''.$v.'\'';
			}
			
		}
		
		$fiel_str	= '('.join(',',$field).')';
		$val_str	= '('.join(',',$val).')';
		
		$arr['filed']	= $fiel_str;
		$arr['val']		= $val_str;
		
		return $arr;
	}
	
	//更新操作
	public function _update_arr_to_sql($arr)
	{
		$up_arr		= array();
		
		if(!empty($arr))
		{
			foreach($arr as $k=>$v)
			{
				if(is_string($v))
				{
					$up_arr[]	= '`'.$k.'`=\''.$v.'\'';
					
				}else{
					$up_arr[]	= '`'.$k.'`='.$v;
				}
			}
			
			$up_str		= join(',',$up_arr);
		}
		return $up_str;
	}
	
	//带条件删除
 	public function _del($where,$table)
	{
		$table		= $this->prefix.$table;
		
		$this->sql		= 'delete from '.$table.$where;
		
		$query		= mysql_query($this->sql);
		
		if($query)
		{
			return true;
		}else{
			return false;
		}
	}
	
	//这个是为了统一返回数据集的格式
	public function fetch_method($res)
	{
		$method	= $this->f_method;
		return $method($res);
	}
	
	public function get_last_sql()
	{
		return $this->sql;
	}
	
	function __destruct()
	{
		
	}
	
	}
	
	
	
