<?php
if(!defined('ML')){die();}
class LoginClass extends ActionClass{

    protected function auto_load()
    {

    }

    public function index()
    {
        $sub_url		= $this->wcfg['w_d_url'].'/login/check';
        $this->assign('s_url',$sub_url);
        $this->template();
    }

    public function check()
    {
        $uname			= $_POST['username'];
        $pword			= $_POST['password'];

        if(empty($uname) || empty($pword))
        {
            $this->error('用户名或密码不能为空!');
        }

        $dMember		= D('member');
        $res			= $dMember->where('username=\''.$uname.'\'')->find();
        if($res){

            $pwd = md5($pword);
            $pwd = md5($pwd.$res->addtime);

            if($pwd != $res->password){
                $this->error('用户名或密码错误!');
            }

            $dMlog			= D('member_log');
            $arr['member_id']	= $res->id;
            $arr['login_time']	= time();
            $arr['ip']		= get_client_ip();

            $dMlog->add($arr);

            $narr['login_num']	= $res->login_num + 1;

            $dMember->where('id='.$res->id)->save($narr);

            //登录成功后，在这里创建连接的token
            $token      = '$*'.$res->id.'&%'.$arr['login_time'].'#@'.$uname;
            $token      = md5(md5($token));
            $tk_arr     = array(
                'uid'   => $res->id,
                'uname' => $uname,
                'ltime' => $arr['login_time'],
                'token' => $token
            );

            set_utoken_cache($res->id,$tk_arr);
            session_start();
            $_SESSION['xt_member_id']	= $res->id;
            $url				= $this->wcfg['w_d_url'].'/index/swlist';
            $this->go_header($url);
        }else{
            $this->error('用户名或密码错误!');
        }
    }

    public function outlogin()
    {
        session_start();
        //                session_unset();
        unset($_SESSION['xt_member_id']);
        //                session_destroy();
        $url	= $this->wcfg['w_d_url'].'/login/';
        $this->go_header($url);

    }
}
