$(function(){
	sendPostData({},ApiUrl+'m/account/detail',function(result){
		var source='<p>我的可用余额(元)</p>'
			+'<h3>&yen;{{acct_balance}}</h3>'
		var render = template.compile(source);
		var str = render(result.data);
		$("#remain").html(str);
		$("#use_cash").click(function(){
			location.href = 'cash.html';
		});
	})

    $(".nav-list1 ul li").click(function(){
		$(this).children("a").addClass("current").parent().siblings().children("a").removeClass("current");
		var idd  = $(this).attr('id');
		var id = idd.split('_')[1];
		sendPostData({page:1,pagesize:10,type:id},ApiUrl+'m/account',getDataResult);

	});

	$("#btnType_1").click();


});
var resultData;
function getDataResult(result){
	resultData = result;
    for(var i=0;i<result.data.rows.length;i++){
    	result.data.rows[i].create_time=new Date(result.data.rows[i].create_time*1000).Format('yyyy-MM-dd hh:mm:ss');
    }

	var source='{{each rows as value i}}'
	            +'<tr>'
				+'	<td>{{value.title}}<br/> {{value.create_time}}</td>'
				+'	<td>{{value.inout}}</td>'
				+'	<td>{{value.amount}}</td>'
				+'</tr>'
	  			+'{{/each}}'

	var render = template.compile(source);
    var str = render(result.data);
    $("#remain-list").html(str);

}