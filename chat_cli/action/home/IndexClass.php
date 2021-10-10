<?php
	if(!defined('ML')){die();}
	class IndexClass extends HomeAction{
		
        protected function _load()
        {
			//vp($_SERVER);
        }

        public function swlist()
        {
            $title  = '系统列表';
            $this->assign('title',$title);
            $this->template();
        }

        //首页进入聊天列表
        public function index()
        {
            $utoken     = get_utoken_cache($this->uid);
            $res	= get_uinfo_cache($this->uid);
            if($utoken == false || $res == false)
            {
                exit('Error3');
            }

            $res['token']   = $utoken['token'];

            $dFriend    = D('friend');
            $ulist        = $dFriend->where('my_uid='.$this->uid.' and state=1')->order('uptime desc')->select();

            //$ulist      = $dAdmin->where('state=1')->limit(50)->order('id desc')->select();
            $groupArr   = [];
            $friArr     = [];
            if($ulist)
            {
                foreach($ulist as $k=>$v)
                {
                    $ulist[$k]->tm  = $v->uptime==0 ? '01月01':date('Y.m.d',$v->uptime);
                    if($v->rel_type == 1)
                    {
                        $tmp_group      = get_group_cache($v->group_id);
//                        vp($tmp_group,2);
                        if(empty($tmp_group['gname'])){
                            $tmp_gname_arr  = [];
                            foreach($tmp_group['ulist'] as $kk=>$vv){
                                $tmp_gname_arr[]    = $vv['name'];
                            }

                            $tmp_group['gname']  = join('、',$tmp_gname_arr);
                        }

                        $ulist[$k]->name= $tmp_group['gname'];
                        $ulist[$k]->ginfo = $tmp_group;
                    }else{
                        $tmp_uinfo  = get_uinfo_cache($v->fri_uid);
                        $ulist[$k]->name= empty($v->diy_name)?$tmp_uinfo['name']:$v->diy_name;
                        $ulist[$k]->avt = $tmp_uinfo['pic'];
                    }
                }
            }

            $frilist      = $dFriend->where('my_uid='.$this->uid.' and state=1')->order('rel_type desc,id desc')->select();
            if($frilist)
            {
                foreach($frilist as $k=>$v)
                {
                    $frilist[$k]->tm  = $v->uptime==0 ? '01月01':date('Y.m.d',$v->uptime);
                    if($v->rel_type == 1)
                    {
                        $tmp_group      = get_group_cache($v->group_id);
//                        vp($tmp_group,2);
                        if(empty($tmp_group['gname'])){
                            $tmp_gname_arr  = [];
                            foreach($tmp_group['ulist'] as $kk=>$vv){
                                $tmp_gname_arr[]    = $vv['name'];
                            }

                            $tmp_group['gname']  = join('、',$tmp_gname_arr);
                        }

                        $frilist[$k]->name= $tmp_group['gname'];
                        $frilist[$k]->ginfo = $tmp_group;
                        $groupArr[]     = $frilist[$k];
                    }else{
                        $tmp_uinfo  = get_uinfo_cache($v->fri_uid);
                        $frilist[$k]->name= empty($v->diy_name)?$tmp_uinfo['name']:$v->diy_name;
                        $frilist[$k]->avt = $tmp_uinfo['pic'];
                        $friArr[]       = $frilist[$k];
                    }
                }
            }

            $this->assign('groupArr',$groupArr);
            $this->assign('friArr',$friArr);
            $this->assign('ulist',$ulist);
            $this->assign('res',$res);

            $this->template();
        }

        //获取群组详情
        public function getgroupdet()
        {
            $uid    = $this->uid;//当前用户的ID
            $gid    = isset($_POST['gid'])?intval($_POST['gid']):'';
            if($gid == '' || $gid == 0)
            {
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误!',
                    '_html'		=> ''
                );
                $this->return_json($r_array);
            }

            $dFriend    = D('friend');
            $res    = $dFriend ->where('my_uid='.$uid.' and group_id='.$gid)->find();
            if(!$res)
            {
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'	    => '数据有误!',
                    '_html'	    => ''
                );

                $this->return_json($r_array);
            }

            $ginfo  = get_group_cache($gid);
            if(empty($ginfo['gname'])){
                $tmp_gname_arr  = [];
                foreach($ginfo['ulist'] as $kk=>$vv){
                    $tmp_gname_arr[]    = $vv['name'];
                }

                $ginfo['gname']  = join('、',$tmp_gname_arr);
            }

            $_html  = '<div class="figroup"><div class="figtit">'.$ginfo['gname'].'</div><div class="figuls">';
            foreach($ginfo['ulist'] as $kk=>$vv){
                $_html  .= '<div class="figublk"><div class="figbavt">';
                $_html  .= '<img src="'.$vv['pic'].'"  width="50"/>';
                $_html  .= '</div><div class="figbtit">'.$vv['name'].'</div></div>';
            }
            $_html .= '</div><div class="figact"><a tp="2" gid="'.$gid.'" uname="'.$ginfo['gname'].'" fm="2" href="javascript:void(0)" onclick="xt_pub.get_msg(this)">发送消息</a></div></div>';


            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_html'		=> $_html
            );

            $this->return_json($r_array);
        }

        //获取朋友详情
        public function getfridet()
        {
            $uid    = $this->uid;//当前用户的ID
            $fid    = isset($_POST['fuid'])?intval($_POST['fuid']):'';
            if($fid == '' || $fid == 0)
            {
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误!',
                    '_html'		=> ''
                );

                $this->return_json($r_array);
            }

            $dFriend    = D('friend');
            $res    = $dFriend ->where('my_uid='.$uid.' and fri_uid='.$fid)->find();
            if(!$res)
            {
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'	    => '数据有误!',
                    '_html'	    => ''
                );

                $this->return_json($r_array);
            }

            $addtime        = Date('Y-m-d H:i',$res->c_time);

            $mem    = D('member')->where('id='.$fid)->find();
            $pic    = empty($mem->pic)?'/'.$this->wcfg['member_img_dir'].'123.jpg':'/'.$this->wcfg['member_img_dir'].$mem->pic;
            $tostr  = '<a tp="1" uid="'.$fid.'" uname="'.$mem->username.'" fm="2" href="javascript:void(0)" onclick="xt_pub.get_msg(this)">发送消息</a>';


            $_html  = <<<EOF
