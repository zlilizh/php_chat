var xt_rtnic	= {
	
	send_rtn_out:function(obj){
		
		$(obj).attr('disabled','disabled');
		$(obj).text('提交中...');
		
		var _p			= $('.rt_ic_data');
		var _url		= xt_site.web_default_url + '/rtnstock/add_rtnic_sub';
		
		var	_rt_num		= _p.find('.rt_num').val();
		var _rt_day		= _p.find('.rt_day').val();
		var _rt_money	= _p.find('.rt_money').val();
		var _rt_remark	= _p.find('.rt_remark').val();
		var _rt_stk_id	= _p.find('.rt_stock_id').val();
		var _is_out		= $('#is_stock_out_id').val();

		if(_rt_num=='')
		{
			alert('退货单号不能为空!');
			
			$(obj).removeAttr('disabled');
			$(obj).text('提交');
			_p.find('.rt_num').focus();
			
			return false;	
		}
		
		if(_rt_day=='')
		{
			alert('退货日期不能为空!');
			
			$(obj).removeAttr('disabled');
			$(obj).text('提交');
			_p.find('.rt_day').focus();
			
			return false;	
		}
		
		var _arr_len	= _p.find('.rt_p_id').length;
		
		if(_arr_len>=1)
		{
			
		}else{
			alert('退货商品不能为空!');
			
			$(obj).removeAttr('disabled');
			$(obj).text('提交');
			
			return false;	
		}
		
		var _data_arr	= new Array();
		
		for(var i=0;i<_arr_len;i++)
		{
			//alert($(_p.find('.rt_p_id')[i]).val());
			
			_data_arr[i]					= {};
			
			_data_arr[i]['p_id']			= $(_p.find('.rt_p_id')[i]).val();
			_data_arr[i]['p_unit']			= $(_p.find('.rt_p_unit')[i]).val();
			_data_arr[i]['p_num']			= $(_p.find('.rt_p_number')[i]).val();
			_data_arr[i]['p_salesprice']	= $(_p.find('.rt_p_salesprice')[i]).val();
			_data_arr[i]['p_totalprice']	= $(_p.find('.rt_p_totalprice')[i]).val();
			_data_arr[i]['p_remark']		= $(_p.find('.rt_p_remark')[i]).val();
			
			if(_data_arr[i]['p_num']==''||_data_arr[i]['p_num']==0)
			{
				alert('退货商品数量不为空或0!');
				$(obj).removeAttr('disabled');
				$(obj).text('提交');
				$(_p.find('.rt_p_number')[i]).focus();
				
				return false;		
			}
			
			
		}
		
		var _farr_len	= _p.find('.rt_f_day').length;
		
		if(_farr_len>=1)
		{
			
			var _fin_arr	= new Array();
		
			for(var i=0;i<_farr_len;i++)
			{
				//alert($(_p.find('.rt_p_id')[i]).val());
				
				if($(_p.find('.rt_f_money')[i]).val()==''||$(_p.find('.rt_f_money')[i]).val()==0)
				{
					alert('退款金额不为空或0!');
					$(obj).removeAttr('disabled');
					$(obj).text('提交');
					$(_p.find('.rt_f_money')[i]).focus();
					
					return false;
				}
				
				_fin_arr[i]					= {};
				
				_fin_arr[i]['f_day']		= $(_p.find('.rt_f_day')[i]).val();
				_fin_arr[i]['f_payment']	= $(_p.find('.rt_f_payment')[i]).val();
				_fin_arr[i]['f_money']		= $(_p.find('.rt_f_money')[i]).val();
				_fin_arr[i]['f_remark']		= $(_p.find('.rt_f_remark')[i]).val();

			}
				
		}else{
			var _fin_arr	= '';
		}

		if(_rt_money==''||_rt_money==0)
		{
			if(confirm('你确定退货金额为"0"?'))
			{
				
			}else{
				_p.find('.rt_money').focus();
				$(obj).removeAttr('disabled');
				$(obj).text('提交');
				return false;
			}	
		}
		
		var _data	= {rt_stock_id:_rt_stk_id,rt_num:_rt_num,rt_day:_rt_day,rt_money:_rt_money,p_data:_data_arr,fin_data:_fin_arr,rt_remark:_rt_remark};
		
		$.post(_url,_data,function(res){
			if(res._state=='ok')
			{
				xt_pub.close_layer();
				//alert('退货成功!');
				if(_is_out==1)
				{
					location.href = xt_site.web_default_url + '/stockout/detial?itemid=' + _rt_stk_id;
				}else{
					location.href = xt_site.web_default_url + '/stock/detial?itemid=' + _rt_stk_id;
				}
				

			}else{
				$(obj).removeAttr('disabled');
				$(obj).text('提交');
				alert('退货失败!');
				return false;		
			}
		},'json');
		
	},
	
	add_fin:function(obj)
	{
		var _str	= '<tr>';
		_str		= _str + '<td><input type="text" name="pay_date[]" value="' + xt_site.stock_date + '" class="paydate_tx rt_f_day" onfocus="SelectDate(this,\'yyyy-MM-dd\')" readonly="readonly"></td>';
		_str		= _str + '<td><select name="payment[]" class="rt_f_payment"><option value="1">现金</option><option value="2">银行转账</option><option value="3">支票</option><option value="9">其它</option></select></td>';
		_str		= _str + '<td><input type="" name="pay_money[]" class="min_tx rt_f_money"></td>';
		_str		= _str + '<td><input type="" name="fremark[]" class="remark_tx rt_f_remark"></td>';
		_str		= _str + '<td><a onclick="xt_rtnic.del_fin(this)" href="javascript:void(0)">删除</a></td>';
		_str		= _str + '</tr>';
		
		var _p		= $(obj).closest('tr');
		
		_p.before(_str);
	},
	del_fin:function(obj)
	{
		$(obj).closest('tr').remove();	
	},
	
	del_ic:function(obj)
	{
		var _p			= $('.rt_ic_data');
		if(confirm('你确定要删除?'))
		{
			$(obj).closest('tr').remove();	
			if(_p.find('.rt_p_id').length < 1)
			{
				xt_pub.close_layer();	
			}
			
		}else{
			return false;	
		}
	},
	
	add_rtn_fin:function(obj)
	{
		var _url		= xt_site.web_default_url + '/rtnstock/add_rtn_fin';
		
		$.post(_url,'',function(res){
			if(res._state=='ok')
			{
				$(obj).closest('tr').before(res._html);
			}
		},'json');
	},
	
	up_rtn_fin:function(obj)
	{
		var _url		= xt_site.web_default_url + '/rtnstock/up_rtn_fin';
		var _ptr		= $(obj).closest('tr');
		var _pntr		= _ptr.next();
		var _fid		= $(obj).attr('data');
		
		$.post(_url,{fid:_fid},function(res){
			if(res._state=='ok')
			{
				_ptr.remove();
				_pntr.before(res._html);
			}
		},'json');	
	},
	
	cancel_rtn_fin:function(obj)
	{
		var _id		= $(obj).attr('data');
		if(_id>0)
		{
			var _url		= xt_site.web_default_url + '/rtnstock/rtn_fin';
			var _pptr		= $(obj).closest('.up_stock_fin');
			var _nptr		= _pptr.next();
			
			$.post(_url,{fid:_id},function(res){
			if(res._state=='ok')
			{
				_pptr.remove();
				_nptr.before(res._html);
			}else{
				alert('出错了!');	
			}
		},'json');	
			
		}else{
			$(obj).closest('.rgf_tr').remove();	
		}
	},
	
	cal_price:function(obj)
	{
		var _p 		= $(obj).closest('tr');
		var _num	= _p.find('.rt_p_number').val()>0?_p.find('.rt_p_number').val():0;
		var _pri	= _p.find('.rt_p_salesprice').val()>0?_p.find('.rt_p_salesprice').val():0;
		
		_pri		= _pri * 10000;
		
		
		var _tpri	= _pri * _num;
		_tpri		= Math.floor(_tpri);
		_tpri		= _tpri/10000;
		
		_p.find('.rt_p_totalprice').val(_tpri);
	},
	
	send_rtn_fin_sub:function(obj)
	{
		$(obj).attr('disabled','disabled');
		$(obj).text('提交中...');
		
		var _url		= xt_site.web_url+'/index.php/rtnstock/add_rtn_fin_sub';
		var _rtg_info	= $(obj).closest('.rtg_info');
		var _sid		= _rtg_info.attr('data');

		var _fid		= $(obj).attr('data');
		
		var _pptr		= $(obj).closest('.up_stock_fin');
		var _nptr		= _pptr.next();
		
		var _pdate		= _pptr.find('.pay_date').val();
		var _pment		= _pptr.find('.payment').val();
		var _pmoney		= _pptr.find('.pay_money').val();
		var _premark	= _pptr.find('.remark').val();

		var _reg 		= new RegExp("^([0-9]*)(\.[0-9]*)?$");
		
		if(_pmoney=='')
		{
			alert('请输入金额!');
			_pptr.find('.pay_money').focus();
			
			$(obj).removeAttr('disabled');
			$(obj).text('提交');
			
			return false;
		}else{
			
			if(!_reg.test(_pmoney))
			{
				alert('金额只能是数字!');
				_pptr.find('.pay_money').select();
				return false;	
			}
				
		}

		var _data		= {stock_id:_sid,pay_date:_pdate,paymoney:_pmoney,payment:_pment,remark:_premark,fid:_fid};
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_pptr.remove();
				_nptr.before(res._html);
				_rtg_info.find('.t_rtn_paid').text(res._cope.paid);
				
			}else{
				alert('数据出错!');	
			}
		});	
	},
	
	del_rtn_fin:function(obj)
	{
		
		if(confirm('确定要删除?'))
		{
			
		}else{
			return false;	
		}
		
		var _url		= xt_site.web_url+'/index.php/rtnstock/del_rtn_fin_sub';
		var _rtg_info	= $(obj).closest('.rtg_info');
		
		var _fid		= $(obj).attr('data');		
		var _pptr		= $(obj).closest('tr');

		var _data		= {fid:_fid};
		
		$.post(_url,_data,function(res){
			res		= eval("("+res+")");
			if(res._state=='ok')
			{
				_pptr.remove();
				_rtg_info.find('.t_rtn_paid').text(res._cope.paid);
				
			}else{
				alert('数据出错!');	
			}
		});	
	}
	
}

