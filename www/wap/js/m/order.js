$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = getUrlParam("site_id");
    if(site_id!=null&&site_id!='')
        save_string_tolocal('site_id', site_id);
    
    var type=getUrlParam('type');
    if(!type){
        type = 0;
    }
    $('.user_order dd').click(function(){
        $(this).siblings('dd').removeClass('on');
        $(this).addClass('on');

        tp = $(this).attr('rel');
        if(swipeHandler){
            swipeHandler.setBefore(true);
        }
        getOrderList(tp,1);
    });

    getOrderList(0,1);

    
    

});


var swipeHandler;
var resultData;
function getOrderList(type,page){
    sendPostData({type:type,page:page},ApiUrl+'m/order',function (result) {
        if(result.code!='SUCCESS'){
            tipsAlert(result.msg);
            return;
        }
        
        for(var i = 0 ;i < result.data.rows.length;i++){
            result.data.rows[i].createtime = new Date(result.data.rows[i].createtime*1000).Format('yyyy-MM-dd hh:mm:ss');
        }
        resultData = result;
        var source = $('#order-list-tpl').html();
        var render = template.compile(source);
        var str = render(resultData.data);
        $('#order-list').html(str);
/*
        if(!swipeHandler){
            $('#order-list').html(str);
            swipeHandler = new SwiperUtils({
                container:'.swiper-container',
                swpierHandler:swpierEvemt,
                collectswiper:'#order-list',
                deep:200
            });
            $('.swiper-container').css('height',$(window).height() - 102);

        }else{
            swipeHandler.setSwiperSlider(str);
        }
*/
        if(resultData.data.count==0){
            $(".empty").show();
            if(tp == 0){
                $(".empty span").text("还没有您的订单");
            }else if(tp ==1){
                $(".empty span").text("还没有您待付款的订单");
            }else if(tp ==4){
                $(".empty span").text("还没有您完成的订单");
            }
        }
        else{
            $(".empty").hide();
        }
        var total = get_total_page(resultData.data.count,resultData.data.pagesize);
        //swipeHandler.setPage(parseInt(resultData.data.page),total)
        
    });
}


var tp;
function swpierEvemt(before){
    var pg = 1;
    if(!before){
        pg = parseInt(resultData.data.page)+1;

    }

    getOrderList(0,pg);
}

function payNow(order_id){
    location.href = '../order/pay.html?order_ids='+order_id;
}

function showOrdeDetail(order_id){
    location.href= 'order_detail.html?order_id='+order_id;
}

function del_order(order_id){
    current_order = order_id;
    show_tips_content2({msg:'您确定要删除这个订单吗？',okbtn:'确定',canbtn:'我在想想',okfun:sureHandler})
}
function sureHandler(){
    sendPostData({order_id:current_order},ApiUrl+'m/order/del',function(result){
        if(result.code == 1){
            swpierEvemt(true);
        }
        $('.cancel-mask').hide();
    });

}
