<?php

if(!defined('CHT')){die();}
class DoudzService
{
    public $pokerVal,$pokerList;

    public function __construct()
    {
        $pkArr  = [];
        $pkVal  = [];
        $pkHs   = ['c','d','h','s','w'];
        foreach($pkHs as $k=>$v){
            for($i = 1; $i < 14; $i++){
                if($v == 'w'){
                    if($i < 3){
                        $pkArr[]    = $v.$i;
                    }
                }else{
                    $pkArr[]    = $v.$i;
                }
            }
        }

        foreach($pkArr as $k=>$v){
            $tmpQz  = substr($v,0,1);
            $tmpVl  = substr($v,1);
            if($tmpQz == 'w'){
                if($tmpVl == 1){
                    $pkVal[$v]  = 18;
                }else{
                    $pkVal[$v]  = 19;
                }
            }else{
                if($tmpVl == 1){
                    $pkVal[$v]  = 14;
                }elseif($tmpVl == 2){
                    $pkVal[$v]  = 16;
                }else{
                    $pkVal[$v]  = $tmpVl;
                }
            }
        }

        $this->pokerList    = $pkArr;
        $this->pokerVal     = $pkVal;
    }

    //扑克排序
    public function sortpk($arr)
    {
        $len    = count($arr);
        if($len == 1 || $len == 0){
            return $arr;
        }

        $newArr = [];
        foreach($arr as $k=>$v){
            $newArr[] = $v;
        }
        $arr = $newArr;
        for($i = 0; $i < $len; $i++) {

            for($j = $i+1; $j < $len; $j++) {
                $tmpival  = $this->pokerVal[$arr[$i]];
                $tmpjval  = $this->pokerVal[$arr[$j]];

                if($tmpival < $tmpjval){
                    $tmpArrval  = $arr[$i];
                    $arr[$i]    = $arr[$j];
                    $arr[$j]    = $tmpArrval;
                }elseif($tmpival == $tmpjval){
                    $tmpikey  = substr($arr[$i],0,1);
                    $tmpjkey  = substr($arr[$j],0,1);

                    if($tmpjkey < $tmpjkey){ //强制字符串大小对比 -_-
                        $tmpArrval  = $arr[$i];
                        $arr[$i]    = $arr[$j];
                        $arr[$j]    = $tmpArrval;
                    }
                }
            }
        }

        return $arr;
    }

    //随机返回$pkarr里面$len张牌，并把返回的pk从pkarr里面路剔出
    public function getuserpk($pkArr,$len)
    {
        $min = array_rand($pkArr,$len);
        $onePk  = [];
        foreach($min as $k=>$v)
        {
            //echo $pkArr[$v].'<br>';
            $onePk[]    = $pkArr[$v];
            unset($pkArr[$v]);
        }

        $rtn['userpk']  = $onePk;
        $rtn['pkarr']   = $pkArr;

        return $rtn;
    }

