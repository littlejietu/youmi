var cash_limit = 10;
$(function(){
	refreshAccount();
	var num = 0

	$("#cash_input").keyup(function(){
		var pay = parseFloat($(this).val());
        if(pay > parseFloat(num)){
            $(this).val(num);
        }
       
	});
	// var weixin=$("#weixin").val();
	
	// $("#weixin").keyup(function(){
	// 	$("#remind").hide();
	// });
	$("#cash_input").keyup(function(){
		$("#remind").hide();
	});

	var height=$(window).height();
	$(".zhezhao").css("top",height+'px');
	$("#cash").click(function(){
		// var weixin=$("#weixin").val();
		// if(weixin && /^[a-zA-Z0-9_]+$/.test(weixin)){
			var num1 =parseFloat($("#cash_input").val());
			if(num1 >= cash_limit){ //需要定义一个最小提现额度
				$(".zhezhao").show();
				$(".zhezhao").animate({top:"0px"},500);
			}
			else{
				$("#remind").show();
				$("#remind").text("您的提现金额不正确!");
			}
			
		// }
		// else{
		// 	$("#remind").show();
		// 	$("#remind").text("账号有误，请输入正确账号！");
		// }
		
		
	});
	$(".close").click(function(){
		var height=$(window).height();
		$(".zhezhao").animate({top:height+'px'},500,function(){
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
			sendPostData({"amount":$('#cash_input').val(),"paypwd":hex_md5(passArr.join(''))},ApiUrl+'m/account/cash',getDataResult);
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
	function getDataResult(result){
		if(result.code==1){	
			$("#cash_input").val(0);
			tipsAlert('提现成功!');
			refreshAccount();
		}
		else{
			tipsAlert(result.msg);
		}
	}

	function refreshAccount(){
		sendPostData({},ApiUrl+'m/account/detail',function(result){
			num = result.data.acct_balance;
			if(num >2000){
				num = 2000;
			}
			$("#cash_num").val(result.data.acct_balance);
		});
	}
});