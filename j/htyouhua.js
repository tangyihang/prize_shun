/**
* 竞彩奖金优化
*/
$(function () {
   //alert(112);
   optimize.init();//初始化函数
  });
var optimize = new Object();

//初始化函数
    optimize.init = function(){
	//初始化变量
	//alert(122);
	optimize.TotalMoney=$("#TotalMoney").val();//购买金额
	optimize.LotteryId=$("#LotteryId").val();//彩种ID
	optimize.MinHit=$("#MinHit").val();//最小奖金
	optimize.MaxHit=$("#MaxHit").val();//最高奖金
	optimize.MultiNum=$("#MultiNum").val();//投注倍数
	//alert(optimize.LotteryId);
	optimize.tempzhushu = [];
	optimize.getdanMoney = '';
	optimize.getdanZhu='';
    optimize.tdmouseover(); //鼠标滑过变红
    optimize.changebeisu();//点击加减号增加或者减去倍数
	optimize.changePosition();//定位右侧的球队信息
	optimize.changeButtonPosition();//定位优化方式
    optimize.changedibuPosition();//定位优化方式
	optimize.pingjunyouhua();//绑定平均优化按钮
	optimize.pingjuninputbur();
	optimize.dibuposition('jjTjObj','yhBetDiv','yhBet');
	optimize.getZhuMoney();//获得一倍时的钱数
	optimize.changeall(); //改变投注金额和最大值最小值
	optimize.beitou();//倍投
	optimize.fillmulriple();//绑定填写倍数的按钮
    optimize.fillmoney();//绑定自己填写金额
	optimize.boreyouhua();//博热优化
	optimize.bolengyouhua();//博冷优化
	optimize.submitfangan();//优化后提交
	optimize.changeRed();//变红
    optimize.lcsubmitfangan();//篮彩提交按钮
    //alert(optimize);
}

//鼠标滑过单元格的时候
optimize.tdmouseover = function()
{
  $("#jjTjObj tr").hover(function () {
	   $(this).addClass("trhover");
	   optimize.houveradd(this);
	   },function () {
		  $(this).removeClass("trhover");
		  optimize.houverdel(this);
		  });
}
//添加左边球队标红
optimize.houveradd = function(obj){ 
    var B = $(obj).attr("data-teamval");
	var P = '';
    if(B != undefined)
	{
	           var C = B.split(';');
		        for(var i = 0;i<C.length;i++){
				 $("#betTjObj").find('.pclearfix span').each(function(){
			         P = $(this).attr("data-val");
                     if(P == C[i])
					 {  
					    $(this).addClass("widthxuanxbg");
					 }
			    });
		      }
		       
	
	}	
		   
}
//删除球队信息标红
optimize.houverdel = function(obj){ 
  var B = $(obj).attr("data-teamval");
	var P = '';
    if(B != undefined)
	{
	           var C = B.split(';');
		        for(var i = 0;i<C.length;i++){
				 $("#betTjObj").find('.pclearfix span').each(function(){
			         P = $(this).attr("data-val");

					 if(P == C[i])
					 {  
					    $(this).removeClass("widthxuanxbg");
					 }
			    });
		      }
		       
	
	}	
}
//按照加减号改变倍数
optimize.changebeisu = function(){
   $("#jjTjObj tr").find(".noteBox a").each(function(e){
        $(this).unbind();
		$(this).bind('click',function(){
			//alert(1111);

	     var type = $(this).attr("data-type");
		 var C = $(this).siblings("input");//获取当前的元素的INPUE的值
		 var parenttr = $(this).parent().parent().parent().parent().parent();//每一注的奖金
		 var beishu = $(this).parent().parent().parent().parent().siblings(".noteBetObj");//行中显示的倍数
		 var SJ =  $(this).parent().siblings("dd").find('input');
		 console.info(SJ.val());
		 if(type == -1){
           if(parenttr.attr("data-betval") == 0 || beishu.html() == 0){
		     alert("您调节的奖金不能小于0");
		    }else{
			parenttr.attr('data-betval',parseInt(beishu.html())-1);//更改TR的倍数
			beishu.html(parseInt(beishu.html())-1);
            var money = parseFloat(SJ.val()) - parseFloat(parenttr.attr('data-noteval'));
			parenttr.attr('data-totalval',money);//更改TR的总金额
			if(money > optimize.TotalMoney){
			    SJ.addClass('font_red');
			 }else if(money < optimize.TotalMoney){
			    SJ.removeClass('font_red');
			  }
            if(money == '0')
				{
			      SJ.val('0.00');
				  C.val('0.00');
			    }else
				{
				  SJ.val(money.toFixed(2));
				  C.val(money.toFixed(2));
				}
        
			 optimize.changeall();
		    }
			
		  }else if(type == 1){
			  //验证倍数201412.26 lds
			if(parenttr.attr("data-betval") >= 999999){
		     alert("您调节的倍数不能大于999999");
		    }else{
			parenttr.attr('data-betval',parseInt(beishu.html())+1);//更改TR的倍数
		    beishu.html(parseInt(beishu.html())+1);
            var money = parseFloat(SJ.val()) + parseFloat(parenttr.attr('data-noteval'));
            parenttr.attr('data-totalval',money.toFixed(2));//更改TR的总金额
            C.val(money.toFixed(2));
			if(money > optimize.TotalMoney){
			    SJ.addClass('font_red');
			 }else if(money < optimize.TotalMoney){
			    SJ.removeClass('font_red');
			  }
		    SJ.val(money.toFixed(2));
            optimize.changeall();
			optimize.changeRed();
			}

		   }
	  });
	    
	  
    });       
}
//左边的球队DIV定位
optimize.changePosition = function(){
   /*var top = $("#betDivObj").offset().top;
   $(window).scroll(function(){
	  var scrolla=$(window).scrollTop();
      var cha=parseInt(scrolla)-parseInt(top);
    if(cha>0)
      { 
		 $("#betDivObj").addClass("xuanxbox");
         
      }
   if(cha<=0)
     {
          $("#betDivObj").removeClass("xuanxbox");
	}
  });
*/
}

