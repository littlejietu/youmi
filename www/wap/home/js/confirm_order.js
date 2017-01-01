
var currentAddressId =0;
var currentCouponId = 0;
var defultAddress = 0;
var addressData;
var orderData;
var couponData;
var shop_id;
var activity ;
var confirmObj = {};
var total;
$(function(){
	$("#zhezhao").hide();
	$("#zhezhao2").hide();
	$("#zhezhao").addClass('animated');
	$("#zhezhao2").addClass('animated');


	$("#arow2").click(function(){
		$("#zhezhao2").show();
		$("#zhezhao2").removeClass('slideOutRight');
		$("#zhezhao2").addClass('slideInRight');
	});
	$(".close3").click(function(){
		$("#zhezhao").addClass('slideOutRight');
		$("#zhezhao").removeClass('slideInRight');
		$("#zhezhao2").addClass('slideOutRight');
		$("#zhezhao2").removeClass('slideInRight');
	});
});
$(function(){
	shop_id = getUrlParam("shop_id");
	activity = getUrlParam('activity');
	//confirmObj[shop_id] = {};
	//if(activity){
	//	confirmObj[shop_id].activity = activity;
	//}
	sendPostData({cart_id:getUrlParam('cart_id'),ifcart:getUrlParam('ifcart')},ApiUrl+'m/buy/confirm',getDataResult);
	sendPostData({},ApiUrl+'m/addr/addr_list',getAddress);
	if(getUrlParam('ifcart')){
		confirmObj['ifcart'] = getUrlParam('ifcart');
	}

});

function getAddress(result){
	addressData = result;
	var source = '{{each data as value i}}'
		+'<div class="wrapper" onclick="addreddClick(event,{{value.id}});">'
		+'<h3>{{value.real_name}}&nbsp;&nbsp;{{value.mobile}}</h3>'
		+'<p>&nbsp;{{value.province_name}}&nbsp;{{value.city_name}}&nbsp;{{value.area_name}}&nbsp;{{value.address}}</p>'
		+'</div>'
		+'{{/each}}';
	var render = template.compile(source);
	var str = render(result.data);
	$(".address2").html(str);
}



function addreddClick(event,addid){
	currentAddressId = addid;
	$("#zhezhao").addClass('slideOutRight');
	$("#zhezhao").removeClass('slideInRight');
	$(".address2 .wrapper").css("border","");
	$(event.currentTarget).css("border","1px solid #ff3d23");

	var value = getaddressById(currentAddressId);
	value.phone = value.mobile;
	value.province = value.province_name;
	value.city = value.city_name;
	value.area = value.area_name;
	value.province = value.province_name;
	value.steet = value.address;
	changeAddress(value);
}

function getaddressById(id){
	if(!addressData || !addressData.data.data){
		return null;
	}
	for(var i = 0 ;i < addressData.data.data.length;i++){
		if(addressData.data.data[i].id == id){
			return addressData.data.data[i];
		}
	}
	return null;
}

function chooseAddress(){
	$("#zhezhao").show();
	$("#zhezhao").removeClass('slideOutRight');
	$("#zhezhao").addClass('slideInRight');

}

function changeAddress(data){
	var source='<a id="arow" onclick="chooseAddress()">'
		+'<div class="wrapper" style="padding-bottom:20px;">'
		+'	<h3>{{real_name}}<span>{{phone}}</span></h3>'
		+isdefualt()
		+'</div>'
		+'<div class="arow"></div>'
		+'<div class="images"></div>'
		+'</a>'
	var render = template.compile(source);
	var str = render(data);
	$("#address").html(str);
}
function isdefualt(){
	if(currentAddressId == defultAddress){
		return '	<p><span>默认</span>&nbsp;{{province}}&nbsp;{{city}}&nbsp;{{area}}&nbsp;{{street}}</p>';
	}else{
		return '	<p>&nbsp;{{province}}&nbsp;{{city}}&nbsp;{{area}}&nbsp;{{street}}</p>';
	}
}
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

	if(result.data.address && result.data.address.id >0){
		changeAddress(result.data.address);
		$(".address-mask").hide();
		currentAddressId = result.data.address.id;
		defultAddress = currentAddressId;
	}else{
		$("#address").html("");
		$(".address-mask").show();
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

function confirmOrder(order_id){
	confirmObj.address_id = currentAddressId;
	if(currentCouponId){
		for(var key in shopIdArr){
			confirmObj['cart['+shopIdArr[key]+'][coupon]'] = currentCouponId;
		}
	}
	sendPostData(confirmObj,ApiUrl+'m/buy/create',function(result){
		if(result.code ==1){
			location.href="../productdetails/pay.html?order_ids="+result.data.order_ids;
		}else{
			tipsAlert(result.msg);
		}
	})

	//sendPostData({},ApiUrl+'m/buy/create',getAddress);
}
