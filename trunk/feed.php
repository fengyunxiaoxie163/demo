<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

$zbp->Initialize();

$action='feed';

if(!$zbp->CheckRights($action)){Http404();die();}

$rss2 = new Rss2($zbp->name,$zbp->host,$zbp->subname);

$articles=$zbp->GetArticleList(
	array('*'),
	array(array('=','log_Istop',0),array('=','log_Status',0)),
	array('log_PostTime'=>'DESC'),
	array(10),
	null
);

foreach ($articles as $article) {
	$rss2->addItem($article->Title,$article->Url,$article->Content,$article->PostTime);
}

header("Content-type:text/xml; Charset=utf8");

echo $rss2->saveXML();

$zbp->Terminate();

RunTime();
?>