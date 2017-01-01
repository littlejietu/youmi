var currentIndex = 0;
var resultData;
var result1;
var dotClassList=["dot-red","dot-blue","dot-orange"];
function setCartNum(){
	$("#cart_num_span").find('i').remove();
	var obj = get_user_data_from_local('cart');
	var num = 0;
	//if(obj&& obj.goods_list && obj.goods_list.length>0){
	//	num = obj.goods_list.length;
	//}
	if(obj&& obj.goods_list){
		for(var key in obj.goods_list){
			num += parseInt(obj.goods_list[key].num);
		}
	}

	if(num > 0){
		$("#cart_num_span").append("<i>"+ num+"</i>");
	}

}
$(function(){
	var token = get_user_token();
	if(!token){
		location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
		return;
	}

	var str = get_string_fromlocal('location')
	if(!str){
		str = '杭州'
	}

	$('#location').html(str);
    sendPostData({},ApiUrl+'goods_class',getDataResult);
	$('#search_input').click(function(){
		location.href = '../search.html'
	});
	setCartNum();
	sendPostData({}, ApiUrl + 'm/message/unread', function (result) {
		$('#message_icon span').remove();
		if (result.code == 1) {
			//$("#msg").html('<span>' + 10 + '</span>');
			var num = parseInt(result.data.un_read_num);
			var message = get_user_data_from_local('message');
			for(var key in message){
				if(!message[key].read){
					num++;
				}
			}

			if(num >0){
				$('#message_icon').append("<span>"+num+"</span>");
			}
			save_user_data_to_local('messageNum',num);

		} else {
			save_user_data_to_local('messageNum', 0);
		}


	});


	// $("#categoryType0").click();
	// var str='{"code":1,"msg":"SUCCESS","action":"goods_class_more","data":{"banner_list":[{"pic_url":"http:\/\/data.zooernet.com\/upload\/img\/9street\/paper1.png","to_url":""},{"pic_url":"http:\/\/data.zooernet.com\/upload\/img\/9street\/paper2.png","to_url":""}],"class_list":[{"id":"","name":"\u5e38\u7528\u5206\u7c7b","tag_color":"#f89067","child":[{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""}]},{"id":"","name":"\u731c\u4f60\u559c\u6b22","tag_color":"#2db3e5","child":[{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""},{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""},{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""}]},{"id":"","name":"\u4e3a\u4f60\u63a8\u8350","tag_color":"#cd94d8","child":[{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""},{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""},{"id":111,"name":"\u8fdb\u53e3\u5976\u7c89","to_url":""},{"id":112,"name":"\u8fdb\u53e3\u98df\u54c1","to_url":""},{"id":113,"name":"\u4f11\u95f2\u96f6\u98df","to_url":""}]}]}}'
 //    result1 = JSON.parse(str);
 //    getDataResult1(result1);
 // 	$("#location").click(function(){
	// 	$(".lo_mask").show();
	// 	$("body").css("overflow","hidden");	
	// });
	// $(".lo_all ul li").click(function(){
	// 	$(this).css("background","#eee").siblings().css("background","#fff"); 
	// 	$(".lo_mask").hide();
	// 	var text=$(this).html();
	// 	$("#location").html(text);
	// })
	// $("#left_select").click(function(){
	// 	$(".lo_mask").hide();
	// });
	 $("#location").click(function(){
        sendPostData({}, "http://"+window.location.host+"/api/area/get_nationwide_area",locationData);
        $(".lo_mask").show();
        $("body").css("overflow","hidden"); 

    });
	FastClick.attach(document.body);
});
function locationData(result){
    var source='{{each data as value i}}'
                // +'{{if i==0}}'
                // +'<li style="background:#eee">{{value.name}}</li>'
                // +'{{else}}'
               +'<li>{{value.name}}</li>'
               // +'{{/if}}'
               +'{{/each}}'
    var render = template.compile(source);
    var str = render(result.data);
    $("#name").html(str); 
    $(".lo_all ul li").click(function(){
        $(this).css("background","#eee").siblings().css("background","#fff"); 
        $(".lo_mask").hide();
        var text=$(this).html();
		save_string_tolocal('location',text);
        $("#location").html(text);
    })
    $("#left_select").click(function(){
        $(".lo_mask").hide();
    });
}
function getDataResult(result){
	resultData = result;
    var source = '{{each class_list as value i}}'   
            +'    <li id="categoryType{{i}}"><a><img src="{{value.icon_untouch}}"/>{{value.name}}</a></li>'
            +    '{{/each}}';
	var render = template.compile(source);
	var str = render(result.data);
	$("#category1").html(str);
	$(".fenlei-left ul li").click(function(){
		// alert("ddd")
		currentIndex=$(".fenlei-left ul li").index(this); //È¡µ±Ç°µã»÷µÄliµÄË÷ÒýÖµ
		var data1 = getDataByIndex(currentIndex);
		sendPostData({category_id:data1.category_id ,type:data1.type},ApiUrl+'goods_class/more',getDataResult1);
		var scrollHeight=0;
		$(".fenlei-left ul li").each(function(index){
			if(currentIndex>index)
			{
				scrollHeight+=$(this).height();
			}
			var mydata = getDataByIndex(index);
			if($(this).find("a").attr("class")=="on")
			{//
				if(currentIndex==index)return;
				$(this).find("a").removeClass("on");

				$(this).find("img").attr("src",mydata.icon_untouch);
				//
			}else{
				if(currentIndex==index)
				{

					$(this).find("a").addClass("on");
					//Í¼Æ¬Ìæ»»  todo.....
					$(this).find("img").attr("src",mydata.icon_touch);
					$(".fenlei-left").animate({scrollTop:scrollHeight+'px'},1000);
					//var size=$(".fenlei-left ul li").size();
					//ajax»ñÈ¡·ÖÀàÊý¾ÝÏÔÊ¾

				}
			}
		});
	});
	$(".fenlei-left ul li")[0].click();
}

function getDataByIndex(index){
	for( i= 0 ;i < resultData.data.class_list.length;i++){
		if(i == index){
			return resultData.data.class_list[i];
		}
	}
}
var slideInit = false;
function getDataResult1(result){

	var source='{{each banner_list as value i}}'
				+'<li><a href="{{value.to_url}}"><img src="{{value.pic_url}}"></a></li>'
       			+'{{/each}}'
    var render=template.compile(source);
	var str=render(result.data);
	$("#category-banner").html(str);
	//if(!slideInit){
		TouchSlide({
			slideCell:"#slideBox",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			mainCell:".bd ul",
			effect:"leftLoop",
			autoPage:true,//自动分页
			autoPlay:true //自动播放
		});
	//}


	var source='{{each class_list as value i}}'
				+'<h3 id="category-title1"><i class="{{dotClassList[i%3]}}"></i><label>{{value.name}}</label></h3>'
				+'<ul id="category-list1">'
				+'{{each value.child as value1 j}}'
				+'<li><a onclick="jump_to_search(\'{{value1.to_url}}\',\'category\')">{{value1.name}}</a></li>'
				+'{{/each}}'
				+'</ul>'
				+'{{/each}}'
		var render=template.compile(source);
		result.data.dotClassList = dotClassList;
		str=render(result.data);
		$("#category_list").html(str);	
	
}
