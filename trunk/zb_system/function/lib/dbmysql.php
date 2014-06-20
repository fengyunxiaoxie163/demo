<?php
/**
 * MySQL数据库操作类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/DataBase 类库
 */
class DbMySQL implements iDataBase {

	/**
	* @var string|null 数据库名前缀
	*/
	public $dbpre = null;
	private $db = null; #数据库连接
	/**
	* @var string|null 数据库名
	*/
	public $dbname = null;
	/**
	 * @var DbSql|null DbSql实例
	 */
	public $sql=null;
	/**
	 * 构造函数，实例化$sql参数
	 */
	function __construct()
	{
		$this->sql=new DbSql($this);
	}

	/**
     * 对字符串进行转义，在指定的字符前添加反斜杠，即执行addslashes函数
     * @use addslashes
	 * @param string $s
	 * @return string
	 */
	public function EscapeString($s){
		return addslashes($s);
	}

	/**
     * 连接数据库
	 * @param array $array 数据库连接配置
	 *              $array=array(
	 *                  'dbmysql_server',
	 *                  'dbmysql_username',
	 *                  'dbmysql_password',
	 *                  'dbmysql_name',
	 *                  'dbmysql_pre',
	 *                  'dbmysql_port',
	 *                  'persistent')
	 * @return bool
	 */
	function Open($array){
		if($array[6]==false){
			$db_link = @mysql_connect($array[0] . ':' . $array[5], $array[1], $array[2]);
		}else{
			$db_link = @mysql_pconnect($array[0] . ':' . $array[5], $array[1], $array[2]);
		}

		if(!$db_link){
			return false;
		} else {
			$this->db = $db_link;
			mysql_query("SET NAMES 'utf8'",$db_link);
			if(mysql_select_db($array[3], $this->db)){
				$this->dbpre=$array[4];
				$this->dbname=$array[3];
				return true;
			} else {
				$this->Close();
				return false;
			}
		}

	}

	/**
	 * 创建数据库
	 * @param string $dbmysql_server
	 * @param string $dbmysql_port
	 * @param string $dbmysql_username
	 * @param string $dbmysql_password
	 * @param string $dbmysql_name
     * @return bool
	 */
	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$db_link = @mysql_connect($dbmysql_server . ':' . $dbmysql_port, $dbmysql_username, $dbmysql_password);
		$this->db = $db_link;
		$this->dbname=$dbmysql_name;
		$s="SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$dbmysql_name'";
		$a=$this->Query($s);
		$c=0;
		if(is_array($a)){
			$b=current($a);
			if(is_array($b)){
				$c=(int)current($b);
			}
		}
		if($c==0){
			mysql_query($this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
			return true;
		}
	}
	
	/**
	 * 关闭数据库连接
	 */
	function Close(){
		mysql_close($this->db);
	}

	/**
	* 执行多行SQL语句
	* @param string $s 以;号分隔的多条SQL语句
	*/
	function QueryMulit($s){
		//$a=explode(';',str_replace('%pre%', $this->dbpre,$s));
		$a=explode(';',$s);
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){
				mysql_query($this->sql->Filter($s));
			}
		}
	}

	/**
	 * 执行SQL查询语句
	 * @param string $query
	 * @return array 返回数据数组
	 */
	function Query($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		$results = mysql_query($this->sql->Filter($query));
		if(mysql_errno())trigger_error(mysql_error(),E_USER_NOTICE);
		$data = array();
		if(is_resource($results)){
			while($row = mysql_fetch_assoc($results)){
				$data[] = $row;
			}
		}else{
			$data[] = $results;
		}
		return $data;
	}

	/**
	 * 更新数据
	 * @param string $query SQL语句
	 * @return resource
	 */
	function Update($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($this->sql->Filter($query));
	}

	/**
	* 删除数据
	* @param string $query SQL语句
	* @return resource
	*/
	function Delete($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($this->sql->Filter($query));
	}

	/**
	* 插入数据
	* @param string $query SQL语句
	* @return int 返回ID序列号
	*/
	function Insert($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($this->sql->Filter($query));
		return mysql_insert_id();
	}

	/**
	* 新建表
	* @param string $tablename 表名
	* @param array $datainfo 表结构
	*/
	function CreateTable($table,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($table,$datainfo));
	}

	/**
	* 删除表
	* @param string $table 表名
	*/
	function DelTable($table){
		$this->QueryMulit($this->sql->DelTable($table));
	}

	/**
	* 判断数据表是否存在
	* @param string $table 表名
	* @return bool
	*/
	function ExistTable($table){
		$a=$this->Query($this->sql->ExistTable($table,$this->dbname));
		if(!is_array($a))return false;
		$b=current($a);
		if(!is_array($b))return false;
		$c=(int)current($b);
		if($c>0){
			return true;
		}else{
			return false;
		}
	}
}
