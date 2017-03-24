$(function() {

	if(isWeixinBrowser()){
		initWxYm(['getLocation'],function(res){
			if(res==1){
				wxGetLocation({success:locSuccess,cancel:cancelFun});

			}
		},location.href,0);
	}

	function locSuccess(data){
        var latitude = data.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = data.longitude; // 经度，浮点数，范围为180 ~ -180。
        $('#lnglat').html('经度:'+longitude+' 纬度:'+latitude);
	}

	function cancelFun(data){
        tipsAlert("用户拒绝获得当前位置");

	}

});