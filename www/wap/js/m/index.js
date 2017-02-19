$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    sendPostData({},ApiUrl+'m/home',function (result) {
    	if(result.code=='SUCCESS'){
    		var info = result.data.info;
    		$('#show_nickname').html(info.nickname);
    		$('#show_mobile').html(info.mobile);
    		$('#show_img').attr('src',info.logo);
    		if(info.member_status!=1)
    			$('#show_member').show();
    		$('#show_nickname').html(info.nickname);
    	}else
    		tipsAlert(result.msg);
    });

});