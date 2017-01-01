$(function(){
	var token = get_user_token();
	if(!token){
		location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
		return;
	}
	getTokenFromUrl();
    sendPostData({page:1,pagesize:100,type:2},ApiUrl+'discount_goods',getDataResult);
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
});
var tejia;
var mycount;
// var swipeHandler;
// var resultData;
function getDataResult(result){
	if(result.code !=1){
		tipsAlert(result.msg);
		return ;
	}
	// resultData = result;
	for(var key in result.data.goods_list){
        var ob = result.data.goods_list[key];
        ob.persent = Math.round(ob.saled/ob.total*100,1);
    }
	var source='{{each goods_list as value i}}'
				// +'<div class="swiper-slide">'
	            +'<div class="tehui-list" onclick="jump_by_tpl_id({{value.tpl_id}});" style="position:relative;">'
				+'	<div class="pic"><a ><img src="{{value.pic_path}}"/></a></div>'
				+'	<div class="text">'
				+'		<div>'
				+'			<h3>{{value.title}}</h3>'
                +           '<div style="position:absolute;bottom:0;right:0;left:40%;">'
				+'			   <p class="p1"><span class="color2 font2">&yen;{{value.price}}&nbsp;&nbsp;</span><span class="font3">原始价&yen;{{value.market_price}}</span></p>'
				+'			   <div class="red-bar" style="margin-top:10px;"><i style="width:{{value.persent}}%"></i><span>已售{{value.persent}}%</span></div>'
				+'			   <a onclick="jump_by_tpl_id({{value.tpl_id}});"><span class="gou go" >立即抢购</span></a>'
				+'		     </div>'
                +'      </div>'
				+'	</div>'
				+'	<div class="clear"></div>'
				+'</div>'
				// +'</div>'
	  			+'{{/each}}'

	var render = template.compile(source);
    str = render(result.data);
     $('#seckill').html(str);
    // if(!swipeHandler){

    //     $('#seckill').html(str);
    //     swipeHandler = new SwiperUtils({
    //         container:'.swiper-container',
    //         swpierHandler:swpierEvemt,
    //         collectswiper:'#seckill',
    //         deep:200
    //     });
    //     $('.swiper-container').css('height',$(window).height() - 96);

    // }else{
    //     swipeHandler.setSwiperSlider(str);
    // }
    // var total = parseInt(resultData.data.totalpage);
    // swipeHandler.setPage(parseInt(resultData.data.page),total);


  //   if(result.data.start_time*1000 > new Date().getTime()){
  //   	var tejia=new countDown(result.data.start_time*1000,'tejia');
		// tejia.init(); //启动倒计时计时器
		// $("#text").text("本场未开枪");
		// $(".go").removeClass("gou").addClass("gou2");
  //   }else if(result.data.start_time*1000<new Date().getTime()){
	var time = new Date().getTime();
	if(result.data.end_time*1000 >0){
		$('#time_left').show();
    		tejia =new countDown(result.data.end_time*1000+time,'tejia',function(){
				$("#text").text("本场已结束");
			});
			tejia.init();
			$("#text").text("本场已开枪");
    	}else if(result.data.end_time==0){
    		$("#text").text("本场已结束");
    	}
    // }

    var width=$(".pic a").width();
    $(".pic a img").height(width);
}



// function swpierEvemt(before){
//     var p = 1;
//     if(!before){
//         p = parseInt(resultData.data.page)+1;

//     }
//     sendPostData({page:p,pagesize:10,type:2},ApiUrl+'discount_goods',getDataResult);
// }