//优化按钮定位
optimize.changeButtonPosition = function(){
   /*var top = $("#qhyhObj").offset().top;
   $(window).scroll(function(){
	  var scrolla=$(window).scrollTop();
      var cha=parseInt(scrolla)-parseInt(top);
    if(cha>0)
      { 
		 $("#qhyhObj").addClass("yhmenuDiv");
         
      }
   if(cha<=0)
     {
          $("#qhyhObj").removeClass("yhmenuDiv");
	}
  });*/
}

optimize.changedibuPosition = function(){//底部导航栏

}

//绑定平均优化按钮
optimize.pingjunyouhua = function(){
$("#pjyh").unbind();
$("#pjyh").click(function(){
var K = $("#updataTotalMoney").val();
if(parseInt(K) <= parseInt(optimize.getdanMoney))// 小于一倍的时候的提示不能小于一倍
	{
	$("#betTipsObj").html('<span>!</span> 请输入1倍以上方案金额进行优化。');
    $("#betTipsObj").show();
    }else if( K%2 != 0)
	{
	 $("#betTipsObj").html('<span>!</span>请输入大于0的偶数金额再进行优化！');
	 $("#betTipsObj").show(); 
	}else if(K > 100000)
	{
	  $("#betTipsObj").html('<span>!</span> 计划购买金额请不要超过10万元！');
	  $("#betTipsObj").show(); 
	}else if(K.length == 0){
	  $("#betTipsObj").html('<span>!</span>请输入金额进行优化！');
      $("#betTipsObj").show(); 
	}else{
	  $("#betTipsObj").hide();
	  $("#TotalMoney").val(K);
	  $("#submit_act").val('pingjun');
	  $("#submit_appnum").val(parseInt(K)/2);
	  $("#myform").submit();
	}
  
});
}
//绑定键盘抬起的时候格式输入金额
optimize.pingjuninputbur = function(){
   $("#updataTotalMoney").unbind();
   $("#updataTotalMoney").keyup(function(){
   var K = $("#updataTotalMoney").val();//获取用户输入的信息
   var D = parseInt(K);
   if(K == ''){
   $("#jjTjObj tr").each(function(){
     var temspvalue = "";//得到当组的SP值
	 var bsValObj = 0;
     var B = '';//得到总共的钱数
     var datatotalval = $(this).attr("data-totalval");
     if(datatotalval){
       $(this).attr("data-betval",bsValObj);
       temspvalue =  $(this).attr("data-noteval");
	   B = parseFloat(bsValObj)*temspvalue;
	   $(this).attr("data-totalval", B.toFixed(2));
	   $(this).find(".noteBetObj").html(bsValObj);
	   $(this).find(".noteBox span").html(B.toFixed(2));
	   $(this).find(".noteBox input").attr('value',B.toFixed(2));
     }
 });
 optimize.changeall();
 return false;
  }
   if(K != parseInt(K).toString()){$("#updataTotalMoney").val(optimize.TotalMoney);}
 
   });
}

