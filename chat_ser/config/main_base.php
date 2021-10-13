<?php 
if(!defined('CHT')){die();}

//网站根目录 公共目录配置文件
date_default_timezone_set('Asia/Shanghai');

$_rootpath  = str_replace('\\','/',dirname(dirname(__FILE__)));

define('ROOT_PATH',	$_rootpath);
define('LIB_PATH',	ROOT_PATH.'/includes/lib/');
define('ORG_PATH',	ROOT_PATH.'/includes/org/');

include_once(ROOT_PATH.'/includes/common/functions.php');
include_once(ROOT_PATH.'/includes/common/cachefun.php');

$db_config	= array(
	'HOST'		=> '192.168.3.1:3306',
	'USER'		=> 'root',
	'PASSWORD'	=> '123456',
	'PREFIX'	=> 'xt_',
	'DB_NAME'	=> 'chat'
);

$r_cof  = array(
    'host'      => '192.168.3.1',
    'port'      => 6379,
    'password'  => '',
    'database'  => 0,
    'expire'    => 86400 //默认过期时间
);

$web_config = array(

);

define('ACTION_PATH',ROOT_PATH.'/action/'); 

$list_file = array( 
        LIB_PATH.'Action.class.php', 
        LIB_PATH.'Model.class.php', 
        LIB_PATH.'db.class.php', 
        LIB_PATH.'Pdo.class.php', 
        LIB_PATH.'Chat.class.php', 
        LIB_PATH.'Redis_cache.class.php', 

        ORG_PATH.'member.class.php', 
        ORG_PATH.'page.class.php', 
        ORG_PATH.'images.class.php',

    ); 

    foreach($list_file as $tmp_file) 
    { 
            include_once($tmp_file); 
    } 

    C('r_cfg',$r_cof);
    C('db_config',$db_config); 
    C('web_config',$web_config); 


?>

