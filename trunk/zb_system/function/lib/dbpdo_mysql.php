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
class Dbpdo_MySQL implements iDataBase
{
	
	public $dbpre = null;
	private $db = null;

	public $sql=null;

	function __construct()
	{
		# code...
	}
	
	public function EscapeString($s){
		return addslashes($s);
	}

	function Open($array){
		/*$array=array(
		GetVars('dbmysql_server','POST'),
		GetVars('dbmysql_username','POST'),
		GetVars('dbmysql_password','POST'),
		GetVars('dbmysql_name','POST'),
		GetVars('dbmysql_pre','POST'));
		*/

		//new PDO(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWD);
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',); 
		$db_link = new PDO('mysql:host=' . $array[0] . ';dbname=' . $array[3],$array[1],$array[2],$options);
		$this->db = $db_link;	
		$this->dbpre=$array[4];
		return true;
	}

	function Close(){

	}

	function CreateTable($path){
		$a=explode(';',str_replace('%pre%', $this->dbpre, file_get_contents($path.'zb_system/defend/createtable/mysql.sql')));
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){$this->db->exec($s);}
		}
	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);
		// 遍历出来		
		$results = $this->db->query($query);
		//fetch || fetchAll
		if($results){
			return $results->fetchAll();
		}
		else{
			return array();
		}

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($query);
	}

	function Delete($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($query);
	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		$this->db->exec($query);
		return $this->db->lastInsertId();
	}

}

?>