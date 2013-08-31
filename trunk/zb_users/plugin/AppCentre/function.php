<?php

function AppCentre_SubMenus($id){
	//m-now

	echo '<a href="main.php"><span class="m-left '.($id==1?'m-now':'').'">浏览在线应用</span></a>';
	echo '<a href="main.php?method=check"><span class="m-left '.($id==2?'m-now':'').'">检查应用更新</span></a>';
	echo '<a href="update.php"><span class="m-left '.($id==3?'m-now':'').'">系统更新与校验</span></a>';

	echo '<a href="setting.php"><span class="m-right '.($id==4?'m-now':'').'">设置</span></a>';
	echo '<a href="plugin_edit.php"><span class="m-right '.($id==5?'m-now':'').'">新建插件</span></a>';
	echo '<a href="theme_edit.php"><span class="m-right '.($id==6?'m-now':'').'">新建主题</span></a>';
}

function GetCheckQueryString(){
	global $zbp;
	$check= '';
	$app=new app;			
	if($app->LoadInfoByXml('theme', $zbp->theme)==true){
		$check.=$app->id . ':' .$app->modified . ';';
	}
	foreach (explode('|',$zbp->option['ZC_USING_PLUGIN_LIST']) as  $id) {
		$app=new app;
		if($app->LoadInfoByXml('plugin', $id)==true){
			$check.=$app->id . ':' .$app->modified . ';';
		}
	}
	return $check;
}

function Server_Open($method){
	global $zbp;

	switch ($method) {
		case 'down':
			Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','ScriptError',PLUGIN_EXITSIGNAL_RETURN);
			header('Content-type: application/x-javascript; Charset=utf8');
			ob_clean();
			$s=Server_SendRequest(APPCENTRE_URL .'?down=' . GetVars('id','GET'));
			if(App::UnPack($s)){
				$zbp->SetHint('good','下载APP并解压安装成功!');
			};
			die();
			break;		
		case 'search':
			if(trim(GetVars('q','GET'))=='')continue;
			$s=Server_SendRequest(APPCENTRE_URL .'?search=' . urlencode(GetVars('q','GET')));
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'view':
			$s=Server_SendRequest(APPCENTRE_URL .'?'. $_SERVER['QUERY_STRING']);
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'check':
			$s=Server_SendRequest(APPCENTRE_URL .'?check=' . urlencode(GetCheckQueryString())) . '';
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'checksilent':
			header('Content-type: application/x-javascript; Charset=utf8');
			ob_clean();
			$s=Server_SendRequest(APPCENTRE_URL .'?silent=1&check=' . urlencode(GetCheckQueryString())) . '';
			if($s!=0){
				echo '$(".main").prepend("<div class=\'hint\'><p class=\'hint hint_tips\'>提示:有'.$s.'个应用需要更新,请在应用中心更新.</p></div>")';
			}
			die();
			break;
		case 'vaild':
			$data=array();
			$data["username"]=GetVars("app_username");
			$data["password"]=md5(GetVars("app_password"));
			$s=Server_SendRequest(APPCENTRE_URL .'?vaild',$data);
			return $s;
			break;
		case 'submitpre':
			$s=Server_SendRequest(APPCENTRE_URL .'?submitpre=' . urlencode(GetVars('id')));
			return $s;
		case 'submit':
			$app=New App;
			$app->LoadInfoByXml($_GET['type'],$_GET['id']);
			$data["zba"]=$app->Pack();
			$s=Server_SendRequest(APPCENTRE_URL .'?submit=' . urlencode(GetVars('id')),$data);
			return $s;
		default:
			# code...
			break;
	}

}



function Server_SendRequest($url,$data=array()){
	global $zbp;

	$un=$zbp->Config('AppCentre')->username;
	$ps=$zbp->Config('AppCentre')->password;
	$c='';
	if($un&&$ps){
		$c="username=".urlencode($un) ."; password=".urlencode($ps);
	}

	if($data){//POST
		$data=http_build_query($data);
		$opts=array(
			'http'=>array(
				'method'=>'POST',
				'header'=>"Content-Type:application/x-www-form-urlencoded\r\n".
					'Content-Length: '.strlen($data)."\r\n".
					"Cookie: ".$c."\r\n",
				'user_agent'=> 'ZBlogPHP/' . substr(ZC_BLOG_VERSION,-6,6) . ' '. GetGuestAgent(),
				'content'=>$data
			)
		);
		$content=stream_context_create($opts);
	}else{//GET
		$opts=array(
			'http'=>array(
				'method'=>'GET',
				'header'=>"Cookie: ".$c."\r\n",
				'user_agent'=> 'ZBlogPHP/' . substr(ZC_BLOG_VERSION,-6,6) . ' '. GetGuestAgent(),
			)
		);
		$content=stream_context_create($opts);
	}

	return file_get_contents($url,false,$content);

}



?>