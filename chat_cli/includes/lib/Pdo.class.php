<?php

/*
 * 
 *只是为了了解下swoole的websocket而已，搞的还需要写个pdo连接mysql的类
 *
 */
 class pdo_db {
	
 	public $link;
 	public $prefix;
 	public $queryID;
 	public $f_method;//返回类型
 	public $sql;//这个只是为测试使用的
 	
 	public function __construct()
	{
		$db_config		= C('db_config');
		$this->prefix		= $db_config['PREFIX'];
				
                $dsn='mysql:host='.$db_config['HOST'].';dbname='.$db_config['DB_NAME'];
                try {
                    $this->link = new PDO($dsn, $db_config['USER'], $db_config['PASSWORD']); 
                    $this->link->query("SET NAMES utf8"); 
                } catch (PDOException $e) {
                    die ('Error2');
                }
                
		return $this->link;
	}
	
	public function _insert($arr,$table)
	{
		$table		= $this->prefix.$table;
		$d_arr		= $this->_insert_arr_to_sql($arr);
		$this->sql	= 'insert into '.$table.' '.$d_arr['filed'].' values '.$d_arr['val'];
		$res		= $this->link->query($this->sql);
		if($res)
		{
			return $this->link->lastInsertId();
		}else{
			return false;
		}

	}
		
	public function _update($where,$arr,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql	= 'update '.$table.' set '.$up_str.$where;
		
		$query		= $this->link->query($this->sql);
		
		if($query  === false)
		{
			return false;
		}else{
			return true;
		}
	}
	
	//更新记录数,每次只能更新一张表中的一个字段
	public function _up_field($where,$field,$num,$table)
	{
		$table			= $this->prefix.$table;
		$where			= ' where '.$where;
		
		$this->sql		= 'update '.$table.' set '.$field.' = '.$field.' + '.$num.$where;
		
		$query			= $this->link->query($this->sql);
		
		if($query  === false)
		{
			return false;
		}else{
			return true;
		}
		
	}
	
	public function _setinc($where,$field,$num,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql	= 'update '.$table.' set '.$field.' = '.$field.' + '.$num.$where;
		
		$query		= $this->link->query($this->sql);
		
		if($query  === false)
		{
			return false;
		}else{
			return true;
		}	
	}
	
 	public function _setdec($where,$field,$num,$table)
	{
		$table		= $this->prefix.$table;
		
		$up_str		= $this->_update_arr_to_sql($arr);
		
		$this->sql	= 'update '.$table.' set '.$field.' = '.$field.' - '.$num.$where;
		
		$query		= $this->link->query($this->sql);
		
		if($query  === false)
		{
			return false;
		}else{
			return true;
		}	
	}
	
	public function query($sql)
	{
		$this->sql	= $sql;
		$this->query 	= $this->link->query($this->sql, $this->link);
		
		if($query  === false)
		{
			return false;
		}else{
			return true;
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
		
		$query	= $this->link->query($this->sql);
		if($query === false)
		{
			return FALSE;//出错
		}else{
                        $res	= $query->fetch(PDO::FETCH_OBJ);
                        //$res	= $query->fetch(PDO::FETCH_ASSOC);
			return $res;
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

		$query	= $this->link->query($this->sql);
		
		if($query === false)
		{
			return FALSE;//出错
		}else{
                        $res = $query->fetchAll();
                        //*转结果集为对象
                        $nres   = array();
                        foreach($res as $k=>$v)
                        {
                            $tobj   = new stdClass;
                            foreach($v as $kk=>$vv)
                            {
                                $tobj->$kk  = $vv;
                            }
                            
                            $nres[] = $tobj;
                            unset($tobj);
                        }
                        $res = $nres;
                       
			return $res;
		}
	}
	
	public function _get_table_field($table)
	{
            return false;//暂时不支持这个功能
		$table		= $this->prefix.$table;
		$this->sql	= 'select * from '.$table;
		$res		= $this->link->query($this->sql);

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
		exit('sdfsadf');
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
                echo $this->sql;
                $query        = $this->link->query($this->sql);
		
		if($query === false)
		{
			return FALSE;//出错
		}else{
            $res = $query->fetchAll();
            if($this->f_method == 1)
            {
                //*转结果集为对象
                $nres   = array();
                foreach($res as $k=>$v)
                {
                    $tobj   = new stdClass;
                    foreach($v as $kk=>$vv)
                    {
                        $tobj->$kk  = $vv;
                    }

                    $nres[] = $tobj;
                    unset($tobj);
                }
                $res = $nres;
            }
                       
			return $res;
		}
	}

	//通过传入sql获取所有结果集
	public function get_all_record($sql)
	{
                $this->sql    = $sql;
		$query        = $this->link->query($sql);
		
		if($query === false)
		{
			return FALSE;//出错
		}else{
                        $res = $query->fetchAll();
                        //*转结果集为对象
                        $nres   = array();
                        foreach($res as $k=>$v)
                        {
                            $tobj   = new stdClass;
                            foreach($v as $kk=>$vv)
                            {
                                $tobj->$kk  = $vv;
                            }
                            
                            $nres[] = $tobj;
                            unset($tobj);
                        }
                        $res = $nres;
                       
			return $res;
		}
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
		
		$this->sql	= 'delete from '.$table.$where;
		
		$query		= $this->link->query($this->sql);
		
		if($query === false)
		{
			return false;
		}else{
			return true;
		}
	}
	
	public function get_last_sql()
	{
		return $this->sql;
	}
	
	function __destruct()
	{
		
	}
	
}
	
	
	
