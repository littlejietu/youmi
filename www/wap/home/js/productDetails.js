var shop_id;

var details =   {};
details.service= '<div class="details3-list">'
    +'<h3>服务承诺</h3>'
    +'<p>平台所售产品均为厂商正品，如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理。我们将争取以更具竞争力的价格、更优质的服务来满足您的最大的需求。</p>'
    +'</div>'
    +'<div class="details3-list">'
    +' <h3>收货须知</h3>'
    +' <p>平台所售产品均为厂商正品，如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理。我们将争取以更具竞争力的价格、更优质的服务来满足您的最大的需求。平台所售产品均为厂商正品，如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理。我们将争取以更具竞争力的价格、更优质的服务来满足您的最大的需求。平台所售产品均为厂商正品，如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理。我们将争取以更具竞争力的价格、更优质的服务来满足您的最大的需求。</p>'
    +'</div>'
    +'<div class="details3-list">'
    +'<h3>温馨提示</h3>'
    +'<p>平台所售产品均为厂商正品，如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理。我们将争取以更具竞争力的价格、更优质的服务来满足您的最大的需求。</p>'
    +'</div>';
$(function () {
    //var str='';
    //getDataResult(result);
    var token = get_user_token();
    if(!token){
        location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
        return;
    }
    var ww = $(window).height();

    $('#scroll').height((ww - 49 -91));

    //$("#zhezhao").css("left", width + 'px');
    //$("#zhezhao").hide();
    $("#zhezhao").hide();
    $("#zhezhao").addClass('animated')
    var obj = {};
    shop_id = getUrlParam('shop_id');
    if(getUrlParam('id')){
        obj.id = getUrlParam('id');
    }else if(getUrlParam('tpl_id')){
        obj.tpl_id = getUrlParam('tpl_id');
    }
    obj.shop_id = shop_id;

    sendPostData(obj, ApiUrl + 'goods/detail', getDataResult);
    $('.icon-bottom').click(function () {

    });





   $("#share").click(function(){
        $(".invite_mask").show();
        $("body").css("overflow","hidden");
    });
    $(".invite_mask").click(function(){
        $(".invite_mask").hide();
        $("body").css("overflow","auto");
    });
    FastClick.attach(document.body);
});
var chooseStyle = {};
var goodsInfo;
var chooseNum = 1;

function setCartNum(){
    $('#cart_num i').remove();
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
        $('#cart_num').append('<i class="cart-num">' + num + '</i>');
    }
}
function loginfun(){
    location.href = 'http://data.zooernet.com/api/wxauth/go?url='+location.href;
    return;
}

function collect_goods() {

    if(!get_user_token()){
        show_tips_content2({msg:'请先登陆！',okbtn:'取消',canbtn:'确定',canfun:loginfun});
        return ;
    }

    if ($("#collect").hasClass("collect")) {

        $("#collect").removeClass("collect");
        // alert('取消收藏');
        sendPostData({ids: goodsInfo.id,}, ApiUrl + 'm/favorite/del', function (result) {
            if (result.code == 1) {
                $("#collect img").attr("src", "../images/collect.png");
                // tipsAlert('取消收藏成功！');
            } else {
                tipsAlert('取消失败！');
            }
        });
    }
    else {
        // $("#collect img").attr("src","../images/collect-red.png");
        $("#collect").addClass("collect");

        sendPostData({item_id: goodsInfo.id, item_type: 1}, ApiUrl + 'm/favorite/add', function (result) {
            if (result.code == 1) {
                $("#collect img").attr("src", "../images/collect-red.png");
                // tipsAlert('添加收藏成功！');
            } else {
                tipsAlert('添加失败！');
            }
        });
    }
    // }
}
function collect_coupon(cid) {
    // if ($(event.currentTarget).hasClass('coupon_class1')) {
    //     return;
    // }
    if(!get_user_token()){
        show_tips_content2({msg:'请先登陆！',okbtn:'取消',canbtn:'确定',canfun:loginfun});
        return ;
    }

    var cou = $(event.currentTarget);
    sendPostData({coupon_id: cid}, ApiUrl + 'm/coupon/collect_coupon', function (result) {
        if (result.code == 1) {
            // cou.addClass('coupon_class1');
            tipsAlert('优惠券领取成功');
        } else {
            tipsAlert(result.msg);
        }
        // cou.removeClass('coupon_class2').addClass('coupon_class1');
    });

}


