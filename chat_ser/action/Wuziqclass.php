<?php

if(!defined('CHT')){die();}	
class wuziqclass extends ActionClass{
		
    protected function _load()
    {

    }
    
    public function message($frame,$data,$ser)
    {
        $_data   = json_decode($data);
        $rtn     = array(
            'mt'    => 1, //返回的消息类型,0是错误消息,1是注册消息，不需要发送数据,2发送的数据，需要返回msg
            'cmt'   => 1 //当消息类型是２时，值为1代表对方不在线，只给我自己返回消息即可，为2时，表示对方也在线，并正在和我聊天中，给彼此都发送消息，为3时，表示他在线，但没和我聊天，发送消息提示即可
        );
        if($_data->type ==1) {//第一次连接注册连接信息

            $rd = new redis_cache();
            $tm_fd_k = 'fd_' . $frame->fd;//每次的新边接都会是一个新的fd,所以同一个用户有两个连接不冲突,可以在聊天的同时保持五子棋的正常
            $tm_fd_arr = array(
                'fid' => $frame->fd,
                'uid' => $_data->data->uid,
                'avt' => $_data->data->avt,
                'uname' => $_data->data->uname,
                'type' => 'wzq',
                'cr_ca_tm' => time()
            );
            //在这里做下验证
            $tk_info = get_utoken_cache($tm_fd_arr['uid']);
            if ($tk_info['token'] == $_data->data->token) {

            } else {
                $rtn['mt'] = 1;
                $rtn['check'] = false;//表示验证没有通过，返回后断开连接

                return $rtn;
            }


            $rd->hset('fdinfo', $tm_fd_k, $tm_fd_arr);

            //用户这里要区分开
            $tm_uf_k = 'uf_' . $_data->data->uid;
            $old_uf = $rd->hget('wzlinkuinfo', $tm_uf_k);
            if ($old_uf === false) {//说明是第一次来连接
                $rtn['od_fd'] = false;
            } else {//说明是在另一个窗口又打开了一个连接，建立新的连接，同时关闭之前的连接
                $rtn['od_fd'] = $old_uf['fid'];
            }
            $tm_uf_arr = array(
                'fid' => $frame->fd,
                'uid' => $_data->data->uid,
                'avt' => $_data->data->avt,
                'uname' => $_data->data->uname,
                'cr_ca_tm' => time()
            );
            $rd->hset('wzlinkuinfo', $tm_uf_k, $tm_uf_arr);
        }else if($_data->type == 2){//当有用户坐下或者离开时,向tablist里面的所有人发送坐下消息,向在桌子里面的所有人发送坐下消息

            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'wzlinkuinfo');//获取用户的缓存

            $sitkey     = $_data->data;
            $sitinfo    = get_key_cache($sitkey);

            if($sitinfo == false){

                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $tblthtml   = $sitinfo['tblthtml'];
            $poshtml    = $sitinfo['poshtml'];
            $tableid    = $sitinfo['tbid'];
            $posid      = $sitinfo['posid'];

            $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$tblthtml,'dt'=>'tbl'];
            $rtn    = $this->sendtablelistsms(2,$tbrtn,$rtn);

            $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$poshtml,'dt'=>'pos'];
            $rtn    = $this->sendtablelistsms(2,$tbrtn,$rtn,$tableid);

