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
            _data['act']     = 'doudz';
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
        data['act'] = 'doudz';
        var _data  = JSON.stringify(data);
        socket.send(_data);
    },

    showdata:function(msg)
    {
        var _data    = JSON.parse(msg.data);
        if(_data.msg._type ==2)
        {
            var _rtnmsg  = _data.msg._data;
            if(_rtnmsg.dt == 'tbl'){
                if(_data.msg._is_self == 1){
                    layer.msg('跳转');
                    window.location.href = '/index.php/doudz/detial';
                }else{
                    $('.ddz_dom_li_'+_rtnmsg.tbid).find('.ddz_dom_pos_'+_rtnmsg.posid).html(_rtnmsg.info);
                }
            }else{
                $('.ddz_dom_uinfo_pos_'+_rtnmsg.posid).find('.ddz_dom_uinfo').html(_rtnmsg.info);
            }

        }else if(_data.msg._type == 3){
            var _rtnmsg  = _data.msg._data;
            if(_data.msg._is_self == 1){
                $('.ddz_dom_act_btn').html(_rtnmsg.btnhtml);
                var _dzpoker = '<dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd>';
                //var _dzpoker = '<dd>pkbm</dd><dd>pkbm</dd><dd>pkbm</dd>';

                $('.ddzm_top_dl').html(_dzpoker);
                $('.ddz_dom_now_pklist').html('');
                $('.ddz_dom_top_mul').html(1);
            }else{
                $('.ddz_dom_pkblk_pos_'+_rtnmsg.posid).find('.ddz_dom_pkblk_tip').html(_rtnmsg.rdhtml);
            }
        }else if(_data.msg._type == 4){
            var _rtnmsg  = _data.msg._data;
            var _dzpoker = '<dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd>';

            $('.ddzm_top_dl').html(_dzpoker);

            xt_ddz.uptip('up');
            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            xt_ddz.watingtime('up',_rtnmsg.up.countdown);

            xt_ddz.uptip('next');
            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            xt_ddz.watingtime('next',_rtnmsg.next.countdown);

            $('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            xt_ddz.watingtime('my',_rtnmsg.my.countdown);
            xt_ddz.showbtn(_rtnmsg.my.btntp);

        }else if(_data.msg._type == 5){
            var _rtnmsg  = _data.msg._data;
            $('.ddz_dom_top_mul').html(_rtnmsg.multiple_num);

            xt_ddz.uptip('up',_rtnmsg.up.acttip);
            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            xt_ddz.watingtime('up',_rtnmsg.up.countdown);

            xt_ddz.uptip('next',_rtnmsg.next.acttip);
            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            xt_ddz.watingtime('next',_rtnmsg.next.countdown);

            //$('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            xt_ddz.watingtime('my',_rtnmsg.my.countdown);
            if(_rtnmsg.my.btntp == 0){
                xt_ddz.uptip('my',_rtnmsg.my.acttip);
            }else{
                xt_ddz.showbtn(_rtnmsg.my.btntp);
            }

        }else if(_data.msg._type == 6){
            var _rtnmsg  = _data.msg._data;
            $('.ddz_dom_top_mul').html(_rtnmsg.multiple_num)

            xt_ddz.uptip('up');
            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            xt_ddz.watingtime('up',_rtnmsg.up.countdown);
            xt_ddz.showlandlord('up',_rtnmsg.up.islandlord);

            xt_ddz.uptip('next');
            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            xt_ddz.watingtime('next',_rtnmsg.next.countdown);
            xt_ddz.showlandlord('next',_rtnmsg.next.islandlord);

            $('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            // xt_ddz.uptip('my',_rtnmsg.my.acttip);
            xt_ddz.watingtime('my',_rtnmsg.my.countdown);
            xt_ddz.showlandlord('my',_rtnmsg.my.islandlord);
            xt_ddz.showbtn(4);

            $('.ddzm_top_dl').html(_rtnmsg.dzpkht);
        }else if(_data.msg._type == 7){

            var _rtnmsg  = _data.msg._data;
            $('.ddz_dom_top_mul').html(_rtnmsg.mulval);
            var tit = '加倍';
            if(_rtnmsg.douval == 2){
                tit = '不加倍';
            }
            var _tit = '<h1 class="layui-font-orange" style="font-weight:bold;display: inline-block">'+tit+'</h1>';
            if(_data.msg._is_self == 1){
                $('.ddz_dom_act_btn').html(_tit);
                xt_ddz.watingtime('my','');
            }else{
                $('.ddz_dom_pkblk_pos_'+_rtnmsg.posid).find('.ddz_dom_pkblk_tip').html(_tit);
                $('.ddz_dom_pkblk_pos_'+_rtnmsg.posid).find('.ddz_dom_tm').hide();

            }
        }else if(_data.msg._type == 8){

            var _rtnmsg  = _data.msg._data;

            xt_ddz.uptip('up','');
            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            xt_ddz.watingtime('up',_rtnmsg.up.countdown);

            xt_ddz.uptip('next','');
            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            xt_ddz.watingtime('next',_rtnmsg.next.countdown);
            //$('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            // xt_ddz.uptip('my',_rtnmsg.my.acttip);
            //$('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            xt_ddz.watingtime('my',_rtnmsg.my.countdown);
            xt_ddz.showbtn(_rtnmsg.my.btntp);
            $('.ddz_dom_top_mul').html(_rtnmsg.multiple_num);
        }else if(_data.msg._type == 9){
            var _rtnmsg  = _data.msg._data;

            if(_rtnmsg.pktip == ''){

            }else{
                layer.msg(_rtnmsg.pktip,{time: 500});
                $('.ddz_dom_top_mul').html(_rtnmsg.multiple_num)
            }

            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            if(_rtnmsg.up.distp == 'pk'){
                $('.ddz_dom_up_tm').hide();
                $('.ddz_dom_up_tip').hide();
                $('.ddz_dom_pkblk_up_tip').hide();
                $('.ddz_dom_up_pklist').show();
                $('.ddz_dom_up_pklist').html(_rtnmsg.up.sendpk);
            }else if(_rtnmsg.up.distp == 'tip'){
                $('.ddz_dom_up_tm').hide();
                $('.ddz_dom_up_tip').show();
                $('.ddz_dom_pkblk_up_tip').show();
                $('.ddz_dom_up_pklist').hide();
                xt_ddz.uptip('up',_rtnmsg.up.acttip);
            }else{
                xt_ddz.watingtime('up',_rtnmsg.up.countdown);
                $('.ddz_dom_up_tm').show();
                $('.ddz_dom_pkblk_up_tip').show();
                $('.ddz_dom_up_tip').hide();
                $('.ddz_dom_up_pklist').hide();
            }

            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            if(_rtnmsg.next.distp == 'pk'){
                $('.ddz_dom_next_tm').hide();
                $('.ddz_dom_next_tip').hide();
                $('.ddz_dom_pkblk_next_tip').hide();
                $('.ddz_dom_next_pklist').show();
                $('.ddz_dom_next_pklist').html(_rtnmsg.next.sendpk);
            }else if(_rtnmsg.next.distp == 'tip'){
                $('.ddz_dom_next_tm').hide();
                $('.ddz_dom_next_tip').show();
                $('.ddz_dom_pkblk_next_tip').show();
                $('.ddz_dom_next_pklist').hide();
                xt_ddz.uptip('next',_rtnmsg.next.acttip);
            }else{
                xt_ddz.watingtime('next',_rtnmsg.next.countdown);
                $('.ddz_dom_next_tm').show();
                $('.ddz_dom_pkblk_next_tip').show();
                $('.ddz_dom_next_tip').hide();
                $('.ddz_dom_next_pklist').hide();
            }

            if(_rtnmsg.my.distp == 'pk'){
                $('.ddz_dom_now_tm').hide();
                $('.ddz_dom_act_btn').hide();
                $('.ddz_dom_my_pklist').show();
                $('.ddz_dom_my_pklist').html(_rtnmsg.my.sendpk);
                $('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);
            }else if(_rtnmsg.my.distp == 'tip'){
                $('.ddz_dom_now_tm').hide();
                $('.ddz_dom_act_btn').show();
                //$('.ddz_dom_my_pklist').hide();
                $('.ddz_dom_my_pklist').html('');
                var _tit = '<h1 class="layui-font-orange" style="font-weight:bold;display: inline-block">'+_rtnmsg.my.acttip+'</h1>';
                $('.ddz_dom_act_btn').html(_tit);
                xt_ddz.watingtime('my',_rtnmsg.my.countdown);
            }else{
                xt_ddz.watingtime('my',_rtnmsg.my.countdown);
                $('.ddz_dom_now_tm').show();
                $('.ddz_dom_act_btn').show();
                //$('.ddz_dom_my_pklist').hide();
                $('.ddz_dom_my_pklist').html('');
                xt_ddz.showbtn(_rtnmsg.my.btntp);
            }
        }else if(_data.msg._type == 10){
            var _rtnmsg  = _data.msg._data;
            $('.ddz_dom_top_mul').html(_rtnmsg.multiple_num);

            xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            if(_rtnmsg.up.distp == 'pk'){
                $('.ddz_dom_up_tm').hide();
                $('.ddz_dom_up_tip').hide();
                $('.ddz_dom_pkblk_up_tip').hide();
                $('.ddz_dom_up_pklist').show();
                $('.ddz_dom_up_pklist').html(_rtnmsg.up.sendpk);
            }else{
                $('.ddz_dom_up_tm').hide();
                $('.ddz_dom_up_tip').hide();
                $('.ddz_dom_pkblk_up_tip').hide();
                $('.ddz_dom_up_pklist').html('');
            }

            xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            if(_rtnmsg.next.distp == 'pk'){
                $('.ddz_dom_next_tm').hide();
                $('.ddz_dom_next_tip').hide();
                $('.ddz_dom_pkblk_next_tip').hide();
                $('.ddz_dom_next_pklist').show();
                $('.ddz_dom_next_pklist').html(_rtnmsg.next.sendpk);
            }else{
                $('.ddz_dom_next_tm').hide();
                $('.ddz_dom_next_tip').hide();
                $('.ddz_dom_pkblk_next_tip').hide();
                $('.ddz_dom_next_pklist').html('');
            }

            $('.ddz_dom_now_tm').hide();
            $('.ddz_dom_act_btn').show();
            $('.ddz_dom_my_pklist').show();
            $('.ddz_dom_my_pklist').html(_rtnmsg.my.sendpk);
            $('.ddz_dom_now_pklist').html(_rtnmsg.my.pokhtml);

            layer.alert(_rtnmsg.wininfo);
            xt_ddz.showbtn(6);

            //不要清理页面，一直到有人点击准备时再清理
            // xt_ddz.uppokernum(_rtnmsg.up.posid,_rtnmsg.up.poknum);
            // xt_ddz.uppokernum(_rtnmsg.next.posid,_rtnmsg.next.poknum);
            // xt_ddz.showlandlord('up','');
            // xt_ddz.showlandlord('next','');
            // xt_ddz.showlandlord('my','');
            // $('.ddz_dom_now_pklist').html('');
            //
            // $('.ddz_dom_up_tm').hide();
            // $('.ddz_dom_up_tip').show();
            // $('.ddz_dom_pkblk_up_tip').show();
            // $('.ddz_dom_up_pklist').hide();
            // xt_ddz.uptip('up','未准备');
            //
            // $('.ddz_dom_next_tm').hide();
            // $('.ddz_dom_next_tip').show();
            // $('.ddz_dom_pkblk_next_tip').show();
            // $('.ddz_dom_next_pklist').hide();
            // xt_ddz.uptip('next','未准备');
            //
            // $('.ddz_dom_now_tm').hide();
            // $('.ddz_dom_act_btn').show();
            // $('.ddz_dom_my_pklist').show();
            // $('.ddz_dom_my_pklist').html('');
            //
            // xt_ddz.showbtn(1);
            //
            // var _dzpoker = '<dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd><dd><img src="/images/pk/pkbm.png" width="80"></dd>';
            // $('.ddzm_top_dl').html(_dzpoker);
            //
            // layer.alert(_rtnmsg.wininfo);
        }else if(_data.msg._type == 11){
            var _rtnmsg  = _data.msg._data;
            if(_rtnmsg.dt == 'leavedet'){
                if(_data.msg._is_self == 1){
                    //layer.msg('跳转');
                    window.location.href = '/index.php/doudz';
                }else{
                    $('.ddz_dom_uinfo_pos_'+_rtnmsg.posid).find('.ddz_dom_uinfo').html(_rtnmsg.info);
                }
            }else{
                $('.ddz_dom_li_'+_rtnmsg.tbid).find('.ddz_dom_pos_'+_rtnmsg.posid).html(_rtnmsg.info);
            }
        }
    }
}

var xt_ddz = {

    startgame:function(){
        var _url    = '/index.php/doudz/startgame';
        $('.ddz_dom_my_pklist').html('');
        var ii      = layer.load();
        $.post(_url,{},function(res){
            if(res._state == 'ok')
            {
                // var _btnhtml    = '<button type="button" class="layui-btn layui-btn-radius layui-btn-sm layui-btn-disabled">已准备</button>';
                // $('.ddz_dom_act_btn').html(_btnhtml)

                layer.close(ii);
                var _data    = {};
                _data['type']    = 3;
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');
    },
    leavegame:function(){

        var ii      = layer.load();
        var _url    = '/index.php/doudz/leavetab';
        $.post(_url,{},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);
                var _data    = {};
                _data['type']    = 11;
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');
    },
    gettips:function(){
        var ii      = layer.load();
        var _url    = '/index.php/doudz/gettypepk';
        $.post(_url,{},function(res){
            if(res._state == 'ok')
            {
                //console.log(res);
                if(res._data.length > 0){
                    for(var i = 0; i < res._data.length; i++){
                        $('.ddz_dom_pkval_'+res._data[i]).removeClass('footer');
                        $('.ddz_dom_pkval_'+res._data[i]).addClass('selpk');
                    }
                }else{
                    layer.msg('你没有大于对方的牌!',{time: 500});
                }
                layer.close(ii);
            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');
    },
    cometab:function(obj){
        var _tabid = $(obj).attr('tabid');
        var _posid = $(obj).attr('posid');

        var ii      = layer.load();
        var _url    = '/index.php/doudz/cometab';
        $.post(_url,{tabid:_tabid,posid:_posid},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);
                var _data    = {};
                _data['type']    = 2;
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');

    },
    roblandlord:function(obj){

        var ii      = layer.load();
        var _url    = '/index.php/doudz/roblandlord';
        var _robval = $(obj).attr('robval');
        $.post(_url,{robval:_robval},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);
                var _data    = {};
                _data['type']    = 5;
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');

    },
    adddouble:function(obj){

        var ii      = layer.load();
        var _url    = '/index.php/doudz/adddouble';
        var _douval = $(obj).attr('douval');
        $.post(_url,{douval:_douval},function(res){
            if(res._state == 'ok')
            {
                layer.close(ii);
                var _data    = {};
                _data['type']    = 7;
                _data['data']    = res._msgkey;
                xt_wsk.send(_data);

            }else{
                layer.alert(res._msg);
                layer.close(ii);
            }

        },'json');

    },
    clickpk:function(obj){
        if($(obj).hasClass('footer')){
            $(obj).addClass('selpk');
            $(obj).removeClass('footer')
        }else{
            $(obj).addClass('footer');
            $(obj).removeClass('selpk')
        }
    },
    sendpk:function(tp=''){
        var _par = $('.ddz_dom_now_pklist');
        var _dd = _par.find('.selpk');
        if(_dd.length > 0 || tp =='notsend'){
            if(tp =='notsend'){
                var _pkv = 'notsend';
                if(_dd.length>0){
                    for(var i=0; i<_dd.length; i++){
                        $(_dd[i]).removeClass('selpk');
                        $(_dd[i]).addClass('footer');
                    }
                }
            }else{
                var _pkv = new Array();
                for(var i=0;i<_dd.length;i++){
                    _pkv[i] = $(_dd[i]).attr('pkval')
                }
            }

            var _url    = '/index.php/doudz/recvpkv';
            $.post(_url,{pkval:_pkv},function(res){
                if(res._state == 'ok')
                {
                    var _data    = {};
                    _data['type']    = 8;
                    _data['data']    = res._msgkey;
                    xt_wsk.send(_data);

                }else{
                    layer.alert(res._msg);
                    layer.close(ii);
                }

            },'json');
        }

    },
    showbtn:function(tp){

        if(tp == 0){
            $('.ddz_dom_act_btn').html('');
            return false;
        }
        var _btn = new Array();
        _btn[1] = '<button type="button" onclick="xt_ddz.startgame()" class="layui-btn layui-btn-radius layui-btn-sm">准备</button>';
        _btn[2] = '<button type="button" class="layui-btn layui-btn-radius layui-btn-sm layui-btn-disabled">已准备</button>';

        _btn[3] = '<button type="button" onclick="xt_ddz.roblandlord(this)" robval="1" class="layui-btn layui-btn-radius layui-btn-sm">抢地主</button>';
        _btn[4] = '<button type="button" onclick="xt_ddz.roblandlord(this)" robval="2"  class="layui-btn layui-btn-radius layui-btn-sm">不抢</button>';

        _btn[5] = '<button type="button" onclick="xt_ddz.adddouble(this)" douval="1" class="layui-btn layui-btn-radius layui-btn-sm">加倍X2</button>';
        _btn[6] = '<button type="button" onclick="xt_ddz.adddouble(this)" douval="2" class="layui-btn  layui-btn-radius layui-btn-sm">不加倍</button>';

        _btn[7] = '<button type="button" onclick="xt_ddz.sendpk(\'notsend\')" class="layui-btn  layui-btn-radius layui-btn-sm">不要</button>';
        _btn[8] = '<button type="button" onclick="xt_ddz.sendpk()" class="layui-btn  layui-btn-radius layui-btn-sm">出牌</button>';
        _btn[9] = '<button type="button" onclick="xt_ddz.gettips()" class="layui-btn  layui-btn-radius layui-btn-warm layui-btn-sm">提示</button>';

        _btn[10] = '<button type="button" onclick="xt_ddz.leavegame()" class="layui-btn layui-btn-radius layui-btn-sm">离开桌子</button>';
        _btn[11] = '<button type="button" onclick="xt_ddz.startgame()" class="layui-btn layui-btn-radius layui-btn-warm layui-btn-sm">再来一局</button>';

        var _rshtml = '';
        if(tp == 1){
            _rshtml = _btn[1] + _btn[10];
        }else if(tp == 2){
            _rshtml = _btn[2];
        }else if(tp == 3){
            _rshtml = _btn[3] + _btn[4];
        }else if(tp == 4){
            _rshtml = _btn[5] + _btn[6];
        }else if(tp == 5){
            _rshtml = _btn[7] + _btn[9] + _btn[8];
        }else if(tp == 6){
            _rshtml = _btn[11] + _btn[10];
        }

        $('.ddz_dom_act_btn').html(_rshtml);
    },

    watingtime:function(_tp,_tim= 120)
    {
        if(_tp == 'my'){

            clearInterval(timmy);
            var tim     = _tim;
            if(tim == 0){
                $('.ddz_dom_now_tm').html('');
                return ;
            }
            var timstr  = '<span class="layui-badge layui-bg-blue">'+tim+'</span>';
            $('.ddz_dom_now_tm').html(timstr);

            timmy = setInterval(function(){
                var time = --tim;
                var timstr  = '<span class="layui-badge layui-bg-blue">'+time+'</span>';
                $('.ddz_dom_now_tm').html(timstr);
                if(time <= 0 ) {
                    clearInterval(timmy);
                };
            }, 1000);

        }else if(_tp == 'up'){

            clearInterval(timup);
            var tim     = _tim;
            if(tim == 0){
                $('.ddz_dom_up_tm').html('');
                return ;
            }
            var timstr  = '<span class="layui-badge layui-bg-blue ddz_dom_tmval">'+tim+'</span>';
            $('.ddz_dom_up_tm').html(timstr);

            timup = setInterval(function(){
                var time = --tim;
                var timstr  = '<span class="layui-badge layui-bg-blue ddz_dom_tmval">'+time+'</span>';
                $('.ddz_dom_up_tm').html(timstr);
                if(time <= 0 ) {
                    clearInterval(timup);
                };
            }, 1000);

        }else if(_tp == 'next'){

            clearInterval(timnext);
            var tim     = _tim;
            if(tim == 0){
                $('.ddz_dom_next_tm').html('');
                return ;
            }
            var timstr  = '<span class="layui-badge layui-bg-blue ddz_dom_tmval">'+tim+'</span>';
            $('.ddz_dom_next_tm').html(timstr);

            timnext = setInterval(function(){
                var time = --tim;
                var timstr  = '<span class="layui-badge layui-bg-blue ddz_dom_tmval">'+time+'</span>';
                $('.ddz_dom_next_tm').html(timstr);
                if(time <= 0 ) {
                    clearInterval(timnext);
                };
            }, 1000);

        }
    },
    uptip:function(tp,tit=''){

        var _tit = '<h1 class="layui-font-orange" style="font-weight:bold;display: inline-block">'+tit+'</h1>';

        if(tit == ''){
            _tit = '';
        }
        if(tp == 'up'){
            $('.ddz_dom_up_tip').html(_tit);
            $('.ddz_dom_up_pklist').hide();
            $('.ddz_dom_pkblk_up_tip').show();
        }else if(tp == 'next'){
            $('.ddz_dom_next_tip').html(_tit);
            $('.ddz_dom_next_pklist').hide();
            $('.ddz_dom_pkblk_next_tip').show();
        }else{
            $('.ddz_dom_act_btn').html(_tit);
            $('.ddz_dom_now_tm').hide();
        }
    },
    showpk:function(tp,pkhtml){

        if(tp == 'up'){
            $('.ddz_dom_up_pklist').show();
            $('.ddz_dom_pkblk_up_tip').hide();
            $('.ddz_dom_up_pklist').html(pkhtml)
        }else if(tp == 'next'){
            $('.ddz_dom_next_pklist').show();
            $('.ddz_dom_pkblk_next_tip').hide();
            $('.ddz_dom_next_pklist').html(pkhtml)
        }else{
            $('.ddz_dom_my_pklist').html(pkhtml)
        }
    },
    uppokernum:function(posid,num){
        $('.ddz_dom_uinfo_pos_'+posid).find('.ddz_dom_pknum').html(num);
    },
    showlandlord:function(tp,isdz){
        var _str = '';
        if(isdz == 1){
            _str = '地主';
        }

        if(tp == 'my'){
            $('.ddz_dom_now_dz').html(_str);
        }else if(tp == 'next'){
            $('.ddz_dom_next_dz').html(_str);
        }else{
            $('.ddz_dom_up_dz').html(_str);
        }
    }

}