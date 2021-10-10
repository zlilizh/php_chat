<?php
if(!defined('CHT')){die();}
class Chat
{

    const HOST = '0.0.0.0'; //允许所有ip的访问
    const PART = 9501; //端口号
    private $server = null;  //服务对象

    public function __construct(){

        $this->server = new swoole_websocket_server(self::HOST, self::PART);

        //监听连接事件
        $this->server->on('open', [$this, 'onOpen']);
        //监听接收消息事件
        $this->server->on('message', [$this, 'onMessage']);
        //监听关闭事件
        $this->server->on('close', [$this, 'onClose']);
        //开启服务
        $this->server->start();
    }

    /**
     * 连接成功回调函数
     * @param $server
     * @param $request
     */
    public function onOpen($server, $request)
    {
        echo $request->fd . '已连接' . PHP_EOL;//输出终端
    }

    /**
     * 接收到信息的回调函数
     * @param $server
     * @param $frame
     * @return bool
     */
    public function onMessage($server, $frame)
    {
        $data = $frame->data;
        // 群发
        //echo $frame->fd . '说：' . $data . PHP_EOL;//打印到我们终端
        echo $frame->fd . '说：...'. PHP_EOL;//打印到我们终端
        //这里要做一个连接验证，不是谁都可以来做连接
        $_data   = json_decode($data);

        if(isset($_data->act) && $_data->act == 'wzq' ) {//第一次连接的时候判断生成谁的连接
            $info = new wuziqclass();
        }elseif(isset($_data->act) && $_data->act == 'doudz' ){
            $info = new Doudzclass();
        }else{
            $info   = new infoclass();
        }

        $res    = $info->message($frame,$data,$server);
        if($res['mt'] == 0)
        {
            $server->push($res['send_to_msg'][0]['to_fd'], json_encode(['msg' => $res['send_to_msg'][0]['msg']])); //只给我发送
        }else if($res['mt']==1)
        {
            if(isset($res['check']) && $res['check'] === false)
            {//连接验证失败，断开连接
               $server->disconnect($frame->fd); 
            }else{
            
                if($res['od_fd'] === false)
                {

                }else{//断开之前的连接，一个用户同时只能在一个窗口聊天
                    //先判断这条连接是否存在，存在的话再关闭
                    if($server->exist($res['od_fd']))
                    {
                        $server->disconnect($res['od_fd']);
                    }
                }
            }
        }else if($res['mt'] == 2)
        {
            
            foreach($res['send_to_msg'] as $k=>$v)
            {
                $server->push($v['to_fd'], json_encode(['msg' => $v['msg']]));
            }

        }else{

        }
    }

    /**
     * 断开连接回调函数
     * @param $server
     * @param $fd
     */
    public function onClose($server, $fd)
    {


        $fd_info    = get_fd_hscache($fd);//获取指定连接ＩＤ的缓
        if($fd_info['type'] == 'chat')
        {

        }else if($fd_info['type'] == 'wzq')
        {
            $info   = new wuziqclass();
            $res    = $info->closefd($fd);

            if($res['mt'] == 2 && !empty($res['send_to_msg'])){
                foreach($res['send_to_msg'] as $k=>$v)
                {
                    $server->push($v['to_fd'], json_encode(['msg' => $v['msg']]));
                }
            }
        }

        $rd = new redis_cache();
        $tm_fd_k   = 'fd_'.$fd;
        $rd->hdel('fdinfo',$tm_fd_k);
        
//        $tm_uf_k   = 'uf_'.$fd;
//        $rd->hdel('ufinfo',$tm_uf_k);
        echo $fd . '断开连接' . PHP_EOL;//输出到终端终端



    }

}