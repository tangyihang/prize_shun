<?php /* Smarty version Smarty-3.0.7, created on 2015-12-11 09:13:33
         compiled from "/Library/WebServer/Documents/test.caipiao.com/j/htoptimize.html" */ ?>
<?php /*%%SmartyHeaderCode:199858501566a93bd799fe2-49456767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f84304fd16ab71a0c1b75d9a863612e0a53ca078' => 
    array (
      0 => '/Library/WebServer/Documents/test.caipiao.com/j/htoptimize.html',
      1 => 1449825210,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '199858501566a93bd799fe2-49456767',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta charset="utf-8" />
<title>竞彩足球奖金优化_竞彩足球奖金计算器_竞彩足球奖金测评-华阳彩票网</title>
<meta name="keywords" content="竞彩足球奖金优化,竞彩足球奖金计算器,竞彩足球奖金测评" />
<meta name="description" content="华阳彩票网竞彩足球奖金优化上线了，竞彩足球奖金优化不仅可以提高盈利，降低风险，保护本金，并且能够最大程度的避免中奖却亏本的情况发生，所以竞彩足球奖金优化是竞彩投注的利器。" />
<meta name="robots" content="all" />
<meta name="copyright" content="华阳彩票网" />
<link rel="Shortcut Icon" href="/favicon_a.ico" />

<link href="http://www.198tc.com/lottery/themes/blue/css/youhua.css" rel="stylesheet" type="text/css" />
<link href="http://www.198tc.com/lottery/themes/blue/css/base.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://www.198tc.com/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://www.198tc.com/lottery/easylib/htyouhua.js"></script>
<script src="http://www.198tc.com/lottery/easylib/easy.lottery.config.js" type="text/javascript"></script>
<script src="http://www.198tc.com/lottery/easylib/easy.lottery.core.js" type="text/javascript"></script>
<script defer type="text/javascript">
    var super_file = '{$superfile}';
    var firstappnum = '{$lottery_appnum}';
 </script>
<style>
.globalHead_cbInner {height: 16px;margin: 0 auto;padding: 5px 4px;width: 989px; background-color:#f6f6f6;border:1px solid #efefef;-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px; }
.globalHead_cbInner font {color: #DC0000;font-weight: bold;margin: 0 2px;}
.globalHead_cbInner a {color: #0068AE;}
.globalHead_cbInner span {font-family: "Arial";line-height: 16px;}
.globalHead_cbInner span.cbLeft {float: left;}
.globalHead_cbInner span.cbRight {float: right;}
</style>
                              
	</head>
<body >
<div class="" style=" width:990px; height:auto; margin:0 auto;">
<h2 class="clearfix youhtitle"><b class="float_l gray3">竞彩足球奖金优化</b></b><a class="float_r link_blue" target="_blank" href="http://zx.198tc.com/gonggao/277915.shtml">使用帮助</a></h2>
<div class="clearfix prizetablebox">
    	        <div id="betDivObj">            
			<div class="float_l prizetable prizetableMix" style=" height:400px; overflow:scroll; overflow-x:hidden">
                 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="borderyh" id="betTjObj">
                    <tr>
                        <th width="12%" scope="col">场次</th>
                        <th width="13%" scope="col">主队</th>
                        <th width="13%" scope="col">客队</th>
                        <th scope="col">投注详情</th>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('teaminfo')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
                                        <tr data-teamVal="<?php echo $_smarty_tpl->tpl_vars['item']->value['teamid'];?>
" class="betTrObj">
                        <td><?php echo $_smarty_tpl->tpl_vars['item']->value['lotttimename'];?>
</td>
                        <td class="leftborder"><span class="namespan"><?php echo $_smarty_tpl->tpl_vars['item']->value['hteam'];?>
</span></td>
                        <td class="leftborder"><span class="namespan"><?php echo $_smarty_tpl->tpl_vars['item']->value['vteam'];?>
</span></td>
                        <td class="leftborder xuanxcon">
						          <?php  $_smarty_tpl->tpl_vars['touzhi'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['item']->value['touzhiinfos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['touzhi']->key => $_smarty_tpl->tpl_vars['touzhi']->value){
?>
                                   <p class="pclearfix ">
								              <?php  $_smarty_tpl->tpl_vars['touzhiinfo'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['touzhi']->value['infovalue']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['touzhiinfo']->key => $_smarty_tpl->tpl_vars['touzhiinfo']->value){
?>
											      <?php if ($_smarty_tpl->tpl_vars['touzhiinfo']->value['lotteryid']=='210'){?>
                                                <span class="betObj float_l widthxuanx" data-val="<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['lotteryid'];?>
^<?php echo $_smarty_tpl->tpl_vars['item']->value['teamid'];?>
_<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['touzhutype'];?>
"><b><?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['touzhuval'];?>
<em class="font_green">(<?php echo $_smarty_tpl->tpl_vars['item']->value['isconcede'];?>
)</em></b>[<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['sp'];?>
]</span>
											      <?php }else{ ?>
                                                <span class="betObj float_l widthxuanx" data-val="<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['lotteryid'];?>
^<?php echo $_smarty_tpl->tpl_vars['item']->value['teamid'];?>
_<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['touzhutype'];?>
"><b><?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['touzhuval'];?>
</b>[<?php echo $_smarty_tpl->tpl_vars['touzhiinfo']->value['sp'];?>
]</span>
                                                    <?php }?>
										      <?php }} ?>  
                                    </p>
                                 <?php }} ?>                                  
                        </td>
                    </tr>
 <?php }} ?>                                    
                                     </table>


                 
            </div>         </div>
        
                <div class="float_r prizBetbox">
        	            <div id="qhyhObj">
                <div class="clearfix yhmenu">
                    <div class="float_l yhmenumoney"><span class="float_l font_14">计划购买:&nbsp;</span><input type="text" value="<?php echo $_smarty_tpl->getVariable('yuhua_schememoney')->value;?>
" name="TotalMoney" class="float_l oneBoxmoneytext" id="updataTotalMoney" data-minmoney="<?php echo $_smarty_tpl->getVariable('yuhua_schememoney')->value;?>
"><span class="float_l font_14">&nbsp;元</span></div>
                    <div class="float_l yhmenuIcon"></div>
                    <div class="float_l yhmenuBtn <?php echo $_smarty_tpl->getVariable('pingjunyouhu')->value;?>
"><a id="pjyh" data-val="2" class="yhmenuBtna">平均优化</a>
                        <p class="yhmenuBtnIcon"><em class="yhmenuBtnIconem"></em><span class="yhmenuBtnIconspan"></span></p></div>
                    <div class="float_l yhmenuBtn <?php if ($_smarty_tpl->getVariable('botype')->value!='1'){?>yhmenuBtnGray<?php }?> <?php if ($_smarty_tpl->getVariable('submit_act')->value=='bore'){?>yhmenuBtnSed<?php }?>"><a class="yhmenuBtna"  id="bryh" data-off="2" data-val="3" >博热优化</a>
                        <p class="yhmenuBtnIcon"><em class="yhmenuBtnIconem"></em><span class="yhmenuBtnIconspan"></span></p></div>
                    <div class="float_l yhmenuBtn <?php if ($_smarty_tpl->getVariable('botype')->value!='1'){?>yhmenuBtnGray<?php }?> <?php if ($_smarty_tpl->getVariable('submit_act')->value=='boleng'){?>yhmenuBtnSed<?php }?>"><a class="yhmenuBtna" id="blyh" data-val="4" data-off="2" >博冷优化</a>
                        <p class="yhmenuBtnIcon"><em class="yhmenuBtnIconem"></em><span class="yhmenuBtnIconspan"></span></p></div>
                    <div class="float_l yhtipbox"><em class="tcbox" id="yhtips">&nbsp;</em>
                    	<div style="display: none;" class="okooo_helptips" id="yhtipsCon">
                        	<div class="helptips_space"></div>
                        	<div class="helptips_arrow state6"><s></s><em></em></div>
                            <div class="helptips_content">
                            	<p class="helptips_contentp">平均优化：使所有单注奖金较为平均，一定程度上避免方案中奖却不盈利！</p>
                                <p class="helptips_contentp">博热优化：在其它单注奖金保本的情况下，使概率最高的单注奖金最大化！</p>
                                <p class="helptips_contentp">博冷优化：在其它单注奖金保本的情况下，使回报最高的单注奖金最大化！</p>
                            </div>
                        </div>
                    </div>                    
                    	<div class="okooo_helptips btntips" id="zhggTips" style="display: none;">
                        	<div class="helptips_space"></div>
                        	<div class="helptips_arrow state6" style="left:50px;" id = "showkuo"><s></s><em></em></div>
                            <div class="helptips_content">
                            	<p class="helptips_contentp2" id="boreboleng">博热优化暂不支持复合过关！</p>
                            </div>
                        </div>
                </div>             </div>
                        <div class="dzlistbox">
            	<div class="youhTipBox" id="betTipsObj" style="display:none;"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="borderyh dzborder" id="jjTjObj" >
                    <tr>
                        <th class="tdwideXh" scope="col">序号</th>
                        <th scope="col">单注</th>
                        <th class="tdwideZs" scope="col">倍数</th>
                        <th class="tdwideJj" scope="col">
                        	<div class="noteAll">
                            	<div class="tdwideJjth" id="pljj" title="批量修改单注奖金">理论奖金</div>
                                <div class="noteAllDiv" id="pljjCon" style="display:none;">
                               	    <div class="noteAllcon">
                                    	<a class="noteAllconClose" id="closePljjCon" href="javascript:void(0);" hidefocus>×</a>
                                        <p>所有单注奖金接近：<input class="noteAllinput" id="allupdateMoney" type="text"> 元&nbsp;&nbsp;<a id="updatamoney" class="noteAllbtn" href="javascript:void(0);" hidefocus>确定</a></p>
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>

<?php echo $_smarty_tpl->getVariable('str')->value;?>

                                       
                                    </table>
            </div>
        </div>    </div>
<div id="betbottom" style="padding-bottom:10px;">
    	<div class="yhBetDiv">
            <div class="clearfix jsBetBox">
               <div class="float_l jsBetBoxtxt">
                    <div class="float_l jsBetBoxBshu" style="display:none;" id="btObj"><span class="float_l font_14">倍数：</span><div class="noteBox">
                                    <a class="float_l symboljian" href="javascript:void(0)" data-type="-1" hidefocus>-</a>
                                    <input type="text" value="1" size="8" class="float_l note_input2" id="bsValObj" >
									<input type="hidden" value="1" id="beisValObj"/>
                                    <a class="float_l symbol" href="javascript:void(0)" data-type="1" hidefocus>+</a>
                                </div></div>
                    <p class="float_l font_14">实际购买：<b id="moneyObj" class="font_red font_18"></b> 元</p>
                                        <p class="jsBetBoxtxtp" id="jjObj" style="display:none">理论奖金：<span ></span>  ～ <span class="font_red"></span>&nbsp;元</p>
                                    </div>
                <a class="bigtzbtn" id="betSubObj" href="javascript:void(0);" ></a> 
                <a class="beitbtn" id="btoff" href="javascript:void(0);">倍投</a>
               
            </div>
    	</div>
    </div>
<form target="_parent" id="myform" name="myform" method="post" action="index.php?act=jc">
<input type="hidden" id="LotteryId"	name="LotteryId"	value="208"/><!--彩种ID-->
<input type="hidden" id="TotalMoney"	 name="TotalMoney"	value="<?php echo $_smarty_tpl->getVariable('yuhua_schememoney')->value;?>
"/><!--总共钱数-->
<input type="hidden" id="submit_date"	 name="submit_date"	value='<?php echo $_smarty_tpl->getVariable('submit_date')->value;?>
'/><!--投注信息-->
<input type="hidden" id="submit_act"	 name="submit_act"	value="pingjun"/><!--投注倍数-->
<input type="hidden" id="yuhua_gate"	name="childtype" value='<?php echo $_smarty_tpl->getVariable('childtype')->value;?>
'/><!--过关方式-->
<input type="hidden" id="yuhua_lotterycode"	name="yuhua_lotterycode" value='<?php echo $_smarty_tpl->getVariable('codes')->value;?>
'/><!--过关投注信息-->
<input type="hidden" id="yuhua_lotterymode"	name="yuhua_lotterymode" value='<?php echo $_smarty_tpl->getVariable('yuhua_lotterymode')->value;?>
'/><!--过关投注信息-->
</form>
<form action="/lottery/index.php?act=sechemset_djlz" id="jq_form_dg" method="post">
                        <input id="submit_lotteryid" type="hidden" name="submit_lotteryid" value="" />
                        <input id="submit_lotteryissue" type="hidden" name="submit_lotteryissue" value="" />
                        <input id="submit_lotteryvalue" type="hidden" name="submit_lotteryvalue" value="0" />
                        <input id="submit_appnum" type="hidden" name="submit_appnum" value="0" />
                        <input id="submit_issueflag" type="hidden" name="submit_issueflag" value="0" />
                        <input id="submit_cachefile" type="hidden" name="submit_cachefile" value="" />
      </form>
</div>
    <div id="UserAccess" style="display: none"></div>
    <div id="MessageBox" style="display: none"><p class="MessageBoxP"></p></div>
</body>
</html>
