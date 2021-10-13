<?php

if(!defined('ML')){die();}
class DoudzClass extends HomeAction{

    protected function _load()
    {
        //vp($_SERVER);
        //待完善功能点
        /***
         *
         * 1,退出牌局 已OK
         * 2，连接断开后的流程处理
         * 3，页面刷新时数据重新显示的各种按键判断
         * 4，每次玩牌要不要带金币？已OK
         * 5，要不要出现打牌记录？以及赢取金币的记录
         * 6，赢得比赛后，要不要针对每个人显示个性化的数据？
         * 7，提示的牌面，对子优先提醒只是对子，三条优先推荐三条，三带一优先推荐三张+最小的单张，三带二优先推荐三张+最小的对子  已OK
         *
         *
         *
         */
    }

    public function index(){
        $utoken = get_utoken_cache($this->uid);
        $res = get_uinfo_cache($this->uid);
        if($utoken == false || $res == false)
        {
            exit('Error3');
        }

        $doudzser = new DoudzService();
        $usState = $doudzser->uptblistuser($this->uid);//加入桌面列表
        //这里到时做一个判断，如果用户当前正在某一个桌子上时，跳转到桌子上去
        if($usState == 1){//跳转到详情页
            $url = '/index.php/doudz/detial';
            $this->go_header($url);
        }

        //下面的流程是用户在未加入桌子时，展示桌子的整体列表
        $res['token'] = $utoken['token'];
        $tabRes = D('ddztable')->select();
        $tabUserRes = D('ddztabulist')->where('state = 1')->select();//桌子上所有有效用户信息
        $tabUfArr = [];//按tabid生成的用户桌子信息
        if($tabUserRes){
            foreach($tabUserRes as $k=>$v){
                $tabUfArr[$v->table_id][$v->tabposnum] = get_uinfo_cache($v->user_id);
            }
        }
        $tabstaArr  = [
            0 => '空位置',
            1 => '对战中',
            2 => '等待中'
        ];

        $tablist    = [];
        foreach($tabRes as $k=>$v)
        {
            $tablist[$v->id]  = [
                'id' => $v->id,
                'state' => $v->state,//0空位置，1对站中，2准备中
                'statetxt' => $tabstaArr[$v->state],//0空位置，1对站中，2准备中
                'licssstr' => $v->state == 1?'ddzing':'ddzdengdai',
                'userlist' => isset($tabUfArr[$v->id])?$tabUfArr[$v->id]:[]
            ];
        }

        $this->assign('tablist',$tablist);
        $this->assign('res',$res);
        $this->template();
    }

