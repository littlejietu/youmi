$(function(){


	sendPostData({},ApiUrl+'m/user/get',function(result){
		if(result.code!='SUCCESS'){
			tipsAlert(result.msg);
			return;
		}

		var info = result.data;
		$('#mobile').val(info.mobile);
		$('#name').val(info.name);
		if(info.birthday!=null && info.birthday!=0)
			$('#birthday').val(new Date(info.birthday*1000).Format('yyyy-MM-dd'));
		if(info.sex==2)
			$("#sex2").attr("checked","true"); 
		else
			$("#sex1").attr("checked","true"); 
		$('#car_no').val(info.car_no);
		$('#car_model').val(info.car_model);
		$('#invoice_title').val(info.invoice_title);
	});

	$('#captchabtn').click(function(){
		var site_id = get_string_fromlocal('site_id');
		var token = get_user_token();
		var data = {mobile:$('#mobile').val(),type_id:8,site_id:site_id,token:token};
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
		var name = $('#name').val();
		var birthday = $('#birthday').val();
		var sex = $('#sex2').attr('checked')?2:1;
		var code = $('#code').val();
		var invoice_title = $('#invoice_title').val();
		var car_no = $('#car_no').val();
		var car_model = $('#car_model').val();
		var data = {mobile:mobile,name:name,birthday:birthday,sex:sex,code:code,invoice_title:invoice_title,car_no:car_no,car_model:car_model,type_id:8};
	
		sendPostData(data,ApiUrl+'m/user/modify',getDataResult);
	});

});

function getDataResult(result){
	if(result.code=='SUCCESS')
		show_tips_content2({msg:'修改成功〜',canbtn:'确定',showok:false,canfun:function(){location.href=WapSiteUrl+'/m/'}});
	else
		tipsAlert(result.msg);

}