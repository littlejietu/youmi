$(function(){
	if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }

    var site_name = get_user_data_from_local('site_name');
    $('#site_name').html(site_name);
    var cashier_name = get_user_data_from_local('name');
    $('#cashier_name').html(cashier_name);
    $('#scan_code').focus();

    var order_id = 0;
	$('#form1').submit(function(){
		//tipsAlert('正在支付中...');
		$('.refund_con').hide();
		$('.get_money_done').show();

		order_sn = $('#scan_code').val();
		
		sendPostData({order_sn:order_sn}, ApiUrl+'cashier/order/detail', function(result){
			//tipsAlertClose();

			
			$('#btnRefresh').hide();
			if(result.code=='SUCCESS'){
				//初始数据
				order_id = result.data.order_id;
				getDataResult2(result);
				$('#btnRefresh').show();


				
			}else
				$('#index-order-detail').html(result.msg);
			
			
		});
		

		return false;
	});

	$('#btnRefund').bind('click',function(){
		var pwd = $('#pwd').val();
		if(order_id!=0 && pwd!=''){
			sendPostData({order_id:order_id, pwd:pwd}, ApiUrl+'cashier/order/refund', function(result){
				if(result.code=='SUCCESS'){
					$('.refund_con').show();
					$('.get_money_done').hide();
					tipsAlert('退款成功');
					$('#pwd').val('');
					$('#scan_code').val('');
				}
				else
					tipsAlert(result.msg);
			});
		}
	});

	$('#btnBack').bind('click',function(){
		window.location.href="index.html";
	});
	
});


function getDataResult2(result) {

    var source = $('#index-order-detail-tpl').html();
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-order-detail").html(str);

}