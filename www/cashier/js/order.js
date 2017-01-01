$(document).ready(function(){ 

	if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }

    var site_name = get_user_data_from_local('site_name');
    $('#site_name').html(site_name);
    var cashier_name = get_user_data_from_local('name');
    $('#cashier_name').html(cashier_name);

    var page=1;
    loadData();

    $('#btnSearch').bind('click', function(){
    	page = 1;
    	loadData();
    });

    function loadData(){
    	var order_id = $('#txtOrder_id').val();
    	var type = $('#hidType').val();
    	var pay_type = $('#hidPay_type').val();
    	var cashier_id = $('#hidCashier_id').val();
    	var time1 = $('#time1').val();
    	var time2 = $('#time2').val();

    	sendPostData({order_sn:order_id,type:type,pay_type:pay_type,cashier_id:cashier_id,begin_time:time1,end_time:time2,page:page}, 
    		ApiUrl+"cashier/order", function(result){
	    	if(result.code=='SUCCESS'){
		        getDataResult(result);
	        }
	    });
    }

    //时间
	var start = {
	  elem: '#time1',
	  format: 'YYYY-MM-DD', 
	  max: '2099-06-16', //最大日期
	  istime: false,
	  istoday: false,
	  choose: function(datas){
	     end.min = datas; //开始日选好后，重置结束日的最小日期
	     end.start = datas //将结束日的初始值设定为开始日
	  }
	};
	var end = {
	  elem: '#time2',
	  format: 'YYYY-MM-DD',
	  max: '2099-06-16',
	  istime: false,
	  istoday: false,
	  choose: function(datas){
	    start.max = datas; //结束日选好后，重置开始日的最大日期
	  }
	};
	laydate(start);
	laydate(end);
	//-时间

	$('.btnPrint').live('click', function(){
		var order_id = $(this).attr('val');
		sendPostData({order_id:order_id}, ApiUrl+'cashier/order/printing', function(result){
			if(result.code=='SUCCESS'){
	    		var print_txt = result.data.print_text;
	    		var order_sn = result.data.order_sn;
	    		javascript:window.external.print(order_sn, print_txt);
			}
		});
	});

});

function getDataResult(result) {
	if(result.code=='SUCCESS'){
	    var source = $('#order-list-tpl').html();
	    var render = template.compile(source);
	    var str = render(result.data);
	    $("#order-list").html(str);
	    //初始数据
	    if(result.data.page==1){
	    	var source = $('#order-cashier-tpl').html();
		    var render = template.compile(source);
		    var str = render(result.data);
		    $("#order-cashier").html(str);

		    $('#order_count').html(result.data.count);
		    $('#order_payed_amt').html(result.data.order_payed_amt);
	    }
	}
}