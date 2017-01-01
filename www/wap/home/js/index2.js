var dataIndex4 = 0;
var homeLoad = 0;
var dataIndex9 = 0;
function setCartNum() {
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
    if (num > 0) {
        $("#cart_num_span").append("<i>" + num + "</i>");
    }

}
function locationData(result){
    var source='{{each data as value i}}'
                // +'{{if i==0}}'
                // +'<li style="background:#eee">{{value.name}}</li>'
                // +'{{else}}'
               +'<li>{{value.name}}</li>'
               // +'{{/if}}'
               +'{{/each}}';
    var render = template.compile(source);
    var str = render(result.data);
    $("#name").html(str); 
    $(".lo_all ul li").click(function(){
        $(this).css("background","#eee").siblings().css("background","#fff"); 
        $(".lo_mask").hide();
        var text=$(this).html();
        save_string_tolocal('location',text);
        $("#location").html(text);
        $("body").css("overflow","auto");
    });
    $("#left_select").click(function(){
        $(".lo_mask").hide();
        $("body").css("overflow","auto");
    });
}
$(function() {
    if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }
    var str = get_string_fromlocal('location')
    if(!str){
        str = '杭州'
    }

    $('#location').html(str);
    $("#location").click(function(){
        sendPostData({}, "http://"+window.location.host+"/api/area/get_nationwide_area",locationData);
        $(".lo_mask").show();
        $("body").css("overflow","hidden"); 

    });

    var latitude =1; // 纬度，浮点数，范围为90 ~ -90
    var longitude =1;
    var url = location.href;
    //url = url.split('?')[0];
    if(isWeixinBrowser()){
        initWx(['getLocation','onMenuShareTimeline','onMenuShareAppMessage'],function(res){

            if(res == 1){
                wxGetLocation({success:locSuccess,cancel:getHomeData});

                shareObj.link = WapSiteUrl+'/home/index.html';
                wxShareTimeLine(shareObj);
                wxShareToFriend(shareObj);
            }else{
                getHomeData();
            }
        },url);
    }else{
        getHomeData();
    }



    //getHomeData();
    function locSuccess(res){
        latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
        //sendPostData({lng: longitude, lat:latitude}, ApiUrl + "home", homeResult);
        //tipsAlert('欢迎来到九号商城');
        getHomeData();

    }

    function getHomeData(){
        if(homeLoad){
            return;
        }
        //tipsAlert('返回已收到')
        homeLoad = 1;
        sendPostData({lng: latitude, lat:latitude}, "http://"+window.location.host+"/api/home", homeResult);
    }
    //alert(get_user_token());、

    function homeResult(result) {

        if(result.data.shop){
            save_string_tolocal('shop_id', result.data.shop.shop_id);
        }else{
            save_string_tolocal('shop_id', 1);
        }

        //alert(result.data.shop.shop_id);
        dataIndex4 = 0;
        // dataIndex5 = 0;
        dataIndex9 = 0;
        $.each(result.data.data, function (index, data) {

            switch (data.type) {
                case 1:

                    getDataResult0(data);
                    break;
                case 2:

                    getDataResult(data);
                    break;
                case 3:
                    source = '{{each data as value i}}'
                        + '<li class="slide" ><a onclick="jump_to_url(\'{{value.to_url}}\');">{{value.title}}</a></li>'
                        + '{{/each}}';
                    render = template.compile(source);
                    $("#hot_ad").html(render(data));
                    break;
                case 4:
                    if (dataIndex4 == 0) {
                        getDataResult1(data);

                    } else if (dataIndex4 == 1) {
                        getDataResult11(data);

                    }
                    else if (dataIndex4 == 2) {
                        getDataResult111(data);

                    }
                    else if (dataIndex4 == 3) {
                        getDataResult1111(data);

                    }
                    else if (dataIndex4 == 4) {
                        getDataResult11111(data);

                    }

                    dataIndex4++;
                    break;
                case 5:
                    getDataResult2(data);

                    break;
                case 6:
                    getDataResult3(data);
                    break;
                case 7:
                    getDataResult4(data);
                    break;
                case 8:
                    getDataResult5(data);
                    break;
                case 9:
                    if (dataIndex9 == 0) {
                        getDataResult6(data);

                    } else if (dataIndex9 == 1) {
                        getDataResult7(data);

                    }
                    dataIndex9++;
                    break;
                case 10:
                    getDataResult8(data);
                    break;
            }
        })
        hotstart();
        if (get_user_token()) {
            /*
             * 请求购物车
             * */
            sendPostData({}, ApiUrl + "m/cart", function (result) {
                if (result.code == 1) {
                    save_user_data_to_local('cart', result.data.cart_list[0]);

                } else {

                }
                setCartNum();

            });
            ///*
            //* 初始化用户数据
            //* */
            sendPostData({}, ApiUrl + "m/user/get", function (result) {
                if (result.code == 1) {
                    save_user_data_to_local('userInfo', result.data);
                    shareObj.link = SiteUrl+'/api/wxauth/go?url='+WapSiteUrl+'/home/index.html&invite_id='+result.data.user_id;
                    wxShareTimeLine(shareObj);
                    wxShareToFriend(shareObj);
                }
            });
            ///*
            //* 请求收藏夹数据
            //* */
            //sendPostData({page:1,pagesize:20,type:1},ApiUrl + "m/favorite",function(result){
            //    if(result.code == 1){
            //        save_user_data_to_local('collect',result.data.rows);
            //    }else{
            //
            //    }
            //
            //});
            ///*
            //*   请求未读数量
            //* */
            sendPostData({}, ApiUrl + 'm/message/unread', function (result) {
                if (result.code == 1) {
                    //$("#msg").html('<span>' + 10 + '</span>');
                    var num = parseInt(result.data.un_read_num);
                    var message = get_user_data_from_local('message');
                    for(var key in message){
                        if(!message[key].read){
                            num++;
                        }
                    }
                    if (num <= 0) {
                        $("#msg").html('');
                    } else {
                        $("#msg").html('<span>' + num + '</span>');
                    }

                    save_user_data_to_local('messageNum',num);

                } else {
                    $("#msg").html('');
                    save_user_data_to_local('messageNum', 0);
                }


            });
        }
    }
});
function getDataResult0(result) {
    var source = '{{each data as value i}}'
        + ' <li><a onclick="jump_to_url(\'{{value.to_url}}\');"><img src="{{value.pic_url}}"></a></li>'
        + '{{/each}}';

    var render = template.compile(source);
    var str = render(result);
    $("#touchslide").html(str);

    TouchSlide({
        slideCell: "#slideBox",
        titCell: ".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell: ".bd ul",
        effect: "leftLoop",
        autoPage: true,//自动分页
        autoPlay: true //自动播放
    });
}
function getDataResult(result) {
    var source = '{{each data as value i}}'
        + '    <li><a onclick="jump_to_url(\'{{value.to_url}}\');"><img class="lazy" src="../images/down.png" data-original="{{value.pic_url}}"/><span>{{value.title}}</span></a></li>'
        + '{{/each}}';

    var render = template.compile(source);
    var str = render(result);
    $("#nav-list").html(str);
    $("img.lazy").lazyload({ 
        skip_invisible : false
    });
}
function getDataResult1(result) {
    var source = ' <div class="index2-wrapper">'
        + '<a onclick="jump_to_url(\'{{to_url}}\');"><img src="{{pic_url}}"/></a>'
        + '</div>'
    var render = template.compile(source);
    var str = render(result.data[0]);
    $("#ad-lg1").html(str);
    // $("#ad-lg2").html(str);
}

