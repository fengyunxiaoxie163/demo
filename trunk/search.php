<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install');}

$zbp->LoadData();

global $zbp;


$article = new Post;
$article->Title=$lang['msg']['search'] . '“' . GetVars('q','GET') . '”';
$article->IsLock=true;
$article->Type=ZC_POST_TYPE_PAGE;


$w=array();
$w[]=array('=','log_Type','0');
$s=trim(GetVars('q','GET'));
if($s){
	$w[]=array('search','log_Content','log_Intro','log_Title',$s);
}else{
	Redirect('./');
}

$array=$zbp->GetArticleList(
	'',
	$w,
	array('log_PostTime'=>'DESC'),
	array(50),
	null
);

foreach ($array as $a) {

	$article->Content .= '<p><br/>' . $a->Title . '<br/>';
	$article->Content .= '<a href="' . $a->Url . '">' . $a->Url . '</a></p>';
}


$zbp->template->SetTags('title',$article->Title);

$zbp->template->SetTags('article',$article);

$zbp->template->display($zbp->option['ZC_PAGE_DEFAULT_TEMPLATE']);

RunTime();
?>