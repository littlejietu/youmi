var pay_code = 1;

$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = get_string_fromlocal('site_id'); 
    initWxYm(['chooseWXPay'],null,location.href, site_id);

    $('#site-name').html(get_string_fromlocal('site_name'));
    // sendPostData({goods_amt:$('#goods_amt').val()},ApiUrl+'m/buy/cash',function(result){


    // });

    var goods_amt = 0;
    var passArr = [];
    $(".tab ul li a").click(function(){
        $("#text").hide();
        var text=$(this).text();
        
        if(text=='.' && passArr.indexOf('.')>-1)
            return;
        
        var goods_amt = parseFloat(passArr.join('')+text).toFixed(2);
        if(goods_amt<1000){
            var pos = 0;
            if(passArr.indexOf('.')>-1)
                pos = passArr.length - passArr.indexOf('.');
            if(pos<3){
                passArr.push(text);
                $('#goods-amt').html(passArr.join(''));
            }
        }
        

    });
    $("#delete_btn").click(function(){
        passArr.pop();
        $('#goods-amt').html(passArr.join(''));

        if(passArr.length==0)
            $('#goods-amt').html('请输入1000元以内金额');
    });

    $("#finish").click(function(){
        if(passArr.length==0){
            tipsAlert('请输入金额');
            return;
        }
        goods_amt = parseFloat(passArr.join('')).toFixed(2);
        sendPostData({"goods_amt":goods_amt,"remark":$('#remark').val()},ApiUrl+'m/order/cash',payresult);
        var height=$(window).height();
        $(".zhezhao").animate({top:height+'px'},500,function(){
            $(".zhezhao").hide();
            passArr.splice(0);
        });

        
    });



    function payresult(result){
        if(result.code ==1 || result.code =="SUCCESS" ){
            if(typeof(result.data.errInfo.return_code)!='undefined' && result.data.errInfo.return_code=='FAIL'){
                show_tips_content2({msg:'未取得微信授权或过期，请重新授权',canfun:goHome,canbtn:'去授权'});

                return;
            }

            wxpay(JSON.parse(result.data.errInfo),success);
        }else{
            tipsAlert(result.msg);
        }

    }

    function success(res){
        //show_tips_content2()
        location.href = ('../order/success.html?pay='+goods_amt+'&v='+Math.random());
    }


});

