<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function zbp_addpagesubmenu(){
	echo '<a href="../cmd.php?act=ArticleEdt&amp;type=1"><span class="m-left">' . $GLOBALS['lang']['msg']['new_page'] . '</span></a>';
}

function zbp_addtagsubmenu(){
	echo '<a href="../cmd.php?act=TagEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_tag'] . '</span></a>';
}

function zbp_addcatesubmenu(){
	echo '<a href="../cmd.php?act=CategoryEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_category'] . '</span></a>';
}


Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu','zbp_addpagesubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_TagMng_SubMenu','zbp_addtagsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu','zbp_addcatesubmenu');

$zbp->LoadTemplates();

$topmenus=array();

$leftmenus=array();


function ResponseAdmin_LeftMenu(){

	global $zbp;
	global $leftmenus;

	$leftmenus[]=MakeLeftMenu("ArticleEdt",$zbp->lang['msg']['new_article'],$zbp->host . "zb_system/cmd.php?act=ArticleEdt","nav_new","aArticleEdt","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$zbp->lang['msg']['article_manage'],$zbp->host . "zb_system/cmd.php?act=ArticleMng","nav_article","aArticleMng","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$zbp->lang['msg']['page_manage'],$zbp->host . "zb_system/cmd.php?act=ArticleMng&amp;type=1","nav_page","aPageMng","");

	$leftmenus[]="<li class='split'><hr/></li>";


	$leftmenus[]=MakeLeftMenu("CategoryMng",$zbp->lang['msg']['category_manage'],$zbp->host . "zb_system/cmd.php?act=CategoryMng","nav_category","aCategoryMng","");
	$leftmenus[]=MakeLeftMenu("TagMng",$zbp->lang['msg']['tag_manage'],$zbp->host . "zb_system/cmd.php?act=TagMng","nav_tags","aTagMng","");
	$leftmenus[]=MakeLeftMenu("CommentMng",$zbp->lang['msg']['comment_manage'],$zbp->host . "zb_system/cmd.php?act=CommentMng","nav_comments","aCommentMng","");
	$leftmenus[]=MakeLeftMenu("UploadMng",$zbp->lang['msg']['upload_manage'],$zbp->host . "zb_system/cmd.php?act=UploadMng","nav_accessories","aUploadMng","");
	$leftmenus[]=MakeLeftMenu("MemberMng",$zbp->lang['msg']['member_manage'],$zbp->host . "zb_system/cmd.php?act=MemberMng","nav_user","aMemberMng","");

	$leftmenus[]="<li class='split'><hr/></li>";

	$leftmenus[]=MakeLeftMenu("ThemeMng",$zbp->lang['msg']['theme_manage'],$zbp->host . "zb_system/cmd.php?act=ThemeMng","nav_themes","aThemeMng","");
	$leftmenus[]=MakeLeftMenu("ModuleMng",$zbp->lang['msg']['module_manage'],$zbp->host . "zb_system/cmd.php?act=ModuleMng","nav_function","aModuleMng","");
	$leftmenus[]=MakeLeftMenu("PluginMng",$zbp->lang['msg']['plugin_manage'],$zbp->host . "zb_system/cmd.php?act=PluginMng","nav_plugin","aPluginMng","");

	foreach ($GLOBALS['Filter_Plugin_Admin_LeftMenu'] as $fpname => &$fpsignal) {
		$fpname($leftmenus);
	}

	foreach ($leftmenus as $m) {
		echo $m;
	}

}

function ResponseAdmin_TopMenu(){

	global $zbp;
	global $topmenus;

	$topmenus[]=MakeTopMenu("admin",$zbp->lang['msg']['dashboard'],$zbp->host . "zb_system/cmd.php?act=admin","","");
	$topmenus[]=MakeTopMenu("SettingMng",$zbp->lang['msg']['settings'],$zbp->host . "zb_system/cmd.php?act=SettingMng","","");

	foreach ($GLOBALS['Filter_Plugin_Admin_TopMenu'] as $fpname => &$fpsignal) {
		$fpname($topmenus);
	}

	$topmenus[]=MakeTopMenu("misc",$zbp->lang['msg']['official_website'],"http://www.rainbowsoft.org/","","_blank");

	foreach ($topmenus as $m) {
		echo $m;
	}

}


