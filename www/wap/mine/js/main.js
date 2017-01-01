
$(function () {
	$(".recharge").click(function(){
		$(".zhezhao").css("bottom","0");
		$(".fucun").css("bottom","0");
	});
	$(".close").click(function(){
		$(".zhezhao").css("bottom","-1000px");
		$(".fucun").css("bottom","-1000px");
	});
	/*order---publish*/
	// $("#charge li").click(function(){
	// 	$(this).children().children("span").addClass("span1").removeClass("span2");
	// 	$(this).siblings().children().children("span").addClass("span2").removeClass("span1");
	// });

	// $(".publish-1 ul li").click(function(){
	// 	$(this).children("i").addClass("bgcolor").parent().siblings().children("i").removeClass("bgcolor");
	// });

	/*order--order*/
	
	/*order-details4*/
	$(".details4-cancel").click(function(){
		$(".cancel-mask").show();
		$(".cancel-outMask").show();
	});
	$(".details4-btn1").click(function(){
		$(".cancel-mask").hide();
		$(".cancel-outMask").hide();
	});
	/*order---apply*/
	// $(".apply-1 ul li").click(function(){
	// 	$(this).children("i").addClass("select").parent().siblings().children("i").removeClass("select");
	// });

	/*wallet---remain2*/
	// $(".nav-list1 ul li").click(function(){
	// 	$(this).children("a").addClass("current").parent().siblings().children("a").removeClass("current");
	// 	var index=$(this).index();
	// 	$(".remain-list").hide();
	// 	$(".remain-list"+index).show();
	// });
	/*wallet---coupon*/
	$(".coupon-1 ul li").click(function(){
		$(this).children("a").addClass("current").parent().siblings().children("a").removeClass("current");
		var index=$(this).index();
		$(".coupon-list").hide();
		$(".coupon-list"+index).show();
	});
	/*shezhi---index2*/
	$("#name").click(function(){
		$(".zhezhao3").show();
		$(".inner3").show();
	});
	$("#cancel").click(function(){
		$(".zhezhao3").hide();
		$(".inner3").hide();
	});
	/*shezhi---index6*/
	$("#submit").click(function(){
		$(".zhezhao4").show();
		$(".inner4").show();
	});
	/*shezhi---address2*/
	$(".chk_dui").click(function(){
		if($(this).hasClass("dui3")){
			$(this).removeClass("dui3").addClass("dui33");
		}
		else{
			$(this).removeClass("dui33").addClass("dui3");
		}
	});
	/*collect----collect*/

});