    public function detial()
    {
        $utoken = get_utoken_cache($this->uid);
        $res = get_uinfo_cache($this->uid);
        if($utoken == false || $res == false)
        {
            exit('Error3');
        }
        //下面的流程是用户在未加入桌子时，展示桌子的整体列表
        $res['token'] = $utoken['token'];

        $user_id    = $this->uid;
        $tabUlist = D('ddzulist')->where('user_id = '.$user_id)->find();
        if(!$tabUlist){//重定向到index里面
            $url = '/index.php/doudz';
            $this->go_header($url);
        }

        if($tabUlist->state != 2){
            $url = '/index.php/doudz';
            $this->go_header($url);
        }
        $table_id = $tabUlist->tableid;
        $tabinfo    = D('ddztable')->where('id ='.$table_id)->find();
        $ddzulist   = D('ddztabulist')->where('table_id='.$table_id.' and state=1')->order('tabposnum asc')->select();
        if($tabinfo->times > 0 && $tabinfo->stepstate > 0 && $tabinfo->stepstate < 4){
            $timeinfo   = D('ddztimes')->where('id ='.$tabinfo->times)->find();
        }

        $uinfo_arr  = [];
        $nowPs_id   = 1;
        $stepstate = $tabinfo->stepstate;
        //$stepstate = 0;//强制桌子处在最后准备阶段

        foreach($ddzulist as $K=>$v)
        {
            if($v->user_id == $user_id)
            {
                $nowPs_id = $v->tabposnum;
            }
        }

        $doudzser   = new DoudzService();
        $tabUArr    = [];
        //初始化位置信息
        $tabUArr[1] = ['tabposnum' => 1,'pknum' => 0];
        $tabUArr[2] = ['tabposnum' => 2,'pknum' => 0];
        $tabUArr[3] = ['tabposnum' => 3,'pknum' => 0];
        foreach($ddzulist as $k=>$v)
        {
            $isdistime  = 0;
            $isdisbtn   = 0;
            $isdisdz    = 0;
            $tmpuinfo   = get_uinfo_cache($v->user_id);
            if(empty($v->nowpk))
            {
                $nowuserpk = [];
                $pknum     = 0;
            }else{
                $nowuserpk = unserialize($v->nowpk);
                $pknum     = count($nowuserpk);
                $nowuserpk = $doudzser->sortpk($nowuserpk);
            }

            //强制桌子处在最后准备阶段
//            $nowuserpk = [];
//            $pknum     = 0;

            if(isset($timeinfo) && $timeinfo->nowuser_id == $v->user_id){
                $isdistime  = 1;
                $isdisbtn   = 1;
            }

            if(isset($timeinfo) && $timeinfo->dzuser_id == $v->user_id){
                $isdisdz  = 1;
            }

            $tabUArr[$v->tabposnum] = [
                'uinfo' => $tmpuinfo,
                'tabinfo' => $v,
                'isdistime' => $isdistime,
                'isdisbtn' => $isdisbtn,
                'isdisdz' => $isdisdz,
                'pkarr' => $nowuserpk,
                'pknum' => $pknum,
                'tabposnum' => $v->tabposnum
            ];
        }

        if($nowPs_id == 1){
            $uinfo_arr['nowuinfo'] = $tabUArr[1];
            $tabUArr[2]['pkarr'] = [];//可有可无
            $tabUArr[3]['pkarr'] = [];//可有可无
            $uinfo_arr['nextuinfo'] = $tabUArr[2];
            $uinfo_arr['upuinfo'] = $tabUArr[3];
        }elseif($nowPs_id == 2){
            $uinfo_arr['nowuinfo'] = $tabUArr[2];
            $tabUArr[1]['pkarr'] = [];
            $tabUArr[3]['pkarr'] = [];
            $uinfo_arr['nextuinfo'] = $tabUArr[3];
            $uinfo_arr['upuinfo'] = $tabUArr[1];
        }else{
            $uinfo_arr['nowuinfo'] = $tabUArr[3];
            $tabUArr[2]['pkarr'] = [];
            $tabUArr[1]['pkarr'] = [];
            $uinfo_arr['nextuinfo'] = $tabUArr[1];
            $uinfo_arr['upuinfo'] = $tabUArr[2];
        }

//        vp($timeinfo,2);
//        vp($uinfo_arr);
        $timepk = [];
        $multipleNum = 1;
        if(isset($timeinfo)){
            $timepk = unserialize($timeinfo->dzpoker);
            $multipleNum = $timeinfo->multiple_num;
        }

        $this->assign('multipleNum',$multipleNum);
        $this->assign('timepk',$timepk);
        $this->assign('stepstate',$stepstate);
        $this->assign('uinfoarr',$uinfo_arr);
        $this->assign('res',$res);
        $this->template();
    }