function MakeTopMenu($requireAction,$strName,$strUrl,$strLiId,$strTarget){
	global $zbp;

	static $AdminTopMenuCount=0;
	if ($zbp->CheckRights($requireAction)==false) {
		return null;
	}

	$tmp=null;
	if($strTarget==""){$strTarget="_self";}
	$AdminTopMenuCount=$AdminTopMenuCount+1;
	if($strLiId==""){$strLiId="topmenu" . $AdminTopMenuCount;}
	$tmp="<li id=\"" . $strLiId . "\"><a href=\"" . $strUrl . "\" target=\"" . $strTarget . "\">" . $strName . "</a></li>";
	return $tmp;
}


function MakeLeftMenu($requireAction,$strName,$strUrl,$strLiId,$strAId,$strImgUrl){
	global $zbp;

	static $AdminLeftMenuCount=0;
	if ($zbp->CheckRights($requireAction)==false) {
		return null;
	}

	$AdminLeftMenuCount=$AdminLeftMenuCount+1;
	$tmp=null;
	if($strImgUrl==""){
		$tmp="<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\"><span style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . "</span></a></li>";
	}else{
		$tmp="<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\"><span>" . $strName . "</span></a></li>";
	}
	return $tmp;
	
}



function Admin_SiteInfo(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['info_intro'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_SiteInfo_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';



	echo '<table class="tableFull tableBorder" id="tbStatistic"><tr><th colspan="4">&nbsp;' . $zbp->lang['msg']['site_analyze'] . '&nbsp;<a href="javascript:statistic(\'?act=misc&amp;type=statistic\');">[' . $zbp->lang['msg']['refresh_cache'] . ']</a> <img id="statloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';
	echo $zbp->GetCache('reload_statistic');
	echo '</table>';

	echo '<table class="tableFull tableBorder" id="tbUpdateInfo"><tr><th>&nbsp;' . $zbp->lang['msg']['latest_news'] . '&nbsp;<a href="javascript:updateinfo(\'?act=misc&amp;type=updateinfo\');">[' . $zbp->lang['msg']['refresh'] . ']</a> <img id="infoloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';
	echo $zbp->GetCache('reload_updateinfo');
	echo '</table>';

	echo '</div>';
	include $zbp->path . "zb_system/defend/thanks.html";
	echo '<script type="text/javascript">ActiveTopMenu("topmenu1");</script>';
}


function Admin_ArticleMng(){

	global $zbp;


	echo '<div class="divHeader">' . $zbp->lang['msg']['article_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ArticleMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<form class="search" id="edit" method="post" action="#">';

	echo '<p>搜索:&nbsp;&nbsp;分类 <select class="edit" size="1" name="cate" style="width:100px;" ><option value="">任意</option></select>&nbsp;&nbsp;&nbsp;&nbsp;
	类型 <select class="edit" size="1" name="status" style="width:80px;" ><option value="">任意</option> <option value="0" >公开</option><option value="2" >私人</option><option value="4" >草稿</option></select>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="checkbox" name="istop" value="True"/>&nbsp;置顶</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="提交"/></p>';
	echo '</form>';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['category'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['title'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . $zbp->lang['msg']['comment'] . '</th>
	<th>' . $zbp->lang['msg']['status'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar();
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;
$p->UrlRule='{%host%}zb_system/cmd.php?act=ArticleMng&amp;page={%page%}';

$w=array();
if(GetVars('search','POST')){
	$w[]=array('search','log_Content','log_Intro','log_Title',GetVars('search','POST'));
}
if(GetVars('istop','POST')){
	$w[]=array('=','log_Istop','1');
}
if(GetVars('status','POST')){
	$w[]=array('=','log_Status',GetVars('status','POST'));
}

$array=$zbp->GetArticleList(
	'',
	$w,
	array('log_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $article) {
	echo '<tr>';
	echo '<td class="td5">' . $article->ID .  '</td>';
	echo '<td class="td10">' . $article->CateID . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td>' . $article->Title . '</td>';
	echo '<td class="td20">' . date('Y-m-d h:i:s',$article->PostTime) . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . $article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=ArticleEdt&amp;id='. $article->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ArticleDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';

foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}

	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aArticleMng");</script>';

}

function Admin_PageMng(){

	global $zbp;


	echo '<div class="divHeader">' . $zbp->lang['msg']['page_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_PageMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<!--<form class="search" id="edit" method="post" action="#"></form>-->';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['title'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . $zbp->lang['msg']['comment'] . '</th>
	<th>' . $zbp->lang['msg']['status'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar();
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;
$p->UrlRule='{%host%}zb_system/cmd.php?act=ArticleMng&amp;type=1&amp;page={%page%}';

$array=$zbp->GetPageList(
	'',
	'',
	array('log_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $article) {
	echo '<tr>';
	echo '<td class="td5">' . $article->ID . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td>' . $article->Title . '</td>';
	echo '<td class="td20">' . date('Y-m-d h:i:s',$article->PostTime) . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . $article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=ArticleEdt&amp;id='. $article->ID .'&amp;type=1"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ArticleDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aPageMng");</script>';
	
}

function Admin_CategoryMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['category_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_CategoryMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>

	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['order'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['alias'] . '</th>
	<th>' . $zbp->lang['msg']['post_count'] . '</th>
	<th></th>
	</tr>';


foreach ($zbp->categorysbyorder as $category) {
	echo '<tr>';
	echo '<td class="td5">' . $category->ID . '</td>';
	echo '<td class="td5">' . $category->Order . '</td>';
	echo '<td class="td25">' . $category->Symbol . $category->Name . '</td>';
	echo '<td class="td20">' . $category->Alias . '</td>';
	echo '<td class="td10">' . $category->Count . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=CategoryEdt&amp;id='. $category->ID .'"><img src="../image/admin/folder_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=CategoryDel&amp;id=26"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script>';

	
}

function Admin_CommentMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['comment_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_CommentMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aCommentMng");</script>';
	
}

function Admin_MemberMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['member_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_MemberMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aMemberMng");</script>';
	
}

function Admin_UploadMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['upload_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_UploadMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aUploadMng");</script>';
	
}

function Admin_TagMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['tag_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_TagMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';


	echo '<div id="divMain2">';
	echo '<!--<form class="search" id="edit" method="post" action="#"></form>-->';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['alias'] . '</th>
	<th>' . $zbp->lang['msg']['post_count'] . '</th>	
	<th></th>
	</tr>';

$p=new Pagebar();
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;
$p->UrlRule='{%host%}zb_system/cmd.php?act=TagMng&amp;type=1&amp;page={%page%}';

$array=$zbp->GetTagList(
	'',
	'',
	array('tag_Name'=>'ASC','tag_ID'=>'ASC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $tag) {
	echo '<tr>';
	echo '<td class="td5">' . $tag->ID . '</td>';
	echo '<td class="td25">' . $tag->Name . '</td>';
	echo '<td class="td20">' . $tag->Alias . '</td>';
	echo '<td class="td10">' . $tag->Count . '</td>';	
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=TagEdt&amp;id='. $tag->ID .'"><img src="../image/admin/tag_blue_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=TagDel&amp;id='. $tag->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';

	echo '<script type="text/javascript">ActiveLeftMenu("aTagMng");</script>';
	
}

function Admin_ThemeMng(){

	global $zbp;

	$zbp->LoadThemes();

	echo '<div class="divHeader">' . $zbp->lang['msg']['theme_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ThemeMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2"><form id="frmTheme" method="post" action="../cmd.php?act=ThemeSet">';
	echo '<input type="hidden" name="theme" id="theme" value="" />';
	echo '<input type="hidden" name="style" id="style" value="" />';

	foreach ($zbp->themes as $theme) {

echo '<div class="theme '.($theme->IsUsed()?'theme-now':'theme-other').'">';
echo '<div class="theme-name">';
echo '<img width="16" title="" alt="" src="../image/admin/layout.png"/>&nbsp;';
echo '<a target="_blank" href="'.$theme->url.'" title="">';
echo '<strong style="display:none;">default</strong><b>'.$theme->name.'</b></a></div>';
echo '<div><img src="'.$theme->GetScreenshot().'" title="'.$theme->name.'" alt="'.$theme->name.'" width="200" height="150" /></div>';
echo '<div class="theme-author">'.$zbp->lang['msg']['author'].': <a target="_blank" href="'.$theme->author_url.'">'.$theme->author_name.'</a></div>';
echo '<div class="theme-style">'.$zbp->lang['msg']['style'].': ';
echo '<select class="edit" size="1" style="width:110px;">';
foreach ($theme->GetCssFiles() as $key => $value) {
	echo '<option value="'.$key.'" '.($theme->IsUsed()?($key==$zbp->style?'selected="selected"':''):'').'>'.basename($value).'</option>';
}
echo '</select>';
echo '<input type="button" onclick="$(\'#style\').val($(this).prev().val());$(\'#theme\').val(\''.$theme->id.'\');$(\'#frmTheme\').submit();" class="theme-activate button" value="'.$zbp->lang['msg']['enable'].'">';
echo '</div>';
echo '</div>';

	}

	echo '</form></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aThemeMng");</script>';
	
}

function Admin_ModuleMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['module_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ModuleMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aModuleMng");</script>';
	
}

function Admin_PluginMng(){

	global $zbp;
	
	$zbp->LoadPlugins();

	echo '<div class="divHeader">' . $zbp->lang['msg']['plugin_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_MemberMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>

	<th></th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th></th>
	</tr>';
$plugins=array();

$app = new App;
if($app->LoadInfoByXml('theme',$zbp->theme)==true){
	if($app->HasPlugin()){
		array_unshift($plugins,$app);
	}
}

$pl=$zbp->option['ZC_USING_PLUGIN_LIST'];
$apl=explode('|',$pl);
foreach ($apl as $name) {
	foreach ($zbp->plugins as $plugin) {
		if($name==$plugin->id){
			$plugins[]=$plugin;
		}
	}
}
foreach ($zbp->plugins as $plugin) {
	if(!$plugin->IsUsed()){
		$plugins[]=$plugin;
	}
}


foreach ($plugins as $plugin) {
	echo '<tr>';
	echo '<td class="td5 tdCenter"><img ' . ($plugin->IsUsed()?'':'style="opacity:0.2"') . ' src="' . $plugin->GetLogo() . '" alt="" width="32" /></td>';
	echo '<td class="td25">' . $plugin->name . '</td>';
	echo '<td class="td20">' . $plugin->author_name . '</td>';
	echo '<td class="td20">' . $plugin->modified . '</td>';
	echo '<td class="td10 tdCenter">';

	if($plugin->type=='plugin'){
		if($plugin->IsUsed()){
			echo '<a href="../cmd.php?act=PluginDisable&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['disable'] . '"><img width="16" alt="' . $zbp->lang['msg']['disable'] . '" src="../IMAGE/ADMIN/control-power.png"/></a>';
		}else{
			echo '<a href="../cmd.php?act=PluginEnable&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['enable'] . '"><img width="16" alt="' . $zbp->lang['msg']['enable'] . '" src="../IMAGE/ADMIN/control-power-off.png"/></a>';
		}
	}
	if($plugin->CanManage()){
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a href="' . $plugin->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '"><img width="16" alt="' . $zbp->lang['msg']['manage'] . '" src="../IMAGE/ADMIN/setting_tools.png"/></a>';
	}	

	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>';
	
}

function Admin_SettingMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['settings'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_SettingMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveTopMenu("topmenu2");</script>';
}

?>