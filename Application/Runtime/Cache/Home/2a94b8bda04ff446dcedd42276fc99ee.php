<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>玩法期及开派奖管理系统</title>
<link href="/assets/admin/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/assets/admin/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/assets/admin/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="/assets/admin/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<!--[if IE]>
<link href="/assets/admin/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->

<!--[if lte IE 9]>
<script src="/assets/admin/js/speedup.js" type="text/javascript"></script>
<![endif]-->

<script src="/assets/admin/js/jquery-1.7.2.js" type="text/javascript"></script>
<script src="/assets/admin/js/jquery.cookie.js" type="text/javascript"></script>
<script src="/assets/admin/js/jquery.validate.js" type="text/javascript"></script>
<script src="/assets/admin/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="/assets/admin/xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
<script src="/assets/admin/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script src="/assets/admin/uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>

<!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
<script type="text/javascript" src="/assets/admin/chart/raphael.js"></script>
<script type="text/javascript" src="/assets/admin/chart/g.raphael.js"></script>
<script type="text/javascript" src="/assets/admin/chart/g.bar.js"></script>
<script type="text/javascript" src="/assets/admin/chart/g.line.js"></script>
<script type="text/javascript" src="/assets/admin/chart/g.pie.js"></script>
<script type="text/javascript" src="/assets/admin/chart/g.dot.js"></script>

<script src="/assets/admin/js/dwz.core.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.util.date.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.validate.method.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.barDrag.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.drag.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.tree.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.accordion.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.ui.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.theme.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.switchEnv.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.alertMsg.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.contextmenu.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.navTab.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.tab.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.resize.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.dialog.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.dialogDrag.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.sortDrag.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.cssTable.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.stable.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.taskBar.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.ajax.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.pagination.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.database.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.datepicker.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.effects.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.panel.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.checkbox.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.history.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.combox.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.print.js" type="text/javascript"></script>
<script src="/assets/admin/js/dwz.regional.zh.js" type="text/javascript"></script>

<script type="text/javascript">
$(function(){
	DWZ.init("/assets/admin/dwz.frag.xml", {
		loginUrl:"login_dialog.html", loginTitle:"登录",	// 弹出登录对话框
//		loginUrl:"login.html",	// 跳到登录页面
		statusCode:{ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
		keys: {statusCode:"statusCode", message:"message"}, //【可选】
		ui:{hideMode:'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"/assets/admin/themes"}); // themeBase 相对于index页面的主题base路径
		}
	});
});

</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<!--<a class="logo" href="">logo</a>-->
				<ul class="nav">
					<li><a href="/index.php/Home/Index/logout">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList" style="display:none">
					<li theme="azure"><div  class="selected">天蓝</div></li>
				</ul>
			</div>

			<!-- navMenu -->
			
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

				<div class="accordion" fillSpace="sidebar">
					<div class="accordionHeader">
						<h2><span>Folder</span>平台管理中心</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<!--<li><a href="javascript:void(0)">赛程管理</a>
								<ul>
									<li><a href="/index.php/Home/Saicheng/jingcai" target="navTab" rel="sc" title="竞彩足球">竞彩足球</a></li>
									<li><a href="/index.php/Home/Saicheng/lancai" target="navTab" rel="sc" title="竞彩篮球">竞彩篮球</a></li>
									<li><a href="/index.php/Home/Saicheng/zucai" target="navTab" rel="sc" title="足彩对阵">足彩对阵</a></li>
									<li><a href="/index.php/Home/Saicheng/grabzc" target="navTab" rel="sc" title="系统录入足彩对阵">系统录入足彩对阵</a></li>
								</ul>
							</li>
							<li><a href="javascript:void(0)">开期管理</a>
								<ul>
									<li><a href="/index.php/Home/Issue/index" target="navTab" rel="insue">玩法期管理</a></li>
									<li><a href="/index.php/Home/Issue/setissue" target="navTab" rel="insue">玩法销售状态管理</a></li>
									<li><a href="/index.php/Home/Issue/zcissue" target="navTab" rel="insue">传统足彩期管理</a></li>
								</ul>
							</li>
							-->
							<li><a href="javascript:void(0)">赛果管理</a>
								<ul>
									<li><a href="/index.php/Home/Result/jingcai" target="navTab" rel="result">竞彩足球</a></li>
									<li><a href="/index.php/Home/Result/lancai" target="navTab" rel="result">竞彩篮球</a></li>
								</ul>
							</li>
							<!--
							<li><a href="javascript:void(0)" >开奖管理</a>
								<ul>
									<li><a href="/index.php/Home/Kaijiang" target="navTab" rel="kaijiang">今日开奖</a></li>
									<li><a href="/index.php/Home/Kaijiang/supplycode" target="navTab" rel="supply">补充开奖</a></li>
								</ul>
							</li>-->
						</ul>
					</div>
				</div>
				
				
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">系统主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight tabsRightDisabled">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">系统主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							<div class="alertInfo">
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>

	</div>

	<div id="footer">Copyright &copy; 2010 <a href="" target="dialog">华阳彩票</a></div>
</body>
</html>