function HTMLDecode(text)
{
    var temp = document.createElement('div');
    temp.innerHTML = text;
    var output = temp.innerText || temp.textContent;
    temp = null;
    return output;
}

function getDataResult(result) {
    if (result.code != 1) {
        tipsAlert(result.msg);
        return;
    }
    initWx([ 'onMenuShareTimeline','onMenuShareAppMessage','getLocation'],shareobj,location.href);
    goodsInfo = result.data.info;
    function shareobj(res){
        if(res == 1){
            shareObj.link = location.href;
            getUserInfo(function(user){
                shareObj.link = SiteUrl+'/api/wxauth/go?url='+WapSiteUrl+'/home/productdetails/index.html?id='+goodsInfo.id+'&invite_id='+user.user_id;
                if(goodsInfo.pic_list && goodsInfo.pic_list[0]){
                    shareObj.imgUrl = goodsInfo.pic_list[0].pic;
                }


                wxShareTimeLine(shareObj);
                wxShareToFriend(shareObj);
            });
            //locFail();
        }else{
            //locFail();
        }
        getCurrentAddr();
    }
    sendPostData({id: goodsInfo.id}, ApiUrl + 'goods/intro', function (result) {
        var content = result.data.content;
        if(content){
            var first = content.substring(0,1);
            if(first=='[' || first=='{'){
                var source = '{{each content as value i}}'
                    + '{{if value.type == "image"}}'
                    + '<img src="{{value.value}}"/>'
                    + '{{else value.type =="text"}}'
                    + '<p>{{value.value}}</p>'
                    + '{{/if}}'
                    + '{{/each}}';
                var render = template.compile(source);
                if(typeof result.data.content == 'string'){
                    result.data.content = JSON.parse(result.data.content);
                }
                var str = render(result.data);
                details.content= str;
                $(".details0").html(str);
            }else{
                content = HTMLDecode(content)
                details.content= content;
                $(".details0").html( content );
            }

        }



    });
    if (result.data.other.is_fav) {
        // return ;
        $("#collect").addClass("collect");
        $("#collect img").attr("src", "../images/collect-red.png");

    } else {
        $("#collect").removeClass("collect");
        $("#collect img").attr("src", "../images/collect.png");
    }

    var source = '{{each pic_list as value i}}'
        + '<li><a href="#"><img src="{{value.pic}}"></a></li>'
        + '{{/each}}'

    var render = template.compile(source);
    var str = render(result.data.info);
    $("#swiper-wrapper").html(str);
    TouchSlide({
    		slideCell:"#slideBox",
    		titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
    		mainCell:".bd ul",
    		effect:"leftLoop",
    		autoPage:true,//自动分页
    		autoPlay:true //自动播放
    	});

    var source = '<p class="p0">{{info.title}}</p>'
        + '<p class="p1">{{info.point}}</p>'
        + '<div class="p2" >{{if info.activity}}<span>{{info.activity}}</span>{{/if}} &yen;<a style="color: #ff3d23" id="page_price">{{info.price}}</a>&nbsp;'
        +'{{if info.market_price>info.price}}'
        +'<del id="page_market_price">&yen;{{info.market_price}}</del>'
        +'{{/if}}'
        +'</div>'
        + '<p class="p3">'
        + '  <span class="span1" style="float:left;width:10%;">领劵</span>'
        + '<span style="float:left;width:90%;">'
        + '{{each coupon as value i}}'
        + '	<span class="span2 coupon_class{{i}}" onclick="collect_coupon({{value.id}});" >{{value.title}}</span>'
        + '{{/each}}'
        + '</span>'
        + '<div class="clear"></div>'
        + '</p>'
    var render = template.compile(source);
    str = render(result.data);
    $("#product1").html(str);
    var source = '<p>'
        + '<label style="float:left;line-height:30px;">送至&nbsp;&nbsp;&nbsp;&nbsp;</label>'
        + '<span style="float:left;width:80%;line-height:30px;"><span id="current_addr"></span>&nbsp;<img onclick="getCurrentAddr();" src="../images/location.png"/><br/><i class="color2">现货</i>，<i style="color:#999;">市区18:00前完成订单，预计1至两天送达</i></span>'
        + '</p>'
        + '<div class="clear"></div>'
        + '<p>服务&nbsp;&nbsp;&nbsp;&nbsp;<span>{{other.service}}</span></p>'
        + '<div class="clear"></div>'
        + '<p>提示&nbsp;&nbsp;&nbsp;&nbsp;<span>{{other.notice}}</span></p>';
    var render = template.compile(source);
    str = render(result.data);
    $("#other").html(str);
    var ob = getSkuById(result.data.info.sku_id);
    if(ob){
        result.data.info.price = ob.price;
        result.data.info.market_price = ob.marketprice
    }

    var source = '<div class="left">'
        + '<a><img src="{{pic_path}}"/></a>'
        + '</div>'
        + '<div class="right">'
        + '	<p class="p2"> '
        + '	<span class="span2" ><i id="sku_price">&nbsp;&yen;{{price}}</i>&nbsp;</span>'
        +'{{if market_price>0}}'
        + '	<del id="sku_market_price">&yen;{{market_price}}</del>'
        +'{{/if}}'
        + '	</p>'
        + '	<p class="p1">商品编号：{{id}}</p>'
        + '</div>'
        + '<div class="close3"><img src="../images/close.png"/></div>'
    var render = template.compile(source);
    str = render(result.data.info);
    $("#inner2-head").html(str);

    var skustr = getSkuStrById(result.data.info.sku_id);
    var skuarr ;
    if (result.data.info.spec_list) {
        if(skustr){
            skuarr = skustr.split('_');
            for(var key in skuarr){
                skulabel:
                for (var i = 0; i < result.data.info.spec_list.length; i++) {
                    var ob = result.data.info.spec_list[i].valList;
                    for(var j in ob){
                        if(ob[j].val_id == skuarr[key]){
                            chooseStyle[result.data.info.spec_list[i].id] = ob[j].val_id;
                            break skulabel;
                        }
                    }
                }
            }


        }else{
            skuarr = [];
            for (var i = 0; i < result.data.info.spec_list.length; i++) {

                var ob = result.data.info.spec_list[i];
                var vv = ob.valList[0];
                if(vv){
                    skuarr.push(ob.id);
                    chooseStyle[ob.id] = vv.val_id;
                }

            }
        }
        result.data.info.skuIdArr= skuarr;
        chooseStyle.num = 1;
    }
    var source = '{{each spec_list as value i}}'
        + '<div class="list">'
        + '	<label>{{value.name}}</label>'
        + '	<div class="list-details" id="spec_{{value.id}}">'
        + '{{each value.valList as vv ii}}'
        + '{{if skuIdArr[i]==vv.val_id}}'
        + '		<span class="borColor" id="val_{{vv.val_id}}">{{vv.val}}</span>'
        + '{{else }}'
        + '		<span id="val_{{vv.val_id}}">{{vv.val}}</span>'
        + '{{/if}}'
        + '{{/each}}'
        + '	</div>'
        + '	<div class="clear"></div>'
        + '</div>'
        + '{{/each}}'

    var render = template.compile(source);
    str = render(result.data.info);
    $("#spec_list").html(str);


    goodsStyleChange();

    //var spec_list = result.data.info.spec_list;
    //for(var i = 0 ;i < spec_list.length;i++ ){
    //
    //}
//resultData.data.rows[i].createtime = new Date(resultData.data.rows[i].createtime*1000).Format('yyyy-MM-dd hh:mm:ss')
    var a_comment=result.data.a_comment;
    if(a_comment){

        a_comment.addtime=new Date(a_comment.addtime*1000).Format('yyyy-MM-dd hh:mm:ss');
        var source = '<div class="product4" >'
            +'<h3><a href="evalue.html?goods_id={{info.id}}&type=0">{{comment.be_comment}}人评价<span><i class="color2">{{comment.be_comment_good}}</i>好评<i class="right-arow"></i></a></span></h3>'
            + '<p class="p1">评分：'
            + '{{if a_comment.score_level==1}}'
            + '<img src="../images/xin.png"/>'
            + '{{else if a_comment.score_level==2}}'
            + '<img src="../images/xin.png"/><img src="../images/xin.png"/>'
            + '{{else if a_comment.score_level==3}}'
            + '<img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/>'
            + '{{else if a_comment.score_level==4}}'
            + '<img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/>'
            + '{{else if a_comment.score_level==5}}'
            + '<img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/><img src="../images/xin.png"/>'
            + '{{/if}}'
            + '</p>'
            + '<p class="p2">{{if a_comment.logo}}<img src="{{a_comment.logo}}"/>{{else}}<img src="../images/girl.png"/>{{/if}}&nbsp;&nbsp;{{a_comment.name}}<span>{{a_comment.addtime}}</span></p>'
            + '<p class="p3">{{a_comment.comment}}</p>'
            + '<div class="pic">'
            + '{{each a_comment.pic_path as value i}}'
            + '	<span><img src="{{value.thumb}}"/></span>'
            + '{{/each}}'
            + '</div>'
            +'</div>'

        var render = template.compile(source);
        if (result.data.a_comment) {
            str = render(result.data);
            $("#comment").html(str);
        } else {
            $("#comment").html('');
        }

        var width=$(".p2 img").width();
        $(".p2 img").height(width);
    }
    
    var attrList = result.data.info.attr;
    if (attrList) {
        var source = '<div class="details3-list">'
            +'{{each attr as value i}}'
            + '<p>{{value.name}}：{{value.valList}}</p>'
            + '{{/each}}'
            +'</div>'
        var render = template.compile(source);


        for (var i = 0; i < attrList.length; i++) {
            var vallist = attrList[i].valList;
            var sss = [];
            for (var key in vallist) {
                sss.push(vallist[key]);
            }
            attrList[i].valList = sss.join(',');
        }
        str = render(result.data.info);
        details.attr = str;
    }
    else{
        details.attr='';
    }
    startEvent();
    var obj = {
        id: result.data.info.id,
        title: result.data.info.title,
        cost_price: result.data.info.cost_price,
        market_price: result.data.info.market_price,
        price: result.data.info.price,
        shop_id: result.data.info.shop_id,
        pic_path: result.data.info.pic_path,


    };
    if(getUrlParam('tpl_id')){
        obj.to_url = 'zooer://productdetail?tpl_id=' + getUrlParam('tpl_id');
    }else{
        obj.to_url = 'zooer://productdetail?id=' + result.data.info.id;
    }
    saveToLocal(obj);
    setCartNum();
}