var xt_report	= {
	
	get_stock:function(obj)
	{
		var _pg		= $(obj).attr('data');
		var _url	= xt_site.web_default_url + '/report/ajax_get_stock';
		var _sort	= $('#stock_sort_id').val();
		var _dtime	= $('#stock_dtime_id').val();
		
		var _data	= {pg:_pg,ssort:_sort,dtime:_dtime};
		
		$.post(_url,_data,function(res){
			if(res._state=='ok')
			{
				$('.fbx_tl').find('.fbx_pt').remove();
				var _bf	= $('.fbx_tl').find('tr');
				_bf.after(res._html);
				$('.page').html(res._pg);
			}else{
				alert('未找到数据!')
			}
		},'json');
	},
	get_finance:function(obj)
	{
		var _pg		= $(obj).attr('data');
		var _url	= xt_site.web_default_url + '/report/ajax_get_finance';
		var _tp		= $('#finance_tp_id').val();
		var _dtime	= $('#finance_dtime_id').val();
		
		var _data	= {pg:_pg,tp:_tp,dtime:_dtime};
		
		$.post(_url,_data,function(res){
			if(res._state=='ok')
			{
				$('.fbx_tl').find('.fbx_pt').remove();
				var _bf	= $('.fbx_tl').find('tr');
				_bf.after(res._html);
				$('.page').html(res._pg);
			}else{
				alert('未找到数据!')
			}
		},'json');
	}

}