<div class="frinfo">
        <div class="fi_top">
                    	<div class="fit_avt">
                        	<img src="$pic"  width="50"/>
                        </div>
                        <div class="fit_name">
                        	{$mem->name}
                        </div>
                    </div>
                    <div class="fi_info">
                    	<ul>
                        	<li>
                            	<span class="fiitit">用户名</span>
                                <span class="fiival">{$mem->username}</span>
                            </li>
                            <li>
                            	<span class="fiitit">性 别</span>
                                <span class="fiival">男</span>
                            </li>
                            <li>
                            	<span class="fiitit">手 机</span>
                                <span class="fiival">{$mem->phone}</span>
                            </li>
							<li>
                            	<span class="fiitit">添加时间</span>
                                <span class="fiival">{$addtime}</span>
                            </li>
							<li>
                            	<span class="fiitit">签 名</span>
                                <span class="fiival">{$mem->intro}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="fiiact">
                    	{$tostr}
                    </div>
</div>
EOF;

            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_html'		=> $_html
            );

            $this->return_json($r_array);

        }

        public function gethistory()
        {

            $uid    = $this->uid;//当前用户的ID
            $fid    = isset($_POST['fuid'])?intval($_POST['fuid']):'';
            $gid    = isset($_POST['gid'])?intval($_POST['gid']):'';
            $tp     = isset($_POST['tp'])?intval($_POST['tp']):1;
            $pg     = isset($_POST['pg'])?intval($_POST['pg']):1;
            $ismge  = 0;//是否群组管理员
            if(($tp == 1 && ($fid == '' || $fid == 0)) || ($tp == 2 && ($gid == '' || $gid == 0)))
            {
                $r_array	= array(
                                '_state'	=> 'error',
                                '_msg'		=> '数据有误!',
                                '_html'		=> ''
                            );

                $this->return_json($r_array);
            }

            //更新用户缓存，把用户当前聊天的对应用户ＩＤ写入到用户的缓存中
            $rd = new redis_cache();
            $tm_uf_k   = 'uf_'.$uid;
            $tm_uf_arr = $rd->hget('ufinfo',$tm_uf_k);

             $dFriend    = D('friend');
            if($tp == 1){
                $tm_uf_arr['to_uid'] = $fid;
                $tm_uf_arr['to_gid'] = 0;
                $rd->hset('ufinfo',$tm_uf_k,$tm_uf_arr);
                
                 $res    = $dFriend ->where('my_uid='.$uid.' and fri_uid='.$fid)->find();
            
                if(!$res)
                {
                    $r_array	= array(
                                    '_state'	=> 'error',
                                    '_msg'	=> '不能给不是好友的人发消息!',
                                    '_html'	=> ''
                                );

                    $this->return_json($r_array);
                }
            }else if($tp ==2)
            {
                $tm_uf_arr['to_uid'] = 0;
                $tm_uf_arr['to_gid'] = $gid;
                $rd->hset('ufinfo',$tm_uf_k,$tm_uf_arr);
                $groupinfo  = get_group_cache($gid);
                if($groupinfo['adduid'] == $uid){
                    $ismge  = 1;
                }
                
                 $res    = $dFriend ->where('my_uid='.$uid.' and group_id='.$gid)->find();
            
                if(!$res)
                {
                    $r_array	= array(
                                    '_state'	=> 'error',
                                    '_msg'	=> '不能给不在的群发消息!',
                                    '_html'	=> ''
                                );

                    $this->return_json($r_array);
                }
            }
            
           
            if($res->cmtn_cnt > 0){
                $td_arr['cmtn_cnt']     = 0;
                $dFriend->where('id='.$res->id)->save($td_arr);
            }
            $chat_id    = $res->cmtn_id;

            $dMsg   = D('message');
            $psize   = 20;
            $start  = ($pg-1)*$psize;
            $npg    = $pg+1;
            $mlist  = $dMsg->where('cmtn_id='.$chat_id)->limit($start.','.$psize)->order('id desc')->select();
            
            $_html  = '';
            $arr_info   = array();
            if(!empty($mlist)){
                asort($mlist);

                $_html  .= '<div class="mc_blk"><div class="mc_more">';
                $_html  .= '<a href="javascript:void(0)" tp="'.$tp.'" gid="'.$gid.'" uid="'.$fid.'" pg="'.$npg.'" onclick="xt_pub.get_more_msg(this)">获取更多</a>';
                $_html  .= '</div></div>';
                $nowDate        = date('Ymd');
                foreach($mlist as $k=>$v){

                    if(!isset($arr_info[$v->form_uid]))
                    {
                        $arr_info[$v->form_uid] = get_uinfo_cache($v->form_uid);
                    }

                    if($v->con_tp == 4){

                        $disfilesize    = ChatService::rtnsize($v->filesize);
                        $fileimg        = ChatService::rtnextimg($v->exttp);
                        $con = <<<EOF
                        <div class="mbc_fl">
                                    <div class="mbcf_lf">
                                    	<div class="lftit">
                                        	{$v->content}
                                        </div>
                                        <div class="lfact">
                                            <span class="lftip">
                                                {$disfilesize}
                                            </span>
                                            <span class="lfdown">
                                                <a title="下载" href="javascript:void(0)" onclick="xt_pub.downfile(this)" msgid="{$v->id}"><span class="downtb"></span></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mbcf_rg">
                                    	<img src="{$fileimg}" width="50px;"/>
                                    </div>
                                </div>
EOF;
                    }else if($v->con_tp == 2)
                    {
                        $con    = bigimg_to_sm($v->content);
                        $con    = '<img src='.$con.' big_addr='.$v->content.' onclick="xt_pub.show_phote(this)">';
                    }else{
                        $con    = $v->content;

                        preg_match_all('/<img(.*)src=(.*)>/', $con, $result);
//                        print_r($result);
                        if(!empty($result[0])){
                            preg_match_all('/src=\"[A-Za-z0-9\/\.\:\-\_]+\"/', $con, $result2);
//                            print_r($result2);
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

                            $con  = str_replace($result[0],$rplArr,$con);
                        }

                    }

                    $stDate         = date('Ymd',$v->send_time);
                    $formatTime     = $nowDate == $stDate?date('H:i',$v->send_time):date('Y-m-d H:i',$v->send_time);
                    if($v->form_uid == $this->uid)
                    {
                        if($v->msgstate ==2)
                        {
                            $_smsg   = '<div class="mc_blk"><div class="chat_tip">';
                            $_smsg   .= '你撤回了一条消息';
                            $_smsg   .= '</div></div>';
                        }else{
                            $_smsg   = '<div class="mc_blk dom-msg-id_'.$v->id.'"><div class="mc_tm"><span>';
                            $_smsg   .= $formatTime;
                            $_smsg   .= '</span></div><div class="mcb_con "><div class="mcbc_avt_r">';
                            $_smsg   .= '<img onclick="xt_pub.open_meminfo(this)" memid="'.$v->form_uid.'" src="'.$arr_info[$v->form_uid]['pic'].'"  width="35"/>';

                            if($v->con_tp == 4){
                                $_smsg   .= '</div><div class="mcbc_con_r mchat_con_bj">';
                            }else{
                                $_smsg   .= '</div><div class="mcbc_con_r mchat_con_bj2">';
                            }

                            $_smsg   .= $con;
                            $_smsg   .= '</div><div class="mcbc_con_r_act" style="display: none;"><a href="javascript:void(0)" onclick="xt_pub.withdraw(this)" mesgid="'.$v->id.'" class="withdrawpic" title="撤回"></a></div></div></div>';
                        }

                        $_html .= $_smsg;
                    }else{
                        if($v->msgstate ==2)
                        {
                            $_msg   = '<div class="mc_blk"><div class="chat_tip">';
                            $_msg   .=  $arr_info[$v->form_uid]['name'].'撤回了一条消息';
                            $_msg   .= '</div></div>';
                        }else {
                            $_msg = '<div class="mc_blk dom-msg-id_'.$v->id.'"><div class="mc_tm"><span>';
                            $_msg .= $formatTime;
                            $_msg .= '</span></div><div class="mcb_con "><div class="mcbc_avt">';
                            $_msg .= '<img onclick="xt_pub.open_meminfo(this)" memid="' . $v->form_uid . '" src="' . $arr_info[$v->form_uid]['pic'] . '"  width="35"/>';
                            $_msg .= '</div><div class="mcbc_con mchat_con_bj">';
                            $_msg .= $con;
                            $_msg .= '</div></div></div>';
                        }
                        $_html .= $_msg;
                    }    
                }
            }

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_isman'    => $ismge,
                '_html'		=> $_html
            ];
            $this->return_json($r_array);
        }

        public function chatlist(){
            $this->template();
        }

        public function getchatlist(){

            $uinfo  = get_uf_hscache($this->uid);
            $dFriend    = D('friend');
            $uid        = $this->uid;
            $friid      = $uinfo['to_uid'];
            $gid        = $uinfo['to_gid'];
            if($friid > 0){
                $res    = $dFriend ->where('my_uid='.$uid.' and fri_uid ='.$friid)->find();
            }else if($gid > 0){
                $res    = $dFriend ->where('my_uid='.$uid.' and group_id='.$gid)->find();
            }else{
                $res = false;
            }

            if(!$res)
            {
                $this->rtnerror('数据异常,请联系管理员!');
            }

            $cmtn_id    = $res->cmtn_id;
            $pg     = isset($_POST['pg'])?intval($_POST['pg']):1;
            $seaval = isset($_POST['seaval'])?trim($_POST['seaval']):'';
            $dMsg   = D('message');
            $psize  = 20;
            $start  = ($pg-1)*$psize;

            if(empty($seaval))
            {
                $mlist  = $dMsg->where('cmtn_id='.$cmtn_id.' and msgstate =1')->limit($start.','.$psize)->order('id desc')->select();
                $count  = $dMsg->field('count(*) as num')->where('cmtn_id='.$cmtn_id.' and msgstate =1')->find();
            }else{//'%k' 效率极低的处理方式，做着玩玩，几个人试着玩一点问题也不用担心
                $mlist  = $dMsg->where('cmtn_id='.$cmtn_id.' and msgstate =1 and content like \'%'.$seaval.'%\'')->limit($start.','.$psize)->order('id desc')->select();
                $count  = $dMsg->field('count(*) as num')->where('cmtn_id='.$cmtn_id.' and msgstate =1 and content like \'%'.$seaval.'%\'')->find();
            }

            $_html  = '';
            $arr_info   = array();
            if(!empty($mlist)){
                asort($mlist);

                $nowDate        = date('Ymd');
                foreach($mlist as $k=>$v){

                    if(!isset($arr_info[$v->form_uid]))
                    {
                        $arr_info[$v->form_uid] = get_uinfo_cache($v->form_uid);
                    }

                    if($v->con_tp == 4){

                        $disfilesize    = ChatService::rtnsize($v->filesize);
                        $fileimg        = ChatService::rtnextimg($v->exttp);
                        $con = <<<EOF
                        <div class="mbc_fl">
                                    <div class="mbcf_lf">
                                    	<div class="lftit">
                                        	{$v->content}
                                        </div>
                                        <div class="lfact">
                                            <span class="lftip">
                                                {$disfilesize}
                                            </span>
                                            <span class="lfdown">
                                                <a title="下载" href="javascript:void(0)" onclick="xt_pub.downfile(this)" msgid="{$v->id}"><span class="downtb"></span></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mbcf_rg">
                                    	<img src="{$fileimg}" width="50px;"/>
                                    </div>
                                </div>
EOF;
                    }else if($v->con_tp == 2)
                    {
                        $con    = bigimg_to_sm($v->content);
                        $con    = '<img src='.$con.' big_addr='.$v->content.' par=1 onclick="xt_pub.show_phote(this)">';
                    }else{
                        $con    = $v->content;

                        preg_match_all('/<img(.*)src=(.*)>/', $con, $result);
                        if(!empty($result[0])){
                            preg_match_all('/src=\"[A-Za-z0-9\/\.\:\-\_]+\"/', $con, $result2);
                            $rplArr = [];
                            foreach($result2[0] as $k2=>$v2){
                                $tmpImg     = substr($v2,5,-1);
                                $isEmoji    = stripos($tmpImg,'emoji');
                                if($isEmoji === false){
                                    $rplArr[]   = '<img src='.$tmpImg.' big_addr='.$tmpImg.' par=1 onclick="xt_pub.show_phote(this)" class="inputimg">';
                                }else{
                                    $rplArr[]   = $result[0][$k2];
                                }
                            }
                            $con  = str_replace($result[0],$rplArr,$con);
                        }
                    }

                    $stDate         = date('Ymd',$v->send_time);
                    $formatTime     = $nowDate == $stDate?date('H:i',$v->send_time):date('Y-m-d H:i',$v->send_time);

                    $_smsg  = '<div class="ctll_blk"><div class="ctllb_pic">';
                    $_smsg  .= '<img onclick="xt_pub.open_meminfo(this)" memid="'.$v->form_uid.'" src="'.$arr_info[$v->form_uid]['pic'].'" width="35">';
                    $_smsg  .= '</div><div class="ctllb_con">';
//                    if($v->form_uid == $this->uid){
//                        $_smsg  .= '<div class="ctb_tit mycttt_cl">';
//                    }else{
                        $_smsg  .= '<div class="ctb_tit otcttt_cl">';
//                    }
                    $_smsg  .= '<span class="ctt_tit layui-font-12">'.$arr_info[$v->form_uid]['name'].'</span><span class="ctt_tm layui-font-12">'.$formatTime.'</span>';
                    if($v->con_tp == 4) {
                        $_smsg  .= '</div><div class="ctb_con layui-font-12 mchat_con_bj">';
                    }else{
                        $_smsg  .= '</div><div class="ctb_con layui-font-12">';
                    }
                    $_smsg  .= $con;
                    $_smsg  .= '</div></div></div>';
                    $_html .= $_smsg;
                }
            }

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_count'    => $count->num,
                '_html'		=> $_html
            ];
            $this->return_json($r_array);

        }

        public function searchmem()
        {
            $username   = trim($_POST['uname']);
            if(empty($username)){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '用户名不能为空'
                );

                $this->return_json($r_array);
            }

            if($username == $this->uinfo->username){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '自己不能加自己为好友!'
                );

                $this->return_json($r_array);
            }

            $res    = D('member')->where('username=\''.$username.'\' and allow_sch=1')->find();

            if(!$res){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '没找到符合要求的账号或对方设置了查找限制'
                );

                $this->return_json($r_array);
            }

            $uinfo  = get_uinfo_cache($res->id);
            $frires = D('friend')->where('my_uid='.$this->uid.' and fri_uid ='.$res->id.' and state=1')->find();
            if($frires)
            {
                $addfrStr   = '<a href="javascript:void(0)">已添加</a>';
            }else{
                $addfrStr   = '<a href="javascript:void(0)" uname="'.$username.'" upic="'.$uinfo['pic'].'" unc="'.$uinfo['name'].'" onclick="xt_mem.addfrimsg(this)">+好友</a>';
            }

            $_html  = <<<EOF