function getDataResult2(result) {
    var source = '<div class="index2-wrapper">'
        +'<table>'
         +'   <tr>'
        +'{{each data as value i}}'
        + '{{if i==2}}'
        + '<td style="border-right:0;" class="ad"><a onclick="jump_to_url(\'{{value.to_url}}\');"><img src="{{value.pic_url}}"/></a></td>'
        + '{{else}}'
        + '<td class="ad"><a onclick="jump_to_url(\'{{value.to_url}}\');"><img src="{{value.pic_url}}"/></a></td>'
        + '{{/if}}'
        + '{{/each}}'
        +'</tr>'
        +'</table>'
        +'</div>'

    var render = template.compile(source);
    var str = render(result);
    $("#ad-sm1").html(str);
    var width = $(".ad").width();
    $(".ad").height(width);
    $(".ad a img").height(width);

}

function getDataResult3(result) {
    var source = '{{each goods_list as value i}}'
        + '<div class="swiper-slide">'
        + '<div class="slide-content">'
        + '      <a onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');">'
        + '          <img src="{{value.pic_url}}"/>'
        + '          <h3>{{value.name}}</h3>'
        // + '          <p class="p1"><del>&yen;{{value.original_price}}</del></p>'
        + '          <p class="p2">&yen;{{value.price}}<span onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1);"></span></p>'
        + '      </a>'
        + '</div>'
        + '</div>'
        + '{{/each}}';

    var render = template.compile(source);
    var str = render(result.data);
    $("#rush").html(str);
    $('#container1').swiper({
        //mode:'horizontal',
        slidesPerView: 'auto',
        offsetPxBefore: 10,
        offsetPxAfter: 10,
        calculateHeight: true,

    })
    //result.data.timeleft=5;
    if (result.data.timeleft <= 0) {
        showQGEnd();
    } else {
        qianggou = new countDown(result.data.timeleft * 1000 + new Date().getTime(), 'qianggou');
        qianggou.init();
        if (!mycount) {
            count();
        }
    }

    $("#everyday").html('每日' + result.data.startTime + '点');
}