function goodsStyleChange(){
    var str = '';
    var num = '';
    for(var key in chooseStyle){
        if(key == 'num'){
            num = chooseStyle[key]+'件'
        }else{
            for(var k in goodsInfo.spec_list){
                for(var v in goodsInfo.spec_list[k].valList){
                    var ob = goodsInfo.spec_list[k].valList[v];
                    if(ob.val_id == chooseStyle[key]){
                        str+=ob.val+' ';
                    }
                }
            }
        }
    }

    if(str){
        $('#choose_style').show();
        $('#style_text').html(str+num);
    }else{
        $('#choose_style').hide();
    }

    var sku_id = getSkuid();
    if(!goodsInfo.sku){
        return;
    }
    var ob = getSkuById(sku_id);
    if(ob){
        $('#page_price').html(ob.price);
        $('#page_market_price').html('&yen;'+ob.marketprice);
        $('#sku_price').html('&nbsp;&yen;'+ob.price+'&nbsp;');
        $('#sku_market_price').html('&yen;'+ob.marketprice);
    }



}

function startEvent() {
    $(document).scrollTop(0);//重置页面滚动条高度
    // var top=$("#product1").offset().top;
    // alert(top);
    var ds = false;//文档滚动条标志,确保scroll内逻辑只执行一次
    $(document).scroll(function () {
        var top = $("#photo").offset().top;
        if ($(document).scrollTop() >= top ) {
            try {
                //$(document).scrollTop(top);
                //$("body").animate({scrollTop:top+'px'},500);

                if($("#scroll")[0].scrollHeight >$("#scroll")[0].offsetHeight ){
                    $("#scroll").css({"overflow": "scroll"});
                    $("body").css('overflow','hidden')
                }

                ds = true;
                $(".icon-left").hide();
                $(".icon-right").hide();
            } catch (e) {
            }
        }else{
            $(".icon-left").show();
            $(".icon-right").show();
        }
    });
    $("#scroll").scroll(function () {
        if ($("#scroll").scrollTop() == 0) {
            $("#scroll").css({"overflow": "visible"});
            $("body").eq(0).css("overflow", "visible");
            ds = false;
            $(".icon-left").show();
            $(".icon-right").show();
        }
    });

    $(".re-top").click(function () {
        if($('body').scrollTop() == 0){
            return ;
        }
        // $('html,body').animate({scrollTop: '0px'}, 700);
        window.scrollTo(0,0);
        $("body").eq(0).css("overflow", "visible");
        $(".icon-left").show();
        $(".icon-right").show();
    });

    $(".arow2").click(function () {
        //var width = $(window).width();
        //$("#zhezhao").css("left", width + 'px');
        //$("#zhezhao").show();
        //$("#zhezhao").animate({left: "0px"}, 500);
        if ($(this).attr('id') == 'buy_now') {
            $('.inner2-footer').find('p').html('立即购买');
        } else {
            $('.inner2-footer').find('p').html('加入购物车');
        }


        $("#zhezhao").show();
        $("#zhezhao").removeClass('slideOutRight');
        $("#zhezhao").addClass('slideInRight');
        $(".icon-right").css("z-index", "20");
        $("body").css("overflow","hidden");
    });
    $(".close3").click(function () {
        //var width = $(window).width();
        //$("#zhezhao").animate({left: width + 'px'}, 500, function () {
        //    $("#zhezhao").hide();
        //});
        $("#zhezhao").removeClass('slideInRight');
        $("#zhezhao").addClass('slideOutRight');
        $("body").css("overflow","auto");
    });
    $('.inner2-footer').click(function () {
        if(!get_user_token()){
            show_tips_content2({msg:'请先登陆！',okbtn:'取消',canbtn:'确定',canfun:loginfun});
            return ;
        }

        var sku_id = getSkuid();

        if ($(this).find('p').html() == "加入购物车") {
            add_goods_to_cart(goodsInfo.id,sku_id, chooseNum, function (result) {
                if (result.code == 1) {
                    $(".close3").click();
                } else {
                    tipsAlert(result.msg);
                }
            });
        }else{
            var url = "../ordersubmit/confirm.html";
            url += '?cart_id=' + goodsInfo.id + ',' + sku_id + ',' + chooseNum + '&ifcart=0&shop_id=' + shop_id;
            location.href = url;
        }


    });

    $(".list-details span").click(function () {
        $(this).addClass("borColor").siblings().removeClass("borColor");
        chooseStyle[$(this).parent().attr('id').split('_')[1]] = $(this).attr('id').split('_')[1];
        goodsStyleChange();

    });
    // $(".list-details span").click();
    // $("#coupon_1").click(function(){
    // 	$(this).css({"color":"#ddd","border":"1px solid #ddd"})
    // });
    // $("#coupon_2").click(function(){
    // 	$(this).css({"color":"#ddd","border":"1px solid #ddd"})
    // });

    $(".div1 ul li").click(function () {
        $(this).children("a").addClass("color2").parent().siblings().children("a").removeClass("color2");
        var index = $(this).index();
        if (index == 0) {
            if(details.content){
                $('.details0').html(details.content);
            }else{
                $('.details0').html('');
            }

        }else if (index == 1) {
            if(details.attr){
                $('.details0').html(details.attr);
            }else{
                $('.details0').html('');
            }
        } else {
            if(details.service){
                $('.details0').html(details.service);
            }else{
                $('.details0').html('');
            }
        }
        $("#scroll").scrollTop(0);
        var top = $("#photo").offset().top;
        //$("body").eq(0).css("overflow", "hidden");
        $("body").animate({scrollTop:top+'px'},500);
        $(".icon-left").hide();
        $(".icon-right").hide();
    });
    // $("#details0").remove();
}

