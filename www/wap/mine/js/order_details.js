/**
 *
 * Created by Administrator on 2016/4/5.
 */
function refund_order(order_goods_id,pay_amt){
    //$('#msg_content').text('你确定要取消这个订单吗？');
    //$('.cancel-mask').show();
    //$('.btn1').click(function(){
    //    location.href = 'apply.html?order_goods_id='+order_goods_id;
    //});
    location.href = './apply.html?order_goods_id='+order_goods_id+'&order_pay='+pay_amt;
}
$(function(){
    sendPostData({order_id:getUrlParam('order_id')},ApiUrl+'m/order/detail',getDataResult);
    function getDataResult(result) {
        var source = '		<p class="p2">'

            +'{{if order_detail.status == "Closed"}}'
            + '<a ><span class="span2">已关闭</span></a>'
            + '{{ else if order_detail.status=="WaitPay" ||order_detail.status=="Create"}}'
            + '<a onclick="cancel_order({{order_detail.order_id}});"><span class="span2">不想要了</span></a>'
            + '        <a onclick="payNow({{order_detail.order_id}});"><span class="span1" style="color:#fff;background:#ff3d23;">立即付款</span></a>'
            + '{{else if order_detail.status=="WaitSend"}}'
            //+ '<a onclick="apply_refund({{order_detail.order_goods_id}});"><span class="span2">申请退款</span></a>'
            + '  <a onclick="cancel_order({{order_detail.order_id}});"><span class="span2">不想要了</span></a><a><span class="span1" style="color:#fff;background:#ff3d23;">等待发货</span></a>'
            + '{{else if order_detail.status=="WaitConfirm"}}'
            + '        <a onclick="confirmOrder({{order_detail.order_id}});"><span class="span1" style="color:#fff;background:#ff3d23;">确认收货</span></a>'
            + '{{else if order_detail.status=="Finished" && (!order_detail.comment_status || order_detail.comment_status == "0")}}'
            + '        <a onclick="commentOrder({{order_detail.order_id}});"><span class="span1" style="color:#fff;background:#ff3d23;">评价</span></a>'
            + '{{/if}}'
            + '      </p>';
        if(result.code !=1){
            tipsAlert(result.msg)
            return ;
        }
        var render = template.compile(source);
        var str = render(result.data);
        result.data.order_detail.createtime = new Date(result.data.order_detail.createtime*1000).Format('yyyy-MM-dd hh:mm:ss');
        $(".wuliu-footer").html(str);
        str = template('address_info', result.data.order_detail);
        $(".up").html(str);
        if(result.data.deliver_log){
            str = template('express_detail', result.data);
            $(".middle").html(str);
        }


        str = template('goods_info', result.data);
        $("#goods_list").html(str);
        var width=$(".left").width();
        $(".left img").height(width);

        str = template('express_info', result.data.order_detail);
        $(".order-details").html(str);
        str = template('real_pay', result.data.order_detail);
        $(".wuliu-3").html(str);

        str = template('order_number', result.data.order_detail);
        $(".wuliu-1").html(str);
    }

});
function confirmOrder(order_id){
    show_tips_content2({msg:'您确认收货吗？',okbtn:'取消',canbtn:'确定',canfun:function(){
        sendPostData({order_id:order_id},ApiUrl+'m/order/confirm',confirmData);
    }});
    //$('#msg_content').text('确认收货后钱？');

}


function commentOrder(order_id){
    location.href = '../order/publish.html?order_id='+order_id;
}

function confirmData(result){
    if(result.code==1){
        location.reload();
    }else{
        tipsAlert(result.msg);
    }
}

function cancel_order(order_id){
    current_order = order_id;
    show_tips_content2({msg:'您确定要取消这个订单吗？',okbtn:'确定',canbtn:'我在想想',okfun:sureHandler})

    // $('#msg_content').text('你确定要取消这个订单吗？');
    // $('.cancel-mask').show();

    // $('.btn1').one('click',sureHandler);

}


function sureHandler(){
    sendPostData({order_id:current_order},ApiUrl+'m/order/close',function(result){
        if(result.code == 1){
             location.reload();
            //$(".wuliu-footer").hide();
        }
        $('.cancel-mask').hide();
    });

}

function apply_refund(order_goods_id){
    current_order =order_goods_id;
    $('#msg_content').text('您确定要要退款？商家将在1-7个工作日退款至您的账户。');
    $('.cancel-mask').show();


}

function payNow(order_id){
    location.href = '../../home/productdetails/pay.html?order_ids='+order_id;
}