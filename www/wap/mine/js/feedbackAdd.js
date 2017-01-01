$(function(){
	$("#submit").click(function(){

		// var phone=$("#phone").val();
		var text = $("#text").val();
		text = $.trim(text);
		if(text ==''){
			$("#remind").text("请输入您的反馈");
			$("#remind").show();
			return;
		}
		else{
			sendPostData({"content":text},ApiUrl + 'm/feedback/add',feedbackData);
			// if(phone && testMobile(phone)){
			// 	sendPostData({"content":text,"mobile":phone},ApiUrl + 'm/feedback/add',feedbackData);
			// }
			// else{
			// 	$("#remind").text("您输入的手机号码有误，请重新输入");
			// 	$("#remind").show();
			// }
		}
		// $("#phone").keyup(function(){
		// 	$("#remind").hide();
		// });
	function feedbackData(result){		
		if(result.code==1){
			show_tips_content2({msg:'您的建议已经收到，谢谢您的反馈！',canbtn:'确定',showok:false,canfun:sureFun});
			
		}else{
			tipsAlert(result.msg);
		}
	
	}

	function sureFun(){
		location.href="feedback.html";
	}
  });
});