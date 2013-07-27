<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require_once '../function/c_system_base.php';
require_once '../function/c_system_admin.php';

$zbp->Initialize();

$action='CategoryMng';
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}

$blogtitle='分类管理';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php
//不要吐槽，我会改的！！！

$cate = new Category();
$a = $cate->GetLibIDArray(array($cate->datainfo['Order'][0] => 'ASC'), null);
foreach ($a as $key => $value) {
	$cate->LoadInfoByID($value);
	foreach ($cate->datainfo as $k => $v) {
		$cata_value[$value][$k] = $cate->Data[$k];
	}
}
foreach ($cata_value as $key => $value) {
	if($value['ParentID'] == 0){
		$cata_parent[$value['ID']] = $value;
	}else{
		$cata_child[$value['ID']] = $value;
	}
}

//echo "<pre>";
//print_r($cata_parent);print_r($cata_child);
//echo "</pre>";
?>

<div id="divMain"> 
<div class="divHeader">分类管理</div>
<div class="SubMenu" style="display: block;">
<a href="../cmd.php?act=CategoryEdt&id=0"><span class="m-left">新建分类</span></a>
</div>
<div id="divMain2">
<table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
 <tbody>
  <tr class="color1">
   <th width="5%"></th>
   <th width="10%">ID</th>
   <th width="10%">排序</th>
   <th>名称</th>
   <th>别名</th>
   <th width="14%"></th>
  </tr>

<?php
foreach($cata_parent as $key => $value){

	print <<<html
	 <tr class="color2">
	   <td align="center"><img width="16" src="../image/admin/folder.png" alt="" /></td>
	   <td>{$value['ID']}</td>
	   <td>{$value['Order']}</td>
	   <td><a href="{$bloghost}catalog.php?cate={$value['ID']}" target="_blank">{$value['Name']}</a></td>
	   <td>{$value['Alias']}</td>
	   <td align="center"><a href="../cmd.php?act=CategoryEdt&amp;id={$value['ID']}" class="button"><img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);" href="../cmd.php?act=CategoryDel&amp;id=1" class="button"><img src="../image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td>
	  </tr>
html;
if(isset($cata_child)){
foreach($cata_child as $k => $v){
	if($key == $v['ParentID']){
		print <<<html
		 <tr class="color2">
		   <td align="center"><img width="16" src="../image/admin/arrow_turn_right.png" alt="" /></td>
		   <td>{$v['ID']}</td>
		   <td>{$v['Order']}</td>
		   <td><a href="{$bloghost}catalog.php?cate={$v['ID']}" target="_blank">{$v['Name']}</a></td>
		   <td>{$v['Alias']}</td>
		   <td align="center"><a href="../cmd.php?act=CategoryEdt&amp;id={$v['ID']}" class="button"><img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);" href="../cmd.php?act=CategoryDel&amp;id=1" class="button"><img src="../image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td>
		  </tr>
html;
	unset($cata_child[$k]);
	}
}
}
} 
?>
 </tbody>
</table>
<p>&nbsp;</p>
</div>
<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script> 
</div>




</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
