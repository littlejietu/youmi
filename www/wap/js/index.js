var homeLoad = 0;
$(function() {
    if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }

    var url = location.href;
    var gun_oil_no = null;
    var site_id = getUrlParam("site_id");
    if(site_id==null||site_id=='')
        site_id = get_string_fromlocal('site_id');
    if(isWeixinBrowser()){
        initWx(['onMenuShareTimeline','onMenuShareAppMessage'],function(res){
            getHomeData();
            if(res == 1){
                shareObj.link = WapSiteUrl+'/index.html?site_id='+site_id;       //?? w/11?
                wxShareTimeLine(shareObj);
                wxShareToFriend(shareObj);
            }
        },url, site_id);
    }else{
        getHomeData();
    }


    function getHomeData(){
        if(homeLoad){
            return;
        }
        //tipsAlert('返回已收到')
        homeLoad = 1;

        sendPostData({site_id:site_id}, "http://"+window.location.host+"/api/m/oil", homeResult);
    }
    //alert(get_user_token());、

    function homeResult(result) {
        if(result.code=='SUCCESS'){
            site_id = result.data.site.site_id;
            save_string_tolocal('site_id', result.data.site.site_id);
            save_string_tolocal('site_name', result.data.site.site_name);
           
            gun_oil_no = result.data.site.gun_oil_no;
            getDataResult(result.data);
        }else{
            tipsAlert(result.msg);
        }

        /*
        if (get_user_token()) {
            // 初始化用户数据
            sendPostData({}, ApiUrl + "m/user/get", function (result) {
                if (result.code == 1) {
                    save_user_data_to_local('userInfo', result.data);
                    shareObj.link = SiteUrl+'/api/wxauth/go?url='+WapSiteUrl+'?site_id='+site_id+'&invite_id='+result.data.user_id;
                    wxShareTimeLine(shareObj);
                    wxShareToFriend(shareObj);
                }
            });
        }
        */
    }

    $("input").focus(function(){
        $(this).closest('div').find('dl').slideDown();
    }).blur(function(){
        $(this).closest('div').find('dl').slideUp();
    }).change(function(){
        $(this).closest('div').find('dd').removeClass('on');
    });
    $('dd').bind('click',function(){
        $(this).siblings('dd').removeClass('on');
        $(this).addClass('on');
        var _text = parseFloat($(this).text());
        $(this).closest('div').find('input').attr('value',_text);
    });

    $('#index-gun dd').live('click',function(){
        var gun_no = $(this).attr('gun');
        var oil_no = $(this).attr('oil');

        oil_change(gun_no, oil_no);
        compare();
    });

    $('#index-amt dd').live('click',function(){
        var gun_no = $("#gun_no").val();
        var amt = $(this).attr('val');
        $("#oil_amt").val(amt);

        compare();
    });

    $('#gun_no').bind('blur',function(){
        var gun_no = $(this).val();
        if(gun_no=='')
            $('#index-oil').html('');
        for(var i=0;i<gun_oil_no.length;i++){
            if(gun_oil_no[i]['gun_no']==gun_no){
                oil_change(gun_no, gun_oil_no[i]['oil_no']);
                break;
            }
        }
        compare();
    });

    $('#oil_amt').bind('blur',function(){
        compare();
    });

    $('#btnSave').click(function(){
        var param = {site_id:site_id,gun_no:$('#gun_no').val(),amt:$('#oil_amt').val()};
        sendPostData(param, ApiUrl+"/m/cart/addoil", function(result){
            if(result.code=='SUCCESS')
                location.href = WapSiteUrl +'/buy/confirm.html?oil_cart_id='+result.data.cart_id;
            else
                tipsAlert(result.msg);
        });
    });

    function oil_change(gun_no, oil_no){
        var oil_type_name = '车用汽油';
        if(oil_no==0)
            oil_type_name = '柴油';

        var source = $('#index-oil-tpl').html();
        var render = template.compile(source);
        var data = {'gun_no':gun_no,'oil_no':oil_no,'oil_type_name':oil_type_name};
        var str = render(data);
        $("#index-oil").html(str);
        $("#gun_no").val(gun_no);
    }

    function compare(){
        var gun_no = $("#gun_no").val();
        var amt = $("#oil_amt").val();
        if(gun_no!='' && amt!='')
            sendPostData({site_id:site_id,gun_no:gun_no,amt:amt}, "http://"+window.location.host+"/api/m/oil/act", getDataResult2);
    }



});


function getDataResult(result) {
    //$("#index-site").html(get_string_fromlocal('site_name'));

    var source = $('#index-gun-tpl').html();
    var render = template.compile(source);
    var str = render(result.site);
    $("#index-gun").html(str);

    var source = $('#index-amt-tpl').html();
    var render = template.compile(source);
    var str = render(result.site);
    $("#index-amt").html(str);

}

function getDataResult2(result) {
    var source = $('#index-act-tpl').html();
    var render = template.compile(source);
    var str = render(result.data);
    $("#index-act").html(str);

    var amt = $('#oil_amt').val()
    if(result.data.act && result.data.act.discount_amt>0)
        amt = amt - result.data.act.discount_amt;
    $('#btnSave').html('实付￥'+amt+'元');
}




