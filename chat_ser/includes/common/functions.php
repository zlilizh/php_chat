<?php
 if(!defined('CHT')){die();}

    //自动载入类
    spl_autoload_register(function ($class) {
        $class_name = ucfirst($class);
        if(substr($class,-7) == 'Service')
        {
            $filename   = ROOT_PATH.'/service/'.$class_name . '.php';
        }else{
            $filename   = ACTION_PATH.$class_name . '.php';
        }
        if(file_exists($filename))
        {
            require_once $filename;
        }else{
            echo $filename;exit;
            echo 'Error';
        }
    });
    
    function bigimg_to_sm($img_addr)
    {
        $img_arr    = explode('/',$img_addr);
        $img_len    = count($img_arr);
        $img_xb     = $img_len -1 ;
        $img_arr[$img_xb] = 'sm_'.$img_arr[$img_xb];

        $img_addr   = join('/',$img_arr);
        
        return $img_addr;
    }
    //使用静态变量
    function C($name,$val=null)
    {
        static $_config     = array();
        
        if (is_null($val))
        {
            return isset($_config[$name]) ? $_config[$name] : null;
        }
        
        $_config[$name]     = $val;
        return;      

    }	
       
    //管理员加密算法
    function admin_md5($str,$arr='')
    {
    	$res	= md5(md5($str).'swoolechat');
    	
    	if(!empty($arr))
    	{
    		$tmp_str	= '';
    		foreach($array as $k=>$v)
    		{
    			$tmp_str.=md5($v);
    		}
    		
    		$res	=md5($res.$tmp_str);
    	}
    	
    	return $res;
    }
 
    
    function D($name='')
    {
    	$o	= new model($name);
    	return $o;
    }
    
    //判断管理员是否登录
    function check_admin()
    {
    	session_start();
    	$admin_id	= $_SESSION['xt_member_id'];
    	
    	if($admin_id)
    	{
    		return $admin_id;
    	}else{
    		$_url	= WEB_URL.'/index.php/login';
    		header('Location:'.$_url);
    		return false;
    	}
    }
    
    
    //调试函数
    function vp($arr,$t=1)
    {
    	echo '<pre>';
    	
    	print_r($arr);
    	
    	if($t==1)
    	{
    		die;
    	}
   	}
   	
   	
   	function den_code($_string, $type = 'D', $key = '', $expiry = 0) {
   		
   		$ckey_length 		= 4;
   		$au_th				= 'swoolechat';

   		$key 				= md5($key ? $key : $au_th);
   	
   		$keya 				= md5(substr($key, 0, 16));
   		$keyb 				= md5(substr($key, 16, 16));
   		$keyc 				= $ckey_length ? ($type == 'D' ? substr($_string, 0, $ckey_length):substr(md5(microtime()), -$ckey_length)) : '';
   		$cryptkey 			= $keya.md5($keya.$keyc);
   		$key_length 		= strlen($cryptkey);

   		$_string			= $type == 'D' ? base64_decode(substr($_string, $ckey_length)) :sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($_string.$keyb), 0, 16).$_string;
   		$string_length 		= strlen($_string);
   		$result 			= '';
   		$box 				= range(0, 255);
   		$rndkey 			= array();

   		for($i = 0; $i <= 255; $i++) {
   			
   			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
   			
   		}

   		for($j = $i = 0; $i < 256; $i++) {
   			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
   			$tmp = $box[$i];
   			$box[$i] = $box[$j];
   			$box[$j] = $tmp;
   		}

   		for($a = $j = $i = 0; $i < $string_length; $i++) {
   			$a = ($a + 1) % 256;
   			$j = ($j + $box[$a]) % 256;
   			$tmp = $box[$a];
   			$box[$a] = $box[$j];
   			$box[$j] = $tmp;
   			$result .= chr(ord($_string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
   		}
   		
   		if($type == 'D') {
   			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
   				return substr($result, 26);
   			} else {
   				return '';
   			}
   		} else {
   			return $keyc.str_replace('=', '', base64_encode($result));
   		}
   	}
   	
   	
	//地址ID转成地址函数
   	function cid_to_customer($id)
   	{
   		if($id==0||$id=='')
   		{
   			return '';
   		}
   		
   		static $sta_address;
   		$web_config	= C('wcfg');

   		$file		= $web_config['cache_dir'].'customer.php';

   		if(file_exists($file))
   		{
   			if(empty($sta_address))
   			{
   				$sta_address	= require_once $file;
   			}
   			
   			return $sta_address[$id];

   		}else{
   			$dCustomer	= D('customer');
   			$res		= $dCustomer->order('id asc')->select();
   			$str		= "<?php \nif(!defined('ML')){die('Access Denied');} \nreturn array(\n";
   			
   			foreach($res as $k=>$v)
   			{
   				$str.="\t".$v->id."=>'$v->company($v->name)',\n";
   			}
   			
   			$str.=");\n ?>";
   			
			$fp		= fopen($file,'w+');
			file_put_contents($file,$str);
			
			fclose($fp);
			
			return cid_to_customer($id);
   		}

   	}
   	
   	//地址ID转成地址函数
   	function admin_id_to_name($id)
   	{
   		if($id==0||$id=='')
   		{
   			return '';
   		}
   		
   		static $sta_admin;
   		$web_config	= C('wcfg');

   		$file		= $web_config['cache_dir'].'admin.php';

   		if(file_exists($file))
   		{
   			if(empty($sta_admin))
   			{
   				$sta_admin	= require_once $file;
   			}
   			
   			return $sta_admin[$id];

   		}else{
   			$dAdmin		= D('admin');
   			$res		= $dAdmin->order('id asc')->select();
   			$str		= "<?php \nif(!defined('ML')){die('Access Denied');} \nreturn array(\n";
   			
   			foreach($res as $k=>$v)
   			{
   				$str.="\t".$v->id."=>'$v->name',\n";
   			}
   			
   			$str.=");\n ?>";
   			
			$fp		= fopen($file,'w+');
			file_put_contents($file,$str);
			
			fclose($fp);
			
			return admin_id_to_name($id);
   		}

   	}
   	
   	//地址ID转成地址函数
   	function admin_id_to_pic($id)
   	{
   		if($id==0||$id=='')
   		{
   			return '';
   		}

   		static $sta_admin_img;
   		$web_config	= C('wcfg');

   		$file		= $web_config['cache_dir'].'admin_pic.php';

   		if(file_exists($file))
   		{
   			if(empty($sta_admin_img))
   			{
   				$sta_admin_img	= require_once $file;
   			}
   			
   			return $sta_admin_img[$id];

   		}else{

   			$dAdmin		= D('admin');
   			$res		= $dAdmin->order('id asc')->select();
   			$str		= "<?php \nif(!defined('ML')){die('Access Denied');} \nreturn array(\n";
   			
   			$c_dir		= '/'.$web_config['member_img_dir'];
   			foreach($res as $k=>$v)
   			{
   				if(empty($v->pic))
   				{
   					$str.="\t".$v->id."=>'".$c_dir."sm_empty.jpg',\n";
   				}else{
   					$str.="\t".$v->id."=>'".$c_dir."sm_".$v->pic."',\n";
   				}
   			}
   			
   			$str.=");\n ?>";
   			
			$fp		= fopen($file,'w+');
			file_put_contents($file,$str);
			
			fclose($fp);
			
			return admin_id_to_pic($id);
   		}

   	}
   	
   	//行业ID转换成行业函数
   	function category_to_name($id)
   	{
   		if($id==0||$id=='')
   		{
   			return '';
   		}
   		
   		static 	$sta_category;
   		$web_config	= C('web_config');
   		$file		= $web_config['cache_dir'].'category.php';

   		if(file_exists($file))
   		{
   			if(empty($sta_category))
   			{
   				$sta_category	= require_once $file;
   			}
   			
   			return $sta_category[$id];

   		}else{
   			$cate		= D('category');
   			$res		= $cate->order('id asc')->select();
   			$str		= "<?php \nif(!defined('ML')){die('Access Denied');}\n \nreturn array(\n";
   			
   			foreach($res as $k=>$v)
   			{
   				$str.="\t".$v->id."=>'$v->name',\n";
   			}
   			
   			$str.="); \n?>";
   			
			$fp		= fopen($file,'w+');
			file_put_contents($file,$str);
			
			fclose($fp);
			
			return category_to_name($id);
   		}
   	}
   	
   	//获取传递过来的类别ID
   	function get_p_catid()
   	{
   		$_catid		= $_POST['catid'];
   		return end($_catid);
   	}
   	
   	//处理JOB中的查询
   	function job_where()
   	{
   		
   	}
   	
   	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @return mixed
	 */
	function get_client_ip($type = 0) {
		$type       =  $type ? 1 : 0;
	    static $ip  =   NULL;
	    if ($ip !== NULL) return $ip[$type];
	    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        $pos    =   array_search('unknown',$arr);
	        if(false !== $pos) unset($arr[$pos]);
	        $ip     =   trim($arr[0]);
	    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
	    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
	        $ip     =   $_SERVER['REMOTE_ADDR'];
	    }
	    // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));
	    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	    return $ip[$type];
	}
	
	//缓存一些data数据 $data会被序列化保存 $time 是分钟
	function set_cache_data($data,$file,$time=1)
	{
		$std		= new stdClass();
		$_time		= time() + $time*60;
		$std->time	= $_time;
		$std->data	= $data;
		$std		= serialize($std);
		$str		= "<?php \nif(!defined('ML')){die('Access Denied');}\n \nreturn ";
		$str.='\''.$std.'\'';		
		$str.=" \n?>";
   			
		$fp		= fopen($file,'w+');
		file_put_contents($file,$str);
		fclose($fp);
	}
	
	//与上面对应的获取$data数据 这是两个很简单的函数,最好的还是诮用memcache
	function get_cache_data($file)
	{
		if(file_exists($file))
		{
			$now_time	= time();
			$res		= require_once $file;
	        $res		= unserialize($res);
	        if($res->time > $now_time)
	        {
	        	return $res->data;
	        }else{
	        	return false;
	        }
		}else{
			return false;
		}
	}
	
	//取除所有的HTML
	function remove_html($content) {
//	   $content	= SpHtml2Text($content);
//	   return $content;
	   $content	= strip_tags($content);
	   $content	= str_replace(' ','',$content);
	   $content	= str_replace('&nbsp;','',$content);
	   $content = preg_replace("/<a[^>]*>/i", "", $content);  
	   $content = preg_replace("/<\/a>/i", "", $content);   
	   $content = preg_replace("/<div[^>]*>/i", "", $content);  
	   $content = preg_replace("/<\/div>/i", "", $content);      
	   $content = preg_replace("/<!--[^>]*-->/i", "", $content);//注释内容
	   $content = preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式  
	   $content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式  
	   $content = preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式     
	   $content = preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式      
	   $content = preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式   
	   $content = preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式   
	   $content = preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式   
	   $content = preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式   
	   $content = preg_replace("/face=.+?['|\"]/",'',$content);//去除样式只允许小写正则匹配没有带 i 参数
	   $content = htmlspecialchars($content);
	   return $content;
	}
	
	//织梦去除HTML
	function SpHtml2Text($str)
	{
		$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
		$alltext = "";
		$start = 1;
		for($i=0;$i<strlen($str);$i++)
		{
			if($start==0 && $str[$i]==">")
			{
				$start = 1;
			}
			else if($start==1)
			{
				if($str[$i]=="<")
				{
					$start = 0;
					$alltext .= " ";
				}
				else if(ord($str[$i])>31)
				{
					$alltext .= $str[$i];
				}
			}
		}
		$alltext = str_replace("　"," ",$alltext);
		$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
		$alltext = preg_replace("/[ ]+/s"," ",$alltext);
		return $alltext;
	}
	
	//字符串载取函数
	function xsubstr($string, $length, $suffix = '...', $start = 0) {
		if($start) {
			$tmp = dsubstr($string, $start);
			$string = substr($string, strlen($tmp));
		}
		$strlen = strlen($string);
		if($strlen <= $length) return $string;
		$string = str_replace(array('&quot;', '&lt;', '&gt;'), array('"', '<', '>'), $string);
		//$length = $length - strlen($suffix);
		$str = '';

		$n = $tn = $noc = 0;
		while($n < $strlen)	{
			$t = ord($string{$n});
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) break;
		}
		if($noc > $length) $n -= $tn;
		$str = substr($string, 0, $n);

		$str = str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $str);
		return $str == $string ? $str : $str.$suffix;
	}
	
	//读取指定目录下的文件 返回绝对目录地址
	function dir_file($dir)
	{
		if(is_dir($dir))
		{
			$tmp_handler	= dir($dir);
			while(($file=$tmp_handler->read())!==false)
			{
				if($file!=='.'&&$file!=='..')
				{
					$arr[]		= $dir.$file;
				}
			}
			closedir($tmp_handler);
			return $arr;
		}
		
		return false;
	}
	
	function made_stock_num($pre)
	{
		$stk_num		= $pre.date('Ymd').(time() - strtotime(date('Y-m-d'))).rand(0,9);
		return $stk_num;
	}
	
	function ouput_csv($filename,$data)
	{
		header("Content-type:text/csv");   
	    header("Content-Disposition:attachment;filename=".$filename);   
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	    header('Expires:0');   
	    header('Pragma:public');   
	    echo $data;   
	    exit();
	}

	
	
