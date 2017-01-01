/**
 *
 * Created by Administrator on 2016/4/8.
 */
$(function(){
    getIP();
    $("#pass_form").validate({
        rules:{
            pwd_num:{
                "required":true,
                "minlength":6,
            },
            repwd_num:{
                "required":true,
                "minlength":6,
                equalTo: "#pwd_num"
            },

        },
        messages:{
            repwd_num: {
                required: "请输入密码!",
                minlength:"密码不能少于6位",
                equalTo: "密码输入不一致!"
            },


        },
        submitHandler:function() {
            //todo
            sendPostData({code:idCode,mobile:phone,pwd:$("#pwd_num").val(),platform_id:1},ApiUrl+'reg/newpwd',function(result){
                if(result.code ==1){
                    show_tips_content2({msg:'密码设置完成！',showok:false,canbtn:'确定',canfun:function(){
                        webBackHandler();
                    }});
                }else{
                    tipsAlert(result.msg);
                }
            });
        }

    });
    var phone ;
    var idCode;
    var md5Code;
    $('#step1_btn').click(function(){
        if(testMobile($('#phone_num').val())){
            phone = $('#phone_num').val();
            $("#section1").hide();
            $("#section2").show();
            $("#section3").hide();
            var reg = /1(\d{2})\d{4}(\d{4})/g;
            var str = phone.replace(reg,"1$1****$2");
            $("#mobile_num").html(str);
        }


    });
    $('#step2_btn').click(function(){

        if($("#id_code").val().length == 6){
            sendPostData({mobile:$('#phone_num').val(),code:$('#id_code').val(),type_id:2},ApiUrl+'sms/check',function(result){
                if(result.code == 1){
                    $("#section1").hide();
                    $("#section2").hide();
                    $("#section3").show();
                }else{
                    tipsAlert(result.msg);
                }
            })

        }

    });

    $("#get_code_btn").click(function(){
        if(countdown == 0){

        }else{

            return ;
        }


        sendPostData({mobile:phone,type_id:2,ip:IPAdd,platform_id:1},ApiUrl+'sms/send',function(result){
            if(result.code == 1){
                //md5Code = ;
                countdown = 60;
                setCountDown();
            }else{
                if(result.code == -1){
                    $("#section1").show();
                    $("#section2").hide();
                    $("#section3").hide();
                    tipsAlert(result.msg);
                }else{
                    tipsAlert(result.msg);
                }

            }
        });
    });


    $("#phone_num").bind('input propertychange',function(){
        if(testMobile($(this).val())){
            $('#step1_btn').css('background','#ff3d23');
        }else{
            $('#step1_btn').css('background','#ddd');
        }
    });

    $("#id_code").bind('input propertychange',function(){
        if($(this).val().length == 6){
            idCode = $(this).val();
            $('#step2_btn').css('background','#ff3d23');
        }else{
            $('#step2_btn').css('background','#ddd');
        }
    });

    $("#pwd_num").bind('input propertychange',function(){
        if($(this).val().length>0 && $(this).val() ==  $("#repwd_num").val()){
            $('#complete_btn').css('background','#ff3d23');
        }else{
            $('#complete_btn').css('background','#ddd');
        }
    });
    $("#repwd_num").bind('input propertychange',function(){
        if($(this).val().length>0 && $(this).val() ==  $("#pwd_num").val()){
            $('#complete_btn').css('background','#ff3d23');
        }else{
            $('#complete_btn').css('background','#ddd');
        }
    });


    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';
});

var countdown=0;
function setCountDown(){
    if(countdown <=0){
        $("#get_code_btn").text('获取验证码');
        $("#get_code_btn").css('background','#ff3d23');
        $("#get_code_btn").css('line-height','50px');
    }else{
        $("#get_code_btn").css('background','#ddd');
        $("#get_code_btn").css({'line-height':'20px','margin-top':'5px',});
        $("#get_code_btn").text('重新获取\n（'+countdown+'）');
        setTimeout(setCountDown,1000);
        countdown--;
    }

}