//底部导航
optimize.dibuposition = function (tableId,showClass, className){
   //var footer=$("."+footerClass);
   //var shower=$("."+showClass);
   //var shower_high=$("."+showClass).height();
   /*
    var tabler=$("#"+tableId);
    var shower=$("."+showClass);
    var tabler_high=tabler.height();
   function bind_div(){
   	       var scroll_top=$(document).scrollTop();
   	       var client_height=document.documentElement.clientHeight;
   	       var tabler_height=tabler.offset().top;
   	       if(scroll_top+client_height<tabler_high+tabler_height){
   	       	 if(shower.hasClass(className))return false;
       		 shower.addClass(className);
   	       }else{
   	       	 if(!shower.hasClass(className))return false;
       		  shower.removeClass(className);
   	       }
}
   bind_div();
  $(window).scroll(function(){
  	    bind_div();
  	});
  $(window).resize(function(){
  		bind_div();
  	});
*/
}


optimize.getZhuMoney = function(){//计算总共有几张票和花的钱数
var tempnun = 0;
 $("#jjTjObj tr").each(function(){
  var datatotalval = $(this).attr("data-totalval");
  if(datatotalval)
	 {
      tempnun++; 
     }
 
 });
optimize.getdanMoney = parseInt(tempnun)*2;
optimize.getdanZhu = tempnun;
}
//倍投开始
optimize.beitou = function(){
$("#btoff").unbind();
$("#btoff").click(function(){
$("#bsValObj").val("1");
$("#btObj").show();
     $("#btObj .noteBox").find("a").each(function(){
	     var type = $(this).attr("data-type");
		 if(type == -1 ){
		 $(this).unbind();
		 $(this).click(function(){
		    optimize.jianyi();
		    });
		  }else if(type == 1){
		 $(this).unbind();
		  $(this).click(function(){
               optimize.jiayi();
		    });
		   }
	   });
  });
}

//倍投减法开始
optimize.jianyi = function(){
   var bsValObj = $("#bsValObj").val();
   if(bsValObj == 1)
	{
      return false;
    }else{
	 $("#bsValObj").val(parseInt(bsValObj)-1);
	 $("#beisValObj").val(parseInt(bsValObj)-1);
	 optimize.tableCalculatejian();
	}
}


//倍投加法开始
optimize.jiayi = function(){
  var bsValObj = $("#bsValObj").val();
   $("#bsValObj").val(parseInt(bsValObj)+1);
   $("#beisValObj").val(parseInt(bsValObj)+1);
   optimize.tableCalculatejia();
}


//计算所有单元格里面的值加法

optimize.tableCalculatejia = function(){
var bsValObj = $("#bsValObj").val(); //得到倍数
if(bsValObj.length == '0')
	{
     bsValObj = 0;
    }
 $("#jjTjObj tr").each(function(){
 var temspvalue = "";//得到当组的SP值
 var B = '';//得到总共的钱数
   var databetval = $(this).attr("data-betval");//得到倍数
   var datanoteval = $(this).attr("data-noteval");
   if(databetval){
       
	   var beinumtemp  = parseInt(databetval)/(parseInt(bsValObj)-1);
	   var beishu = parseInt(beinumtemp)*parseInt(bsValObj);
	   var beitounum = parseFloat(datanoteval)*parseInt(beishu);
	   $(this).attr("data-betval",beishu);
	   $(this).attr("data-totalval",beitounum.toFixed(2));
	   $(this).find(".noteBetObj").html(beishu);
	   $(this).find(".noteBox span").html(beitounum.toFixed(2));
	   $(this).find(".noteBox input").attr('value',beitounum.toFixed(2));
     }
 });
optimize.changeall();
optimize.changeRed();
}

