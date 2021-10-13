<?php
    if(!defined('ML')){die('Access Denied');}   
//所有的缓存函数都写在这里来，方面后期的统一管理
    
    //用户token缓存函数
    function set_utoken_cache($uid,$val)
    {
        $expire     = 3600*24*2;//写死两天缓存
        $key        = 'us_tk_key_'.$uid;
        set_key_cache($key, $val,$expire);
    }
    
    //用户token缓存函数
    function get_utoken_cache($uid)
    {
        $key        = 'us_tk_key_'.$uid;
        $res        = get_key_cache($key);
        return $res;
    }
    
    
    function set_key_cache($key,$val,$expire='')
    {
        $rd = new redis_cache();
        $rd->set($key,$val,$expire);
        
    }
    
    function get_key_cache($key)
    {
        $rd = new redis_cache();
        $res    = $rd->get($key);
        return $res;
    }
    
    function get_group_cache($gid)
    {
        $tm_k   = 'group_info_'.$gid;        
        $group_info = get_key_cache($tm_k);
        $group_info = false;
        if($group_info == false)
        {
            $dGroup     = D('group');
            $dFriend    = D('friend');

            $g_info     = $dGroup->where('id='.$gid)->find();
            if(!$g_info)
            {
                return false;
            }

            $g_fri      = $dFriend->where('group_id='.$gid.' and state=1')->order('id asc')->select();
            if(!$g_fri)
            {
                return false;
            }

            $ulist  = array();
            //组装数据
            foreach($g_fri as $k=>$v)
            {
                $tmp_uinfo  = get_uinfo_cache($v->my_uid);
                $ulist[]    = array(
                    'uid'   => $tmp_uinfo['id'],
                    'uname' => $tmp_uinfo['uname'],
                    'name'  => $tmp_uinfo['name'],
                    'pic'   => $tmp_uinfo['pic'],
                    'state' => $tmp_uinfo['state'],
                    'ctime' => $v->c_time
                );
            }

            $ucount     = count($ulist);
            $avt_hei    = 11;
            if($ucount <= 4)
            {
                $avt_hei    = 18;
            }
            $group_info = array(
                'id'        => $g_info->id,
                'gname'     => $g_info->gname,
                'gnote'     => $g_info->gnote,
                'addtime'   => $g_info->addtime,
                'adduid'    => $g_info->adduid,
                'adduinfo'  => get_uinfo_cache($g_info->adduid),
                'ucount'    => $ucount,
                'ulist'     => $ulist,
                'uavt_hei'  => $avt_hei
            );
            
            $expire     = 3600*24*2;//写死两天缓存
            set_key_cache($tm_k, $group_info,$expire);
        }
        return $group_info;
    }
    //获取用户的缓存，用户表中的缓存,如果没有就回调
    function get_uinfo_cache($uid)
    {
        $rd = new redis_cache();
        $tm_k   = 'uinfo_'.$uid;
        $uinfo  = $rd->hget('userinfo',$tm_k);
//        $uinfo  = false;
        if($uinfo === false)
        {//回调
            $u_arr  = array();
            $dMember = D('member');
            $res    = $dMember->where('id='.$uid)->find();
            if($res)
            {
                $wcfg   = C('wcfg');
                $u_arr  = array(
                    'id'    => $res->id,
                    'uname' => $res->username,
                    'name'  => $res->name,
                    'pic'   => empty($res->pic)?'/'.$wcfg['member_img_dir'].'123.jpg':'/'.$wcfg['member_img_dir'].$res->pic,
                    'state' => $res->state,
                    'addtime'=>$res->addtime,
                    'uptime'=> $res->uptime,
                    'cr_ca_tm' => time()
                );
                $rd->hset('userinfo',$tm_k,$u_arr);
                return $u_arr;
            }else{
                return false;//就没有这个会员
            }
        }
        
        return $uinfo;
    }
    
    //获取指定用户的缓存(在socket建立连接后创建的缓存)
    function get_uf_hscache($uid)
    {
        $rd = new redis_cache();
        $tm_uf_k   = 'uf_'.$uid;//获取用户的缓存
        $uf_info   = $rd->hget('ufinfo',$tm_uf_k);
        return $uf_info;      
    }
   
    //获取指定连接缓存
    function get_fd_hscache($fd)
    {
        $rd = new redis_cache();
        $tm_k   = 'fd_'.$fd;
        $fd_info  = $rd->hget('fdinfo',$tm_k);
        return $fd_info;
    }
    
    

	
	
