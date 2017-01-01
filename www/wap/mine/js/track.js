/**
 * Created by Administrator on 2016/3/30.
 */
var maxLen = 20;
var trackList;
$(function(){
    var trackData = get_user_data_from_local('track');
    if(!trackData||trackData.length==0){
        trackData = [];
        $(".empty").show();
    }
    else{
        $(".empty").hide();
    }
    
    trackList = trackData;
    var time = new Date()
    for(var i = 0 ;i < trackList.length;i++){
        var old = new Date((trackList[i].time))
        if(old.getMonth() == time.getMonth() && old.getDate() == time.getDate()){
            trackList[i].dayName = '今天';
        }else if(new Date(trackList[i].time +24*3600).getDate() == time.getDate()){
            trackList[i].dayName = '昨天';
        }else{
            trackList[i].dayName = new Date(trackList[i].time).Format('yyyy-MM-dd');
        }
    }

    
    var source = '{{each  as obj i}}'
        +'<div class="track"><p>{{obj.dayName}}</p></div>'
        +'<section>'
        +'{{each obj.list as list i}}'
        +'<div class="track-1" onclick="jump_to_url(\'{{list.to_url}}\')">'
        +'<div class="left"><img src="{{list.pic_path}}"/></div>'
        +'<div class="right">'
        +'<p class="p1">{{list.title}}</p>'
        +'<p class="p2">&yen;{{list.price}}&nbsp;&nbsp;&nbsp;'
        +'{{if list.market_price>0}}'
        +'<del>&yen;{{list.market_price}}</del>'
        +'{{/if}}'
        +'</p>'
        +'<p class="p3">喜欢趁早下手哦！</p>'
        +'</div>'
        +'<div class="clear"></div>'
        +'</div>'
        +'{{/each}}'
        +'</section>'
        +'{{/each}}';
    var render = template.compile(source);
    var str = render(trackData);
    $('#track_content').html(str);
    $('#clear_track').click(function(){
        if(trackList && trackList.length>0){
            // $(".cancel-mask").show();
            show_tips_content2({msg:'您确定要清除所有商品吗?',okbtn:'取消',canbtn:'确定',canfun:cancelHandler});
        }
       
    })
    function cancelHandler(){
        trackData = [];
        trackList = [];
        save_user_data_to_local('track',trackData);
        $(".empty").show();
        $('#track_content').html('');
    }

    var width=$(".left").width();
    $(".left img").height(width);
});