//计算所有单元格里面的值减法
optimize.tableCalculatejian = function(){

var bsValObj = $("#bsValObj").val(); //得到倍数
if(bsValObj.length == '0')
	{
     bsValObj = 0;
    }
 $("#jjTjObj tr").each(function(){
   var temspvalue = "";//得到当组的SP值
   var B = '';//得到总共的钱数
   var databetval = $(this).attr("data-betval");//得到倍数
   var datanoteval = $(this).attr("data-noteval");
   if(databetval){
       
	   var beinumtemp  = parseInt(databetval)/(parseInt(bsValObj)+1);
	   var beishu = parseInt(beinumtemp)*parseInt(bsValObj);
	   var beitounum = parseFloat(datanoteval)*parseInt(beishu);
	   if(bsValObj.length != '0')
	   {
	      $(this).attr("data-betval",beishu);
          $(this).attr("data-totalval",beitounum.toFixed(2));
	   }
	   $(this).find(".noteBetObj").html(beishu);
	   $(this).find(".noteBox span").html(beitounum.toFixed(2));
	   $(this).find(".noteBox input").attr('value',beitounum.toFixed(2));
     }
 });
optimize.changeall();
optimize.changeRed();

}
//自己填写倍数bsValObj
optimize.fillmulriple = function(){
 $("#bsValObj").keyup(function(){var arg=/^[1-9][0-9]*$/;if(this.value!=""&&!arg.test(this.value)){this.value=1;}
if(this.value>100000)this.value=100000;optimize.filltableCalculate(this.value);}).blur(function(){if(this.value==""){this.value=1;optimize.filltableCalculate(this.value);}}).focus(function(){$(this).select();}); 
       

}
 optimize.filltableCalculate = function(d){
var bsValObj = '';
if(d.length == ''){bsValObj=0;}else{bsValObj=d;}
 var beisValObj = $("#beisValObj").val(); //得到隐藏的倍数
 if(bsValObj == '0')
	 {
         $("#moneyObj").html('1222');
	     $("#jjObj span").each(function(){
		  if($(this).attr('class') == 'font_red')
			{
			   $(this).html('0.00');
			}else{
			   $(this).html('0.00');
			}
	    });
 
     }
//计算金额

$("#jjTjObj tr").each(function(){
   var temspvalue = "";//得到当组的SP值
   var B = '';//得到总共的钱数
   var databetval = $(this).attr("data-betval");//得到倍数
   var datanoteval = $(this).attr("data-noteval");
   if(databetval){
       var beinumtemp  = parseInt(databetval)/(parseInt(beisValObj));
	   var beishu = parseInt(beinumtemp)*parseInt(bsValObj);
	   var beitounum = parseFloat(datanoteval)*parseInt(beishu);
	   $(this).find(".noteBetObj").html(beishu);
	   $(this).find(".noteBox span").html(beitounum.toFixed(2));
	   $(this).find(".noteBox input").attr('value',beitounum.toFixed(2));
      if(bsValObj != '0')
	    { 
          $(this).attr("data-betval",beishu);
          $(this).attr("data-totalval",beitounum.toFixed(2));
		  $("#beisValObj").val(bsValObj);
		  optimize.changeall();
          optimize.changeRed();
        }

     }
 });

   
 }