function getDataResult11(result) {
    var source = ' <div class="index2-wrapper">'
        + '<a onclick="jump_to_url(\'{{to_url}}\');"><img src="{{pic_url}}"/></a>'
        + '</div>'

    var render = template.compile(source);
    var str = render(result.data[0]);
    $("#ad-lg2").html(str);
}
var daojishi;
var qianggou;
var mycount = 0;

function showQGEnd() {
    $("#index_time").html('');
    $("#index_time").html('<img src="images/over2.png"/>');
    $("#index_time img").css({"height": "24px", "width": "100px", "margin-top": "5px"});
}
function showMSend() {
    // $("#index_time2").empty();
    // $("#index_time2").html('<img src="images/over.png"/>');
    // $("#index_time2").css({"left": "0", "top": "0"});
    // $("#index_time2 img").css({"width": "100px", "height": "100px"});
    $(".pic").append("<i></i>");
}

function count() {

    if (!daojishi && !qianggou) {
        clearInterval(mycount);
        return;
    }
    if (daojishi && daojishi.maxTime <= 0) {
        daojishi = null;
        showMSend();
    }
    if (qianggou && qianggou.maxTime <= 0) {
        qianggou = null;
        showQGEnd();
    }
    if (!mycount) {
        mycount = setInterval(count, 1000);
    }
}
function getDataResult4(result) {
    var source = '<span>{{title}}</span>&nbsp;{{subtitle}}<i><img src="{{tip_bg_url}}"/></i>';
    var render = template.compile(source);
    str = render(result.data);
    $("#kill0").html(str);
    $.each(result.data.goods_list, function (index, value) {
        var str;
        if (value.image_type == 0) {
            var source = '<a onclick="jump_to_url(\'{{to_url}}\');">'

                + '<p id="index_time2"><label>还剩</label><span id="miaosha"><i></i>:<i></i>:<i></i></span></p>'
                + '<div class="pic"><img src="{{pic_url}}"/></div>'
                + '</a>';
            //var data = getDataByType(1,result)
            var render = template.compile(source);
            str = render(value);
            $("#kill").html(str);
            // value.timeleft = 5;
            if (value.timeleft <= 0) {
                showMSend();
            } else {
                daojishi = new countDown(value.timeleft * 1000 + new Date().getTime(), 'miaosha');
                daojishi.init();
                if (!mycount) {
                    count();
                }
            }


        }
        else if (value.img_type == 1) {
            var source = '<a onclick="jump_to_url(\'{{to_url}}\');">'

                + '    <div class="right"><img src="{{pic_url}}"/></div>'
                + '    <div class="clear"></div>'
                + '</a>'
            //var data = getDataByType(1,result)
            var render = template.compile(source);
            str = render(value);
            $("#kill2").html(str);
        } else if (value.img_type == 2) {
            var source = '{{each data.goods_list as value i}}'
                + '{{if i > 1}}'
                + '<li>'
                + '<a onclick="jump_to_url(\'{{value.to_url}}\');">'

                + '    <div><img src="{{value.pic_url}}"/></div>'
                + '</a>'
                + '</li>'
                + '{{/if}}'
                + '{{/each}}';
            var render = template.compile(source);
            str = render(result);
            $("#kill3").html(str);
        }
    });

}
function getDataResult111(result) {
    var source = ' <div class="index2-wrapper">'
        + '<a onclick="jump_to_url(\'{{to_url}}\');"><img src="{{pic_url}}"/></a>'
        + '</div>'

    var render = template.compile(source);
    var str = render(result.data[0]);
    $("#ad-lg3").html(str);

}

