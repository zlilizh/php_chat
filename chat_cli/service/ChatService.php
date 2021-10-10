<?php
if(!defined('ML')){die();}

class ChatService
{
    public static function buildgrouphtml($group_id,$isgact,$delUid=[])
    {
//            $group_id       = 1;
        $tmp_group      = get_group_cache($group_id);
        if(empty($tmp_group['gname'])){
            $tmp_gname_arr  = [];
            foreach($tmp_group['ulist'] as $kk=>$vv){
                $tmp_gname_arr[]    = $vv['name'];
            }

            $tmp_group['gname']  = join('、',$tmp_gname_arr);
        }

        $_tohtml    = '<div class = "ublk_area"><div class="ublk _group_chat_'.$group_id.'" onclick="xt_pub.get_msg(this)" tp="2" gid="'.$group_id.'" uname="'.$tmp_group['gname'].'">';
        $_formhtml  = '<div class = "ublk_area"><div class="ublk ublk_on _group_chat_'.$group_id.'" onclick="xt_pub.get_msg(this)" tp="2" gid="'.$group_id.'" uname="'.$tmp_group['gname'].'">';

        $_html  = '<div class="uk_avt">';
        $_frhtml  = '<div class = "ublk_area"><div class="ublk _fgl_group_'.$group_id.'" onclick="xt_pub.getgroinfo(this)" gid="'.$group_id.'"><div class="uk_avt">';
        foreach($tmp_group['ulist'] as $K=>$v){
            if($K < 9){
                $_html  .= '<div class="smuk">';
                $_html  .= '<img src="'.$v['pic'].'" height="'.$tmp_group['uavt_hei'].'" width="'.$tmp_group['uavt_hei'].'"/>';
                $_html  .= '</div>';

                $_frhtml  .= '<div class="smuk"><img src="'.$v['pic'].'" height="'.$tmp_group['uavt_hei'].'" width="'.$tmp_group['uavt_hei'].'"/></div>';
            }
        }

        $_html  .= '</div><div class="ukl"><div class="uk_nm">';
        $_html  .= $tmp_group['gname'];
        $_html  .= '</div><div class="sim_msg"></div></div>';
        $_html  .= '<div class="ukr"><div class="uk_tm">';
        $_html  .= date('Y.m.d',$tmp_group['addtime']);
        $_html  .= '</div>';
        $_html  .= '<div class="mg_num chat_cmtn_num" style="display:none"></div></div></div></div>';
        $_frhtml .= '</div>';
        $_frhtml .= '<div class="uk_lnm">'.$tmp_group['gname'].'</div></div></div>';

        $group_key  = uniqid();
        $group_key  .= get_rand_str(4);
        $valArr     = ['group_id'=>$group_id,'group_name'=>$tmp_group['gname'],'group_html'=>$_html,'tohtml'=>$_tohtml,'formhtml'=>$_formhtml,'frihtml'=>$_frhtml,'isgact'=>$isgact,'deluid'=>$delUid];
        $expTime    = 60;
        set_key_cache($group_key,$valArr,$expTime);

        return $group_key;
    }

    public static function rtnextimg($ext)
    {
        $extArr         = [
            'xlsx'	=> 'excel.png',
            'xls'	=> 'excel.png',
            'csv'	=> 'excel.png',
            'txt'	=> 'text.png',
            'zip'	=> 'rar.png',
            'rar'	=> 'rar.png',
            'doc'	=> 'word.png',
            'docx'	=> 'word.png',
            'wps'	=> 'word.png',
            'ppt'	=> 'ppt.png',
            'pptx'	=> 'ppt.png',
            'pdf'   => 'pdf.png',
        ];

        $img = 'other.png';
        if(isset($extArr[$ext])){
            $img = $extArr[$ext];
        }

        $fileimg        = '/templates/home/default/public/img/'.$img;

        return $fileimg;
    }

    public static function rtnsize($byte)
    {

        $KB = 1024;
        $MB = 1024 * $KB;
        $GB = 1024 * $MB;
        $TB = 1024 * $GB;
        if ($byte < $KB) {
            return $byte . "B";
        } elseif ($byte < $MB) {
            return round($byte / $KB, 2) . "KB";
        } elseif ($byte < $GB) {
            return round($byte / $MB, 2) . "MB";
        } elseif ($byte < $TB) {
            return round($byte / $GB, 2) . "GB";
        } else {
            return round($byte / $TB, 2) . "TB";
        }
    }

    public static function savebase64pic($base64dt,$filename)
    {
        $res    = file_put_contents($filename,base64_decode($base64dt));
        if(!$res){
            return false;
        }else{
            return $filename;
        }

    }
}
