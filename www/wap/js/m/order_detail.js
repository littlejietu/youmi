$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var order_id = getUrlParam('order_id');
    sendPostData({order_id:order_id},ApiUrl+'m/order/detail',function (result) {
    	if(result.code=='SUCCESS'){
            result.data.order_detail.createtime = new Date(result.data.order_detail.createtime*1000).Format('yyyy-MM-dd hh:mm:ss');
    		var data = {info:result.data.order_detail};
    		var source = $('#order-detail-tpl').html();
            var render = template.compile(source);
            var str = render(data);
            $('#order-detail').html(str);
    	}else
    		tipsAlert(result.msg);
    });

});



function payNow(order_id){
    location.href = '../order/pay.html?order_ids='+order_id;
}

function del_order(order_id){
    current_order = order_id;
    show_tips_content2({msg:'您确定要删除这个订单吗？',okbtn:'确定',canbtn:'我在想想',okfun:sureHandler})
}
function sureHandler(){
    sendPostData({order_id:current_order},ApiUrl+'m/order/del',function(result){
        if(result.code == 1){
            location.href='order.html';
        }
        $('.cancel-mask').hide();
    });

}