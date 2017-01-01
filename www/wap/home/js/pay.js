var pay_code = 0;

$(function(){
	FastClick.attach(document.body);
	var site_id = get_string_fromlocal('site_id'); 
	initWx(['chooseWXPay'],null,location.href, site_id);

	var ids = getUrlParam('order_ids');
	sendPostData({order_id:ids,agent_type:0},ApiUrl+'m/buy/cashier',getDataResult);
	$(".return").click(function(){
		// $(".cancel-mask").show();
		show_tips_content2({msg:'您确定要这么做吗？您的订单可能在24小时消失！',okbtn:'离开',canbtn:'继续支付',okfun:sureHandler});
	});
	$(".details4-btn1").click(function(){
		 $(".cancel-mask").hide();
	});
	function sureHandler(){
		location.href = ('/wap/mine/order/order.html?type=1');
	}
	// $('#leave_btn').click(function(){
		
	// });
	$(".pay-btn").click(function(){
		//alert(IPAdd);
		if(pay_code=='12'){
			sendPostData({"order_ids":ids,"paymethod":pay_code,"ip":IPAdd},ApiUrl+'m/order/paying',function(result){
				if(result.code ==1 ){
					wxpay(JSON.parse(result.data.errInfo),success);
				}else{
					tipsAlert(result.msg)
				}
			});
		}else if(pay_code == '1'){

			$(".zhezhao").show();
			var height = $(window).height();
			$(".zhezhao").css("top",height);
			$(".zhezhao").animate({top:"0px"},500);
		}

	})

	function success(res){
		//show_tips_content2()
		location.href = ('success.html?pay='+pay_amount);
	}

	$(".close").click(function() {
		var height = $(window).height();
		$(".zhezhao").animate({top: height + 'px'}, 500, function () {
			$(".zhezhao").hide();
			passArr.splice(0);
			resetpass();
		});
	});
	var passArr = [];
	$(".tab ul li a").click(function(){
		$("#text").hide();
		var text=$(this).text();
		if(passArr.length <6){
			passArr.push(text);
		}
		resetpass();
	});
	$("#delete_btn").click(function(){
		passArr.pop();
		resetpass();
	});
	function resetpass(){
		$('.form table tr td input').each(function(index,element){
			if(index < passArr.length){
				$(this).val(passArr[index]);
			}else{
				$(this).val('');
			}

		});
		// alert(passArr.join(" "));
	}
	$("#finish").click(function(){
		if(passArr.length==6){
			sendPostData({"paymethod":pay_code,"paypwd":hex_md5(passArr.join('')),"order_ids":ids,'ip':IPAdd},ApiUrl+'m/order/paying',payresult);
			var height=$(window).height();
			$(".zhezhao").animate({top:height+'px'},500,function(){
				$(".zhezhao").hide();
				passArr.splice(0);
				resetpass();
			});

		}
		else{
			$("#text").show();
		}
	});

	function payresult(result){
		if(result.code ==1){
			location.href = ('success.html?pay='+pay_amount);
		}else{
			tipsAlert(result.msg);
		}

	}
});
var pay_amount;
function getDataResult(result){
	if(result.code != 'SUCCESS'){
		goHome();
	}
	pay_amount=result.data.pay_amount;
	$("#pay_amount").html(pay_amount+'元');
	var source='{{each paymethod as value i}}'
	            +'<li id="pay_{{value.code}}">'
	  			+'	<div class="list">'	
				+'{{if value.code==1}}'
				+'{{if user_amount*1<pay_amount*1}}'
				+'<span class="span0">{{value.title}}</span>'
				+'{{else}}'
				+'<span class="span1">{{value.title}}</span>'
				+'{{/if}}'
				+'<span class="span2">可用金额{{user_amount}}元</span>'
				+'{{else }}'
				+'<span class="span3">{{value.title}}</span>'
				+'{{/if}}'
				+'		<i class="icon3"></i>'
				+'	</div>'
	  			+'</li>'
	  			+'{{/each}}';

	var render = template.compile(source);
    str = render(result.data);
    $("#paymethod").html(str);
    $(".fukuan-content ul li").click(function(){
		if($(this).find('span').hasClass('span0')){
			return;
		}
		var codeid = $(this).attr('id').split("_")[1];
		var current = $(this);
		if(codeid == '1'){
			getUserInfo(getuserpwdInfo);
			function getuserpwdInfo(user){
				if(user.paypwd_status == "0"){
					show_tips_content2({msg:'尚未设置支付密码，请先设置支付密码',canfun:showsetPass,canbtn:'确定'});
				}else{
					pay_code = codeid;
					current.children().children("i").addClass("icon4").removeClass("icon3");
					current.siblings().children().children("i").removeClass("icon4").addClass("icon3");
				}
			}

		}else{
			pay_code = $(this).attr('id').split("_")[1];
			$(this).children().children("i").addClass("icon4").removeClass("icon3");
			$(this).siblings().children().children("i").removeClass("icon4").addClass("icon3");
		}





	});

	function showsetPass(){
		location.href = '../../mine/wallet/private5.html?url='+encodeURI(location.href);
	}

}