/**
 * Created by Administrator on 2016/3/28.
 */
$(function(){

    $("#share").click(function(){
        $(".invite_mask").show();
    });
    $(".invite_mask").click(function(){
        $(".invite_mask").hide();
    });
    var url = ApiUrl+ 'm/invite/billboard';
    sendPostData({},url,billboardData);
    //billboardData();
    function billboardData(result){
        if(result.code !=1){
            tipsAlert(result.msg);
            return ;
        }else{

        }
        var source ='{{each ranking_list as value i}}'
            +'<div class="yaoqing-list">'
            +'<div class="left">'
            +'<span class="span1"><img src="{{value.avatar}}"/></span>'
            +'<span class="span2" >{{value.user_name}}<br/>NO.{{i+1}}</span>'
            +'<div class="clear"></div>'
            +'</div>'
            +'<div class="right">{{value.invite_num}}</div>'
            +'<div class="clear"></div>'
            + '</div>'
            +'{{/each}}';

        var render = template.compile(source);
        var str = render(result.data);
        $("#invite_ranking").html(str);
        var width=$(".span1").width();
        $(".span1 img").height(width);
        initWx([ 'onMenuShareTimeline','onMenuShareAppMessage'],initSuccess,location.href);
        shareObj.title = result.data.invite_action_info.title;
        shareObj.link = result.data.invite_action_info.url;
        //shareObj.imgUrl = WapSiteUrl+'/images/108X108-icon.png';
        shareObj.desc = result.data.invite_action_info.desc;

        function initSuccess(){
            wxShareTimeLine(shareObj);
            wxShareToFriend(shareObj);
        }

    }


   
});


