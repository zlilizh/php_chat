<?php

//此CLASS瑄是在ACTION里面被调用合用的
class view {
		
		public $web_config;
		public $class_name;//控制器名
        public $val;//变量名
        public $method_name;//实例化对象对应的方法名称
		
		public function __construct($class_name,$web_config,$method)
		{
			$this->web_config	= $web_config;
			$this->class_name	= strtolower($class_name);
			$this->method_name  = $method;
		}
		
		public function template($file='')
		{
			if(!empty($this->val))
			{
				extract($this->val);
			}
			if(empty($file))
			{
				$tpl	    = ROOT_PATH.$this->web_config['template'].$this->class_name.'/'.$this->method_name.'.html';
				include_once($tpl);
			}else{
				$tmp_arr	= explode(':',$file);
				$an_file	= join('/',$tmp_arr);
				if(count($tmp_arr)==1)
				{//如果只传入文件名，就载入当前CLASS下面的文件
					$tpl		= ROOT_PATH.$this->web_config['template'].$this->class_name.'/'.$an_file.'.html';
				}else{
					$tpl		= ROOT_PATH.$this->web_config['template'].$an_file.'.html';
				}
				
				include_once($tpl);
			}
            
		}
        
        public function assign($name,$value)
        {
            $this->val[$name]   = $value;
        }
        
	
	}
	
	
	
