/*
	服务器地址配置信息。
*/
var easy_ucenterurl='http://uczh.198tc.com';
var easy_newsurl='http://bangzhu.198tc.com';

var easy_domain='http://www.198tc.com';
//入口是vip
if(window.location.host=="vip.198tc.com"){
    easy_domain='http://vip.198tc.com';
}
if(window.location.host=="fdc.198tc.com"){
    easy_domain='http://fdc.198tc.com';
}

var easy_matchcenter='http://matchcenter.198tc.com';
var easy_upload = 'http://upload.198tc.com';

var easy_ajaxurl = easy_domain+'/home/php_client/clientxml.php';
var easy_ajaxjsonurl = easy_domain+'/home/php_client/clientjson.php';
var easy_lotterygourl = easy_domain+'/lottery/lotterygo.php';
var easy_lotterygodjlzurl = easy_domain+'/lottery/lotterygo_djlz.php';

//华阳彩票代购协议
var easy_url_cpgmxy=easy_newsurl+"/a/201304/180.html";

//免费注册
var easy_url_mfzc=easy_newsurl+"/a/201304/6.html";
//购彩流程
var easy_url_gclc=easy_newsurl+"/goucai/touzhu/263520.shtml";
//中奖查询
var easy_url_zjcx=easy_newsurl+"/a/201304/17.html";
//充值方式
var easy_url_czfs=easy_newsurl+"/a/201304/14.html";
//申请提现
var easy_url_sqtx=easy_newsurl+"/a/201304/13.html";
//合买流程
var easy_url_hmlc=easy_newsurl+"/a/201304/16.html";
//奖金分配
var easy_url_jjfp=easy_newsurl+"/a/201304/18.html";
//佣金计算
var easy_url_yjjs=easy_newsurl+"/a/201304/19.html";
//什么是合买
var easy_url_smhm=easy_newsurl+"/a/201304/16.html";
//合买大厅页面 更多
var easy_url_buyroom_more=easy_newsurl+"/xgcpt/bzzx/gcbc/gm/index.shtml";
//数字彩单式上传查看标准格式样本
//var easy_url_dssc_szc=easy_newsurl+"/help/gcbc/dg/724126.shtml";
var easy_url_dssc_szc=easy_newsurl+"/a/201305/11.html";
//var easy_url_dssc_bd=easy_newsurl+"/help/gcbc/sjcp/781102.shtml";
var easy_url_dssc_bd=easy_newsurl+"/a/201305/11.html";

//玩法介绍
var easy_url_wfjs_default=easy_newsurl+"/help/ccgc/";
var easy_url_wfjs_100=easy_newsurl+"/a/201304/29.html";
var easy_url_wfjs_102=easy_newsurl+"/a/201304/31.html";
var easy_url_wfjs_103=easy_newsurl+"/a/201304/30.html";
var easy_url_wfjs_104=easy_newsurl+"/a/201304/32.html";

var easy_url_wfjs_105=easy_newsurl+"/";
var easy_url_wfjs_106=easy_newsurl+"/a/201304/45.html";
var easy_url_wfjs_107=easy_newsurl+"/";
var easy_url_wfjs_108=easy_newsurl+"/a/201304/33.html";
var easy_url_wfjs_109=easy_newsurl+"/a/201304/24.html";
var easy_url_wfjs_110=easy_newsurl+"/a/201304/26.html";
var easy_url_wfjs_111=easy_newsurl+"/a/201304/25.html";

var easy_url_wfjs_112=easy_newsurl+"/a/201304/27.html";

var easy_url_wfjs_113=easy_newsurl+"/a/201304/27.html";
var easy_url_wfjs_114=easy_newsurl+"/";
//var easy_url_wfjs_115=easy_newsurl+"/wanfa/fucai/262883.shtml";
var easy_url_wfjs_116=easy_newsurl+"/a/201304/21.html";
var easy_url_wfjs_117=easy_newsurl+"/a/201304/23.html";
var easy_url_wfjs_118=easy_newsurl+"/a/201304/22.html";

//var easy_url_wfjs_bd=easy_newsurl+"/wanfa/ticai/262849.shtml";
var easy_url_wfjs_bd=easy_newsurl+"/a/201304/44.html";
var easy_url_wfjs_bd_upload=easy_newsurl+"/wanfa/fucai/262851.shtml";

var easy_url_wfjs_208=easy_newsurl+"/a/201309/47.html";
var easy_url_wfjs_209=easy_newsurl+"/a/201304/36.html";
var easy_url_wfjs_210=easy_newsurl+"/a/201304/36.html";
var easy_url_wfjs_211=easy_newsurl+"/a/201304/37.html";
var easy_url_wfjs_212=easy_newsurl+"/a/201304/38.html";
var easy_url_wfjs_213=easy_newsurl+"/a/201304/39.html";


var easy_url_wfjs_214=easy_newsurl+"/a/201304/40.html";
var easy_url_wfjs_215=easy_newsurl+"/a/201304/43.html";
var easy_url_wfjs_216=easy_newsurl+"/a/201304/41.html";
var easy_url_wfjs_217=easy_newsurl+"/a/201304/42.html";
//分析资讯
/*北单，竞彩，篮彩*/
var easy_url_fxzx_bd=easy_newsurl+"/";
var easy_url_fxzx_jc="http://zx.198tc.com/jcyc/index.shtml";
//var easy_url_fxzx_lc=easy_newsurl+"/";
var easy_url_fxzx_lc="http://zx.198tc.com/lcyc/index.shtml";
var lotteryinfo = {"108":"足彩-胜负彩","109":"足彩-任选九","110":"足彩-六场半全场","111":"足彩-四场进球"};
//查看竞彩加奖活动
var easy_url_action_jc="http://www.198tc.com/action/activity.php?op=jcjj";
jQuery(function(){
	$("#look_action").css("color",'red');
	$('#look_action').attr("href",easy_url_action_jc);//查看加奖活动
	//$("#jc_action").hide();//隐藏文字
	
});

//网站模板skin 开关 red 为红色  blue为蓝色
/*var easy_skin='blue';
jQuery(function () {
	var css_str="";
	$('link').each(function(){
	   css_str=$(this).attr('href').replace('red','blue');
	   $(this).attr('href',css_str);	
	   
	});
	if($("#basecss")){
	$("#basecss").attr('href',"/lottery/themes/"+easy_skin+"/css/base.css");
  }
  if($("#indexcss")){
	$("#indexcss").attr('href',"/lottery/themes/"+easy_skin+"/css/index.css");
  }
})*/

//访问跟踪代码
/*var _adwq = _adwq || [];
_adwq.push(['_setAccount', 'mab77']);
_adwq.push(['_setDomainName', '.198tc.com']);
_adwq.push(['_trackPageview']);

var otherJS = 'http://d.emarbox.com/js/adw.js?adwa=mab77';//js的地址，请自定义
document.write('<scr' + 'ipt type="text/javascript" src="'+otherJS+'"></scr' + 'ipt>');
*/
