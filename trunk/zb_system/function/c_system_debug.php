<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

set_error_handler("error_handler");
set_exception_handler('exception_handler');
register_shutdown_function('shutdown_error_handler');



function error_handler($errno, $errstr, $errfile, $errline ){

	#throw new ErrorException($errstr,0,$errno, $errfile, $errline);
	//die();

	ob_clean();		
	$zbe=new ZblogException();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

 }




function exception_handler($exception){

	ob_clean();
	$zbe=new ZblogException();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}




function shutdown_error_handler(){
	if ($error = error_get_last()) {

		ob_clean();
		$zbe=new ZblogException();
		$zbe->ParseShutdown($error);
		$zbe->Display();
		die();
	}
}


/**
* 
*/
class ZblogException
{
	
	public $type;
	public $message;
	public $file;
	public $line;


	function ParseError($type,$message,$file,$line){
		$this->type=$type;
		$this->message=$message;
		$this->file=$file;
		$this->line=$line;	
	}	

	function ParseShutdown($error){

		$this->type=$error['type'];
		$this->message=$error['message'];
		$this->file=$error['file'];
		$this->line=$error['line'];	

	}

	function ParseException($exception){

		$this->message=$exception->getMessage();
		$this->type=$exception->getCode();
		$this->file=$exception->getFile();
		$this->line=$exception->getLine();
	}



	function Display(){

		$e='';
		$e.= 'type:<br/>'.$this->type;
		$e.= "<hr/>";
		$e.= 'message:<br/>'.$this->message;
		$e.= "<hr/>";
		$e.= 'file:<br/>'.$this->file;
		$e.= "<hr/>";
		$e.= 'line:<br/>'.$this->line;		

		$h=file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/error.html');
		$h=str_replace('<#ZC_BLOG_HOST#>', $GLOBALS['bloghost'], $h);
		$h=str_replace('<#ZC_BLOG_TITLE#>', $GLOBALS['option']['ZC_BLOG_TITLE'], $h);
		$h=str_replace('<#BlogTitle#>', $GLOBALS['lang']['ZC_MSG045'], $h);		
		$h=str_replace('<#ERROR#>', $e, $h);
		echo $h;
		echo RunTime();
	}


}

?>