
var imageObj = {};
var reasonIndex =1;
$(function(){
    //initWx();
    getTokenFromUrl();
    //window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
    $("#icon-down").click(function(){
        $(".apply-mask").show();
    });
    $(".mask-inner ul li").click(function(){
        $(this).children("i").addClass("num2").removeClass("num3");
        $(this).siblings().children("i").addClass("num3").removeClass("num2");
        $(".apply-mask").hide();

        var text=$(this).children("span").text();
        $("#icon-down").find("span").text(text);
        reasonIndex = $(this).parent().index()+1;
    });
    $('#reason_text').live('input',function(){
        var str = $(this).val();
        var num = str.length;
        if(num >= 200){
            num = 200;
        }
        $('#text_num').text(str.length+'/200');
    });

    $('#input_refund').keyup(function(){
        var pay = parseFloat($(this).val());
        if(pay > order_refund){
            $(this).val(order_refund);
        }
    });
    $(".apply-2 ul li i").hide();
    
    $(".apply-2 ul li i").click(function(){
         $(this).hide();
        $(this).siblings('img').attr('src','../images/25.png');
        imageObj[$(this).siblings('img').attr('id')] = null;
    });

    $('#submit_btn').click(function(){
        var obj = {};
        var arr = [];
        for( var key in imageObj){
            if(imageObj[key]){
                arr.push(imageObj[key]);
            }
        }
        obj.order_goods_id = getUrlParam('order_goods_id');
        obj.reason_id = reasonIndex;
        obj.refunds_money = $('#input_refund').val();
        obj.reason_content = $('#reason_text').val();
        obj.pic =arr.join(',');
        sendPostData(obj,ApiUrl+'m/order/refunds',function(result){
            if(result.code ==1){
                if(deviceInfo){
                    location.href = 'details5.html?token='+urlToken;
                }else{
                    location.href = 'details5.html';
                }

            }else{
                tipsAlert(result.msg);
            }
        });
    });

    //$(".apply-2 ul li img").click(function(){
    //    if($(this).attr('src') == '../images/25.png'){
    //        wxChooseImage(function(res){
    //            $(this).attr("src",res.localIds[0]);
    //
    //        });
    //    }else{
    //        $(this).attr("src",'../images/25.png');
    //    }
    //
    //});
    var width=$('.apply-2 ul li img').width();
    $('.apply-2 ul li img').css('height',width)
    $(".apply-2 ul li input").width(width);
    $(".apply-2 ul li input").height(width);

    var order_refund = parseFloat(getUrlParam("order_pay"));
    $('#max_input').text(' (最多申请'+order_refund+'元)');
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';

})

