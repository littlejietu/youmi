
var cartSwiper;
var resultData ;
$(function(){


     //    var result = JSON.parse(str);
     //getDataResult(result);

	FastClick.attach(document.body);
 	sendPostData({"shop_id":get_string_fromlocal('shop_id')},ApiUrl + "m/cart",getDataResult);
	sendPostData({"shop_id":get_string_fromlocal('shop_id')},ApiUrl + "goods/wish_goods",getWishGoods);
	if(getUrlParam('ef')){
		$('.left-arow').hide();
	}else{
		$('.left-arow').show();
	}

	$("#delete").click(function(){
		if($(".chk_icon").hasClass('icon1'))
		{
			// $(".mask").show();
			// $(".mask_out").show();
			 show_tips_content2({msg:'你确定要删除商品？',okbtn:'确定',canbtn:'我在想想',okfun:sureHandler});
		}
	});
    function sureHandler(){
    	$('.cancel-mask').hide();
    	var id = getChooseGoodsId();
    	sendPostData({cart_ids:id},ApiUrl + "m/cart/delete",function(result){
			//alert('success')//todo;
			//value.num = current;
			//$('#num'+id).html(value.num);

			if(result.code == 1){
				var arr = id.split(",");
				removeSildeById(arr);
				setCartListTotal(0);
				remove_goods(id);
			}else{
				tipsAlert(result.msg);
			}

		});
    }

	function remove_goods(ids){
		var arr = ids.split(',');
		var list = get_user_data_from_local('cart');
		for(var key in list){
			if(arr.indexOf(list[key].cart_id) >=0){
				list.remove(key);

			}
		}
		save_user_data_to_local('cart',list);
		setCartNum();

	}
	// $("#cancel").click(function(){
	// 	$(".mask").hide();
	// 	$(".mask_out").hide();
	// });

	// $("#tips2_sure_btn").click(function(){
	// 	$('.cancel-mask').hide();
	// 	var id = getChooseGoodsId();
		

	// });


	$(".chk_sum").click(function(){
		if($(this).hasClass('icon4')){//没有选择
			$(".chk_icon").addClass("icon1").removeClass("icon2");
			$(this).removeClass("icon4").addClass("icon3");
		}else{
			$(this).removeClass("icon3").addClass("icon4");

			$(".chk_icon").addClass("icon2").removeClass("icon1");
		}
		resetChoosePrice();
	});
	setCartListTotal(0);

	$("#switch").live('click',function(){

		if($(this).hasClass("switch1")){
			$(".cart-edit").show();
			$(".cart-finish").hide();
			$(this).removeClass("switch1").addClass("switch2");
			$(".a1").show();
			$(".a2").hide();
			$("#sum").show();
			$("#delete").hide();
			setSumNum();
		}
		else{
			$(this).removeClass("switch2").addClass("switch1");
			$(".cart-edit").hide();
			$(".cart-finish").show();
			$(".a2").show();
			$(".a1").hide();
			$("#sum").hide();
			$("#delete").show();
		}
	});
});

function getWishGoods(result){
	renderSlide(result)
}

function removeSildeById(idarr){
	for(var i = 0 ;i < idarr.length;i++){
		removeCartDataById(idarr[i]);
	}

	//var index = getIndexOfList(id);
	//index = parseInt(index);
	//if(index<0){
	//	return ;
	//}
	//cartSwiper.removeSlide(index);
	//cartSwiper.reInit();
	renderCardList();
	if(resultData.data.cart_list[0].goods_list.length<=0){
		//resultData.code = -1;
		//getDataResult(resultData);
		$("#cart_list").html('');
		$('#cart').hide();
		$('#empty').show();
		$('.cart-edit').hide();
		$('.budget').hide();
		$(".scroll").css("bottom","49px");
		$(".chk_sum").removeClass("icon3").addClass("icon4");
	}

}

function removeCartDataById(id){
	if(!resultData){
		return -1;
	}
	if(resultData.code != 1){
		return -1;
	}
	var data = resultData.data.cart_list[0].goods_list;
	for(var value in data){
		if(data[value].cart_id == id){
			data.splice(value,1);
			return 1;
		}
	};
	return  -1;
}


