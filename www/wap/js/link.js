$(function() {

	var company_id = $('#company_id').val();
	if(isWeixinBrowser()){
		initWxCom(['getLocation'],function(res){
			if(res==1){
				wxGetLocation({success:locSuccess,cancel:cancelFun});

			}
		},location.href,company_id);
	}

	function locSuccess(data){

        var latitude = data.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = data.longitude; // 经度，浮点数，范围为180 ~ -180。
//tipsAlert(latitude+'--'+longitude);

		var obj = {lat:latitude,lng:longitude,company_id:company_id,url:location.href};
		$.ajax({
		     url: '/link/site',
		     type: 'post',
		     dataType: 'json',
		     data:obj,
		     success:function(result){
		     	if(result.code=='SUCCESS'){
			        $('#site_local').text(result.data.site_name);
			        $('#site_local').attr('href',result.data.url);
			        $('#site_near').show();
		        }
		     },
		     error:function(result){
		     	tipsAlert(result);
		     },
		 });	    
	}

	function cancelFun(data){
        tipsAlert("用户拒绝获得当前位置");

	}




});