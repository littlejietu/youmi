$(function(){
	var str='{"data": {"count": "12","rows": [{"id": "4862","title": "那你","content": "天天","send_time": "1457924690","type_id": "2","action_title": null,"web_url": null,"app_url": null,"addtime": null},{"id": "5237","title": "退款提醒:您的10元退款","content": "退款提醒:您的10元退款1-10111","send_time": "1458560904","type_id": "2","action_title": "3","web_url": "3","app_url": "3","addtime": null}],"page": "1","pagesize": "10"},"code": "SUCCESS","message": "操作成功"}'
	var result = JSON.parse(str);
    getDataResult(result);
});
function getDataResult(result){
	var source = '{{each rows as value i}}' 
					+'<div class="info-list">'
					+'    	<a href="inform.html">'
					+'	  		<div class="pic">'
					+'{{if value.type_id ==1}}'
					+'	  			<img src="../images/2.png"/>'
					+'{{else if value.type_id ==2}}'
					+'	  			<img src="../images/3.png"/>'
					+'{{else}}'
					+'	  			<img src="../images/4.png"/>'
					+'{{/if}}'
					+'	  			<i>3</i>'
					+'	  		</div>'
					+'	  		<div class="text">'
					+'	    		<h3>'
					+'	    			<span class="title">{{value.title}}</span>'
					+'	    			<span class="info">{{value.type_name}}</span>'
					+'	    			<span class="time">{{value.send_time}}</span>'
					+'	    		</h3>'
					+'				<p>{{value.content}}</p>'
					+'	  		</div>'
					+'	  		<div class="clear"></div>'
					+'	  	</a>'
					+'	</div>'
	             	+'{{/each}}'
	 
	 for (var i = 0;i < result.data.rows.length;i++) {
	 	
	 	if(result.data.rows[i].type_id == 2){
	 		result.data.rows[i].type_name = "交易消息";
	 	}
	 	else if(result.data.rows[i].type_id == 1){
	 		result.data.rows[i].type_name = "通知消息";
	 	}
	 	else{
	 		result.data.rows[i].type_name = "物流消息";
	 	}
	 }
	 var render = template.compile(source);
    str = render(result.data);
    $("#infor").html(str);
    
}