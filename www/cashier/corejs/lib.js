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