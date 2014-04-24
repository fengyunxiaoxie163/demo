<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version       2.0 2013-06-14
 */

function error_handler($errno, $errstr, $errfile, $errline) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
	if(ZBlogException::$isdisable==true)return true;
	//ob_clean();
	$zbe = ZBlogException::GetInstance();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

}

function exception_handler($exception) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
	if(ZBlogException::$isdisable==true)return true;
	//ob_clean();
	$zbe = ZBlogException::GetInstance();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}

function shutdown_error_handler() {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
	if ($error = error_get_last()) {
		if( $error['type'] == E_NOTICE )return true;
		if( $error['type'] == E_STRICT )return true;
		if( $error['type'] == E_USER_NOTICE )return true;
		if( defined('E_DEPRECATED') && $error['type']== E_DEPRECATED )return true;
		if( defined('E_USER_DEPRECATED ') && $error['type']== E_USER_DEPRECATED )return true;
		if(ZBlogException::$isdisable==true)return true;
		//ob_clean();
		$zbe = ZBlogException::GetInstance();
		$zbe->ParseShutdown($error);
		$zbe->Display();
		
		die();
	}
}

class ZBlogException {
	static private $_zbe = null;
	static public $isdisable = false;
	public $type;
	public $message;
	public $file;
	public $line;
	public $errarray=array();

	function __construct(){
		$this->errarray[0]='UNKNOWN';
		$this->errarray[1]='E_ERROR';
		$this->errarray[2]='E_WARNING';
		$this->errarray[4]='E_PARSE';
		$this->errarray[8]='E_NOTICE';
		$this->errarray[16]='E_CORE_ERROR';
		$this->errarray[32]='E_CORE_WARNING';
		$this->errarray[64]='E_COMPILE_ERROR';
		$this->errarray[128]='E_COMPILE_WARNING';
		$this->errarray[256]='E_USER_ERROR';
		$this->errarray[512]='E_USER_WARNING';
		$this->errarray[1024]='E_USER_NOTICE';
		$this->errarray[2048]='E_STRICT';
		$this->errarray[4096]='E_RECOVERABLE_ERROR';	
		$this->errarray[8192]='E_DEPRECATED';
		$this->errarray[16384]='E_USER_DEPRECATED';
	}

	public function __get($name){
		if($name=='typeName'){
			if(isset($this->errarray[$this->type])){
				return $this->errarray[$this->type];
			}else{
				return $this->errarray[0];
			}
		}
	}
	
	static public function GetInstance() {
		if (!isset(self::$_zbe)) {
			self::$_zbe = new ZBlogException;
		}

		return self::$_zbe;
	}

	static public function SetErrorHook() {
		set_error_handler('error_handler');
		set_exception_handler('exception_handler');
		register_shutdown_function('shutdown_error_handler');
	}

	static public function ClearErrorHook() {
		#set_error_handler(create_function('', ''));
		#set_exception_handler(create_function('', ''));
		#register_shutdown_function(create_function('', ''));
		self::$isdisable = true;
	}

	static public function DisableErrorHook() {
		self::$isdisable = true;
	}

	static public function EnableErrorHook() {
		self::$isdisable = false;
	}

	static public function Trace($s) {
		Logs($s);
	}

	function ParseError($type, $message, $file, $line) {

		$this->type = $type;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;

	}

	function ParseShutdown($error) {

		$this->type = $error['type'];
		$this->message = $error['message'];
		$this->file = $error['file'];
		$this->line = $error['line'];
	}

	function ParseException($exception) {

		$this->message = $exception->getMessage();
		$this->type = $exception->getCode();
		$this->file = $exception->getFile();
		$this->line = $exception->getLine();

		if (ZBlogPHP::$error_file !== null)
			$this->file = ZBlogPHP::$error_file;
		if (ZBlogPHP::$error_line !== null)
			$this->line = ZBlogPHP::$error_line;

	}

	function Display() {

		Http500();
		ob_end_clean();
		$zbp = ZBlogPHP::GetInstance();
		$zbp->StartGzip();
		require $GLOBALS['blogpath'] . 'zb_system/defend/error.html';
		RunTime();
	}

	function get_code($file, $line) {
		if(strcasecmp($file,'Unknown')==0)return array();
		if(!is_readable($file))return array();
		$aFile = array_slice(file($file), max(0, $line - 5), 10, true);
		foreach ($aFile as &$sData) { //&$ = ByRef
			$sData = htmlspecialchars($sData);
		}
		return $aFile;
	}

}
