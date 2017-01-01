/**
 *
 * Created by Administrator on 2016/3/28.
 */

//var teststr = '';
//var testdata = JSON.parse(teststr);
var swipeHandler;
var resultData;
var myObj = {};
var userInfo;
var currentLevelId;
var levelArr = [];
var curentLevel = 1;
$(function(){

    //mylistResult(testdata);
    getTokenFromUrl();
    if(getUrlParam('token')){
        $('#inviteHead').hide();
        $('#content1').css('top','0px');
        $('.scroll').css('top','50px');
        $('#loadingMask').css('top','0px');
    }else{
        $('#inviteHead').show();
        $('#content1').css('top','48px');
        $('.scroll').css('top','98px');
        $('#loadingMask').css('top','48px');
    }
    getUserInfo(function(user){
        userInfo = user;
        myObj.page = 1;
        myObj.pagesize = 10;
        myObj.user_id  = user.user_id;
        currentLevelId = user.user_id;
        levelArr.push(currentLevelId);
        $('#backlevel').hide();
        $('#levelTxt').html('我的'+curentLevel+'级下线');
        sendPostData(myObj,ApiUrl+'m/invite/mylist',mylistResult);
        $('#loadingMask').show();
        shareObj.link = SiteUrl+'/api/wxauth/go?url='+WapSiteUrl+'/home/index.html&invite_id='+userInfo.user_id;
        wxShareTimeLine(shareObj);
        wxShareToFriend(shareObj);
    });

    $('#backlevel').click(function(){
        if(curentLevel<=1){
            return;
        }
        swipeHandler.setBefore(true);
        curentLevel--;

        $('#levelTxt').html('我的'+curentLevel+'级下线');
        myObj.page = 1;
        myObj.user_id = levelArr[curentLevel-1];
        levelArr.splice(curentLevel,1);
        if(myObj.user_id == userInfo.user_id){
            $('#backlevel').hide();
        }
        sendPostData(myObj,ApiUrl+'m/invite/mylist',mylistResult);
        $('#loadingMask').show();
    });
    initWx([ 'onMenuShareTimeline','onMenuShareAppMessage'],initSuccess,location.href);


    function initSuccess(){
        if(userInfo){
            shareObj.link = SiteUrl+'/api/wxauth/go?url='+WapSiteUrl+'/home/index.html&invite_id='+userInfo.user_id;
            wxShareTimeLine(shareObj);
            wxShareToFriend(shareObj);
        }


    }


});

function swpierEvemt(before){
    var p = 1;
    if(!before){
        p = parseInt(resultData.data.curpage)+1;

    }
    myObj.page = p;
    sendPostData(myObj,ApiUrl+'m/invite/mylist',mylistResult);
    $('#loadingMask').show();
}

function selectUser(user_id){
    currentLevelId = user_id;
    levelArr.push(currentLevelId);
    swipeHandler.setBefore(true);
    $('#backlevel').show();
    curentLevel++;

    $('#levelTxt').html('我的'+curentLevel+'级下线');
    myObj.page = 1;
    myObj.user_id = user_id;
    sendPostData(myObj,ApiUrl+'m/invite/mylist',mylistResult);
    $('#loadingMask').show();

}

function mylistResult(data) {
    $('#loadingMask').hide();
    resultData = data;
    var source = '{{each user_list as value i}}'
        + '<div class="swiper-slide">'
        + '<div class="yaoqing-list" onclick="selectUser({{value.user_id}});" >'
        + '<div class="left">'
        + '<span class="span1"><img src="{{value.avatar}}"/></span>'
        + '<span class="span2" style="margin-top: 15px;">{{value.user_name}}</span>'
        + '<div class="clear"></div>'
        + '</div>'
        + '<div class="right" style="margin-top:17px">{{value.date}}<i></i></div>'
        + '<div class="clear"></div>'
        + '</div>'
        + '</div>'
        + '{{/each}}';

    var render = template.compile(source);
    var str = render(data.data);
    if(!swipeHandler){
        $('#invite_swiper').html(str);
        swipeHandler = new SwiperUtils({
            container:'.swiper-container',
            swpierHandler:swpierEvemt,
            collectswiper:'#invite_swiper',
            deep:200
        });
        $('.swiper-container').css('height',$(window).height() - 108);

    }else{
        swipeHandler.setSwiperSlider(str);
    }
    var total = parseInt(resultData.data.totalpage);
    swipeHandler.setPage(parseInt(resultData.data.curpage),total)
};