function getSkuStrById(id){
    if (goodsInfo && goodsInfo.sku) {
        for(var key in goodsInfo.sku){
            var ob = goodsInfo.sku[key];
            if(ob.id == id){
                return key;
            }
        }
    }
    return '';
}

function getSkuById(id){
    if (goodsInfo && goodsInfo.sku) {
        for(var key in goodsInfo.sku){
            var ob = goodsInfo.sku[key];
            if(ob.id == id){
                return ob;
            }
        }
    }
    return null;
}

function getSkuid(){
    var arr = [];
    for (var key in chooseStyle) {
        if(key == 'num'){
            continue;
        }
        arr.push(key);
    }
    arr.sort();
    var arr1 = [];
    for (var i = 0; i < arr.length; i++) {
        arr1.push(chooseStyle[arr[i]]);
    }


    var ss = arr1.join("_");
    var sku_id  = 0;
    if (goodsInfo && goodsInfo.sku) {
        if(!goodsInfo.sku[ss]){
            sku_id = 0
        }else{
            sku_id = goodsInfo.sku[ss].id;
        }
    }
    return sku_id;
}

function plus(index) {
    var num = document.getElementById("num" + index).innerHTML;
    if (num > 0) {
        num++;
        document.getElementById("num" + index).innerHTML = num;
        chooseStyle.num = num;
        chooseNum = num;
    }
    goodsStyleChange();

}
function minus(index) {
    var num = document.getElementById("num" + index).innerHTML;
    if (num > 1) {
        num--;
        document.getElementById("num" + index).innerHTML = num;
        chooseStyle.num = num;
        chooseNum = num;
    }
    goodsStyleChange();

}