//绑定自己填写金额的时候
optimize.fillmoney = function(){
 $("#jjTjObj tr").each(function(){
  var datatotalval = $(this).attr("data-totalval");
  var spvalue = $(this).attr("data-noteval");
  var thisOldValObj = $(this).find(".noteValObj");//获取显示金额的SAP标签
  var beishu = $(this).find(".noteBetObj");
  var spanvalue = $(this).find(".noteBox span");
  var obj = $(this);
  thisOldValObj.css("cursor", "pointer").click(function() {
             if (!$("#updataTotalMoney").hasClass("gray9")) {$("#updataTotalMoney").addClass("gray9")}
			 var thisObj = $(this);
			 thisObj.hide();
			 var thisInputObj = thisObj.next();
			 var thisVal = thisObj.html();//获取原来的的值
			 thisInputObj.show();
			 thisInputObj.focus();
             thisInputObj.select();
			 thisInputObj.blur(function(){
				var newsVal = this.value;
				var parNewsVal = Number(this.value);
              if(Number(this.value) == Number(thisVal) || isNaN(parNewsVal) || parNewsVal.length == 0) {
                    thisInputObj.hide();
                    thisInputObj.prev().show();
					this.value = thisVal;
                    thisInputObj.unbind("blur");
                    return false;
                }else{
                   var betNum = Math.round(parNewsVal/spvalue);//获取的注数值
			       betNum < 0 ? betNum = 0 : betNum = betNum;
				   var newThisVal = betNum * spvalue;
				   obj.attr("data-betval",betNum);
				   obj.attr("data-totalval",newThisVal.toFixed(2));
                   beishu.html(betNum);
                   spanvalue.html(newThisVal.toFixed(2));
				   this.value = newThisVal.toFixed(2);
				   optimize.changeall();
                   optimize.changeRed();
				 }
            });

   
  });
 
 });
}

/*博热优化*/
optimize.boreyouhua = function (){
$("#bryh").unbind(); //childtype
var childtype = '';
childtype = $("#yuhua_gate").val();
var num = childtype.indexOf('^');
$("#bryh").click(function(){
   if(num >1)
	 {  
	     alert('博热优化暂不支持复合过关！');
        return false;
     }else
	 {
	       var K = $("#updataTotalMoney").val();
		 if(parseInt(K) <= parseInt(optimize.getdanMoney))// 小于一倍的时候的提示不能小于一倍
			{
			$("#betTipsObj").html('<span>!</span> 请输入1倍以上方案金额进行优化。');
			$("#betTipsObj").show();
			}else if( K%2 != 0)
			{
			 $("#betTipsObj").html('<span>!</span>请输入大于0的偶数金额再进行优化！');
			 $("#betTipsObj").show(); 
			}else if(K > 100000)
			{
			  $("#betTipsObj").html('<span>!</span> 计划购买金额请不要超过10万元！');
			  $("#betTipsObj").show(); 
			}else if(K.length == 0){
	          $("#betTipsObj").html('<span>!</span> 请输入金额进行优化！');
              $("#betTipsObj").show(); 
	         }else{
			  $("#betTipsObj").hide();
			  $("#TotalMoney").val(K);
			  $("#submit_act").val('bore');
			  $("#submit_appnum").val(parseInt(K)/2);
			  $("#myform").submit();
			}  
	 }
  });
  if(num >1){
      $("#bryh").hover(function () {
	     $("#boreboleng").html("博热优化暂不支持复合过关！");$("#zhggTips").css('display','block');$("#showkuo").css("left","50px");},function () {$("#zhggTips").css('display','none');});

   }
 }
