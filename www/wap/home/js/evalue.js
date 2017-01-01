/**
 * Created by Administrator on 2016/3/15.
 */
var source ='{{each comment_list as value i}}'
    +'<div class="swiper-slide product4">'
    +'<p class="p2" style="border-bottom:1px solid #eee;padding-bottom:5px;">'
    +'{{if value.avatar}}<img src="{{value.avatar}}"/>{{else}}<img src="../images/girl.png"/>{{/if}}&nbsp;&nbsp;&nbsp;&nbsp;<label style="width: 100px;overflow: hidden;text-overflow: ellipsis;display: inline-block;">{{value.name}}</label><span>{{value.add_time}}</span>'
    +'</p>'
    +'<p class="p1">评分：'
    +"{{each value.stars as value}}"
    +'<img src="../images/xin.png"/>'
    +'{{/each}}'
    +'</p>'
    +'<p class="p3">{{value.content}}</p>'
    +'<div class="pic">'
    +'{{each value.comment_images as imgurl}}'
    +'<span><img class="image_class" src="{{imgurl}}"/></span>'
    +'{{/each}}'
    +'</div>'
    +'<p class="p4">{{value.sku}}</p>'
    +'</div>'
    +'{{/each}}';
var swipeHandler;
var resultData;
var currentType=0;
$(function() {
    //$(".re-top").click(function(){
    //    $('html,body').animate({scrollTop: '0px'}, 700);
    //});
    //$('body').css("overflow","hidden");

    sendPostData({goods_id:getUrlParam("goods_id"),page:1,pagesize:10,type:0}, ApiUrl + 'goods/comment_list', getResultData);

    $(".re-top").click(function () {
        swipeHandler.gotoTop();
    });

});
function getResultData(result) {
    resultData = result;
    if(result.code == 1){
        if(!result.data.comment_list){
            $('.swiper-wrapper').html('');
            return;
        }
    
        for(var i = 0 ;i < result.data.comment_list.length;i++){
            result.data.comment_list[i].stars = new Array(parseInt(result.data.comment_list[i].rating));
            result.data.comment_list[i].add_time=new Date(result.data.comment_list[i].add_time*1000).Format('yyyy-MM-dd hh:mm:ss');
        }
        $("#all_0").html("全部评价<br/>"+result.data.content.all_num);
        $("#positive_1").html("好评<br/>"+result.data.content.positive_num);
        $("#neutral_2").html("中评<br/>"+result.data.content.neutral_num);
        $("#negative_3").html("差评<br/>"+result.data.content.negative_num);
        $("#haspic_4").html("晒图<br/>"+result.data.content.haspic_num);
        var render = template.compile(source);
        var str = render(result.data);

        if(!swipeHandler){
            $('.swiper-wrapper').html(str);

            swipeHandler = new SwiperUtils({
                container:'.swiper-container',
                swpierHandler:swpierEvemt,
                collectswiper:'.swiper-wrapper',
                deep:300
            });

        }else{
            swipeHandler.setSwiperSlider(str);
        }
        var elements = document.querySelectorAll( '.image_class' );
        if(elements.length>0){
            Intense( elements );
        }

        //var total = get_total_page(resultData.data.content.all_num,resultData.data.content.pagesize);
        swipeHandler.setPage(parseInt(result.data.content.curpage),parseInt(resultData.data.content.page_total));
    }
}
function swpierEvemt(before) {
    var p = 1;
    if (!before) {
        p = parseInt(resultData.data.content.curpage) + 1;
    }
    sendPostData({
        goods_id: getUrlParam("goods_id"),
        page: p,
        pagesize: 10,
        type: currentType
    }, ApiUrl + 'goods/comment_list', getResultData);
}
function commentClick(event){
    if($(event.target).hasClass("color2")){
        return;
    }
    $(".evalue").find('a').removeClass("color2");
    $(event.target).addClass("color2");
    currentType = $(event.currentTarget).attr('id').split('_')[1];
    swipeHandler.setBefore(true);
    swpierEvemt(true);

}


