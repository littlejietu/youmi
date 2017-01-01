/**
 *
 * Created by Administrator on 2016/4/8.
 */

var resultData;
$(function(){
    sendPostData({},ApiUrl+"m/addr/addr_list",getDataResult);
    function getDataResult(result){
        resultData = result;
        if(result.data){
            save_user_data_to_local('addressList',result.data.data)
        }else{
            save_user_data_to_local('addressList',null);
        }

        showAddress(result);
    }
});

function showAddress(result){
    if(result.data.data && result.data.data.length>0){
        $('#empty_add').hide();
    }else{
        $('#empty_add').show();
    }
    var str = template('address_sp',result.data);
    $('.address2').html(str);
}

function setDefault_addr(id){
    if(isDefult(id)){
        return ;
    }
    $chk =$(event.currentTarget);
    sendPostData({id:id},ApiUrl+'m/addr/is_default',function(result){
        if(result.code == 1){
            $('.chk_dui').addClass("dui33");
            $('.chk_dui').removeClass("dui3");
            $chk.find("i").removeClass('dui33');
            $chk.find("i").addClass('dui3');
            resetDefult(id);
        }else{
            tipsAlert(result.msg);
        }
    });
}

function resetDefult(id){
    $(resultData.data.data).each(function(index,element){
        if(element.id != id && element.is_default){
            element.is_default = 0;
        }else if(element.id == id){
            element.is_default = 1;
        }
    });
}


function isDefult(id){
    $(resultData.data.data).each(function(index,element){
        if(element.id == id){
            return true;
        }
    });

    return false;
}

function delete_addr(id){
    show_tips_content2({msg:'确定要删除这个地址吗？',okbtn:'取消',canbtn:'确定',canfun:function(){
        sendPostData({id:id},ApiUrl+'m/addr/del',function(result){
            if(result.code ==1) {
                $(resultData.data.data).each(function(index,element){
                    if(element.id == id){
                        resultData.data.data.splice(index,1);
                        showAddress(resultData);
                        return;
                    }
                });
            }else{
                tipsAlert(result.msg);
            }
        });
    }});

}

function edit_addr(id){
    location.href = 'address.html?id='+id;
}

function addAddress(){
    if(resultData.data && resultData.data.data && resultData.data.data.length>=10){
        tipsAlert('地址数量已经达到最多，请删除后再添加新的地址');
        return;
    }
    location.href = 'address.html';
}