    //这个进入桌子的流程，等后续流程全部打通后，放到ser里面去，更新数据库与发送消息应该是同步进行，分两步走，如果数据库已经更新，连接又突然断开就是个问题
    public function cometab()
    {
        $tabid = intval($_POST['tabid']);
        $posid = intval($_POST['posid']);
        $userid = $this->uid;
        if($tabid == 0 || $tabid == '' || $posid == 0 || $posid == '' || $posid < 1 || $posid > 3){
            $this->rtnerror('数据异常,请联系管理员!');
        }

        $tabPosRes = D('ddztabulist')->where('table_id = '.$tabid.' and tabposnum = '.$posid.' and state = 1')->find();
        if($tabPosRes){
            $this->rtnerror('此位置已经被别人占了,请重新选个位置!');
        }

        $tabPosRes = D('ddztabulist')->where('user_id = '.$userid.' and state = 1')->find();
        if($tabPosRes){
            $this->rtnerror('你已经坐在其它位置了，不能同时坐多个位置!');
        }

        $ddzUinfo = D('ddzulist')->where('user_id = '.$userid)->find();
        if($ddzUinfo->state == 2){
            //$this->rtnerror('数据不一致，请联系管理员检查下数据!');
        }

        //生成当前用户的坐在桌子上的数据
        //更新桌子的状态(ddztable)
        $nowTime = time();
        $tabInfo = D('ddztable')->where('id = '.$tabid)->find();
        if($tabInfo->state == 0){
            $upTabArr['state'] = 2;
            D('ddztable')->where('id = '.$tabid)->save($upTabArr);
        }

        //更新用户列表数据(ddzulist)
        $upUserArr['state'] = 2;
        $upUserArr['tableid'] = $tabid;
        $upUserArr['tableposid'] = $posid;
        $upUserArr['lastuptime'] = $nowTime;
        D('ddzulist')->where('user_id = '.$userid)->save($upUserArr);

        //初始化坐桌子上的新用户 生成用户桌子信息(ddztabulist)
        $ctTabUsArr = [
            'user_id' => $userid,
            'addtime' => $nowTime,
            'uptime' => $nowTime,
            'nowpk' => '',
            'times' => 0,
            'table_id' => $tabid,
            'state' => 1,
            'startpk' => '',
            'is_dz' => 0,
            'is_readly' => 0,
            'tabposnum' => $posid
        ];
        D('ddztabulist')->add($ctTabUsArr);

        $userInfo = get_uinfo_cache($userid);

        $_tblhtml = '<div class="avt">';
        $_tblhtml .= '<img src="'.$userInfo['pic'].'"  width="25"/>';
        $_tblhtml .= '</div><div class="tit">';
        $_tblhtml .= $userInfo['name'].'</div>';

        $_poshtml = '<div class="ddl_pic">';
        $_poshtml .= '<img src="'.$userInfo['pic'].'" width="35">';
        $_poshtml .= '</div><div class="ddl_name">';
        $_poshtml .= $userInfo['name'].'</div>';

        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $valArr     = ['tbid'=>$tabid,'posid'=>$posid,'tblthtml'=>$_tblhtml,'poshtml'=>$_poshtml,'act'=>'cometab'];
        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '加入成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);
    }

    public function leavetab()
    {
        $userid = $this->uid;
        $tabUinfoRes = D('ddztabulist')->where('user_id = '.$userid.' and state = 1')->find();
        if(!$tabUinfoRes){
            $this->rtnerror('未找到合法信息!');
        }

        $ddzUinfo = D('ddzulist')->where('user_id = '.$userid)->find();
        if($ddzUinfo->state != 2){
            $this->rtnerror('数据不一致，请联系管理员检查下数据!');
        }

        if($tabUinfoRes->times > 0){
            $timesInfo = D('ddztimes')->where('id = '.$tabUinfoRes->times)->find();
            if($timesInfo->stepstate != 4){
                $this->rtnerror('游戏正在进行中，不能退出!');
            }
        }

        $nowTime = time();
        //更新tabulist
        $tabUArr = [
            'uptime' => $nowTime,
            'state' => 0,
        ];
        D('ddztabulist')->where('id = '.$tabUinfoRes->id)->save($tabUArr);
        //更新ulist
        $usArr = [
            'state' => 1,
            'tableid' => 0,
            'tableposid' => 0,
            'lastuptime' => $nowTime
        ];
        D('ddzulist')->where('user_id = '.$userid)->save($usArr);

        $tabArr = [
            'state' => 2,
            'stepstate' => 0,
        ];
        $tabUlist = D('ddztabulist')->where('tableid ='.$tabUinfoRes->tableid.' and state= 1')->find();
        if(!$tabUlist){
            $tabArr['state'] = 0;
        }

        D('ddztable')->where('id = '.$tabUinfoRes->table_id)->save($tabArr);

        $tabid = $tabUinfoRes->table_id;
        $posid = $tabUinfoRes->tabposnum;

        $_tblhtml = '<div class="dtpos" title="点击进入桌子" tabid="'.$tabid.'" posid="'.$posid.'" onclick="xt_ddz.cometab(this)">'.$posid.'号位</div>';
        $_poshtml = '<div class="ddz_empuif">空位置</div>';

        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $valArr     = ['tbid'=>$tabid,'posid'=>$posid,'tblthtml'=>$_tblhtml,'poshtml'=>$_poshtml,'act'=>'leavedet'];
        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '退出成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);

    }

