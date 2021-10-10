<?php

if(!defined('CHT')){die();}
class Doudzclass extends ActionClass{

    protected function _load()
    {

    }

    public function message($frame,$data,$ser)
    {
        $_data = json_decode($data);
        $rtn = array(
            'mt' => 1, //返回的消息类型,0是错误消息,1是注册消息，不需要发送数据,2发送的数据，需要返回msg
            'cmt' => 1 //当消息类型是２时，值为1代表对方不在线，只给我自己返回消息即可，为2时，表示对方也在线，并正在和我聊天中，给彼此都发送消息，为3时，表示他在线，但没和我聊天，发送消息提示即可
        );
        if ($_data->type == 1) {//第一次连接注册连接信息

            $rd = new redis_cache();
            $tm_fd_k = 'fd_' . $frame->fd;//每次的新边接都会是一个新的fd,所以同一个用户有两个连接不冲突,可以在聊天的同时保持五子棋的正常
            $tm_fd_arr = array(
                'fid' => $frame->fd,
                'uid' => $_data->data->uid,
                'avt' => $_data->data->avt,
                'uname' => $_data->data->uname,
                'type' => 'doudz',
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
            $old_uf = $rd->hget('doudzlinkuinfo', $tm_uf_k);
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
            $rd->hset('doudzlinkuinfo', $tm_uf_k, $tm_uf_arr);
        }elseif($_data->type == 2){
            //有人坐在桌子上时，向所有广场上有效的人发送通知消息
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

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
            $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,2,1,$tbrtn);//强制给自己发送一条大厅的消息

            $rtn    = $this->sendtablelistsms(2,$tbrtn,$rtn,0,$fd_info['uid']);//给大厅里的其它人发送消息

            $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$poshtml,'dt'=>'det'];
            $rtn    = $this->sendtablelistsms(2,$tbrtn,$rtn,$tableid,$fd_info['uid'],0);//给桌子上的其它人发送消息

            return $rtn;
        }elseif($_data->type == 3){
            //点击准备后，向当前桌子上的人同步消息
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

            $sitkey     = $_data->data;
            $sitinfo    = get_key_cache($sitkey);

            if($sitinfo == false){
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $tableid = $sitinfo['tbid'];
            $posid = $sitinfo['posid'];
            $gamestart = $sitinfo['gamestart'];
            $btnhtml = $sitinfo['btnhtml'];
            $rdhtml = $sitinfo['rdhtml'];

            if($gamestart == 0){
                $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'btnhtml'=>$btnhtml,'rdhtml'=>$rdhtml,'dt'=>'startgame'];
                $rtn    = $this->sendtablelistsms(3,$tbrtn,$rtn,$tableid,$fd_info['uid']);
            }else{
                //这里处理同步各自的牌 向桌子上的用户发牌 抢地主开始
                $btntp = 3;
                $wattime = 30;
                $msgtp = 4;
                $acttit = '';
                $rtn = $this->sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn);

                //数据发送前，应该在这里定义超时函数，抢地主的倒计时

            }
            return $rtn;

        }elseif($_data->type == 4){//这个类型已经被上面用了
            //都准备好后，向桌子上的用户发牌 抢地主开始

        }elseif($_data->type == 5){
            //同步抢地主状态，加倍
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

            $sitkey     = $_data->data;
            $sitinfo    = get_key_cache($sitkey);

            if($sitinfo == false){
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $tableid = $sitinfo['tbid'];
            $hasrob = $sitinfo['hasrob'];
            $robval = $sitinfo['robval'];

            $btntp = 3;
            $wattime = 30;
            $msgtp = 5;
            if($hasrob == 1){//确认了地主时
                $btntp = 4;
                $msgtp = 6;
            }

            if($robval == 1){
                $acttit = '抢地主';
            }else{
                $acttit = '不抢';
            }

            $rtn = $this->sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn);
            //数据发送前，应该在这里定义超时函数，抢地主的倒计时
            return $rtn;

        }elseif($_data->type == 7){
            //同步加倍状态
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

            $sitkey     = $_data->data;
            $sitinfo    = get_key_cache($sitkey);

            if($sitinfo == false){
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $tableid = $sitinfo['tbid'];
            $isEndDou = $sitinfo['isenddou'];
            $posid = $sitinfo['posid'];
            $douval = $sitinfo['douval'];
            $multiple_num = $sitinfo['multiple_num'];

            if($isEndDou == 0){
                //只同步当前人的棋局
                $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'mulval'=>$multiple_num,'douval'=>$douval,'dt'=>'adddouble'];
                $rtn    = $this->sendtablelistsms(7,$tbrtn,$rtn,$tableid,$fd_info['uid']);
            }else{
                //格式化所有人的棋局
                $btntp = 5;
                $wattime = 30;
                $msgtp = 8;
                $rtn = $this->sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn);
            }


        }elseif($_data->type == 8){
            //开始打牌，同步出牌
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

            $sitkey     = $_data->data;
            $sitinfo    = get_key_cache($sitkey);

            if($sitinfo == false){
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,0,1,'数据异常，请刷新页面后重新操作');
                return $rtn;
            }

            $tableid = $sitinfo['tbid'];
            $posid = $sitinfo['posid'];
            $pkval = $sitinfo['pkval'];
            $iswin = $sitinfo['iswin'];
            $times = $sitinfo['times'];

            $btntp = 5;
            $wattime = 30;
            $msgtp = 9;
            if($iswin == 1){//同步打牌结果，显示结算，进入下一轮流程
                $msgtp = 10;
                $btntp = 1;
                $wattime = 0;
                $rtn = $this->sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn,$times);
            }else{
                $rtn = $this->sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn);
            }

        }elseif($_data->type == 11){

            //有人坐在桌子上时，向所有广场上有效的人发送通知消息
            $rtn['mt']   = 2;
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid'],'doudzlinkuinfo');//获取用户的缓存

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

            $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$poshtml,'dt'=>'leavedet'];
            $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,11,1,$tbrtn);//强制给自己发送一条离开详情的消息
            $rtn    = $this->sendtablelistsms(11,$tbrtn,$rtn,$tableid,$fd_info['uid']);//给桌子上的其它人发送消息

            $tbrtn  = ['tbid'=>$tableid,'posid'=>$posid,'info'=>$tblthtml,'dt'=>'comtbl'];
            $rtn    = $this->sendtablelistsms(11,$tbrtn,$rtn,0,$fd_info['uid'],0);//给大厅里的其它人发送消息

        }

        return $rtn;
    }

    //给正在一个桌子上的用户同步彼此之间的数据和状态
    public function sendtbulist($tableid,$btntp,$wattime,$msgtp,$rtn,$times=0)
    {
        $tabinfo = D('ddztable')->where('id ='.$tableid)->find();
        if($msgtp == 10){
            $ddzulist = D('ddztabulist')->where('table_id='.$tableid.' and times = '.$times)->order('id asc')->select();
            $timeinfo = D('ddztimes')->where('id ='.$times)->find();
        }else{
            $ddzulist = D('ddztabulist')->where('table_id='.$tableid.' and state = 1')->order('id asc')->select();
            $timeinfo = D('ddztimes')->where('id ='.$tabinfo->times)->find();
        }

        $doudzser   = new DoudzService();
        $tabUArr    = [];
        if($msgtp == 9){
            $lastPkArr = unserialize($timeinfo->lastsendpk);
            $lastPkTp = $doudzser->rtnpktp($lastPkArr);
            $lastPkTip = '';
            $pokhtml = '';
            foreach($lastPkArr as $k=>$v){
                $pokhtml .= '<dd><img  src="/images/pk/'.$v.'.jpg" width="80"></dd>';
                //$pokhtml .= '<dd>'.$v.'</dd>';
            }
            if($timeinfo->nowuser_id != $timeinfo->lastsduser_id){//说明到了下一轮 如果等于自己时，就不显示出的牌，不等于自己时显示最后一手牌
                $lastPkU[$timeinfo->lastsduser_id] = $pokhtml;
            }

            if($timeinfo->lastactuser_id != $timeinfo->lastsduser_id){//最后一次出牌人没有出牌
                $lastTipU[$timeinfo->lastactuser_id] = '不要';
            }else{//提示只在第一次出牌时提醒
                if(in_array($lastPkTp['type'],[4,6,8])){
                    $lastPkTip = $lastPkTp['intro'];
                }
            }
        }

        if($msgtp == 10){
            $lastPkArr = unserialize($timeinfo->lastsendpk);
            $lastPkTp = $doudzser->rtnpktp($lastPkArr);
            $lastPkTip = '';
            $pokhtml = '';
            foreach($lastPkArr as $k=>$v){
                $pokhtml .= '<dd><img  src="/images/pk/'.$v.'.jpg" width="80"></dd>';
                //$pokhtml .= '<dd>'.$v.'</dd>';
            }

            //游戏结束时，给所有人同步最后一手牌 包括自己
            $lastPkU[$timeinfo->lastsduser_id] = $pokhtml;
            if(in_array($lastPkTp['type'],[4,6,8])){
                $lastPkTip = $lastPkTp['intro'];
            }

        }

        if($msgtp == 6){
            $dzpoker = unserialize($timeinfo->dzpoker);
            $dzpokerhtml = '';
            foreach($dzpoker as $k=>$v){
                $dzpokerhtml .= '<dd><img src="/images/pk/'.$v.'.jpg" width="80"></dd>';
                //$dzpokerhtml .= '<dd>'.$v.'</dd>';
            }
        }

        $multiple_num = $timeinfo->multiple_num;

        $lldUser_id = 0;//地主ID
        $farmeruser_id = [];
        $iswinty = 1;//默认地主赢得了比赛
        //初始化位置信息
        foreach($ddzulist as $k=>$v)
        {
            $isnowact = 0;
            $acttip = '';
            if(empty($v->nowpk))
            {
                $nowuserpk = [];
                $pknum     = 0;
            }else{
                $nowuserpk = unserialize($v->nowpk);
                $pknum     = count($nowuserpk);
                $nowuserpk = $doudzser->sortpk($nowuserpk);
            }

            if($timeinfo->nowuser_id == $v->user_id){
                $isnowact = 1;
            }

            if($msgtp == 10){
                if($v->is_dz == 1){
                    $lldUser_id = $v->user_id;
                }else{
                    $farmeruser_id[] = $v->user_id;
                    if($timeinfo->winuser_id == $v->user_id){
                        $iswinty = 2;
                    }
                }

            }else if($msgtp == 9){
                $acttip = isset($lastTipU[$v->user_id])?$lastTipU[$v->user_id]:'';
            }else if($msgtp == 8){

            }else{
                if($v->is_roblld == 1){
                    $acttip = '抢地主';
                }elseif($v->is_roblld == 2){
                    $acttip = '不抢';
                }
            }

            $tabUArr[$v->tabposnum] = [
                'isnowact' => $isnowact,
                'pkarr' => $nowuserpk,
                'pknum' => $pknum,
                'tabposnum' => $v->tabposnum,
                'is_dz' => $v->is_dz,
                'acttip' => $acttip,
                'sendpk' => isset($lastPkU[$v->user_id])?$lastPkU[$v->user_id]:'',
                'distp' => isset($lastPkU[$v->user_id])?'pk':(!empty($acttip)?'tip':'tim'),
            ];
        }

        if($msgtp == 10){
            if($iswinty == 1){
                $wininfo = '地主['.$lldUser_id.']赢得了此场比赛';
            }else{
                $wininfo = '农民['.join(',',$farmeruser_id).']赢得了此场比赛';
            }
        }

        //生成每个人的牌面数据
        foreach($ddzulist as $k=>$v){
            $to_uf_info     = get_uf_hscache($v->user_id,'doudzlinkuinfo');//获取用户的缓存
            if($to_uf_info != false){
                $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存

                }else{
                    //这里生成下针对每个人的数据
                    $rtndata = $this->rtnuserpokerdt($v->tabposnum,$tabUArr,$btntp,$wattime);
                    if($msgtp == 10){
                        $rtndata['wininfo'] = $wininfo;
                        $rtndata['multiple_num'] = $multiple_num;
                    }else if($msgtp == 9){
                        $rtndata['pktip'] = $lastPkTip;
                        $rtndata['multiple_num'] = $multiple_num;
                    }else if($msgtp == 6){
                        $rtndata['dzpkht'] = $dzpokerhtml;
                        $rtndata['multiple_num'] = $multiple_num;
                    }else if(in_array($msgtp,[5,7,8])){
                        $rtndata['multiple_num'] = $multiple_num;
                    }

                    $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,$msgtp,1,$rtndata);
                }
            }
        }

        return $rtn;
    }

    //合成通用前端数据
    public function rtnuserpokerdt($tabposnum,$tabUArr,$btntp,$wattime=30)
    {
        $uinfo_arr  = [];
        if($tabposnum == 1){
            $uinfo_arr['nowuinfo'] = $tabUArr[1];
            $uinfo_arr['nextuinfo'] = $tabUArr[2];
            $uinfo_arr['upuinfo'] = $tabUArr[3];
        }elseif($tabposnum == 2){
            $uinfo_arr['nowuinfo'] = $tabUArr[2];
            $uinfo_arr['nextuinfo'] = $tabUArr[3];
            $uinfo_arr['upuinfo'] = $tabUArr[1];
        }else{
            $uinfo_arr['nowuinfo'] = $tabUArr[3];
            $uinfo_arr['nextuinfo'] = $tabUArr[1];
            $uinfo_arr['upuinfo'] = $tabUArr[2];
        }

        $pokhtml = '';
        foreach($uinfo_arr['nowuinfo']['pkarr'] as $k=>$v){
            $pokhtml .= '<dd class="footer ddz_dom_pkval_'.$v.'" pkval="'.$v.'" onclick="xt_ddz.clickpk(this)"><img class="footer" src="/images/pk/'.$v.'.jpg" width="80"></dd>';
            //$pokhtml .= '<dd class="footer ddz_dom_pkval_'.$v.'" pkval="'.$v.'" onclick="xt_ddz.clickpk(this)">'.$v.'</dd>';
        }

        //返回自己的牌
        $rtn['my'] = [
            'pokhtml' => $pokhtml,//poker html
            'btntp' => $uinfo_arr['nowuinfo']['isnowact'] == 1?$btntp:0,//btn html
            'countdown' => $btntp==4?$wattime:($uinfo_arr['nowuinfo']['isnowact'] == 1?$wattime:0),//倒计时
            'acttip' => $uinfo_arr['nowuinfo']['acttip'], // 提示
            'islandlord' => $uinfo_arr['nowuinfo']['is_dz']==1?1:0, // 是否地主
            'sendpk' => $uinfo_arr['nowuinfo']['sendpk'],
            'distp' => $uinfo_arr['nowuinfo']['distp'],

        ];

        //返回上家和下家的信息
        $rtn['up'] = [
            'poknum' => $uinfo_arr['upuinfo']['pknum'],//poken num
            'countdown' => $btntp==4?$wattime:($uinfo_arr['upuinfo']['isnowact'] == 1?$wattime:0), //倒计时
            'posid' => $uinfo_arr['upuinfo']['tabposnum'],
            'acttip' => $uinfo_arr['upuinfo']['acttip'], // 提示
            'islandlord' => $uinfo_arr['upuinfo']['is_dz']==1?1:0, // 是否地主
            'sendpk' => $uinfo_arr['upuinfo']['sendpk'],
            'distp' => $uinfo_arr['upuinfo']['distp'],
        ];

        $rtn['next'] = [
            'poknum' => $uinfo_arr['nextuinfo']['pknum'],//poken num
            'countdown' => $btntp==4?$wattime:($uinfo_arr['nextuinfo']['isnowact'] == 1?$wattime:0), //倒计时
            'posid' => $uinfo_arr['nextuinfo']['tabposnum'],
            'acttip' => $uinfo_arr['nextuinfo']['acttip'], // 提示
            'islandlord' => $uinfo_arr['nextuinfo']['is_dz']==1?1:0, // 是否地主
            'sendpk' => $uinfo_arr['nextuinfo']['sendpk'],
            'distp' => $uinfo_arr['nextuinfo']['distp'],
        ];

        return $rtn;

    }

    //给大厅里的所有用户发送消息
    public function sendtablelistsms($msgtp,$msgDt,$rtn,$tabid=0,$user_id,$to_self = 1)
    {
        if($tabid == 0){//向大厅里面的所有人发消息
            $ulistRes   = D('ddzulist')->where('state = 1')->select();
        }else{//向具体桌子上的所有人推送消息
            $ulistRes   = D('ddzulist')->where('tableid = '.$tabid.' and state = 2')->select();
        }

        if($ulistRes == false)
        {

        }else{
            foreach($ulistRes as $k=>$v){
                $to_uf_info     = get_uf_hscache($v->user_id,'doudzlinkuinfo');//获取用户的缓存
                if($to_uf_info != false){
                    $isSelf = 0;
                    if($user_id == $v->user_id){
                        $isSelf = 1;
                    }
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){//这里后期可以处理下，对连接已经断开的用户，修正下对应的缓存

                    }else{
                        if($to_self != 1 && $isSelf == 1){

                        }else{
                            $rtn['send_to_msg'][]   = $this->rtnsendmsg($to_fd_info,$msgtp,$isSelf,$msgDt);
                        }
                    }
                }
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