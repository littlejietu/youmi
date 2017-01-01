/**
 *
 * Created by Administrator on 2016/4/7.
 */
var refund_id ;
$(function(){
    getTokenFromUrl();
    sendPostData({order_goods_id:getUrlParam("order_goods_id")},ApiUrl+"m/order/get_refundsinfo",getDataResult);
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
    function getDataResult(resutl){

        if(resutl.code ==1){
            refund_id = resutl.data.refundsInfo.id;
            var statusStr = getStatusStr(resutl.data.refundsInfo.status);
            $('#title_info').text(statusStr);
            $('#refund_cash').text(resutl.data.refundsInfo.refunds_money);
            $('#refund_reason').text(resutl.data.refundsInfo.reason_name);
            $('#refund_instructions').text(resutl.data.refundsInfo.reason_content);
            $('#refund_date').text(new Date(resutl.data.refundsInfo.addtime*1000).Format("yyyy-MM-dd hh:mm:ss"));
            showStatus(resutl.data.refundsInfo.status,resutl)
        }else{

        }
        $('#express_detail').show();
    }

    function showStatus(status,resutl){
        if(status ==1){
            document.getElementById("status_1").className = "span1";
            document.getElementById("status_2").className = "span1";
            document.getElementById("status_3").className = "span2";
            $("#status_title1").css('color',"#ff3d23");
            $("#status_title2").css('color',"#ff3d23");
            $("#status_title3").css('color',"#ff3d23");
             $(".line4").css("background","#ff3d23");
            $(".line3").css("background","#ff3d23");
            $('#details_info').text('退货已经完成');
            $('#cancel_btn1').hide();
            $('#cancel_btn2').hide();
            // $("#tips_text").text("");

        }else if(status == 2){
            document.getElementById("status_1").className = "span1";
            document.getElementById("status_2").className = "span4";
            document.getElementById("status_3").className = "span3";
            $("#status_title3").css('color',"#999");
            $("#status_title2").css('color',"#ff3d23");
            $("#status_title1").css('color',"#ff3d23");
             $(".line4").css("background","#ff3d23");
           
            $('#details_info').text('等待商家确认退货信息');
            $('#cancel_btn1').show();
            $('#cancel_btn2').hide();

        }else if(status ==3){
            document.getElementById("status_1").className = "span1";
            document.getElementById("status_2").className = "span1";
            document.getElementById("status_3").className = "span3";
            $("#status_title1").css('color',"#ff3d23");
            $("#status_title2").css('color',"#ff3d23");
            $("#status_title3").css('color',"#999");
             $(".line4").css("background","#ff3d23");
            var str =resutl.data.shopInfo.province+resutl.data.shopInfo.city+resutl.data.shopInfo.area+resutl.data.shopInfo.address +"    收件人："+resutl.data.shopInfo.seller_name;

            $('#details_info').text(str);
            $('#cancel_btn1').hide();
            $('#cancel_btn2').show();
        }	else if(status ==4){
            document.getElementById("status_1").className = "span1";
            document.getElementById("status_2").className = "span1";
            document.getElementById("status_3").className = "span3";
            $("#status_title1").css('color',"#ff3d23");
            $("#status_title2").css('color',"#ff3d23");
            $("#status_title3").css('color',"#999");
            $(".line4").css("background","#ff3d23");
            $(".line3").css("background","#ff3d23");
            $('#details_info').text('已经退货给商家，等待商家确认收货后，退款将退回到你的账户余额中！');
            $('#cancel_btn1').hide();
            $('#cancel_btn2').hide();
        }
        else if(status ==5){
            document.getElementById("status_1").className = "span4";
            document.getElementById("status_2").className = "span4";
            document.getElementById("status_3").className = "span3";
            $('#details_info').text('您已取消退货');
            $('#cancel_btn1').hide();
            $('#cancel_btn2').hide();
            $("#status_title1").css('color',"#999");
            $("#status_title2").css('color',"#999");
            $("#status_title3").css('color',"#999");
        }
    }

    function getStatusStr(status){
        if(status ==1){
            return '完成';
        }else if(status == 2){
            return '审核中';
        }else if(status ==3){
            return '同意退款';
        }	else if(status ==4){
            return '等待商家确认';
        }
        else if(status ==5){
            return '已取消';
        }
        return '';
    }
    //var width=$(window).width();
    //$(".apply2-mask").css("left",width+'px');
    $(".apply2-mask").addClass('animated')
    $(".apply2-mask").hide();
    $(".sign").click(function(){
        $(".apply2-mask").show();
        //$(".apply2-mask").animate({left:"0px"},500);
        $(".apply2-mask").removeClass('slideOutRight');
        $(".apply2-mask").addClass('slideInRight');
        $(".scroll").css("overflow","hidden");
    });
    $("#express_info").click(function(){
        //var width=$(window).width();
        //$(".apply2-mask").animate({left:width+'px'},500,function(){
        //    $(".apply2-mask").hide();
        //});
        $(".apply2-mask").removeClass('slideInRight');
        $(".apply2-mask").addClass('slideOutRight');
         $(".scroll").css("overflow","auto");
    });

    $.validator.setDefaults({
        submitHandler: function() {
            //alert("提交事件!");
            var dd =$("#express_form").serializeArray();
            var ob = {};
            for(var i = 0 ;i < dd.length;i++){
                ob[dd[i].name] = dd[i].value;
            }
            ob.id = getUrlParam("order_goods_id");
            sendPostData(ob,ApiUrl+'m/order/addlogistic',function(result){
                if(result.code ==1){
                    sendPostData({order_goods_id:getUrlParam("order_goods_id")},ApiUrl+"m/order/get_refundsinfo",getDataResult);
                }else{

                }
            });
        }
    });
    jQuery.validator.addMethod("isMobile", function(value, element) {
        var len = value.length;
        return this.optional(element) || (len == 11 && testMobile(value));
    }, "请正确填写您的手机号码");
    // 在键盘按下并释放及提交后验证提交表单
    $("#express_form").validate({
        rules:{
            logistic_name:"required",
            phone:{
                required:true,
                isMobile:true,
                minlength : 11,
            },
            logistic_id:"required",

        },
        messages:{
            logistic_name:"请输入快递公司名称",
            phone:{
                required : "请输入手机号",
                minlength : "确认手机不能小于11个字符",
                isMobile : "请正确填写您的手机号码"
            },
            logistic_id:"请输入快递单号",

        },
        submitHandler: function() {
            sendPostData({id:refund_id,logistic_sn:$('#logistic_id').val(),logistic_name:$('#logistic_name').val(),phone:$('#phone').val()},ApiUrl+'m/order/addlogistic',function(result){
                if(result.code ==1){
                    $("#express_info").click();
                    tipsAlert("退货快递信息填写成功");
                    sendPostData({order_goods_id:getUrlParam("order_goods_id")},ApiUrl+"m/order/get_refundsinfo",getDataResult);
                }else{
                    tipsAlert(result.msg)
                }
            })
        }

    });

});

function cancelRefund(){
    show_tips_content2({msg:'确定要取消退货吗？',okfun:function(){
        sendPostData({id:refund_id},ApiUrl+'m/order/cancelorder',function(resutl){
            if(resutl.code ==1 ){

                show_tips_content2({msg:'取消退货成功！',showok:false,canbtn:'确定',canfun:function(){
                    window.history.go(-1);
                }});

            }else{
                tipsAlert(resutl.msg);
            }
        });
    }})

}