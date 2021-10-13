<?php 
if(!defined('ML')){die('Access Denied');}
define('WEB_URL','http://www.csct.com');

$db_config	= array(
	'HOST'		=> '192.168.3.1:3306',
	'USER'		=> 'root',
	'PASSWORD'	=> '123456',
	'PREFIX'	=> 'xt_',
	'DB_NAME'	=> 'chat'
);
$web_config = array(
    'template'  => '/templates/home/default/',//前台模板页
    'w_url'     => WEB_URL,
    'w_d_url'   => WEB_URL.'/index.php',
    'emoji_arr' => $emoji_arr,
    'ws_addr'   => 'ws://192.168.3.1:9501'
);

$r_cof  = array(
    'host'      => '192.168.3.1',
    'port'      => 6379,
    'password'  => '',
    'database'  => 0,
    'expire'    => 86400 //默认过期时间
);