    public function startgame()
    {
        $uid    = $this->uid;
        $dTabuinfo  = D('ddztabulist')->where('user_id ='.$uid.' and state =1')->find();

        if(!$dTabuinfo){
            $this->rtnerror('数据异常,请联系管理员1!');
        }

        if($dTabuinfo->is_readly == 1){
            $this->rtnerror('数据异常,请联系管理员2!');
        }

        $dTabinfo   = D('ddztable')->where('id='.$dTabuinfo->table_id)->find();
        if($dTabinfo->state != 2){
            $this->rtnerror('数据异常,请联系管理员3!');
        }

        if($dTabinfo->stepstate != 0){
            $this->rtnerror('数据异常,请联系管理员4!');
        }

        $nowTime    = time();

        $upTuArr    = [
            'is_readly' => 1,
            'uptime' => $nowTime
        ];

        D('ddztabulist')->where('id='.$dTabuinfo->id)->save($upTuArr);

        $tbus   = D('ddztabulist')->where('table_id='.$dTabuinfo->table_id.' and state = 1')->order('id asc')->select();
        $isAllRd = 1;
        $tbusNum = 0;
        $useridArr = [];
        foreach($tbus as $k=>$v){
            if($v->is_readly == 0){
                $isAllRd = 0;
            }

            $useridArr[]    = $v->user_id;
            $tbusNum++;
        }
//echo $isAllRd.'-'.$tbusNum;exit;
        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $btnHtml    = '<button type="button" class="layui-btn layui-btn-radius layui-btn-sm layui-btn-disabled">已准备</button>';
        $rdHtml     = '<h1 class="layui-font-orange" style="font-weight:bold;display: inline-block">已就绪</h1>';
        $valArr     = ['tbid'=>$dTabuinfo->table_id,'posid'=>$dTabuinfo->tabposnum,'btnhtml'=>$btnHtml,'rdhtml'=>$rdHtml,'gamestart'=>0,'act'=>'startgame'];
        if($isAllRd == 1 && $tbusNum == 3){
            //发poker
            $doudzser   = new DoudzService();
            //根据次新的打牌记录生成新的PK
            $createpkArr = $doudzser->byrecordcreatepk();
            if(empty($createpkArr)){
                //如果没有打牌记录就随机生成
                $pokerArr   = $doudzser->pokerList;
                $rtn    = $doudzser->getuserpk($pokerArr,17);
                $pokerArr   = $rtn['pkarr'];
                $userpk[]    = $rtn['userpk'];
                $rtn    = $doudzser->getuserpk($pokerArr,17);
                $pokerArr   = $rtn['pkarr'];
                $userpk[]    = $rtn['userpk'];
                $rtn    = $doudzser->getuserpk($pokerArr,17);
                $pokerArr   = $rtn['pkarr'];
                $userpk[]    = $rtn['userpk'];
            }else{
                $userpk = $createpkArr['userpk'];
                $pokerArr = $createpkArr['othpk'];
            }

            $lastTabNum = $dTabinfo->lastdef_num ==0?1:($dTabinfo->lastdef_num ==1?2:0);

            $ddzTm  = [
                'tableid' => $dTabuinfo->table_id,
                'state' => 1,
                'addtime' => $nowTime,
                'uptime' => $nowTime,
                'stepstate' => 1,
                'nowuser_id' => $useridArr[$lastTabNum],
                'startuser_id' => $useridArr[$lastTabNum],
                'dzpoker' => serialize($pokerArr),
                'rob_num' => 0,
                'base_coins' => 100,
                'multiple_num' => 1
            ];

            $dMod = D('ddztimes');
            $timesid = $dMod->add($ddzTm);

            //更新tabuser
            foreach($tbus as $k=>$v){

                $tmpserpk   = serialize($userpk[$k]);
                $tmpUsDt = [
                    'uptime' => $nowTime,
                    'nowpk' => $tmpserpk,
                    'times' => $timesid,
                    'startpk' => $tmpserpk,
                ];

                D('ddztabulist')->where('id = '.$v->id)->save($tmpUsDt);
            }

            //更新table
            $tabDt = [
                'times' => $timesid,
                'state' => 1,
                'nowuser_id' => $useridArr[$lastTabNum],
                'lastdef_num' => $lastTabNum,
                'stepstate' => 1
            ];

            D('ddztable')->where('id = '.$dTabinfo->id)->save($tabDt);

            $valArr['gamestart'] = 1;
        }

        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '操作成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);
    }

