$(function(){
	var token = get_user_token();
	if(!token){
		location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
		return;
	}
	getTokenFromUrl();
    sendPostData({},ApiUrl+'discount_goods/get_category',getDataResult2);
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
});
var swipeHandler;
var resultData;
var currentCategory;
function getDataResult(result){
	if(result.code !=1){
		tipsAlert(result.msg);
		return ;
	}
	resultData = result;
	var source='{{each goods_list as value i}}'
				+ '{{if i%2 == 0}}'
				+ '<div class="swiper-slide">'
				+ '{{/if}}'
	            +'<div class="product-list">'
				+'	<figure>'
				//+'     <a onclick="jump_to_url(\'{{value.to_url}}\');">'
				+ '      <a onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');">'
				+'		<div class="pic"><img src="{{value.pic_path}}"/></div>'
				+'		<figcaption>'
				+'			<h3>{{value.title}}</h3>'
				+'			<p>'
				+'				<span class="color2">&yen;{{value.price}}</span>'
				+'				<span  style="background:#fff;color:#999;">剩余{{value.total-value.saled}}</span>'
				+'				<del class="del">&yen;{{value.market_price}}</del>'
				+'			</p>'
				+'		</figcaption>'
				+'     </a>'
				+'</figure>'
				+'{{if value.total==value.saled}}'
				+'<div class="product-mask"></div>'
				+'<div class="product-outMask">售光了</div>'
				+'{{/if}}'
				+ '</div>'
				+ '{{if i%2 == 1}}'
				+ '</div>'
				+ '{{else if i%2==0 && i == goods_list.length-1}}'
				+ '</div>'
				+ '{{/if}}'
	  			+'{{/each}}'

	var render = template.compile(source);
    str = render(result.data);
    
     str = render(result.data);
    if(!swipeHandler){
        $('#nine').html(str);
        swipeHandler = new SwiperUtils({
            container:'#goods_cont',
            swpierHandler:swpierEvemt,
            collectswiper:'#nine',
            deep:200
        });
        $('.swiper-container').css('height',$(window).height() - 98);

    }else{
        swipeHandler.setSwiperSlider(str);
    }
    var total = parseInt(resultData.data.totalpage);
    swipeHandler.setPage(parseInt(resultData.data.page),total);
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfo';

	var width=$(".pic").width();
	$(".pic img").height(width);
}
function swpierEvemt(before){
    var p = 1;
    if(!before){
        p = parseInt(resultData.data.page)+1;
    }
    sendPostData({page:p,pagesize:10,type:3,category:currentCategory},ApiUrl+'discount_goods',getDataResult);
}

function getDataResult2(result){
	var source='{{each data as value i}}'
				+'<div class="swiper-slide swiper-slide-visible swiper-slide-active slide-nav">'
				// +'{{if i==0}}'
				// +'	<a class="active">{{value.name}}</a>'
				// +'{{else}}'
				+'<a id="{{value.id}}">{{value.name}}</a>'
				// +'{{/if}}'
				+'</div>'
				+'{{/each}}'
	var render = template.compile(source);
    str = render(result);
    $("#nine-nav").html(str);	
    $('#category_cont').swiper({
			slidesPerView:'auto',
			offsetPxBefore:0,
			offsetPxAfter:0,
			calculateHeight: true
	});

	
	$(".swiper-wrapper .slide-nav").click(function(){
		$(this).children("a").addClass("active").parent().siblings().children("a").removeClass("active");
		currentCategory = $(this).children("a").attr('id');
		sendPostData({page:1,pagesize:10,type:3,category:currentCategory},ApiUrl+'discount_goods',getDataResult);
	});
	$($(".swiper-wrapper .slide-nav").children("a")[0]).click();
    
}

