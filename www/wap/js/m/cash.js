var pay_code = 1;

$(function(){
    if(typeof FastClick != 'undefined') FastClick.attach(document.body);

    var site_id = getUrlParam("site_id");
    if(site_id==null||site_id=='')
        site_id = get_string_fromlocal('site_id');
    else
        save_string_tolocal('site_id', site_id);

    initWxYm(['chooseWXPay'],null,location.href, site_id);

    sendPostData({site_id:site_id}, "http://"+window.location.host+"/api/home/info", function(result){
        if(result.code=='SUCCESS'){
            site_id = result.data.site_id;
            save_string_tolocal('site_id', result.data.site_id);
            save_string_tolocal('site_name', result.data.site_name);
            $('#site-name').html(result.data.site_name);
            document.title = result.data.site_name;
        }else{
            tipsAlert(result.msg);
        }
    });

    //$('#site-name').html(get_string_fromlocal('site_name'));
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
        sendPostData({"goods_amt":goods_amt,"remark":$('#remark').html()},ApiUrl+'m/order/cash',payresult);
        var height=$(window).height();
        $(".zhezhao").animate({top:height+'px'},500,function(){
            $(".zhezhao").hide();
            passArr.splice(0);
        });

        
    });

    


    $('#btnAdd').click(function(){
        showRemark({canfun:hideRemark,okfun:saveRamark});
    });



    function payresult(result){
        if(result.code ==1 || result.code =="SUCCESS" ){
            if(typeof(result.data.errInfo.return_code)!='undefined' && result.data.errInfo.return_code=='FAIL'){
                show_tips_content2({canfun:goHome,canbtn:'去授权'});

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




var tips_remark = '<div class="other-mask" style="display:none;">'
    +'  <div class="cancel-outMask3" >'
    +'      <div class="cancel-wrapper">'
    +'          <h3>备注</h3>'
    +'          <div class="div1" style="text-align:center;position:relative;">'
    +'              <div id="msg_content" style="line-height:20px;text-align:left;margin-right:15px"><input type="text" id="txtRemard" style="border: 1px solid #ccc;width:100%"></div>'
    +'          </div>'
    +'          <p class="p1"><button class="btn2 details4-btn1" id="tips2_sure_btn">确定</button><button  class="btn1" id="tips2_cancel_btn">取消</button></p>'
    +'      </div>'
    +'  </div>'
    +'</div>';
var tips_remark_init1  = false;
/*
 opts{msg:'111',okbtn:'确定',canbtn:'取消'，okfun:fun,canfun:fun,showcan:boolean,showok:boolean}

 */
function showRemark(opts){
    if(!tips_remark_init1){
        tips_remark_init1 = true;
        $('body').append(tips_remark);

        $(window).bind('resize',function(){
            resize();
        });
    }

    $("#tips2_cancel_btn").one('click',function(){
        $('.other-mask').hide();
        $("#tips2_sure_btn").off('click');
        if(opts.canfun){
            opts.canfun();
        }
    });

    $("#tips2_sure_btn").one('click',function(){
        $('.other-mask').hide();
        $("#tips2_cancel_btn").off('click');
        if(opts.okfun){
            opts.okfun();
        }
    });

    $('.other-mask').show();
    resize();
    function resize(){
        var hei =$(window).height();
        var wid =$(window).width();
        var hh = $('.other-mask .cancel-outMask3').height();
        var ww = $('.other-mask .cancel-outMask3').width();
        $('.other-mask .cancel-outMask3').css('left',wid-ww >> 1 );
        $('.other-mask .cancel-outMask3').css('top',hei-hh >> 1 );
    }

}

function hideRemark(){
    $('.other-mask').hide();
}

function saveRamark(){
    $('#remark').html($('#txtRemard').val());
    $('#btnAdd').text('修改');

}
