<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */



/**
* 
*/
class DbMySQL implements iDataBase
{
	
	public $dbpre = null;
	private $db = null;

	function __construct()
	{
		# code...
	}

	function Open($array){

		if ($this->db = mysql_connect($array[0], $array[1], $array[2])) {
			if(mysql_select_db($array[3], $this->db)){
				$this->dbpre=$array[4];
				return true;
			} else {
				$this->Close();
				return false;
			}
		} else {
			return false;
		}


	}

	function Close(){

	}

	function CreateTable(){
		foreach ($GLOBALS['TableSql_MySQL'] as $s) {
			$s=str_replace('%pre%', $this->dbpre, $s);
			mysql_query($s);
		}
	}

	function Query(){
//mysql_query('CREATE DATABASE `zblog`'); // 创建数据库
	}

	function Update(){

	}

	function Delete(){

	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($query);
		return mysql_insert_id();
	}

}

?>