$(function(){

	sendPostData({},ApiUrl+'m/home',getDataResult);
	sendPostData({}, ApiUrl + 'm/message/unread', function (result) {
		$('#message_icon i').remove();
		if (result.code == 1) {
			//$("#msg").html('<span>' + 10 + '</span>');
			var num = parseInt(result.data.un_read_num);
			var message = get_user_data_from_local('message');
			for(var key in message){
				if(!message[key].read){
					num++;
				}
			}

			if(num >0){
				$('#message_icon').append("<i>"+num+"</i>");
			}
			save_user_data_to_local('messageNum',num);

		} else {
			save_user_data_to_local('messageNum', 0);
		}


	});
	///*
	//* 初始化用户数据
	//* */
	sendPostData({}, ApiUrl + "m/user/get", function (result) {
		if (result.code == 1) {
			save_user_data_to_local('userInfo', result.data);
		}
	});


	function getDataResult(result){
		var source='<ul>'
			+'<li><a href="../wallet/integral.html"><label>{{acct_integral}}</label><label>我的积分</label></a></li>'
			+'<li><a href="../wallet/remain2.html"><label>{{acct_balance}}</label><label>账户余额</label></a></li>'
			+'<li><a href="../wallet/coupon.html" style="border-right:0;"><label>{{coupon_num}}</label><label>优惠劵</label></a></li>'
			+'</ul>'

		var render = template.compile(source);
		str = render(result.data);
		$("#me-list").html(str);

		var render = template('order_detail',result.data);
		$("#me-nav").html(render);
	}
	var width=$("#user_image").width();
	$("#user_image").height(width);
	var userInfo = get_user_data_from_local('userInfo');
	if(userInfo){
		$('#user_name').html(userInfo.name);
		$('#user_title').html('<span>ID:'+userInfo.user_id+'</span>');
		$('#user_image').attr("src",userInfo.logo);
	}else{
		$('#user_name').html('');
		$('#user_title').html('');
		$('#user_image').attr("src",'../images/person.png');
	}
	setCartNum();

});
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