$(function(){
    // var str='{"msg":"SUCCESS","code":1,"content":{"record_list":[{"id":"1","user_id":"1","goods_id":"1","shop_id":"1001","num":"1","integral_cost":"100","exchange_date":"1458287936","status":"1","goods_name":"\u8863\u670d","goods_url":"http:\\\/\\\/www.jshgwsc.com\\\/data\\\/upload\\\/shop\\\/store\\\/goods\\\/1\\\/1_04418254218437108_240.jpg"}],"page":1,"total":1,"integral":110}}'
    // var result = JSON.parse(str);
    // getDataResult(result);
	getTokenFromUrl();
    sendPostData({'page':1},ApiUrl+'m/integral/record_list',getDataResult);
});
function getDataResult(result){
	var source = '{{each record_list as value i}}'
            	+'<div class="jifen2-1">'
				+'<a onclick="gotoOrderDetails(\'{{value.id}}\')">'
				+'	<div class="left"><img src="{{value.goods_url}}"/></div>'
				+'	<div class="right">'
				+'		<p class="p1">{{value.goods_name}}</p>'
				+'		<p class="p1" style="margin-top:0;">{{value.integral_cost}}积分</p>'
				+'{{ if value.status ==1}}'
				+'		<p class="p2">兑换成功</p>'
				+'{{else}}'
				+'		<p class="p2">兑换失败</p>'
				+'{{/if}}'
				+'	</div>'
				+'	<div class="clear"></div>'
				+'	<i></i>'
				+'</a>'
				+'</div>'    
        		+'{{/each}}';
    var render = template.compile(source);
    str = render(result.data);
    $("#integral_record").html(str);
    var width=$(".left").width();
    $(".left img").height(width);
}

function gotoOrderDetails(record_id){
	var url = '../wallet/exchange-details.html?record_id='+record_id;
	if(urlToken){
		url +='&token='+urlToken;
	}
	location.href =url;
}