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
       xt_dom.stoptit();
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
                if(res._state == 'ok')
                {
                    var _data    = {};
                    _data['type']    = 2;
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
//        var abc = JSON.parse(msg.data);
//        console.log(abc);
// console.log(msg.data);
//
//        var _data    = eval("("+ msg['data'] +")");
       var _data    = JSON.parse(msg.data);
       var _upblk   = '';
       // console.log(_data);
       if(_data.msg._type ==2)
       {
            xt_dom.showTipmsg(_data);
           if(!$('.chatclass').hasClass('chathoverclass'))
           {
               $('.chatclass').text('N');
           }
       }else if(_data.msg._type ==1) {
           xt_dom.showChatmsg(_data);
           if(!$('.chatclass').hasClass('chathoverclass'))
           {
               $('.chatclass').text('N');
           }
       }else if(_data.msg._type == 4) {
           xt_dom.showGroup(_data);
           if(!$('.chatclass').hasClass('chathoverclass'))
           {
               $('.chatclass').text('N');
           }
       }else if(_data.msg._type == 5) {
           xt_dom.showFrirqq(_data);
       }else if(_data.msg._type == 7){
           xt_dom.showwithdraw(_data);
       }else{
           //console.log(_data);
           //alert(_data.msg._data);
           layer.open({
               title: '异常',
               content: _data.msg._data
           });
       }
       if(_data.msg._is_self == 1)
       {

       }else{
           xt_dom.showtit();
       }

   },
   
   tofooter:function()
   {
       var e=document.getElementById("mchat_id");
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

var xt_dom  = {
    showChatmsg:function(_data)
    {
        if(_data.msg._to_gid == 0){
            if(_data.msg._is_self == 1)
            {//更新发送者的短消息
                $('.chat_blk_'+ _data.msg._to_uid).find('.sim_msg').html(_data.msg._simmsg);
                _upblk   = $('.chat_blk_'+ _data.msg._to_uid);
            }else{//更新接收者的短消息

                $('.chat_blk_'+ _data.msg._form_uid).find('.sim_msg').html(_data.msg._simmsg);
                _upblk   = $('.chat_blk_'+ _data.msg._form_uid);
            }
        }else{
            $('._group_chat_'+ _data.msg._to_gid).find('.sim_msg').html(_data.msg._simmsg);
            _upblk   = $('._group_chat_'+ _data.msg._to_gid);
        }

        $('.mchat').append(_data.msg._data);

        var _blkhtml = _upblk.closest('.ublk_area').html();
        _upblk.closest('.ublk_area').remove();
        _blkhtml     = '<div class = "ublk_area">' + _blkhtml + '</div>';
        $('.chat_ulist_blk').prepend(_blkhtml);
        xt_wsk.tofooter();
        xt_pub.mouseoverts();
    },
    showwithdraw:function(_data)
    {
        if(_data.msg._to_gid == 0){
            if(_data.msg._is_self == 1)
            {//更新发送者的短消息
                $('.chat_blk_'+ _data.msg._to_uid).find('.sim_msg').html(_data.msg._simmsg);
                _upblk   = $('.chat_blk_'+ _data.msg._to_uid);
            }else{//更新接收者的短消息

                $('.chat_blk_'+ _data.msg._form_uid).find('.sim_msg').html(_data.msg._simmsg);
                _upblk   = $('.chat_blk_'+ _data.msg._form_uid);
            }
        }else{
            $('._group_chat_'+ _data.msg._to_gid).find('.sim_msg').html(_data.msg._simmsg);
            _upblk   = $('._group_chat_'+ _data.msg._to_gid);
        }

        if(_data.msg._is_self == 1)
        {
            var _showmsg    = _data.msg._data.sfmsg;
            var _msgid      = _data.msg._data.msgid;
        }else{
            var _showmsg    = _data.msg._data.tomsg;
            var _msgid      = _data.msg._data.msgid;
        }

        if($('.dom-msg-id_'+_msgid).length > 0)
        {
            $('.dom-msg-id_'+_msgid).html(_showmsg);
        }

        layer.closeAll();

    },
    showTipmsg:function(_data)
    {
        if(_data.msg._to_gid == 0)
        {
            $('.chat_blk_'+ _data.msg._form_uid).find('.chat_cmtn_num').html(_data.msg._data);
            $('.chat_blk_'+ _data.msg._form_uid).find('.chat_cmtn_num').show();
            $('.chat_blk_'+ _data.msg._form_uid).find('.sim_msg').html(_data.msg._simmsg);
            _upblk   = $('.chat_blk_'+ _data.msg._form_uid);
        }else{
            $('._group_chat_'+ _data.msg._to_gid).find('.chat_cmtn_num').html(_data.msg._data);
            $('._group_chat_'+ _data.msg._to_gid).find('.chat_cmtn_num').show();
            $('._group_chat_'+ _data.msg._to_gid).find('.sim_msg').html(_data.msg._simmsg);
            _upblk   = $('._group_chat_'+ _data.msg._to_gid);
        }

        var _blkhtml = _upblk.closest('.ublk_area').html();
        _upblk.closest('.ublk_area').remove();
        _blkhtml     = '<div class = "ublk_area">' + _blkhtml + '</div>';
        $('.chat_ulist_blk').prepend(_blkhtml);

    },
    showGroup:function(_data)
    {

        if(_data.msg._data.gtype == 1){
            $('.chat_ulist_blk').prepend(_data.msg._data.chtml);
            $('.friu_group_list').prepend(_data.msg._data.fhtml);
            if(_data.msg._is_self == 1){
                $('.chat_f_name').html(_data.msg._data.group_name);
                $('.mchat').html('');
            }
        }else if(_data.msg._data.gtype == 2 || _data.msg._data.gtype == 3){//添加新成员

            $('._group_chat_'+_data.msg._data.group_id).closest('.ublk_area').remove();
            $('._fgl_group_'+_data.msg._data.group_id).closest('.ublk_area').remove();

            if(_data.msg._data.isdel == 1){

            }else{
                $('.chat_ulist_blk').prepend(_data.msg._data.chtml);
                $('.friu_group_list').prepend(_data.msg._data.fhtml);
            }
        }

    },
    showGroupName:function()
    {

    },
    showFrirqq:function(_data)
    {
        var _rtnmsg = _data.msg._data;
        if(_rtnmsg._reqtp == 0) {
            $('.noticeclass').text(_rtnmsg._tznum);
        }else if(_rtnmsg._reqtp == 1){
            if(_data.msg._is_self ==1)
            {

            }else{
                $('.noticeclass').text(_rtnmsg._tznum);
            }

            $('.chat_ulist_blk').prepend(_rtnmsg._chathtml);
            $('.ful_ulist_dom').prepend(_rtnmsg._frihtml);

        }else if(_rtnmsg._reqtp == 2){
            $('.noticeclass').text(_rtnmsg._tznum);
        }else{

        }
    },
    showtit:function()
    {
        document.title = '你有新消息!';
    },
    stoptit:function()
    {
        document.title = '聊天';
    },

}

var xt_group = {
    click_check:function(obj)
    {
        var _chk_val    = $(obj).attr('checked');
        var _par        = $(obj).closest('li');
        var _uavt       = _par.find('.gbuavt').attr('data');
        var _unam       = _par.find('.gbuname').attr('data');
        var _uid        = _par.find('.gbradio').attr('data');

        if(_chk_val == 'checked')
        {
            var _html   = '<li data_uid="'+ _uid +'" class="group_user_'+ _uid +'"><span class="gbravt">';
            _html       += '<img src="'+ _uavt +'" height="20px"/>';
            _html       += '</span><span class="gbrname">';
            _html       += _unam;
            _html       += '</span><span class="gbact">';
            _html       += '<a href="javascript:void(0)" rel="删除" onclick="xt_group.remove_check(this)">X</a></span></li>';

            $('.add_group_user_list').append(_html);
        }else{
            $('.group_user_'+_uid).remove();
        }
        xt_group.group_msg();
    },
    remove_check:function(obj)
    {
        var _par    = $(obj).closest('li');
        var _uid    = _par.attr('data_uid');

        $('.group_user_check_'+_uid).removeAttr('checked');
        _par.remove();
        xt_group.group_msg();
    },
    group_msg:function()
    {
        var _len       = $('.add_group_user_list').find('li').length;
        if(_len >= 1){
            var _str    = '你已选择了'+_len+'联系人';
            $('.group_check_user_msg').html(_str);
        }else{
            var _str    = $('.group_check_user_msg').attr('dt');
            $('.group_check_user_msg').html(_str);
        }


    },
    sub_user:function(obj)
    {
        var _par    = $('.add_group_user_list').find('li');
        var _len    = _par.length;
        var _udata  = new Array();
        var _upgid  = $(obj).attr('upgid');
        var _acttp  = $(obj).attr('acttp');
        var _url    = xt_site.web_url+'/index.php/index/creatgroup';
        
        if(_len == 0)
        {
            return false;
        }
        for(var i=0;i<_len;i++)
        {
            _udata[i]  = $(_par[i]).attr('data_uid');
        }

        $.post(_url,{udata:_udata,upgid:_upgid,acttp:_acttp},function(res){
            if(res._state == 'ok')
            {
                parent.$('.ublk_on').removeClass('ublk_on');
                var _tsmsg  = '创建群组成功';
                if(_acttp == 'del')
                {
                    _tsmsg  = '删除成员成功';
                }
                layer.msg(_tsmsg,{time:1000},function(){
                    xt_pub.close_layer();
                    var _data    = {};
                    _data['type']    = 4;//创建群组消息
                    _data['data']    = res._sendmsg;
                    // console.log(_data);
                    // xt_wsk.send(_data);
                    parent.xt_wsk.send(_data);
                });
            }
        },'json');
        
    }
}


var xt_pub = {
    getchatlist:function(obj)
    {
        $('.chat_ulist_blk').show();
        $('.main_chat_blk').show();
        $('.chatclass').text('');
        $('.fri_ulist_blk').hide();
        $('.main_fri_blk').hide();


        $(obj).closest('ul').find('.friclass').removeClass('frihoverclass');
        $(obj).addClass('chathoverclass');
        //
        // var _url    = xt_site.web_url+'/index.php/index/getchatlist';
        // $.post(_url,{},function(res){
        //     if(res._state == 'ok')
        //     {
        //         $('.ulist').html(res._html);
        //         $('.main_dom').html(res._html2);
        //     }
        // },'json');

    },
    getflist:function(obj)
    {
        $('.chat_ulist_blk').hide();
        $('.main_chat_blk').hide();
        $('.fri_ulist_blk').show();
        $('.main_fri_blk').show();
        $(obj).closest('ul').find('.chatclass').removeClass('chathoverclass');
        $(obj).addClass('frihoverclass');
        // var _url    = xt_site.web_url+'/index.php/index/getfrilist';
        // $.post(_url,{},function(res){
        //     if(res._state == 'ok')
        //     {
        //         $('.ulist').html(res._html);
        //         $('.main_dom').html(res._html2);
        //     }
        // },'json');
    },
    getgroinfo:function(obj)
    {
        var _gid    = $(obj).attr('gid');
        var _url    = xt_site.web_url+'/index.php/index/getgroupdet';
        $('.fri_ulist_blk').find('.ublk_on').removeClass('ublk_on');
        $.post(_url,{gid:_gid},function(res){
            if(res._state == 'ok')
            {
                $(obj).addClass('ublk_on');
                $('.main_fri_blk').html(res._html);
            }
        },'json');
    },

    getfriinfo:function(obj)
    {
        var _uid    = $(obj).attr('uid');
        var _url    = xt_site.web_url+'/index.php/index/getfridet';
        $('.fri_ulist_blk').find('.ublk_on').removeClass('ublk_on');
        $.post(_url,{fuid:_uid},function(res){
            if(res._state == 'ok')
            {
                $(obj).addClass('ublk_on');
                $('.main_fri_blk').html(res._html);
            }
        },'json');
    },
    open_addfri:function()
    {

        layer.open({
            type: 1,
            area: ['500px','300px'],
            title: false,
            shadeClose: true,
            closeBtn: 0,
            content: $('.addfri_blk_dom')
        });
    },
    open_group:function(obj)
    {
            var _url        = $(obj).attr('data');
            layer.open({
                type: 2,
                area: ['600px','500px'],
                title: false,
                closeBtn: 0,
                shadeClose: true,
                content: [_url, 'no']
            }); 
    },
    open_ctlist:function()
    {
        var _url        = '/index.php/index/chatlist';
        layer.open({
            type: 2,
            area: ['608px','720px'],
            title: false,
            closeBtn: 1,
            shadeClose: false,
            content: [_url, 'no']
        });
    },
    open_frireq:function(obj)
    {

        $(obj).text('');
        var _url        = '/index.php/index/frireqlist';
        layer.open({
            title: false,
            area: ['550px','315px'],
            closeBtn:0,
            shadeClose: true,
            type: 2,
            content: [_url, 'no']
        });
    },
    open_meminfo:function(obj)
    {
        var _memid    = $(obj).attr('memid');
        var _url        = '/index.php/index/meminfo?memberid='+_memid
        layer.open({
            title: false,
            area: ['300px','328px'],
            closeBtn:0,
            shadeClose: true,
            type: 2,
            content: [_url, 'no']
        });
    },
    open_pwd:function()
    {
        var _url        = 'index.php/index/uppwd';
        layer.open({
            title: false,
            area: ['300px','170px'],
            closeBtn:0,
            shadeClose: true,
            type: 2,
            content: [_url, 'no']
        });
    },
    open_upuinfo:function()
    {
        var _url        = 'index.php/index/upinfo';
        layer.open({
            title: false,
            area: ['300px','290px'],
            closeBtn:0,
            shadeClose: true,
            type: 2,
            content: [_url, 'no']
        });
    },
    close_layer:function()
    {
        var index = parent.layer.getFrameIndex(window.name); 
        parent.layer.close(index); 
    },
    disableEmojiArea:function()
    {
        $('.emoji_area').show();
    },
    inputemoji:function(obj){
        var _tit    = $(obj).attr('title');
        var _src    = $(obj).attr('src');

        var _img    = '<img title="'+_tit+'" src="'+_src+'" class="emj">';

        var _oldtxt = $('.div_put_text').html();
        var _txt = _oldtxt + _img;
        $('.div_put_text').html(_txt);

        $('.emoji_area').hide();
        $('.div_put_text').focus();
    },
    withdraw:function(obj){
        layer.confirm('确定要撤回？', {
            btn: ['确定','取消'] //按钮
        }, function(){

            var _url    = xt_site.web_url+'/index.php/index/withdraw';
            var _data   = {};
            var _msgid  = $(obj).attr('mesgid');

            $.post(_url, {mesgid:_msgid},function(res){

                if(res._state == 'ok')
                {
                    var _tzdt   = {};
                    _tzdt['type']    = 7;
                    _tzdt['data']    = res._wdkey;

                    xt_wsk.send(_tzdt);
                }else{
                    layer.alert(res._msg);
                }

            },'json');

        }, function(){
            xt_pub.close_layer();
        });
    },
    get_msg:function(obj)
    {
        var _url    = xt_site.web_url+'/index.php/index/gethistory';
        var _data   = {};
        var _tp     = $(obj).attr('tp');
        var _fuid   = $(obj).attr('uid');
        var _gid    = $(obj).attr('gid');
        var _funm   = $(obj).attr('uname');
        var _fm     = $(obj).attr('fm');
        var _actahtml= '<a href="javascript:void(0)" onclick="xt_pub.open_group(this)" data="/index.php/index/addgroup">+</a>';

        $('.chat_ulist_blk').find('.ublk_on').removeClass('ublk_on');
        if(_tp == 2)
        {
            _data['tp']     = 2;
            _data['gid']   = _gid;
        }else{
            _data['tp']     = 1;
            _data['fuid']   = _fuid;  
        }
        
        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                // $(obj).find('.chat_cmtn_num').html(0);
                // $(obj).find('.chat_cmtn_num').hide();
                // $(obj).addClass('ublk_on');

                if(_tp ==2)
                {
                    $('._group_chat_'+_gid).addClass('ublk_on');
                    $('._group_chat_'+_gid).find('.chat_cmtn_num').html(0);
                    $('._group_chat_'+_gid).find('.chat_cmtn_num').hide();
                    _actahtml = '<a href="javascript:void(0)" onclick="xt_pub.open_group(this)" data="/index.php/index/addgroup?gid='+ _gid +'">+</a>';
                    if(res._isman == 1)
                    {
                        _actahtml = _actahtml + '<a href="javascript:void(0)" onclick="xt_pub.open_group(this)" data="/index.php/index/delgroupuser?groupid='+_gid+'">-</a>';
                    }

                }else{
                    $('.chat_blk_'+_fuid).addClass('ublk_on');
                    $('.chat_blk_'+_fuid).find('.chat_cmtn_num').html(0);
                    $('.chat_blk_'+_fuid).find('.chat_cmtn_num').hide();
                }

                $('.chat_f_name').html(_funm);
                $('.chat_f_act').html(_actahtml);
                $('.mchat').html('');
                $('.mchat').append(res._html);

                if(_fm == 2){
                    $('.chatclass').addClass('chathoverclass');
                    $('.friclass').removeClass('frihoverclass');
                    $('.chat_ulist_blk').show();
                    $('.chatclass').text('');
                    $('.main_chat_blk').show();
                    $('.fri_ulist_blk').hide();
                    $('.main_fri_blk').hide();
                }
            }
            xt_wsk.tofooter();
            xt_pub.mouseoverts();
        },'json');
    },
    mouseoverts:function(){
        $('.mcb_con').mouseover(function(){
            $(this).find('.mcbc_con_r_act').show();
        });
        $('.mcb_con').mouseout(function(){
            $(this).find('.mcbc_con_r_act').hide();
        });
    },
    get_more_msg:function(obj)
    {
        var _url    = xt_site.web_url+'/index.php/index/gethistory';
        var _data  = {};
        var _tp     = $(obj).attr('tp');
        var _fuid   = $(obj).attr('uid');
        var _gid    = $(obj).attr('gid');
        var _pg     = $(obj).attr('pg');
        if(_tp == 2)
        {
            _data['tp']     = 2;
            _data['gid']   = _gid;
            _data['pg']     = _pg;
        }else{
            _data['tp']     = 1;
            _data['fuid']   = _fuid;  
            _data['pg']     = _pg;
        }
        
        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                //$('.mchat').after(res._html);
                $(obj).closest('.mc_blk').after(res._html);
                $(obj).closest('.mc_blk').remove();
            }
        },'json');
    },
    show_phote:function(obj)
    {
        var _img_add    = $(obj).attr('big_addr');
        var _img    = '<img src='+ _img_add +' >';
        var _par    = $(obj).attr('par');
        var _acttk  = layer;
        if(_par == 1){
            _acttk  = parent.layer;
        }
        _acttk.open({
          type: 1,
          title: false,
          closeBtn: 0,
          area: ['auto'],
          skin: 'layui-layer-nobg', //没有背景色
          shadeClose: true,
          content: _img
        });
    },
    up_avt:function()
    {
       var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'up_mem_avt_id', // you can pass an id...
	//container: document.getElementById('container'), // ... or DOM Element itself
	url : 'index.php/index/upload',
	flash_swf_url : '../js/Moxie.swf',
	silverlight_xap_url : '../js/Moxie.xap',
	file_data_name: 'files',
	multipart_params: {
		up_ty:2
	},
	filters : {
		max_file_size : '2mb',
		mime_types: []
	},

	init: {

		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {

			});
			uploader.start();
			return false;
		},

		UploadProgress: function(up, file) {
		},
		
		FileUploaded: function(up, file,res){
			
			
			var _res	= jQuery.parseJSON(res.response);
			
			if(_res._state == 'ok'){
				$('#up_mem_avt_id').attr('src',_res._adr);
			}else{
				alert(_res._msg);
			}
			
			$('#'+ file.id).remove();
		},
		Error:function(up,err)
		{
			alert('上传失败');
		}

            }
        });
 
	uploader.init();  
    },
    send_pic:function()
    {
        var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'upload_pic_id', // you can pass an id...
	//container: document.getElementById('container'), // ... or DOM Element itself
	url : 'index.php/index/upload',
	flash_swf_url : '../js/Moxie.swf',
	silverlight_xap_url : '../js/Moxie.xap',
	file_data_name: 'files',
	multipart_params: {
		
	},
	filters : {
		max_file_size : '10mb',
		mime_types: []
	},

	init: {

		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				var _html	= '<div class="upfile_ys" id="'+ file.id +'">0%</div>';
				$('.upfile_blk').html(_html);
				
			});
			uploader.start();
			return false;
		},

		UploadProgress: function(up, file) {
			$('#'+ file.id).html(file.percent+"%");
			$('#'+ file.id).width(file.percent+"%");
		},
		
		FileUploaded: function(up, file,res){
			
			
			var _res	= jQuery.parseJSON(res.response);
			
			if(_res._state == 'ok'){
				
				var _data    = {}; 
			   _data['type']    = 3;
			   _data['data']    = _res._adr;
			   xt_wsk.send(_data);
				
			}else{
				alert(_res._msg);
			}
			
			$('#'+ file.id).remove();
		},
		Error:function(up,err)
		{
			alert('上传失败');
		}

            }
        });
 
	uploader.init();
    },
    send_annex:function()
    {
        var uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'upload_annex_id', // you can pass an id...
            //container: document.getElementById('container'), // ... or DOM Element itself
            url : 'index.php/index/uploadannex',
            flash_swf_url : '../js/Moxie.swf',
            silverlight_xap_url : '../js/Moxie.xap',
            file_data_name: 'files',
            multipart_params: {

            },
            filters : {
                max_file_size : '10mb',
                mime_types: []
            },

            init: {

                FilesAdded: function(up, files) {
                    plupload.each(files, function(file) {
                        var _html	= '<div class="upfile_ys" id="'+ file.id +'">0%</div>';
                        $('.upfile_blk').html(_html);

                    });
                    uploader.start();
                    return false;
                },

                UploadProgress: function(up, file) {
                    $('#'+ file.id).html(file.percent+"%");
                    $('#'+ file.id).width(file.percent+"%");
                },

                FileUploaded: function(up, file,res){


                    var _res	= jQuery.parseJSON(res.response);

                    if(_res._state == 'ok'){

                        var _data    = {};
                        _data['type']    = 6;
                        _data['data']    = _res._annexkey;
                        xt_wsk.send(_data);

                    }else{
                        alert(_res._msg);
                    }

                    $('#'+ file.id).remove();
                },
                Error:function(up,err)
                {
                    alert('上传失败');
                }

            }
        });

        uploader.init();
    },
    downfile:function(obj)
    {
        var id   = $(obj).attr('msgid');
        var _url = xt_site.web_url+'/index.php/index/downfile?msgid=' + id;
        document.getElementById('iframe').src = _url ;

    },
    logout:function()
    {
        layer.confirm('确定要退出？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href = '/index.php/login/outlogin'
        }, function(){
            xt_pub.close_layer();
        });
    },
    ctlstpg:function (ctn) {
        layui.use(['laypage', 'layer'], function(){
            var laypage = layui.laypage
                ,layer = layui.layer;

            //总页数大于页码总数
            laypage.render({
                elem: 'page'
                ,limit:20
                ,count: ctn //数据总数
                ,jump: function(obj,first){
                    // console.log(obj)
                    if(!first){
                        xt_pub.getctlist(obj.curr,2);
                    }
                }
            });
        });
    },
    getctlist:function(page,tp=1)
    {
        var _url    = '/index.php/index/getchatlist';
        var _data   = {};
        _data['pg']     = page;
        _data['seaval'] = $('#searchctlid').val();
// console.log(_data)
        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                $('.dom_ctl_list').html(res._html)
                if(tp == 1){
                    xt_pub.ctlstpg(res._count)
                }
            }
        },'json');
    },
}

