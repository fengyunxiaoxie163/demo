<?php
return function($author, $content, &$sv, $config_sv, $config_array){
	
	$matches = array();
	
	$regex = $config_array['BLACK_LIST']['BADWORD_LIST']['VALUE'];
	$regex = "/" . $regex . "/si";
	
	preg_match_all($regex, $content, $matches);
	
	//var_dump($matches[0]);
	$count = count($matches[0]);
	$sv += $config_sv * $count;
};