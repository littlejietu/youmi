var postObj;
var swipeHandler;
var category_id;
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

function onfromsubmit(){
    postObj.keyword =$("#keyword_input").val(keyword);
    sendPostData(postObj, ApiUrl + 'search/result', getDataResult);
    return false;

}
$(function () {
    // $("#icon").click();
    var token = get_user_token();
    if(!token){
        location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
        return;
    }
    var sortstyle = 1;
    var keyword = decodeURI(getUrlParam('keyword'));
    var search_scene = getUrlParam('search_scene');
    category_id = getUrlParam('category_id');
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
    //var resultData;
    if(!category_id){
        if (keyword) {
            postObj = {page: 1, pagesize: 10, sort_key: 0, sort_by: 0, keyword: keyword, search_scene: search_scene};
            if(get_string_fromlocal('shop_id')){
                postObj.shop_id = get_string_fromlocal('shop_id');
            }else{
                postObj.shop_id =1;
            }

        }
    }else{

        postObj = {keyword: keyword,page: 1, pagesize: 10, sort_key: 0, sort_by: 0,category_id:category_id,shop_id:get_string_fromlocal('shop_id')};
        if(get_string_fromlocal('shop_id')){
            postObj.shop_id = get_string_fromlocal('shop_id');
        }else{
            postObj.shop_id =1;
        }

    }
    sendPostData(postObj, ApiUrl + 'search/result', getDataResult);
    $("#keyword_input").val(keyword);
    $('#keyword_input').click(function(){
        if(category_id){
            location.href = 'search.html'
        }
    });


    $("#keyword_input").keydown(function(event){
        if(event.keyCode == 13){
            var txt = $.trim($("#keyword_input").val());
            if(txt){
                //postObj = {page: 1, pagesize: 10, sort_key: 0, sort_by: 0, keyword: txt, search_scene: 0};
                postObj.page = 1;
                postObj.search_scene = 0;
                postObj.keyword = keyword;
                refreshData(postObj,true);
            }
        }
    });
    setCartNum();




    function getDataResult(result) {
        //resultData = result;
        sortstyle = 1;
        var source = '{{each goods as value i}}'
            + '{{if i%2 == 0}}'
            + '<div class="swiper-slide">'
            + '{{/if}}'
            + '<div class="product-list" >'
            + '<figure>'
            + '<div class="pic"><a onclick="jump_by_tpl_id({{value.tpl_id}});"><img src="{{value.pic_url}}"/></a></div>'
            + '<figcaption>'
            + '<h3><a onclick="jump_by_tpl_id({{value.tpl_id}});">{{value.name}}</a></h3>'
            
            + '<p><span class="color2 font2">&yen;{{value.price}}</span></p>'
            + '<p class="haoping"><span>评论{{value.comments_num}}条</span><span class="pic1" onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1);"></span><span class="num"><i class="color2">{{value.fav_rate}}</i>好评&nbsp;</span></p>'
            + '</figcaption>'
            + '</figure>'
            + '</div>'
            + '{{if i%2 == 1}}'
            + '</div>'
            + '{{else if i%2==0 && i == goods.length-1}}'
            + '</div>'
            + '{{/if}}'
            + '{{/each}}';
        var render = template.compile(source);
        var str = render(result.data);
        if (!swipeHandler) {
            $('#product_list').html(str);
            swipeHandler = new SwiperUtils({
                container: '.swiper-container',
                swpierHandler: swpierEvemt,
                collectswiper: '#product_list',
                deep: 200
            });
            $('.swiper-container').css('height', $(window).height() - 88);

        } else {
            //swipeHandler.setBefore( parseInt(result.data.curpage) == 1);
            swipeHandler.setSwiperSlider(str);
        }
        if(result.data.total > 0){
            $(".empty").hide();
        }else{
            $(".empty").show();
        }


        var total = parseInt(result.data.totalpage);
        swipeHandler.setPage(parseInt(result.data.curpage), total);
        var width=$(".pic a").width();
        $(".pic a img").height(width);
    }

    function getDataResult2(result) {

        sortstyle = 2
        var source = '{{each goods as value i}}'
            + '<div class="swiper-slide">'
            + '<div class="tehui-list">'
            + '    <div class="pic"><a onclick="jump_by_tpl_id({{value.tpl_id}});"><img src="{{value.pic_url}}"/></a></div>'
            + '    <div class="text">'
            + '        <div>'
            + '            <h3><a onclick="jump_by_tpl_id({{value.tpl_id}});">{{value.name}}</a></h3>'
           
            + '            <p>'
            + '                <span class="color2 font2">&yen;{{value.price}}</span>'
            + '            </p>'
            + '            <p class="haoping"><span>评论{{value.comments_num}}条</span><span class="pic1" onclick="add_goods_to_cart(\'{{value.goods_id}}\',0,1);"></span><span class="num"><i class="color2">{{value.fav_rate}}</i>好评&nbsp;</span></p>'
            + '        </div>'
            + '    </div>'
            + '    <div class="clear"></div>'
            + '</div>'
            + '</div>'
            + '{{/each}}'
        var render = template.compile(source);
        var str = render(result.data);

        if(result.data.total > 0){
            $(".empty").hide();
        }else{
            $(".empty").show();
        }
        swipeHandler.setSwiperSlider(str);
        var total = parseInt(result.data.totalpage);
        swipeHandler.setPage(result.data.curpage, total);

        var width=$(".pic a").width();
        $(".pic a img").height(width);
    }

    //var data = getDataByType(1,result)
    $("#icon").click(function () {
        postObj.page = 1;
        swipeHandler.setBefore(true);
        if ($(this).hasClass("icon-hor")) {
            //getDataResult2(resultData);
            sendPostData(postObj, ApiUrl + 'search/result', getDataResult2);
            $(this).removeClass("icon-hor").addClass("icon-ver");


        }
        else {
            $(this).removeClass("icon-ver").addClass("icon-hor");
            sendPostData(postObj, ApiUrl + 'search/result', getDataResult);
            //getDataResult(resultData);
            // var render = template.compile(source1);
            // str = render(result.data);
            // $("#product_list").html(str);
        }

    });
    // str = render(result.data);
    // $("#product_list").html(str);


    // //var data = getDataByType(1,result);
    // var render = template.compile(source);
    // str = render(result.data);
    // $("#product_list1").html(str);


    $(".a3").click(function () {

        if ($(this).children("i").hasClass("arow-down")) {
            $(this).children("i").removeClass("arow-down").addClass("arow-up");
            //postObj = {
            //    page: 1,
            //    pagesize: 10,
            //    sort_by: 3,
            //    sort_key: 0,
            //    keyword: keyword,
            //    search_scene: search_scene
            //};
            postObj.page = 1;
            postObj.sort_key  =1;
            postObj.sort_by= 3;
        }
        else {
            $(this).children("i").removeClass("arow-up").addClass("arow-down");
            postObj.page = 1;
            postObj.sort_key  = 0;
            postObj.sort_by= 3;
        }
        refreshData(postObj,true);
    });

    function swpierEvemt(before){
        var p = 1;
        if(!before){
            p = parseInt(postObj.page)+1;
        }
        postObj.page = p;
        refreshData(postObj,before);
    }


    $("#nav table tr td ").click(function(){
        $(this).children("a").addClass("color2").parent().siblings().children("a").removeClass("color2");
    });

    $('#comp_goods').click(function(){

        postObj.page = 1;
        postObj.sort_by = 0;
        postObj.sort_key = 1;
        refreshData(postObj,true);
    });

    $('#sales_volume').click(function(){


        //postObj = {

        //    page: 1,
        //    pagesize: 10,
        //    sort_by: 1,
        //    sort_key: 1,
        //    keyword: keyword,
        //    search_scene: search_scene
        //};
        postObj.page = 1;
        postObj.sort_by = 1;
        postObj.sort_key = 1;
        refreshData(postObj,true);
    });
    $('#new_goods').click(function(){

        //postObj = {
        //    page: 1,
        //    pagesize: 10,
        //    sort_by: 2,
        //    sort_key: 1,
        //    keyword: keyword,
        //    search_scene: search_scene
        //};
        postObj.page = 1;
        postObj.sort_by = 2;
        postObj.sort_key = 1;
        refreshData(postObj,true);

    });

    function refreshData(obj,boo){
        if(boo){
            swipeHandler.setBefore(true);
        }
        var sortFun;
        if (sortstyle == 1) {
            sortFun = getDataResult;
        } else {
            sortFun = getDataResult2;

        }
        sendPostData(obj, ApiUrl + 'search/result', sortFun);
    }
    FastClick.attach(document.body);
});