    //抢地主
    public function roblandlord(){

        $uid = $this->uid;
        $robval = intval($_POST['robval'])>0?intval($_POST['robval']):0;
        if($robval == 0){
            $this->rtnerror('数据异常,请联系管理员!');
        }
        $robval = $robval == 1?1:2;
        $dTabuinfo  = D('ddztabulist')->where('user_id ='.$uid.' and state =1')->find();
        if(!$dTabuinfo){
            $this->rtnerror('数据异常,请联系管理员1!');
        }

        if($dTabuinfo->is_roblld == 2){
            $this->rtnerror('数据异常,请联系管理员5!');
        }

        if($dTabuinfo->is_readly != 1){
            $this->rtnerror('数据异常,请联系管理员2!');
        }

        $dTimeinfo = D('ddztimes')->where('id = '.$dTabuinfo->times)->find();
        if($dTimeinfo->rob_num > 3 || $dTimeinfo->stepstate != 1){
            $this->rtnerror('数据异常,请联系管理员3!');
        }

        if($dTimeinfo->nowuser_id != $uid){
            $this->rtnerror('数据异常,请联系管理员4!');
        }

        $ddzulist = D('ddztabulist')->where('table_id='.$dTabuinfo->table_id.' and state=1')->order('tabposnum asc')->select();
        $ddzTpArr = [];
        $nowUid = 0;//确认下把操作人的临时变量
        $nextuid = 0;//下把的操作人
        $isuprob = 0;//判断是否已经有人愿意抢地主了

        $duUidArr = [];//最终确定地主用户对就的数据ID
        $duUidPkArr = [];//确定对应人员的PK，抢地方成功后要用
        $tmpddzulist = [];//确认下把操作人的临时变量

        foreach($ddzulist as $k=>$v){

            $duUidArr[$v->user_id] = $v->id;
            $duUidPkArr[$v->user_id] = $v->nowpk;

            if($v->is_roblld != 2){
                $tmpddzulist[] = $v;
            }

            if($v->is_roblld == 1){
                $isuprob = $isuprob + 1;
            }
        }

        if(!empty($tmpddzulist)){
            foreach($tmpddzulist as $k=>$v){
                $ddzTpArr[] = $v->user_id;
                if($nowUid > 0 && $nextuid == 0 ){
                    $nextuid = $v->user_id;
                }

                if($v->user_id == $uid){
                    $nowUid = $uid;
                }
            }
            if($nextuid == 0){
                $nextuid = $ddzTpArr[0];
            }
        }

        $nowTime = time();
        //更新times数据
        $upTimArr = [
            'rob_num' => $dTimeinfo->rob_num + 1,
            'nowuser_id' => $nextuid,
            'uptime' => $nowTime
        ];
        if($robval == 1){//抢地主的话
            $upTimArr['robuser_id'] = $uid;
            $upTimArr['multiple_num'] = $dTimeinfo->multiple_num * 2;
        }

        //更新tab数据
        $upTabArr = [
            'nowuser_id' => $nextuid,
        ];

        //更新tabulist数据
        $upTabUsArr = [
            'uptime' => $nowTime,
            'is_roblld' => $robval
        ];


        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $valArr     = ['tbid'=>$dTabuinfo->table_id,'posid'=>$dTabuinfo->tabposnum,'robval'=>$robval,'nextuser_id'=>$nextuid,'hasrob'=>0,'act'=>'roblandlord'];

        $robuid = 0;
        //已经抢到第四次时 或者 三人已经都抢过了没有一个人愿意做地主 或者 三个人已经都抢过了只有一个人愿意做地主
//        echo $dTimeinfo->rob_num.'--'.$isuprob.'--'.$robval;exit;
        if($dTimeinfo->rob_num == 3 || ($dTimeinfo->rob_num == 2 and (($isuprob == 0 and $robval == 2) || ($isuprob == 1 and $robval == 2) || ($isuprob == 0 and $robval == 1)))){
//            vp($dTimeinfo,2);
            if($robval == 1){
                $robuid = $uid;
            }else{
                if($dTimeinfo->robuser_id >0){
                    $robuid = $dTimeinfo->robuser_id;
                }else{
                    $robuid = $dTimeinfo->startuser_id;
                }
            }

            $valArr['hasrob'] = 1;//地主已经确认

            $upTimArr['dzuser_id'] = $robuid;
            $upTimArr['stepstate'] = 2;//进入加倍环节
            $upTimArr['nowuser_id'] = $robuid; //下一轮地方出牌

            $upTabArr['stepstate'] = 2;
            $upTabArr['nowuser_id'] = $robuid;//下一轮地方出牌

            $nowpk = unserialize($duUidPkArr[$robuid]);
            $dzpoker = unserialize($dTimeinfo->dzpoker);
            $nowpk = array_merge($nowpk,$dzpoker);
            $nowpk = serialize($nowpk);

            $upTuArr = [
                'uptime' => $nowTime,
                'nowpk' => $nowpk,
                'is_dz' => 1
            ];

            $dzupid = $duUidArr[$robuid];
//            echo $dzupid;
//vp($upTuArr);
            D('ddztabulist')->where('id='.$dzupid)->save($upTuArr);
        }
//vp($upTimArr);

        //更新当前操作人数据
        D('ddztabulist')->where('id='.$dTabuinfo->id)->save($upTabUsArr);

        D('ddztimes')->where('id='.$dTimeinfo->id)->save($upTimArr);
        //更新table表
        D('ddztable')->where('id='.$dTabuinfo->table_id)->save($upTabArr);

        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '操作成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);
    }

