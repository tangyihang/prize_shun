window.onload=function(){
	utils.addEvt(document,utils.touch, function(event){
		 switch(event.target.className){ 
		  case "graybtn":
	       utils.initlottery();
           break;
		   
          default:break;
		 
		 }
		})/*******************ȫ���¼�����***********************/
	utils.lottery={type:"ssqdt",red:{dan:[],tuo:[]},blue:[],zhu:0};  //��ʼ��������
	utils.initlottery=function(){                  //��ʼ��Ͷע����
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
		 var value=element.innerHTML;  //ѡ��
		 var target=element.parentNode;   //liԪ��
		 var ul_list=target.parentNode;    //ul�ڵ�
		  switch(ul_list.id){
			case "redDan":
			  if(utils.hasCls(target,"active")){ 
				  utils.removeCls(target,"active");
				  utils.lottery.red.dan.splice(utils.lottery.red.dan.indexOf(value),1);
				  }else{
				  if(utils.lottery.red.dan.length>=5){alert("ǰ��������ѡ��1-5����");return false;}
				  var relative=utils.$("#redTuo").children[utils.index(target)];   //liԪ��
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
				  var relative=utils.$("#redDan").children[utils.index(target)];   //liԪ��
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