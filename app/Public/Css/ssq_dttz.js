window.onload=function(){
	utils.addEvt(document,utils.touch, function(event){
		 switch(event.target.className){ 
		  case "graybtn":
	       utils.initlottery();
           break;
		   
          default:break;
		 
		 }
		})/*******************全局事件处理***********************/
	utils.lottery={type:"ssqdt",red:{dan:[],tuo:[]},blue:[],zhu:0};  //初始化胆拖区
	utils.initlottery=function(){                  //初始化投注内容
	 var doms=utils.$(".active");
		    for(var i=0;i<doms.length;i++)
	       utils.removeCls(doms[i],"active");
		   utils.lottery.red.dan=[];
		   utils.lottery.red.tuo=[];
		   utils.lottery.blue=[];
		   utils.lottery.zhu=0;
		   utils.$(".betNum")[0].innerHTML=0;
		   utils.$(".betMoney")[0].innerHTML=0;
	
	}; 
	utils.addEvt(utils.$(".clearfix li span"),utils.touch,function(event){
		if(navigator.vibrate)navigator.vibrate(50);
		 var element=event.srcElement?event.srcElement:event.target; //span
		 var value=element.innerHTML;  //选号
		 var target=element.parentNode;   //li元素
		 var ul_list=target.parentNode;    //ul节点
		  switch(ul_list.id){
			case "redDan":
			  if(utils.hasCls(target,"active")){ 
				  utils.removeCls(target,"active");
				  utils.lottery.red.dan.splice(utils.lottery.red.dan.indexOf(value),1);
				  }else{
				  if(utils.lottery.red.dan.length>=5){alert("前区号码请选择1-5个！");return false;}
				  var relative=utils.$("#redTuo").children[utils.index(target)];   //li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.red.tuo.splice(utils.lottery.red.tuo.indexOf(value),1);
					  }
				  utils.addCls(target,"active"); 
				  utils.lottery.red.dan.push(value); 
					  }
			 break;
			 case "redTuo":
			  if(utils.hasCls(target,"active")){
				  utils.removeCls(target,"active");
				  utils.lottery.red.tuo.splice(utils.lottery.red.tuo.indexOf(value),1);
				  }else{
				  var relative=utils.$("#redDan").children[utils.index(target)];   //li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.red.dan.splice(utils.lottery.red.dan.indexOf(value),1);
					  }
				  utils.addCls(target,"active"); 
				  utils.lottery.red.tuo.push(value); 
					  }
			 break;
			 case "blueDT":
			   if(utils.hasCls(target,"active")){
				   utils.removeCls(target,"active");
				   if(utils.lottery.blue.length>0){
				     utils.lottery.blue.splice(utils.lottery.blue.indexOf(value),1);
			      } }else{
					utils.addCls(target,"active"); 
					  utils.lottery.blue.push(value);
					   }
			 
			 break;
			  default: break;
			  }
	       var plan=utils.calZhu(utils.lottery);
		   if(plan>=0){
			     utils.lottery.zhu=plan;
				 utils.$(".betNum")[0].innerHTML=plan;
				 utils.$(".betMoney")[0].innerHTML=2*plan;
				 } 
		})
	}