<?php

/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

#error_reporting(0);
ini_set('display_errors',1);
error_reporting(E_ALL);

@ini_set('magic_quotes_runtime',0);
@ini_set('magic_quotes_gpc',0);

ob_start();

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';
$cookiespath = null;
$bloghost = null;
$blogtitle = null;
$option = require_once($blogpath . 'zb_users/c_option.php');
$option['ZC_BLOG_PRODUCT_FULL']=$option['ZC_BLOG_PRODUCT'] . ' ' . $option['ZC_BLOG_VERSION'];
$option['ZC_BLOG_PRODUCT_FULLHTML']='<a href="http://www.rainbowsoft.org/" title="RainbowSoft Z-BlogPHP">' . $option['ZC_BLOG_PRODUCT_FULL'] . '</a>';
$lang = require_once($blogpath . 'zb_users/language/' . $option['ZC_BLOG_LANGUAGEPACK'] . '.php');
$action=null;

date_default_timezone_set($option['ZC_TIME_ZONE_NAME']);

require_once $blogpath.'zb_system/function/c_system_debug.php';
require_once $blogpath.'zb_system/function/c_system_common.php';
require_once $blogpath.'zb_system/function/c_system_plugin.php';
require_once $blogpath.'zb_system/function/c_system_event.php';


$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

require_once $blogpath.'zb_system/function/c_system_lib_zblogphp.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbfactory.php';

require_once $blogpath.'zb_system/function/c_system_lib_dbmysql.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbpdo_mysql.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite3.php';
#以后修改
#require_once $blogpath.'zb_system/function/c_system_lib_db' .$option['ZC_DATABASE_TYPE']. '.php';

$lib_array = array('base', 'article','category','comment','member','module','tag','upload');
foreach ($lib_array as $f) {
	require_once $blogpath.'zb_system/function/c_system_lib_' .$f. '.php';
}





#定义命令
$actions=array(
'root'=>1,
'login'=>5,
'logout'=>5,
'verify'=>5,
'admin'=>4,
'SettingMng'=>1,
'vrs'=>5,
'ArticleEdt'=>3,
'ArticleMng'=>3,
'CategoryMng'=>1,
'TagMng'=>1,
'CommentMng'=>4,
'FileMng'=>1,
'UserMng'=>1,
'ThemeMng'=>1,
'PlugInMng'=>1,
'FunctionMng'=>1,
'UserEdt'=>0,
);




$zbp=ZBlogPHP::GetInstance();




/*include plugin*/
#加载主题插件
if (file_exists($filename=$blogpath.'zb_users/theme/'.$option['ZC_BLOG_THEME'].'/plugin/include.php')) {
	require_once $filename;
}
#加载激活插件
foreach (explode("|", $option['ZC_USING_PLUGIN_LIST']) as $plugin) {
	if ($filename&&file_exists($filename=$blogpath.'zb_users/plugin/'.$plugin.'/include.php')) {
		require_once $filename;
	}
}




function __autoload($classname) {
     require_once $GLOBALS['blogpath'] . 'zb_system/function/c_system_lib_' . strtolower($classname) .'.php';
}


?>