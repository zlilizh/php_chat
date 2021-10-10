var xt_wsk ={
   
   link:function()
   {
       var _url     = xt_site.web_socket;
       var _uarr    = {};
       _uarr['uid'] = $('._d_uid').val();
       _uarr['avt'] = $('._d_avt').val();
       _uarr['uname'] = $('._d_unm').val();
       _uarr['token'] = $('._d_tok').val();
       //暂时先这两个值，后面可以再加一个验证的相关值
       
       var _data    = {};
       
       socket=new WebSocket(_url);
       socket.onopen=function(){
           _data['type']    = 1;
           _data['act']     = 'wzq';
           _data['data']    = _uarr;
           xt_wsk.send(_data);
        }
        
        socket.onmessage=function(msg){xt_wsk.showdata(msg)};
        socket.onclose=function(){
            layer.open({
                title: '连接提示',
                content: '连接已断开,刷新页面重新连接!'
            });
        };
   },
   
   send:function(data)
   {
       var _data  = JSON.stringify(data);  
       socket.send(_data);
   },
   sendto:function()
   {
        var _tmsg    = $('.div_put_text').html();//$('.chat_input_data').val();
        var _msg    = _tmsg.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, ' ');
        if(_msg == '')
        {
            return false;
        }else{

            var _url    = xt_site.web_url+'/index.php/index/formatcont';
            var _sdata  = {};
            _sdata['sendtxt'] = _msg;

            $.post(_url,_sdata,function(res){
                if(res._state == 'ok')watingtime
                {
                    var _data    = {};
                    _data['type']    = 2;
                    _data['act']     = 'wzq';
                    _data['data']    = res._sendmsg;
                    xt_wsk.send(_data);

                    $('.div_put_text').html('');
                }

            },'json');
        }
        //$('.chat_input_data').val('');
   },
   showdata:function(msg)
   {
       var _data    = JSON.parse(msg.data);
       if(_data.msg._type ==2)
       {//进入某一个空位坐下时
           if(_data.msg._data.dt == 'pos')
           {
                $('.wz_dom_post'+_data.msg._data.posid).html(_data.msg._data.info);
                $('.wz_dom_post'+_data.msg._data.posid).closest('.wbk_bj').removeClass('wbk_bj');//重新赋值时，去除下棋者标识
           }else if(_data.msg._data.dt == 'tbl'){
               $('.wz_dom_li_'+_data.msg._data.tbid).find('.wz_dom_li_pos_'+_data.msg._data.posid).html(_data.msg._data.info);
           }
       }else if(_data.msg._type ==3){
            $('.wz_dom_chat_list').append(_data.msg._data.info);
           xt_wsk.tofooter();

       }else if(_data.msg._type ==4){

           var _rtnmsg  = _data.msg._data;

           if(_rtnmsg.chathtml == '')
           {

           }else{
               $('.wz_dom_chat_list').append(_rtnmsg.chathtml);
               xt_wsk.tofooter();
           }

           $('.wz_dom_tab_sta').html(_rtnmsg.tbstate);
           $('.wz_dom_post_state_1').html(_rtnmsg.us1.tip);
           $('.wz_dom_post_state_2').html(_rtnmsg.us2.tip);

           if(_rtnmsg.us1.upbj == 1){
               $('.wz_dom_post1').closest('.wzblk').addClass('wbk_bj');
               xt_wuzi.watingtime(1);
           }else{
               $('.wz_dom_post1').closest('.wzblk').removeClass('wbk_bj');
           }

           if(_rtnmsg.us2.upbj == 1){
               $('.wz_dom_post2').closest('.wzblk').addClass('wbk_bj');
               xt_wuzi.watingtime(2);
           }else{
               $('.wz_dom_post2').closest('.wzblk').removeClass('wbk_bj');
           }

           if(_rtnmsg.isstart == 1)
           {
               //var _astr    = '<a href="javascript:void(0)" onClick="xt_wuzi.playchess(this)"></a>';
               // $('.w2pic').html(_astr);
               // $('.b2pic').html(_astr);
               // $('.wpic').html(_astr);
               // $('.bpic').html(_astr);

               $('.w2pic').addClass('wz_dom_chess_dd');
               $('.b2pic').addClass('wz_dom_chess_dd');
               $('.wpic').addClass('wz_dom_chess_dd');
               $('.bpic').addClass('wz_dom_chess_dd');

               $('.w2pic').removeClass('w2pic');
               $('.b2pic').removeClass('b2pic');
               $('.wpic').removeClass('wpic');
               $('.bpic').removeClass('bpic');
           }

       }else if(_data.msg._type ==5){

           var _astr    = '<a href="javascript:void(0)" onClick="xt_wuzi.playchess(this)"></a>';
           if(_data.msg._data.isstart == 1 && _data.msg._data.dt == 'startus'){//进行中的棋局控制
               $('.games').find('dd').html(_astr);
           }
           if(_data.msg._data.isstart == 1 && _data.msg._data.dt == 'playchess'){//新开始的棋局控制
               $('.wz_dom_chess_dd').html(_astr);
           }
       }else if(_data.msg._type == 6){

           var _rtnmsg  = _data.msg._data;

           //变换棋子
           if(_rtnmsg.ysstr == 'w2pic')
           {
               $('.b2pic').addClass('bpic');
               $('.b2pic').removeClass('b2pic');

           }else{
               $('.w2pic').addClass('wpic');
               $('.w2pic').removeClass('w2pic');

           }

           //棋子样式
           $('.dd_'+ _rtnmsg.dx +'_'+_rtnmsg.dy).addClass(_rtnmsg.ysstr);
           $('.dd_'+ _rtnmsg.dx +'_'+_rtnmsg.dy).removeClass('wz_dom_chess_dd');
           $('.dd_'+ _rtnmsg.dx +'_'+_rtnmsg.dy).html('');

           if(_rtnmsg.iswin == 1)
           {//比赛结束时/某一位赢得了比赛时
               var _winstr  = _rtnmsg.actuname + '赢得了本场比赛';
               layer.alert(_winstr);
               xt_wuzi.watingtime(3);
               xt_wuzi.disstart(1);

               // $('.wz_dom_post1').closest('.wzblk').removeClass('wbk_bj');
               // $('.wz_dom_post2').closest('.wzblk').removeClass('wbk_bj');
           }else{

               if(_rtnmsg.us1.upbj == 1){
                   $('.wz_dom_post1').closest('.wzblk').addClass('wbk_bj');
                   xt_wuzi.watingtime(1);
               }else{
                   $('.wz_dom_post1').closest('.wzblk').removeClass('wbk_bj');
               }

               if(_rtnmsg.us2.upbj == 1){
                   $('.wz_dom_post2').closest('.wzblk').addClass('wbk_bj');
                   xt_wuzi.watingtime(2);
               }else{
                   $('.wz_dom_post2').closest('.wzblk').removeClass('wbk_bj');
               }

               // $('.wz_dom_post_state_1').html(_rtnmsg.us1.tip);
               // $('.wz_dom_post_state_2').html(_rtnmsg.us2.tip);
           }
       }else if(_data.msg._type == 7){//超时时
           var _rtnmsg  = _data.msg._data;

           if(_rtnmsg.iswin == 1)
           {//超时时显示比赛结果
               var _winstr  = '因对手超时未处理,系统判定'+ _rtnmsg.actuname +'赢得了本场比赛!';
               layer.alert(_winstr);
               xt_wuzi.watingtime(3);
               $('.wzblk').removeClass('wbk_bj');
               $('.wz_dom_chess_dd').html('');
               xt_wuzi.disstart(1);

           }
       }
   },
   
   tofooter:function()
   {
       var e=document.getElementById("wzq_dom_chat_id");
       e.scrollTop=e.scrollHeight;
   },
   keydown:function(e)
    {
            var theEvent 	= e || window.event;    
            var code 		= theEvent.keyCode || theEvent.which || theEvent.charCode;

            if (code == 13) {    
                    xt_wsk.sendto();
            }
    }
   
}

