var pay_code = 1;

$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = get_string_fromlocal('site_id'); 
    initWx(['chooseWXPay'],null,location.href, site_id);

    var ids = getUrlParam('order_ids');
    $('#site-name').html(get_string_fromlocal('site_name'));
    sendPostData({order_ids:ids,agent_type:0},ApiUrl+'m/buy/cashier',getDataResult);

/*
    $(".return").click(function(){
        // $(".cancel-mask").show();
        show_tips_content2({msg:'您确定要这么做吗？您的订单可能在24小时消失！',okbtn:'离开',canbtn:'继续支付',okfun:sureHandler});
    });
    $(".details4-btn1").click(function(){
         $(".cancel-mask").hide();
    });
    function sureHandler(){
        location.href = ('/wap/mine/order/order.html?type=1');
    }
    */
    // $('#leave_btn').click(function(){
        
    // });

    function success(res){
        //show_tips_content2()
        location.href = ('success.html?pay='+pay_amount+'&v='+Math.random());
    }

    $(".close").click(function() {
        var height = $(window).height();
        $(".zhezhao").animate({top: height + 'px'}, 500, function () {
            $(".zhezhao").hide();
            passArr.splice(0);
            resetpass();
        });
    });
    var passArr = [];
    $(".tab ul li a").click(function(){
        $("#text").hide();
        var text=$(this).text();
        if(passArr.length <6){
            passArr.push(text);
        }
        resetpass();
    });
    $("#delete_btn").click(function(){
        passArr.pop();
        resetpass();
    });
    function resetpass(){
        $('.form table tr td input').each(function(index,element){
            if(index < passArr.length){
                $(this).val(passArr[index]);
            }else{
                $(this).val('');
            }

        });
        // alert(passArr.join(" "));
    }
    $("#finish").click(function(){
        if(passArr.length==6){
            sendPostData({"paymethod":pay_code,"paypwd":hex_md5(passArr.join('')),"order_ids":ids},ApiUrl+'m/order/paying',payresult);
            var height=$(window).height();
            $(".zhezhao").animate({top:height+'px'},500,function(){
                $(".zhezhao").hide();
                passArr.splice(0);
                resetpass();
            });

        }
        else{
            $("#text").show();
        }
    });

    function payresult(result){
        if(result.code ==1 || result.code=='SUCCESS'){
            location.href = ('success.html?pay='+pay_amount);
        }else{
            tipsAlert(result.msg);
        }

    }
});
var pay_amount;
function getDataResult(result){
    if(result.code != 1 && result.code !='SUCCESS'){
        goHome();
    }
    pay_amount=result.data.pay_amount;
    var source = $('#oil-info-tpl').html();
    var render = template.compile(source);
    var str = render(result.data);
    $("#oil-info").html(str);

    $("#paymethod dd").click(function(){
        if(!$(this).hasClass('type')){
            return;
        }

        pay_code = $(this).attr('payType');
        var current = $(this);
        if(pay_code == '1'){
            getUserInfo(getuserpwdInfo);
            function getuserpwdInfo(user){
                if(user.paypwd_status == "0"){
                    show_tips_content2({msg:'尚未设置支付密码，请先设置支付密码',canfun:showsetPass,canbtn:'确定'});
                }else{
                    current.siblings().removeClass("on");
                    current.addClass("on");
                }
            }

        }else{
            $(this).siblings().removeClass("on");
            $(this).addClass("on");
        }

    });

    function showsetPass(){
        location.href = '../../mine/wallet/private5.html?url='+encodeURI(location.href);
    }


    $("#pay-btn").click(function(){
        if(pay_code=='12'){
            sendPostData({"order_ids":ids,"paymethod":pay_code},ApiUrl+'m/order/paying',function(result){
                console.log(result);
                if(result.code ==1 ){
                    wxpay(JSON.parse(result.data.errInfo),success);
                }else{
                    tipsAlert(result.msg)
                }
            });
        }else if(pay_code == '1'){

            $(".zhezhao").show();
            var height = $(window).height();
            $(".zhezhao").css("top",height);
            $(".zhezhao").animate({top:"0px"},500);
        }

    });


}