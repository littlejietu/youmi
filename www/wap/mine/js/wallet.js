$(function(){
    sendPostData({},ApiUrl+'m/account/detail',getDataResult);

});
function getDataResult(result){
    var render = template('integralNum',result.data);
        $("#integralNum1").html(render);

        var render = template('couponNum',result.data);
        $("#couponNum1").html(render);

        var render = template('acc_balance',result.data);
        $("#acc_balance1").html(render);
}
function setPassword(){
    getUserInfo(resetUser);
}


function resetUser(user){
    if(!user){
        return;
    }
    if(user.paypwd_status == "0"){
        location.href = 'private5.html';
    }else{
        location.href = 'private.html';
    }
}