function getDataResult5(result) {
    var source = '<span>{{title}}</span>&nbsp;{{subtitle}}<i class="discovery"><img src="{{tip_bg_url}}"/></i>';
    var render = template.compile(source);
    var str = render(result.data);
    $("#goods0").html(str);
    var source = '{{each data.goods_list as value i}}'
        + '{{if i==0}}'
        + '<li  id="pic_big" style="width:66.66%;"><a onclick="jump_to_url(\'{{value.to_url}}\');"><div><img src="{{value.pic_url}}"/></div></a></li>'
        + '{{else }}'
        + '<li id="pic_small"><a onclick="jump_to_url(\'{{value.to_url}}\');"><div><img src="{{value.pic_url}}"/></div></a></li>'
        + '{{/if}}'
        + '{{/each}}';
    var render = template.compile(source);
    var str = render(result);
    $("#goods").html(str);
    var width = $("#pic_small").width();
    $("#pic_small div img").height(width);
    $("#pic_big div img").height(width);
    $("#pic_big div img").width(width * 2);


}
function getDataResult1111(result) {
    var source = '<div class="index2-haibao">'
        +' <div class="index2-wrapper">'
        + '<a onclick="jump_to_url(\'{{to_url}}\');"><img src="{{pic_url}}"/></a>'
        + '</div>'
        + '</div>'
    var render = template.compile(source);
    var str = render(result.data[0]);
    $("#ad-lg4").html(str);
}

