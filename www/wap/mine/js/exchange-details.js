$(function(){
	sendPostData({record_id:getUrlParam("record_id")},ApiUrl+'m/integral/detail',getDataResult)
	function getDataResult(result){
		var source = '<div class="jifen2-1">'
			+'<div class="left"><img src="{{goods_url}}"/></div>'
			+'<div class="right">'
			+'	<p class="p1">{{goods_name}}</p>'
			+'	<p class="p1" style="margin-top:0;">{{integral_cost}}积分</p>'
			+'	<p class="p2">{{status}}</p>'
			+'</div>'
			+'<div class="clear"></div>	'
			+'</div>'
			+'<div class="jifen2-2">'
			+'	<div class="list">'
			+'	　兑换时间<span>{{exchange_date}}</span>'
			+'	</div>'
			+'	<div class="list">'
			+'	　兑换状态<span>{{status}}</span>'
			+'	</div>'
			+'</div>   '

		if(result.code ==1){
			var render = template.compile(source);
			result.data.exchange_date = new Date(result.data.exchange_date*1000).Format('yyyy-MM-dd hh:mm:ss');
			if(result.data.status ==1){
				result.data.status = '兑换成功'
			}else{
				result.data.status = '兑换失败'
			}

			var str = render(result.data);
			$("#exchange-details").html(str);
		}else{

		}
		var width=$(".left").width();
		$(".left img").height(width);
	}
});


