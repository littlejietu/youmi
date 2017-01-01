/**
 *
 * Created by Administrator on 2016/5/4.
 */
$(function(){

    var countdown = 0;
    getIP();
    function setCountDown(){
        if(countdown <=0){
            //border:1px solid #ff3d23;color:#ff3d23;d
            $("#get_check_btn").text('获取验证码');
            $("#get_check_btn").css({'border':'1px solid #ff3d23','color':'#ff3d23'});
        }else{
            //$("#get_check_btn").css('background','#ddd');
            $("#get_check_btn").css({'border':'1px solid #ddd','color':'#ddd'});
            $("#get_check_btn").text('验证码（'+countdown+'）');

            setTimeout(setCountDown,1000);
            countdown--;
        }

    }

    $('#get_check_btn').click(function(){
        if(countdown != 0){
            return ;

        }
        if (!testMobile($('#addmobile').val())) {
            $('#error_label').text('手机号码输入有误');

            return;
        }

        sendPostData({mobile:$('#addmobile').val(),type_id:5,ip:IPAdd,platform_id:1},ApiUrl+'sms/send',function(result){
            if(result.code == 1){
                //md5Code = ;
                countdown = 60;
                setCountDown();
            }else{
                tipsAlert(result.msg);
            }
        });
    });
    $('#submit').click(function(){
        if (!testMobile($('#addmobile').val())) {
            $('#error_label').text('手机号码输入有误');

            return;
        }
        if ($.trim($("#code_input").val()) == "" || $('#code_input').val().length<6) {
            $('#error_label').text('请输入正确的验证码');
            return;
        }else{

        }

        sendPostData({mobile:$('#addmobile').val(),type_id:5,platform_id:1,code:$("#code_input").val()}, ApiUrl + 'm/user/mod_mobile', function (result) {
            if (result.code == 1) {
                getUserInfo(resetUser);

            } else {
                tipsAlert(result.msg);
            }
        });
    });
});
function resetUser(user){
    if(user){
        user.mobile = $('#addmobile').val();
        save_user_data_to_local('userInfo',user);
        if($('#bind_mobile').length>0){
            $('#bind_mobile').hide();
        }else{
            location.href= '../wallet/private.html'
        }
    }
}