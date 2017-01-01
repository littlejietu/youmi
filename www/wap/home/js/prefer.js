$(function(){
	var token = get_user_token();
	if(!token){
		location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
		return;
	}
	getTokenFromUrl();
 	sendPostData({page:1,pagesize:100,type:5},ApiUrl+'discount_goods',getDataResult);
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
});
// var swipeHandler;
// var resultData;
function getDataResult(result){
	if(result.code !=1){
		tipsAlert(result.msg);
		return ;
	}
	// resultData = result;
	var source='{{each goods_list as value i}}'
				// +'<div class="swiper-slide">'
	            //+'<div class="tehui-list" onclick="jump_to_url(\'{{value.to_url}}\');">'
	            +'<div class="tehui-list" onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');">'

				+'	<div class="pic"><a ><img src="{{value.pic_path}}"/></a></div>'
				+'		<div class="text">'
				+'			<div>'
				+'				<h3 style="margin-bottom:20px;">{{value.title}}</h3>'
				+'				<p><span class="color2 font2">&yen;{{value.price}}&nbsp;&nbsp;</span><del>&yen;{{value.market_price}}<del></p>'
				//+'				<p style="color:#000;font-size:12px;">2451人想买</p>'
				+'				<div class="bar">'
				+'					<span class="span1"><label>已抢{{value.saled}}/{{value.total}}件</label><i style="width:{{value.saled/value.total*100}}%"></i></span>'
				+'					<span class="span2">立即抢购</span>'
				+'					<div class="clear"></div>'
				+'				</div>'
				+'			</div>'
				+'		</div>'
				+'	<div class="clear"></div>'
				+'</div>'
				// +'</div>'
	  			+'{{/each}}'

	var render = template.compile(source);
    str = render(result.data);
    $('#tehui-list').html(str);
    /*if(!swipeHandler){

        $('#tehui-list').html(str);
        swipeHandler = new SwiperUtils({
            container:'.swiper-container',
            swpierHandler:swpierEvemt,
            collectswiper:'#tehui-list',
            deep:200
        });
        $('.swiper-container').css('height',$(window).height() - 216);

    }else{
        swipeHandler.setSwiperSlider(str);
    }*/
    // var total = parseInt(resultData.data.totalpage);
    // swipeHandler.setPage(parseInt(resultData.data.page),total)

    var width=$(".pic a").width();
    $(".pic a img").height(width);
}

// function swpierEvemt(before){
//     var p = 1;
//     if(!before){
//         p = parseInt(resultData.data.page)+1;

//     }
//     sendPostData({page:p,pagesize:10,type:5},ApiUrl+'discount_goods',getDataResult);
// }