
$(function(){		 
          
	$(".qiandao").click(function(){
		 
		$(".details-zhezhao").show();
		$(".details-tanchu").show();
	});

	$("#add").click(function(){
        $(".chat-pic").show();	
	});
    /*psoductDetails---index.html*/

 	/*orderSubmit----confirm*/
 //    $("#peisong").click(function(){
		 
	// 	$(".confirm-zhezhao").css("top","0");
 //        $(".confirm-fucun").css("top","40%");
	// 	$(".confirm-fucun .div1").css("bottom","0");
	// });
 //    $("#confirm").click(function(){
 //    	$(".confirm-zhezhao").hide();
 //        $(".confirm-fucun").hide();
 //    });

        /*fenlei*/
	// $(".fenlei-left ul li").click(function(){
	// 	var currentIndex=$(".fenlei-left ul li").index(this); //È¡µ±Ç°µã»÷µÄliµÄË÷ÒýÖµ
	// 	var scrollHeight=0;
	// 	$(".fenlei-left ul li").each(function(index){
	// 		if(currentIndex>index)
	// 		{
	// 		scrollHeight+=$(this).height();
	// 		}

	// 		if($(this).find("a").attr("class")=="on")
	// 		{//»Ö¸´ÑùÊ½ÎªÄ¬ÈÏ×´Ì¬
	// 			if(currentIndex==index)return;
	// 			$(this).find("a").removeClass("on");
	// 			$(this).find("img").attr("src","../images/icona"+index+".png");
	// 		//Í¼Æ¬ÐèÌæ»»   todo.....
	// 		}
	// 		else
	// 		{
	// 			if(currentIndex==index)
	// 			{
				
	// 				$(this).find("a").addClass("on");
	// 				//Í¼Æ¬Ìæ»»  todo.....
	// 				$(this).find("img").attr("src","../images/iconb"+index+".png");
	// 				$(".fenlei-left").animate({scrollTop:scrollHeight+'px'},1000);
	// 				var size=$(".fenlei-left ul li").size();
	// 				//ajax»ñÈ¡·ÖÀàÊý¾ÝÏÔÊ¾
					
	// 			}
	// 		}
	//     });
 //    });

    /*cart*/
    


	$("#switch").live('click',function(){
		
		if($(this).hasClass("switch1")){
			$(".cart-edit").show();
			$(".cart-finish").hide();
			$(this).removeClass("switch1").addClass("switch2");
			$(".a1").show();
			$(".a2").hide();
			$("#sum").show();
			$("#delete").hide();
		}
		else{
			$(this).removeClass("switch2").addClass("switch1");
			$(".cart-edit").hide();
			$(".cart-finish").show();
			$(".a2").show();
			$(".a1").hide();
			$("#sum").hide();
			$("#delete").show();
		}
	});
	/*pay*/

	$(".fukuan-content ul li").click(function(){
		
		$(this).children().children("i").addClass("icon4").removeClass("icon3");

        $(this).siblings().children().children("i").removeClass("icon4").addClass("icon3");


	});

   	/*goods-show*/
   	$(".a3").click(function(){
   		if($(this).children("i").hasClass("arow-down")){
   			$(this).children("i").removeClass("arow-down").addClass("arow-up");
   		}
   		else{
   			$(this).children("i").removeClass("arow-up").addClass("arow-down");
   		}
   	});
	$(".a4").click(function(){
		$(".shaixuan-zhezhao").css("right","0");
		$(".shaixuan-inner").css("right","0");
		$(".shaixuan-inner h3").css("right","0");
		$(".shaixuan-foot").css("right","0");
	});
	$(".arow-red").click(function(){
		$(".shaixuan-zhezhao").css("right","-999px");
		$(".shaixuan-inner").css("right","-999px");
		$(".shaixuan-inner h3").css("right","-999px");
		$(".shaixuan-foot").css("right","-999px");
	});

	$(".show1 ul li").click(function(){
       $(this).children("a").addClass("current");
       $(this).siblings().children("a").removeClass("current");
	});
	
	$("#liebiao li").click(function(){
		$(this).addClass("color2").siblings().removeClass("color2");
		$(this).children("i").addClass("gou").parent().siblings().children("i").removeClass("gou");
	});

	$(".brand ul li").click(function(){
		$(this).children("a").addClass("b-current");
		$(this).siblings().children("a").removeClass("b-current");
		var index=$(this).index();
		if(index==0){
			$("#brand0").show();
			$("#brand1").hide();
		}
		else{
			$("#brand1").show();
			$("#brand0").hide();
		}
	});

	$("#price").click(function(){
		$("#mask1").hide();
		$("#outMask1").hide();
		$("#mask3").hide();
		$("#outMask3").hide();
		$("#mask4").hide();
		$("#outMask4").hide();
		$("#mask2").show();
		$("#outMask2").show();

	});
	$("#brandGood").click(function(){
		$("#mask1").hide();
		$("#outMask1").hide();
		$("#mask2").hide();
		$("#outMask2").hide();
		$("#mask4").hide();
		$("#outMask4").hide();
		$("#mask3").show();
		$("#outMask3").show();
	});
    
    $("#shu-address").click(function(){
    	$("#mask1").hide();
		$("#outMask1").hide();
		$("#mask2").hide();
		$("#outMask2").hide();
		$("#mask3").hide();
		$("#outMask3").hide();
		$("#mask4").show();
		$("#outMask4").show();
    });
	$(".arow2-red").click(function(){
		$("#mask2").hide();
		$("#outMask2").hide();
		$("#mask3").hide();
		$("#outMask3").hide();
		$("#mask4").hide();
		$("#outMask4").hide();
		$("#mask1").show();
		$("#outMask1").show();
	});

	/*login---app-order*/
	$(".order-nav ul li").click(function(){
		$(this).children("a").addClass("b-current").parent().siblings().children("a").removeClass("b-current");
		var index=$(this).index();
		$(".order-content").hide();
		$(".order-content"+index).show();

	});
	$(".order2").click(function(){
		$(".order-mask2").show();
		$(".order-outMask2").show();
	});

	$("#task").click(function(){
		
		if($(this).hasClass("task0")){
			$(this).removeClass("task0").addClass("task1");
			$("#list").addClass("listt").removeClass("listh");
			$(".order-mask").css("left","0");

			$(".order-outMask").css("left","0");
			$('html,body').css('overflow', 'hidden');
		}
		else{
			$(this).removeClass("task1").addClass("task0");
			$("#list").addClass("listh").removeClass("listt");
			$(".order-mask").css("left","-999px");
			$(".order-outMask").css("left","-999px");
		}
	});
	/*coupon---coupon*/
	// $(".coupon-1 ul li").click(function(){
	// 	$(this).children("a").addClass("current").parent().siblings().children("a").removeClass("current");
	// 	var index=$(this).index();
	// 	$(".coupon-list").hide();
	// 	$(".coupon-list"+index).show();
	// });

	/*productDetails----index*/
	// $(".list-details span").click(function(){
	// 	$(this).addClass("borColor").siblings().removeClass("borColor");
	// });

	/*orderSubmit---confirm*/
	$("#dist .list").click(function(){
		$(this).children("span").addClass("dist1").parent().siblings().children("span").removeClass("dist1");
	});
	/*productDetails*/
	$(".return").click(function(){
		$(".cancel-mask").show();
		$(".cancel-outMask").show();
	});
	$(".details4-btn1").click(function(){
		$(".cancel-mask").hide();
		$(".cancel-outMask").hide();
	});
});  

function plus(index){
		var num=document.getElementById("num"+index).innerHTML;
	if(num>0)
	{
		num++; 
    	document.getElementById("num"+index).innerHTML=num;	
	}
}
function minus(index){
	var num=document.getElementById("num"+index).innerHTML;
	if(num>1)
	{
		num--;
    	document.getElementById("num"+index).innerHTML=num;
 	}
}
var goods="goods0";
var details="details0";
function phoDetail(index){
   document.getElementById(details).style.display="none";
   document.getElementById(goods).style.color="#575757"
   document.getElementById("details"+index).style.display="block";
   document.getElementById("goods"+index).style.color="#ff3d23";
   details="details"+index;
   goods="goods"+index;
}

