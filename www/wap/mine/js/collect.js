                                                                                                                                                                                                                                                                                                                                                                                                                           
var swipeHandler;
var resultData;
var goods_list_data = {rows:[]};
var mode  = 1;
window.onload = function(){
    //var str=''
    //var result = JSON.parse(str);
    //getDataResult(result);
    //tipsAlert('token');
	getTokenFromUrl();
	sendPostData({page:1,pagesize:10,type:1},ApiUrl+'m/favorite',getDataResult);
	window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
	$(".c_span1").click(function(){
		mode = 2;
		if(goods_list_data.rows.length<1){
			return ;
		}
		$(".circle").show();
		$(".c_span1").hide();
		$(".c_span2").show();
		$(".c_span3").show();

	});
	$(".c_span2").click(function(){
		if($(".circle").hasClass("circle-red")){
			show_tips_content2({msg:'你确定要删除该商品？',okbtn:'取消',canbtn:'确定',canfun:cancelHandler});
			$(".circle").show();
		}
	});
	function cancelHandler(){
		var goods_arr = [];
		$.each($(".circle"),function(){
			if($(this).hasClass("circle-red")){
				goods_arr.push($(this).attr('id'));
			}
		});
		if(goods_arr.length>0){
			sendPostData({ids:goods_arr.join(",")},ApiUrl+'m/favorite/del',function(result){
				if(result.code ==1){
					for(var key in goods_arr){
						removeCollect([goods_arr[key]]);
					}

					swipeHandler.before = true;
					swpierEvemt(true);
				}
			});
		}
	}

	$('.c_span3').click(function(){
		$('.c_span1').show();
		$('.c_span2').hide();
		$('.c_span3').hide();
		$(".circle").hide();
		mode = 1;
	});
};

function removeCollect(id){
	if(goods_list_data.rows.length<1){
		return;
	}
	for(var i = 0;i < goods_list_data.rows.length;i++){
		if(goods_list_data.rows[i].goods_id == id){
			goods_list_data.rows.splice(i,1);
			return;
		}
	}
}

function chooseGoods(event){
	event.stopPropagation();
	if($(event.target).hasClass("circle-gray")){
		$(event.target).removeClass("circle-gray").addClass("circle-red");
	}
	else{
		$(event.target).removeClass("circle-red").addClass("circle-gray");
	}
}

function getDataResult(result){
	resultData = result;
	//tipsAlert(JSON.stringify(resultData));
	if(resultData.code == 1){
		if(resultData.data.page  == 1 ){
			goods_list_data.rows.splice(0);
		}
		goods_list_data.rows = goods_list_data.rows.concat( resultData.data.rows);

	}else{
		tipsAlert(resultData.msg)
	}


	//todo
	$('.c_span1').show();
	$('.c_span2').hide();
	$('.c_span3').hide();
	$(".circle").hide();
	if(!result.data.rows || result.data.rows.length<=0){
		$('.c_span1').hide();
		$('#number2').html('0');
		$('#collect_swiper').html('');
		return ;
	}else{
		$('#number2').html(result.data.count);

	}
	mode = 1;
	renderSlide(result.data,0);
	var total = get_total_page(resultData.data.count,resultData.data.pagesize);
	swipeHandler.setPage(parseInt(result.data.page),total)

}
function jump_to_detail(tpl_id){
	if(mode !=1){
		return ;
	}
	var url = 'zooer://productdetail?tpl_id='+tpl_id;
	jump_to_url(url);
}

function renderSlide(data,refresh) {


	var source = '{{each rows as value i}}'
		+ '{{if i%2 == 0}}'
		+ '<div class="swiper-slide">'
		+ '{{/if}}'
		+ '<div class="product-list" onclick="jump_to_detail(\'{{value.tpl_id}}\')">'
		+ '	<figure>'
		+ '		<div class="pic"><img src="{{value.pic_path}}"/></div>'
		+ '		<figcaption>'
		+ '			<h3>{{value.title}}</h3>'
		+ '			<p>'
		+ '				<span style="color:#ff3d23;font-size:16px;">&yen;{{value.price}}&nbsp;&nbsp;</span>'
		+                '{{if value.market_price>0}}'
		+ '				<del>&yen;{{value.market_price}}</del>'
		 +               '{{/if}}'
		+ '			</p>'
		+ '		</figcaption>'
		+ '	</figure>'
		+ '	<span class="circle circle-gray" style="display:none;" id="{{value.goods_id}}" onclick="chooseGoods(event);"></span>'
		+ '</div>'
		+ '{{if i%2 == 1}}'
		+ '</div>'
		+ '{{else if i%2==0 && i == rows.length-1}}'
		+ '</div>'
		+ '{{/if}}'
		+ '{{/each}}'

	var render = template.compile(source);
	var str = render(data);
	if(!swipeHandler){
		$('#collect_swiper').html(str);
		swipeHandler = new SwiperUtils({
			container:'.swiper-container',
			swpierHandler:swpierEvemt,
			collectswiper:'#collect_swiper',
			deep:300
		});
		$('.swiper-container').css('height',$(window).height() - 88);
	}else{
		swipeHandler.setSwiperSlider(str);
	}
	//if(refresh){
	//	swipeHandler.refreshSlider(str);
	//}else{

	//}

	var width=$(".pic").width();
	$(".pic img").height(width);

}

function swpierEvemt(before){
	var p = 1;
	if(!before){
		p = parseInt(resultData.data.page)+1;

	}
	sendPostData({page:p,pagesize:10,type:1},ApiUrl+'m/favorite',getDataResult);
}