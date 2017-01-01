var currentCouponId = 0;
var orderData;
var couponData;
var site_id;
var activity ;
var confirmObj = {};
var total;

$(function(){
	//只有油品,自动确认订单
	sendPostData({oil_cart_id:getUrlParam('oil_cart_id'),cart_id:getUrlParam('cart_id'),ifcart:getUrlParam('ifcart')},ApiUrl+'m/buy/confirm',getDataResult);

});

function getDataResult(result){
	var oil = result.data.oil;
	site_id = get_string_fromlocal('site_id');
	if(oil){
		confirmObj['cart['+site_id+'][oil]'] = oil.gun_no+',0,'+oil.oil_amt;
		sendPostData(confirmObj, ApiUrl+'m/buy/create', function(result2){
			if(result2.code=='SUCCESS')
				location.href = WapSiteUrl+'/order/pay.html?order_ids='+result2.data.order_ids;
			else
				goHome();
		});
	}
	else
		goHome();

}
/*
$(function(){

	$("#zhezhao2").hide();
	$("#zhezhao2").addClass('animated');


	$("#arow2").click(function(){
		$("#zhezhao2").show();
		$("#zhezhao2").removeClass('slideOutRight');
		$("#zhezhao2").addClass('slideInRight');
	});
	$(".close3").click(function(){
		$("#zhezhao2").addClass('slideOutRight');
		$("#zhezhao2").removeClass('slideInRight');
	});

	sendPostData({oil_cart_id:getUrlParam('oil_cart_id'),cart_id:getUrlParam('cart_id'),ifcart:getUrlParam('ifcart')},ApiUrl+'m/buy/confirm',getDataResult);
	if(getUrlParam('ifcart')){
		confirmObj['ifcart'] = getUrlParam('ifcart');
	}

});



var shopIdArr = [];
function getDataResult(result){
	orderData = result;
	couponData = result.data.coupon;
	if(result.code !=1){
		tipsAlert(result.msg);
		return ;
	}
	for(var key in result.data.goods){
		var ob = result.data.goods[key];
		//if(activity){
		//	confirmObj[shop_id].activity = activity;
		//}
		var ss = '';
		for(var key1 in ob.goods_list){
			ss +=ob.goods_list[key1].goods_id+','+ob.goods_list[key1].sku_id+","+ob.goods_list[key1].num+"|";
		}

		if(ss.charAt(ss.length-1) == "|"){
			ss = ss.substring(0,ss.length -1);
		}
		shopIdArr.push(ob.shop.id);
		confirmObj['cart['+ob.shop.id+'][goods]'] = ss;
		confirmObj['cart['+ob.shop.id+'][coupon]'] = 0;
		confirmObj['cart['+ob.shop.id+'][activity]'] = 0;

	}

    var source='{{each goods as gg i}}'
				+'{{each gg.goods_list as value i}}'
			    +'<div class="goods-list">'
				+'		<div class="goods-pic"><a><img src="{{value.pic_url}}"/></a></div>'
				+'		<div class="goods-text">'
				+'			<h3><a href="#">{{value.goods_title}}</a></h3>'
				+'			<p class="font4">{{value.sku}}</p>'
				+'			<p><span>&yen;{{value.goods_price}}</span><i>x{{value.num}}</i></p>'
				+'		</div>'
				+'		<div class="clear"></div>'
				+'	</div>'
			    +'{{/each}}'
			    +'{{/each}}'

	var render = template.compile(source);
    var str = render(result.data);
    $("#goods_list").html(str);
    var width=$(".goods-pic").width();
    $(".goods-pic a img").height(width);

    var source='{{each  as value i}}'
                  +'<p>{{value.title}}</p>'
				+'{{/each}}'

	var render = template.compile(source);
    str = render(result.data.delivery);
    $("#delivery").html(str);

    var source='<p>商品金额<span>&yen;{{total_goods}}</span></p>'
			    +'<p>运费<span>+&yen;{{fare}}</span></p>'
				
	var render = template.compile(source);
    str = render(result.data.amount);
    $("#amount").html(str);
     total=result.data.amount.total_goods+result.data.amount.fare;
    $("#total").html(total);

	setCouponData(result.data.coupon);
	$('#coupon_num').html(result.data.coupon.num+'张可用');
	$('#coupon_name').html('未使用');

}
function setCouponData(coupon){
	if(!coupon.list){
		return ;
	}
	for(var i = 0 ;i < coupon.list.length;i++){
		coupon.list[i].get_date = new Date( coupon.list[i].get_date*1000).Format("yyyy-MM-dd");
		coupon.list[i].overdue_date = new Date( coupon.list[i].overdue_date*1000).Format("yyyy-MM-dd");
	}
	var source = '{{each list as value i}}'
		+'<div class="list" onclick="couponClick(event,{{value.id}})">'
		+'<div class="left">￥<span>{{value.price}}</span>.0</div>'
		+'<div class="right">'
		+'<h3>全站</h3>'
		+'<ul>'
		+'<li>{{value.coupon_name}}</li>'
		+'<li>{{value.desc}}</li>'
		+'<li>{{value.get_date}}至{{value.overdue_date}}</li>'
		+'</ul>'
		+'</div>'
		+'<div class="clear"></div>'
		+'<i></i>'
		+'</div>'
		+'{{/each}}';
	var render = template.compile(source);
	str = render(coupon);
	$('.coupon-2').html(str);


}
function couponClick(event,id){
	currentCouponId = id;
	$("#zhezhao2").addClass('slideOutRight');
	$("#zhezhao2").removeClass('slideInRight');
	$(".coupon-2 .list").css("border","");
	$(event.currentTarget).css("border","1px solid #ff3d23");
	var value = getCouponById(id);
	if(value){
		$('#coupon_name').html(value.coupon_name);
		// coupon_price=parseInt(value.price);
		$("#total").html(total-value.price);
	}else{
		$('#coupon_name').html('未使用');
	}

	//});
}

function getCouponById(id){
	for(var i =0;i < couponData.list.length;i++){
		if(couponData.list[i].id == id){
			return couponData.list[i];
		}
	}
}

function confirmOrder(){
	if(currentCouponId){
		for(var key in shopIdArr){
			confirmObj['cart['+shopIdArr[key]+'][coupon]'] = currentCouponId;
		}
	}
	sendPostData(confirmObj,ApiUrl+'m/buy/create',function(result){
		if(result.code ==1){
			location.href=WapSiteUrl+"../order/pay.html?order_ids="+result.data.order_ids;
		}else{
			tipsAlert(result.msg);
		}
	})

}
*/