            return $rtn;

        }else if($_data->type == 3){//五子棋聊天

            $rtn['mt']  = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'wzlinkuinfo');//获取用户的缓存

            $wzckey     = $_data->data;
            $wzchat     = get_key_cache($wzckey);

            if($wzchat == false){

                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $wzchhtml   = $wzchat['chathtml'];
            $tableid    = $wzchat['tbid'];

            $tbrtn  = ['tbid'=>$tableid,'info'=>$wzchhtml,'dt'=>'chat'];
            $rtn    = $this->sendtablelistsms(3,$tbrtn,$rtn,$tableid);
            
            return $rtn;

        }else if($_data->type ==4){//检查棋局状态,向前端页面发送相关数据,向桌子的每个用户下发自己的状态

            $rtn['mt']  = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'wzlinkuinfo');//获取用户的缓存

            $wzckey     = $_data->data;
            $wzchat     = get_key_cache($wzckey);

            if($wzchat == false){

                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $wzchhtml   = $wzchat['chathtml'];
            $tableid    = $wzchat['tbid'];
            $lasttime   = $wzchat['lasttime'];

            $tabInfo    = D('wztable')->where('id='.$tableid)->find();
            $tabstaArr  = [
                0 => '空位置',
                1 => '对战中',
                2 => '等待中'
            ];

            $tbrtn  = ['tbid'=>$tableid,'chathtml'=>$wzchhtml,'dt'=>'stgm'];
            $tbrtn['tbstate']   = $tabstaArr[$tabInfo->state];
            $tbrtn['isstart']   = $wzchat['isstart'];
            $tbrtn['us1']       = ['upbj'=>0,'tip'=>''];
            $tbrtn['us2']       = ['upbj'=>0,'tip'=>''];
            if($tabInfo->state == 1)
            {

                $tmpuserid          = $tabInfo->user1id;
                if($tabInfo->nowposid == 1){
                    $tbrtn['us1']       = ['upbj'=>1,'tip'=>'*','wating'=>15];
                }else{
                    $tbrtn['us2']       = ['upbj'=>1,'tip'=>'*','wating'=>15];
                    $tmpuserid          = $tabInfo->user2id;
                }

                $tmpurtn  = ['tbid'=>$tableid,'isstart'=>1,'dt'=>'startus'];//向下棋者多发一条消息，同步下界面的功能
                $rtn    = $this->sendoneuser(5,$tmpurtn,$rtn,$tmpuserid);

                //注册倒记时判断
                $this->timeoutfun($tableid,$lasttime,$ser,121);

            }else{
                if($tabInfo->user1id >0 && $tabInfo->u1state == 1){
                    $tbrtn['us1']       = ['upbj'=>0,'tip'=>'已准备'];
                }
                if($tabInfo->user2id >0 && $tabInfo->u2state == 1){
                    $tbrtn['us2']       = ['upbj'=>0,'tip'=>'已准备'];
                }
            }

            $rtn    = $this->sendtablelistsms(4,$tbrtn,$rtn,$tableid);
            return $rtn;
        }else if($_data->type == 5){//下棋的时候 如果没有下棋的时候，正常应该是不会到这里

            $rtn['mt']  = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'wzlinkuinfo');//获取用户的缓存

            $wzckey     = $_data->data;
            $wzchat     = get_key_cache($wzckey);

            if($wzchat == false){

                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $ysstr      = $wzchat['ysstr'];
            $dx         = $wzchat['dx'];
            $dy         = $wzchat['dy'];
            $tableid    = $wzchat['tbid'];
            $iswin      = $wzchat['iswin'];
            $lasttime   = $wzchat['lasttime'];

            $tabInfo    = D('wztable')->where('id='.$tableid)->find();
            $tabstaArr  = [
                0 => '空位置',
                1 => '对战中',
                2 => '等待中'
            ];

            $tbrtn  = ['tbid'=>$tableid,'ysstr'=>$ysstr,'dx'=>$dx,'dy'=>$dy,'dt'=>'playchess','iswin'=>$iswin,'actuname'=>$uf_info['uname']];
            $tbrtn['tbstate']   = $tabstaArr[$tabInfo->state];
            $tbrtn['us1']       = ['upbj'=>0,'tip'=>''];
            $tbrtn['us2']       = ['upbj'=>0,'tip'=>''];

            $tmpuserid          = $tabInfo->user1id;
            if($tabInfo->nowposid == 1){
                $tbrtn['us1']       = ['upbj'=>1,'tip'=>'*'];
            }else{
                $tbrtn['us2']       = ['upbj'=>1,'tip'=>'*'];
                $tmpuserid          = $tabInfo->user2id;
            }

            if($iswin == 0){
                $tmpurtn  = ['tbid'=>$tableid,'isstart'=>1,'dt'=>'playchess'];//向下棋者多发一条消息，同步下界面的功能
                $rtn    = $this->sendoneuser(5,$tmpurtn,$rtn,$tmpuserid);

                //在这里注册超时事件
                $this->timeoutfun($tableid,$lasttime,$ser,121);
            }

            $rtn    = $this->sendtablelistsms(6,$tbrtn,$rtn,$tableid);
            return $rtn;

        }else{


        }
        
        return $rtn;
        
    }

    //超时判断
    public function timeoutfun($tableid,$lasttime,$ser,$time = 15)
    {
        $wattime    = $time*1000;//间隔时间
        //时间函数测试
        Swoole\Timer::after($wattime, function()use($tableid,$lasttime,$ser){

            $tabInfo    = D('wztable')->where('id='.$tableid)->find();
            $timsInfo   = D('wztimes')->where('id='.$tabInfo->times)->find();
            $nowtime    = time();

            if($tabInfo->state == 1 && $timsInfo->uptime == $lasttime)
            {//棋局正在进行中时，如果在过了$wattime长时间后，棋局还没有更新，判断超时

                $upTimeArr['winposid']  = $tabInfo->nowposid == 1?2:1;//超时后系统判断对手赢得比赛
                $upTimeArr['state']     = 2;
                $upTimeArr['uptime']    = $nowtime;
                D('wztimes')->where('id='.$timsInfo->id)->save($upTimeArr);

                if($tabInfo->nowposid == 1)
                {//取对手信息
                    $winUserInfo    = get_uinfo_cache($tabInfo->user2id);
                }else{
                    $winUserInfo    = get_uinfo_cache($tabInfo->user1id);
                }

                //还原棋局状态
                $upTabArr['u1state']    = 0;
                $upTabArr['u2state']    = 0;
                $upTabArr['state']      = 2;
                //$upTabArr['nowposid']   = $tabInfo->nowposid == 1?2:1;//超时后系统判断对手赢得比赛 这里应该不用更新了
                D('wztable')->where('id='.$tabInfo->id)->save($upTabArr);//更新位置下棋者为对手位置

                $rtn    = [];
                $msgDt  = ['tbid'=>$tableid,'dt'=>'timeout','iswin'=>1,'actuname'=>$winUserInfo['uname']];
                //向具体桌子上的所有人推送消息
                $ulistRes   = D('wzulist')->where('tableid = '.$tableid.' and state = 2')->select();
                if($ulistRes == false)
                {

                }else{
                    foreach($ulistRes as $k=>$v){
                        $to_uf_info     = get_uf_hscache($v->userid,'wzlinkuinfo');//获取用户的缓存
                        if($to_uf_info != false){
                            $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                            if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存

                            }else{
                                $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,7,0,$msgDt);
                            }
                        }
                    }
                }

                if(!empty($rtn['send_to_msg'])){
                    foreach($rtn['send_to_msg'] as $k=>$v)
                    {
                        $ser->push($v['to_fd'], json_encode(['msg' => $v['msg']]));
                    }
                }
            }
        });
    }

    public function sendoneuser($msgtp,$msgDt,$rtn,$userid)
    {
        $to_uf_info     = get_uf_hscache($userid,'wzlinkuinfo');//获取用户的缓存
        if($to_uf_info != false){
            $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
            if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存

            }else{
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,$msgtp,0,$msgDt);
            }
        }
        return $rtn;
    }

    //给大厅里的所有用户发送消息
    public function sendtablelistsms($msgtp,$msgDt,$rtn,$tabid=0)
    {
//        $tblistuser = get_key_cache('wztblistuser');
//        if($tblistuser == false){
//
//        }else{
//            foreach($tblistuser as $k=>$v)
//            {
//                $to_uf_info     = get_uf_hscache($v,'wzlinkuinfo');//获取用户的缓存
//                if($to_uf_info != false){
//                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
//                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存
//
//                    }else{
//                        $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,$msgtp,0,$tbrtn);
//                    }
//                }
//            }
//        }
        //给某个桌子的人发消息
//        $rd         = new redis_cache();
//        $tbkey      = 'wztb_'.$tableid;
//        $tbinfo     = $rd->hget('wztableinfo',$tbkey);
//
//        $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$poshtml,'dt'=>'pos'];
//        if(count($tbinfo['alluid'])>0){
//            foreach($tbinfo['alluid'] as $k=>$v){
//                $to_uf_info     = get_uf_hscache($v,'wzlinkuinfo');//获取用户的缓存
//                if($to_uf_info != false){
//                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
//                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存
//
//                    }else{
//                        $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,2,0,$tbrtn);
//                    }
//                }
//            }
//        }

        if($tabid == 0){//向大厅里面的所有人发消息
            $ulistRes   = D('wzulist')->where('state = 1')->select();
        }else{//向具体桌子上的所有人推送消息
            $ulistRes   = D('wzulist')->where('tableid = '.$tabid.' and state = 2')->select();
        }

        if($ulistRes == false)
        {

        }else{
            foreach($ulistRes as $k=>$v){
                $to_uf_info     = get_uf_hscache($v->userid,'wzlinkuinfo');//获取用户的缓存
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存

                    }else{
                        $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,$msgtp,0,$msgDt);
                    }
                }
            }
        }

        return $rtn;
    }

    public function closefd($fd)
    {
        $rtn['mt']   = 2;
        return $rtn;//连接中断后，不修改数据，统一用超时来处理

        $fd_info    = get_fd_hscache($fd);//获取指定连接ＩＤ的缓
        $userid     = $fd_info['uid'];

        $res    = D('wzulist')->where('userid = '.$userid)->find();
        $newtime = time();
        if($res) {

            $posid              = $res->tableposid;
            $tableid            = $res->tableid;
            $ulistArr['lastuptime'] = $newtime;
            if($res->tableid == 0){
                $ulistArr['state']  = 0;
            }else{

                $ulistArr['state']  = 0;
                $ulistArr['tableid'] = 0;

                if($res->tableposid >0){//需要更新对应的表格

                    $ulistArr['tableposid'] = 0;
                    $tabRes    = D('wztable')->where('id='.$res->tableid)->find();
                    $upTabArr['state']  = 2;

                    if($posid == 1) {
                        $upTabArr['user1id']    = 0;
                        if($tabRes->user2id == 0){
                            $upTabArr['state']  = 0;
                        }
                    }else{
                        $upTabArr['user2id']    = 0;
                        if($tabRes->user1id == 0){
                            $upTabArr['state']  = 0;
                        }
                    }

                    D('wztable')->where('id='.$tabRes->id)->save($upTabArr);
                    $tblemphtml     = '<div class="wzemp">空</div>';
                    $tbrtn          = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$tblemphtml,'dt'=>'tbl','tp'=>'cancal'];
                    $rtn            = $this->sendtablelistsms(2,$tbrtn,$rtn);
                }
            }
            D('wzulist')->where('id=' . $res->id)->save($ulistArr);

            if($tableid >0 && $res->tableposid >0){
                $posemphtml     = '<div class="wzemp" onclick="xt_wuzi.sitdown(this)" poid="'.$posid.'">坐下</div>';
                $tbrtn          = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$posemphtml,'dt'=>'pos','tp'=>'cancal'];
                $rtn            = $this->sendtablelistsms(2,$tbrtn,$rtn,$tableid);
            }
        }

        return $rtn;
    }

    //生成返回消息的格式
    public function rtnsendmsg($fd_info,$msg_type,$is_self,$_msg)
    {
        $send_msg    = array(
            'to_fd'     => $fd_info['fid'],
            'to_uid'    => $fd_info['uid'],
            'msg'       => array(
                '_type'     => $msg_type, //发送的消息类型，0表示异常
                '_is_self'  => $is_self,//是否自己发送,1表示发送给自己,2表示发送给对方
                '_data'     => $_msg,//显示给前端需要展示的数据
            )
        );

        return $send_msg;
    }

    
    
}
