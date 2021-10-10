<?php
if(!defined('ML')){die();}

class WuziqService
{
    //加入或者退出桌面list
    public static function uptblistuser($uid,$tabid =0)
    {
        $res    = D('wzulist')->where('userid = '.$uid)->find();
        $newtime = time();
        if($res)
        {
            if($tabid == 0)
            {//后期这里再调整下，现在只要刷新页面就更新，后期改为刷新页面前先判断状态，不一致时再更新
                $ulistArr['state']      = 1;
                $ulistArr['tableid']    = 0;
                $ulistArr['tableposid'] = 0;
                $ulistArr['lastuptime'] = $newtime;

                //按道理这里本来不用更新，但每次刷新页面，数据库的数据已经变了，但前台还是没有变，说明swoole的更新速度慢于前端，那就在这里也更新下吧
                if($res->tableid > 0 && $res->tableposid > 0)
                {//如果已经在桌子上了，不刷新数据，用户的异常退出统一交由后端的超时处理
                    return;
                    /***
                    $tbid       = $res->tableid;
                    $posid      = $res->tableposid;

                    $tabRes                 = D('wztable')->where('id = '.$tbid)->find();
                    $upTabArr['state']      = 2;
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
                     * ***/

                }

            }else{
                $ulistArr['state']      = 2;
                $ulistArr['tableid']    = $tabid;
                $ulistArr['lastuptime'] = $newtime;
            }

            D('wzulist')->where('id='.$res->id)->save($ulistArr);

        }else{
            $ulistArr['userid']     = $uid;
            $ulistArr['state']      = 1;
            $ulistArr['tableid']    = 0;
            $ulistArr['tableposid'] = 0;
            $ulistArr['addtime']    = $newtime;
            $ulistArr['lastuptime'] = $newtime;

            D('wzulist')->add($ulistArr);
        }
    }

    public static function clawin($chessArr,$posid)
    {
        $selArr = [];
        foreach($chessArr as $k=>$v)
        {
            foreach($v as $kk=>$vv)
            {
                if($vv == $posid)
                {
                    $selArr[$k][$kk]    = $posid;
                }
            }
        }

        $iswin  = 0;
        foreach($selArr as $k=>$v)
        {
            foreach($v as $kk=>$vv)
            {
                if(isset($selArr[$k][$kk+1]) && isset($selArr[$k][$kk+2]) && isset($selArr[$k][$kk+3]) && isset($selArr[$k][$kk+4]))
                {
                    $iswin =1;
                }

                if(isset($selArr[$k+1][$kk]) && isset($selArr[$k+2][$kk]) && isset($selArr[$k+3][$kk]) && isset($selArr[$k+4][$kk]))
                {
                    $iswin =1;
                }

                if($k>=4)
                {
                    if(isset($selArr[$k-1][$kk+1]) && isset($selArr[$k-2][$kk+2]) && isset($selArr[$k-3][$kk+3]) && isset($selArr[$k-4][$kk+4]))
                    {
                        $iswin =1;
                    }
                }

                if($k<=10)
                {
                    if(isset($selArr[$k+1][$kk+1]) && isset($selArr[$k+2][$kk+2]) && isset($selArr[$k+3][$kk+3]) && isset($selArr[$k+4][$kk+4]))
                    {
                        $iswin =1;
                    }
                }
            }
        }

        return $iswin;

    }
}
