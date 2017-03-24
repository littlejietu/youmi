$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = getUrlParam("site_id");
    if(site_id!=null&&site_id!='')
        save_string_tolocal('site_id', site_id);
    
    $('.user_points span').bind('click',function(){
        $('.point_intro').show();
        $('.point_intro ul.point_rules').animate({marginLeft:"3.4rem"});
    });

    $('.point_intro').bind('click',function(){
        $('.point_intro').hide();
    });

    getList(0,1);

    
    

});


var swipeHandler;
var resultData;
function getList(type,page){
    sendPostData({type:type,page:page},ApiUrl+'m/integral',function (result) {
        if(result.code!='SUCCESS'){
            tipsAlert(result.msg);
            return;
        }

        if($('#show_acct_integral').html()=='')
            $('#show_acct_integral').html(result.data.info.acct_integral);
        
        for(var i = 0 ;i < result.data.list.rows.length;i++){
            result.data.list.rows[i].add_time = new Date(result.data.list.rows[i].add_time*1000).Format('yyyy-MM-dd hh:mm:ss');
        }
        resultData = result;
        var source = $('#integral-list-tpl').html();
        var render = template.compile(source);
        var str = render(resultData.data.list);
        $('#integral-list').html(str);
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
        var total = get_total_page(resultData.data.list.count,resultData.data.list.pagesize);
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
