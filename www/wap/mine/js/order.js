$(function(){
	// var str='{"data": {"page":1,"pagesize":10,"count": "8","rows": [{"order_id": "9","order_sn": "16031316344814578832","title": "消费","shop_id": "2","total_amt": "645.00","pay_amt": "645.00","discount_amt": "0.00","coupon_amt": "0.00","coupon_id": "0","pay_type": "1","fare_amt": "0.00","comment_status": "0","buyer_userid": "5","buyer_username": "test","seller_userid": "0","seller_username": "22","createtime": "0000-00-00 00:00:00","status": "待付款","platform_id": "1","goods": [{"goods_id": "9","sku_id": "0","title": "羊绒衫","num": 8,"pic_path": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","spec": "红色L码"},{"goods_id": "9","sku_id": "0","title": "羊绒衫","num": 8,"pic_path": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","spec": "红色L码"}]}]},"code": "SUCCESS","message": "操作成功"}'
	// var result = JSON.parse(str);
 //    getDataResult(result);
    var type=getUrlParam('type');
    if(!type){
    	type = 0;
    }
    // sendPostData({page:1,pagesize:10,type:type},ApiUrl+'m/order',getDataResult);
   
    $(".order ul li").click(function(){
		$(this).children("span").addClass("color2").parent().siblings().children("span").removeClass("color2");
		var idd  = $(this).attr('id');
		tp = idd.split('_')[1];
		if(swipeHandler){
			swipeHandler.setBefore(true);
		}
    	sendPostData({page:1,pagesize:10,type:tp},ApiUrl+'m/order',getDataResult);
	});

	$('.btn2').click(function(){
		$('.cancel-mask').hide();
	});
	 $('#ordertype_'+type).click();

});
var swipeHandler;
var resultData;
function getDataResult(result){
	if(result.code !=1){
		return;
	}
	resultData = result;
	for(var i = 0 ;i < resultData.data.rows.length;i++){
		resultData.data.rows[i].createtime = new Date(resultData.data.rows[i].createtime*1000).Format('yyyy-MM-dd hh:mm:ss')
	}
	var source = '{{each rows as value i}}'
					+'<div class="swiper-slide" >'
						+'<div class="order-1" >'
						+'<div class="top">{{value.createtime}}<span>{{value.status_name}}</span></div>'
						+'{{each value.goods as vv ii}}'
						+'	<div class="up" onclick="showOrdeDetails(\'{{value.order_id}}\');">'
						+'		<div class="left"><img src="{{vv.pic_path}}"/></div>'
						+'		<div class="right">'
						+'			<h3>{{vv.title}}</h3>'
						+'			<p>{{vv.spec}}<span>X{{vv.num}}</span></p>'
						+'		</div>'
						+'		<div class="clear"></div>'
						+'	</div>'
						+'{{/each}}'
						+'	<div class="down">'
						+'		<p class="p1">共1件商品<span>实收款：<i>&yen;{{value.total_amt}}&nbsp;</i>（邮费&yen;{{value.fare_amt}}）</span></p>'
						+'		<p class="p2">'
						+'{{if value.status=="Closed"}}'
						+'<a ><span class="span2">已关闭</span></a>'
						+'{{else if value.status=="WaitPay" || value.status=="Create"}}'
						+'        <a onclick="payNow({{value.order_id}});"><span class="span1">立即付款</span></a>'
						+'<a onclick="cancel_order({{value.order_id}});"><span class="span2">不想要了</span></a>'
						+'{{else if value.status=="WaitSend"}}'
						+'        <a ><span class="span1">等待发货</span></a>'
						+'<a onclick="cancel_order({{value.order_id}});"><span class="span2">不想要了</span></a>'
						//+'<a onclick="apply_refund({{vv.order_id}});"><span class="span2">申请退款</span></a>'
						+'{{else if value.status=="WaitConfirm"}}'
						+'        <a onclick="confirmOrder({{value.order_id}});"><span class="span1">确认收货</span></a>'
						//+'<a onclick="refund_order({{value.order_id}},{{value.pay_amt}});"><span class="span2">申请退货</span></a>'
						+'{{else if value.status=="Finished" && value.comment_status == "0"}}'
						+'        <a onclick="commentOrder({{value.order_id}});"><span class="span1">评价</span></a>'
						+'{{/if}}'
						+'      </p>'
						+'	</div>'
						+'</div>'
					+'</div>'
	            +'{{/each}}'
	var render = template.compile(source);
    var str = render(result.data);
 
    if(!swipeHandler){

        $('#order_list').html(str);
        swipeHandler = new SwiperUtils({
            container:'.swiper-container',
            swpierHandler:swpierEvemt,
            collectswiper:'#order_list',
            deep:200
        });
        $('.swiper-container').css('height',$(window).height() - 102);

    }else{
        swipeHandler.setSwiperSlider(str);
    }
    if(resultData.data.count==0){
    	$(".empty").show();
    	if(tp == 0){
    		$(".empty span").text("还没有您的全部订单");
    	}else if(tp ==1){
    		$(".empty span").text("还没有您的待付款订单");
    	}else if(tp ==2){
    		$(".empty span").text("还没有您的待发货订单");
    	}else if(tp ==3){
    		$(".empty span").text("还没有您的待收货订单");
    	}else if(tp ==4){
    		$(".empty span").text("还没有您的待评价订单");
    	}
    }
    else{
    	$(".empty").hide();
    }
    var total = get_total_page(resultData.data.count,resultData.data.pagesize);
    swipeHandler.setPage(parseInt(resultData.data.page),total)

    var width=$(".up .left").width();
    $(".up .left img").height(width);
}
var tp;
var current_order ;
function swpierEvemt(before){
    var p = 1;
    if(!before){
        p = parseInt(resultData.data.page)+1;

    }
    sendPostData({page:p,pagesize:10,type:tp},ApiUrl+'m/order',getDataResult);
}

function payNow(order_id){
	location.href = '../../home/productdetails/pay.html?order_ids='+order_id;
}


function confirmOrder(order_id){
	show_tips_content2({msg:'您确认收货吗？',okbtn:'取消',canbtn:'确定',canfun:function(){
		sendPostData({order_id:order_id},ApiUrl+'m/order/confirm',confirmData);
	}});
	

}


function commentOrder(order_id){
	location.href = '../order/publish.html?order_id='+order_id;
}

function confirmData(result){
	if(result.code==1){
		swpierEvemt(true)
	}
}

function cancel_order(order_id){
	current_order = order_id;
	show_tips_content2({msg:'您确定要取消这个订单吗？',okbtn:'确定',canbtn:'我在想想',okfun:sureHandler})

}


function sureHandler(){
	sendPostData({order_id:current_order},ApiUrl+'m/order/close',function(result){
		if(result.code == 1){
			swpierEvemt(true);
		}
		$('.cancel-mask').hide();
	});

}

function apply_refund(order_id){
	current_order =order_id;
	$('#msg_content').text('您确定要要退款？商家将在1-7个工作日退款至您的账户。');
	$('.cancel-mask').show();
	$('.btn1').one('click',function(){

	});

}

function showOrdeDetails(order_id){
	location.href= './details.html?order_id='+order_id;
}
