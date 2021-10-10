<?php

	if(!defined('ML')){die();}	
	class WuziqClass extends HomeAction{
		
        protected function _load()
        {
			//vp($_SERVER);
        }

        //五子棋列表
        public function index()
        {
            $utoken     = get_utoken_cache($this->uid);
            $res	    = get_uinfo_cache($this->uid);
            if($utoken == false || $res == false)
            {
                exit('Error3');
            }

            WuziqService::uptblistuser($this->uid);//加入桌面列表

            $res['token']   = $utoken['token'];
            $pgdis      = 1;//1显示列表，2显示桌子
            $tabRes     = D('wztable')->select();
            $tabUsRes   = D('wzulist')->where('userid='.$this->uid)->find();
            $tabUtbid   = 0;
            $tmpTabIf   = [];
            $nowtime    = time();
            if($tabUsRes->tableid>0 && $tabUsRes->tableposid>0)
            {
                $tabUtbid   = $tabUsRes->tableid;
            }
            $tablist    = [];
            foreach($tabRes as $k=>$v)
            {
                $tablist[$v->id]  = [
                    'id'    => $v->id,
                    'state' => $v->state,//0空位置，1对站中，2准备中
                    'user1' => $v->user1id == 0?[]:get_uinfo_cache($v->user1id),
                    'user2' => $v->user2id == 0?[]:get_uinfo_cache($v->user2id),
                ];

                if($tabUtbid>0 && $v->id == $tabUtbid)
                {
                    $tmpTabIf   = $v;
                }
            }

            $tabstaArr  = [
                0 => '空位置',
                1 => '对战中',
                2 => '等待中'
            ];

            $tabinfo    = [];
            //获取当前用户信息
            if($tabUsRes->tableid>0 && $tabUsRes->tableposid>0)
            {//初始化桌子
                $tabTim = D('wztimes')->where('id='.$tmpTabIf->times)->find();
                $pgdis  = 2;
                $nowChs = 0;
                if( $tmpTabIf->state == 1 && $tmpTabIf->nowposid ==2)
                {
                    if($this->uid == $tmpTabIf->user2id)
                    {
                        $nowChs = 1;
                    }
                }else if($tmpTabIf->state == 1 && $tmpTabIf->nowposid ==1){
                    if($this->uid == $tmpTabIf->user1id)
                    {
                        $nowChs = 1;
                    }
                }

                $tmpLsArr   = [];
                $wattime    = 0;
                if(!empty($tabTim->lastch))
                {
                    $tmpLsArr   = explode('_',$tabTim->lastch);
                    $wattime    = $tabTim->uptime + 120 - $nowtime;
                    $wattime    = $wattime>0?$wattime:0;
                }

                $tabinfo    = [
                    'id'        => $tabUsRes->tableid,
                    'state'     => $tablist[$tabUsRes->tableid]['state'],
                    'user1'     => $tablist[$tabUsRes->tableid]['user1'],
                    'user2'     => $tablist[$tabUsRes->tableid]['user2'],
                    'u1sta'     => $tmpTabIf->u1state,
                    'u2sta'     => $tmpTabIf->u2state,
                    'nowposid'  => $tmpTabIf->state ==1?$tmpTabIf->nowposid:0,//只有棋局正在开始的时候返回轮到谁了
                    'chesscon'  => $tmpTabIf->state ==1?unserialize($tabTim->chesscon):[],//只有棋局正在开始的时候返回棋局
                    'nowChs'    => $nowChs,
                    'lastcharr' => $tmpLsArr,
                    'wattime'   => $wattime
                ];
            }

//            vp($tabinfo);

            $title  = '五子棋';
            $this->assign('title',$title);
            $this->assign('tabinfo',$tabinfo);
            $this->assign('pgdis',$pgdis);
            $this->assign('tabsta',$tabstaArr);
            $this->assign('tablist',$tablist);
            $this->assign('res',$res);
            $this->template();
        }

        public function getversusinfo()
        {
            $tbbid = intval($_POST['tbid']);

            /**
            $rd = new redis_cache();
            $tm_uf_k   = 'uf_'.$uid;//获取用户的缓存
            $uf_info   = $rd->hget('ufinfo',$tm_uf_k);

            $rd->hset('fdinfo', $tm_fd_k, $tm_fd_arr);
             * */

            if($tbbid == 0 || $tbbid >20)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据有误'
                ];

                $this->return_json($r_array);
            }

            $tabRes     = D('wzulist')->where('userid='.$this->uid)->find();
            $upArr['state']     = 2;
            $upArr['tableid']   = $tbbid;
            $upArr['lastuptime']= time();

            D('wzulist')->where('id='.$tabRes->id)->save($upArr);

            $rnthtml    = [
                'posiontion1'   => '<div class="wzemp" onclick="xt_wuzi.sitdown(this)" poid="1">坐下</div>',
                'postip1'       => '',
                'posiontion2'   => '<div class="wzemp" onclick="xt_wuzi.sitdown(this)" poid="2">坐下</div>',
                'postip2'       => ''
            ];

            $tabInfo    = D('wztable')->where('id='.$tbbid)->find();

            if(!empty($tabInfo->user1id))
            {
                $tabuf  = get_uinfo_cache($tabInfo->user1id);
                $_html  = '<div class="avt">';
                $_html  .= '<img src="'.$tabuf['pic'].'"  width="25"/>';
                $_html  .= '</div><div class="tit">';
                $_html  .= $tabuf['name'];
                $_html  .= '</div>';

                $rnthtml['posiontion1']   = $_html;
                if($tabInfo->u1state == 1)
                {
                    $rnthtml['postip1']   = '准备中';
                }
            }

            if(!empty($tabInfo->user2id))
            {
                $tabuf  = get_uinfo_cache($tabInfo->user2id);
                $_html  = '<div class="avt">';
                $_html  .= '<img src="'.$tabuf['pic'].'"  width="25"/>';
                $_html  .= '</div><div class="tit">';
                $_html  .= $tabuf['name'];
                $_html  .= '</div>';

                $rnthtml['posiontion2']   = $_html;
                if($tabInfo->u2state == 1)
                {
                    $rnthtml['postip2']   = '准备中';
                }
            }

            WuziqService::uptblistuser($this->uid,$tbbid);//退出桌面列表

            $tabstaArr  = [
                0 => '空位置',
                1 => '对战中',
                2 => '等待中'
            ];

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '进入成功',
                '_tabsta'   => $tabstaArr[$tabInfo->state],
                '_rtnht'    => $rnthtml
            ];

            $this->return_json($r_array);
        }

        public function sitdown()
        {
            $positionid = isset($_POST['posid'])?intval($_POST['posid']):1;
            $positionid = $positionid ==1?1:2;

            $uTabRes     = D('wzulist')->where('userid='.$this->uid)->find();
            if($uTabRes->tableid == 0){
                $r_array	= [
                    '_state'	=> 'error1',
                    '_msg'		=> '数据异常'
                ];
                $this->return_json($r_array);
            }

            if($uTabRes->tableposid > 0){
                $r_array	= [
                    '_state'	=> 'error6',
                    '_msg'		=> '渣,你已经在其它位置坐下,不能脚踩多条船'
                ];
                $this->return_json($r_array);
            }

            $tbbid      = $uTabRes->tableid;

            $tabInfo    = D('wztable')->where('id='.$tbbid)->find();
            if(($positionid == 1 && $tabInfo->user1id >0) || ($positionid == 2 && $tabInfo->user2id >0))
            {
                $r_array	= [
                    '_state'	=> 'error3',
                    '_msg'		=> '数据异常'
                ];
                $this->return_json($r_array);
            }

            if($tabInfo->state ==1)
            {
                $r_array	= [
                    '_state'	=> 'error4',
                    '_msg'		=> '对战中'
                ];
                $this->return_json($r_array);
            }

            if($positionid ==1){
                $upTab['user1id']   = $this->uid;
                $upUs['tableposid'] = $positionid;
            }else{
                $upTab['user2id']   = $this->uid;
                $upUs['tableposid'] = 2;
            }

            $upUs['lastuptime'] = time();

            if($tabInfo->state == 0){
                $upTab['state']   = 2;//状态更改
            }

            //这里应用一个事务,随便写的这种架架不支持事务...hehe
            D('wztable')->where('id='.$tabInfo->id)->save($upTab);
            D('wzulist')->where('id='.$uTabRes->id)->save($upUs);

            $userInfo   = get_uinfo_cache($this->uid);

            $_html  = '<div class="avt">';
            $_html  .= '<img src="'.$userInfo['pic'].'"  width="25"/>';
            $_html  .= '</div><div class="tit">';
            $_html  .= $userInfo['name'];
            $_html  .= '</div>';

            $_tblhtml  = '<div class="avt"><img src="'.$userInfo['pic'].'"  width="25"/></div>';
            $_tblhtml  .= '<div class="tit">'.$userInfo['name'].'</div>';


            $msg_key    = uniqid();
            $msg_key    .= get_rand_str(4);
            $valArr     = ['tbid'=>$tbbid,'posid'=>$positionid,'tblthtml'=>$_tblhtml,'poshtml'=>$_html,'act'=>'sitdown'];
            $expTime    = 60;
            set_key_cache($msg_key,$valArr,$expTime);

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '加入成功',
                '_msgkey'   => $msg_key,
                '_html'     => $_html
            ];
            $this->return_json($r_array);
        }

        public function goout()
        {

            $ulistRes   = D('wzulist')->where('userid = '.$this->uid)->find();
            $tbid       = $ulistRes->tableid;
            $posid      = $ulistRes->tableposid;
            if($tbid == 0 || $tbid > 20){
                $r_array	= [
                    '_state'	=> 'error4',
                    '_msg'		=> '不要瞎操作'
                ];
                $this->return_json($r_array);
            }

            $nowtime    = time();
            $upUsArr['tableid']     = 0;
            $upUsArr['state']       = 1;
            $upUsArr['lastuptime']  = $nowtime;
            if($posid > 0)
            {//只有之前坐在桌子上的才需要更新桌子信息
                $upUsArr['tableposid']  = 0;

                $tabRes     = D('wztable')->where('id = '.$tbid)->find();
                if($tabRes->state == 1){
                    $r_array	= [
                        '_state'	=> 'error4',
                        '_msg'		=> '游戏正在进行中,不能退出'
                    ];
                    $this->return_json($r_array);
                }

                $upTabArr['state']  = 2;
                if($posid == 1) {
                    $upTabArr['user1id']    = 0;
                    $upTabArr['u1state']    = 0;
                    if($tabRes->user2id == 0){
                        $upTabArr['state']  = 0;
                    }
                }else{
                    $upTabArr['user2id']    = 0;
                    $upTabArr['u2state']    = 0;
                    if($tabRes->user1id == 0){
                        $upTabArr['state']  = 0;
                    }
                }

                D('wztable')->where('id='.$tabRes->id)->save($upTabArr);
            }
            D('wzulist')->where('id='.$ulistRes->id)->save($upUsArr);

            $tablist    = [];
            $tabnum     = 0;
            $tabstaArr  = [
                0 => '空位置',
                1 => '对战中',
                2 => '等待中'
            ];
            $tblemphtml   = '<div class="wzemp">空</div>';

            $tabRes     = D('wztable')->select();
            $tablist    = [];
            foreach($tabRes as $k=>$v)
            {
                $html1   = '<div class="wzemp">空</div>';
                $html2   = '<div class="wzemp">空</div>';
                if($v->user1id > 0)
                {
                    $userinfo   = get_uinfo_cache($v->user1id);
                    $html1  = '<div class="avt"><img src="'.$userinfo['pic'].'"  width="25"/></div>';
                    $html1  .= '<div class="tit">'.$userinfo['name'].'</div>';
                }

                if($v->user2id > 0)
                {
                    $userinfo   = get_uinfo_cache($v->user2id);
                    $html2  = '<div class="avt"><img src="'.$userinfo['pic'].'"  width="25"/></div>';
                    $html2  .= '<div class="tit">'.$userinfo['name'].'</div>';
                }

                $tablist[$v->id]  = [
                    'id'    => $v->id,
                    'state' => $tabstaArr[$v->state],//0空位置，1对站中，2准备中
                    'user1' => $html1,
                    'user2' => $html2,
                ];

                $tabnum++;
            }

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '退出成功',
                '_msgkey'   => '',
                '_count'    => $tabnum,
                '_tbinfo'   => $tablist
            ];

            if($posid >0){//有会下的退出才更新各自的table
                $posemphtml   = '<div class="wzemp" onclick="xt_wuzi.sitdown(this)" poid="'.$posid.'">坐下</div>';
                $msg_key    = uniqid();
                $msg_key    .= get_rand_str(4);
                $valArr     = ['tbid'=>$tbid,'posid'=>$posid,'tblthtml'=>$tblemphtml,'poshtml'=>$posemphtml,'act'=>'goout'];
                $expTime    = 60;
                set_key_cache($msg_key,$valArr,$expTime);
                $r_array['_msgkey'] = $msg_key;
            }

            $this->return_json($r_array);
        }

        public function sendtxt()
        {
            $_sdtxt = $_POST['sendtxt'];

            $ulistRes   = D('wzulist')->where('userid = '.$this->uid)->find();
            if($ulistRes == false)
            {//正常不会有这种情况
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常',
                ];
                $this->return_json($r_array);
            }

            $tbbid      = $ulistRes->tableid;
            $uinfo	    = get_uinfo_cache($this->uid);

            $html       = '<div class="wzc_blk"><div class="wzcb_con">';
            $html       .= '<div class="wzcbc_name">'.$uinfo['name'].':</div>';
            $html       .= '<div class="wzcbc_con">'.$_sdtxt.'</div>';
            $html       .= '</div></div>';

            $wzCtArr['formuid'] = $this->uid;
            $wzCtArr['totableid'] = $tbbid;
            $wzCtArr['chattxt'] = $_sdtxt;
            $wzCtArr['addtime'] = time();
            D('wzchat')->add($wzCtArr);

            $msg_key    = uniqid();
            $msg_key    .= get_rand_str(4);
            $valArr     = ['tbid'=>$tbbid,'chathtml'=>$html,'act'=>'wzchat'];
            $expTime    = 60;
            set_key_cache($msg_key,$valArr,$expTime);

            $r_array	= [
                '_state'	=> 'ok',
                '_msgkey'   => $msg_key,
                '_msg'		=> '发送成功',
            ];
            $this->return_json($r_array);

        }

        public function startgm()
        {//这块的逻辑放在这边还是服务端...还是放在这端吧,服务端尽量只转发消息就好
            $uid    = $this->uid;
            $res    = D('wzulist')->where('userid = '.$uid)->find();
            if($res->tableid == 0 || $res->tableposid == 0)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常',
                ];
                $this->return_json($r_array);
            }

            $tabInfo    = D('wztable')->where('id = '.$res->tableid)->find();
            if($res->tableposid == 1 && $tabInfo->user1id == 0)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常',
                ];
                $this->return_json($r_array);
            }

            if($res->tableposid == 2 && $tabInfo->user2id == 0)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常',
                ];
                $this->return_json($r_array);
            }


            $isStart    = 0;
            if($res->tableposid == 1)
            {
                if($tabInfo->u1state ==1){
                    $r_array	= [
                        '_state'	=> 'error',
                        '_msg'		=> '不要重复提交',
                    ];
                    $this->return_json($r_array);
                }

                $upTabArr['u1state']    = 1;

                if($tabInfo->u2state == 1){
                    $upTabArr['state']  = 1;
                    $upTabArr['nowposid']  = 2;
                    $upTabArr['u1qz']  = 1;
                    $upTabArr['u2qz']  = 2;
                    $isStart            = 1;
                }

            }

            if($res->tableposid == 2)
            {
                if($tabInfo->u2state ==1){
                    $r_array	= [
                        '_state'	=> 'error',
                        '_msg'		=> '不要重复提交',
                    ];
                    $this->return_json($r_array);
                }

                $upTabArr['u2state']    = 1;
                if($tabInfo->u1state == 1){
                    $upTabArr['state']  = 1;
                    $upTabArr['nowposid']  = 1;
                    $upTabArr['u1qz']  = 2;
                    $upTabArr['u2qz']  = 1;
                    $isStart            = 1;
                }
            }

            $msghtml    = '';
            if($isStart == 1)
            {//创建游戏棋盘
                $nowtime            = time();
                $upTmArr['tableid'] = $tabInfo->id;
                $upTmArr['user1id'] = $tabInfo->user1id;
                $upTmArr['user2id'] = $tabInfo->user2id;
                $upTmArr['state']   = 1;
                $upTmArr['winposid'] = 0;
                $upTmArr['u1qz']    = $upTabArr['u1qz'];
                $upTmArr['u2qz']    = $upTabArr['u2qz'];
                $upTmArr['addtime'] = $nowtime;
                $upTmArr['uptime']  = $nowtime;

                $chessArr   = [];
                for($i=0;$i<15;$i++)
                {
                    for($y=0;$y<15;$y++)
                    {
                        $chessArr[$i][$y]   = 0;
                    }
                }

                $upTmArr['chesscon']  = serialize($chessArr);

                $timeId = D('wztimes')->add($upTmArr);
                $upTabArr['times']  = $timeId;

                $msghtml       = '<div class="wzc_blk"><div class="wzcb_con">';
                $msghtml       .= '<div class="wzcbc_name">系统:</div>';
                $msghtml       .= '<div class="wzcbc_con">游戏开始了</div>';
                $msghtml       .= '</div></div>';
            }

            $msg_key    = uniqid();
            $msg_key    .= get_rand_str(4);
            $valArr     = ['tbid'=>$tabInfo->id,'chathtml'=>$msghtml,'isstart'=>$isStart,'lasttime'=>$nowtime,'act'=>'gmstart'];
            $expTime    = 60;
            set_key_cache($msg_key,$valArr,$expTime);

            D('wztable')->where('id = '.$tabInfo->id)->save($upTabArr);

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '创建成功',
                '_msgkey'   => $msg_key
            ];
            $this->return_json($r_array);
        }

        //下棋的时候
        public function palychess()
        {
            $dx     = intval($_POST['dx']);
            $dy     = intval($_POST['dy']);
            if($dx >= 15 || $dy >= 15)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常'
                ];
                $this->return_json($r_array);
            }

            $uid    = $this->uid;
            $res    = D('wzulist')->where('userid = '.$uid)->find();
            if($res->tableid == 0 || $res->tableposid == 0)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据异常',
                ];
                $this->return_json($r_array);
            }

            $tabInfo    = D('wztable')->where('id = '.$res->tableid)->find();

            if($res->tableposid == $tabInfo->nowposid && $tabInfo->nowposid>0 && $tabInfo->state == 1)
            {

            }else{
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '非法提交',
                ];
                $this->return_json($r_array);
            }

            $nowtime    = time();
            $timesInfo  = D('wztimes')->where('id='.$tabInfo->times)->find();
            $chessArr   = unserialize($timesInfo->chesscon);

            if($chessArr[$dy][$dx] != 0)
            {
                $r_array	= [
                    '_state'	=> 'error',
                    '_msg'		=> '数据非法提交',
                ];
                $this->return_json($r_array);
            }

            $chYsArr      = [
                1   => 'w2pic',
                2   => 'b2pic'
            ];
            $chYs       = '';
            if($res->tableposid == 1)
            {
                $chYs   = $chYsArr[$tabInfo->u1qz];
                $chessArr[$dy][$dx] = $tabInfo->u1qz;
                $nowqx  = $tabInfo->u1qz;
            }else{
                $chYs   = $chYsArr[$tabInfo->u2qz];
                $chessArr[$dy][$dx] = $tabInfo->u2qz;
                $nowqx  = $tabInfo->u2qz;
            }

            $iswin  = WuziqService::clawin($chessArr,$nowqx);
            if($iswin == 1)
            {
                $upTimeArr['winposid']  = $res->tableposid;
                $upTimeArr['state']     = 2;

                //赢得比赛后，还原用户所在桌子的状态，两个对应用户的状态
                $upTabArr['u1state']    = 0;
                $upTabArr['u2state']    = 0;
                $upTabArr['state']      = 2;

            }
            $upTimeArr['lastch']    = $dy.'_'.$dx;
            $upTimeArr['uptime']    = $nowtime;
            $upTimeArr['chesscon']  = serialize($chessArr);
            D('wztimes')->where('id='.$timesInfo->id)->save($upTimeArr);

            $upTabArr['nowposid']   = $tabInfo->nowposid == 1?2:1;
            D('wztable')->where('id='.$tabInfo->id)->save($upTabArr);//更新位置下棋者为对手位置

            $msg_key    = uniqid();
            $msg_key    .= get_rand_str(4);
            $valArr     = ['tbid'=>$tabInfo->id,'ysstr'=>$chYs,'dx'=>$dx,'dy'=>$dy,'iswin'=>$iswin,'actuid'=>$uid,'lasttime'=>$nowtime,'act'=>'playchess'];
            $expTime    = 60;
            set_key_cache($msg_key,$valArr,$expTime);

            $r_array	= [
                '_state'	=> 'ok',
                '_msg'		=> '成功',
                '_ysstr'    => $chYs,
                '_msgkey'   => $msg_key
            ];
            $this->return_json($r_array);

        }

        public function css()
        {
            $tmRes  = D('wztimes')->where('id=9')->find();

            $chessArr   = unserialize($tmRes->chesscon);

            WuziqService::clawin($chessArr,1);
            WuziqService::clawin($chessArr,2);
        }
}
