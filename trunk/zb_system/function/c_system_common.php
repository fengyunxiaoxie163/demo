<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

$_SERVER['_start_time'] = microtime(1); //RunTime
function runtime(){
	echo '<!--'.number_format(microtime(1) - $_SERVER['_start_time'], 6).'s-->';
}


function getGuid(){
	$s=str_replace('.','',trim(uniqid('zbp',true),'zbp'));
	return $s;
}

function GetVars($name,$type='REQUEST'){
	if ($type=='ENV') {$array=&$_ENV;}
	if ($type=='GET') {$array=&$_GET;}
	if ($type=='POST') {$array=&$_POST;}
	if ($type=='COOKIE') {$array=&$_COOKIE;}
	if ($type=='REQUEST') {$array=&$_REQUEST;}
	if ($type=='SERVER') {$array=&$_SERVER;}
	if ($type=='SESSION') {$array=&$_SESSION;}
	if ($type=='FILES') {$array=&$_FILES;}

	if(isset($array[$name])){
		return $array[$name];
	}else{
		return '';
	}
}

function getDbName(){

	return 'zb_users/data/' . str_replace('-','','#%20' . strtolower(getGuid())) . '.db';
}


function getCurrentHost(&$cookiespath){
	if (array_key_exists('HTTPS',$_SERVER)) {
		if ($_SERVER['HTTPS']=='off') {
			$host='http://';
		} else {
			$host='https://';
		}
	} else {
		$host='http://';
	}


	$host.=$_SERVER['HTTP_HOST'];

	$y=strtolower($GLOBALS['blogpath']);
	$x=strtolower($_SERVER['SCRIPT_NAME']);

	for ($i=strlen($x); $i >0 ; $i--) { 
		$z=substr($x,0,$i);
		if(substr($y,strlen($y)-$i)==$z){
			break;
		}
	}

	$cookiespath=$z;

	return $host . $z;
}


function getPassWordByGuid($ps,$guid){

	return md5(md5($ps).$guid);

}


function getFilesInDir($dir,$type){

	$files=array();

	foreach (scandir($dir) as $f) {
		if (is_file($dir . $f)) {
			foreach (explode("|",$type) as $t) {
				$t='.' . $t;
				$i=strlen($t);
				if (substr($f,-$i,$i)==$t) {
					$sortname=substr($f,0,strlen($f)-$i);
					$files[$sortname]=$dir . $f;
					break;
				}
			}

		}
	}

	return $files;

}


function redirect($url){
	header("HTTP/1.1 302 Found");
	header('Location: '.$url);
}


function logs($s){
	$f=$GLOBALS['blogpath'] . 'zb_users/logs/'. $GLOBALS['option']['ZC_BLOG_CLSID'] .'-log' . date("Ymd"). '.txt';
	$handle = fopen($f, 'a+');
	fwrite($handle,"[" . date('c') . "~" . current(explode(" ", microtime()))  . "]" . $s . "\r\n");
	fclose($handle);	
}

function getGuestIP(){
	return $_SERVER["REMOTE_ADDR"];
}


?>