
var xt_stock	= {

	keydown:function(e) {
        var theEvent 	= e || window.event;    
        var code 		= theEvent.keyCode || theEvent.which || theEvent.charCode;
		var _va		    = $('.ic_search_input').val();
		var _tp			= $('#date_type_id').val();
		$('#ic_kw_val_id').val(_va);
        if (code == 13) {    
           if(_va!='')
		   {
				if(_tp=='cu')   
				{
					xt_stock.get_ic_by_page(1,_va,_tp);  
				}else{
					xt_stock.get_ic_by_page(1,_va); 	
				}
				return false;   
			}else{
				if(_tp=='cu')   
				{
					xt_stock.get_ic_by_page(1,'',_tp);  
				}else{
					xt_stock.get_ic_by_page(1); 	
				}	
			}
            return false;    
        }    
        return true;    
    },
	
	search_date:function()
	{
		var _va		= $('.ic_search_input').val();
		var _tp		= $('#date_type_id').val();
		$('#ic_kw_val_id').val(_va);
		
		if(_va!='')
		{
			if(_tp=='cu')   
			{
				xt_stock.get_ic_by_page(1,_va,_tp);  
			}else{
				xt_stock.get_ic_by_page(1,_va); 	
			}
			   
		}else{
			if(_tp=='cu')   
			{
				xt_stock.get_ic_by_page(1,'',_tp);  
			}else{
				xt_stock.get_ic_by_page(1); 	
			}	
		}
		
	},
	
	get_ic:function(obj)
	{
		var _pg		= $(obj).attr('data');
		var _kw		= $('#ic_kw_val_id').val();
		var _tp		= $('#date_type_id').val();
		if(_kw=='')
		{
			if(_tp=='cu')
			{
				xt_stock.get_ic_by_page(_pg,'',_tp);	
			}else{
				xt_stock.get_ic_by_page(_pg);	
			}
		}else{
			if(_tp=='cu')
			{
				xt_stock.get_ic_by_page(_pg,_kw,_tp);
			}else{	
				xt_stock.get_ic_by_page(_pg,_kw);
			}
		}
	},
	
	get_ic_by_page:function(_pg,_kw,_tp)
	{
		var _url	= xt_site.web_url+'/index.php/stockpub/ajax_get_date';

		_url		= _url + '?p=' + _pg;
		var _p		= $('.fm_rl');
		var _pp		= _p.find('.page');
		var _data	= {kw:_kw,tp:_tp};
		if(_tp=='cu')
		{
			var _c_is_class	= $('#customer_is_class_id').val();
			_data.is_class	= _c_is_class;	
		}
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				if(_tp=='cu')
				{
					xt_stock.print_customer_data(res._data);
				}else{
					xt_stock.print_data(res._data);
				}
				_pp.html(res._phtml);
			}else{
				var _html	= '<tr class="fbx_pt"><td colspan="7">暂无数据</td></tr>';
				_p.find('.fbx_pt').remove();
				_p.find('tr').after(_html);
				_pp.html('');
			}
		});
	},
	
	print_data:function(data)
	{
		var _len	= data.length;
		var _html	= '';
		var _p		= $('.fm_rl');
		
		var _ic		= $('#ic_val_id');
		var _val	= _ic.val();
		if(_val=='')
		{
			_val_str	= '';
		}else{
			_val_str	=  xt_site.ic_split_str + _val + xt_site.ic_split_str ;	
		}
		
		//alert(data[0].model);
		for(var i=0;i<_len;i++)
		{
			_html	= _html + '<tr class="fbx_pt" onmouseover="$(this).addClass(\'on_hover\')" onmouseout="$(this).removeClass(\'on_hover\')">';
			
			var _tmp_val	=  data[i].id + xt_site.split_str + data[i].model + xt_site.split_str + data[i].number + xt_site.split_str + data[i].salesprice + xt_site.split_str + data[i].cost ;
			
			if(_val_str.length>0)
			{
				var _t_tmp_val	= xt_site.ic_split_str + _tmp_val + xt_site.ic_split_str;
				if(_val_str.indexOf(_tmp_val)==-1)
				{
					_html	= _html + '<td><input name="ic_val[]" value="'+ _tmp_val +'" onclick="xt_stock.checkbox_click(this)" class="ic_val" type="checkbox" /></td>';
				}else{
					_html	= _html + '<td><input name="ic_val[]" checked="checked" value="'+ _tmp_val +'" onclick="xt_stock.checkbox_click(this)" class="ic_val" type="checkbox" /></td>';
				}
			}else{
				_html	= _html + '<td><input name="ic_val[]" value="'+ _tmp_val +'" onclick="xt_stock.checkbox_click(this)" class="ic_val" type="checkbox" /></td>';
			}
			_html	= _html + '<td>'+ data[i].model + '</td>';
			_html	= _html + '<td>'+ data[i].lotnum + '</td>';
			_html	= _html + '<td>'+ data[i].salesprice + '</td>';
			_html	= _html + '<td>'+ data[i].cost + '</td>';
			_html	= _html + '<td>'+ data[i].number + '</td>';
			_html	= _html + '<td>'+ data[i].uptime + '</td>';
			_html	= _html + '</tr>';
		}
		
		_p.find('.fbx_pt').remove();
		_p.find('tr').after(_html);
	},
	
	print_customer_data:function(data)
	{
		var _len	= data.length;
		var _html	= '';
		var _p		= $('.fm_rl');
		
		var _ic		= $('#ic_val_id');
		var _val	= _ic.val();
		if(_val=='')
		{
			_val_str	= '';
		}else{
			_val_str	= ':'+ _val +':';	
		}
		
		//alert(data[0].model);
		for(var i=0;i<_len;i++)
		{
			_html	= _html + '<tr class="fbx_pt" onmouseover="$(this).addClass(\'on_hover\')" onmouseout="$(this).removeClass(\'on_hover\')">';
			_html	= _html + '<td><input name="ic_val[]" value="'+ data[i].id + xt_site.split_str + data[i].company +'('+ data[i].name +')' + xt_site.split_str + data[i].company + xt_site.split_str + data[i].name + ' " onclick="xt_stock.radio_click(this)" class="ic_val" type="radio" /></td>';
			_html	= _html + '<td>'+ data[i].company + '</td>';
			_html	= _html + '<td>'+ data[i].name + '</td>';
			_html	= _html + '<td>'+ data[i].phone + '</td>';
			_html	= _html + '<td>'+ data[i].telphone + '</td>';
			_html	= _html + '<td>'+ data[i].uptime + '</td>';
			_html	= _html + '</tr>';
		}
		
		_p.find('.fbx_pt').remove();
		_p.find('tr').after(_html);
	},
	
	checkbox_click:function(obj)
	{
		var _ic		= $('#ic_val_id');
		var _val	= _ic.val();
		var _ov		= $(obj).val();
		
		if(_val=='')
		{
			var _arr	= new Array();		
		}else{
			var _arr	= _val.split(xt_site.ic_split_str);
		}
		
		if($(obj).attr("checked"))
		{
			_arr.push(_ov);
		}else{
			for(var i=0;i<_arr.length;i++)
			{
				if(_arr[i]==_ov)
				{
					_arr.splice(i,1);
				}	
			}
		}
		
		if(_arr.length>0)
		{
			_new_val	= _arr.join(xt_site.ic_split_str);	
		}else{
			_new_val	= '';
		}
		
		_ic.val(_new_val);
	},
	
	radio_click:function(obj)
	{
		var _cu		= $('#customer_val_id');
		var _va		= $(obj).val();
		
		_cu.val(_va);
	},
	
	sub_data:function()
	{
		var _ic_val	= $('#ic_val_id').val();
		var _is_inq	= $('#is_inquiry_id').val();
		var _html	= '';
		if(_ic_val=='')
		{
				
		}else{
			var _arr	= _ic_val.split(xt_site.ic_split_str);
			var _len	= _arr.length;
			var _is_out	= parent.$('#is_stock_out_id').val();//出库入库区分
			
			
			for(var i=0;i<_len;i++)
			{
				var _tmp_arr	= _arr[i].split(xt_site.split_str);
				_html			= _html + '<tr onmouseover="$(this).addClass(\'on_hover\')" onmouseout="$(this).removeClass(\'on_hover\')">';
				_html			= _html + '<td>'+_tmp_arr[1]+'<input type="hidden" name="id[]" value="'+_tmp_arr[0]+'"></td>';
				_html			= _html + '<td><input name="unit[]" class="min_tx" type="" /></td>';
				if(_is_out==1){
					_html			= _html + '<td class="pt_n"><input onblur="xt_stock.total_price(this)" name="number[]" class="min_tx st_number" type="" /><span class="prompt"> <= '+_tmp_arr[2]+'</span></td>';
					_html			= _html + '<td class="pt_n"><input onblur="xt_stock.total_price(this)" name="salesprice[]" value="'+_tmp_arr[3]+'" class="min_tx st_salesprice" type="" /><span class="prompt"> >= '+_tmp_arr[3]+' >= '+ _tmp_arr[4] +'</span></td>';
				}else{
					_html			= _html + '<td><input onblur="xt_stock.total_price(this)" name="number[]" class="min_tx st_number" type="" /></td>';
					_html			= _html + '<td><input onblur="xt_stock.total_price(this)" name="salesprice[]" class="min_tx st_salesprice" type="" /></td>';
				}
				_html			= _html + '<td><input name="totalprice[]"  readonly="readonly" class="min_tx st_totalprice" type="" /></td>';
				_html			= _html + '<td><input name="premark[]" class="remark_tx" type="" /></td>';
				_html			= _html + '<td><a onclick="xt_stock.del_tr(this)" href="javascript:void(0)">删除</a></td>';
				_html			= _html + '</tr>';
			}
			
			var _pt		= parent.$('.p_table');
			_pt.find('.lt_tit').after(_html);
		}
		
		xt_pub.close_layer();
	},
	customer_sub_data:function()
	{
		var _cu_val	= $('#customer_val_id').val();
		var _is_sel	= $('#is_select_cus_id').val();
		if(_cu_val=='')
		{
			
		}else{
			var _arr	= _cu_val.split(xt_site.split_str);
			if(_is_sel==1)
			{
				parent.$('#customer_name_id').val(_arr[2]);
			}else{
				parent.$('#customer_name_id').val(_arr[1]);
			}
			
			parent.$('#customer_id_id').val(_arr[0]);
		}
		xt_pub.close_layer();
	},
	del_tr:function(obj)
	{
		var _p	= $(obj).closest('tr');
		_p.remove();
		xt_stock.calculate_price();
	},
	add_finance:function(obj)
	{
			var _html	= '<tr onmouseout="$(this).removeClass(\'on_hover\')" onmouseover="$(this).addClass(\'on_hover\')">';
          	_html		= _html + '<td><input type="text" readonly="readonly" onfocus="SelectDate(this,\'yyyy-MM-dd\')" class="paydate_tx" value="'+xt_site.stock_date+'" name="pay_date[]"></td>';
            _html		= _html + "<td><select name=\"payment[]\">" + xt_site.stock_option + "</select></td>";
            _html		= _html + '<td><input type="" onblur="xt_stock.calculate_price()" class="min_tx pay_money" name="pay_money[]"></td>';
            _html		= _html + '<td><input type="" class="remark_tx" name="fremark[]"></td>';
            _html		= _html + '<td><a href="javascript:void(0)" onclick="xt_stock.del_tr(this)">删除</a></td></tr>';
			
			$(obj).closest('tr').before(_html);
	},
	go_search:function(obj)
	{
		var _sp			= $(obj).closest('.search');
		var _stnum		= _sp.find('.stock_s_num').val();
		var _scustomer	= _sp.find('.stock_s_customer').val();
		var _scusid		= $('#customer_id_id').val();
		var _sstime		= _sp.find('.stock_s_stime').val();
		var _setime		= _sp.find('.stock_s_etime').val();
		var _ssmy		= _sp.find('.stock_s_smy').val();
		var _semy		= _sp.find('.stock_s_emy').val();
		var _spay		= _sp.find('.stock_pay_type').val();
		var _stp		= _sp.find('.stock_s_type').val();
		
		if(_stp==1)
		{
			var _url		= xt_site.web_url+'/index.php/stock?';
		}else{
			var _url		= xt_site.web_url+'/index.php/rtnstock/stock?';
		}

		_url			= _url + 'stnum=' + _stnum + '&customerid=' + _scusid + '&spay=' + _spay + '&stime=' + _sstime + '&etime=' + _setime + '&smoney=' + _ssmy + '&emoney=' + _semy;
		
		location.href	= _url;
	},
	go_report_search:function(obj)
	{
		var _sp			= $(obj).closest('.search');
		var _stnum		= _sp.find('.stock_s_num').val();
		var _scustomer	= _sp.find('.stock_s_customer').val();
		var _scusid		= $('#customer_id_id').val();
		var _sstime		= _sp.find('.stock_s_stime').val();
		var _setime		= _sp.find('.stock_s_etime').val();
		var _ssmy		= _sp.find('.stock_s_smy').val();
		var _semy		= _sp.find('.stock_s_emy').val();
		var _spay		= _sp.find('.stock_pay_type').val();
		var _url		= xt_site.web_url+'/index.php/stock/report?';
		
		_url			= _url + 'stnum=' + _stnum + '&customerid=' + _scusid + '&spay=' + _spay + '&stime=' + _sstime + '&etime=' + _setime + '&smoney=' + _ssmy + '&emoney=' + _semy;
		
		location.href	= _url;
	},
	go_out_search:function(obj)
	{
		var _sp			= $(obj).closest('.search');
		var _stnum		= _sp.find('.stock_s_num').val();
		var _scustomer	= _sp.find('.stock_s_customer').val();
		var _scusid		= $('#customer_id_id').val();
		var _sstime		= _sp.find('.stock_s_stime').val();
		var _setime		= _sp.find('.stock_s_etime').val();
		var _ssmy		= _sp.find('.stock_s_smy').val();
		var _semy		= _sp.find('.stock_s_emy').val();
		var _spay		= _sp.find('.stock_pay_type').val();
		var _stp		= _sp.find('.stock_s_type').val();
		
		if(_stp==1)
		{
			var _url		= xt_site.web_url+'/index.php/stockout?';
		}else{
			var _url		= xt_site.web_url+'/index.php/rtnstock/stockout?';
		}
		
		if(_scusid>0)
		{
			_url			= _url + 'stnum=' + _stnum + '&customerid=' + _scusid + '&spay=' + _spay + '&stime=' + _sstime + '&etime=' + _setime + '&smoney=' + _ssmy + '&emoney=' + _semy;
		}else{
			_url			= _url + 'stnum=' + _stnum + '&customer=' + _scustomer + '&spay=' + _spay + '&stime=' + _sstime + '&etime=' + _setime + '&smoney=' + _ssmy + '&emoney=' + _semy;
		}
		
		
		location.href	= _url;
	},
	go_out_report_search:function(obj)
	{
		var _sp			= $(obj).closest('.search');
		var _stnum		= _sp.find('.stock_s_num').val();
		var _scustomer	= _sp.find('.stock_s_customer').val();
		var _scusid		= $('#customer_id_id').val();
		var _sstime		= _sp.find('.stock_s_stime').val();
		var _setime		= _sp.find('.stock_s_etime').val();
		var _ssmy		= _sp.find('.stock_s_smy').val();
		var _semy		= _sp.find('.stock_s_emy').val();
		var _spay		= _sp.find('.stock_pay_type').val();
		var _url		= xt_site.web_url+'/index.php/stockout/report?';
		
		_url			= _url + 'stnum=' + _stnum + '&customerid=' + _scusid + '&spay=' + _spay + '&stime=' + _sstime + '&etime=' + _setime + '&smoney=' + _ssmy + '&emoney=' + _semy;
		
		location.href	= _url;
	},
	del_stock:function(obj)
	{
		var _sid		= $(obj).attr('data');
		var _tp			= $(obj).attr('tp');
		var _is_detial	= $(obj).attr('is_detial');
		var _url		= xt_site.web_url+'/index.php/stockpub/del_stock';
		
		if(_sid==''||_sid==0)
		{
			return false;	
		}
		
		if(_tp==1)
		{
			if(!confirm('你确定要删除此入库记录吗?如果删除,所对应的入库商品的库存会相应减少!'))
			{
				return false;
			}
				
		}else if(_tp==2){
			if(!confirm('你确定要删除此出库记录吗?如果删除,所对应的出库商品的库存会相应的增加!'))
			{
				return false;
			}
		}else if(_tp==3){
			if(!confirm('你确定要删除此退货的记录吗?如果删除,所对应的退货商品的库存会相应的增加!'))
			{
				return false;
			}
		}else{
			if(!confirm('你确定要删除此客户的退货吗?如果删除,所对应的退货商品的库存会相应的减小!'))
			{
				return false;
			}
		}
		
		$.post(_url,{stock_id:_sid,tp:_tp},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{

				if(_is_detial==1)
				{
					$(obj).closest('.rtg_det').remove();
					if($('.rtn_ic_list').find('.rtg_det').length > 0)
					{
						
					}else{
						$('.stock_info').find('.rtn_bs').remove();
						$('.rtn_ic_list').remove();
					}	
				}else if(_is_detial==2){
					//alert('删除退货成功!');
					location.href = xt_site.web_default_url + '/rtnstock/stockout';
				}else if(_is_detial==3){
					//alert('删除退货成功!');
					location.href = xt_site.web_default_url + '/rtnstock/stock';
				}else{
					if($('.icld_' + _sid).length>0)
					{
						$('.icld_' + _sid).remove();	
					}
					$(obj).closest('tr').remove();
				}
			}else{
				alert(res._msg);	
			}
		});
	},
	
	verify:function(obj)
	{
		var _p			= $(obj);
		var _ft			= $('.f_table');
		var _pt			= $('.p_table');
		var _reg 		= new RegExp("^([0-9]*)(\.[0-9]*)?$");
		var _is_out		= $('#is_stock_out_id').val();
		
		if(_is_out==1)
		{
			var _sn_msg	= '出库单号不能为空!';	
		}else{
			var _sn_msg	= '入库单号不能为空!';	
		}

		if(_p.find('.stock_num').val()=='')
		{
			alert(_sn_msg);
			_p.find('.stock_num').focus();
			return false;	
		}
		
		if($('#customer_id_id').val()=='')
		{
			alert('客户不能为空!');
			$('#customer_name_id').focus();
			return false;	
		}
		
		var _ft_m		= _ft.find('.pay_money');
		if(_ft_m.length>0)
		{
			var _ft_len	= _ft_m.length;
			for(var i=0;i<_ft_len;i++)
			{
				if($(_ft_m[i]).val()=='')
				{
					alert('金额不能为空!');
					$(_ft_m[i]).focus();
					$(_ft_m[i]).closest('tr').addClass('on_hover');
					return false;	
				}else{
				
					if(!_reg.test($(_ft_m[i]).val())){
						alert('请输入数字!');
						$(_ft_m[i]).select();
						$(_ft_m[i]).closest('tr').addClass('on_hover');
						return false;	
					}
					
				}
			}
		}

		var _pt_m		= _pt.find('.st_number');
		if(_pt_m.length>0)
		{
			var _pt_len	= _pt_m.length;
			for(var i=0;i<_pt_len;i++)
			{
				if($(_pt_m[i]).val()==''||$(_pt_m[i]).val()==0)
				{
					alert('商品数量不能为空!');
					$(_pt_m[i]).focus();
					$(_pt_m[i]).closest('tr').addClass('on_hover');
					return false;	
				}else{
					
					if(!_reg.test($(_pt_m[i]).val())){
						alert('请输入数字!');
						$(_pt_m[i]).select();
						$(_pt_m[i]).closest('tr').addClass('on_hover');
						return false;	
					}
				}
			}
		}else{
			alert('请添加商品!');
			return false;	
		}
		
		var _pt_p		= _pt.find('.st_salesprice');
		if(_pt_p.length>0)
		{
			var _pt_len	= _pt_p.length;
			for(var i=0;i<_pt_len;i++)
			{
				if($(_pt_p[i]).val()=='')
				{
					alert('商品单价不能为空!');
					$(_pt_p[i]).focus();
					$(_pt_p[i]).closest('tr').addClass('on_hover');
					return false;	
				}else{
					
					if(!_reg.test($(_pt_p[i]).val())){
						alert('请输入数字!');
						$(_pt_p[i]).select();
						$(_pt_p[i]).closest('tr').addClass('on_hover');
						return false;	
					}
				}
			}
		}
		
		var zj_m	= $('.a_total_cope').val();
		var a_pid	= $('.a_total_paid').val();
		
		if(Number(a_pid) > Number(zj_m))
		{
			alert('已付总计不能大于总计!');
			return false;	
		}
		
	},
	
	total_price:function(obj)
	{
		var _p		= $(obj).closest('tr');
		var _num	= _p.find('.st_number').val();
		var _price	= _p.find('.st_salesprice').val();
		
		_price		= _price * 10000;
		
		if( _num>=0 && _price>=0)
		{
			var _aprice	= _num * _price;
			_aprice 	= Math.round(_aprice);
			_aprice		= _aprice / 10000;
			_p.find('.st_totalprice').val(_aprice.toFixed(4));	
			
			xt_stock.calculate_price();
		}
	},
	//计算总前的总额
	calculate_price:function()
	{
		var _ft			= $('.f_table');
		var _pt			= $('.p_table');
		var _reg 		= new RegExp("^([0-9]*)(\.[0-9]*)?$");
		var _is_out		= $('#is_stock_out_id').val();
		
		var _all_fm		= 0;//财务总和
		var _all_pm		= 0; //商品总价
		
		var _pt_m		= _pt.find('.st_totalprice');
		var _tmp_pt_m	= 0;
		if(_pt_m.length>0)
		{
			var _pt_len	= _pt_m.length;
			for(var i=0;i<_pt_len;i++)
			{
				if($(_pt_m[i]).val()=='')
				{

				}else{
					
					if(!_reg.test($(_pt_m[i]).val())){
						alert('请输入数字!');
						$(_pt_m[i]).select();
						$(_pt_m[i]).closest('tr').addClass('on_hover');
						return false;
					}else{
						_tmp_pt_m	= Number($(_pt_m[i]).val())*10000;
						_all_pm		= Number(_all_pm) + Number(_tmp_pt_m);
						_tmp_pt_m	= 0;
					}
					
				}	
			}
		}
		
		var _ft_m		= _ft.find('.pay_money');
		var _tmp_ft_m	= 0;
		if(_ft_m.length>0)
		{
			var _ft_len	= _ft_m.length;
			for(var i=0;i<_ft_len;i++)
			{
				if($(_ft_m[i]).val()=='')
				{
						
				}else{
				
					if(!_reg.test($(_ft_m[i]).val())){
						alert('请输入数字!');
						$(_ft_m[i]).select();
						$(_ft_m[i]).closest('tr').addClass('on_hover');
						return false;	
					}else{
						_tmp_ft_m	= Number($(_ft_m[i]).val())*10000;
						_all_fm		= Number(_all_fm) + Number(_tmp_ft_m);
						_tmp_ft_m	= 0;
					}
					
				}
			}
		}

		_all_pm		= Math.round(_all_pm);
		_all_fm		= Math.round(_all_fm);
		var a_t_m	= _all_pm / 10000;
		var a_pid	= _all_fm / 10000;
		
		if(_is_out==0)
		{
			a_pid	= 0 - a_pid;	
		}
		
		var yh_m	= $('.a_total_discount').val();
		var ot_m	= $('.a_total_other').val();
		
		var zj_m	= Number(_all_pm) + Number(ot_m)*10000 - Number(yh_m)*10000;
		var yk_m	= Number(zj_m) - Number(_all_fm);
		zj_m		= zj_m / 10000;
		yk_m		= yk_m / 10000;
		
		
		$('.a_total_money').val(a_t_m.toFixed(4));
		$('.a_total_cope').val(zj_m.toFixed(4));
		$('.a_total_paid').val(a_pid.toFixed(4));
		$('.a_total_balance').val(yk_m.toFixed(4));
		
	},
	
	print_cope:function(data)
	{
		//alert(data.money);
		var _p	= $('.a_money');
		if(_p.length>0)
		{
			_p.find('.money').html(data.money);	
			_p.find('.discount').html(data.discount);	
			_p.find('.othermoney').html(data.othermoney);	
			_p.find('.cope').html(data.cope);	
			_p.find('.paid').html(data.paid);	
			_p.find('.balance').html(data.balance);	
		}
	},
	get_detial:function(obj)
	{
		var _url	= xt_site.web_url+'/index.php/stockpub/get_stock_detial';
		var _sid	= $(obj).attr('data');
		var _img		= $(obj).find('img').attr('src');
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;	
		}
		
		if(_img == '/templates/home/default/public/img/arrow_up.gif')
		{
			$('.icld_'+_sid).remove();
			$(obj).find('img').attr('src','/templates/home/default/public/img/arrow_down.gif');
			return false;
		}
		
		$.post(_url,{stock_id:_sid},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				$(obj).find('img').attr('src','/templates/home/default/public/img/arrow_up.gif');
				$(obj).closest('tr').after(res._html);
			}else{
				alert('数据有误!');	
			}
			
		});
		
	}
	
}

