$(function(){
	var site_id = getUrlParam("site_id");
    if(site_id!=null&&site_id!='')
        save_string_tolocal('site_id', site_id);

	$('#captchabtn').click(function(){
		var site_id = get_string_fromlocal('site_id');
		var data = {mobile:$('#mobile').val(),type_id:8,site_id:site_id};
		sendPostData(data,ApiUrl+'sms/send',function(result){
			if(result.code=='SUCCESS'){
				tipsAlert('短信已发送');
			}else{
				tipsAlert(result.msg);
			}
		});
	});

	$('#btnSave').click(function(){

		var mobile = $('#mobile').val();
		var code = $('#code').val();
		var invoice_title = $('#invoice_title').val();
		var car_no = $('#car_no').val();
		var car_model = $('#car_model').val();
		var user = getUserInfo(null);
		var data = {mobile:mobile,code:code,invoice_title:invoice_title,car_no:car_no,car_model:car_model,type_id:8,company_id:user.company_id};
	
		sendPostData(data,ApiUrl+'m/user/member',getDataResult);
	});

});

function getDataResult(result){
	if(result.code=='SUCCESS')
		show_tips_content2({msg:'已成功申请成为正式会员〜',canbtn:'确定',showok:false,canfun:function(){goHome()}});
	else
		tipsAlert(result.msg);

}