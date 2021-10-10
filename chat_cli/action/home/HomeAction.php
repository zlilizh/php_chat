<?php
	if(!defined('ML')){die();}	
	class HomeAction extends ActionClass{
		public $sys;
		public $uinfo;
		public $uid;
        protected function auto_load()
        {
        	if(method_exists($this,'_load'))
        	{//自动加载
        		$this->_load();
        	}

        	$this->uid		= check_admin();

        	//加载公共模板变量
        	$this->_pb();

        	//之后在这里添加登录功能
        	$this->uinfo	= $this->get_u_info();
        }
        
        //在这里定义一些模板里面的公共变量
        public function _pb()
        {	
        	$pcfg		= C('pcfg');
        	$this->assign('pcfg',$pcfg);

        	$dSConfig	= D('sys_config');
        	$tmp_res	= $dSConfig->select();
        	foreach($tmp_res as $k=>$v)
        	{
        		$tmp_obj[$v->k_field]	= $v->k_value;
        	}
        	$this->sys	= &$tmp_obj;

        }
        
        //此函数只是为了统一所有的当前地址的变量名
		protected function ad_address($array)
		{
			$this->assign('now_add',$array);
		}
		
		//获取用户信息
		public function get_u_info()
		{
			$dAdmin		= D('member');
			$uid		= $this->uid;
			$uinfo		= $dAdmin->where('id='.$uid)->find();
			//vp($uinfo);
			if($uinfo)
			{
				$this->assign('sys_uinfo',$uinfo);
				return $uinfo;
			}else{
				$_out_url	= $this->wcfg['w_d_url'].'/login/outlogin';
				$this->go_header($_out_url);
			}

		}
				
		//统一所有的是交地址变量名
		protected function ad_s_url($url)
		{
			$url		= $this->wcfg['w_d_url'].$url;
			$this->assign('s_url',$url);
		} 
	}
