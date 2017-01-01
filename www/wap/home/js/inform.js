/**
 *
 * Created by Administrator on 2016/4/4.
 */
$(function(){

    sendPostData({page:1,pagesize:20},ApiUrl+'m/message',getDataResult);
});

function clickMessage(event){
    $(event.currentTarget).addClass('wrapper1');
        sendPostData({id:$(event.currentTarget).attr('id')},ApiUrl+'m/message/readed',function(result){

    });
    for(var key in message){
        if(message[key].id == $(event.currentTarget).attr('id')){
            if(!message[key].read){
                var num = parseInt(get_user_data_from_local('messageNum'));
                if(num >1){
                    num -= 1;
                    save_user_data_to_local('messageNum',num);
                }
            }
            message[key].read = true;

            save_user_data_to_local('message',message);
            break;
        }
    }
    $(event.currentTarget).find('i').remove();
}
var swipeHandler;
var resultData;
var message;
function getDataResult(result){
    resultData = result;
    for(var key in result.data.rows){
        result.data.rows[key].send_time = new Date(result.data.rows[key].send_time*1000).Format('yyyy-MM-dd hh:mm:ss');
        result.data.rows[key].read = false;
    }
    message = get_user_data_from_local('message');
    var obj = {};
    if(message){
        message = message.concat(result.data.rows);
    }else{
        message = result.data.rows;
    }
    message.sort(function(a,b){
        return b.id - a.id;
    });
    if(message.length>50){
        message.splice(50,message.length - 50);
    }
    obj.rows = message;
    save_user_data_to_local('message',message);
    var source='{{each rows as value i}}'
        //+'<div class="swiper-slide">'
        +'<div  class="wrapper {{if value.read}}wrapper1{{/if}}"  id="{{value.id}}" onclick="clickMessage(event);">'
        +'<h3>{{value.title}}<span>{{value.send_time}}</span></h3>'
        +'<p>{{value.content}}{{if !value.read}}<i></i>{{/if}}</p>'
        +'<p class="p2"></p>'
        +'</div>'
        //+'</div>'
        +'{{/each}}'

    var render = template.compile(source);
    str = render(obj);
    $('.trade-list').html(str);

    //if(!swipeHandler){
    //    $('#info_swiper').html(str);
    //    swipeHandler = new SwiperUtils({
    //        container:'.swiper-container',
    //        swpierHandler:swpierEvemt,
    //        collectswiper:'#info_swiper',
    //        deep:200
    //    });
    //    $('.swiper-container').css('height',$(window).height() - 58);
    //
    //}else{
    //    swipeHandler.setSwiperSlider(str);
    //}
    //var total = get_total_page(resultData.data.count,resultData.data.pagesize);
    //swipeHandler.setPage(parseInt(resultData.data.page),total)

}

function swpierEvemt(before){
    var p = 1;
    if(!before){
        p = parseInt(resultData.data.page)+1;

    }
    sendPostData({page:p,pagesize:10,type:4},ApiUrl+'m/message',getDataResult);
}