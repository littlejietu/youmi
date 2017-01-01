

function clickSlide() {
  //stop auto sliding
  window.clearInterval(autoSlide);
  isAutoSliding = false;
  var slideIndex = $bullet.index($(this));
  updateIndex(slideIndex);
};

function updateIndex(currentSlide) {
  if(isAutoSliding) {
    if(current === slidesTotal) {
        $slideGroup.css("top","100%");
        current = 0;
    } else {
      current++;
    }
  } else {
      current = currentSlide;
  }

  $bullet.removeClass('current');
  $bullet.eq(current).addClass('current');

  transition(current);
};

function transition(slidePosition) {
    $slideGroup.animate({
      'top': '-' + slidePosition + '00%'
    });
};

var autoSlide, $slide,slidesTotal,current,$slideGroup,$bullet,isAutoSliding

function hotstart(){
    autoSlide = window.setInterval(updateIndex, 2000);
    $slide = $('.slide');
    $slideGroup = $('.slide-group');
    $bullet = $('.bullet');

    slidesTotal= ($slide.length - 1);
    current = 0;
    isAutoSliding = true;

    $bullet.first().addClass('current');
    $bullet.on( 'click', clickSlide);
}



// $(document).ready(function(){
//     var str='';
//     var str1='';
//     var str2='';
//     var str3='';
//     var str4='';
//     $.ajax({ 
//      url: "/user.php", 
//      data:"{'act':'getgoodinfo','sort':1}",
//         type:'get',    
//         cache:false,    
//         dataType:'json',    
//          success: function(data){
//            /*轮播*/
//            for(var i in data.swiplist){
//              str4+='<a href="#" class="swiper-slide"><img src="'+data.swiplist['+i+']+'"></a>'
//            }
//            $('#swiper').html(str4);
//            /*热点广告*/
//            for(var i in data.adlist){
//              str3+='<li class="slide">'+data.adlist['+i+''].text+'</li>'
//            }
//            $('#hot_ad').html(str3);
//            /*猜你喜欢*/
//              for(var i in data.goodlist){
//                  str=str+'<li><a href="#"><img src="'+data.goodlist['+i+'].img+'"/><h3>'+data.goodlist['+i+'].title+'</h3><p class="p1"></p><p class="p2">&yen;'+data.goodlist['+i+'].price+'<span></span></p><p class="p3">'+data.goodlist['+i+'].categray+' &gt;</p></a></li>';
//                     if(i%3==0 && i>0){
//                         str=str+'<div class="clear"></div>';
//                     }
//              }
//              $('#love_goodlist').html(str);
//              /*热卖推荐*/
//              for(var i in data.goodlist2){
//                str1+='<li><a href="#"><img src="'+data.goodlist2['+i+'].img+'"/><h3>'+data.goodlist2['+i+'].title+'</h3><p class="p1"></p><br/><p class="p2">&yen;'+data.goodlist2['+i+'].price+'&nbsp;<del>&yen;'+data.goodlist2['+i+'].be_price+'</del><i></i></p></a></li>'
//              }
//              $('#hot_goodlist').html(str1);
//              /*精选推荐*/
//              for(var i in data.goodlist3){
//                str2+='<li><a href="#"><img src="'+data.goodlist3['+i+'].img+'"/><h3>'+data.goodlist3['+i+'].title+'</h3><p class="p1"></p><br/><p class="p2">&yen;'+data.goodlist3['+i+'].price+'&nbsp;<del>&yen;'+data.goodlist3['+i+'].be_price+'</del><i></i></p></a></li>'
//              }
//              $('#select_goodlist').html(str2);
//              /*消息*/
//              if(data.msg_num>0){
//                 $('#msg').html('<span>'+data.msg_num+'</span>');
//              }
//              /*特价秒杀*/
//                 if(data.miaosha_info){
//                  var miaosha_end=data.miaosha_info.end_time;//'2016-02-22 21:00:00'
//                  var daojishi=new countDown(miaosha_end,'miaosha');
//                daojishi.init(); //启动倒计时计时器
//                //内容替换
//                $('#miaosha').html(data.miaosha_info.img);
//              }
//          }
//      });
// });