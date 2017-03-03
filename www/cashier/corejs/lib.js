//rem设置
!function(a,b){var d,c=function(){var a=32,c=b.documentElement,d=c.clientWidth;d&&(c.style.fontSize=a*(d/320)+"px")};b.addEventListener&&(d="orientationchange"in a?"orientationchange":"resize",a.addEventListener(d,c,!1),b.addEventListener("DOMContentLoaded",c,!1),c())}(window,document);

//getJsonData
function getJsonData(url,data,callfunc,errfunc){
  $.ajax({
    url: url,
    data:data,
    dataType: "jsonp",
    jsonp: "callback",
    success: function (data){
       if(data.code==0||data.code==1 || data.success == true){
        callfunc(data);
       }else if(data.code==-200)
       {
       	$(".loginout").show();
       }
       else{
        if(errfunc)
        errfunc(data);
        else
        new superLog(data.info);
       }
    },
      error:function(data){
        if(errfunc)
        errfunc(data);
      }
    });
}
//让IE下支持placeholder的属性
$(document).ready(function(){   
  var doc=document,
		inputs=doc.getElementsByTagName('input'),
		supportPlaceholder='placeholder'in doc.createElement('input'),

	placeholder=function(input){
 		var text=input.getAttribute('placeholder'),
 				defaultValue=input.defaultValue;
	 	if(defaultValue==''){
	    input.value=text
	 	}
	 	input.onfocus=function(){
	    if(input.value===text)
	    {
	        this.value=''
	    }
	  };
 		input.onblur=function(){
	    if(input.value===''){
			  this.value=text
			}
		}
	};
  
  if(!supportPlaceholder){
     for(var i=0,len=inputs.length;i<len;i++){
      var input=inputs[i],
      	text=input.getAttribute('placeholder');
			if(input.type==='text'&&text){
			  placeholder(input)
			}
		}
  }; 
  //下拉框js
  $.divselect(".divselect",".inputselect");


  $('#logoout').bind('click', function(){
    sendPostData({}, ApiUrl+"cashier/login/logout", function(result){
        if(result.code=='SUCCESS'){
          location.href='/cashier/login.html';
          }
      });
  });

  var goCenter = function(id){
    var mydiv = document.getElementById(id);  
    mydiv.style.left = (document.documentElement.clientWidth - 400) / 2+"px";
    mydiv.style.top = (document.documentElement.clientHeight-479) / 2+"px";  
    $('#'+id).show();
  }
  
  //goCenter("mask_con");
    
  var changePasswordStr = "";//修改密码弹框
      changePasswordStr += "<h3>修改密码<\/h3>";
      changePasswordStr += "<div class=\"mask_list\">";
      changePasswordStr += "  <dl>";
      changePasswordStr += "    <dd>";
      changePasswordStr += "    <label class=\"add_space\">原密码<\/label>";
      changePasswordStr += "    <input type=\"password\"/>";
      changePasswordStr += "    <\/dd>";
      changePasswordStr += "    <dd>";
      changePasswordStr += "    <label class=\"add_space\">新密码<\/label>";
      changePasswordStr += "    <input type=\"password\"/>";
      changePasswordStr += "    <\/dd>";
      changePasswordStr += "    <dd>";
      changePasswordStr += "    <label>确认密码<\/label>";
      changePasswordStr += "    <input type=\"password\"/>";
      changePasswordStr += "    <\/dd>";
      changePasswordStr += "  <\/dl>";
      changePasswordStr += "<\/div>";
      changePasswordStr += "<div class=\"operate\">";
      changePasswordStr += "  <a href=\"javascript:void(0);\" class=\"btn save\">保存<\/a>";
      changePasswordStr += "  <a href=\"javascript:void(0);\" class=\"btn cancel\">取消<\/a>";
      changePasswordStr += "<\/div>";
  var printStr ="";//打印机设置弹框
      printStr += "<div class=\"mark\">";
      printStr += "<\/div>";
      printStr += "<div id=\"mark_con\" class=\"mark_con\">";
      printStr += "<h3>打印机设置<\/h3>";
      printStr += "<div class=\"mask_list\">";
      printStr += " <dl>";
      printStr += "   <dd>";
      printStr += "   <label>打印开关<\/label>";
      printStr += "   <span class=\"turn\" id=\"set_onoff\"><\/span>";
      printStr += "   <\/dd>";
      printStr += "   <dd>";
      printStr += "   <label>打印张数<\/label>";
      printStr += "   <div class=\"number_con clean\">";
      printStr += "     <span class=\"del\">-<\/span>";
      printStr += "     <input class=\"number\" readonly type=\"text\" id=\"set_papernum\" value=\"1\"/>";
      printStr += "     <span class=\"add\">+<\/span>";
      printStr += "   <\/div>";
      printStr += "   <\/dd>";
      printStr += "   <dd>";
      printStr += "   <label>打印延迟<\/label>";
      printStr += "   <div class=\"number_con clean\">";
      printStr += "     <span class=\"del\">-<\/span>";
      printStr += "     <input class=\"number\" readonly type=\"text\"  id=\"set_delay\"  value=\"1\"/>";
      printStr += "     <span class=\"add\">+<\/span>";
      printStr += "   <\/div>";
      printStr += "   <\/dd>";
      printStr += " <\/dl>";
      printStr += "<\/div>";
      printStr += "<div class=\"operate\">";
      printStr += " <a href=\"javascript:void(0);\" class=\"btn save\">保存<\/a>";
      printStr += " <a href=\"javascript:void(0);\" class=\"btn cancel\">取消<\/a>";
      printStr += "<\/div>";
      printStr += "<\/div>";
  var printListStr = "";//绑定打印弹框
      printListStr += "<h3>绑定打印<\/h3>";
      printListStr += "<div class=\"mask_list\">";
      printListStr += " <ul class=\"print_ul\" id=\"set_names\">";
      printListStr += " <\/ul>";
      printListStr += "<\/div>";
      printListStr += "<div class=\"operate\">";
      printListStr += " <a href=\"javascript:void(0);\" class=\"btn sure\">确定<\/a>";
      printListStr += " <a href=\"javascript:void(0);\" class=\"btn cancel\">取消<\/a>";
      printListStr += "<\/div>";
  var keyCodeStr = "";//热键唤醒弹框
      keyCodeStr += "<h3>热键唤醒<\/h3>";
      keyCodeStr += "<div class=\"mask_list\">";
      keyCodeStr += " <dl>";
      keyCodeStr += "   <dd>";
      keyCodeStr += "   <label>唤起隐藏<\/label>";
      keyCodeStr += "   <span class=\"key\">Ctrl+Shift+<input class=\"key\" id=\"hotkey\"><\/span>";
      keyCodeStr += "   <\/dd>";
      keyCodeStr += " <\/dl>";
      keyCodeStr += "<\/div>";
      keyCodeStr += "<div class=\"operate\">";
      keyCodeStr += " <a href=\"javascript:void(0);\" class=\"btn sure\">确定<\/a>";
      keyCodeStr += " <a href=\"javascript:void(0);\" class=\"btn cancel\">取消<\/a>";
      keyCodeStr += "<\/div>";
  var strVal = [];
      strVal[0] = null;
      strVal[1] = null;
      strVal[2] = printStr;
      strVal[3] = printListStr;
      strVal[4] = changePasswordStr;
      strVal[5] = keyCodeStr
    
  //goCenter("mask_con");
  $('body').delegate('.mask_con .turn','click',function(){
    $(this).toggleClass('off');
  });
  
  var indx = 0;
  $('.set_list dd').click(function(){
    indx = $(this).index();
    if(indx==0){
      var serverIp = get_user_data_from_local('msg_server_ip');
      var serverPort = get_user_data_from_local('msg_server_port');
      var admin_id = get_user_data_from_local('admin_id');
      var initMsg = '{\"mrid\":\"mrid'+admin_id+'\"}';
      var is_dedug = true;
      javascript:window.external.init(serverIp, serverPort, initMsg, is_dedug);
      return;
    }

    if(indx==1){
      var dt = new Date();
      var now = dt.getFullYear()+'-'+(dt.getMonth()+1)+'-'+dt.getDate()+' '+dt.toLocaleTimeString();
      javascript:window.external.print('1','测试打印 时间:'+now);
      return;
    }

    $('.mask').show();
    $('body #mask_con').html(strVal[indx]);
    goCenter("mask_con");

    if(indx==2){
      var onoff = get_string_fromlocal('onoff');
      var papernum = get_string_fromlocal('papernum');
      var delay = get_string_fromlocal('delay');
      papernum = papernum==0?1:papernum;
      delay = delay==0?1:delay;
      if(typeof(onoff)=='undefined')
        onoff = 'true';
      if(typeof(papernum)=='undefined' || papernum=='NaN' || papernum=='undefined')
        papernum = 1;
      if(typeof(delay)=='undefined' || delay=='NaN' || delay=='undefined')
        delay = 1;
      if(onoff=='true')
        $('#set_onoff').removeClass('off').addClass('on');
      else
        $('#set_onoff').removeClass('on').addClass('off');
        

      $('#set_papernum').val(papernum);
      $('#set_delay').val(delay);
    }else if(indx==3){
      var p_names = get_string_fromlocal('p_names');
      var all_prints = get_string_fromlocal('all_prints');

      if(p_names!='' && all_prints!=''){
        var set_names = '';
        var arrSel = p_names.split('|');
        var arrPrint = all_prints.split('|');
        for(var i=0;i<arrPrint.length;i++){
          var p = arrPrint[i];
          var hover = '';
          for(var ii=0;ii<arrSel.length;ii++){
            var p_s = arrSel[ii];
            if(p==p_s)
              hover = "class=\"hover\"";
          }
          set_names += "<li "+hover+">"+p+"<\/li>";
        }
        $('#set_names').html(set_names);
      }
    }else if(indx==5){
      var hotkey = get_string_fromlocal('hotkey');
      if(hotkey==0 || hotkey=='undefined' || hotkey=='')
        hotkey = 89;
      $('#hotkey').val(String.fromCharCode(hotkey));
    }
  });

  $("#mask_con").delegate('.print_ul li','click', function(){
    if($(this).hasClass('hover'))
      $(this).removeClass('hover');
    else
      $(this).addClass('hover');
  });

  $("#mask_con").delegate('.operate .cancel,.operate .save,.operate .sure','click',function(){

    var cls = $(this).attr('class');
    if(cls=='btn sure' || cls=='btn save'){

      var onoff = papernum = delay = p_names = hotkey = '';
      if(indx==2){
        onoff = $('#set_onoff').hasClass('off')==true?'false':'true';
        papernum = $('#set_papernum').val();
        delay = $('#set_delay').val();
        if(typeof(onoff)!='undefined')
          save_string_tolocal('onoff', onoff);
        if(typeof(papernum)!='undefined')
          save_string_tolocal('papernum', papernum);
        if(typeof(delay)!='undefined')
          save_string_tolocal('delay', delay);
      }else if(indx==3){
        p_names = '';
        $("#set_names li").each(function(i,element){
          if($(this).hasClass('hover'))
            p_names+='|'+$(this).text();
        });
        if(p_names!='')
          p_names = p_names.substring(1);

        if(typeof(p_names)!='undefined')
          save_string_tolocal('p_names', p_names);
      }else if(indx==5){
        hotkey = $('#hotkey').val().charCodeAt();
        if(typeof(hotkey)!='undefined')
          save_string_tolocal('hotkey', hotkey);
      }

      try{
        javascript:window.external.set_param(onoff, papernum, delay, p_names,  hotkey);
          
      }catch(err){
          alert(err);
      }
    }

    $('#mask_con').hide();
    $('.mask').hide();
  });
  
  $("#mask_con").delegate('.number_con .add','click',function(){
    addNum($(this));
  });
  $("#mask_con").delegate('.number_con .del','click',function(){
    delNum($(this));
  });
  
  var addNum = function($this){
    var $parent = $this.closest('.number_con');
    var $input = $parent.find('input.number');
    var num = parseInt($input.attr('value'));
    $input.attr('value',num+1);
  }
  
  var delNum =function($this){
    var $parent = $this.closest('.number_con');
    var $input = $parent.find('input.number');
    var num = parseInt($input.attr('value'));
    if(num<1) return false;
    $input.attr('value',num-1);
  }


 });
 
jQuery.divselect = function(divselect,inputselectid) {
	var inputselect = $(inputselectid);
	$(divselect+" cite").click(function(event){
		event.stopPropagation();
    var ul = $(this).closest(divselect).find("ul");
    if(ul.css("display")=="none"){
    	$(divselect).find('ul').hide();
      ul.slideDown("fast");
    }else{
      ul.slideUp("fast");
    }
	});
	$(divselect+" ul li a").click(function(event){
		event.stopPropagation();
    var txt = $(this).text();
    $(this).closest(divselect).find("cite").html(txt);
    var value = $(this).attr("selectid");
    $(this).closest(divselect).find(inputselect).val(value);
    $(divselect+" ul").hide();
	});
	$('body').click(function(event){
		event.stopPropagation();
		$(divselect).find('ul').hide();
	});
};