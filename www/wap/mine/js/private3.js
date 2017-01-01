/**
 *
 * Created by Administrator on 2016/4/13.
 */
var countdown=0;
function setCountDown(){
    if(countdown <=0){
        //border:1px solid #ff3d23;color:#ff3d23;
        $("#get_check_btn").text('获取验证码');
        $("#get_check_btn").css({'border':'1px solid #ff3d23','color':'#ff3d23'});
    }else{
        //$("#get_check_btn").css('background','#ddd');
        $("#get_check_btn").css({'border':'1px solid #ddd','color':'#ddd'});
        $("#get_check_btn").text('验证码\n（'+countdown+'）');

        setTimeout(setCountDown,1000);
        countdown--;
    }

}
$(function () {

    getIP();
    $('#get_check_btn').click(function(){
        if(countdown != 0){
            return ;

        }

        if (!testMobile($('#mobile').val())) {
            $('#error_label').text('手机号码输入有误');

            return;
        }


        sendPostData({mobile:$('#mobile').val(),type_id:2,ip:IPAdd,platform_id:1},ApiUrl+'sms/send',function(result){
            if(result.code == 1){
                //md5Code = ;
                countdown = 60;
                setCountDown();
            }else{
                tipsAlert('手机号码不存在');
            }
        });
    });
    $("#next_btn").click(function () {
        if (!testMobile($('#mobile').val())) {
            $('#error_label').text('手机号码输入有误');

            return;
        }
        if ($.trim($("#id_code").val()) == "") {
            $('#error_label').text('请输入正确的验证码');
            return;
        }else{

        }

        sendPostData({mobile:$('#mobile').val(),type_id:2,platform_id:1,code:$("#id_code").val()}, ApiUrl + 'sms/check', function (result) {
            if (result.code == 1) {
                $("#first_step").hide();
                $("#second_step").show();
            } else {
                tipsAlert(result.msg);
            }
        });

    });
    $('#set_psd_form').validate({
        rules:{

            pay_psd:{
                "required":true,
                "maxlength":6,
                "minlength":6,
                "number":true,
            },
            re_psd:{
                "required":true,
                "equalTo":"#pay_psd",
            },


        },
        messages:{

            pay_psd:{
                "required":"密码必须是6位数字",
                "maxlength":"密码必须是6位数字",
                "minlength":"密码必须是6位数字",
                "number":"密码必须是6位数字",
            },
            re_psd:{
                "required":'请再次确认密码',
                "equalTo":"两次密码输入不一致",
            },
        },
        submitHandler: function() {
            sendPostData({pwd:hex_md5($('#pay_psd').val()),repwd:hex_md5($('#re_psd').val()),platform_id:1},ApiUrl+'m/account/newpwd',function(resutl){
                if(resutl.code == 1){
                    getUserInfo(resetUser);
                }else{
                    tipsAlert(resutl.msg)
                }
            });
        }

    });
});

function resetUser(user){
    if(user){
        user.paypwd_status = 1;
        save_user_data_to_local('userInfo',user);
        location.href = 'private.html';
    }
}