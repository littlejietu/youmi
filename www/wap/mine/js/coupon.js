
var currentType = 0;
$(function(){
	getTokenFromUrl();
    // getDataResult(result);
	$(".coupon-1 ul li").click(function(){
		$(this).children("a").addClass("current").parent().siblings().children("a").removeClass("current");
		var index=$(this).index();
		currentType = index;
		sendPostData({page:1,type:index},ApiUrl+"m/coupon",getDataResult)
	});


    $("#default").click();
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';


});



 function getDataResult(result){
	 for(var key in result.data.list){
		 var value = result.data.list[key];
		 value.get_date = new Date(value.get_date*1000).Format("yyyy-MM-dd");
		 value.overdue_date = new Date(value.overdue_date*1000).Format("yyyy-MM-dd");
	 }



 	 var source = '{{each list as value i}}' 
	            +' <div class="list">'
				+'<div class="left">￥<span>{{value.price}}</span>.0</div>'
				+'<div class="right">'
				+'	<h3>{{value.shop_name}}</h3>'
				+'	<ul>'
				+'		<li>满{{value.condition}}元使用</li>'
				// +'		<li>限尾号8214的手机使用</li>'
				+'		<li>{{value.get_date}}至{{value.overdue_date}}</li>'
				+'	</ul>'
				+' </div>'
				+'{{if 1 == index}}'
				+'<div class="pic"><img src="../images/9.png"/></div>'
				+'{{ else if 2 == index}}'
				+'<div class="pic"><img src="../images/8.png"/></div><div class="zhezhao2"></div>'
				+'{{ /if}}'
				+' <div class="clear"></div>'
				+'<i></i>'
			    +'</div>'    
	            +'{{/each}}';
		result.data.index = currentType;
        var render = template.compile(source);
            str = render(result.data);
            $("#coupon-list").html(str);

 }



   