    public function pklist()
    {

        //$arr = ['s2'];
//        $arr = ['h2','s2'];
//        $arr = ['w2','w1'];

//        $arr = ['h2','d2','s2'];
//        $arr = ['h2','d2','s2','c1'];$arr = ['h2','d2','s2','c2'];
//        $arr = ['h2','d2','s2','c9','d9'];$arr = ['h11','d12','s13','c9','d10'];
        //六张时
        //$arr = ['h13','d12','s11','c1','d10','s9']; 顺子
        //$arr = ['h13','d13','s13','c1','d1','s1'];//不带牌的飞机
        //$arr = ['h13','d13','s12','c11','d12','s11'];//连对
        //$arr = ['h2','d2','s2','c2','d11','s11'];//四带二

        //七张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1'];//七张只能是连子

        //八张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7'];//八张顺子
        //$arr = ['h13','d13','s11','c10','d1','s1','c1','c13'];//八张飞机

        //九张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7','d6'];//九张顺子
        //$arr = ['h13','d13','s13','c1','d1','s1','c12','d12','s12'];//九张时，不带牌的飞机

        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7','d6','d5'];
        //$arr = ['h13','d13','s13','c1','d1','s1','s12','c6','d12','s6'];//十张带一对的飞机

        $pk1Arr = ['h13','d12','s11','c10','d9','s8','c7'];
        $pk2Arr = ['h1','d1','s1','c1'];

        $pk1Rel = $this->rtnpktp($pk1Arr);
        $pk2Rel = $this->rtnpktp($pk2Arr);

        if($pk1Rel['type'] == 0){
            exit('玩家1的牌无规则,系统退出');
        }

        if($pk2Rel['type'] == 0){
            exit('玩家2的牌无规则,系统退出');
        }

        $res = $this->pokerpk($pk1Rel,$pk2Rel);
        vp($pk1Rel,2);
        vp($pk2Rel,2);
        if($res == 2){
            echo '玩家二大于玩家一';
        }else{
            echo '玩家一大';
        }
        vp($res);



        $pkArr  = $this->pokerList;
        $min = array_rand($pkArr,8);
        $onePk  = [];
        foreach($min as $k=>$v)
        {
            //echo $pkArr[$v].'<br>';
            $onePk[]    = $pkArr[$v];
            unset($pkArr[$v]);
        }
        vp($onePk,2);

        /**
         * 返回当前牌的类型
         * 单张 1
         * 对子 2
         * 三张 不带 带1 带1对 3
         * 四张 炸弹 带2张 带2对 4
         * 顺子 最少5张 5
         * 飞机 6连飞机 带单或带双 6
         * 连对 最少6张 7
         * 王炸 8
         */

        $rtnArr = $this->getpktypebypkarr($onePk,2,2);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,3,3);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,4,4);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,5,4);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,5,3);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,5,5);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,6,6);
        vp($rtnArr,2);

        $rtnArr = $this->getpktypebypkarr($onePk,6,5);
        vp($rtnArr,2);




        $fiveArr = $this->getpktypebypkarr($onePk,4,4);
        $wzArr  = $this->getpktypebypkarr($onePk,2,8);
        vp($wzArr,2);
        vp($fiveArr);

        $onePk  = $this->sortpk($onePk);

        foreach($onePk as $k=>$v)
        {
            // echo '<img src="/images/pk/'.$v.'.jpg" width="80">';
        }

        vp($onePk,2);

        //vp($min,2);

        $min = array_rand($pkArr,21);
        foreach($min as $k=>$v)
        {
            echo $pkArr[$v].'<br>';
            unset($pkArr[$v]);
        }

        //vp($min,2);

        $min = array_rand($pkArr,15);
        foreach($min as $k=>$v)
        {
            echo $pkArr[$v].'<br>';
            unset($pkArr[$v]);
        }

        vp($pkArr);
    }

    /*
     * 从指定的扑克中取出想要类型的扑克牌类型集合
     * $pkarr 现在的牌中的集合
     * $len 想要取出的牌数
     * $type 想要取出的牌数类别
     * 要用递归的方式来取，不然太麻烦了
     */
    public function getpktypebypkarr($pkarr,$len,$type)
    {
        $fiveArr    = $this->getpklenbypkarr($pkarr,$len);
        $rtnArr     = [];

        if(!empty($fiveArr))
        {
            foreach($fiveArr as $k=>$v){
                $nowPkArr   = $this->rtnpktp($v);

                if($nowPkArr['type'] == $type){
                    $rtnArr[]   = $nowPkArr;
                }
            }
        }

        return $rtnArr;
    }


    /***
     * @param $pkarr
     * @param $len
     * @return array
     * 获取当前扑克中指定张数的所有组合集
     */
    public function getpklenbypkarr($pkarr,$len){

        if(empty($pkarr)){
            return [];
        }
        $rtnArr = [];
        foreach($pkarr as $k=>$v){
            $tmpLen     = $len - 1;
            if($tmpLen >0){
                if(isset($tmpPkArr)){
                    unset($tmpPkArr[$k]);
                }else{
                    $tmpPkArr   = $pkarr;
                    unset($tmpPkArr[$k]);
                }

                $tmpChileComb    = $this->getpklenbypkarr($tmpPkArr,$tmpLen);
                if(!empty($tmpChileComb)){
                    foreach($tmpChileComb as $kk=>$vv){
                        if(is_array($vv)){
                            $tmptmpComb   = $vv;
                        }else{
                            $tmptmpComb     = [];
                            $tmptmpComb[]   = $vv;
                        }

                        $tmptmpComb[]   = $v;
                        $rtnArr[]   = $tmptmpComb;
                    }
                }
            }else{
                $rtnArr[] = $v;
            }
        }

        return $rtnArr;
    }

    /**
     * @param array $pk1Arr
     * @param array $pk2Arr
     *
     * 这里主要验证的是Pk2是否大于PK1,验证两副牌的大小，斗地主只有大于与否的情况，不存在等于的情况
     */
    public function pokerpk($pk1Arr,$pk2Arr){

        $res = 1;
        if($pk2Arr['type'] == 8){//如果2是王炸
            return 2;
        }

        if($pk1Arr['type'] != 4 && $pk2Arr['type'] == 4){
            return 2;
        }

        $typeArr    = [1,2,4,9];//单张，对子，炸弹，四带二这几种只要对比最大牌面就好了
        if($pk1Arr['type'] == $pk2Arr['type']){
            if(in_array($pk1Arr['type'],$typeArr)){
                if($pk2Arr['max'] > $pk1Arr['max']){
                    return 2;
                }
            }

            //三带对比的时候，先要确定类型一致的
            if($pk1Arr['type'] == 3 && $pk1Arr['type2'] == $pk2Arr['type2']){
                if($pk2Arr['max'] > $pk1Arr['max']){
                    return 2;
                }
            }

            //顺子对比时，先确定长度一样，再对比最大的
            if($pk1Arr['type'] == 5 && $pk1Arr['cislen'] == $pk2Arr['cislen']){
                if($pk2Arr['max'] > $pk1Arr['max']){
                    return 2;
                }
            }

            //飞机对比时，先确定长度和类型是一样的，再对比最大牌面
            if($pk1Arr['type'] == 6 && $pk1Arr['type2'] == $pk2Arr['type2'] && $pk1Arr['flylen'] == $pk2Arr['flylen']){
                if($pk2Arr['max'] > $pk1Arr['max']){
                    return 2;
                }
            }

            //连对对比时，先确定长度都一样，再对比最大牌面
            if($pk1Arr['type'] == 7 && $pk1Arr['doublen'] == $pk2Arr['doublen']){
                if($pk2Arr['max'] > $pk1Arr['max']){
                    return 2;
                }
            }
        }

        return 1;
    }

    /**
     * 返回当前牌的类型
     * 单张 1
     * 对子 2
     * 三张 不带 带1 带1对 3
     * 四张 炸弹 带2张 带2对 4
     * 顺子 最少5张 5
     * 飞机 6连飞机 带单或带双 6
     * 连对 最少6张 7
     * 王炸 8
     *  一张 单张
     * 两张 对子 王炸弹
     * 三张 三张一样的
     * 四张 炸弹 三带一
     * 五张 顺子 三带一对
     * 六张 顺子 不带牌的飞机 四带二 连对
     * 七张 顺子
     * 八张 顺子 连对 带一张牌的飞机
     * 九张 顺子 不带牌的飞机
     * 十张 顺子 连对 带两张牌的飞机
     * 十一张 顺子
     * 十二张 顺子 连对 带一张牌的飞机 不带牌的飞机
     * 十三张 顺子
     * 十四张 顺子 连对
     * 十五张 顺子 带两张牌的飞机 不带牌的飞机
     * 十六张 顺子 连对 带一张牌的飞机
     * 十七张 顺子
     * 十八张 顺子 连对 不带牌的飞机
     * 十九张 顺子
     * 二十张 顺子 连对 带一张牌的飞机 带两张牌的飞机
     */
    public function rtnpktp($arr=[])
    {
        //$arr = ['s2'];
//        $arr = ['h2','s2'];
//        $arr = ['w2','w1'];

//        $arr = ['h2','d2','s2'];
//        $arr = ['h2','d2','s2','c1'];$arr = ['h2','d2','s2','c2'];
//        $arr = ['h2','d2','s2','c9','d9'];$arr = ['h11','d12','s13','c9','d10'];
        //六张时
        //$arr = ['h13','d12','s11','c1','d10','s9']; 顺子
        //$arr = ['h13','d13','s13','c1','d1','s1'];//不带牌的飞机
        //$arr = ['h13','d13','s12','c11','d12','s11'];//连对
        //$arr = ['h2','d2','s2','c2','d11','s11'];//四带二

        //七张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1'];//七张只能是连子

        //八张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7'];//八张顺子
        //$arr = ['h13','d13','s11','c10','d1','s1','c1','c13'];//八张飞机

        //九张时
        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7','d6'];//九张顺子
        //$arr = ['h13','d13','s13','c1','d1','s1','c12','d12','s12'];//九张时，不带牌的飞机

        //$arr = ['h13','d12','s11','c10','d9','s8','c1','c7','d6','d5'];
        //$arr = ['h13','d13','s13','c1','d1','s1','s12','c6','d12','s6'];//十张带一对的飞机


        $rtnArr = ['type'=>0,'max'=>'','intro'=>'啥也不是','pklist'=>[],];//默认返回0，不是正式的牌型
        //type 1 单张，2一对，3 三带，4 炸弹(4张)，5顺子，6飞机，7连对，8 对王，9四带二,

        if(empty($arr)){
            return $rtnArr;
        }

        $arrLen = count($arr);
        if($arrLen == 1){
            //只会是单张
            $rtnArr['type']     = 1;
            $rtnArr['pklist']   = $arr;
            $rtnArr['max']      = $this->pokerVal[$arr[0]];
            $rtnArr['intro']    = '单张';

        }elseif($arrLen == 2){
            $pksrtArr = $this->sortpk($arr);
            foreach($pksrtArr as $k=>$v){
                $pkValArr[]   = $this->pokerVal[$v];
            }
            if($pkValArr[0] == 19 && $pkValArr[1] == 18)
            {
                $rtnArr['type']     = 8;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = 19;
                $rtnArr['intro']    = '王炸';
            }elseif($pkValArr[0] == $pkValArr[1]){
                $rtnArr['type']     = 2;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $pkValArr[0];
                $rtnArr['intro']    = '对子';
            }

            return $rtnArr;
        }elseif($arrLen == 3){
            $pksrtArr = $this->sortpk($arr);
            foreach($pksrtArr as $k=>$v){
                $tmpval     = $this->pokerVal[$v];
                $pkValArr[$tmpval]   = $tmpval;
            }

            if(count($pkValArr) == 1){
                $rtnArr['type']     = 3;
                $rtnArr['type2']    = 1;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $tmpval;
                $rtnArr['intro']    = '三不带';
            }


        }elseif($arrLen == 4){
            $maxVal = '';
            $pksrtArr = $this->sortpk($arr);
            foreach($pksrtArr as $k=>$v){
                $tmpval     = $this->pokerVal[$v];
                if(isset($pkValArr[$tmpval])){
                    $pkValArr[$tmpval]++;
                    $maxVal = $tmpval;
                }else{
                    $pkValArr[$tmpval]  = 1;
                }
            }

            if(count($pkValArr) == 1){
                $rtnArr['type']     = 4;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $maxVal;
                $rtnArr['intro']    = '炸弹';
            }

            if(count($pkValArr) == 2 && $pkValArr[$maxVal] == 3){
                $rtnArr['type']     = 3;
                $rtnArr['type2']    = 2;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $maxVal;
                $rtnArr['intro']    = '三带一';
            }

        }elseif($arrLen == 5){

            $pksrtArr = $this->sortpk($arr);
            foreach($pksrtArr as $k=>$v){
                $tmpval     = $this->pokerVal[$v];
                if(isset($pkValArr[$tmpval])){
                    $pkValArr[$tmpval]++;
                }else{
                    $pkValArr[$tmpval]  = 1;
                }
            }

            if(count($pkValArr) == 2){//是否三带二
                $tmpMaxVal  = '';
                foreach($pkValArr as $k=>$v){
                    if($v == 3){
                        $tmpMaxVal  = $k;
                    }
                }

                if(!empty($tmpMaxVal)){
                    $rtnArr['type']     = 3;
                    $rtnArr['type2']    = 3;
                    $rtnArr['pklist']   = $arr;
                    $rtnArr['max']      = $tmpMaxVal;
                    $rtnArr['intro']    = '三带二';
                }
            }

            if($rtnArr['type'] == 0){
                //判断是否是顺子
                $rtnArr = $this->is_cis($arr,$rtnArr);
            }

        }elseif($arrLen == 6){
            //不带牌的飞机  四带二 顺子 连对

            if($rtnArr['type'] == 0){
                //判断是否是顺子
                $rtnArr = $this->is_cis($arr,$rtnArr);
            }

            if($rtnArr['type'] == 0){
                //判断是否是飞机
                $rtnArr = $this->is_fly($arr,$rtnArr);
            }

            if($rtnArr['type'] == 0){
                //判断是否是连对
                $rtnArr = $this->is_doub_cis($arr,$rtnArr);
            }

            if($rtnArr['type'] == 0){
                $pksrtArr = $this->sortpk($arr);
                foreach($pksrtArr as $k=>$v){
                    $tmpval     = $this->pokerVal[$v];
                    if(isset($pkValArr[$tmpval])){
                        $pkValArr[$tmpval]++;
                    }else{
                        $pkValArr[$tmpval]  = 1;
                    }
                }

                if(count($pkValArr) == 2){
                    $newPkArr   = [];
                    foreach($pkValArr as $k=>$v){
                        $newPkArr[$v]   = $k;
                    }

                    if(isset($newPkArr[4]) && isset($newPkArr[2])){//四带二 这里有问题，有时间的时候处理下
                        $rtnArr['type']     = 9;
                        $rtnArr['pklist']   = $arr;
                        $rtnArr['max']      = $newPkArr[4];
                        $rtnArr['intro']    = '四带二';
                    }
                }
            }
        }elseif($arrLen == 7){
            //七张牌只能是顺子
            if($rtnArr['type'] == 0){
                //判断是否是顺子
                $rtnArr = $this->is_cis($arr,$rtnArr);
            }
        }else{//大于八张的，只能是顺子，连对，或者飞机
            //顺子，飞机，连对
            if($rtnArr['type'] == 0){
                //判断是否是顺子
                $rtnArr = $this->is_cis($arr,$rtnArr);
            }

            if($rtnArr['type'] == 0){
                //判断是否是飞机
                $rtnArr = $this->is_fly($arr,$rtnArr);
            }

            if($rtnArr['type'] == 0){
                //判断是否是连对
                $rtnArr = $this->is_doub_cis($arr,$rtnArr);
            }
        }

        return $rtnArr;
    }

    //是否连对
    public function is_doub_cis($arr,$rtnArr){

        $arrlen = count($arr);
        if($arrlen%2 != 0){
            return $rtnArr;
        }
        $pksrtArr = $this->sortpk($arr);
        foreach($pksrtArr as $k=>$v){
            $tmpval     = $this->pokerVal[$v];
            if(isset($pkValArr[$tmpval])){
                $pkValArr[$tmpval]++;
            }else{
                $pkValArr[$tmpval]  = 1;
            }
        }

        $halfLen = $arrlen/2;
        $isTwo  = 1;
        $tmpPxArr   = [];

        foreach($pkValArr as $k=>$v){
            if($v != 2){
                $isTwo  = 0;
            }
            $tmpPxArr[] = $k;
        }

        if($isTwo == 1){
            $tmparrlen = count($pkValArr);
            $lastKey    = $tmparrlen - 1;
            if($tmpPxArr[0] - $tmpPxArr[$lastKey] == $lastKey){
                $rtnArr['type']     = 7;
                $rtnArr['doublen']  = $halfLen;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $tmpPxArr[0];
                $rtnArr['intro']    = '连对';
            }
        }

        return $rtnArr;
    }

    //是否飞机
    public function is_fly($arr,$rtnArr){

        $arrlen = count($arr);
        $pksrtArr = $this->sortpk($arr);
        foreach($pksrtArr as $k=>$v){
            $tmpval     = $this->pokerVal[$v];
            if(isset($pkValArr[$tmpval])){
                $pkValArr[$tmpval]++;
            }else{
                $pkValArr[$tmpval]  = 1;
            }
        }

        $isTrue = 0;
        if($arrlen == 6 || $arrlen == 9 || $arrlen == 12 || $arrlen == 15 || $arrlen == 18){
            //不带牌的飞机
            $isAllTr    = 1;
            $tmpPxArr   = [];
            foreach($pkValArr as $k=>$v) {
                if($v != 3){
                    $isAllTr = 0;
                }
                $tmpPxArr[]   = $k;
            }

            if($isAllTr == 1){
                $tmparrlen = count($pkValArr);
                $lastKey    = $tmparrlen - 1;
                if($tmpPxArr[0] - $tmpPxArr[$lastKey] == $lastKey){
                    $rtnArr['type']     = 6;
                    $rtnArr['type2']    = 1;
                    $rtnArr['flylen']   = $tmparrlen;
                    $rtnArr['pklist']   = $arr;
                    $rtnArr['max']      = $tmpPxArr[0];
                    $rtnArr['intro']    = '飞机(不带牌)';
                    $isTrue             = 1;
                }
            }
        }

        if(($arrlen == 8 || $arrlen == 12 || $arrlen == 16 || $arrlen == 20) && $isTrue == 0){
            //带一张牌的飞机
            $thrPkArr   = [];//三张的pk牌面
            $onePkArr   = [];//一张的pk牌面
            $isThOne    = 1; //是否只有三张和一张
            foreach($pkValArr as $k=>$v) {
                if($v == 3){
                    $thrPkArr[] = $k;
                }elseif($v == 1){
                    $onePkArr[] = $k;
                }else{
                    $isThOne = 0;
                }
            }

            if($isThOne == 1 && count($thrPkArr) == count($onePkArr)){
                $thrArrlen = count($thrPkArr);
                $lastKey    = $thrArrlen - 1;
                if($thrPkArr[0] - $thrPkArr[$lastKey] == $lastKey){
                    $rtnArr['type']     = 6;
                    $rtnArr['type2']    = 2;
                    $rtnArr['flylen']   = $thrArrlen;
                    $rtnArr['pklist']   = $arr;
                    $rtnArr['max']      = $thrPkArr[0];
                    $rtnArr['intro']    = '飞机(带一张)';
                    $isTrue             = 1;
                }
            }
        }

        if(($arrlen == 10 || $arrlen == 15 || $arrlen == 20) && $isTrue == 0){
            //带一对牌的飞机
            $thrPkArr   = [];//三张的pk牌面
            $twoPkArr   = [];//两张的pk牌面
            $isThTwo    = 1; //是否只有三张和两张
            foreach($pkValArr as $k=>$v) {
                if($v == 3){
                    $thrPkArr[] = $k;
                }elseif($v == 2){
                    $twoPkArr[] = $k;
                }else{
                    $isThTwo = 0;
                }
            }

            if($isThTwo == 1 && count($thrPkArr) == count($twoPkArr)){
                $thrArrlen = count($thrPkArr);
                $lastKey    = $thrArrlen - 1;
                if($thrPkArr[0] - $thrPkArr[$lastKey] == $lastKey){
                    $rtnArr['type']     = 6;
                    $rtnArr['type2']    = 3;
                    $rtnArr['flylen']   = $thrArrlen;
                    $rtnArr['pklist']   = $arr;
                    $rtnArr['max']      = $thrPkArr[0];
                    $rtnArr['intro']    = '飞机(带一对)';
                }
            }
        }

        return $rtnArr;
    }

    //是否是连子
    public function is_cis($arr,$rtnArr){

        $pksrtArr = $this->sortpk($arr);
        foreach($pksrtArr as $k=>$v){
            $tmpval     = $this->pokerVal[$v];
            if(isset($pkValArr[$tmpval])){
                $pkValArr[$tmpval]++;
            }else{
                $pkValArr[$tmpval]  = 1;
            }
        }

        $isNo   = 0;
        $tmpPxArr   = [];
        foreach($pkValArr as $k=>$v){
            if($v > 1){
                $isNo = 1;
            }
            $tmpPxArr[] = $k;
        }

        if($isNo != 1){
            $arrlen = count($pkValArr);
            $lastKey    = $arrlen - 1;
            if($tmpPxArr[0] - $tmpPxArr[$lastKey] == $lastKey){
                $rtnArr['type']     = 5;
                $rtnArr['cislen']   = $arrlen;
                $rtnArr['pklist']   = $arr;
                $rtnArr['max']      = $tmpPxArr[0];
                $rtnArr['intro']    = '顺子';
            }
        }

        return $rtnArr;
    }

    //加入或者退出桌面list 这里和五子棋不同，只允许打牌的人进入桌子，没参与的人不能进入桌子
    public static function uptblistuser($uid)
    {
        $res    = D('ddzulist')->where('user_id = '.$uid)->find();
        $newtime = time();
        if($res)
        {
            //后期这里再调整下，现在只要刷新页面就更新，后期改为刷新页面前先判断状态，不一致时再更新
            $ulistArr['state']      = 1;
            $ulistArr['tableid']    = 0;
            $ulistArr['tableposid'] = 0;
            $ulistArr['lastuptime'] = $newtime;

            //按道理这里本来不用更新，但每次刷新页面，数据库的数据已经变了，但前台还是没有变，说明swoole的更新速度慢于前端，那就在这里也更新下吧
            if($res->tableid > 0 && $res->tableposid > 0)
            {//如果已经在桌子上了，不刷新数据，用户的异常退出统一交由后端的超时处理
                return 1;
            }

            D('ddzulist')->where('id='.$res->id)->save($ulistArr);

        }else{
            $ulistArr['user_id']     = $uid;
            $ulistArr['state']      = 1;
            $ulistArr['tableid']    = 0;
            $ulistArr['tableposid'] = 0;
            $ulistArr['addtime']    = $newtime;
            $ulistArr['lastuptime'] = $newtime;
            D('ddzulist')->add($ulistArr);
        }

        return 2;
    }

}