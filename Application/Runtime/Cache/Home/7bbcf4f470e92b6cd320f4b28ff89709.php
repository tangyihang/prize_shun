<?php if (!defined('THINK_PATH')) exit();?>﻿<div class="pageContent">
	<form method="post" action="/index.php/Home/Result/add_act_jc" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p style="width:100%"><strong><?php echo ($kjinfo["cz_name"]); ?>&nbsp;竞彩赛果录入信息</strong></p>
			<p style="width:100%">
				<label style="width:100px;text-align:right;">对阵编号：</label>
				<input type="text" name="ballid" alt="" value="" size="20" class="required textInput valid" />
				<label style="width:200px;color:#999;">对阵编号格式：001</label>
			</p>
			<p style="width:100%">
				<label style="width:100px;text-align:right;">对阵时间：</label>
				<input type="text" name="lotttime" alt="" value="" size="20" class="required date" datefmt="yyyy-MM-dd"/>
				<label style="width:200px;color:#999;">对阵时间格式：2015-11-17</label>
			</p>
			<p style="width:100%">
				<label style="width:100px;text-align:right;">比赛时间：</label>
				<input type="text" name="matchtime" alt="" value="" size="20" class="required date" datefmt="yyyy-MM-dd HH:mm:ss"/>
				<label style="width:200px;color:#999;">比赛时间格式：2015-11-17 13:30:00</label>
			</p>
			<p style="width:100%">
				<label style="width:100px;text-align:right;">来源：</label>
				<select name="sourcejc">
					<option value="from163">from163</option>
					<option value="fromOkooo">fromOkooo</option>
					<option value="500wan">500wan</option>
				</select>
				
			</p>
		</div>
		
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button class="close" type="button">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>