/*搏冷优化*/
optimize.bolengyouhua = function(){
$("#blyh").unbind(); //childtype
var childtype = '';
childtype = $("#yuhua_gate").val();
var num = childtype.indexOf('^');
$("#blyh").click(function(){
   if(num >1)
	 {  
	    alert('博冷优化暂不支持复合过关！');
        return false;
     }else
	 {
	     var K = $("#updataTotalMoney").val();
		 if(parseInt(K) <= parseInt(optimize.getdanMoney))// 小于一倍的时候的提示不能小于一倍
			{
			$("#betTipsObj").html('<span>!</span> 请输入1倍以上方案金额进行优化。');
			$("#betTipsObj").show();
			}else if( K%2 != 0)
			{
			 $("#betTipsObj").html('<span>!</span>请输入大于0的偶数金额再进行优化！');
			 $("#betTipsObj").show(); 
			}else if(K > 100000)
			{
			  $("#betTipsObj").html('<span>!</span>计划购买金额请不要超过10万元！');
			  $("#betTipsObj").show(); 
			}else if(K.length == 0){
	          $("#betTipsObj").html('<span>!</span>请输入金额进行优化！');
              $("#betTipsObj").show(); 
	        }else{
			  $("#betTipsObj").hide();
			  $("#TotalMoney").val(K);
			  $("#submit_act").val('boleng');
			  $("#submit_appnum").val(parseInt(K)/2);
			  $("#myform").submit();
			}
	 }
  });
    if(num >1){
	      $("#blyh").hover(function () {
	 $("#boreboleng").html("博冷优化暂不支持复合过关！");
	 $("#showkuo").css("left","145px");
	 $("#zhggTips").css('display','block');},function () {$("#zhggTips").css('display','none');});
	}

}


/*提交优化的方案*betSubObj*/
optimize.submitfangan = function(){
$("#betSubObj").unbind();
$("#betSubObj").click(function(){
	    var B='';
	    var M='';
	    M=optimize.totalBetCount();//总共的注数
		if(M == '0'){alert('您修改了计划购买金额，请点击优化后再提交投注！');return false;}
		B =parseInt(M)*2;
        var message = {},verrorcode=-1;
		message.header = {},message.body = {},message.body.elements = {},message.body.elements.element={};
		message.header.transactiontype = '';
		message.body.elements.element.agentserialid = easy_Power.getTicketId();
		message.body.elements.element.lotteryid = 208;
		message.body.elements.element.issue = 20000;
		message.body.elements.element.lotteryvalue = B * 100;
		message.body.elements.element.schchildtype = $("#yuhua_gate").val();//过关方式
		message.body.elements.element.schdetail = $("#yuhua_lotterycode").val();// 投注信息
		message.body.elements.element.multiple = optimize.get_afteryouhuacode();
		message.body.elements.element.ticketinfo = {};
		message.body.elements.element.ticketinfo.ticket = [];
		
		var oneTicket = {};

		oneTicket.childtype = $("#yuhua_gate").val();//过关方式
		oneTicket.saletype = 0;
		oneTicket.lotterycode = $("#yuhua_lotterycode").val();// 投注信息
		oneTicket.appnumbers = 1;
		oneTicket.lotterynumber = optimize.totalBetCount();//总共的注数
		oneTicket.agentticketid = optimize.get_planId();
		oneTicket.ticketorder = optimize.get_planId();
		oneTicket.betmode = 0;
		oneTicket.isfile = 0;
		oneTicket.ticketmode = '';

		message.body.elements.element.ticketinfo.ticket.push(oneTicket);

		message.body.elements.element.issueflag = 0;
		message.body.elements.element.bonusstop = 1;
		message.body.elements.element.stopvalue = 0;
		message.body.elements.element.lotterymode = 4;
		message.body.elements.element.issueslist = 20000;
		message.body.elements.element.issuesnumbers = '1';
		message.body.elements.element.betlotterymode = optimize.get_minMaxMatch($("#yuhua_lotterycode").val());
		
		$.ajax({
			type: "POST",
			url: easy_lotterygodjlzurl,
			data: message,
			dataType: "json",
			async: false,// 使用同步
			success: function (data, status) {
			if (status === 'success') {
				if (data.errorcode === '0') {
					      verrorcode = 0;
								$('#submit_lotteryid').val(208);
								$('#submit_lotteryvalue').val(parseInt(M) * 2);
								$('#submit_appnum').val('1');
								$('#submit_lotteryissue').val(20000);
								$('#submit_issueflag').val(0);
								$('#submit_cachefile').val(data.detail.filename);
				} else if (data.errorcode == '14005') {
				    $.MessageBox.ShowOK({ "text": "对不起，订单提交失败(一票不能超过20000元)！" });
				} else {
				    $.MessageBox.ShowOK({ "text": "格式验证失败！" });
				}
			}
			},
			error: function (a, b) {
			alert('errormsg:' + a + b);
			}
		});
				
		if (verrorcode == 0) {
		$('#submit_lotteryvalue').val(B);
		$('#submit_appnum').val(1);
		$("#jq_form_dg").attr('target', '_blank');
    $("#jq_form_dg").attr("action", "/lottery/index.php?act=sechemset_djlz");
	$("#jq_form_dg").submit();
    $("#jq_buy_dg").attr("disabled", false);
    $("#jq_buy_fqhm").attr("disabled", false);
		}    


	   
});

}

