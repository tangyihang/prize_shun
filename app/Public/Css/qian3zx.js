window.onload=function(){
	 if(!!!utils.qihigh){
		      utils.qihigh=utils.getStyle(utils.$(".eight_dat")[0],"height");
		      utils.setStyle(utils.$(".eight_dat")[0],"height",0);
		      }	
	/***********************************************初始化期数高度***************/
		utils.addEvt(document,utils.touch, function(event){    //
	   switch(event.target.className){ 
			case "click_up":
					utils.setStyle(utils.$(".click_up")[0],"display","none");
					utils.setStyle(utils.$(".eight_dat")[0],"height",utils.qihigh);
		   break;
		   case "click_down":
					 utils.setStyle(utils.$(".eight_dat")[0],"height",0);
					  setTimeout(function(){
						utils.setStyle(utils.$(".click_up")[0],"display","block");
						},1000);
		   break;
		   case "click_h1":
		   if(utils.getStyle(utils.$(".choose_wey_bet")[0],"display")=="none"){
					 utils.setStyle(utils.$(".choose_wey_bet")[0],"display","block");
					 }else{
						utils.setStyle(utils.$(".choose_wey_bet")[0],"display","none")	;
			}
		   break;
		   
		   case "click_h2":
		   if(utils.getStyle(utils.$(".choose_cz")[0],"display")=="none"){
					 utils.setStyle(utils.$(".choose_cz")[0],"display","block");
					 }else{
						utils.setStyle(utils.$(".choose_cz")[0],"display","none")	;
			}
		   break;
		   case "graybtn":
			   utils.initlottery();
			break;
		  default:break;
		}
	});
  	utils.lottery={type:"qian3zx",wan:[],qian:[],bai:[],zhu:0};     //utils.lottery存放用户投注方案
	utils.initlottery=function(){                  //初始化投注内容
	 var doms=utils.$(".active");
		    for(var i=0;i<doms.length;i++)
	       utils.removeCls(doms[i],"active");
		   utils.lottery.wan=[];
		   utils.lottery.qian=[];
		   utils.lottery.bai=[];
		   utils.$(".betNum")[0].innerHTML=0;
		   utils.$(".betMoney")[0].innerHTML=0;
	       utils.lottery.zhu=0;
	};
		utils.addEvt(utils.$(".clearfix li span"),utils.touch,function(event){
		if(navigator.vibrate)navigator.vibrate(50);
		 var element=event.srcElement?event.srcElement:event.target; //span
		 var value=element.innerHTML;  //选号
		 var target=element.parentNode;   //li元素
		 var ul_list=target.parentNode;    //ul节点
		 switch(ul_list.id){
			 case "qian3Wan":
			 if(utils.hasCls(target,"active")){ 
			      utils.removeCls(target,"active");
				  utils.lottery.wan.splice(utils.lottery.wan.indexOf(value),1);
				}else{
				  var relative=utils.$("#qian3Qian").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.qian.splice(utils.lottery.qian.indexOf(value),1);
					  }
				 relative=utils.$("#qian3Bai").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.bai.splice(utils.lottery.bai.indexOf(value),1);
					  }
				  utils.addCls(target,"active"); 
				  utils.lottery.wan.push(value); 
					}
			 break;
			 case "qian3Qian":
			 	if(utils.hasCls(target,"active")){ 
			      utils.removeCls(target,"active");
				  utils.lottery.qian.splice(utils.lottery.qian.indexOf(value),1);
				}else{
				  var relative=utils.$("#qian3Wan").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.wan.splice(utils.lottery.wan.indexOf(value),1);
					  }
					  relative=utils.$("#qian3Bai").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.bai.splice(utils.lottery.bai.indexOf(value),1);
					  }
				  utils.addCls(target,"active"); 
				  utils.lottery.qian.push(value); 
					}
			 break;
			 case "qian3Bai":
			 	if(utils.hasCls(target,"active")){ 
			      utils.removeCls(target,"active");
				  utils.lottery.bai.splice(utils.lottery.bai.indexOf(value),1);
				}else{
				  var relative=utils.$("#qian3Wan").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.wan.splice(utils.lottery.wan.indexOf(value),1);
					  }
					  relative=utils.$("#qian3Qian").children[utils.index(target)];//li元素
				  if(utils.hasCls(relative,"active")){
					  utils.removeCls(relative,"active"); 
					  utils.lottery.qian.splice(utils.lottery.qian.indexOf(value),1);
					  }
				  utils.addCls(target,"active"); 
				  utils.lottery.bai.push(value); 
					}
			 break;
			 default:break;
			 }
			// console.info(utils.lottery.wan+"~~~"+utils.lottery.qian+"~~~"+utils.lottery.bai)
		var plan=utils.calZhu(utils.lottery);
		   if(plan>=0){
			     utils.lottery.zhu=plan;
				 utils.$(".betNum")[0].innerHTML=plan;
				 utils.$(".betMoney")[0].innerHTML=2*plan;
				 } 
			 
		 });
	/*****************摇一摇功能实现*******************************/
var SHAKE_THRESHOLD = 2000;
var last_update = 0;
var x;
var y;
var z;
var last_x;
var last_y;
var last_z;
utils.addEvt(window,'devicemotion',function(eventData){
	  var  acceleration = eventData.accelerationIncludingGravity;
   var curTime = new Date().getTime(); 
   var diffTime = curTime -last_update;
   if(diffTime>100){
   	last_update = curTime;
	x = acceleration.x; 
	y = acceleration.y;
	z = acceleration.z;
   var speed=Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000; 
	if(speed>SHAKE_THRESHOLD){
	/*********功能实现************************/
	if(navigator.vibrate)navigator.vibrate(500);
	var results=[];
	for(var i=0;i<5;i++){
		results.push(utils.randoms({min:1,max:11,many:3}));//随机摇号结果集
		}
	var count=0;
	var timer=setInterval(function(){
		  if(count==results.length){
			  clearInterval(timer);
			  }else{
		    	utils.initlottery();
				utils.addCls(utils.$("#qian3Wan").children[results[count][0]-1],"active");
				utils.lottery.wan.push(results[count][0]);
				utils.addCls(utils.$("#qian3Qian").children[results[count][1]-1],"active");
				utils.lottery.qian.push(results[count][1]);
				utils.addCls(utils.$("#qian3Bai").children[results[count][2]-1],"active");
				utils.lottery.bai.push(results[count][2]);
		         var plan=utils.calZhu(utils.lottery);
				 if(plan>=0){
				  utils.$(".betNum")[0].innerHTML=plan;
				  utils.$(".betMoney")[0].innerHTML=2*plan;
				}
				}
		  count++;
		},200);
	 }
	last_x=x; 
	last_y=y;
	last_z=z;
   }
});
	
	
	
	
	
	
	
}