$(function(){
    
    // var str='{"code": 1,"msg": "SUCCESS","action": "search","data": {"hot_search": [{"word": "牛奶","type": "","actionUrl": "","hotWordCategory": "","position": ""},{"word": "拖鞋","type": "","actionUrl": "","hotWordCategory": "","position": ""},{"word": "衬衫","type": "","actionUrl": "","hotWordCategory": "","position": ""},{"word": "面包","type": "","actionUrl": "","hotWordCategory": "","position": ""},{"word": "水果","type": "","actionUrl": "","hotWordCategory": "","position": ""},{"word": "1","type": "","actionUrl": "","hotWordCategory": "","position": ""}],"hot_sale": [{"goods_id": "4","shop_id": "1","name": "test1","pic_url": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","price": "111.00","market_price": "123.00"},{"goods_id": "5","shop_id": "1","name": "test1","pic_url": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","price": "112.00","market_price": "123.00"},{"goods_id": "8","shop_id": "1","name": "商品名称1","pic_url": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103985032025765_240.png","price": "115.00","market_price": "222.00"},{"goods_id": "9","shop_id": "1","name": "羊绒衫","pic_url": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","price": "129.00","market_price": "130.00"},{"goods_id": "10","shop_id": "1","name": "羊绒衫","pic_url": "http://www.xshop.com/upload/shop/goods/1/2016/1_05103984368683352_240.png","price": "130.00","market_price": "130.00"}] }}'
    // var result = JSON.parse(str);
    // getDataResult(result);
    var token = get_user_token();
    if(!token){
        location.href = 'http://data.zooernet.com/api/wxauth/go?url=http://data.zooernet.com/wap/home/index.html';
        return;
    }
    sendPostData({},ApiUrl+'search',getDataResult);
    var mylist = get_user_data_from_local('searchList');
    $("#search_history").html('');
    if(mylist){
        var lst = '';
        for(var key in mylist){
            lst += '<a onclick="gotoSearchList(\''+mylist[key]+'\',2)">'+mylist[key]+'</a>';
        }
        $("#search_history").html(lst);
    }else{

    }

    $(".btn-search").click(function(){
        var str = $.trim($("#keyword_input").val());
        if(!str){
            return ;
        }
        location.href = 'gshow-hor.html?keyword='+(str)+'&search_scene=0';
        var arr = get_user_data_from_local('searchList');
        if(!arr){
            arr =[];
        }
        var num = arr.indexOf(str);
        if(num >=0){
            arr.splice(num,1);
            arr.unshift(str);
            save_user_data_to_local('searchList',arr);
            return ;
        }
        arr.unshift(str);
        if(arr.length > 10){
            arr.pop();
        }
        save_user_data_to_local('searchList',arr);
        
    });
    $("#delete").click(function(){
        var arr = get_user_data_from_local('searchList');
        arr.splice(0);
        $("#search_history").html('');
        save_user_data_to_local('searchList',arr);
    });

});

function gotoSearchList(word,scene){
    location.href = 'gshow-hor.html?keyword='+word+'&search_scene='+scene;
}
function getDataResult(result){
var source = '{{each hot_search as value i}}'   
        +'   <a onclick="gotoSearchList(\'{{value.word}}\',1)">{{value.word}}</a>'    
        +'{{/each}}';

    var render = template.compile(source);
        str = render(result.data);
        $("#hot-search").html(str);

var source = '{{each hot_sale as value i}}'   
        +'  <li id="pic">'
		+'	<a onclick="jump_by_tpl_id({{value.tpl_id}})">'
		+'		<img src="{{value.pic_url}}"/>'
		+'		<p class="p1"></p>'
		+'		<p class="p2">&yen;{{value.price}}<span></span></p>'
		+'	</a>'
		+' </li>'    
    	+ '{{/each}}'

    var render = template.compile(source);
        str = render(result.data);
        $("#search-list").html(str);
        var width=$("#pic").width();
        $("#pic a img").height(width);
}

