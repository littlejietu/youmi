$(function(){
	if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }

    var site_name = get_user_data_from_local('site_name');
    $('#site_name').html(site_name);
    var cashier_name = get_user_data_from_local('name');
    $('#cashier_name').html(cashier_name);

    var gun_oil_no = null;

    sendPostData({}, ApiUrl+"cashier/oil", function(result){
    	if(result.code=='SUCCESS'){
	    	gun_oil_no = result.data.site.gun_oil_no;
	        getDataResult(result.data);
        }
    });

    $('#btnGo').bind('click', function(){
    	goScan();
    });

     $("#oil_amt,#goods_amt").keyup(function(){    
                    $(this).val($(this).val().replace(/[^0-9.]/g,''));    
                }).bind("paste",function(){  //CTR+V事件处理    
                    $(this).val($(this).val().replace(/[^0-9.]/g,''));     
                }).css("ime-mode", "disabled"); //CSS设置输入法不可用    

    $('#index-gun-list dd').live('click',function(){
		$(this).siblings('dd').removeClass('on');
		$(this).addClass('on');

		var gun_no = $(this).attr('gun');
        var oil_no = $(this).attr('oil');

        oil_change(gun_no, oil_no);
        $('#oil_amt').focus();
	});

    function oil_change(gun_no, oil_no){
        var oil_type_name = '车用汽油';
        if(oil_no==0)
            oil_type_name = '柴油';

        $("#index-oil").html(oil_no+'#'+oil_type_name);
        $("#gun_no").val(gun_no);
    }


	$(document).keyup(function(evt){
		evt = (evt) ? evt : ((window.event) ? window.event : "") //兼容IE和Firefox获得keyBoardEvent对象  
        var key = evt.keyCode?evt.keyCode:evt.which; //兼容IE和Firefox获得keyBoardEvent对象的键值  
		if(key ==13){
			if($('#scan_code').val()=='')
				goScan();
			else
				$('#form1').submit();
		}else if(key == 27){
			goStart();
		}
	});

	function goScan(){
		if($('#oil_amt').val()=='' && $('#goods_amt').val()==''){
			$('#index-oil').html('请填写金额');
				return;
		}

		var oil_amt = 0;
		if($('#oil_amt').val()!=''){
			oil_amt = parseFloat( $('#oil_amt').val());
		
			if(oil_amt>0){
				var gun_no = $('#gun_no').val();
				if(gun_no==''){
					$('#index-oil').html('请选择油枪');
					return;
				}
			}
		}
		var goods_amt = 0;
		if($('#goods_amt').val()!='')
			goods_amt = parseFloat( $('#goods_amt').val());
		var amt = goods_amt+oil_amt;
		$('#amt').html(amt.toFixed(2));
		if(!$('.get_money_con').is(':hidden') && $('.get_money_sure').is(':hidden')){
			$('.get_money_con').hide();
			$('.get_money_sure').show();
			$('.get_money_done').hide();
		}
		$('#scan_code').focus();
	}

	function goStart(){
		$('.get_money_con').show();
		$('.get_money_sure').hide();
		$('.get_money_done').hide();

		$('#oil_amt').focus();
		$('#scan_code').val('');
	}

	$('#form1').submit(function(){
		tipsAlert('正在支付中...');
		$('.get_money_con').hide();
		$('.get_money_sure').hide();
		$('.get_money_done').show();

		/*
		tipsAlertClose();
		var result = {code:'SUCCESS',data:{order_id:"78",order_sn:"64161478765813161000",pay_amt:'0.01',title:"购买92号0.00L",netpay_method:"13"}};
		$('#gun_no').val('');
		$('#oil_amt').val('');
		$('#index-oil').html('');
		$('#scan_code').val('');
		$('#index-gun-list dd').each(function(){
		    $(this).removeClass('on');
		});

		getDataResult2(result);
		*/


		
		sendPostData({gun_no:$('#gun_no').val(), oil_amt:$('#oil_amt').val(), goods_amt:$('#goods_amt').val(), scan_code:$('#scan_code').val()}, ApiUrl+'cashier/order/create', function(result){
			tipsAlertClose();

			$('#gun_no').val('');
			$('#oil_amt').val('');
			$('#goods_amt').val('');
			$('#index-oil').html('');
			$('#scan_code').val('');
			$('#index-gun-list dd').each(function(){
			    $(this).removeClass('on');
			});
			$('#index-order-detail').html('支付失败');
			$('#btnRefresh').hide();

			if(result.code=='SUCCESS'){
				//初始数据
				getDataResult2(result);
				$('#btnRefresh').show();
				
			}else{
				tipsAlert(result.msg);
				goStart();
			}
			
		});
		

		return false;
	});

	$('#btnRefresh').bind('click',function(){
		var order_id = $('#order_id').html();
		if(order_id!=''){
			sendPostData({order_id:order_id}, ApiUrl+'cashier/order/detail2', function(result){
				getDataResult2(result);
			});
		}
	});

	$('#btnBack').bind('click',function(){
		goStart();
	});

	$('#btnConnect').bind('click', function(){
		var serverIp = get_user_data_from_local('msg_server_ip');
        var serverPort = get_user_data_from_local('msg_server_port');
        var admin_id = get_user_data_from_local('admin_id');
        var initMsg = '{\"mrid\":\"mrid'+admin_id+'\"}';
        javascript:window.external.init(serverIp, serverPort, initMsg);
	});
	
});


function getDataResult(result) {

    var source = $('#index-gun-amt-tpl').html();
    var render = template.compile(source);
    var str = render(result.site);
    $("#index-gun-amt").html(str);

}

function getDataResult2(result) {

    var source = $('#index-order-detail-tpl').html();
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-order-detail").html(str);

}