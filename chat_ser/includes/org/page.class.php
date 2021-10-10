<?php
if(!defined('CHT')){die();}
class page{
	
	protected 	$allcount;//总记录数
    protected 	$nowpage;//当前页
    public 		$pagesize;//每页显示数
    protected 	$p;//分页变量
    public 		$firstrow;//开始记录数
    public 		$ps;

   	function __construct($allcount,$pagesize=20,$nowpage='')
   	{
   		$this->p			= 'p';
		$this->allcount		= $allcount;
		$this->pagesize		= $pagesize;
		if(empty($nowpage))
		{
			$this->nowpage		= !empty($_GET[$this->p])?intval($_GET[$this->p]):1;
		}else{
			$this->nowpage		= $nowpage;
		}
		
		$this->firstrow		= ($this->nowpage - 1)*$this->pagesize;
		$this->ps			= $this->firstrow.','.$this->pagesize;
		
   	}
   
	//分页函数   
	function pg($page_num=5,$url='')
   	{
   		if(strstr($url,'?')===false)
   		{
   			$p			= '?'.$this->p;
   		}else{
   			$p			= '&'.$this->p;
   		}

   		$all_count		= $this->allcount;
   		$page_size		= $this->pagesize;
   		$dq_page		= $this->nowpage;
   		$all_page		= ceil($all_count/$page_size);
   		//echo $all_count.','.$page_size.','.$dq_page.','.$all_page.'---';
   		$up_row			= $dq_page-1;
   		$next_row		= $dq_page+1;

   		if($up_row > 0){
   			$str_u		=  "<span class=\"all_num\">共 ".$this->allcount." 条记录</span><a href='".$url.$p."=".$up_row."'>上一页</a>";
   		}else{
   			$str_u		=  "<span class=\"all_num\">共 ".$this->allcount." 条记录</span><span>上一页</span>";
   		}
   		
   		if($next_row <= $all_page){
   			$str_n		= "<a href='".$url.$p."=".$next_row."'>下一页</a>";
   		}else{
   			$str_n		= "<span>下一页</span>";
   		}
   		
   		if($all_page <= $page_num){//总页数小于显示数
   			
   			for($i=1;$i<=$all_page;$i++){
   				
   				if($dq_page == $i)
   				{
   					$str.="<span>".$i."</span>";
   				}else{
   					$str.="<a href='".$url.$p."=".$i."'>".$i."</a>";
   				}
   				
   			}
   			
   		}else{
   			$q_page		= floor($page_num/2);//前面显示数
   			$h_page		= $page_num-$q_page-1;//后面显示数
   			if($dq_page <= $q_page){//小于前面显示数时，从前面开始向后数
   				for($i=1;$i<=$page_num;$i++){
   					if($dq_page == $i)
   					{
   						$str.="<span>".$i."</span>";
   					}else{
   						$str.="<a href='".$url.$p."=".$i."'>".$i."</a>";
   					}
   				}
   				if($all_page-$page_num <=1){
   					$str.="<a href='".$url.$p."=".$all_page."'>".$all_page."</a>";
   				}else{
   					$str.="...<a href='".$url.$p."=".$all_page."'>".$all_page."</a>";
   				}
   			}else if($dq_page+$h_page>=$all_page){//当前面大于后面临界页的时候从后向前退
   				$e=$all_page-$page_num+1;//如果开始数与显示数之和大于总数时候，从后向前算
   				$h=$dq_page+$h_page;
   				if($e-1<=1){
   					$str="<a href='".$url.$p."=1'>1</a>";
   				}else{
   					$str="<a href='".$url.$p."=1'>1</a>...";
   				}
   				for($i=$e;$i<=$all_page;$i++){
	   				if($dq_page == $i)
	   				{
	   					$str.="<span>".$i."</span>";
	   				}else{
	   					$str.="<a href='".$url.$p."=".$i."'>".$i."</a>";
	   				}
   				}
   			}else{
   				$e=($dq_page-$q_page)>1?($dq_page-$q_page):2;
   				$h=$dq_page+$h_page;
   				if($e-1<=1){
   					$str="<a href='".$url.$p."=1'>1</a>";
   				}else{
   					$str="<a href='".$url.$p."=1'>1</a>...";
   				}
   				for($i=$e;$i<=$h;$i++){
	   				if($dq_page == $i)
	   				{
	   					$str.="<span>".$i."</span>";
	   				}else{
	   					$str.="<a href='".$url.$p."=".$i."'>".$i."</a>";
	   				}
   				}
   				if($all_page-$h<=1){
   					$str.="<a href='".$url.$p."=".$all_page."'>".$all_page."</a>";
   				}else{
   					$str.="...<a href='".$url.$p."=".$all_page."'>".$all_page."</a>";
   				}
   			}
   		}
   		
   		$str=$str_u.$str.$str_n;
   		
   		return $str;
   	}
   	
