<?php

class chat{
		
        public function __construct()
        {

        }

        static public function start() {

        $paths 			= explode('/',trim($_SERVER['PATH_INFO'],'/'));
        $var[C('cla_act')]	= array_shift($paths);
        $var[C('cla_met')]	= array_shift($paths);
        $_GET			= array_merge($_GET,$var);

        $cla_act		= $_GET[C('cla_act')];//CLASS名
        $cla_met                = $_GET[C('cla_met')];//方法名

        if(!isset($cla_act)||empty($cla_act))
        {
                $cla_act	= 'index';
        }

        if(!isset($cla_met)||empty($cla_met))
        {
                $cla_met	= 'index';
        }

        $cla_act		= $cla_act.'Class';

        $action			= new $cla_act($cla_met);

        }
        
        public function __tostring()
        {
        
        }
	
}
	
	
	
