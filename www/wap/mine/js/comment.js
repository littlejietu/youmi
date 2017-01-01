/**
 *
 * Created by Administrator on 2016/4/4.
 */
//var star_num = 5;
var imageObj = {};
var starObj = {};
function ratingStars(index,goods_id){

    starObj[goods_id] = index+1;
    var p = $(event.currentTarget).parents('.publish');
    //alert($(p).attr('id'));

    var pp = $(event.currentTarget).parents('.publish-2');
    $($(pp).find('i')).each(function(ind,element){
        if(ind <= index){
            $(element).removeClass("heart");
            $(element).addClass("heart2");
        }else{
            $(element).addClass("heart");
            $(element).removeClass("heart2");
        }
    })
    //for(var i=0;i<5;i++){
    //    if(i<=index){
    //        //$("#rating-stars0"+i).removeClass("heart");
    //        //$("#rating-stars0"+i).addClass("heart2");
    //        document.getElementById("rating-stars0"+ i).className = 'heart2';
    //
    //    }else{
    //        //$("#rating-stars0"+i).removeClass("heart2");
    //        //$("#rating-stars0"+i).addClass("heart");
    //        document.getElementById("rating-stars0"+ i).className = 'heart';
    //
    //    }
    //}
    star_num = index;
}

$(function(){
    var orderData;
    $(".div1").click(function(){
        var com = {};
        com.order_id = order_id;
        for(var i = 0;i < orderData.goods.length;i++){
            var go = orderData.goods[i];
            com['comment['+go.goods_id+'][goods_id]']  = go.goods_id;
            com['comment['+go.goods_id+'][sku_id]']  = go.sku_id;
            com['comment['+go.goods_id+'][comment]'] = $('#commentdetail_'+go.goods_id).val();
            var ss = '';
            var picO ;
            var arr = [];
            for(var key in imageObj){
                if(key == go.goods_id){
                    picO = imageObj[go.goods_id];
                }
            }
            for(key in picO){
                if(picO[key]){
                    arr.push(picO[key]);
                }
            }
            com['comment['+go.goods_id+'][pic_path]'] = arr.join("|");
            com['comment['+go.goods_id+'][score_level]'] = starObj[go.goods_id];



        }
        sendPostData(com,ApiUrl+'m/comment/add',function(result){
            if(result.code == 1){
                location.href = 'order.html?type=4'
            }else{
                tipsAlert(result.msg);
            }
        });

    });
    var order_id = getUrlParam('order_id');

    sendPostData({order_id:order_id},ApiUrl+'m/order/detail',getOrderData);

    function getOrderData(result){
        for(var i = 0;i < result.data.goods.length;i++){
            imageObj[result.data.goods[i].goods_id] = {};
            starObj[result.data.goods[i].goods_id] = 5;
        }
        orderData = result.data;
        var source = template('order_temp',result.data);
        $("#order_list").html(source);
        var wid = $('.down span img').width();
        $(".down span img").height(wid);
        $(".down span input").height(wid);
        var width=$(".left").width();
        $(".left img").height(width);
    }


});

//function chooseImage(){
//    if($(this).attr('src') == '../images/24.png'){
//        wxChooseImage(function(res){
//            $(this).attr("src",res.localIds[0]);
//
//        });
//    }else{
//        $(this).attr("src",'../images/24.png');
//    }
//}