var xt_wuzi  = {
    comein:function(obj)
    {
        var _tbid   = $(obj).attr('tbid');
        var _url    = xt_site.web_url+'/index.php/wuziq/getversusinfo';
        var ii      = layer.load();
        $.post(_url,{tbid:_tbid},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);
                var _tbname = _tbid+'号桌';
                $('.wz_dom_list').hide();
                $('.wz_dom_one').show();
                $('.wzdo_tb_name').text(_tbname);
                $('.wz_dom_chat_inp').val('');
                $('.wz_dom_chat_list').html('');
                $('.wz_dom_tab_sta').html(res._tabsta);

                if(res._rtnht.posiontion1 != '')
                {
                    $('.wz_dom_post1').html(res._rtnht.posiontion1);
                }

                if(res._rtnht.posiontion1 != '')
                {
                    $('.wz_dom_post2').html(res._rtnht.posiontion2);
                }

                $('.wz_dom_post_state_1').html(res._rtnht.postip1);
                $('.wz_dom_post_state_2').html(res._rtnht.postip2);
                $('.wz_dom_chess_dd').html('');

            }else{
                layer.alert(res._msg);
            }
        },'json');
    },
    resettab:function()
    {//还原桌面
        $('.w2pic').addClass('wz_dom_chess_dd');
        $('.b2pic').addClass('wz_dom_chess_dd');
        $('.wpic').addClass('wz_dom_chess_dd');
        $('.bpic').addClass('wz_dom_chess_dd');

        $('.w2pic').removeClass('w2pic');
        $('.b2pic').removeClass('b2pic');
        $('.wpic').removeClass('wpic');
        $('.bpic').removeClass('bpic');
        $('.wbk_bj').removeClass('wbk_bj');
    },
    goout:function()
    {
        var _url    = xt_site.web_url+'/index.php/wuziq/goout';
        var ii      = layer.load();
        $.post(_url,{},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);

                var _tbls   = res._tbinfo;
                var _len    = res._count;
                // console.log(_tbls[1]['id']);
                for(var i=1;i<=_len;i++)
                {
                    $('.wz_dom_li_'+_tbls[i]['id']).find('.wz_dom_li_pos_1').html(_tbls[i]['user1']);
                    $('.wz_dom_li_'+_tbls[i]['id']).find('.wz_dom_li_pos_2').html(_tbls[i]['user2']);
                    $('.wz_dom_li_'+_tbls[i]['id']).find('.wz_dom_li_sta').html(_tbls[i]['state']);
                }

                $('.wz_dom_one').hide();
                $('.wz_dom_list').show();

                if(res._msgkey == '')
                {

                }else{
                    var _data    = {};
                    _data['type']    = 2;
                    _data['act']     = 'wzq';
                    _data['data']    = res._msgkey;
                    xt_wsk.send(_data);
                }

                xt_wuzi.resettab();
                xt_wuzi.disstart(2);
            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }
        },'json');


    },
    sitdown:function(obj)
    {
        var _url    = xt_site.web_url+'/index.php/wuziq/sitdown';
        //var ii      = layer.load();
        var _poid   = $(obj).attr('poid');
        var _par    = $(obj).closest('.wz_dom_position');
        $.post(_url,{posid:_poid},function(res){
            if(res._state == 'ok')
            {
                var _data    = {};
                _data['type']    = 2;
                _data['act']     = 'wzq';
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

                xt_wuzi.disstart(1);

            }else{
                layer.alert(res._msg);
            }
            //layer.close(ii);
        },'json');
    },
    sendtxt:function()
    {
        var _url    = xt_site.web_url+'/index.php/wuziq/sendtxt';
        var _sendt   = $('.wz_dom_chat_inp').val();
        $.post(_url,{sendtxt:_sendt},function(res){
            if(res._state == 'ok')
            {
                $('.wz_dom_chat_inp').val('');
                $('.wz_dom_chat_inp').focus();

                var _data    = {};
                _data['type']    = 3;
                _data['act']     = 'wzq';
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
            }
        },'json');
    },
    startgm:function()
    {
        var _url    = xt_site.web_url+'/index.php/wuziq/startgm';
        $.post(_url,{},function(res){
            if(res._state == 'ok')
            {
                var _data    = {};
                _data['type']    = 4;
                _data['act']     = 'wzq';
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

                xt_wuzi.disstart(2);
                //layer.alert(res._msg);
            }else{
                layer.alert(res._msg);
            }
        },'json');
    },
    playchess:function(obj)
    {
        var _url  = xt_site.web_url+'/index.php/wuziq/palychess';
        var _par  = $(obj).closest('dd');
        var _dx   = _par.attr('dx');
        var _dy   = _par.attr('dy');

        $.post(_url,{dx:_dx,dy:_dy},function(res){
            if(res._state == 'ok')
            {
                _par.addClass(res._ysstr);
                _par.removeClass('wz_dom_chess_dd');
                _par.html('');
                $('.wz_dom_chess_dd').html('');

                var _data    = {};
                _data['type']    = 5;
                _data['act']     = 'wzq';
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);
                //layer.alert(res._msg);
            }else{
                layer.alert(res._msg);
            }
        },'json');
    },
    watingtime:function(_tp,_tim= 120)
    {
        clearInterval(wzdsq);
        var tim     = _tim;
        if(_tp == 1)
        {
            $('.wz_dom_post_state_2').html('');
            $('.wz_dom_post_state_1').html(tim);
        }else if(_tp ==2){
            $('.wz_dom_post_state_1').html('');
            $('.wz_dom_post_state_2').html(tim);
        }else{
            $('.wz_dom_post_state_1').html('');
            $('.wz_dom_post_state_2').html('');
            return false;
        }

        wzdsq = setInterval(function(){
            var time = --tim;
            if(_tp == 1)
            {
                $('.wz_dom_post_state_2').html('');
                $('.wz_dom_post_state_1').html(time);
            }else{
                $('.wz_dom_post_state_1').html('');
                $('.wz_dom_post_state_2').html(time);
            }
            if(time <= 0 ) {
                clearInterval(wzdsq);
            };

        }, 1000);
    },
    disstart:function(_tp)
    {
        var _str    = '<a href="javascript:void(0)" onclick="xt_wuzi.startgm()">开始</a>';
        var _nstr   = '<span class="nocli">开始</span>';
        if(_tp == 1)
        {
            $('.wz_dom_start').html(_str);
        }else{
            $('.wz_dom_start').html(_nstr);
        }
    }
}


