/**
 *
 * Created by Administrator on 2016/4/12.
 */
$(function(){
    getUserInfo(showUserInfo);
    function showUserInfo(user){
        if(user.mobile){
            $('#bind_mobile').hide();
        }else{
            $('#bind_mobile').show();
        }
    }
    $('#set_psd_form').validate({
        rules:{

            pwd:{
                "required":true,
                "maxlength":6,
                "minlength":6,
                "number":true,
            },
            repwd:{
                "required":true,
                "equalTo":"#pwd",
            },


        },
        messages:{

            pwd:{
                "required":"密码必须是6位数字",
                "maxlength":"密码必须是6位数字",
                "minlength":"密码必须是6位数字",
                "number":"密码必须是6位数字",
            },
            repwd:{
                "required":'请再次确认密码',
                "equalTo":"两次密码输入不一致",
            },
        },
        submitHandler: function() {
            sendPostData({pwd:hex_md5($('#pwd').val()),repwd:hex_md5($('#repwd').val()),platform_id:1},ApiUrl+'m/account/newpwd',function(resutl){
                if(resutl.code == 1){
                    var url = getUrlParam('url');
                    if(url){
                        location.href = url;
                    }else{
                        location.href = 'private.html';

                    }

                    var user = get_user_data_from_local('userInfo');
                    user.paypwd_status = 1;
                    save_user_data_to_local('userInfo',user);
                }else{
                    tipsAlert(resutl.msg)
                }
            });
        }

    });
});