function getIndexOfList(id){
	if(!resultData){
		return -1;
	}
	if(resultData.code != 1){
		return -1;
	}
	var data = resultData.data.cart_list[0].goods_list;
	for(var value in data){
		if(data[value].cart_id == id){
			return value;
		}
	};
	return  -1;
}

function resetChoosePrice(){

	var id = getChooseGoodsId();
	sendPostData({cart_ids:id},ApiUrl + "m/cart/amount",function(result){
		//alert('success')//todo;
		//value.num = current;
		//$('#num'+id).html(value.num);
		if(result.code == 1){
			setCartListTotal(result.data.total_price);
		}else{
			setCartListTotal(0);
		}

	});
	setSumNum();

}

function getChooseCoodsNum(){
	var arr = [];
	$.each($('.chk_icon'),function(){
		if($(this).hasClass('icon1')){
			arr.push($(this).attr("id"));
		}
	});
	return arr.length;

}

function getChooseGoodsId(){
	var arr = [];
	$.each($('.chk_icon'),function(){
		if($(this).hasClass('icon1')){
			arr.push($(this).attr("id"));
		}
	});
	if(arr.length>0){
		return arr.join(',');
	}
	return '';
}

function getDataResult(result){
	resultData = result;

	if(result.code ==1){

		//renderSlide();
		$('#cart').show();
		$('#empty').hide();
		$('.cart-edit').show();
		$('.budget').show();
		$(".scroll").css("bottom","100px");
		$('.cart-finish').hide();

		renderCardList();

	}else{

		$("#cart_list").html('');
		$('#cart').hide();
		$('#empty').show();
		$('.cart-edit').hide();
		$('.budget').hide();
		$(".scroll").css("bottom","49px");
	}
	setCartNum();
}
function setSumNum(){
	$("#sum").find("span").remove();
	//var obj = get_user_data_from_local('cart');
	//var num = 0;
	//if(obj&& obj.goods_list && obj.goods_list.length>0){
	//	num = obj.goods_list.length;
	//}
	var num = getChooseCoodsNum();
	if(num > 0){
		$("#sum a").append("<span>("+ num+")</span>");
	}

}
function setCartNum(){
	$("#cart_num_span").find('i').remove();
	var obj = get_user_data_from_local('cart');
	var num = 0;
	//if(obj&& obj.goods_list && obj.goods_list.length>0){
	//	num = obj.goods_list.length;
	//}
	if(obj&& obj.goods_list){
		for(var key in obj.goods_list){
			num += parseInt(obj.goods_list[key].num);
		}
	}

	if(num > 0){
		$("#cart_num_span").append("<i>"+ num+"</i>");
	}

}

function renderCardList(){
	var source = '{{each goods_list as value i}}'
		+'<div class="cart-list">'
		+'<div class="cart-wrapper">'
		+'	<div class="left" onclick="jump_by_tpl_id({{value.tpl_id}});"><img src="{{value.pic_url}}"/></div>'
		+'	<div class="right">'
		+'		<h3><a onclick="jump_by_tpl_id({{value.tpl_id}});">{{value.goods_title}}</a></h3>'
		+'		<p>{{value.sku}}</p>'
		+'		<div class="number">'
		+'			<label>&yen;{{value.goods_price}}</label>'
		+'				<span style="border-left:0;background: #ddd;border-top-right-radius: 8px;border-bottom-right-radius: 8px;font-size: 2em;line-height: 24px;" onclick="resetNum(\'{{value.cart_id}}\',1);">+</span>'
		+'				<span style="border:1px solid #eee;height:28px;width:30px;color:#000;" id="num{{value.cart_id}}">{{value.num}}</span>'
		+'				<span onclick="resetNum(\'{{value.cart_id}}\',0);" style="background: #ddd;border-top-left-radius: 8px;border-bottom-left-radius: 8px;font-size: 2em;border-right:0;line-height: 26px;">-</span>'
		+'		</div>'
		+'	</div>'
		+'	<div class="clear"></div>'
		+'	<i class="chk_icon icon2" id="{{value.cart_id}}" onclick="chooseHandler(event);"></i>'
		+'</div>'
		+'</div>'
		+ '{{/each}}';
	var render = template.compile(source);
	save_user_data_to_local('cart',resultData.data.cart_list[0]);
	if(resultData.data.cart_list.length>0){
		var str = render(resultData.data.cart_list[0]);
		$("#cart_list").html(str);
	}else{
		$("#cart_list").html('');
		$('#cart').hide();
		$('#empty').show();
		$('.cart-edit').hide();
		$('.budget').hide();
		$(".scroll").css("bottom","49px");
	}

	var width=$(".cart-wrapper .left").width();
	$(".cart-wrapper .left img").height(width);

}