/*篮彩提交优化的方案*betSubObj*/
optimize.lcsubmitfangan = function(){
$("#lcbetSubObj").unbind();
$("#lcbetSubObj").click(function(){
	    var B='';
	    var M='';
	    M=optimize.totalBetCount();//总共的注数
		if(M == '0'){alert('您修改了计划购买金额，请点击优化后再提交投注！');return false;}
		B =parseInt(M)*2;
        var message = {},verrorcode=-1;
		message.header = {},message.body = {},message.body.elements = {},message.body.elements.element={};
		message.header.transactiontype = '';
		message.body.elements.element.agentserialid = easy_Power.getTicketId();
		message.body.elements.element.lotteryid = optimize.LotteryId;
		message.body.elements.element.issue = 20000;
		message.body.elements.element.lotteryvalue = B * 100;
		message.body.elements.element.schchildtype = $("#yuhua_gate").val();//过关方式
		message.body.elements.element.schdetail = $("#yuhua_lotterycode").val();// 投注信息
		message.body.elements.element.lmultiple = optimize.get_afteryouhuacode();
		message.body.elements.element.ticketinfo = {};
		message.body.elements.element.ticketinfo.ticket = [];
		
		var oneTicket = {};

		oneTicket.childtype = $("#yuhua_gate").val();//过关方式
		oneTicket.saletype = 0;
		oneTicket.lotterycode = $("#yuhua_lotterycode").val();// 投注信息
		oneTicket.appnumbers = 1;
		oneTicket.lotterynumber = 1;//总共的注数
		oneTicket.agentticketid = optimize.get_planId();
		oneTicket.ticketorder = optimize.get_planId();
		oneTicket.betmode = 0;
		oneTicket.isfile = 0;
		oneTicket.ticketmode = '';

		message.body.elements.element.ticketinfo.ticket.push(oneTicket);

		message.body.elements.element.issueflag = 0;
		message.body.elements.element.bonusstop = 1;
		message.body.elements.element.stopvalue = 0;
		message.body.elements.element.lotterymode = 4;
		message.body.elements.element.issueslist = 20000;
		message.body.elements.element.issuesnumbers = 1;
		message.body.elements.element.betlotterymode = optimize.get_minMaxMatch($("#yuhua_lotterycode").val());
		
		$.ajax({
			type: "POST",
			url: easy_lotterygodjlzurl,
			data: message,
			dataType: "json",
			async: false,// 使用同步
			success: function (data, status) {
			if (status === 'success') {
				if (data.errorcode === '0') {
					      verrorcode = 0;
								$('#submit_lotteryid').val(optimize.LotteryId);
								$('#submit_lotteryvalue').val(parseInt(M) * 2);
								$('#submit_appnum').val('1');
								$('#submit_lotteryissue').val(20000);
								$('#submit_issueflag').val(0);
								$('#submit_cachefile').val(data.detail.filename);
				} else if (data.errorcode == '14005') {
				    $.MessageBox.ShowOK({ "text": "对不起，订单提交失败(一票不能超过20000元)！" });
				} else {
				    $.MessageBox.ShowOK({ "text": "格式验证失败！" });
				}
			}
			},
			error: function (a, b) {
			alert('errormsg:' + a + b);
			}
		});
				
		if (verrorcode == 0) {
		$('#submit_lotteryvalue').val(B);
		$('#submit_appnum').val(1);
		$("#jq_form_dg").attr('target', '_blank');
       $("#jq_form_dg").attr("action", "/lottery/index.php?act=sechemset_djlz");
	  $("#jq_form_dg").submit();
      $("#jq_buy_dg").attr("disabled", false);
      $("#jq_buy_fqhm").attr("disabled", false);
		}    


	   
});

}

