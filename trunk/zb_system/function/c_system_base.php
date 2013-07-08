<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
error_reporting(0);
error_reporting(E_ALL);

ob_start();

$startime=microtime();

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';

require_once $blogpath.'/zb_system/function/c_system_common.php';
require_once $blogpath.'/zb_system/function/c_system_lib.php';
require_once $blogpath.'/zb_system/function/c_system_plugin.php';

$c_option = include($blogpath.'zb_users/c_option.php');	
$c_lang = include($blogpath.'zb_users/language/'.$c_option['ZC_BLOG_LANGUAGEPACK'].'.php');

$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

/*include plugin*/

$zbp=new zblogphp;

?>