    public function adddouble()
    {
        $uid = $this->uid;
        $douval = intval($_POST['douval'])>0?intval($_POST['douval']):0;
        if($douval == 0){
            $this->rtnerror('数据异常,请联系管理员!');
        }
        $douval = $douval == 1?1:2;
        $dTabuinfo  = D('ddztabulist')->where('user_id ='.$uid.' and state =1')->find();
        if(!$dTabuinfo){
            $this->rtnerror('数据异常,请联系管理员1!');
        }

        if($dTabuinfo->is_double > 0){
            $this->rtnerror('数据异常,请联系管理员5!');
        }

        if($dTabuinfo->is_readly != 1){
            $this->rtnerror('数据异常,请联系管理员2!');
        }

        $dTimeinfo = D('ddztimes')->where('id = '.$dTabuinfo->times)->find();
        if($dTimeinfo->double_num > 2 || $dTimeinfo->stepstate != 2){
            $this->rtnerror('数据异常,请联系管理员3!');
        }

        $nowTime = time();
        //更新tabulist表
        $upTabUsArr = [
            'is_double' => $douval,
            'uptime' => $nowTime
        ];
        D('ddztabulist')->where('id = '.$dTabuinfo->id)->save($upTabUsArr);

        //更新times表
        $upTimArr = [
            'double_num' => $dTimeinfo->double_num + 1,
            'uptime' => $nowTime
        ];
        $multiple_num = $dTimeinfo->multiple_num;
        $isEndDou = 0;//加倍结束
        if($dTimeinfo->double_num == 2){
            $isEndDou = 1;
            $upTimArr['stepstate'] = 3;
            //更新tab表
            $upTabArr['stepstate'] = 3;
            D('ddztable')->where('id='.$dTabuinfo->table_id)->save($upTabArr);
        }
        if($douval == 1){
            $upTimArr['multiple_num'] = $dTimeinfo->multiple_num * 2;
            $multiple_num = $upTimArr['multiple_num'];
        }
        D('ddztimes')->where('id='.$dTimeinfo->id)->save($upTimArr);

        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $valArr     = ['tbid'=>$dTabuinfo->table_id,'posid'=>$dTabuinfo->tabposnum,'isenddou'=>$isEndDou,'douval'=>$douval,'multiple_num'=>$multiple_num,'act'=>'adddouble'];

        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '操作成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);

    }

