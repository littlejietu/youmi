$(function(){
    getTokenFromUrl();
    sendPostData({"page":1,},ApiUrl+'m/integral',getDataResult);
    sendPostData({},ApiUrl+'m/addr/addr_list',getAddress);
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';

    //var width=$(window).width();

    //$("#zhezhao2").css("left",width+'px');
    $("#zhezhao2").hide();
    $("#zhezhao").hide();
    $("#zhezhao2").addClass('animated')
    $("#zhezhao").addClass('animated')
    //$("#arow2").click(function(){
    //    $("#zhezhao2").css("left",width+'px');
    //    $("#zhezhao2").show();
    //    $("#zhezhao2").animate({left:"0px"},500);
    //});
    //$(".left-arow").click(function(){
    //    $("#zhezhao").animate({left:width+'px'},500,function(){
    //        $("#zhezhao").hide();
    //    });
    //    $("#zhezhao2").animate({left:width+'px'},500,function(){
    //        $("#zhezhao2").hide();
    //    });
    //});
    $("#arow2").click(function(){
        $("#zhezhao2").show();
        $("#zhezhao2").removeClass('slideOutRight');
        $("#zhezhao2").addClass('slideInRight');
    });
    $(".left-arow").click(function(){
        $("#zhezhao").removeClass('slideInRight');
        $("#zhezhao").addClass('slideOutRight');
        $("#zhezhao2").removeClass('slideInRight');
        $("#zhezhao2").addClass('slideOutRight');

    });
});
function getAddress(result){
    addressData = result;
    if(result.code !=1){
        return;
    }

    var source = '{{each data as value i}}'
        +'<div class="wrapper" onclick="addreddClick(event,{{value.id}});">'
        +'<h3>{{value.real_name}}&nbsp;&nbsp;{{value.mobile}}</h3>'
        +'<p>&nbsp;{{value.province_name}}&nbsp;{{value.city_name}}&nbsp;{{value.area_name}}&nbsp;{{value.address}}</p>'
        +'</div>'
        +'{{/each}}';
    var render = template.compile(source);
    var str = render(result.data);
    $(".address2").html(str);
}

function addreddClick(event,addid){
    addr_id = addid;
    //var wwidth = $(window).width();

    //$("#zhezhao").animate({left:wwidth+'px'},500,function(){
    //    $("#zhezhao").hide();
    //});
    $("#zhezhao").removeClass('slideInRight');
    $("#zhezhao").addClass('slideOutRight');

    $(".address2 .wrapper").css("border","");
    $(event.currentTarget).css("border","1px solid #ff3d23");


}


var exchange_id = 0;
var resultData ;
var addr_id;
var currentcost;

function showRecord(){
    if(deviceInfo){
        location.href="market.html?token="+urlToken;
    }else{
        location.href="market.html";
    }

}
function getDataResult(result){
    resultData = result.data;
    var source='{{each goods_list as value i}}'
	    		+'<li>'
                +'<div class="pic2"><img src="{{value.goods_url}}"/></div>'
                +'<h3>{{value.goods_name}}</h3>'
                +'<p>{{value.integral_cost}}积分</p>'
                +'<p><button onclick="exchangeGoods({{value.id}},{{value.integral_cost}});">立即兑换</button></p>'
                +'</li>'
				+'{{/each}}'
	var render = template.compile(source);
        str = render(result.data);
        $("#integral_list").html(str);
    var width=$(".pic2").width();
    $(".pic2 img").height(width);

    var render = template('integral2',result.data);
    $("#integral").html(render);

}

function confirm(){
    sendPostData({goods_id:exchange_id,addr_id:addr_id},ApiUrl+'m/integral/exchange',function(result){
        if(result.code == 1){

            tipsAlert('兑换成功！')
            resultData.integral -= currentcost;
            var render = template('integral2',resultData);
            $("#integral").html(render);
        }else{
            tipsAlert(result.msg)
        }
        currentcost =0;

    });
}

function exchangeGoods(goods_id,cost){
    if(resultData.integral < cost){
        tipsAlert('兑换积分不足！');
        return ;
    }
    if(!addr_id){
        //var width=$(window).width();
        //$("#zhezhao").css("left",width+'px');
        //$("#zhezhao").show();
        //$("#zhezhao").animate({left:"0px"},500);
        $("#zhezhao").show();
        $("#zhezhao").removeClass('slideOutRight');
        $("#zhezhao").addClass('slideInRight');

        return ;
    }
    exchange_id =goods_id;
    currentcost =cost;
    show_tips_content2({msg:'此次兑换将使用<span style="color: #ff3d23;"> '+cost+' </span>积分，确定兑换吗？',okbtn:'取消',canbtn:'确定',canfun:confirm});
}