var xt_stock_up	= {

	up_stock:function(obj)
	{
		var _url	= xt_site.web_url+'/index.php/stockpub/up_stock';
		var _sid	= $(obj).attr('data');
		var _p		= $(obj).closest('.stock_info');
		var _tx		= $(obj).text();
		var _ssort	= $(obj).attr('sort');
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;	
		}
		
		if(_tx=='修改')
		{
			var _tp	= 1;
		}else if(_tx=='取消'){
			var _tp	= 2;
		}else{
			return false;
		}
		
		$.post(_url,{stock_id:_sid,tp:_tp,ssort:_ssort},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				if(_tp==1)
				{
					$(obj).text('取消');	
					_p.find('.info').remove();
				}else{
					$(obj).text('修改');	
					_p.find('.up_stock').remove();
				}
				_p.append(res._html);
                                laydate.render({
                                    elem: '.stock_date' //指定元素
                                });
			}else{
				alert('数据有误!');	
			}
			
		});
		
	},
	up_stock_sub:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/up_stock_sub';
		var _p			= $(obj).closest('.up_stock');
		var _dp			= $(obj).closest('.stock_info');
		var _id			= $(obj).attr('data');
		var _ssort		= $(obj).attr('sort');
		var _stock_num	= _p.find('.stock_num').val();
		var _stock_date	= _p.find('.stock_date').val();
		var _cid		= _p.find('.customer_id').val();
		
		if(_ssort==2)
		{
			var _sn_msg	= '出库单号不能为空!';	
		}else{
			var _sn_msg	= '进库单号不能为空!';	
		}
		
		if(_p.find('.stock_num').val()=='')
		{
			alert(_sn_msg);
			_p.find('.stock_num').focus();
			return false;	
		}
		
		if($('#customer_id_id').val()=='')
		{
			alert('客户不能为空!');
			$('#customer_name_id').focus();
			return false;	
		}
		
		var _data		= {id:_id,stock_num:_stock_num,stock_date:_stock_date,customer_id:_cid,ssort:_ssort};
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_dp.find('h2').find('.so_up').find('a').text('修改');
				_p.remove();
				_dp.append(res._html);
				
			}else{
				alert('数据出错!');
			}
		});
	},
	
	add_stock_finance:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/add_stock_finance';
		var _p			= $(obj).closest('tr');
		var _is_out		= $('#is_stock_out_id').val();
		
		$.post(_url,{is_out:_is_out},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_p.before(res._html);
			}
		});
	},
	
	sub_stock_finance:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/sub_stock_finance';
		var _sid		= $(obj).closest('.list').attr('data');
		//var _sid		= $('#sure_stock_id').val();
		var _ptable		= $(obj).closest('.up_stock_fin').closest('tr').closest('table');
		var _fid		= $(obj).attr('data');
		
		var _ptr		= $(obj).closest('.up_stock_fin').closest('tr');
		var _pptr		= $(obj).closest('.up_stock_fin');
		var _nptr		= _ptr.next();
		var _pdate		= _pptr.find('.pay_date').val();
		var _pment		= _pptr.find('.payment').val();
		var _pmoney		= _pptr.find('.pay_money').val();
		var _premark	= _pptr.find('.remark').val();
		var _is_out		= $('#is_stock_out_id').val();
		var _reg 		= new RegExp("^([0-9]*)(\.[0-9]*)?$");
		
		if(_pmoney=='')
		{
			alert('请输入金额!');
			_pptr.find('.pay_money').focus();
			return false;
		}else{
			
			if(!_reg.test(_pmoney))
			{
				alert('金额只能是数字!');
				_pptr.find('.pay_money').select();
				return false;	
			}
				
		}

		$(obj).attr('disabled','disabled');
		$(obj).text('提交中...');
		
		var _data		= {stock_id:_sid,pay_date:_pdate,paymoney:_pmoney,payment:_pment,remark:_premark,fid:_fid,is_out:_is_out};
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				if(_fid>0)
				{
					_ptr.remove();
					_nptr.before(res._html);
				}else{
					_ptr.remove();
					_ptable.find('.table_act').before(res._html);
				}
				
				//$('.stock_fin_act_money').html(res._act_money);
				xt_stock.print_cope(res._cope);
				
				
			}else{
				
				$(obj).removeAttr('disabled');
				$(obj).text('提交');
				
				alert('数据出错!');	
			}
		});
		
	},
	
	del_stock_finance:function(obj)
	{
		var _fid		= $(obj).attr('data');
		var _is_out		= $('#is_stock_out_id').val();
		var _url		= xt_site.web_url+'/index.php/stockpub/del_finance';
		
		if(_fid==0||_fid=='')
		{
			return false;	
		}
		
		if(!confirm('你确定要删除此财务记录?删除之后不可恢复!'))
		{
			return false;	
		}
		
		$.post(_url,{fid:_fid,is_out:_is_out},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				$(obj).closest('tr').remove();
				xt_stock.print_cope(res._cope);
				//$('.stock_fin_act_money').html(res._act_money);	
			}else{
				alert('删除失败!');	
			}
		});
		
	},
	
	cancel_stock_act:function(obj)
	{
		var _pptr		= $(obj).closest('.up_stock_fin');
		var _ptr		= $(obj).closest('.up_stock_fin').closest('tr');
		var _nptr		= _ptr.next();
		var _fid		= $(obj).attr('data');
		var _is_out		= $('#is_stock_out_id').val();
		var _url		= xt_site.web_url+'/index.php/stockpub/cancel_up_stock_finance';
		
		if(_fid>0)
		{	
			$.post(_url,{fid:_fid,is_out:_is_out},function(res){
				res		= eval("("+res+")");
				if(res._state=='ok')
				{
					_ptr.remove();
					_nptr.before(res._html);
				}else{
					alert('出错!');	
				}
			});
			
		}else{
			_pptr.closest('tr').remove();
		}

	},
	
	up_stock_finance:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/up_stock_finance';
		var _ptr		= $(obj).closest('tr');
		var _fid		= $(obj).attr('data');
		
		$.post(_url,{fid:_fid},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_ptr.removeAttr('onmouseover');
				_ptr.removeAttr('onmouseout');
				_ptr.removeClass('on_hover');
				_ptr.addClass('on_up');
				_ptr.find('td').remove();
				_ptr.html(res._html);	
			}else{
				
			}
		});
	},
	
	ad_rad:function(){
//		$(".add_pg").fancybox({
//			'modal':false,
//			'padding':0,
//			'overlayShow':true,
//			'overlayColor':'#EEE',
//			'hideOnOverlayClick':false,
//			'hideOnContentClick':false,
//			'enableEscapeButton':false,
//			'showCloseButton':false,
//			'centerOnScroll':true,
//			'autoScale':true
//		});
	},
	
	up_remark:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/up_stock_remark';
		var _sid		= $(obj).closest('.list').attr('data');
		var _tx			= $(obj).text();
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;
		}
		
		if(_tx=='修改')
		{
			var _tp	= 1;
		}else if(_tx=='取消'){
			var _tp	= 2;
		}else{
			return false;
		}

		$.post(_url,{stock_id:_sid,tp:_tp},function(res){
			res		= eval("("+res+")");
			
			if(res._state=='ok')
			{
				if(_tp==1)
				{
					$(obj).text('取消');		
				}else{
					$(obj).text('修改');	
				}
				$(obj).closest('.list').find('.ltable').html(res._html);
			}else{
				alert(res._msg);
				return false;	
			}
					
		})
		
	},
	
	sub_up_remark:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/sub_up_stock_remark';
		var _pli		= $(obj).closest('.list');
		var _sid		= _pli.attr('data');
		var _remark		= _pli.find('.det_remark').find('.remark').val();
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;
		}


		$.post(_url,{stock_id:_sid,remark:_remark},function(res){
			res		= eval("("+res+")");
			
			if(res._state=='ok')
			{
				_pli.find('.lt').find('span').find('a').text('修改');
				$(obj).closest('.list').find('.ltable').html(res._html);
			}else{
				alert(res._msg);
				return false;	
			}
					
		})		
	},

	
	up_stock_money:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/up_stock_money';
		var _sid		= $(obj).closest('.list').attr('data');
		var _tx			= $(obj).text();
		var _is_out		= $('#is_stock_out_id').val();
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;
		}
		
		if(_tx=='修改')
		{
			var _tp	= 1;
		}else if(_tx=='取消'){
			var _tp	= 2;
		}else{
			return false;
		}

		$.post(_url,{stock_id:_sid,tp:_tp,is_out:_is_out},function(res){
			res		= eval("("+res+")");
			
			if(res._state=='ok')
			{
				if(_tp==1)
				{
					$(obj).text('取消');		
				}else{
					$(obj).text('修改');	
				}
				$(obj).closest('.list').find('.ltable').html(res._html);
			}else{
				alert(res._msg);
				return false;	
			}
					
		})
	},
	
	sub_up_stock_money:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/sub_up_stock_money';
		var _pli		= $(obj).closest('.list');
		var _sid		= _pli.attr('data');
		var _p			= $('.au_money');
		var _is_out		= $('#is_stock_out_id').val();
		
		var _discount	= _p.find('.a_total_discount').val();
		var _othermoney	= _p.find('.a_total_other').val();
		
		//alert(_discount);
		
		if(_sid==''||_sid==0)
		{
			alert('数据有误!');
			return false;
		}

		$.post(_url,{stock_id:_sid,discount:_discount,othermoney:_othermoney,is_out:_is_out},function(res){
			res		= eval("("+res+")");
			
			if(res._state=='ok')
			{
				_pli.find('.lt').find('span').find('a').text('修改');
				$(obj).closest('.list').find('.ltable').html(res._html);
			}else{
				alert(res._msg);
				return false;	
			}
					
		})			
	},
	
	up_stock_calculate_price:function(obj)
	{
		var _p			= $(obj).closest('.au_money');
		var _money		= Number(_p.find('.a_total_money').val())*10000;
		var _discount	= Number(_p.find('.a_total_discount').val())*10000;
		var _othermoney	= Number(_p.find('.a_total_other').val())*10000;
		var _cope		= Number(_p.find('.a_total_cope').val())*10000;
		var _paid		= Number(_p.find('.a_total_paid').val())*10000;
		var _balance	= Number(_p.find('.a_total_balance').val())*10000;
		
		_cope			= _money - _discount + _othermoney;
		_balance		= _cope - Math.abs(_paid);
		
		_cope			= Math.round(_cope);
		_cope			= _cope / 10000;
		
		_balance		= Math.round(_balance);
		_balance		= _balance / 10000;
		//alert(_balance);
		_p.find('.a_total_cope').val(_cope.toFixed(4));
		_p.find('.a_total_balance').val(_balance.toFixed(4));
		
	}
	
	

}