    public function recvpkv()
    {
        $pkval = $_POST['pkval'];
        $uid = $this->uid;
        $dTabuinfo  = D('ddztabulist')->where('user_id ='.$uid.' and state =1')->find();
        if(!$dTabuinfo){
            $this->rtnerror('数据异常,请联系管理员1!');
        }

        $dTimeinfo = D('ddztimes')->where('id = '.$dTabuinfo->times)->find();
        if($dTimeinfo->nowuser_id != $uid){
            $this->rtnerror('数据异常,请联系管理员2!');
        }

        if($dTimeinfo->stepstate != 3){
            $this->rtnerror('数据异常,请联系管理员3!');
        }

        $nowTime = time();
        $nowSendNum = $dTimeinfo->sendpk_num + 1;//当前是第几把

        $ddzulist = D('ddztabulist')->where('table_id='.$dTabuinfo->table_id.' and state=1')->order('tabposnum asc')->select();
        $ddzTpArr = [];
        $nowUid = 0;//确认下把操作人的临时变量
        $nextuid = 0;//下把的操作人

        foreach($ddzulist as $k=>$v){
            $ddzTpArr[] = $v->user_id;
            if($nowUid > 0 && $nextuid == 0 ){
                $nextuid = $v->user_id;
            }

            if($v->user_id == $uid){
                $nowUid = $uid;
            }
        }
        if($nextuid == 0){
            $nextuid = $ddzTpArr[0];
        }

        $userPkArr = unserialize($dTabuinfo->nowpk);
        //$userPkArr2 = unserialize($dTabuinfo->starpk);
        if($pkval == 'notsend'){//如果用户不要时

        }else{
            //判断牌逻辑
            $isNopk = 0;//判断前台输入的PK是不是当前用户的合法pk
            foreach($pkval as $k=>$v){
                if(!in_array($v,$userPkArr)){
                    $isNopk = 1;
                }
            }

            if($isNopk == 1){
                $this->rtnerror('提交的数据有误,请联系管理员2!');
            }

            $doudzser   = new DoudzService();
            $pktype = $doudzser->rtnpktp($pkval);
            if($pktype['type'] == 0){
                $this->rtnerror($pktype['intro']);
            }
        }

        if($dTimeinfo->sendpk_num == 0 || $dTimeinfo->lastsduser_id == $dTimeinfo->nowuser_id){
            //第一次出牌时 或者 别人不要牌时，又轮到自己出牌时 这种情况不用对比牌的大小
            if($pkval == 'notsend'){
                $this->rtnerror('第一次出牌/别人都不要时,必须出牌，不能不要');
            }
        }else{
            if($pkval == 'notsend'){

            }else{
                //比牌型与牌的大小
                $lastsdpk = unserialize($dTimeinfo->lastsendpk);

                $pkres = $doudzser->pokerarrpk($lastsdpk,$pkval);
                if($pkres != 2){
                    $this->rtnerror('当前牌面小于前用户，出牌不合法，出牌失败');
                }
            }
        }

        $upTabuArr = [
            'uptime' => $nowTime,
        ];

        if($pkval == 'notsend'){

        }else{
            //打出的牌更新到times里面去
            foreach($userPkArr as $k=>$v){
                if(in_array($v,$pkval)){
                    unset($userPkArr[$k]);
                }
            }

            $upTabuArr['nowpk'] = serialize($userPkArr);
        }


        $iswin = 0;
        if(empty($userPkArr)){
            //赢得了比赛
            $iswin = 1;
        }

        if($nowSendNum == 1){
            $sendRecArr[$nowSendNum] = [
                'user_id' => $uid,
                'sendpk' => $pkval
            ];
        }else{
            $sendRecArr = unserialize($dTimeinfo->send_record);
            $sendRecArr[$nowSendNum] = [
                'user_id' => $uid,
                'sendpk' => $pkval
            ];
        }
//        vp($upTabuArr,2);
        D('ddztabulist')->where('id = '.$dTabuinfo->id)->save($upTabuArr);

        $multiple_num = $dTimeinfo->multiple_num;
        if($pktype['type'] == 8 || $pktype['type'] == 4){
            $multiple_num = $multiple_num * 2;
        }

        $upTimesArr = [
            'uptime' => $nowTime,
            'lastactuser_id' => $uid,
            'send_record' =>  serialize($sendRecArr),
            'nowuser_id' => $nextuid,//下轮用户的ID
            'sendpk_num' => $nowSendNum,
            'multiple_num' => $multiple_num,
        ];

        if($pkval != 'notsend'){
            $upTimesArr['lastsduser_id'] = $uid;
            $upTimesArr['lastsendpk'] = serialize($pkval);
        }

        if($iswin == 1){
            $upTimesArr['winuser_id'] = $uid;
            $upTimesArr['stepstate'] = 4;

            //以后有时间了再在这里补赢了比赛后的结算逻辑
            $doudzser->clatimes($ddzulist,$dTimeinfo,$dTabuinfo,$uid);
            $upTimesArr['is_cla'] = 1;

            $resetTabuArr = [
                'state' => 0,
                'uptime' => $nowTime,
            ];
            //更新用户所在桌子信息
            D('ddztabulist')->where('table_id='.$dTabuinfo->table_id.' and state=1')->save($resetTabuArr);

            //更新桌子信息
            $resetTabArr = [
                'state' => 2,
                'nowuser_id' => 0,
                'stepstate' => 0
            ];
            D('ddztable')->where('id ='.$dTabuinfo->table_id)->save($resetTabArr);

            //生成新的用户桌子信息
            foreach($ddzulist as $k=>$v){
                $tmpValArr = [
                    'user_id' => $v->user_id,
                    'addtime' => $nowTime,
                    'uptime' => $nowTime,
                    'nowpk' => '',
                    'times' => 0,
                    'table_id' => $v->table_id,
                    'state' => 1,
                    'startpk' => '',
                    'is_dz' => 0,
                    'is_readly' => 0,
                    'tabposnum' => $v->tabposnum,
                    'is_roblld' => 0,
                    'is_double' => 0,
                ];

                D('ddztabulist')->add($tmpValArr);
            }
        }
//vp($upTimesArr);
        D('ddztimes')->where('id = '.$dTimeinfo->id)->save($upTimesArr);

        $msg_key    = uniqid();
        $msg_key    .= get_rand_str(4);
        $valArr     = ['tbid'=>$dTabuinfo->table_id,'posid'=>$dTabuinfo->tabposnum,'times'=>$dTabuinfo->times,'pkval'=>$pkval,'iswin'=>$iswin,'act'=>'sendpk'];

        $expTime    = 60;
        set_key_cache($msg_key,$valArr,$expTime);

        $r_array	= [
            '_msg' => '操作成功',
            '_msgkey' => $msg_key
        ];
        $this->rtnsuc($r_array);
    }


