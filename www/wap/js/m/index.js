$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = getUrlParam("site_id");
    if(site_id!=null&&site_id!='')
        save_string_tolocal('site_id', site_id);

    sendPostData({},ApiUrl+'m/home',function (result) {
    	if(result.code=='SUCCESS'){
    		var info = result.data.info;
    		$('#show_nickname').html(info.nickname);
    		$('#show_mobile').html(info.mobile);
    		$('#show_img').attr('src',info.logo);
    		if(info.member_status!=1){
                $('#show_member').show();
                $('#show_mycard').hide();
            }else{
                $('#show_member').hide();
                $('#show_mycard').show();
            }
    		$('#show_nickname').html(info.nickname);

            $('#show_acct_integral').html(info.acct_integral);
            $('#show_next_msg').html(info.next_level_msg);
            $('.user_point_now').width(info.next_percent);
            

            var source = $('#level-list-tpl').html();
            var render = template.compile(source);
            var str = render(result.data);
            $('#level-list').html(str);
    	}else
    		tipsAlert(result.msg);
    });

});