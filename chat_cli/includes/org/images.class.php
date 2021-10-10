<?php

	//图片处理类文件
	class pic_class{
		
		public $config;
		public $img_dir;
		public $is_upload;

		public function __construct()
		{
			$this->is_upload= false;
		}
		
		//上传个人形象照
		public function upload_member_img($f_img,$dir)
		{
			$tmp_new_name	= date('YmdHis').rand(0,999);
			$new_name		= $tmp_new_name.'.'.end(explode('.',$f_img['name']));
            $bing_name		= 'big_'.$tmp_new_name.'.'.end(explode('.',$f_img['name']));
            //$res			= $this->move_s2_pic($f_img,$dir,600,600,$bing_name,2);
			$res			= $this->move_s_pic($f_img,$dir,350,350,$new_name,2);
			$res			= $this->move_s_pic($f_img,$dir,100,100,$new_name);
			
			if($res)
			{
				return $new_name;
			}else{
				return false;
			}
		}
                
                public function upload_chat_img($f_img,$dir)
                {
                    $tmp_new_name	= date('YmdHis').rand(0,999);
                    $tmp_ext_arr	= explode('.',$f_img['name']);
                    $tmp_ext	    = end($tmp_ext_arr);
                    $new_name		= $tmp_new_name.'.'.$tmp_ext;

                    $this->move_s_pic($f_img,$dir,100,100,$new_name);
                    $res		= $this->move_pic($f_img['tmp_name'],$dir.$new_name);

                    if($res)
                    {
                            return $new_name;
                    }else{
                            return false;
                    }
                }

		function move_upload($f_img,$bafile,$width='',$height='',$t='')
		{
			$width		=$width?$width:'';
			$height		=$height?$height:'';
			$tmp_iname	= date('YmdHis').rand(0,100);
			$new_name	= $tmp_iname.".".end(explode('.',$f_img['name']));
			$s_new_name	= 'sm_'.$tmp_iname.'.'.end(explode('.',$f_img['name']));
			if(empty($t)){
				if(empty($width)||empty($height)){
					return $this->move_pic($f_img['tmp_name'],$bafile.$new_name);
				}else{
					return $this->move_s_pic($f_img,$bafile,$width,$height,$new_name);
				}
			}else{
				$this->move_s_pic($f_img,$bafile,50,50,$new_name);
				$this->move_s_pic($f_img,$bafile,30,30,$s_new_name);
				$res	=  $this->move_pic($f_img['tmp_name'],$bafile.$new_name);
				if($res)
				{
					return $new_name;
				}else{
					return false;
				}
			}
		}
		function move_pic($f,$name){//直接上传图片
			$r_note				= move_uploaded_file($f,$name);
			$this->is_upload	= true;
			if($r_note){
				return $name;
			}else{
				$res	= $this->copy_img($f,$name);
				if($res)
				{
					return $name;
				}else{
					return false;
				}
			}
		}
		//处理图片,这个函数会把图片截取处理
		function move_s_pic($f_img,$bafile,$width,$height,$new_name,$t=1){//经过处理再上传
			$imginfo	= getimagesize($f_img['tmp_name']);
			switch($imginfo['mime']){
					case 'image/jpeg':
						$imgcreate	= 'imagecreatefromjpeg';
						$imgmethod	= 'imagejpeg';
						break;
					case 'image/gif':
						$imgcreate	= 'imagecreatefromgif';
						$imgmethod	= 'imagegif';
						break;
					case 'image/png':
						$imgcreate	= 'imagecreatefrompng';
						$imgmethod	= 'imagepng';
						break;
				}
				$src_img	= $imgcreate($f_img['tmp_name']);
				$owcy		= (float)substr($imginfo[0]/$width,0,5);
				$ohcy		= (float)substr($imginfo[1]/$height,0,5);

				if($owcy<$ohcy)
				{
					$imginfo[1]	= intval(ceil($height * $owcy)); 
				}else{
					$imginfo[0]	= intval(ceil($width * $ohcy)); 
				}

				$desc_img	= imagecreatetruecolor($width,$height);
				imagecopyresampled($desc_img,$src_img,0,0,0,0,$width,$height,$imginfo[0],$imginfo[1]);

				if($t==2){
					$nimg=$bafile.$new_name;
				}else{
					$nimg=$bafile."sm_".$new_name;
				}
				
				$this->new_name=$nimg;
				$imgmethod($desc_img, $nimg);
				imagedestroy($desc_img);
				imagedestroy($src_img);
				return $nimg;
		}
		
		//等比例处理处理,缩小宽或高或原图不变,保证原图的比例,不截取图片
		function move_s2_pic($f_img,$bafile,$width,$height,$new_name,$t=1){//经过处理再上传
			$imginfo	= getimagesize($f_img['tmp_name']);
			switch($imginfo[mime]){
					case 'image/jpeg':
						$imgcreate	= imagecreatefromjpeg;
						$imgmethod	= imagejpeg;
						break;
					case 'image/gif':
						$imgcreate	= imagecreatefromgif;
						$imgmethod	= imagegif;
						break;
					case 'image/png':
						$imgcreate	= imagecreatefrompng;
						$imgmethod	= imagepng;
						break;
				}
				
				
				if($width >= $imginfo[0])
				{
					if($height >= $imginfo[1])
					{//上传原图
						
						if($t==2){
							$tmp_new_name	= $bafile.$new_name;
						}else{
							$tmp_new_name	= $bafile.'sm_'.$new_name;
						}
						
						$res	=  $this->move_pic($f_img['tmp_name'],$tmp_new_name);
						if($res)
						{
							return $new_name;
						}else{
							return false;
						}
					}else{
						$ohcy		= (float)substr($imginfo[1]/$height,0,5);
						$width		= intval(ceil($imginfo[0] / $ohcy)); 
					}
				}else{
					
					if($height >= $imginfo[1])
					{
						$owcy		= (float)substr($imginfo[0]/$width,0,5);
						$height		= intval(ceil($imginfo[1] / $owcy)); 
					}else{
						$ohcy		= (float)substr($imginfo[1]/$height,0,5);
						$owcy		= (float)substr($imginfo[0]/$width,0,5);
						//echo $ohcy.'-'.$owcy;
						$int_ohcy	= intval($ohcy * 100);
						$int_owcy	= intval($owcy * 100);
						if($int_ohcy >= $int_owcy)
						{
							$width		= intval(ceil($imginfo[0] / $ohcy)); 
						}else{
							//echo '--'.$width.'--';
							$height		= intval(ceil($imginfo[1] / $owcy)); 
						}
					}
				}
				//echo $width.'-'.$height;die;
				$src_img	= $imgcreate($f_img['tmp_name']);
				$desc_img	= imagecreatetruecolor($width,$height);
				imagecopyresampled($desc_img,$src_img,0,0,0,0,$width,$height,$imginfo[0],$imginfo[1]);
				
				if($t==2){
					$nimg=$bafile.$new_name;
				}else{
					$nimg=$bafile."sm_".$new_name;
				}
				
				$this->new_name=$nimg;
				$imgmethod($desc_img, $nimg);
				imagedestroy($desc_img);
				imagedestroy($src_img);
				return $nimg;
				
		}
		
		/*
		 * 此函数是配合上面的函数使用，在一张图片的原始图片和缩小图都小于要求的大小的时候，
		 * 直接上传图片功能只能用一次,所以这个函数是配合直接上传图片的时候用的
		 */
		
		function copy_img($f_img,$new_name)
		{
			$imginfo	= getimagesize($f_img);
			var_dump($imginfo);
			switch($imginfo[mime]){
					case 'image/jpeg':
						$imgcreate	= imagecreatefromjpeg;
						$imgmethod	= imagejpeg;
						break;
					case 'image/gif':
						$imgcreate	= imagecreatefromgif;
						$imgmethod	= imagegif;
						break;
					case 'image/png':
						$imgcreate	= imagecreatefrompng;
						$imgmethod	= imagepng;
						break;
				}

				$src_img	= $imgcreate($f_img);
				$desc_img	= imagecreatetruecolor($imginfo[0],$imginfo[1]);
				imagecopyresampled($desc_img,$src_img,0,0,0,0,$imginfo[0],$imginfo[1],$imginfo[0],$imginfo[1]);

				$imgmethod($desc_img, $new_name);
				imagedestroy($desc_img);
				imagedestroy($src_img);
				return $new_name;
		}

	}
	
	
	