    public function gettypepk()
    {
        $uid = $this->uid;
        $res = D('ddztabulist')->where('user_id = '.$uid.' and state =1')->find();
        $doudzser = new DoudzService();
        $respk = unserialize($res->nowpk);

        $nowRs = D('ddztimes')->where('id = '.$res->times)->find();
        if(empty($nowRs->lastsendpk)){
            $this->rtnerror('还未有出牌记录,你可以任意出一个合法的牌!');
        }

        $nowpk = unserialize($nowRs->lastsendpk);
        //$nowpk = ['s9','d9','s3','d3','h3'];
        $rtnArr = $doudzser->rtnbigpk($nowpk,$respk);
        //vp($rtnArr);

        $r_array	= [
            '_msg' => '操作成功',
            '_data' => $rtnArr
        ];
        $this->rtnsuc($r_array);

    }

    public function pklist()
    {

        return;
        $res = D('ddztabulist')->where('times = 42')->select();
        $arr = [];
        foreach($res as $k=>$v){
//            echo $v->user_id.'<br>';
            $tmpv = unserialize($v->nowpk);
            if(!empty($tmpv)){
                $arr = array_merge($arr,$tmpv);
            }
        }

//        vp($arr,2);

        $res = D('ddztimes')->where('id = 42')->find();
        $pk = unserialize($res->send_record);
//vp($pk,2);
        foreach($pk as $k=>$v){
            if(is_array($v['sendpk'])){
                $arr = array_merge($arr,$v['sendpk']);
            }
        }

        vp($arr,2);

        $upk = [];
        foreach($arr as $k=>$v){

            if($k < 5 || ($k > 14 and $k < 20) || ($k > 29 and $k < 37)){
                $upk['one'][] = $v;
            }else if(($k > 4 and $k < 9) || ($k > 19 and $k < 25) || ($k > 28 and $k < 44)){
                $upk['two'][] = $v;
            }else if($k < 51){
                $upk['three'][] = $v;
            }else{
                $upk['sy'][] = $v;
            }

        }
        $doudzser   = new DoudzService();

        foreach($upk as $k=>$v){
            $v = $doudzser->sortpk($v);
            foreach($v as $kk=>$vv){
                echo '<img src="/images/pk/'.$vv.'.jpg" width="80">';
            }
            echo '<br><br>';
        }



        return ;
        $doudzser   = new DoudzService();
        //$rtn = $doudzser->rtnpktp();
        //vp($rtn);

        $doudzser->pklist();
    }






}