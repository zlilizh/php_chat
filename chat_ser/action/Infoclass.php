<?php

if(!defined('CHT')){die();}	
class infoclass extends ActionClass{
		
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
            $tm_fd_k = 'fd_' . $frame->fd;
            $tm_fd_arr = array(
                'fid' => $frame->fd,
                'uid' => $_data->data->uid,
                'avt' => $_data->data->avt,
                'uname' => $_data->data->uname,
                'type' => 'chat',
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

            $tm_uf_k = 'uf_' . $_data->data->uid;
            $old_uf = $rd->hget('ufinfo', $tm_uf_k);
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
            $rd->hset('ufinfo', $tm_uf_k, $tm_uf_arr);
        }else if($_data->type == 7){//撤销消息的时候

            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $wdkey      = $_data->data;
            $wdKInfo    = get_key_cache($wdkey);
            $megId      = $wdKInfo['_mesgid'];
            $rtn['mt']  = 2;
            $mesRes     = D('message') ->where('id='.$megId)->find();
            if($mesRes->msgstate != 2 && $mesRes->msg_tp > 2)
            {
                $rtn['mt']     = 3;
            }

            $memIf  = get_uinfo_cache($mesRes->form_uid);
            $sfmsg  = '<div class="chat_tip">你撤回了一条消息</div>';
            $tomsg  = '<div class="chat_tip">'.$memIf['name'].'撤回了一条消息</div>';

            $msg    = ['msgid'=>$megId,'sfmsg'=>$sfmsg,'tomsg'=>$tomsg,'chating'=>1];//先给自己发送一条消息
            $sim_msg = '撤回消息';
            $rtn['send_to_msg'][] = $this->rtnsendmsg($fd_info,7,$fd_info['uid'],1,$mesRes->to_uid,0,$sim_msg,$msg);
            $nowtime    = time();
            if($mesRes->msg_tp == 1)
            {
                $to_uf_info     = get_uf_hscache($mesRes->to_uid);//获取用户的缓存
                $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                }

                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                }else{
                    if(isset($to_fd_info['to_uid']) && $to_fd_info['to_uid'] == $fd_info['uid'])
                    {
                        $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,7,$fd_info['uid'],2,$mesRes->to_uid,0,$sim_msg,$msg);
                    }else{
                        $msg['chating']     = 2;
                        $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,7,$fd_info['uid'],2,$mesRes->to_uid,0,$sim_msg,$msg);
                    }
                }

            }else{

                $group_info = get_group_cache($mesRes->to_group_id);
                foreach($group_info['ulist'] as $k=>$v)
                {
                    if($v['uid'] == $fd_info['uid'])
                    {
                        continue;
                    }
                    $to_uf_info     = get_uf_hscache($v['uid']);//获取用户的缓存
                    $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                    if($to_uf_info != false){
                        $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                    }
                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) )
                    {

                    }else{
                        if(isset($to_fd_info['to_gid']) && $to_fd_info['to_gid'] == $mesRes->to_group_id)
                        {
                            $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,7,$fd_info['uid'],2,'',$mesRes->to_group_id,$sim_msg,$msg);
                        }else{
                            $msg['chating']     = 2;
                            $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,7,$fd_info['uid'],2,'',$mesRes->to_group_id,$sim_msg,$msg);
                        }
                    }
                }
            }

            $dFriend            = D('friend');
            $td_arr['uptime']   = $nowtime;
            $td_arr['sim_msg']  = $sim_msg;
            $dFriend->where('cmtn_id='.$mesRes->cmtn_id)->save($td_arr);

        }else if($_data->type == 5){//添加好友结果通知

            $t_time     = time();
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $reqKey     = $_data->data;
            $reqInfo    = get_key_cache($reqKey);
            $rtn['mt']     = 3;//默认不回复消息
            if($fd_info['uid'] != $reqInfo['to_uid'] && $reqInfo['state'] !=0){

                $msg = '好友请求异常，请刷新页面后重新操作试试';
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][] = $this->rtnsendmsg($fd_info,3,$fd_info['uid'],1,'',0,'',$msg);
                return $rtn;
            }

            if($reqInfo['state'] == 3){//忽略状态，啥也不用处理

            }else if($reqInfo['state'] == 2){//拒绝时，给对方发送新消息通知即可
                $to_uf_info     = get_uf_hscache($reqInfo['form_uid']);//获取用户的缓存
                $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                }
                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                }else{

                    $memres    = D('member') ->where('id='.$to_fd_info['uid'])->find();
                    if($memres->notice_num > 0){
                        $rtn['mt']     = 2;
                        $rtnmsg = ['_reqtp'=>2,'_tznum'=>$memres->notice_num,'_html'=>''];
                        $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,5,$fd_info['uid'],2,'',0,'',$rtnmsg);
                    }
                }

                return $rtn;
            }else if($reqInfo['state'] == 1){//同意好友请求时

                $to_uf_info     = get_uf_hscache($reqInfo['form_uid']);//获取用户的缓存
                $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                }

                $rtn['mt']     = 2;
                $rtnmsg = ['_reqtp'=>1,'_tznum'=>0,'_chathtml'=>$reqInfo['rtnhtml'][$reqInfo['form_uid']]['chathtml'],'_frihtml'=>$reqInfo['rtnhtml'][$reqInfo['form_uid']]['frihtml']];
                $rtn['send_to_msg'][] = $this->rtnsendmsg($fd_info,5,$fd_info['uid'],1,'',0,'',$rtnmsg);

                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                }else{

                    $memres    = D('member') ->where('id='.$to_fd_info['uid'])->find();
                    if($memres->notice_num > 0){

                        $rtnmsg = ['_reqtp'=>1,'_tznum'=>$memres->notice_num,'_chathtml'=>$reqInfo['rtnhtml'][$reqInfo['to_uid']]['chathtml'],'_frihtml'=>$reqInfo['rtnhtml'][$reqInfo['to_uid']]['frihtml']];
                        $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,5,$fd_info['uid'],2,'',0,'',$rtnmsg);
                    }
                }

                return $rtn;
            }else if($reqInfo['state'] == 0){//发送好友请求时

                $to_uf_info     = get_uf_hscache($reqInfo['to_uid']);//获取用户的缓存
                $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                }
                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                }else{

                    $memres    = D('member') ->where('id='.$to_fd_info['uid'])->find();
                    if($memres->notice_num > 0){
                        $rtn['mt']     = 2;
                        $rtnmsg = ['_reqtp'=>0,'_tznum'=>$memres->notice_num,'_html'=>''];
                        $rtn['send_to_msg'][] = $this->rtnsendmsg($to_fd_info,5,$fd_info['uid'],2,'',0,'',$rtnmsg);
                    }
                }

                return $rtn;
            }

        }else if($_data->type ==4){//创建群组只转发消息

            $t_time     = time();
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            $uf_info    = get_uf_hscache($fd_info['uid']);//获取用户的缓存
            $groupkey   = $_data->data->msgdt;
            $groupArr   = get_key_cache($groupkey);

            $groupid    = $groupArr['group_id'];
            $sendmsg    = $groupArr['group_html'];
            $tohtml     = $groupArr['tohtml'];
            $formhtml   = $groupArr['formhtml'];
            $frihtml    = $groupArr['frihtml'];
            $groupName  = $groupArr['group_name'];
            $isupgroup  = $groupArr['isgact'];//1创建，2新增用户，3删除用户
            $delUid     = $groupArr['deluid'];

            $group_info = get_group_cache($groupid);

            if($fd_info['uid'] != $group_info['adduid'] && $isupgroup==0){
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,3,$fd_info['uid'],1,0,0,'','数据异常，请刷新页面后重新操作');

                return $rtn;
            }

            $rtn['mt']     = 2;
            foreach($group_info['ulist'] as $k=>$v)
            {
                $msg_is_self    = $v['uid'] == $fd_info['uid']?1:2;
                $to_uf_info     = get_uf_hscache($v['uid']);//获取用户的缓存
                $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                if($to_uf_info != false){
                    $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                }
                $tmpsendmsg     = '';

                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                }else{

                    if($msg_is_self == 1){
                        $tmpsendmsg    = $formhtml.$sendmsg;
                    }else{
                        if($isupgroup == 2 || $isupgroup == 3){

                            if(isset($to_uf_info['to_gid']) && $to_uf_info['to_gid'] == $groupid)
                            {
                                $tmpsendmsg    = $formhtml.$sendmsg;
                            }else{
                                $tmpsendmsg    = $tohtml.$sendmsg;
                            }
                        }else{
                            $tmpsendmsg    = $tohtml.$sendmsg;
                        }
                    }

                    $grinfo = ['group_id'=>$groupid,'gtype'=>$isupgroup,'group_name'=>$groupName,'chtml'=>$tmpsendmsg,'fhtml'=>$frihtml,'isdel'=>0];
                    $rtn['send_to_msg'][]     = $this->rtnsendmsg($to_fd_info,4,$fd_info['uid'],$msg_is_self,$v['uid'],$groupid,'',$grinfo);
                }
            }

            if($isupgroup == 3 && !empty($delUid)){

                foreach($delUid as $kk => $vv){
                    $to_uf_info     = get_uf_hscache($vv);//获取用户的缓存
                    $to_fd_info     = false;//默认对方的连接ＩＤ不存在
                    if($to_uf_info != false){
                        $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
                    }
                    $tmpsendmsg     = '';

                    if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) ){

                    }else{

                        $grinfo = ['group_id'=>$groupid,'gtype'=>$isupgroup,'group_name'=>$groupName,'chtml'=>'','fhtml'=>'','isdel'=>1];
                        $rtn['send_to_msg'][]     = $this->rtnsendmsg($to_fd_info,4,$fd_info['uid'],2,$vv,$groupid,'',$grinfo);
                    }
                }

            }

        }else{

            $t_time     = time();
            $fd_info    = get_fd_hscache($frame->fd);//获取指定连接ＩＤ的缓
            //在这里先看是发朋友还是群组
            
            $rtn['mt']          = 2;
            $rtn['form_uinfo']  = array(//发送者信息
                'uid'       => $fd_info['uid'],
                'fd'        => $frame->fd
            );
           
            $uf_info    = get_uf_hscache($fd_info['uid']);//获取用户的缓存
            //在这里应该做一个用户的发送消息检查，发送的消息过快在这里拒绝
            if((!isset($uf_info['to_uid']) || $uf_info['to_uid'] == 0) && (!isset($uf_info['to_gid']) || $uf_info['to_gid'] == 0))
            {//没有指定发送方ID的消息不能发送
                $rtn['mt']     = 0;
                $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,3,$fd_info['uid'],1,0,0,'','未指定发送方，消息发送失败');

                return $rtn;
            }

            //在这里判断发送的消息是否合法，如果是发朋友，检查下有没这个好友关系，如果是发送，看下是否他在群里面
            if($uf_info['to_uid'] != 0)
            {//发给好友的
                
                $dFriend    = D('friend');
                $fri_res    = $dFriend ->where('my_uid='.$fd_info['uid'].' and fri_uid='.$uf_info['to_uid'])->find();

                if(!$fri_res)
                {//如果两个用户之间没有建立朋友关系，不能发送消息
                    
                    $rtn['mt']     = 0;
                    $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,3,$fd_info['uid'],1,0,0,'','非好友不能发送消息');

                    return $rtn;
                }else{
                    $cmtn_id    = $fri_res->cmtn_id;
                } 
            }else{//发送给群组的消息
                $dFriend    = D('friend');
                $fri_res    = $dFriend ->where('my_uid='.$fd_info['uid'].' and group_id='.$uf_info['to_gid'])->find();

                if(!$fri_res)
                {//如果两个用户之间没有建立朋友关系，不能发送消息
                    
                    $rtn['mt']     = 0;
                    $rtn['send_to_msg'][]   = $this->rtnsendmsg($fd_info,3,$fd_info['uid'],1,$uf_info['to_uid'],0,'','不能给未加入的群组发消息');

                    return $rtn;
                }else{
                    $cmtn_id    = $fri_res->cmtn_id;
                } 
            }

            $msgTpArr   = [2=>1,3=>2,6=>4];
            $msgType    = isset($msgTpArr[$_data->type])?$msgTpArr[$_data->type]:1;
            $msg_data   = $_data->data;

            //这里存数据，这里其实最好先用一个中间件去存数据或者先写入缓存中
            $_dt['form_uid']    = $fd_info['uid'];
            $_dt['to_uid']      = (isset($uf_info['to_uid']) && $uf_info['to_uid']>0)?$uf_info['to_uid']:0;
            $_dt['msg_tp']      = (isset($uf_info['to_gid']) && $uf_info['to_gid']>0)?2:1;//是不是发送给某人的，不是的话就是发给群组的
            $_dt['to_group_id'] = (isset($uf_info['to_gid']) && $uf_info['to_gid']>0)?$uf_info['to_gid']:0;;
            $_dt['cmtn_id']     = $cmtn_id;
            $_dt['send_time']   = $t_time;
            $_dt['con_tp']      = $msgType;
            $_dt['content']     = htmlspecialchars_decode($msg_data);
            if($msgType == 4){
                $megkey     = $_data->data;
                $megInfo    = get_key_cache($megkey);
                $msg_data   = $megInfo['_filename'];

                $_dt['content']     = $megInfo['_filename'];
                $_dt['exttp']       = $megInfo['_extname'];
                $_dt['realaddr']    = $megInfo['_realaddr'];
                $_dt['exptime']     = $megInfo['_exptime'];
                $_dt['filesize']    = $megInfo['_fsize'];
            }

            $D_msg          = D('message');
            $res            = $D_msg->add($_dt);
            if($res){}else{}

            $sim_msg    = '';
            if($msgType == 4){

                $msg_data   = $megInfo['_html'];
                $msg_data   = str_replace('#mesgid#',$res,$msg_data);
                $sim_msg    = '[附件]';

            }else if($msgType == 2)
            {
                $img_addr   = bigimg_to_sm($msg_data);
                $msg_data   = '<img src='.$img_addr.' big_addr='.$msg_data.' onclick="xt_pub.show_phote(this)">';
                $sim_msg    = '[图片]';
            }else{//文字
                $sim_msg    = xsubstr($msg_data,10);

                preg_match_all('/<img(.*)src=(.*)>/', $msg_data, $result);
                if(!empty($result[0])){
                    preg_match_all('/src=\"[A-Za-z0-9\/\.]+\"/', $msg_data, $result2);
                    $rplArr = [];
                    foreach($result2[0] as $k2=>$v2){
                        $tmpImg     = substr($v2,5,-1);
                        $isEmoji    = stripos($tmpImg,'emoji');
                        if($isEmoji === false){
                            $rplArr[]   = '<img src='.$tmpImg.' big_addr='.$tmpImg.' onclick="xt_pub.show_phote(this)" class="inputimg">';
                        }else{
                            $rplArr[]   = $result[0][$k2];
                        }
                    }
                    $sim_msg    = '[图片]';
                    $msg_data  = str_replace($result[0],$rplArr,$msg_data);
                }
            }

            $msgDtArr   = ['msgid'=>$res,'msgdt'=>$msg_data];

            if($uf_info['to_uid'] != 0)
            {//发给好友的
                $self_msg   = $this->build_msg($fd_info,$uf_info, $fd_info['uid'], $msgDtArr,$sim_msg,$cmtn_id,$t_time,0,$msgType);
                $rtn['send_to_msg'][]   = $self_msg;
                $msg_dt   = $this->build_msg($fd_info,$uf_info, $uf_info['to_uid'], $msgDtArr,$sim_msg,$cmtn_id,$t_time,0,$msgType);
                if($msg_dt != '')
                {
                    $rtn['send_to_msg'][]=$msg_dt;
                }
            }else{//发送群组
                $group_info = get_group_cache($uf_info['to_gid']);
//                echo '<pre>';
//                print_r($group_info);
                foreach($group_info['ulist'] as $k=>$v)
                {
                    $msg_dt   = $this->build_msg($fd_info,$uf_info, $v['uid'], $msgDtArr,$sim_msg,$cmtn_id,$t_time,$uf_info['to_gid'],$msgType);
                    if($msg_dt != '')
                    {
                        $rtn['send_to_msg'][]=$msg_dt;
                    }
                }
            }
        }
        
        return $rtn;
        
    }

    public function build_msg($fd_info,$uf_info,$to_uid,$msgArr,$sim_msg,$cmtn_id,$t_time,$to_group_id =0,$msg_tp=1)
    {

        $msg_data       = $msgArr['msgdt'];
        $msg_id         = $msgArr['msgid'];
        $msg_type       = 1;//黑认正常发送
        $msg_is_self    = 2;
        $nowDate        = date('Ymd');
        $stDate         = date('Ymd',$t_time);
        $formatTime     = $nowDate == $stDate?date('H:i',$t_time):date('Y-m-d H:i',$t_time);
        if($fd_info['uid'] == $to_uid)
        {
            $_msg   = '<div class="mc_blk dom-msg-id_'.$msg_id.'"><div class="mc_tm"><span>';
            $_msg   .= $formatTime;
            $_msg   .= '</span></div><div class="mcb_con"><div class="mcbc_avt_r">';
            $_msg   .= '<img onclick="xt_pub.open_meminfo(this)" memid="'.$fd_info['uid'].'" src="'.$fd_info['avt'].'"  width="35"/>';

            if($msg_tp == 4){
                $_msg   .= '</div><div class="mcbc_con_r mchat_con_bj">';
            }else{
                $_msg   .= '</div><div class="mcbc_con_r mchat_con_bj2">';
            }

            $_msg   .= $msg_data;
            $_msg   .= '</div><div class="mcbc_con_r_act" style="display: none;"><a href="javascript:void(0)" onclick="xt_pub.withdraw(this)" mesgid="'.$msg_id.'" class="withdrawpic" title="撤回"></a></div></div></div>';
            $msg_is_self    = 1;
            
            //组装msg
            $msg_to_uid = isset($uf_info['to_uid'])?$uf_info['to_uid']:0;
            $msg_to_gid = isset($uf_info['to_gid'])?$uf_info['to_gid']:0;
            $send_msg   = $this->rtnsendmsg($fd_info,$msg_type,$fd_info['uid'],$msg_is_self,$msg_to_uid,$msg_to_gid,$sim_msg,$_msg);
            
            if($to_group_id != 0)
            {
                $dFriend    = D('friend');
                $t_rs   = $dFriend ->where('my_uid='.$fd_info['uid'].' and group_id='.$to_group_id)->find();
                if($t_rs)
                {
                    $td_arr['uptime']   = $t_time;
                    $td_arr['sim_msg']  = $sim_msg;
                    $dFriend->where('id='.$t_rs->id)->save($td_arr);
                }
            }
            
            return $send_msg;
            
        }else{
            //合成消息 $_msg是发送给对方的消息,$_smsg是发送给自己的消息，把这个消息提出来，方便维护修改
            $_msg   = '<div class="mc_blk dom-msg-id_'.$msg_id.'"><div class="mc_tm"><span>';
            $_msg   .= $formatTime;
            $_msg   .= '</span></div><div class="mcb_con"><div class="mcbc_avt">';
            $_msg   .= '<img onclick="xt_pub.open_meminfo(this)" memid="'.$fd_info['uid'].'" src="'.$fd_info['avt'].'"  width="35"/>';
            $_msg   .= '</div><div class="mcbc_con mchat_con_bj">';
            $_msg   .= $msg_data;
            $_msg   .= '</div></div></div>';
            
            //我要发送消息给老王，老王有几种状态，每个状态的返回逻辑不一样，先获取老王的缓存，
            $to_uf_info     = get_uf_hscache($to_uid);//获取用户的缓存
            $to_fd_info     = false;//默认对方的连接ＩＤ不存在
            if($to_uf_info != false){
                $to_fd_info     = get_fd_hscache($to_uf_info['fid']);
            }
            
            $dFriend    = D('friend');
            //这里临时写了数据库操作，实际里面最好是写到缓存里面
            //查找两个用户之间的信息ＩＤ，要是有就写到库里面，要是没有就说明有问题，拒绝写入
            if($to_group_id == 0)
            {//不是发送给群组的
                
                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) )//这里还要加一个逻辑，缓存存在，但sockt连接中断中
                {//第一种状态，老王没在线，更新老王的消息就好，数据给我自己发送就好
                    $t_rs   = $dFriend ->where('my_uid='.$uf_info['to_uid'].' and fri_uid='.$uf_info['uid'])->find();
                    if($t_rs)
                    {
                        $td_arr['cmtn_cnt'] = $t_rs->cmtn_cnt +1;
                        $dFriend->where('id='.$t_rs->id)->save($td_arr);
                    }
                    
                    $send_msg   = '';

                }else{//下面都是老王的在线状态

                  if(isset($to_uf_info['to_uid']) && ($to_uf_info['to_uid'] == $fd_info['uid']))
                  {//太好了，说明老王在线，并正在和我聊天
                        //组装msg
                        $send_msg   = $this->rtnsendmsg($to_fd_info,1,$fd_info['uid'],2,$to_uid,0,$sim_msg,$_msg);
                  }else{//第二种状态，老王在线，但正在撩别的妹子，并没和我这个朋友聊天，给老王返回消息提示
                      
                        $t_rs   = $dFriend ->where('my_uid='.$to_uf_info['uid'].' and fri_uid='.$uf_info['uid'])->find();
                        if($t_rs)
                        {
                            $td_arr['cmtn_cnt'] = $t_rs->cmtn_cnt +1;
                            $dFriend->where('id='.$t_rs->id)->save($td_arr);
                        }
                        //组装msg
                        $send_msg   = $this->rtnsendmsg($to_fd_info,2,$fd_info['uid'],2,$to_uid,0,$sim_msg,$td_arr['cmtn_cnt']);
                  }
                }
                //更新双方数据
                unset($td_arr);
                $td_arr['uptime']   = $t_time;
                $td_arr['sim_msg']  = $sim_msg;
                $dFriend->where('cmtn_id='.$cmtn_id)->save($td_arr);
                
                return $send_msg;
            }else{//发送群组
                
                if($to_uf_info === false || (isset($to_uf_info) && $to_fd_info === false) )//这里还要加一个逻辑，缓存存在，但sockt连接中断中
                {//第一种状态，老王没在线，更新老王的消息就好，数据给我自己发送就好
                    $t_rs   = $dFriend ->where('my_uid='.$to_uid.' and group_id='.$to_group_id)->find();
                    if($t_rs)
                    {
                        $td_arr['cmtn_cnt'] = $t_rs->cmtn_cnt +1;
                        $td_arr['uptime']   = $t_time;
                        $td_arr['sim_msg']  = $sim_msg;
                        $dFriend->where('id='.$t_rs->id)->save($td_arr);
                    }
                    
                    $send_msg   = '';

                }else{//下面都是老王的在线状态

                  if(isset($to_uf_info['to_gid']) && ($to_uf_info['to_gid'] == $to_group_id))
                  {//太好了，说明老王在线，并正在和我聊天

                        $t_rs   = $dFriend ->where('my_uid='.$to_uid.' and group_id='.$to_group_id)->find();
                        if($t_rs)
                        {
                            $td_arr['uptime']   = $t_time;
                            $td_arr['sim_msg']  = $sim_msg;
                            $dFriend->where('id='.$t_rs->id)->save($td_arr);
                        }

                      $send_msg   = $this->rtnsendmsg($to_fd_info,1,$fd_info['uid'],2,$to_uid,$to_group_id,$sim_msg,$_msg);
                  }else{//第二种状态，老王在线，但正在撩别的妹子，并没和我这个朋友聊天，给老王返回消息提示
                      
                        $t_rs   = $dFriend ->where('my_uid='.$to_uid.' and group_id='.$to_group_id)->find();
                        if($t_rs)
                        {
                            $td_arr['cmtn_cnt'] = $t_rs->cmtn_cnt +1;
                            $td_arr['uptime']   = $t_time;
                            $td_arr['sim_msg']  = $sim_msg;
                            $dFriend->where('id='.$t_rs->id)->save($td_arr);
                        }

                      $send_msg   = $this->rtnsendmsg($to_fd_info,2,$fd_info['uid'],2,$to_uid,$to_group_id,$sim_msg,$td_arr['cmtn_cnt']);
                  }
                }
                
                return $send_msg;
            }
        }
           
    }

    //生成返回消息的格式
    public function rtnsendmsg($fd_info,$msg_type,$msg_from_uid,$is_self,$msg_to_uid,$msg_to_gid,$sim_msg,$_msg)
    {
        $send_msg    = array(
            'to_fd'     => $fd_info['fid'],
            'to_uid'    => $fd_info['uid'],
            'msg'       => array(
                '_type'     => $msg_type, //发送的消息类型，1表示消息到前端后直接显示在chat区，２表示消息到前端后显示在提示上，3表示发送异常，4发送群组创建消息,5添加好友消息处理
                '_form_uid' => $msg_from_uid,//消息来源ＩＤ前端显示消息时使用
                '_is_self'  => $is_self,//是否自己发送,1表示发送给自己,2表示发送给对方
                '_to_uid'   => $msg_to_uid,//消息受体的目标ID,前端显示消息时使用
                '_to_gid'   => $msg_to_gid,//发送的群组ＩＤ
                '_simmsg'   => $sim_msg,//短消息
                '_data'     => $_msg,//显示给前端需要展示的数据
            )
        );

        return $send_msg;
    }
    
}