//得到投注信息
optimize.get_planId =  function () {
                return (new Date()).getTime() + parseInt(Math.random() * 100000);
            }
//得到投注的注数
optimize.totalBetCount = function(){
 var Z= parseInt(parseInt(optimize.TotalMoney)/2);
 var ZJ = 0;
  $("#jjTjObj tr").each(function(){
     var T = 0;
	 T = $(this).attr('data-betval');
     if(T != undefined)
	  {
	   ZJ = ZJ+parseInt(T);
	  }
  });

  return ZJ;
}

optimize.get_minMaxMatch = function(matchStr) {
      if(optimize.LotteryId == '218'){
	      var reg = new RegExp(/\d{8}\-\d{3}/g);
	  }else if(optimize.LotteryId == '208'){
	     var reg = new RegExp(/\d{6}\-\d{3}/g);
	  }
       var retStr = '';
       if (typeof matchStr == 'string') {
        var retArr = matchStr.match(reg);
          if (retArr.length != null && retArr.length > 0) {
              retArr.sort();
               retStr = retArr[retArr.length - 1] + '^' + retArr[0];
                   }
              }
         return retStr;
}
/*格式化投注信息*/
optimize.get_afteryouhuacode = function(){
  var U = '';//连接起来的投注信息
  $("#jjTjObj tr").each(function(){
     var T = 0; //注数
	 var P = [];
	 var H = '';
	 var temp = '';
	 var N = '';//单注投注
	 var W = '';
	 T = $(this).attr('data-betval');
     if(T != undefined)
	  {
	      N = $(this).attr('data-teamval');
		   var M = N.replaceAll('-','');
		   var MM = M.replaceAll(';',':');
		   N = MM+'*'+T
       } 
     if(N != '' && N != undefined)
	  {
	     U =U+N+';';
	  }
  });
   return U;
}

//更改投注的总金额和总大小中奖
optimize.changeall = function(){
  var D=$("#jjTjObj tr");
  var Z = [];//统计总共的注数
  var M=[]; //统计有多少钱
  var P = 0;
  var S = 0;
  D.each(function(e){
	F = $(this).attr('data-betval');//得到每一注的倍数
	G = $(this).attr('data-totalval');
	if(F != undefined){Z[Z.length]=F;}
	if(G != undefined){M[M.length]=G;}
  });
  for(var i=0;i<Z.length;i++)//计算总共的有多少注
	{
       P = parseInt(P) + parseInt(Z[i]);
    }
   P = P.toFixed(2);
   optimize.MultiNum=P;
   $("#MultiNum").val(P);
   optimize.TotalMoney=  P*2;
   $("#TotalMoney").val(P*2);//购买金额
   $("#moneyObj").html(P*2);
    for(var i=0;i<M.length;i++)//计算大小奖金
	{
       S = parseFloat(S) + parseFloat(M[i]);
    }
	$("#jjObj span").each(function(){
	if($(this).attr('class') == 'font_red')
		{
	       $(this).html(S.toFixed(2));
	    }else{
			 if(M.length == 1)
			  {
			   $(this).html('0.00');
			  }else{
			    var HI = Math.min.apply(null, M);
				$(this).html(HI.toFixed(2));
			  }
	
		  
		}
	});
}
//如果中奖金额大于投注金额变红
optimize.changeRed = function(){
  $("#jjTjObj .noteBox span").each(function(e){
     var V = $(this).html();
	 if(V > optimize.TotalMoney)
	  {
	    $(this).addClass('font_red');
	  }else
	  {
	    $(this).removeClass('font_red');
	  }
   });
}


String.prototype.replaceAll = function(reallyDo, replaceWith, ignoreCase) {  
    if (!RegExp.prototype.isPrototypeOf(reallyDo)) {  
        return this.replace(new RegExp(reallyDo, (ignoreCase ? "gi": "g")), replaceWith);  
    } else {  
        return this.replace(reallyDo, replaceWith);  
    }  
}  


