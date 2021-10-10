<?php
if(!defined('ML')){die();}
//主配置参数

$x_time	= '2014-07-21 12:50';
$x_time	= strtotime($x_time);

$pub_config = [];

$web_config = array_merge($web_config,$public_config);

define('ACTION_PATH',ROOT_PATH.'/action/home/');

$list_file = array( 
			LIB_PATH.'Action.class.php', 
			LIB_PATH.'Model.class.php', 
			LIB_PATH.'View.class.php', 
			LIB_PATH.'db.class.php', 
            LIB_PATH.'Pdo.class.php',
            LIB_PATH.'Redis_cache.class.php',
			LIB_PATH.'Chat.class.php',
			ORG_PATH.'member.class.php', 
			ORG_PATH.'page.class.php', 
			ORG_PATH.'images.class.php',
			 

	); 

	foreach($list_file as $tmp_file) 
	{ 
		include_once($tmp_file); 
	} 

    C('r_cfg',$r_cof);
	C('wcfg',$web_config); 
	C('db_config',$db_config); 
	C('pcfg',$pub_config); 
	C('cla_act','cla_act'); 
	C('cla_met','cla_met');
        
?>