var xt_mem = {
	
	uppwd:function(obj){
        var _url    = xt_site.web_url+'/index.php/index/subpwd';
        var _data  = {};
        var _par    = $(obj).closest('.ufptb');
        var _odpwd    = _par.find('.upuf_oldpwd').val();
        var _newpwd   = _par.find('.upuf_newpwd').val();
        var _cfpwd    = _par.find('.upuf_cfpwd').val();

        if(_odpwd == '' || _newpwd == '' || _cfpwd ==''){
            parent.layer.alert('数据有误');
            return;
        }

        _data['oldpwd']     = _odpwd;
        _data['newpwd']     = _newpwd;
        _data['cfpwd']      = _cfpwd;

        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                parent.layer.msg('密码修改成功');
                xt_pub.close_layer();
            }else{
                parent.layer.alert(res._msg);
            }
        },'json');
    },
    searchmem:function()
    {
        var _url    = xt_site.web_url+'/index.php/index/searchmem';
        var _uname  = $('.add_fri_inp').val();
        $.post(_url,{uname:_uname},function(res){
            if(res._state == 'ok')
            {
                $('.add_fri_res_dom').html(res._html);
            }else{
                var _str    = '<div class="adfdrs"><div class="adfdnfind">'+res._msg+'</div></div>';
                $('.add_fri_res_dom').html(_str);
            }
        },'json');
    },
    addfrimsg:function(obj)
    {
        var _upic = $(obj).attr('upic');
        var _unc = $(obj).attr('unc');
        var _uname = $(obj).attr('uname');
        // console.log(_upic);
        var _picstr = '<img clss="" src="'+_upic+'" height="50px"/>';
        $('.afmsg_bd_pic').html(_picstr);
        $('.afmsg_bd_unc').text(_unc);
        $('.afmsg_bd_uname').text(_uname);
        $('.send_addfri_uname').val(_uname);
        $('.afmsg_bd_msg').val('');
        layerind = parent.layer.open({
            type: 1,
            area: ['350px','180px'],
            title: false,
            shadeClose: true,
            closeBtn: 0,
            content: $('.afmsg_blk_dom')
        });
    },
    sendfrireq:function()
    {
        var _uname  = $('.send_addfri_uname').val();
        var _smsg   = $('.afmsg_bd_msg').val();
        var _data  = {};
        var _url    = xt_site.web_url+'/index.php/index/addfrireq';

        _data['uname']  = _uname;
        _data['smsg']   = _smsg;

        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                layer.msg(res._msg,{time:3000});
                layer.close(layerind)

                var _tzdt   = {};
                _tzdt['type']    = 5;
                _tzdt['data']    = res._reqid;

                parent.xt_wsk.send(_tzdt);
            }else{
                layer.msg(res._msg,{time:3000,icon:2});
                layer.close(layerind)
            }
        },'json');

    },
    handlefrireq:function(obj)
    {
        var _par    = $(obj).closest('.rtn_act_res_dom');
        var _reqid  = _par.attr('reqid');
        var _agrval = $(obj).attr('agrval');
        var _data   = {};
        var _url    = xt_site.web_url+'/index.php/index/handlefrireq';

        _data['reqid']      = _reqid;
        _data['agrval']     = _agrval;

        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                var _rtnstr = '<span class="rs"><span>'+ res._msg +'</span></span>';
                _par.html(_rtnstr);

                var _tzdt   = {};
                _tzdt['type']    = 5;
                _tzdt['data']    = res._reqid;

                parent.xt_wsk.send(_tzdt);
            }else{
                layer.msg(res._msg,{time:3000,icon:2});
            }
        },'json');
    },
    resetsrc:function()
    {
        $('.add_fri_inp').val('');
        $('.add_fri_res_dom').html('');
    },
    upuinfo:function(obj){
        var _url    = xt_site.web_url+'/index.php/index/subinfo';
        var _data   = {};
        var _par    = $(obj).closest('.ufptb');
        var _name   = _par.find('.upuf_name').val();
        var _email  = _par.find('.upuf_email').val();
        var _phone  = _par.find('.upuf_phone').val();
        var _chked  = _par.find('.upup_allow_sch').attr('checked')
        var _intro  = _par.find('.upuf_intro').val();

        if(_name ==''){
            parent.layer.alert('呢称不能为空');
            return;
        }

        _data['name']    = _name;
        _data['email']   = _email;
        _data['phone']   = _phone;
        _data['alwsc']   = _chked=='checked'?1:2;
        _data['intro']   = _intro;

        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                parent.layer.msg('信息修改成功');
                xt_pub.close_layer();
            }else{
                parent.layer.alert(res._msg);
            }
        },'json');
    },
    reqpage:function (ctn) {
        layui.use(['laypage', 'layer'], function(){
            var laypage = layui.laypage
                ,layer = layui.layer;

            //总页数大于页码总数
            laypage.render({
                elem: 'dom_req_page'
                ,limit:10
                ,count: ctn //数据总数
                ,jump: function(obj,first){
                    // console.log(obj)
                    if(!first){
                        xt_mem.getreqlist(obj.curr,2);
                    }
                }
            });
        });
    },
    getreqlist:function(page,tp=1,seaval='')
    {
        var _url    = '/index.php/index/getfrireqlist';
        var _data   = {};
        _data['pg']     = page;
        $.post(_url,_data,function(res){
            if(res._state == 'ok')
            {
                $('.dom_req_list').html(res._html)
                if(tp == 1){
                    xt_mem.reqpage(res._count)
                }
            }
        },'json');
    },
}

