<?php 
if(!defined('ML')){die();}
//error_reporting(0);
//网站根目录 公共目录配置文件

date_default_timezone_set('Asia/Shanghai');

$_rootpath			= str_replace('\\','/',dirname(dirname(__FILE__)));

define('ROOT_PATH',	    $_rootpath);
define('LIB_PATH',	    ROOT_PATH.'/includes/lib/');
define('MODEL_PATH',    ROOT_PATH.'/includes/model/');
define('ORG_PATH',      ROOT_PATH.'/includes/org/');

include_once(ROOT_PATH.'/config/emoji.php');
include_once(ROOT_PATH.'/config/db_config.php');//数据库配置文件
include_once(ROOT_PATH.'/includes/common/functions.php');
include_once(ROOT_PATH.'/includes/common/cachefun.php');

$public_config			= array(
		
		'member_img_dir'	=> 'images/member/',//会员中心会员头象
        'chat_img_dir'      => 'images/chat/',//聊天中的图片存储地址
		'csv_dir'		    => 'cache/csv/',//CSV目录
        'annex_dir'	        => 'images/annex/',//附件上传地址
		
		);
if(defined('ML'))
{
	include_once(ROOT_PATH.'/config/'.ML.'/config.php');
}else{
	die('出错!');
}