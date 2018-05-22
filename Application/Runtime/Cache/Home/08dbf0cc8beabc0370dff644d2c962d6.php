<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="/index.php/Home/Result/jingcai">
	<input type="hidden" name="act" value="query">
	<input type="hidden" name="lotttime" value="<?php echo ($lotttime); ?>" />
	<input type="hidden" name="ballid" value="<?php echo ($ballid); ?>" />
	<input type="hidden" name="status" value="<?php echo ($status); ?>" />	
	<input type="hidden" name="pageNum" value="<?php echo ($currentPage); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="/index.php/Home/Result/jingcai" method="post">
	<input type="hidden" name="act" value="query" />
	<div class="searchBar">
		<table class="searchContent">
			<tr>
			   <td width="180">
					对阵时间：<input type="text" class="date" name="lotttime" value='<?php echo ($lotttime); ?>' readonly="true" size='15' />
				</td>
			    <td width="180">
					对阵编号：<input type="text" class="textInput" name="ballid" value='<?php echo ($ballid); ?>'  size='15' />
				</td>
				<td>
					<div style="float:left;width:auto;line-height:24px;">状态：</div>
					<div style="float:left;width:auto;line-height:30px;">
					<select class="combox" name="status" id="jcstatus">
						<option value="1" <?php if($status ==1 ): ?>selected<?php endif; ?>>异常</option>
						<option value="2" <?php if($status ==2 ): ?>selected<?php endif; ?>>正常</option>
					</select>
					</div>
				</td>
				<td>
					<div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div>
				</td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="/index.php/Home/Result/add?act=jc" target="dialog" rel="jcresult" title="添加赛果"><span>添加赛果</span></a></li>
			<li><a class="edit" href="/index.php/Home/Result/view?recid={lotttime_ballid}&act=edit" target="dialog" rel="editresult" title="修改赛果"><span>修改赛果</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="113">
		<thead>
			<tr>
				<th width="80" align="center">ID</th>
				<th width="60" align="center">对阵编号</th>
				<th width="150" align="center">对阵时间</th>
				<th width="60" align="center">来源</th>
				<th width="100" align="center">半场比分</th>
				<th width="100" align="center">全场比分</th>
				<th width="150" align="center">比赛时间</th>
				<th width="150" align="center">入库时间</th>
				<th width="100" align="center">赛果</th>
				<th width="100" align="center">状态</th>
				<th width="150" align="center" >操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php if(is_array($resultlist)): foreach($resultlist as $key=>$res): ?><tr target="lotttime_ballid" rel="<?php echo ($res["lotttime"]); ?>_<?php echo ($res["ballid"]); ?>" >
				<td><?php echo ($res["id"]); ?></td>
				<td><?php echo ($res["cnballid"]); ?></td>
				<td><?php echo ($res["lotttime"]); ?></td>
				<td><?php echo ($res["source"]); ?></td>
				<td><?php echo ($res["half_score"]); ?></td>
				<td><?php echo ($res["full_score"]); ?></td>
				<td><?php echo ($res["match_starttime"]); ?></td>
				<td><?php echo ($res["addtime"]); ?></td>
				<td><?php echo ($res["result"]); ?></td>
				<td><?php echo ($res["status"]); ?></td>
				<td><a href='/index.php/Home/Result/view?recid=<?php echo ($res["lotttime"]); ?>_<?php echo ($res["ballid"]); ?>' target="dialog" title="查看赛果">查看</a>
					<?php if(($res["status"] != '4') and ($status == '2') ): ?>| <a style="color:#0033ff" href="/index.php/Home/Result/pauseAward?recid=<?php echo ($res["lotttime"]); ?>_<?php echo ($res["ballid"]); ?>&rstatus=<?php echo ($res["status"]); ?>&act=jc" title="您确定要暂停派奖操作吗？" target="ajaxTodo">暂停派奖</a><?php elseif(($res["status"] == '4') and ($status == '2') ): ?> | <a style="color:#0033ff" href="/index.php/Home/Result/pauseAward?recid=<?php echo ($res["lotttime"]); ?>_<?php echo ($res["ballid"]); ?>&rstatus=<?php echo ($res["status"]); ?>&act=jc" title="您确定要开启派奖操作吗？" target="ajaxTodo">开启派奖</a><?php endif; ?></td>
			</tr><?php endforeach; endif; ?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20" <?php if($numPerPage == '20'): ?>selected="selected"<?php endif; ?>>20</option>
				<option value="50" <?php if($numPerPage == '50'): ?>selected="selected"<?php endif; ?>>50</option>
				<option value="100" <?php if($numPerPage == '100'): ?>selected="selected"<?php endif; ?>>100</option>
				<option value="200" <?php if($numPerPage == '200'): ?>selected="selected"<?php endif; ?>>200</option>
			</select>
			<span>共<?php echo ($pageTotal); ?>页</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="<?php echo ($pageNumShown); ?>" currentPage="<?php echo ($currentPage); ?>"></div>
	</div>
</div>