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

$action='CategoryEdt';
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}

$blogtitle='分类编辑';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php
//不要吐槽，我会改的！！！

if(isset($_POST['edtID'])){
	$cate = new Category();
	if($_POST['edtID'] == 0){
		$cate->LoadInfobyArray(array($_POST['edtID'], $_POST['edtName'], $_POST['edtOrder'], 0, $_POST['edtAlias'], '', 0, $_POST['edtPareID'], $_POST['edtTemplate'], $_POST['edtLogTemplate'], ''));
		$cate->Post();
	}else{
		$cate->LoadInfoByID($_POST['edtID']);
		$cate->Data['Name'] = $_POST['edtName'];
		$catadata = $cate->Data;
		if($cate->Post()){redirect('catalog.php');}
	}
}



if(isset($_GET['id'])){$cateid = $_GET['id'];}else{$cateid = 0;}

$cate = new Category();
$cate->LoadInfoByID($cateid);
$catadata = $cate->Data;
//print_r($catadata);

?>

<div id="divMain">
  <div class="divHeader2">分类编辑</div>
  <div class="SubMenu"></div>
  <div id="divMain2">
	<form id="edit" name="edit" method="post" action="">
	  <input id="edtID" name="edtID" type="hidden" value="<?php echo $cateid;?>" />
	  <p>
		<span class="title">名称:</span><span class="star">(*)</span><br />
		<input id="edtName" class="cleanuphtml-1" size="40" name="edtName" maxlength="50" type="text" value="<?php echo $catadata['Name'];?>" />
	  </p>
	  <p>
		<span class="title">别名:</span><br />
		<input id="edtAlias" class="cleanuphtml-1" size="40" name="edtAlias" type="text" value="<?php echo $catadata['Alias'];?>" />
	  </p>
	  <?php if($cateid != 1){ ?>
	  <p>
		<span class="title">排序:</span><br />
		<input id="edtOrder" class="cleanuphtml-1" size="40" name="edtOrder" type="text" value="<?php echo $catadata['Order'];?>" />
	  </p>
	  <p>
		<span class="title">父分类:</span><br />
		<select id="edtPareID" name="edtPareID" class="edit cleanuphtml-2" size="1">
		  <option value="<?php echo $catadata['ParentID'];?>" selected="selected">无</option>
		</select>
	  </p>
	  <p>
		<span class="title">模板:</span><br />
		<select class="edit cleanuphtml-2" size="1" id="cmbTemplate" onchange="edtTemplate.value=this.options[this.selectedIndex].value">
		  <option value="CATALOG" selected="selected">CATALOG (默认模板)</option>
		  <option value="DEFAULT">DEFAULT</option>
		  <option value="SEARCH">SEARCH</option>
		  <option value="TAGS">TAGS</option>
		</select><input type="hidden" name="edtTemplate" id="edtTemplate" value="<?php echo $catadata['Template'];?>" />
	  </p>
	  <p>
		<span class="title">此目录下文章的默认模板:</span><br />
		<select class="edit cleanuphtml-2" size="1" id="cmbLogTemplate" onchange="edtLogTemplate.value=this.options[this.selectedIndex].value">
		  <option value="PAGE">PAGE</option>
		  <option value="SEARCH">SEARCH</option>
		  <option value="SINGLE" selected="selected">SINGLE (默认模板)</option>
		</select><input type="hidden" name="edtLogTemplate" id="edtLogTemplate" value="<?php echo $catadata['LogTemplate'];?>" />
	  </p>
	  <p>
		<label><input type="checkbox" name="edtAddNavbar" id="edtAddNavbar" value="True" />  <span class="title">加入导航栏菜单</span></label>
	  </p>
	  <?php }else{echo "<p> 提示:'未分类'分类是系统默认加入的分类,不能删除;未指定分类的文章都归入'未分类'下,该分类下没有文章的话将不显示在前台分类列表中.</p>";}?>
	  <p>
		<input type="submit" class="button" value="提交" id="btnPost" onclick="return checkCateInfo();" />
	  </p>
	</form>
	<script type="text/javascript">
			var str17="名称不能为空";
			function checkCateInfo(){
					document.getElementById("edit").action="catalog_edit.php?act=CategoryEdt";

					if(!$("#edtName").val()){
							alert(str17);
							return false
					}

			}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script>
  </div>
</div>


<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
