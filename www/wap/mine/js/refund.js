$(function () {
    getTokenFromUrl();
    sendPostData({page: 1, pagesize: 10}, ApiUrl + 'm/order/refundslist', getDataResult);
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
});
var swipeHandler;
var resultData;
function getDataResult(result) {
    if (result.code == 1) {
        resultData = result;
        if(result.data.count==0){
            $(".empty").show();
        }
        else{
            $(".empty").hide();
        }
        for (var i = 0; i < result.data.rows.length; i++) {
            result.data.rows[i].statusStr = getStatusStr(result.data.rows[i].status)
        }
        var source = '{{each rows as value i}}'
            + '<div class="swiper-slide">'
            + '<div class="order-1">'
            + '	<div class="up">'
            + '		<div class="left"><img src="{{value.pic_path}}"/></div>'
            + '		<div class="right">'
            + '			<h3>{{value.title}}</h3>'
                +'			<p><span>X{{value.num}}</span></p>'
            + '		</div>'
            + '		<div class="clear"></div>'
            + '	</div>'
            + '	<div class="down">'
            + '		<p class="p1">共1件商品<span>总价：<i>&yen;{{value.num*value.price}}&nbsp;</i></span></p>'
            + '		<p class="p2"><a onclick="gotoApplyDetail({{value.order_goods_id}})"><span class="span1">查看详情</span></a><span class="span3">{{value.statusStr}}</span></p>'
            + '	</div>'
            + '</div>'
            + '</div>'
            + '{{/each}}'
        var render = template.compile(source);
        var str = render(result.data);
        if (!swipeHandler) {
            $('#refund').html(str);
            swipeHandler = new SwiperUtils({
                container: '.swiper-container',
                swpierHandler: swpierEvemt,
                collectswiper: '#refund',
                deep: 200
            });
            $('.swiper-container').css('height', $(window).height() - 58);

        } else {
            swipeHandler.setSwiperSlider(str);
        }
        var total = get_total_page(resultData.data.count, resultData.data.pagesize);
        swipeHandler.setPage(parseInt(resultData.data.page), total);

        var width=$(".left").width();
        $(".left img").height(width);
    }

}

function gotoApplyDetail(order_goods_id){
    var url = 'apply2.html?order_goods_id='+order_goods_id;
    if(deviceInfo){
        url = url+'&token='+urlToken;
    }
    location.href = url;
}

function getStatusStr(status) {
    if (status == 1) {
        return '完成';
    } else if (status == 2) {
        return '审核中';
    } else if (status == 3) {
        return '同意退款';
    } else if (status == 4) {
        return '等待商家确认';
    }
    else if (status == 5) {
        return '已取消';
    }
    return '';
}
function swpierEvemt(before) {
    var p = 1;
    if (!before) {
        p = parseInt(resultData.data.page) + 1;
    }
    sendPostData({page: p, pagesize: 10}, ApiUrl + 'm/order/refundslist', getDataResult);
}