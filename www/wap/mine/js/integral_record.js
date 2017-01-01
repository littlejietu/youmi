$(function(){
    getTokenFromUrl();
    sendPostData({"page":1,},ApiUrl+'m/integral/integral_list',getDataResult);
    window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';

});
function getDataResult(result){
    var render = template('integral_num',result.data);
    $("#integral_num1").html(render);
	//var source = '{{each list as value i}}'
     //       	+'<div class="jifen-list">'
	//			+'<span class="span1">{{value.op_type}}<i>{{value.add_time}}</i></span>'
	//			+'<span class="span2">{{value.num}}</span>'
	//			+'</div>'
     //   		+'{{/each}}';
    var str = '';
    for(var key in result.data.list){
        var ob = result.data.list[key];
        str +=   '<div class="jifen-list">'
        			+'<span class="span1">'+getStrByType(ob.op_type)+'<i>'+new Date(ob.add_time*1000).Format('yyyy-MM-dd')+'</i></span>'
        			+'<span class="span2">'+ob.num+'分</span>'
        			+'</div>'
    }
        //var render = template.compile(source);
        //str = render(result.data);
        $("#jifen_record").html(str);
}

function getStrByType(type){
    if(type == 1){
        return '消费获得积分';
    }else if(type == 2){
        return '签到获得积分'
    }else if(type == 3){
        return '兑换消耗积分'
    }else{
        return '';
    }

}