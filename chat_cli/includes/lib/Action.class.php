<?php

//此action是供所有模块共用，里面不包含任何独立模块的逻辑

abstract class ActionClass {
		
		public $db_config,$wcfg;
		public $class_name;//控制器名
        public $method_name;//实例化对象对应的方法名称
        public $view;//视图类
        public $protec_array	= array('auto_load','_load');//受保护的实例方法名，就是这些方法被当作页面来实例化时会报错
        public $pub_config;
		
		public function __construct($method)
		{
			if(in_array($method,$this->protec_array))
			{
				die('出错了');
			}

			//$this->_url_center();
			
			$this->db_config	= C('db_config');
			$this->wcfg			= C('wcfg');
			$class_name			= get_class($this);
			$this->class_name	= substr($class_name,0,-5);
			$this->method_name  = $method;
			$this->view			= new view($this->class_name,$this->wcfg,$method);//视图对象

			if(method_exists($this,'auto_load'))
			{//公共的里面调用
				$this->auto_load();
			}

			
			$this->assign('wcfg',$this->wcfg);
           
			$this->$method();
		}
		
		public function template($file='')
		{		
			$this->view->template($file);
		}
		
        public function __pb()
        {
        	ob_start();
        		system("ipconfig/all");
        		$c	= ob_get_contents();
        	ob_clean();
        	
        	preg_match("/(([a-zA-Z0-9]){2}-){5,}+([a-zA-Z0-9]{2}){1}/i",$c,$arr);
        	
        	return $arr;
        }
        
        public function assign($name,$value)
        {
            $this->view->assign($name,$value);
        }
        
        public function success($str='',$url='',$wait=2)
        {
        	if(empty($str))
        	{
        		$str	= '数据提交成功!';
        	}
        	if(empty($url))
        	{
        		$url	= $_SERVER["HTTP_REFERER"];
        	}	
        	$this->assign('str',$str);
        	$this->assign('waitSecond',$wait);
        	$this->assign('jumpUrl',$url);
        	$this->template('public:success');
        	exit();
        }
        
		public function error($str='',$url='')
        {
        	if(empty($str))
        	{
        		$str	= '数据提交失败!';
        	}
        	if(empty($url))
        	{
        		$url	= $_SERVER["HTTP_REFERER"];
        	}	
        	$this->assign('str',$str);
        	$this->assign('jumpUrl',$url);
        	$this->template('public:error');
        	exit();
        }
        
        //$arr传入进来时不能为空  此函数本来是想做为系统的一个url规范使用 后面感觉太麻烦，弃用了20130804
        public function _url($arr='')
        {
        	//$url		= WEB_URL.'/member.php';
        	$url		= $this->wcfg['w_d_url'];
        	
        	if(empty($arr))
        	{
        		
        		return $url;
        		
        	}else{
        		
        		$tmp_arr	= array();
        		
        		foreach($arr as $k=>$v)
        		{
        			$tmp_arr[]	= $k.'='.$v;
        		}
        		 
        		$tmp_str	= join('&',$tmp_arr);
        		$url		= $url.'?'.$tmp_str;
        		return $url;
        		
        	}
        	
        }
        
        //设置模板页提交的SUB地址 现在为了开发速度就放到这个里面来吧！
        public function url_set($arr)
        {
        	$sub_url	= $this->_url($arr);
        	$this->assign('s_url',$sub_url);
        }
        
        //统一返回josn格式数据
        public function return_json($arr)
        {
        	$tmp	= json_encode($arr);
        	echo $tmp;
        	exit();
        }

        public function rtnerror($msg = '异常'){
            $r_array	= [
                '_state'=> 'error',
                '_msg'	=> $msg,
            ];
            $this->return_json($r_array);
        }

        public function rtnsuc($arr = ['_msg' => '成功']){
            $r_array	= [
                '_state' => 'ok'
            ];

            $r_array = array_merge($r_array,$arr);
            $this->return_json($r_array);
        }
        
        public function js_jump($msg,$url='')
        {
        	if(empty($url))
        	{
        		$url	= WEB_URL;
        	}
        	echo '<script type="text/javascript">alert(\''.$msg.'\');window.location.href="'.$url.'";</script>';
        }
        
        public function go_to_by_url($url)
        {
        	echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
        	exit();
        }
        
        public function go_header($url)
        {
        	header('Location:'.$url);
        	exit();
        }
        
        
        
        
        public function __call($name,$val)
        {
            //header('')
            $this->error('未找到对应的地址!');
            //echo '未找到对应的地址';
            die;
        }
        public function __tostring()
        {
        
        }
	
	}
	
	
	
