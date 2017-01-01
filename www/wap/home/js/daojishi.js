function countDown(time1,container1,endfun){
		this.id=countDown.names.length;
		this.time=time1;
		this.container=container1;
		countDown.names[this.id]=this;
		this.maxTime=-1;
		this.timer=null; //定时器
		this.endFun = endfun;
	}
	countDown.names=new Array();
	countDown.prototype.init=function(){//传入倒计时截止日期字符串,计算出与当前时间的秒数差
			var endTime;
			if(typeof(this.time) == "number"){
				endTime = this.time;
			}else{
				endTime = new Date(this.time.replace(/-/g, "/")).getTime();
			}

			var currentTime=new Date().getTime();
			this.maxTime=parseInt((endTime-currentTime)/1000);
			this.reshow();
			this.timer = setInterval("countDown.names['"+this.id+"'].start();",1000);
		};
	countDown.prototype.reshow = function(){
		var hours=Math.floor(this.maxTime/3600);
		var minutes = Math.floor(this.maxTime%3600/60);
		var seconds = Math.floor(this.maxTime%3600%60);
		if(hours<10)hours="0"+hours;
		if(minutes<10)minutes="0"+minutes;
		if(seconds<10)seconds="0"+seconds;
		var msg = "<i>"+hours+"</i>:<i>"+minutes+"</i>:<i>"+seconds+"</i>";
		if(document.getElementById(this.container)){
			document.getElementById(this.container).innerHTML = msg;
		}
  	}
	countDown.prototype.start=function(){
			if(this.maxTime>=0){
				this.reshow();
				
				--this.maxTime;   
			}   
			else{   
				clearInterval(this.timer);
				if(typeof(this.endFun) == "function"){
					this.endFun();
				}
				//alert("时间已结束"); //生产环境注释此行，替换为结束时样式
			} 
		}

	