function refreshCartList(){
	sendPostData({"shop_id":get_string_fromlocal('shop_id')},ApiUrl + "m/cart",getDataResult);
}
function renderSlide(result){
	var source = '{{each wish_list as value i}}'
				+'<div class="swiper-slide">'
				+'<div class="slide-content">'
				+'<a onclick="jump_to_url(\'{{value.to_url}}\')">'
				+'<img src="{{value.pic_url}}"/>'
				+'<h3>{{value.name}}</h3>'
				+'<p class="p1"><del>&yen;{{value.original_price}}</del></p>'
				+'<p class="p2">&yen;{{value.price}}<span  onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1,refreshCartList);"></span></p>'
				+'</a>'
				+'</div>'
				+'</div>'
				+'{{/each}}'
	var render = template.compile(source);
	var str = render(result.data);
	$("#show_you_like").html(str);
	//$("#show_you_like").show();
	cartSwiper = new Swiper('.thumbs-cotnainer',{
		slidesPerView:'auto',
		offsetPxBefore:10,
		offsetPxAfter:10,
		watchActiveIndex: true,
		//calculateHeight: true
	});
}

function resetNum(id,add){
	var value = getCartDatById(id);
	if(!value){
		return ;
	}
	var current = value.num;
	if(add){
		//if(value.num >10){
		//	return ;todo上限限制？
		//}
		current++;
	}else{
		if(current < 1 ){
			return;
		}
		current--;
	}

	sendPostData({cart_id:id,num:current},ApiUrl + "m/cart/update",function(result){
		//alert('success')//todo;
		if(result.code == 1){
			value.num = current;
			save_user_data_to_local('cart',resultData.data.cart_list[0]);
			setCartNum();
			$('#num'+id).html(value.num);
			if($('#'+id).hasClass("icon1")){
				resetChoosePrice();
			}
		}else{

		}


	});
}

function getCartDatById(id){
	if(!resultData){
		return null;
	}
	if(resultData.code != 1){
		return null;
	}
	var data = resultData.data.cart_list[0].goods_list;
	for(var value in data){
		if(data[value].cart_id == id){
			return data[value];
		}
	};
	return null;
}

function setCartListTotal(total){
	$('#totol_price').html('&yen;'+total);
}

var chooseHandler = function(event){
	var totallength = $('.chk_icon').length;//总长度
	if($(event.target).hasClass('icon2')){//是否已选择
		$(event.target).removeClass("icon2").addClass("icon1");

	}else{
		$(event.target).removeClass("icon1").addClass("icon2");
	}


	var length = $('.icon1').length;//选中的长度
	if(length==totallength){//如果已选中长度等于总长度，则全选
		$(".chk_sum").removeClass("icon4").addClass("icon3");
	}
	if(length<totallength){
		$(".chk_sum").removeClass("icon3").addClass("icon4");
	}
	resetChoosePrice();
}


function orderConfirm(){
	var url ="../ordersubmit/confirm.html";
	var arr = [];
	$.each($('.chk_icon'),function(){
		if($(this).hasClass('icon1')){
			arr.push($(this).attr("id"));
		}
	});
	var cartArr = [];
	for(var i = 0;i < arr.length;i++){
		var value = getCartDatById(arr[i]);
		if(value){
			cartArr.push(value.cart_id+','+value.sku_id+','+value.num) ;
		}
	}
	if(cartArr.length<1){
		return;
	}
	var cartstr = cartArr.join("|");
	url += "?cart_id="+cartstr+'&ifcart=1';
	location.href = url;
}