function getDataResult6(result) {
    var source = '<h3><span>{{title}}</span>&nbsp;{{subtitle}}<i class="choice"><img src="{{tip_bg_url}}"/></i></h3>';
    var render = template.compile(source);
   var  str = render(result.data);
    $("#intro").html(str);
    var source ='<div class="index2-case">'
        +'<div class="swiper-container thumbs-cotnainer" id="container2">'
        +'    <div class="swiper-wrapper" style="width:540px;" >'
        +'{{each goods_list as value i}}'
        + '<div class="swiper-slide">'
        + '<div class="slide-content">'
        //+ '      <a onclick="jump_to_url(\'{{value.to_url}}\');">'
        + '      <a onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');">'
        + '          <img src="{{value.pic_url}}"/>'
        + '         <h3>{{value.name}}</h3>'
        + '         <p class="p1"></p>'
        + '         <br/>'
        + '         <p class="p2">&yen;{{value.price}}</p>'
        // + '         <p class="p2">&yen;{{value.price}}&nbsp;<del>&yen;{{value.original_price}}</del><i></i></p>'
        + '    </a>'
        + '</div>'
        + '</div>'
        + '{{/each}}'
        +'</div>'
        +'</div>'
        +'</div>';

    var render = template.compile(source);
    var str = render(result.data);
    $("#intro1").html(str);
    $("#container2").swiper({
        slidesPerView: 'auto',
        offsetPxBefore: 10,
        offsetPxAfter: 10,
        calculateHeight: true
    })
}
function getDataResult11111(result) {
    var source = '<div class="index2-haibao" >'
        +' <div class="index2-wrapper">'
        + '<a onclick="jump_to_url(\'{{to_url}}\');"><img src="{{pic_url}}"/></a>'
        + '</div>'
        + '</div>';
    var render = template.compile(source);
    var str = render(result.data[0]);
    $("#ad-lg5").html(str);
}
function getDataResult7(result) {
    var source = '<h3><span>{{title}}</span>&nbsp;{{subtitle}}<i class="hott"><img src="{{tip_bg_url}}"/></i></h3>';
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-title2").html(str);
    var source = '<div class="index2-case">'
        +'<div class="swiper-container thumbs-cotnainer"id="container3">'
        +'    <div class="swiper-wrapper" style="width:540px; " >'
            +'{{each goods_list as value i}}'
        + '<div class="swiper-slide">'
        + '<div class="slide-content">'
        //+ '      <a onclick="jump_to_url(\'{{value.to_url}}\');">'
        + '      <a onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');">'
        + '          <img src="{{value.pic_url}}"/>'
        + '         <h3>{{value.name}}</h3>'
        + '         <p class="p1"></p>'
        + '         <br/>'
        + '         <p class="p2">&yen;{{value.price}}</p>'
        // + '         <p class="p2">&yen;{{value.price}}&nbsp;<del>&yen;{{value.original_price}}</del><i></i></p>'
        + '    </a>'
        + '</div>'
        + '</div>'
        + '{{/each}}'
        + '</div>'
        + '</div>'
        + '</div>';
    var render = template.compile(source);
    var str = render(result.data);
    $("#intro2").html(str);
    $("#container3").swiper({
        slidesPerView: 'auto',
        offsetPxBefore: 10,
        offsetPxAfter: 10,
        calculateHeight: true
    });
}

function getDataResult8(result) {
    var source = '<p><i class="line1"></i><img src="images/collect2.png"/>&nbsp;&nbsp;{{title}}<i class="line2"></i></p>'
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-title3").html(str);

    var source = '{{each goods_list as value i}}'
        + '<li> '
        + '<div class="pic">'
        //'<a onclick="jump_to_url(\'{{value.to_url}}\');">' +
        + '      <a onclick="jump_by_tpl_id(\'{{value.tpl_id}}\');" style="display:block;width:100%;">'
        +'<img src="{{value.pic_url}}"/></a></div>'
        + '<h3><a onclick="jump_to_url(\'{{value.to_url}}\');">{{value.name}}</a></h3>'
        + '<p class="p1"></p>'
        + '<p class="p2">&yen;{{value.price}}<span onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1);"></span></p>'
        + '<p class="p3"><a onclick="jump_to_category(\'{{value.cate_name}}\',\'{{value.category_id}}\');">{{value.cate_name}} <i></i></a></p>'
        + '</li>'
        + '{{/each}}';
    var render = template.compile(source);
    var str = render(result.data);
    $("#love_goodlist").html(str);
    var width = $(".pic a img").width();
    $(".pic a img").height(width);

}

function jump_to_category(cname,cid){
    location.href = 'gshow-hor.html?keyword='+cname+'&category_id='+cid;
}
