$(function(){
	if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }

    var site_name = get_user_data_from_local('site_name');
    $('#site_name').html(site_name);
    var cashier_name = get_user_data_from_local('name');
    $('#cashier_name').html(cashier_name);

    var order_id = getUrlParam("id");
    sendPostData({order_id:order_id}, ApiUrl+'cashier/order/detail', function(result){
				getDataResult2(result);
			});

	$(document).keyup(function(evt){
		evt = (evt) ? evt : ((window.event) ? window.event : "") //兼容IE和Firefox获得keyBoardEvent对象  
        var key = evt.keyCode?evt.keyCode:evt.which; //兼容IE和Firefox获得keyBoardEvent对象的键值  
		if(key == 27){
			window.location.href='index.html';
		}
	});

	

	$('#btnRefresh').bind('click',function(){
		if(order_id!=''){
			sendPostData({order_id:order_id}, ApiUrl+'cashier/order/detail', function(result){
				getDataResult2(result);
			});
		}
	});

	$('#btnBack').bind('click',function(){
		window.history.go(-1);
	});
	
});


function getDataResult2(result) {

    var source = $('#index-order-detail-tpl').html();
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-order-detail").html(str);

}