var xt_stock_ic	= {
	
	add_ic:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/add_ic';
		var _p			= $(obj).closest('tr');
		var _is_out		= $('#is_stock_out_id').val();
		
		$.post(_url,{is_out:_is_out},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_p.before(res._html);
				xt_stock_up.ad_rad();
			}
		});	
	},
	
	up_stockic:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/up_stock_ic';
		var _ptr		= $(obj).closest('tr');
		var _fid		= $(obj).attr('data');
		var _is_out		= $('#is_stock_out_id').val();
		
		$.post(_url,{fid:_fid,is_out:_is_out},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_ptr.removeAttr('onmouseover');
				_ptr.removeAttr('onmouseout');
				_ptr.removeClass('on_hover');
				_ptr.addClass('on_up');
				_ptr.find('td').remove();
				_ptr.html(res._html);
				xt_stock_up.ad_rad();	
			}else{
				
			}
		});
	},
	
	sub_stockic:function(obj)
	{
		var _url		= xt_site.web_url+'/index.php/stockpub/sub_stock_ic';
		var _sid		= $(obj).closest('.list').attr('data');
		var _ptable		= $(obj).closest('.up_stock_fin').closest('tr').closest('table');
		var _fid		= $(obj).attr('data');
		
		var _ptr		= $(obj).closest('.up_stock_fin').closest('tr');
		var _pptr		= $(obj).closest('.up_stock_fin');
		var _nptr		= _ptr.next();
		
		var _ic_id		= _pptr.find('.ic_id').val();
		var _unit		= _pptr.find('.unit').val();
		var _number		= _pptr.find('.number').val();
		var _salesprice	= _pptr.find('.salesprice').val();
		var _premark	= _pptr.find('.premark').val();
		var _totalprice	= _pptr.find('.totalprice').val();
		var _is_out		= $('#is_stock_out_id').val();
		var _reg 		= new RegExp("^[0-9]*$");
		
		if(_ic_id == '')
		{
			alert('请先选择商品!');
			_pptr.find('.ic_name').focus();
			return false;	
		}
		
		if(_number=='')
		{
			alert('数量不能为空!');
			_pptr.find('.number').focus();
			return false;	
		}else{
			
			if(!_reg.test(_number))
			{
				alert('数量只能是数字!');
				_pptr.find('.number').select();
				return false;	
			}
			
		}

		$(obj).attr('disabled','disabled');
		$(obj).text('提交中...');

		var _data		= {stock_id:_sid,ic_id:_ic_id,iunit:_unit,number:_number,salesprice:_salesprice,remark:_premark,fid:_fid,is_out:_is_out,totalprice:_totalprice};
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				if(_fid>0)
				{
					_ptr.remove();
				}else{
					_ptr.remove();
				}
				_nptr.before(res._html);
				xt_stock.print_cope(res._cope);
			}else{
				
				$(obj).removeAttr('disabled');
				$(obj).text('提交');
				
				alert('数据出错!');	
			}
		});
		
	},
	
	cancel_stock_act:function(obj)
	{
		var _pptr		= $(obj).closest('.up_stock_fin');
		var _ptr		= $(obj).closest('.up_stock_fin').closest('tr');
		var _nptr		= _ptr.next();
		var _fid		= $(obj).attr('data');
		var _url		= xt_site.web_url+'/index.php/stockpub/cancel_up_stock_ic';
		
		if(_fid>0)
		{	
			$.post(_url,{fid:_fid},function(res){
				res		= eval("("+res+")");
				if(res._state=='ok')
				{
					_ptr.remove();
					_nptr.before(res._html);
				}else{
					alert('出错!');	
				}
			});
			
		}else{
			_pptr.closest('tr').remove();
		}

	},
	
	sub_ic_val:function(obj)
	{
		var _p		= $(obj).closest('.fbx_list');
		var _ival	= $('#ic_val_id').val();
		var _rtid	= $('#return_val_id_id').val();
		var _is_out	= parent.$('#is_stock_out_id').val();
		
		var _pp		= parent.$('#' + _rtid);
		
		if(_ival.length>0)
		{
			var _arr	= _ival.split(xt_site.split_str);
			_pp.find('.ic_id').val(_arr[0]);
			_pp.find('.ic_name').val(_arr[1]);
			if(_is_out==1)
			{
				_pp.find('.number').val('');
				_pp.find('.number_span').html('<=' + _arr[2]);
				_pp.find('.salesprice').val(_arr[3]);
				_pp.find('.totalprice').val('');
				_pp.find('.salesprice_span').html('>=' + _arr[3] + '>=' + _arr[4]);
			}else{
				_pp.find('.number_span').html(_arr[2]);
			}

		}else{
			
		}
		
		xt_pub.close_layer();
	},
	
	get_ic_by_page:function(_pg,_kw,_tp)
	{
		var _url	= xt_site.web_url+'/index.php/stockpub/up_ajax_get_date';

		_url		= _url + '?p=' + _pg;
		var _p		= $('.fm_rl');
		var _pp		= _p.find('.page');
		var _data	= {kw:_kw,tp:_tp};
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				xt_stock_ic.print_data(res._data);
				_pp.html(res._phtml);
			}else{
				var _html	= '<tr class="fbx_pt"><td colspan="7">暂无数据</td></tr>';
				_p.find('.fbx_pt').remove();
				_p.find('tr').after(_html);
				_pp.html('');
			}
		});
	},
	
	print_data:function(data)
	{
		var _len	= data.length;
		var _html	= '';
		var _p		= $('.fm_rl');
		
		var _ic		= $('#ic_val_id');
		var _val	= _ic.val();
		if(_val=='')
		{
			_val_str	= '';
		}else{
			_val_str	= _val;	
		}
		
		for(var i=0;i<_len;i++)
		{
			_html	= _html + '<tr class="fbx_pt" onmouseover="$(this).addClass(\'on_hover\')" onmouseout="$(this).removeClass(\'on_hover\')">';
			
			var _tmp_val	= data[i].id + xt_site.split_str + data[i].model + xt_site.split_str + data[i].number + xt_site.split_str + data[i].salesprice + xt_site.split_str + data[i].cost ;
			
			if(_val_str.length>0 && _val_str == _tmp_val)
			{
				_html	= _html + '<td><input name="ic_val[]" checked="checked" value="'+ _tmp_val +'" onclick="xt_stock_ic.click_radio(this)" class="ic_val" type="radio" /></td>';
			}else{
				_html	= _html + '<td><input name="ic_val[]" value="'+_tmp_val +'" onclick="xt_stock_ic.click_radio(this)" class="ic_val" type="radio" /></td>';
			}
			_html	= _html + '<td>'+ data[i].model + '</td>';
			_html	= _html + '<td>'+ data[i].lotnum + '</td>';
			_html	= _html + '<td>'+ data[i].salesprice + '</td>';
			_html	= _html + '<td>'+ data[i].cost + '</td>';
			_html	= _html + '<td>'+ data[i].number + '</td>';
			_html	= _html + '<td>'+ data[i].uptime + '</td>';
			_html	= _html + '</tr>';
		}
		
		_p.find('.fbx_pt').remove();
		_p.find('tr').after(_html);
	},
	
	get_ic:function(obj)
	{
		var _pg		= $(obj).attr('data');
		var _kw		= $('#ic_kw_val_id').val();
		
		if(_kw=='')
		{
			xt_stock_ic.get_ic_by_page(_pg);	
		}else{
			xt_stock_ic.get_ic_by_page(_pg,_kw);
		}
	},
	click_radio:function(obj)
	{
		var _val	= $(obj).val();
		$('#ic_val_id').val(_val);
	},
	keydown:function(e) {
        var theEvent 	= e || window.event;    
        var code 		= theEvent.keyCode || theEvent.which || theEvent.charCode;
		var _va		    = $('.ic_search_input').val();
		$('#ic_kw_val_id').val(_va);
		
        if (code == 13) {    
           if(_va!='')
		   {
				xt_stock_ic.get_ic_by_page(1,_va);
			}else{
				xt_stock_ic.get_ic_by_page(1);	
			}
        }    
        return true;    
    },
	on_search:function()
	{
		var _va		    = $('.ic_search_input').val();
		$('#ic_kw_val_id').val(_va);
		if(_va!='')
		{
			xt_stock_ic.get_ic_by_page(1,_va);
		}else{
			xt_stock_ic.get_ic_by_page(1);	
		}
	},
	del_stock_ic:function(obj)
	{
		var _fid		= $(obj).attr('data');
		var _is_out		= $('#is_stock_out_id').val();
		var _url		= xt_site.web_url+'/index.php/stockpub/del_ic';
		
		if(_fid==0||_fid=='')
		{
			return false;	
		}
		
		if(_is_out==1)
		{
			var _c_msg	= '你确定要删除此产品出库记录?删除之后不可恢复!';	
		}else{
			var _c_msg	= '你确定要删除此产品入库记录?删除之后不可恢复!';
		}
		
		if(!confirm(_c_msg))
		{
			return false;	
		}
		
		$.post(_url,{fid:_fid,is_out:_is_out},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				$(obj).closest('tr').remove();
				xt_stock.print_cope(res._cope);
			}else{
				alert('删除失败!');	
			}
		});
	},
	total_price:function(obj)
	{
		var _pt		= $(obj).closest('table');
		var _num	= _pt.find('.number').val();
		var _price	= _pt.find('.salesprice').val();

		if(_num >0 && _price >0)
		{
			var _tprice		= Number(_num) * Number(_price) * 10000;
			_tprice			= Math.round(_tprice);
			_tprice			= Number(_tprice) / 10000;
			//alert(_tprice);
			_pt.find('.totalprice').val(_tprice);
		}else{
			_pt.find('.totalprice').val('0');	
		}
		
	}
		
}

