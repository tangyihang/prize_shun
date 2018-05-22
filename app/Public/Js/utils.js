utils={
		init:function(){
			this.scroll="";
			this.screenX=document.documentElement.clientHeight||document.body.clientHeight||window.innerHeight||0;
			this.screenY=document.documentElement.clientWidth||document.body.clientWidth||window.innerWidth||0;
			var temp=0;
			if(this.$("#top_header")!==undefined){
				  temp=this.getStyle(this.$("#top_header"),"height");
			      }
			if(temp=="auto")temp="85px";
		    this.headerX=parseFloat(temp)>10?parseFloat(temp):parseFloat(temp)*20;
			if(this.$("#buttom_fixd")!==undefined){
				this.footerX=(parseInt(this.getStyle(this.$("#buttom_fixd"),"height"))||this.headerX)+3;
				}else{
					this.footerX=3;
					}
		    this.setStyle(this.$("#wrapper"),"height",(this.screenX-this.headerX-this.footerX)+"px");
			//alert(this.headerX+"~~~"+this.footerX+"~~~~"+this.screenX)
			/*
*utils.touch 默认的为touch事件，在电脑上为mousedown事件
*
*/
			this.touch="ontouchstart" in document ?"touchstart":"click";
			navigator.vibrate = navigator.vibrate ||navigator.webkitVibrate ||navigator.mozVibrate || navigator.msVibrate;
            /*
			*
			*初始化高度
			*/
		 },
	    $:function(id_cls){
	    var  dom=document.querySelectorAll(id_cls);
		 if(id_cls.indexOf("#")==0){
			 return dom[0];
			 }else{
	         return dom;
		   }
	    },
	    index:function(el){  //根据class获取元素的index值
	      var parent=el.parentNode;
	      for(var i=0;i<parent.children.length;i++){
	           if(parent.children[i]==el) return i;
	      }
	      return false;
	    },
	    getStyle:function(dom,name){
	        if(dom.currentStyle){
		         return dom.currentStyle[name];
	           }else{
		         return getComputedStyle(dom,false)[name];
	        }
        },
        setStyle:function(dom,name,value){
        	dom.style[name]=value;
            if(!!dom.style[name]){
            	dom.style[name]=value;
        	}else{
        		dom.setAttribute(name,value);
        	}
         },
        addEvt:function(el,type,fn){
			if(el.length>0){
			    for(var i=0;i<el.length;i++){
					  if (window.addEventListener) {
			               el[i].addEventListener(type, fn, false);
		                     } else if (window.attachEvent) {
                                   el[i].attachEvent('on' + type, fn);
		                    }
			        }
				}else{
           if (window.addEventListener) {
			el.addEventListener(type, fn, false);
		       } else if (window.attachEvent) {
               el.attachEvent('on' + type, fn)
		   }
		  }
        },
        addCls:function(el,cls){
         el.className+=" "+cls;
         return el;
        },
		hasCls:function(el,cls){
			if(index=el.className.indexOf(cls)>=0){
				return true;
				}else{
					return false;
					}
		},
		calZhu:function(lottery){ //根据彩种类型type计算注数及奖金预测等
			switch(lottery.type){
				case "ssq":
				    var red=lottery.red.length;
					var blue=lottery.blue.length;
					var zhu=1;
					if(red<6||blue==0)return 0;
					while(red>6){
						zhu*=red;
						 red--;
						}
					  zhu*=blue;
					return zhu;
				break;
				case "dlt":
				  var red=lottery.red.length;
				  var blue=lottery.blue.length;
				  var zhu=1;
				  if(red<5||blue<2)return 0;
				  return red*(red-1)*(red-2)*(red-3)*(red-4)*blue*(blue-1)/240 ;
				break;
				case "dltdt":
				  var redDan=lottery.red.dan.length;
				  var redTuo=lottery.red.tuo.length;
				  var blueDan=lottery.blue.dan.length;
				  var blueTuo=lottery.blue.tuo.length;
				  var zhu=1;
				  if((redDan+redTuo<6)||redDan<1)return 0;
				  if((blueDan+blueTuo<3&&blueDan==1)||(blueDan+blueTuo<2&&blueDan==0))return 0;
				  var temp=5-redDan;
				  var temp_=1;
				  while(temp>0){
					  zhu*=(redTuo-temp+1);
					  temp_*=temp;
					  temp--;
					  }
				zhu/=temp_;
				  if(blueDan>0)return zhu*blueTuo;
				  return zhu*blueTuo*(blueTuo-1)/2;
				break;
				case "ssqdt":
				var redDan=lottery.red.dan.length;
				var redTuo=lottery.red.tuo.length;
				var blue=lottery.blue.length;
				var zhu=1;
				if(redDan<1||redDan+redTuo<=6||blue<1)return 0;
				var temp=6-redDan;
				  var temp_=1;
				  while(temp>0){
					  zhu*=(redTuo-temp+1);
					  temp_*=temp;
					  temp--;
					  }
				zhu/=temp_;
				return zhu*blue;
				break;
				case "ren2":
				var ball=lottery.ball.length;
				if(ball<2)return 0;
				return ball*(ball-1)/2;
				break;
				case "ren3":
				var ball=lottery.ball.length;
				if(ball<3)return 0;
				return ball*(ball-1)*(ball-2)/6;
				case "ren4":
				var ball=lottery.ball.length;
				if(ball<4)return 0;
				return ball*(ball-1)*(ball-2)*(ball-3)/24;
				break;
				break;
				case "ren5":
				var ball=lottery.ball.length;
				if(ball<5)return 0;
				return ball*(ball-1)*(ball-2)*(ball-3)*(ball-4)/120;
				break;
				case "ren6":
				var ball=lottery.ball.length;
				if(ball<6)return 0;
				return ball*(ball-1)*(ball-2)*(ball-3)*(ball-4)*(ball-5)/720;
				break;
				case "ren7":
				var ball=lottery.ball.length;
				if(ball<7)return 0;
				return ball*(ball-1)*(ball-2)*(ball-3)*(ball-4)*(ball-5)*(ball-6)/5040;
				case "ren8":
				var ball=lottery.ball.length;
				if(ball<8)return 0;
				return ball*(ball-1)*(ball-2)*(ball-3)*(ball-4)*(ball-5)*(ball-6)*(ball-7)/40320;
				break;
				case "qian1":
				var ball=lottery.ball.length;
				return ball;
				break;
				case "qian2":
				var ball=lottery.ball.length;
				if(ball<2)return 0;
				return ball*(ball-1)/2
				break;
				case "qian2zx":
				var wan=lottery.wan.length;
				var qian=lottery.qian.length;
				if(wan ==0||qian==0)return 0;
				return wan*qian;
				break;
				case "qian3":
                var ball=lottery.ball.length;
				if(ball<3)return 0;
				return ball*(ball-1)*(ball-2)/6;
				break;
				case "qian3zx":
				var wan=lottery.wan.length;
				var qian=lottery.qian.length;
				var bai=lottery.bai.length;
				if(wan ==0||qian==0||bai==0)return 0;
				return wan*qian*bai;
				break;
				default:break;
								
				}
			},
		removeCls:function(el,cls){
        	if(index=el.className.indexOf(cls)>=0){
        	var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
                el.className=el.className.replace(reg,'');
         }
            return el;
        },
		randoms:function(obj){  //obj参数{min,max,many}
			 var arr=[];
			 for(var i=0;i<obj.many;i++){
				 var val=obj.min+Math.round((obj.max-obj.min)*Math.random());
				 while(arr.indexOf(val)!=-1){
					 val=obj.min+Math.round((obj.max-obj.min)*Math.random());
					 }
				  arr.push(val);
				 }
			     return arr;
			}

    }