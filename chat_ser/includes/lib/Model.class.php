<?php
if(!defined('CHT')){die();}
/**
 * 
 * @author lilizh
 *MODEL时面只做关于数据库的操作
 *1,这个里面不载入用户全局变量,只接收从外部传递进来的用户变量
 *2,其它继承模块只要继承MODEL方法就可以,尽量不要在子模块里重写MODEL方法
 */	
 class model {
 	
	public 		$db;
	public 		$table;
	public 		$web_config;
	protected 	$options	= array();
	protected 	$methods	= array('where','order','limit','field');
    public          $dbtype         = 'pdo';//pdo
	
 	function __construct($table_name='')
	{
                if($this->dbtype == 'msyql')
                {
                    $this->db	= new db();
                }elseif($this->dbtype == 'pdo')
                {
                    $this->db	= new pdo_db();
                }else{
                    $this->db	= new db();
                }
		
		
		$this->web_config	= C('web_config');
		
		if(method_exists($this,'_load'))
                {
                      $this->_load();
                }
		
		if($table_name)
		{
			$this->table	= $table_name;
		}
	}
	
	public function add($arr,$table='')
	{
		if(empty($table))
		{
			if( !empty($this->table) )
			{
				$act_table	= $this->table;
			}
		}else{
			$act_table	= $table;
		}
		
		if(empty($act_table) || empty($arr))
		{
			return false;
		}
		
		$res	= $this->db->_insert($arr,$act_table);
		
		return $res;
	}
	
	//这类方法不支持limit
 	public function find($table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		$tmp_arr	= $this->_options_to_sql();
		
		$res	= $this->db->_find($tmp_arr['sql'],$act_table,$tmp_arr['field']);
		return $res;
	}
	
	public function select($table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		$tmp_arr	= $this->_options_to_sql();

		$res	= $this->db->_select($tmp_arr['sql'],$act_table,$tmp_arr['field']);
		
		return $res;
		
	}
	
	public function query($sql)
	{
		$res	= $this->db->query($sql);
		return $res;
	}
	
 	//更新数据 BY 多个 field 在这个里机可以调用where 操作，且只能只能连贯where操作
	public function delete($table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		if(empty($act_table))
		{
			return false;
		}
		
		$tmp_arr	= $this->_options_to_sql();
		
		$res	= $this->db->_del($tmp_arr['sql'],$act_table);
		
		return $res;
	}
	
	//更新数据 BY 多个 field 在这个里机可以调用where 操作，且只能只能连贯where操作
	public function save($arr,$table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		if(empty($act_table))
		{
			return false;
		}
		
		$tmp_arr	= $this->_options_to_sql();
		
		$res	= $this->db->_update($tmp_arr['sql'],$arr,$act_table);
		
		return $res;
	}
	
	//更新记录数 记录数加$num
	public function up_field($where,$field,$num,$table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		if(empty($act_table))
		{
			return false;
		}
		
		$res	= $this->db->_up_field($where,$field,$num,$table);
	}
	
	//更新记录数,写一个比上面更灵活的函数
	public function setinc($field,$num,$table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		$tmp_arr	= $this->_options_to_sql();
		
		if(empty($tmp_arr['sql']))
		{
			return false;
		}
		
		$res	= $this->db->_setinc($tmp_arr['sql'],$field,$num,$act_table);
		
		return $res;
	}
	
	//记录数减小
 	public function setdec($field,$num,$table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		$tmp_arr	= $this->_options_to_sql();
		
		if(empty($tmp_arr['sql']))
		{
			return false;
		}
		
		$res	= $this->db->_setdec($tmp_arr['sql'],$field,$num,$act_table);
		
		return $res;
	}
	
	//获取结果集 $arr为后面的条件 这些都是老函数，都是在没有开发select函数及连贯操作前写的函数
 	public function get_all($where,$order,$limit,$table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		if(empty($act_table))
		{
			return false;
		}
		
		
		$res	= $this->db->get_all($where,$order,$limit,$act_table);
		
		return $res;
	}
	
	public function get_sql()
	{
		$res	= $this->db->get_last_sql();
		return $res;
	}
	
	//分析sql
	public function _options_to_sql()
	{
		$sql	= ' ';
	
		if(count($this->options)<1)
		{
			return '';
		}
		
		//options['where']只用字符串，而且只是一个字符串
		if(isset($this->options['where']))
		{
			$sql.='where ';
			$sql	= $sql.$this->options['where'][0];
			unset($this->options['where']);
		}
		
		if(isset($this->options['order']))
		{
			$sql	= $sql.' order by '.$this->options['order'][0];
			unset($this->options['order']);
		}
		
		if(isset($this->options['limit']))
		{
			if(strstr($this->options['limit'][0],','))
			{
				$sql	= $sql.' limit '.$this->options['limit'][0];
			}else{
				$sql	= $sql.' limit 0,'.$this->options['limit'][0];
			}
			unset($this->options['limit']);
		}
		
		if(isset($this->options['field']))
		{
			$_fields	= $this->options['field'][0];
			unset($this->options['field']);
		}
		
		$r_array	= array(
				'sql'	=> $sql,
				'field'	=> isset($_fields)?$_fields:'*'
				);
		
		return $r_array;
	}
	//20130829 现在的写入操作要把每个变理赋次值,太麻烦
	public function get_table_field($table='')
	{
                if($this->dbtype == 'pdo')
                {//PDO暂时不支持这个功能
                    
                    return false;
                }
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		$res	= $this->db->_get_table_field($act_table);
		
		return $res;
		
	}
	
	//赋值操作 接收POST数据,此方法只能在POST过来的数据中使用,与THINKPHP不同,不直接创建属性值,只返回数组  20130829
 	public function create($table='')
	{
		if(empty($table))
		{
			$act_table		= $this->table;
		}else{
			$act_table		= $table;
		}
		
		$f_name		= $this->web_config['table_dir'].$act_table.'.php';
		
		if(file_exists($f_name))
		{
			$res	= include_once $f_name;
			
			$arr	= array();
			
			foreach($_POST as $k=>$v)
			{
				if(in_array($k,$res['field']))
				{
					$arr[$k]	= $_POST[$k];
				}
			}
			
			return $arr;
		}else{
			$f_fields	= $this->get_table_field($act_table);
			
			$f_str		= '<?php return ';
			$f_str.= 'array( ';
			
			$field_str.= 'array( ';
			$state_str.= 'array( ';
			
			foreach($f_fields as $k=>$v)
			{
				$state_str.= ' \''.$v->name.'\' => array( ';
				$state_str.= ' \'not_null\' =>'.$v->not_null.',';
				$state_str.= ' \'primary_key\' =>'.$v->primary_key.',';
				$state_str.= ' \'type\' =>'.$v->type;
				
				$state_str.='),';
				$field_str.='\''.$v->name.'\',';
			}
			
			$state_str.=')';
			$field_str.=')';
			
			$f_str.= '\'field\'=>'.$field_str.',\'state\'=>'.$state_str;
			
			$f_str.= ') ?>';
			
			//这里要做一个错误判断，不然会进入死循环
			$fp		= fopen($f_name,'w+');
			//vp($f_str);
			file_put_contents($f_name,$f_str);
			
			fclose($fp);
			
			return $this->create($act_table);
		}

		return false;
	}
	
	//可以实现连贯操作
	public function __call($fun,$arg)
	{
		if(in_array($fun,$this->methods))
		{
			$this->options[$fun]	= $arg;
			return $this;
		}else{
			die('没有对应的方法!');
		}
		
		
	}
	
	function __destruct()
	{
		
	}
	
	}
	
	
	