var xt_inquiry	= {
	
	verify:function(obj)
	{
		var _p		= $(obj);
		var _pt		= $('.p_table');
		var _ptm	= _pt.find('.p_t_model');
		
		if(_p.find('.copmany_name').val()=='')
		{
			alert('公司名称不能为空!');
			_p.find('.copmany_name').focus();
			return false;	
		}

		var _len	= _ptm.length;
		
		if(_len < 1)
		{
			alert('请先添加询价型号/名称!');
			return false;	
		}
		
		for(var i=0;i<_len;i++)
		{
			if($(_ptm[i]).val()=='')
			{
				alert('型号/名称不能为空!');
				$(_ptm[i]).focus();
				return false;	
			}	
		}
		
		
	},
	
	add_tr:function(obj)
	{
		var _str	= '<tr onmouseout="$(this).removeClass(\'on_hover\')" onmouseover="$(this).addClass(\'on_hover\')" >';
            _str	= _str + '<td><input name="model[]" class="min_tx p_t_model" type="" /></td>';
        	_str	= _str + '<td><input name="firm[]" class="min_tx " type="" /></td>';
            _str	= _str + '<td><input name="lotnum[]" class="min_tx " type="" /></td>';
            _str	= _str + '<td><input name="package[]" class="min_tx " type="" /></td>';
            _str	= _str + '<td><input name="number[]" class="min_tx " type="" /></td>';
            _str	= _str + '<td><input name="price[]" class="min_tx " type="" /></td>';
            _str	= _str + '<td><input type="" class="remark_tx" name="premark[]"></td>';
            _str	= _str + '<td><a onclick="xt_inquiry.del_tr(this)" href="javascript:void(0)">删除</a></td></tr>';	
		$(obj).closest('tr').before(_str);
	},
	del:function(obj)
	{
		
		var _fid		= $(obj).attr('data');
		var _url		= xt_site.web_url+'/index.php/inquiry/del';
		
		if(_fid==0||_fid=='')
		{
			return false;	
		}
		
		if(!confirm('确定删除?'))
		{
			return false;	
		}
		
		$.post(_url,{itemid:_fid},function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				$(obj).closest('tr').remove();
			}else{
				alert('删除失败!');	
			}
		});
		
	}
	
	
}