<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function getguid(){
	$s=trim(com_create_guid(),'{..}');
	return $s;
}


?>