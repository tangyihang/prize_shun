<?php if (!defined('THINK_PATH')) exit();?>﻿<div class="pageContent">
	<form method="post" action="/index.php/Home/Result/edit_act_lc" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
		   <input type="hidden" name="lotttime" value="<?php echo ($lotttime); ?>">
		   <input type="hidden" name="ballid" value="<?php echo ($ballid); ?>">
		   <?php if(is_array($Rlist)): foreach($Rlist as $key=>$RL): ?><p style="width:100%">
				<label style="width:40px;">来源：</label>
				<input name="source[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["source"]); ?>" class="textInput2"/>
				<label style="width:20px;">一：</label>
			    <input name="first[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["first_score"]); ?>" class="textInput2"/>
				<label style="width:20px;">二：</label>
			    <input name="two[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["two_score"]); ?>" class="textInput2"/>
			    <label style="width:20px;">三：</label>
			    <input name="three[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["three_score"]); ?>" class="textInput2"/>
			    <label style="width:20px;">四：</label>
				<input name="four[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["four_score"]); ?>" class="textInput2"/>
			    <label style="width:20px;">加：</label>
			    <input name="jia[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["add_score"]); ?>" class="textInput2"/>
				<label style="width:20px;">总：</label>
				<input name="full[<?php echo ($RL["id"]); ?>]" type="text" size="10" value="<?php echo ($RL["full_score"]); ?>" class="textInput2"/>
			  <label style="width:50px;">修改项：</label>
			    <input name="isRight[<?php echo ($RL["id"]); ?>]" type="checkbox" size="10" class="textInput2"/>
			</p><?php endforeach; endif; ?>
			<p style="width:100%;">注：来源分别来自 500wan ,fromOkooo,from163 缺少哪一项填对应项，录入时一定仔细对照，方提交</p>
		</div>
		<?php if($act == 'edit'): ?><div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">修改</button></div></div></li>
			</ul>
		</div>
		<?php else: endif; ?>
	</form>
</div>