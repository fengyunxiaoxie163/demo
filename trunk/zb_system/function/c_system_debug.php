<?php
/**
 * �������
 * @package Z-BlogPHP
 * @subpackage System/Debug �������
 * @copyright (C) RainbowSoft Studio
 */

/**
 * ���������ʾ
 * @param int $errno ���󼶱�
 * @param string $errstr ������Ϣ
 * @param string $errfile �����ļ���
 * @param int $errline ������
 * @return bool
 */

function Debug_Error_Handler($errno, $errstr, $errfile, $errline) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;

	if(is_readable($errfile)){
		$a = array_slice(file($errfile), max(0,$errline-1), 1, true);
		$s = reset($a);
		if(strpos($s,'@')!==false)return true;
	}

	if(ZBlogException::$iswarning==false){
		if( $errno == E_WARNING )return true;
	}
	if(ZBlogException::$isstrict==false){
		if( $errno == E_NOTICE )return true;
		if( $errno == E_STRICT )return true;
		if( $errno == E_USER_NOTICE )return true;
		if( defined('E_DEPRECATED') && $errno== E_DEPRECATED )return true;
		if( defined('E_USER_DEPRECATED ') && $errno== E_USER_DEPRECATED )return true;
	}
	if(ZBlogException::$isdisable==true)return true;

	$zbe = ZBlogException::GetInstance();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

}

/**
 * �쳣����
 * @param $exception �쳣�¼�
 * @return bool
 */
function Debug_Exception_Handler($exception) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
	if(ZBlogException::$isdisable==true)return true;

	$zbe = ZBlogException::GetInstance();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}

/**
 * ����������
 * @return bool
 */
function Debug_Shutdown_Handler() {
	foreach ($GLOBALS['Filter_Plugin_Debug_Shutdown_Handler'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname();
	}
	if ($error = error_get_last()) {
		$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
		if(ZBlogException::$iswarning==false){
			if( $error['type'] == E_WARNING )return true;
		}
		if(ZBlogException::$isstrict==false){
			if( $error['type'] == E_NOTICE )return true;
			if( $error['type'] == E_STRICT )return true;
			if( $error['type'] == E_USER_NOTICE )return true;
			if( defined('E_DEPRECATED') && $error['type']== E_DEPRECATED )return true;
			if( defined('E_USER_DEPRECATED ') && $error['type']== E_USER_DEPRECATED )return true;
		}
		if(ZBlogException::$isdisable==true)return true;

		$zbe = ZBlogException::GetInstance();
		$zbe->ParseShutdown($error);
		$zbe->Display();
		
		die();
	}
}

/**
 * Class ZBlogException
 *
 */
class ZBlogException {
	private static $_zbe = null;
	public static $isdisable = false;
	private static $_isdisable = null;
	public static $isstrict = false;
	public static $iswarning = true;
	public static $error_id=0;
	public static $error_file=null;
	public static $error_line=null;
	public $type;
	public $message;
	public $file;
	public $line;
	public $errarray=array();

	/**
	 * ���캯�������峣���������
	 */
	function __construct(){
		$this->errarray=array(
			0=>'UNKNOWN',
			1=>'E_ERROR',
			2=>'E_WARNING',
			4=>'E_PARSE',
			8=>'E_NOTICE',
			16=>'E_CORE_ERROR',
			32=>'E_CORE_WARNING',
			64=>'E_COMPILE_ERROR',
			128=>'E_COMPILE_WARNING',
			256=>'E_USER_ERROR',
			512=>'E_USER_WARNING',
			1024=>'E_USER_NOTICE',
			2048=>'E_STRICT',
			4096=>'E_RECOVERABLE_ERROR',
			8192=>'E_DEPRECATED',
			16384=>'E_USER_DEPRECATED',
		);
	}

	/**
	* ��ȡ����
	* @param $name
	* @return mixed
	*/
	public function __get($name){
		if($name=='typeName'){
			if(isset($this->errarray[$this->type])){
				return $this->errarray[$this->type];
			}else{
				return $this->errarray[0];
			}
		}
	}
	
	/**
	* ��ȡ��һʵ��
	* @return ZBlogException
	*/
	static public function GetInstance() {
		if (!isset(self::$_zbe)) {
			self::$_zbe = new ZBlogException;
		}

		return self::$_zbe;
	}

	/**
	* �趨��������
	*/
	static public function SetErrorHook() {
		set_error_handler('Debug_Error_Handler');
		set_exception_handler('Debug_Exception_Handler');
		register_shutdown_function('Debug_Shutdown_Handler');
	}

	/**
	* ���������Ϣ
	*/
	static public function ClearErrorHook() {
		#set_error_handler(create_function('', ''));
		#set_exception_handler(create_function('', ''));
		#register_shutdown_function(create_function('', ''));
		self::$isdisable = true;
	}

	/**
	* ��ֹ�������
	*/
	static public function DisableErrorHook() {
		self::$isdisable = true;
	}
	
	static public function SuspendErrorHook() {
		if(self::$_isdisable !== null)return;
		self::$_isdisable = self::$isdisable;
		self::$isdisable = true;
	}
	static public function ResumeErrorHook() {
		if(self::$_isdisable === null)return;
		self::$isdisable = self::$_isdisable;
		self::$_isdisable = null;
	}
	
	static public function EnableErrorHook() {
		self::$isdisable = false;
	}

	static public function DisableStrict() {
		self::$isstrict = false;
	}

	static public function EnableStrict() {
		self::$isstrict = true;
	}
	
	static public function DisableWarning() {
		self::$iswarning = false;
	}

	static public function EnableWarning() {
		self::$iswarning = true;
	}
	
	static public function Trace($s) {
		Logs($s);
	}

	/**
	* ����������Ϣ
	* @param $type
	* @param $message
	* @param $file
	* @param $line
	*/
	function ParseError($type, $message, $file, $line) {

		$this->type = $type;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;

	}

	/**
	* ����������Ϣ
	* @param $error
	*/
	function ParseShutdown($error) {

		$this->type = $error['type'];
		$this->message = $error['message'];
		$this->file = $error['file'];
		$this->line = $error['line'];
	}

	/**
	* �����쳣��Ϣ
	* @param $exception
	*/
	function ParseException($exception) {

		$this->message = $exception->getMessage();
		$this->type = $exception->getCode();
		$this->file = $exception->getFile();
		$this->line = $exception->getLine();

		if (self::$error_file !== null)
			$this->file = self::$error_file;
		if (self::$error_line !== null)
			$this->line = self::$error_line;

	}

	/**
	* ���������Ϣ
	*/
	function Display() {

		Http500();

		ob_clean();

		require dirname(__FILE__) . '/../defend/error.html';
		RunTime();
		die();
	}

	/**
	* ��ȡ���������Ϣ
	* @param $file
	* @param $line
	* @return array
	*/
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