<div class="adfdrs">
        <div class="afrblk">
            <div class="afrbavt">
                <img src="{$uinfo['pic']}"  width="50"/>
            </div>
            <div class="afrbrg">
                <div class="afrbtit">
                    {$uinfo['name']}
                </div>
                <div class="afrbact">
                    {$addfrStr}
                </div>
            </div>
        </div>
    </div>
EOF;
            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_html'		=> $_html
            );

            $this->return_json($r_array);

        }

        public function frireqlist()
        {
            $this->template('frireq');
        }

        public function getfrireqlist()
        {
            $pg     = isset($_POST['pg'])?intval($_POST['pg']):1;
            $psize  = 10;
            $start  = ($pg-1)*$psize;
            $res    = D('frireq')->where('to_uid='.$this->uid)->limit($start.','.$psize)->order('id desc')->select();
            $count  = D('frireq')->field('count(*) as num')->where('to_uid='.$this->uid)->find();
            $rtnArr = [];
            $staArr = [
                0   => '未操作',
                1   => '已同意',
                2   => '已拒绝',
                3   => '已忽略'
            ];

            $uinfo  = D('member')->where('id='.$this->uid)->find();
            if($uinfo->notice_num >0){
                $upArr['notice_num']    = 0;
                D('member')->where('id='.$this->uid)->save($upArr);
            }

            $_html  = '';
            if($res) {
                foreach ($res as $k => $v) {
                    $tmpUinfo = get_uinfo_cache($v->form_uid);

                    $tmpArr = [
                        'reqid' => $v->id,
                        'reqtp' => $v->reqtp,
                        'intro' => $v->intro,
                        'state' => $v->state,
                        'state_str' => $staArr[$v->state],
                        'is_agree' => $v->is_agree,
                        'addtime' => date('Y.m.d', $v->addtime),
                        'form_avt' => $tmpUinfo['pic'],
                        'form_uname' => $tmpUinfo['uname'],
                        'form_name' => $tmpUinfo['name']
                    ];

                    $rtnArr[] = $tmpArr;
                }

                foreach ($rtnArr as $k => $v) {
                    if ($v['reqtp'] == 1) {
                        $tmpHtml = <<<EOF
                            <div class="frqblk">
                                <div class="frqavt">
                                    <img src="{$v['form_avt']}" height="35px"/>
                                </div>
                                <div class="frqrgt">
                                    <div class="frgrtit">
                                        <span class="tt">{$v['form_uname']}</span>
                                        <span class="if">请求加好友</span>
                                    </div>
                                    <div class="frqrif">
                                        <span class="frqrftt">申请时间:</span>
                                        <span class="frqrftf">{$v['addtime']}</span>
                                    </div>
                                    <div class="frqrif">
                                        <span class="frqrftt">附带信息:</span>
                                        <span class="frqrftf">{$v['intro']}</span>
                                    </div>
                                </div>
EOF;
                        if ($v['state'] == 0) {
                            $tmpHtml .= <<<EOF
                                <div class="frqract rtn_act_res_dom" reqid="{$v['reqid']}">
                                    <span class="agr"><a href="javascript:void(0)" onclick="xt_mem.handlefrireq(this)" agrval="1" >同意</a></span>
                                    <span class="nagr"><a href="javascript:void(0)" onclick="xt_mem.handlefrireq(this)" agrval="2">拒绝</a></span>
                                    <span class="wshi"><a href="javascript:void(0)" onclick="xt_mem.handlefrireq(this)" agrval="3">忽略</a></span>
                                </div></div>
EOF;
                        } else {
                            $tmpHtml .= <<<EOF
                            <div class="frqract rtn_act_res_dom" reqid="{$v['reqid']}">
                             <span class="rs"><span>{$v['state_str']}</span></span>
                            </div></div>
EOF;
                        }
                    }else{
                        $tmpstr = $v['is_agree']==1?'同意了':'拒绝了';
                        $tmpstr2 = $v['is_agree']==1?'已添加':'未添加';
                        $tmpHtml = <<<EOF
                         <div class="frqblk">
                            <div class="frqavt">
                                <img src="{$v['form_avt']}" height="35px"/>
                            </div>
                            <div class="frqrgt">
                                <div class="frgrtit">
                                    <span class="tt">{$v['form_uname']}</span>
                                    <span class="if">{$tmpstr}你的好友请求</span>
                                </div>
                                <div class="frqrif">
                                    <span class="frqrftt">操作时间:</span>
                                    <span class="frqrftf">{$v['addtime']}</span>
                                </div>
                            </div>
                            <div class="frqract">
                                <span class="rs"><span>{$tmpstr2}</span></span>
                            </div>
                        </div>
EOF;
                    }
                    $_html  .= $tmpHtml;
                }
            }

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_count'    => $count->num,
                '_html'		=> $_html
            ];
            $this->return_json($r_array);
        }

        public function handlefrireq()
        {
//            $r_array	= array(
//                '_state'	=> 'ok',
//                '_reqid'    => 123,
//                '_msg'		=> '测试下'
//            );
//
//            $this->return_json($r_array);

            $reqid      = intval($_POST['reqid']);
            $agrval     = intval($_POST['agrval']);
            if($reqid=='' || $reqid == 0){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '请求ID有误'
                );
                $this->return_json($r_array);
            }

            $res    = D('frireq')->where('id='.$reqid.' and to_uid='.$this->uid)->find();
            if(!$res){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误，未找到对应数据'
                );
                $this->return_json($r_array);
            }

            if($res->reqtp == 2){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误，未找到对应数据'
                );
                $this->return_json($r_array);
            }

            if($res->state > 0){
                $r_array	= array(
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误,未找到对应合法数据'
                );
                $this->return_json($r_array);
            }

            $nowtime    = time();
            $upFArr['uptime']   = $nowtime;

            //处理的反馈结果消息
            $dtArr['form_uid']  = $this->uid;
            $dtArr['to_uid']    = $res->form_uid;
            $dtArr['reqtp']     = 2;
            $dtArr['state']     = 0;
            $dtArr['form_reqid']= $reqid;
            $dtArr['addtime']   = $nowtime;
            $dtArr['uptime']    = $nowtime;
            $rtnstr             = '已同意';

            $rtnHmArr           = [];

            if($agrval == 1){//同意

                $firres    = D('friend') ->where('my_uid='.$this->uid.' and fri_uid='.$res->form_uid)->find();
                if($firres)
                {//如果已经是好友了

                }else{//未建立好友关系

                    $dFriend            = D('friend');
                    $u_da['my_uid']     = $this->uid;
                    $u_da['fri_uid']    = $res->form_uid;
                    $u_da['c_time']     = $nowtime;
                    $u_da['uptime']     = $nowtime;

                    $cmtn_id        = $dFriend->add($u_da);
                    $tt_da['cmtn_id']= $cmtn_id;
                    $dFriend->where('id='.$cmtn_id)->save($tt_da);

                    $a_da['my_uid']     = $res->form_uid;
                    $a_da['fri_uid']    = $this->uid;
                    $a_da['c_time']     = $nowtime;
                    $a_da['uptime']     = $nowtime;
                    $a_da['cmtn_id']    = $cmtn_id;
                    $dFriend->add($a_da);

//                    $rtnHmArr[$res->form_uid]  = $this->buildulisthtml($res->form_uid,$nowtime);
//                    $rtnHmArr[$this->uid] = $this->buildulisthtml($this->uid,$nowtime);

                    //如果是新建立的，分别生成两边的个人头像 合成HTML在生成的时候合成还是在发送消息的时候合成，还是在这里合成在缓存里面最方便
                    $rtnHmArr   = [
                        $res->form_uid  => [
                            'chathtml'  => $this->buildulisthtml($res->form_uid,$nowtime),
                            'frihtml'   => $this->buildulisthtml($res->form_uid,$nowtime,2),
                        ],
                        $this->uid      => [
                            'chathtml'  => $this->buildulisthtml($this->uid,$nowtime),
                            'frihtml'   => $this->buildulisthtml($this->uid,$nowtime,2),
                        ]
                    ];

                }

                $upFArr['state'] = 1;

                //向对方返回处理消息
                $dtArr['is_agree']  = 1;
                D('frireq')->add($dtArr);

                $uinfo  = D('member')->where('id='.$res->form_uid)->find();
                $upArr['notice_num']    = $uinfo->notice_num + 1;
                $upArr['uptime']        = $nowtime;
                D('member')->where('id='.$res->form_uid)->save($upArr);

            }else if($agrval ==2){//拒绝

                $upFArr['state'] = 2;
                //向对方返回处理消息
                $dtArr['is_agree']  = 2;
                D('frireq')->add($dtArr);
                $rtnstr = '已拒绝';

                $uinfo  = D('member')->where('id='.$res->form_uid)->find();
                $upArr['notice_num']    = $uinfo->notice_num + 1;
                $upArr['uptime']        = $nowtime;
                D('member')->where('id='.$res->form_uid)->save($upArr);

            }else{//忽略
                $upFArr['state'] = 3;
                $rtnstr = '已忽略';
            }

            D('frireq')->where('id='.$reqid)->save($upFArr);

            $reqid_key  = uniqid();
            $reqid_key  .= get_rand_str(4);
            $valArr     = ['_reqid'=>$reqid,'form_uid'=>$res->form_uid,'to_uid'=>$this->uid,'state'=>$upFArr['state'],'rtnhtml'=>$rtnHmArr];
            $expTime    = 60;
            set_key_cache($reqid_key,$valArr,$expTime);

            $r_array	= array(
                '_state'	=> 'ok',
                '_reqid'    => $reqid_key,
                '_msg'		=> $rtnstr
            );

            $this->return_json($r_array);
        }

        public function buildulisthtml($uid,$nowtime,$tp=1)
        {
            $tmp_uinfo  = get_uinfo_cache($uid);
            $_html  = '';

            if($tp ==1){
                $_html  .= '<div class = "ublk_area">';
                $_html  .= '<div class="ublk chat_blk_'.$tmp_uinfo['id'].'" onclick="xt_pub.get_msg(this)" uid="'.$tmp_uinfo['id'].'" uname="'.$tmp_uinfo['name'].'">';
                $_html  .= '<div class="uk_avt">';
                $_html  .= '<img src="'.$tmp_uinfo['pic'].'"  width="40"/>';
                $_html  .= '</div><div class="ukl"><div class="uk_nm">';
                $_html  .= $tmp_uinfo['name'];
                $_html  .= '</div><div class="sim_msg"></div></div>';
                $_html  .= '<div class="ukr"><div class="uk_tm">';
                $_html  .= date('Y.m.d',$nowtime);
                $_html  .= '</div>';
                $_html .= '<div class="mg_num chat_cmtn_num" style="display:none"></div></div></div></div>';
            }else{
                $_html  .= '<div class = "ublk_area">';
                $_html .= '<div class="ublk" onclick="xt_pub.getfriinfo(this)" uid="'.$tmp_uinfo['id'].'"><div class="uk_avt">';
                $_html .= '<img src="'.$tmp_uinfo['pic'].'"  width="40"/>';
                $_html .= '</div><div class="uk_lnm">'.$tmp_uinfo['name'].'</div></div></div>';
            }

            return $_html;
        }

        public function addfrireq()
        {
            $username   = trim($_POST['uname']);
            if(empty($username)){
                $this->rtnerror('添加失败:用户名不能为空');
            }

            if($username == $this->uinfo->username){
                $this->rtnerror('添加失败:自己不能加自己为好友!');
            }

            $res    = D('member')->where('username=\''.$username.'\' and allow_sch=1')->find();

            if(!$res){
                $this->rtnerror('添加失败:没找到符合要求的账号或对方设置了查找限制');
            }

            $frires = D('friend')->where('my_uid='.$this->uid.' and fri_uid ='.$res->id.' and state=1')->find();

            if($frires)
            {
                $this->rtnerror('添加失败:'.$username.'已经是您的好友');
            }

            $xzres      = D('frireq')->field('count(*) as num')->where('form_uid='.$this->uid.' and to_uid='.$res->id.' and state =0 and reqtp = 1')->find();
            if($xzres->num >=5)
            {
                $this->rtnerror('请求失败:你对该用户已发送过多次好友请求');
            }

            $nowtime    = time();

            $dtArr['form_uid']  = $this->uid;
            $dtArr['to_uid']    = $res->id;
            $dtArr['reqtp']     = 1;
            $dtArr['intro']     = trim($_POST['smsg']);
            $dtArr['state']     = 0;
            $dtArr['addtime']   = $nowtime;
            $dtArr['uptime']    = $nowtime;
            $reqid = D('frireq')->add($dtArr);

            $uinfo  = D('member')->where('id='.$res->id)->find();
            $upArr['notice_num']    = $uinfo->notice_num + 1;
            $upArr['uptime']        = $nowtime;
            D('member')->where('id='.$res->id)->save($upArr);

            $reqid_key  = uniqid();
            $reqid_key  .= get_rand_str(4);
            $valArr     = ['_reqid'=>$reqid,'form_uid'=>$this->uid,'to_uid'=>$res->id,'state'=>0];
            $expTime    = 60;
            set_key_cache($reqid_key,$valArr,$expTime);

            $r_array	= array(
                 '_state'	=> 'ok',
                '_reqid'    => $reqid_key,
                 '_msg'		=> '好友请求发送成功，请等待对方应答'
            );

            $this->return_json($r_array);

        }

        public function meminfo()
        {
            $memberid   = intval($_GET['memberid']);
            $groupid    = intval($_GET['groupid']);
            if($memberid == 0 || empty($memberid)){
                $memberid = $this->uid;
            }

            $mem_id     = $this->uid;

            $dFriend    = D('friend');
            $ulist      = $dFriend->where('my_uid='.$this->uid.' and fri_uid ='.$memberid)->find();
            if($ulist){
                 $uinfo  = get_uinfo_cache($memberid);
                $mem_id = $memberid;
            }else{//查看是否在公共群组
                if($memberid == $this->uid)
                {
                    $uinfo  = get_uinfo_cache($memberid);
                }else if($groupid == 0 || empty($groupid))
                {
                    exit('异常');
                }else{
                    $groupinfo  = get_group_cache($groupid);
                    $gulist     = $groupinfo['ulist'];
                    $guidarr    = [];
                    foreach($gulist as $kk=>$vv){
                        $guidarr[]  = $vv['uid'];
                    }

                    if(in_array($this->uid,$guidarr) && in_array($memberid,$guidarr))
                    {
                        $uinfo  = get_uinfo_cache($memberid);
                    }else{
                        exit('异常1');
                    }
                }
            }

            $dModel = D('member');
            $res  = $dModel->where('id='.$memberid)->find();

            $uinfo['pic']   = empty($res->pic)?'/'.$this->wcfg['member_img_dir'].'123.jpg':'/'.$this->wcfg['member_img_dir'].$res->pic;
            $uinfo['name']  = $res->name;
            $uinfo['uname'] = $res->username;
            $uinfo['phone'] = $res->phone;
            $uinfo['intro'] = $res->intro;

            $this->assign('uinfo',$uinfo);
            $this->template('usinfo');
        }

        //修改个人信息
        public function upinfo()
        {
            $uid    = $this->uid;
            $dModel = D('member');
            $uinfo  = $dModel->where('id='.$uid)->find();

            $this->assign('uinfo',$uinfo);
            $this->template('upinfo');
        }

        public function subinfo()
        {
            $name         = $_POST['name'];
            $email        = $_POST['email'];
            $phone        = $_POST['phone'];
            $intro        = $_POST['intro'];
            $alwsch       = $_POST['alwsc'];

            if(empty($name)){
                $r_array	= array(
                    '_state'	=> 'err',
                    '_msg'		=> '呢称不能为空!'
                );

                $this->return_json($r_array);
            }

            $upda['name']       = $name;
            $upda['email']      = $email;
            $upda['phone']      = $phone;
            $upda['intro']      = $intro;
            $upda['allow_sch']  = $alwsch == 1?1:2;
            $upda['uptime']     = time();

            $dMember		= D('member');
            $res = $dMember->where('id='.$this->uid)->save($upda);

            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!'
            );

            $this->return_json($r_array);

        }

        //修改密码
        public function uppwd()
        {
            $this->template('pwd');
        }

        public function subpwd()
        {
            $oldpwd         = $_POST['oldpwd'];
            $newpwd         = $_POST['newpwd'];
            $cfpwd          = $_POST['cfpwd'];

            if($newpwd != $cfpwd){
                $r_array	= array(
                    '_state'	=> 'err',
                    '_msg'		=> '确认密码与新密码不一致，请确认输入正常!'
                );

                $this->return_json($r_array);
            }

            $dMember		= D('member');
            $uinfo          = $dMember->where('id='.$this->uid)->find();

            if($oldpwd != $uinfo->password){
                $r_array	= array(
                    '_state'	=> 'err',
                    '_msg'		=> '原密码输入错误!'
                );

                $this->return_json($r_array);
            }

            $upda['password']   = $newpwd;
            $upda['uptime']     = time();

            $res = $dMember->where('id='.$this->uid)->save($upda);

            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!'
            );

            $this->return_json($r_array);
        }


        public function addgroup()
        {
//            exit();

            $dFriend    = D('friend');
            $ulist      = $dFriend->where('my_uid='.$this->uid.' and state=1 and rel_type=0')->order('uptime desc')->select();

            $gid        = intval($_GET['gid']);
            $upgid      = 0;
            $uidArr     = [];
            if(empty($gid) || $gid == 0)
            {

            }else{

                $groupinfo  = get_group_cache($gid);
                $res        = $dFriend->where('my_uid='.$this->uid.' and group_id='.$gid)->find();
                if($res)
                {
                    $upgid  = $gid;
                    foreach($groupinfo['ulist'] as $k=>$v){
                        $uidArr[]   = $v['uid'];
                    }
                }
            }

            if($ulist)
            {
                foreach($ulist as $k=>$v)
                {
                    $tmp_uinfo  = get_uinfo_cache($v->fri_uid);
                    $ulist[$k]->name    = empty($v->diy_name)?$tmp_uinfo['name']:$v->diy_name;
                    $ulist[$k]->avt     = $tmp_uinfo['pic'];
                    $ulist[$k]->issel   = 0;
                    if(in_array($v->fri_uid,$uidArr)){
                        $ulist[$k]->issel = 1;
                    }
                }
            }

            $this->assign('ulist',$ulist);
            $this->assign('upgid',$upgid);
            $this->template();
        }

        //删除群组用户
        public function delgroupuser()
        {
            $groupid    = intval($_GET['groupid']);
            //$groupid    = 1;
            if(empty($groupid) || $groupid == 0){
                exit('异常');
            }

            $groupinfo  = get_group_cache($groupid);
            if($this->uid != $groupinfo['adduid']){
                exit('异常');
            }

            $ulist  = [];
            foreach($groupinfo['ulist'] as $k=>$v){
                if($v['uid'] != $this->uid)
                {
                    $ulist[]    = $v;
                }
            }

            $this->assign('ulist',$ulist);
            $this->assign('upgid',$groupid);
            $this->template();
        }
        
        public function creatgroup()
        {

//            $rtn_arr	= array(
//                '_state'    => 'ok',
//                '_msg'      => '添加成功',
//                '_sendmsg'	=> [
//                    'groupid'   => 5,
//                    'msgdt'     => $this->buildgrouphtml(5)
//                ]
//            );
//
//            $this->return_json($rtn_arr);
            
            $uid_arr    = $_POST['udata'];
            $upgid      = intval($_POST['upgid']);
            $acttp      = $_POST['acttp'];
            $_time      = time();
            $isgact     = 1;//群组操作类型，1新增，2修改，3删除

            $dFriend      = D('friend');
            $frilist      = $dFriend->where('my_uid='.$this->uid.' and state=1 and rel_type=0')->order('rel_type desc,id desc')->select();
            if(!$frilist){
                $rtn_arr	= array(
                    '_state'	=> 'error',
                    '_msg'	=> '数据有误'
                );
                $this->return_json($rtn_arr);
            }

            $fridArr    = [];
            foreach($frilist as $kk=>$vv)
            {
                $fridArr[]  = $vv->fri_uid;
            }

            $isExp      = 0;
            foreach($uid_arr as $k=>$v)
            {
                if(!in_array($v,$fridArr)){
                    $isExp  = 1;
                }
            }

            if($isExp == 1){
                $rtn_arr	= array(
                    '_state'	=> 'error',
                    '_msg'	    => '数据有误'
                );
                $this->return_json($rtn_arr);
            }

            $delUidArr  = [];

            if($upgid == 0 || empty($upgid)){

                if(empty($uid_arr) || count($uid_arr) ==1)
                {
                    $rtn_arr	= array(
                        '_state'	=> 'error',
                        '_msg'	=> '数据有误'
                    );
                    $this->return_json($rtn_arr);
                }

                $dGroup  = D('group');
                $d_arr['adduid']   = $this->uid;
                $d_arr['addtime']   = $_time;
                $d_arr['uptime']    = $_time;

                $group_id   = $dGroup->add($d_arr);

                if(!$group_id)
                {
                    $rtn_arr	= array(
                        '_state'	=> 'error',
                        '_msg'	=> '添加失败'
                    );

                    $this->return_json($rtn_arr);
                }

                $u_da['my_uid']     = $this->uid;
                $u_da['fri_uid']    = 0;
                $u_da['group_id']   = $group_id;
                //$u_da['cmtn_id']    = $group_id;
                $u_da['c_time']     = $_time;
                $u_da['uptime']     = $_time;
                $u_da['rel_type']   = 1;


                $cmtn_id            = $dFriend->add($u_da);
                $upArr['cmtn_id']   = $cmtn_id;

                $dGroup->where('id='.$group_id)->save($upArr);
                $dFriend->where('id='.$cmtn_id)->save($upArr);

                $u_da['cmtn_id']    = $cmtn_id;

                foreach ($uid_arr as $k=>$v)
                {
                    $u_da['my_uid']   = $v;
                    $dFriend->add($u_da);
                }

                //更新用户缓存，把用户当前聊天的对应用户ＩＤ写入到用户的缓存中
                $rd = new redis_cache();
                $tm_uf_k   = 'uf_'.$this->uid;
                $tm_uf_arr = $rd->hget('ufinfo',$tm_uf_k);
                $tm_uf_arr['to_uid'] = 0;
                $tm_uf_arr['to_gid'] = $group_id;
                $rd->hset('ufinfo',$tm_uf_k,$tm_uf_arr);

            }else{

                if(empty($uid_arr) || count($uid_arr) ==0)
                {
                    $this->rtnerror('数据有误');
                }

                if($acttp == 'del'){

                    $groupinfo  = get_group_cache($upgid);
                    if($this->uid != $groupinfo['adduid']){
                        $this->rtnerror('数据有误');
                    }

                    $groupRes   = $dFriend->where('group_id='.$upgid)->select();
                    $delIdArr   = [];
                    $delUidArr  = [];
                    foreach($groupRes as $k=>$v){
                        if(in_array($v->my_uid,$uid_arr))
                        {
                            $delIdArr[]     = $v->id;
                            $delUidArr[]    = $v->my_uid;
                        }
                    }
                    if(empty($delIdArr))
                    {
                        $this->rtnerror('数据有误');
                    }
                    $group_id   = $upgid;
                    $dFriend->where('id in ('.join(',',$delIdArr).')')->delete();

                    $isgact       = 3;//删除用户

                    $upArr['uptime']     = $_time;
                    $dFriend->where('group_id='.$group_id)->save($upArr);

                }else{

                    $res        = $dFriend->where('my_uid='.$this->uid.' and group_id='.$upgid)->find();
                    if(!$res)
                    {
                        $this->rtnerror('数据有误');
                    }

                    $group_id   = $upgid;
                    $isgact       = 2;

                    $u_da['fri_uid']    = 0;
                    $u_da['group_id']   = $group_id;
                    //$u_da['cmtn_id']    = $group_id;
                    $u_da['c_time']     = $_time;
                    $u_da['uptime']     = $_time;
                    $u_da['rel_type']   = 1;
                    $u_da['cmtn_id']    = $res->cmtn_id;

                    $groupinfo  = get_group_cache($group_id);
                    $uidArr     = [];
                    foreach($groupinfo['ulist'] as $k=>$v){
                        $uidArr[]   = $v['uid'];
                    }


                    foreach ($uid_arr as $k=>$v)
                    {
                        if(!in_array($v,$uidArr)){//防止前端修改数据，这里验证下只有不在群组里面的用户ID才能添加成功，
                            $u_da['my_uid']   = $v;
                            $dFriend->add($u_da);
                        }
                    }
                }

                $upArr['uptime']     = $_time;
                $dFriend->where('group_id='.$group_id)->save($upArr);

            }

            $rtn_arr	= array(
                '_state'    => 'ok',
                '_msg'      => '添加成功',
                '_sendmsg'	=> [
                    'groupid'   => $group_id,
                    'msgdt'     => ChatService::buildgrouphtml($group_id,$isgact,$delUidArr)
                ]
            );

            $this->return_json($rtn_arr);
            
        }

        public function formatcont()
        {

            $sendStr      = $_POST['sendtxt'];
            //preg_match_all('/(src="data:image\/(\w+);base64,)+[A-Za-z0-9\/\+]+\"/', $str, $result);
            $abc = preg_match_all('/("data:image\/(\w+);base64,)+[A-Za-z0-9\/\+\;\,\=]+\"/', $sendStr, $result);

//            echo '<pre>';
//            var_dump($abc);
//            print_r($result);
//            return;

            $replaceArr = [];
            $replVal    = [];
            $memimgdir  = $this->wcfg['member_img_dir'];
            foreach($result[0] as $k=>$v){
                $tmpV       = substr($v,1,-1);
                $tmpVarr    = explode(',',$tmpV);
                $tmpNewName	= date('YmdHis').rand(0,999);
                $filename   = $memimgdir.$tmpNewName.'.'.$result[2][$k];
                $tmpRtn = ChatService::savebase64pic($tmpVarr[1],$filename);
                if($tmpRtn == false){

                }else{
                    $replaceArr[]   = $tmpV;
                    $replVal[]      = '/'.$tmpRtn;
                }
            }
//
//            print_r($replaceArr);
//            print_r($replVal);

            $sendmsg    = str_replace($replaceArr,$replVal,$sendStr);
            $rtn_arr	= array(
                '_state'	=> 'ok',
                '_msg'		=> '添加成功',
                '_sendmsg'	=> $sendmsg,
            );

            $this->return_json($rtn_arr);

        }

        public function upload()
        {
            if(empty($_FILES['files']['tmp_name']))
            {
                $this->rtnerror('上传文件不能为空');
            }else{
                    $up_tp      = intval($_POST['up_ty']);
                    if($up_tp ==2)
                    {
                        $pic_img	= new pic_class();
                        $dir		= $this->wcfg['member_img_dir'];
                        $res		= $pic_img->upload_member_img($_FILES['files'],$dir);
                    }else{
                        $pic_img	= new pic_class();
                        $dir            = $this->wcfg['chat_img_dir'];
                        $res            = $pic_img->upload_chat_img($_FILES['files'],$dir);
                    }
                    

                    if($res == false){
                        $this->rtnerror('上传出错');
                    }
                    
                    if($up_tp ==2)
                    {
                        $rd = new redis_cache();
                        $tm_k   = 'uinfo_'.$this->uid;
                        $u_arr  = false;

                        $_arr['pic']	= $res;
                        $dMember		= D('member');
			            $up_res			= $dMember->where('id='.$this->uid)->save($_arr);
                        $rd->hset('userinfo',$tm_k,$u_arr);
                    }
            }
            
            
            $rtn_arr	= array(
                '_state'	=> 'ok',
                '_msg'		=> '添加成功',
                '_adr'		=> '/'.$dir.$res,
                '_sadd'		=> '/'.$dir.'sm_'.$res
            );
		
            $this->return_json($rtn_arr);
        }


        public function uploadannex()
        {
            if(empty($_FILES['files']['tmp_name']))
            {
                $this->rtnerror('上传文件不能为空');
            }

            $file_name	= $_FILES['files']['name'];
            $file_size  = $_FILES['files']['size'];
            $extname    = explode('.',$file_name);
            $extname    = end($extname);

            $dir            = $this->wcfg['annex_dir'];
            $nfname         = uniqid();
            $nfname         .= get_rand_str(5);
            $nfname         = md5($nfname);

            $new_address	= $dir.$nfname.'.'.$extname;
            //简单粗暴处理
            move_uploaded_file($_FILES["files"]["tmp_name"],$new_address);
            $fileimg        = ChatService::rtnextimg($extname);


            $disfilesize    = ChatService::rtnsize($file_size);
            $_html  = <<<EOF
<div class="mbc_fl">
    <div class="mbcf_lf">
        <div class="lftit">
            {$file_name}
        </div>
        <div class="lfact">
            <span class="lftip">
                {$disfilesize}
            </span>
            <span class="lfdown">
                <a title="下载" href="javascript:void(0)" onclick="xt_pub.downfile(this)" msgid="#mesgid#"><span class="downtb"></span></a>
            </span>
        </div>
    </div>
    <div class="mbcf_rg">
        <img src="{$fileimg}" width="50px;"/>
    </div>
</div>
EOF;

            $reqid_key  = uniqid();
            $reqid_key  .= get_rand_str(4);
            $expTime    = time() + 3600*24*30;//七天有效期
            $valArr     = ['_filename'=>$file_name,'_extname'=>$extname,'_html'=>$_html,'_realaddr'=>$new_address,'_exptime'=>$expTime,'_fsize'=>$file_size];
            $expTime    = 60;
            set_key_cache($reqid_key,$valArr,$expTime);


            $rtn_arr	= array(
                '_state'	=> 'ok',
                '_msg'		=> '添加成功',
                '_annexkey'	=> $reqid_key
            );

            $this->return_json($rtn_arr);
        }

        public function downfile()
        {

            $mesid  = intval($_GET['msgid']);
            $mesres = D('message')->where('id='.$mesid)->find();
            if($mesres->to_group_id >0){
                $frires = D('friend')->where('my_uid='.$this->uid.' and group_id='.$mesres->to_group_id)->find();
                if(!$frires)
                {
                    exit('error1');
                }
            }else{

                if($mesres->form_uid == $this->uid || $mesres->to_uid == $this->uid)
                {

                }else{
                    exit('error2');
                }
            }

            $filename   = $mesres->content;
            $file       = $mesres->realaddr;


            header("Content-type: file");
            header("Content-Disposition: attachment;filename=$filename");
            header("Content-Transfer-Encoding: binary");
            header('Pragma: no-cache');
            header('Expires: 0');
            set_time_limit(0);
            readfile($file);
        }

        public function withdraw()
        {
            $mesgid     = intval($_POST['mesgid']);
            if($mesgid == 0 || empty($mesgid))
            {
                $this->rtnerror('消息ID在误!');
            }

            $dMessage   = D('message');
            $msginfo    = $dMessage->where('id='.$mesgid.' and form_uid='.$this->uid)->find();
            if($msginfo)
            {
                if($msginfo->msgstate == 2)
                {
                    $this->rtnerror('发送数据有误!');
                }

                $now_time   = time();
                $aldtime    = $now_time - $msginfo->send_time;
                if($aldtime > 24*3600){
                    $this->rtnerror('超过24小时的消息不能撤回!');
                }

                $upda['msgstate']   = 2;
                $upda['uptime']     = time();

                $dMessage->where('id='.$mesgid)->save($upda);

            }else{
                $this->rtnerror('发送数据有误1!');
            }

            $reqid_key  = uniqid();
            $reqid_key  .= get_rand_str(4);
            $valArr     = ['_mesgid'=>$mesgid];
            $expTime    = 60;
            set_key_cache($reqid_key,$valArr,$expTime);

            $r_array	= array(
                '_state'	=> 'ok',
                '_msg'		=> '成功!',
                '_wdkey'    => $reqid_key
            );

            $this->return_json($r_array);
        }
}
