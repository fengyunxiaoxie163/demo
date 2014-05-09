﻿<?php
#///////////////////////////////////////////////////////////////////////////////
#//              Z-BlogPHP 在线安装程序
#///////////////////////////////////////////////////////////////////////////////

error_reporting(0);

header('Content-type: text/html; charset=utf-8');

//ob_start();

$xml=null;

function GetHttpContent($url) {
	$r = null;
	if (function_exists("curl_init")) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
			curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		}
		$r = curl_exec($ch);
		curl_close($ch);
	} elseif (ini_get("allow_url_fopen")) {
		ini_set('default_socket_timeout',120);
		if( version_compare ( PHP_VERSION ,  '5.3.0' ) >=  0 ){
			$r = file_get_contents('compress.zlib://'.$url);
		}else{
			$r = file_get_contents($url);
		}
	}

	return $r;
}


function install0(){

	$d=dirname(__FILE__);

	if(substr((string)decoct(fileperms($d)),-3)<>'755'&&substr((string)decoct(fileperms($d)),-3)<>'777'){
		echo "<p>警告:安装目录权限" . $d . "不是0755或是0777,可能无法运行在线安装程序.</p>";
	}

}


function install1(){

	echo "<p>正在努力地下载数据包...</p>";
	ob_flush();

	$GLOBALS['xml']=GetHttpContent('http://update.zblogcn.com/zblogphp/?install');

	//file_put_contents('release.xml',$GLOBALS['xml']);

}

function install2(){

	echo "<p>正在解压和安装文件...</p>";
	ob_flush();
	if ($GLOBALS['xml']) {
		$xml = simplexml_load_string($GLOBALS['xml'],'SimpleXMLElement');
		$old = umask(0);
		foreach ($xml->file as $f) {
			$filename=str_replace('\\','/',$f->attributes());
			$dirname= dirname($filename);
			mkdir($dirname,0755,true);
			if(PHP_OS=='WINNT'||PHP_OS=='WIN32'||PHP_OS=='Windows'){
				//$fn=iconv("UTF-8","GBK//IGNORE",$filename);
				$fn=$filename;
			}else{
				$fn=$filename;
			}
			file_put_contents($fn,base64_decode($f));
		}
		umask($old);
	} else {
		exit('release.xml不存在!');
	}

}

function install3(){

	#unlink('release.xml');
	@unlink('install.php');
	echo '<script type="text/javascript">window.location="./zb_install/"</script>';
	
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title>Z-BlogPHP 在线安装程序</title>
<style type="text/css"><!--
*{font-size:14px;font-family:'Microsoft YaHei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', 'Heiti SC', STHeiti, SimSun, sans-serif , Verdana, Arial;}
body{margin:0;padding:0;color: #000000;background:#fff;}
h1,h2,h3,h4,h5,h6{font-size:18px;padding:0;color:#3a6ea5;}
h1{font-size:28px;}
input{padding:15px 82px;}
div{position:absolute;left: 50%;top: 50%;margin: -190px 0px 0px -150px;padding:0;overflow:hidden;width:300px;background-color:white;text-align:center;}
--></style>
</head>
<body>
<div>
<h1>Z-BlogPHP 在线安装</h1>
<p><?php echo (($v=GetHttpContent('http://update.zblogcn.com/zblogphp/'))=='')?'不能联网获取Z-BlogPHP！':'最新版本：'.$v;?></p>
<p><img src="http://update.zblogcn.com/zblogphp/loading.png" alt="Z-BlogPHP在线安装" title="Z-BlogPHP在线安装"/></p>
<form method="post" action="#">
<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	install1();
	install2();
	install3();
	die();
}

install0();
?>
<p><input type="submit" value="开始安装" onclick="this.style.display='none';" /></p>
</form>
</div>
</body>
</html>