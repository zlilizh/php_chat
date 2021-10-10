<?php
if(!defined('CHT')){die();}
//此action是供所有模块共用，里面包含所有公共变量

class ActionClass {
		
        public $protec_array	= array('auto_load','_load');//受保护的实例方法名，就是这些方法被当作页面来实例化时会报错

        public function __construct()
        {
            if(method_exists($this,'auto_load'))
            {//公共的里面调用
                    $this->auto_load();
            }

        }

        public function __call($name,$val)
        {
            //header('')
            $this->error('error!');
            //echo '未找到对应的地址';

        }

    public function __tostring()
    {

    }

}
	
	
	