	//分页函数   
	function ajax_pg($page_num=5,$onclik='xt_stock.get_ic(this)')
   	{
   		if(strstr($url,'?')===false)
   		{
   			$p			= '?'.$this->p;
   		}else{
   			$p			= '&'.$this->p;
   		}

   		$all_count		= $this->allcount;
   		$page_size		= $this->pagesize;
   		$dq_page		= $this->nowpage;
   		$all_page		= ceil($all_count/$page_size);
   		//echo $all_count.','.$page_size.','.$dq_page.','.$all_page.'---';
   		$up_row			= $dq_page-1;
   		$next_row		= $dq_page+1;

   		if($up_row > 0){
   			$str_u		= "<span class=\"all_num\">共 ".$this->allcount." 条记录</span><a data='$up_row' onclick='$onclik' href='javascript:void(0)'>上一页</a>";
   		}else{
   			$str_u		= "<span class=\"all_num\">共 ".$this->allcount." 条记录</span><span>上一页</span>";
   		}
   		
   		if($next_row <= $all_page){
   			$str_n		= "<a  data='$next_row' onclick='$onclik' href='javascript:void(0)'>下一页</a>";
   		}else{
   			$str_n		= "<span>下一页</span>";
   		}
   		
   		if($all_page <= $page_num){//总页数小于显示数
   			
   			for($i=1;$i<=$all_page;$i++){
   				
   				if($dq_page == $i)
   				{
   					$str.="<span>".$i."</span>";
   				}else{
   					$str.="<a data='$i' onclick='$onclik' href='javascript:void(0)'>".$i."</a>";
   				}
   				
   			}
   			
   		}else{
   			$q_page		= floor($page_num/2);//前面显示数
   			$h_page		= $page_num-$q_page-1;//后面显示数
   			if($dq_page <= $q_page){//小于前面显示数时，从前面开始向后数
   				for($i=1;$i<=$page_num;$i++){
   					if($dq_page == $i)
   					{
   						$str.="<span>".$i."</span>";
   					}else{
   						$str.="<a data='$i' onclick='$onclik' href='javascript:void(0)'>".$i."</a>";
   					}
   				}
   				if($all_page-$page_num <=1){
   					$str.="<a data='$all_page' onclick='$onclik' href='javascript:void(0)'>".$all_page."</a>";
   				}else{
   					$str.="...<a data='$all_page' onclick='$onclik' href='javascript:void(0)'>".$all_page."</a>";
   				}
   			}else if($dq_page+$h_page>=$all_page){//当前面大于后面临界页的时候从后向前退
   				$e=$all_page-$page_num+1;//如果开始数与显示数之和大于总数时候，从后向前算
   				$h=$dq_page+$h_page;
   				if($e-1<=0){
   					$str="<a data='1' onclick='$onclik' href='javascript:void(0)'>1</a>";
   				}else{
   					$str="<a data='1' onclick='$onclik' href='javascript:void(0)'>1</a>...";
   				}
   				for($i=$e;$i<=$all_page;$i++){
	   				if($dq_page == $i)
	   				{
	   					$str.="<span>".$i."</span>";
	   				}else{
	   					$str.="<a data='$i' onclick='$onclik' href='javascript:void(0)'>".$i."</a>";
	   				}
   				}
   			}else{
   				$e=($dq_page-$q_page)>1?($dq_page-$q_page):2;
   				$h=$dq_page+$h_page;
   				if($e-1<=1){
   					$str="<a data='1' onclick='$onclik' href='javascript:void(0)'>1</a>";
   				}else{
   					$str="<a data='1' onclick='$onclik' href='javascript:void(0)'>1</a>...";
   				}
   				for($i=$e;$i<=$h;$i++){
	   				if($dq_page == $i)
	   				{
	   					$str.="<span>".$i."</span>";
	   				}else{
	   					$str.="<a data='$i' onclick='$onclik' href='javascript:void(0)'>".$i."</a>";
	   				}
   				}
   				if($all_page-$h<=1){
   					$str.="<a data='$all_page' onclick='$onclik' href='javascript:void(0)'>".$all_page."</a>";
   				}else{
   					$str.="...<a data='$all_page' onclick='$onclik' href='javascript:void(0)'>".$all_page."</a>";
   				}
   			}
   		}
   		
   		$str=$str_u.$str.$str_n;
   		
   		return $str;
   	}

}
