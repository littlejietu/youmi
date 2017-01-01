$(function(){
    var token = get_user_token();
    if(!token){
        location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
        return;
    }
    getTokenFromUrl();
    sendPostData({page:1,pagesize:10,type:4},ApiUrl+'discount_goods',getDataResult);
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
    FastClick.attach(document.body);
});
var swipeHandler;
var resultData;
var tejia;
function getDataResult(result){
    if(result.code !=1){
        tipsAlert(result.msg);
        return ;
    }
	resultData = result;
    for(var key in result.data.goods_list){
        var ob = result.data.goods_list[key];
        ob.persent = Math.round(ob.saled/ob.total*100,1);
    }
    var source='{{each goods_list as value i}}'
				+'<div class="swiper-slide">'
	            +'<div class="tehui-list" style="position:relative;">'
				+'	<div class="pic" onclick="jump_by_tpl_id({{value.tpl_id}});"><a><img src="{{value.pic_path}}"/></a></div>'
				+'	<div class="text">'
				+'		<div>'
				+'			<h3>{{value.title}}</h3>'
                +'          <div style="position:absolute;left:40%;bottom:0;right:0;">'
				+'			  <p class="p1">'
				+'				<span class="color2 font2">&yen;{{value.price}}&nbsp;&nbsp;</span>'
				+'				<span class="red-bar" style="margin:-4px 0;"><i style="width:{{value.persent}}%"></i><span>已售{{value.persent}}%</span></span>'
				+'			  </p>'
				+'			  <p class="p2">原始价&yen;{{value.market_price}}<i onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1);"></i></p>'
				+'		    </div>'
                +'      </div>'
				+'	</div>'
				+'	<div class="clear"></div>'
				+'</div>'
				+'</div>'
	  			+'{{/each}}'

    var render = template.compile(source);
    var str = render(result.data);
    if(!swipeHandler){
        $('#snap-up').html(str);
        swipeHandler = new SwiperUtils({
            container:'.swiper-container',
            swpierHandler:swpierEvemt,
            collectswiper:'#snap-up',
            deep:200
        });
        $('.swiper-container').css('height',$(window).height() - 98);

    }else{
        swipeHandler.setSwiperSlider(str);
    }

    var width=$(".pic a").width();
     $(".pic a img").height(width);

    var total = parseInt(resultData.data.totalpage);
    swipeHandler.setPage(parseInt(resultData.data.page),total)
    var time = new Date().getTime();

    if(result.data.end_time*1000 >0){
        tejia=new countDown(time+result.data.end_time*1000,'qianggou',endfunction);
        $("#everyday").html('活动进行中');
         $("#time").show();
    }else{
         tejia=new countDown(0,'qianggou');
        $("#everyday").html('每日'+result.data.startTime+'点开始');
        $("#time").hide();
    }
		tejia.init();

    function endfunction(){
        $("#everyday").html('每日'+result.data.startTime+'点开始');
    }
}


function swpierEvemt(before){
    var p = 1;
    if(!before){
        p = parseInt(resultData.data.page)+1;

    }
    sendPostData({page:p,pagesize:10,type:4},ApiUrl+'discount_goods',getDataResult);
}