function saveToLocal(obj) {
    // var data = get_json_fromlocal(get_my_open_id());
    // if (!data) {
    //     data = {};

    // } else {
    //     trackList = data.track;
    // }
    // if (!data.track) {
    //     data.track = [];
    // }

    var trackData = get_user_data_from_local('track');
    if(!trackData){
        trackData=[];
    }
    else{
        trackList=trackData;
    }
    //var now = new Date(new Date().getTime() - 24*3600*1000*5) ;
    var now = new Date();
    var bool = false;
    for (var i = 0; i < trackData.length; i++) {
        var ob = trackData[i];

        var old = new Date(ob.time);
        if (now.getMonth() == old.getMonth() && now.getDate() == old.getDate()) {
            for (var j = 0; j < ob.list.length; j++) {
                if (ob.list[j].id == obj.id) {
                    return;
                }
            }
            if (ob.list.unshift(obj) > 10) {
                ob.list.pop();
            }
            bool = true;
            break;
        }
    }
    if (!bool) {
        var oo = {};
        oo.time = now.getTime();
        oo.dayName = '今天';
        oo.list = [];
        oo.list.unshift(obj);
        if (trackData.unshift(oo) > 5) {
            trackData.pop();
        }
    }


    // save_json_tolocal(get_my_open_id(), data);
    save_user_data_to_local('track',trackData);

}

function gotoCart(){
    if(!get_user_token()){
        show_tips_content2({msg:'请先登陆！',okbtn:'取消',canbtn:'确定',canfun:loginfun});
        return ;
    }
    location.href="../cart/cart3.html"
}

function getCurrentAddr(){
    wxGetLocation({
        success:function(res){
            //tipsAlert(JSON.stringify(res))
            var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            //codeLatLng(latitude,longitude);
            var latLng = new qq.maps.LatLng(latitude, longitude);
            //调用获取位置方法
            var geocoder = new qq.maps.Geocoder({
                complete:function(result){
                    $('#current_addr').html(result.detail.addressComponents.province+' '+result.detail.addressComponents.city+' '+result.detail.addressComponents.district);
                    //$('#detail_address').val(result.detail.addressComponents.street+' '+result.detail.addressComponents.town+' '+result.detail.addressComponents.streetNumber);
                },
                error:function(){
                    tipsAlert("出错了，请输入正确的经纬度！！！");
                }
            });
            geocoder.getAddress(latLng);

        },
        cancel:function(res){

        }
    });
}