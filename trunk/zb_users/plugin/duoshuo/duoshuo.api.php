<?php

class duoshuo_api
{
	function implode2($ary)
	{
		$str = '';
		if(!$ary['ary']) $ary['ary'] = array();
		if(!$ary['before']) $ary['before'] = '';
		if(!$ary['after']) $ary['after'] = '';
		
		for($i = 0; $i < count($ary['ary']); $i++)
		{
			$str .= $ary['before'] . $ary['ary'][$i] . $ary['after'];
			if($i < count($ary['ary'])-1) $str .= $ary['split_tag'];  
		}

		return $str;
		
	}
	
	function create($meta)
	{
		global $zbp;
		global $duoshuo;
		$zbp->LoadMembers();
		$c = new comment();
		$date = time($meta->meta->created_at);

    	$c->Name = $meta->meta->author_name;

		if (isset($zbp->members[$meta->meta->author_key])) $c->AuthorID = $meta->meta->author_key;
		$c->Email = $meta->meta->author_email;
		$c->HomePage = $meta->meta->author_url;
		$c->IP = $meta->meta->ip;
		$c->PostTime = $date;
	    $c->Content = $meta->meta->message;
		$c->Agent = $meta->meta->agent;
    	$c->LogID = $meta->meta->thread_key;

		//FilterComment
		if(!CheckRegExp($c->Name,'[username]')) $c->Name = $zbp->lang['user_level_name'][5];
		if($c->Email && (!CheckRegExp($c->Email,'[email]'))) $c->Email = 'null@null.com';
		if($c->HomePage && (!CheckRegExp($c->HomePage,'[homepage]'))) $c->HomePage = $zbp->host;

		$c->Name=substr($c->Name, 0,20);
		$c->Email=substr($c->Email, 0,30);
		$c->HomePage=substr($c->HomePage, 0,100);
		$c->Content=TransferHTML($c->Content,'[nohtml]');
		$c->Content=substr($c->Content, 0,1000);
		$c->Content=trim($c->Content);
		if(strlen($c->Content)==0) return;
	
	    //写入数据库
		if ($meta->meta->parent_id > 0) {
			$sql = $zbp->db->sql->Select(
				$duoshuo->db['comment'],
				array('ds_cmtid'),
				array(array('=','ds_key',$meta->meta->parent_id)),
				null,
				array(1),
				null
			);
			
			$result = $zbp->db->Query($sql);
			if(count($result)>0) $c->ParentID = $result[0]['ds_cmtid'];
			$sql = $zbp->db->sql->Select('%pre%comment',array('comm_RootID'),array(array('=','comm_ID',$c->ParentID)),null,null,null);
			$result = $zbp->db->Query($sql);
			if(count($result)>0)
			{
				$c->RootID = $result[0]['comm_RootID'];
				if ((int)$c->RootID == 0) $c->RootID = $c->ParentID;
			}
			
		}

		if ($c->Save())
		{
			$sql = $zbp->db->sql->Insert(
				$duoshuo->db['comment'],
				array('ds_key'=>$meta->meta->post_id , 'ds_cmtid'=>$c->ID)
			);
			$zbp->db->Insert($sql);
		}
		
		$c = NULL; 
		return $meta->log_id;
	}
	
	function approve($meta)
	{
		global $zbp;
		global $duoshuo;
		$sql = $zbp->db->sql->Update(
			'%pre%Comment,' . $duoshuo->db['comment'],
			array('comm_IsChecking' => 0),
			array(
				array('=',$duoshuo->db['comment'].'.ds_cmtid','%pre%comment.comm_id'),
				array('custom','('.$duoshuo->db['comment'].'.ds_key) In ' . $this->implode2(array(
					'ary' => $meta->meta,
					'before' => "'",
					'after' => "'",
					'split_tag' => ','
				)) . ')')
			)
		);
		$zbp->db->Update($sql);
	}
	
	function spam($meta)
	{
		global $zbp;
		global $duoshuo;
		$sql = $zbp->db->sql->Update(
			'%pre%Comment,' . $duoshuo->db['comment'],
			array('comm_IsChecking' => 1),
			array(
				array('=',$duoshuo->db['comment'].'.ds_cmtid','%pre%comment.comm_id'),
				array('custom','('.$duoshuo->db['comment'].'.ds_key) In ' . $this->implode2(array(
					'ary' => $meta->meta,
					'before' => "'",
					'after' => "'",
					'split_tag' => ','
				)) . ')')
			)
		);
		$zbp->db->Update($sql);
	}
	
	function delete_forever($meta)
	{
		global $zbp;
		global $duoshuo;
		$ary_cmtid = array();
		$ds_keylist = $this->implode2(array('ary' => $meta->meta,'before' => "'",'after' => "'",'split_tag' => ','));
		//我靠好麻烦，MySQL和SQLite还要写不同的，不如直接通用（虽然效率低）
		$sql = $zbp->db->sql->Select(
			$duoshuo->db['comment'],
			'ds_cmtid',
			array('custom','ds_key In (' . $ds_keylist . ')'),
			null,
			null,
			null
		);
		$result = $zbp->db->Query($sql);
		if(count($result)>0)
		{
			foreach($rs as $result) $ary_cmtid[] = $rs['ds_cmtid'];
			$zbp->db->Delete($zbp->db->sql->Delete('%pre%Comment',array('custom','comm_ID In (' .$this->implode2(array('ary' => $ary_cmtid,'before' => "'",'after' => "'",'split_tag' => ',')) . ')')));
			$zbp->db->Delete($zbp->db->sql->Delete($duoshuo->db['comment'],array('custom','ds_key In (' . $ds_keylist . ')')));
		}
		
	}
	
	function sync()
	{
		global $duoshuo;
		global $zbp;
		$duoshuo->init();
		$ajax = new NetworkFactory();
		$ajax = $ajax->Create();
		if(!$ajax) throw new Exception('主机没有开启网络功能');

		$url = ''; $data = array(); $s = 0; $log_id = '';
		$url = 'http://' . $duoshuo->cfg->api_hostname . '/' .  $duoshuo->url['log']['list'];
		$url .= '?limit=50&short_name=' . urlencode($duoshuo->cfg->short_name);
		$url .= '&secret=' . urlencode($duoshuo->cfg->secret);
		if ((int)$duoshuo->cfg->log_id>0) $url .= '&since_id=' . $duoshuo->cfg->log_id;
		else $duoshuo->cfg->log_id = 0;

		$bol_cc_fix = $duoshuo->cfg->cc_fix;
		$ajax->open('GET', $url);
		$ajax->send();
		
		$json = json_decode($ajax->responseText);
		//var_dump($json);exit;
		foreach($json->response as $i)
		{
			
			$func_name = str_replace('-','_',$i->action);
			if($func_name == 'delete') $func_name = 'spam';
			if($func_name == 'update') $func_name = 'delete_forever';
			$log_id = $this->$func_name($i);
			if($log_id) $duoshuo->cfg->log_id = $log_id;
			if(!$bol_cc_fix)
			{
//				if($i->meta->thread_key) 
			}
		}
		
		$zbp->SaveConfig('duoshuo');
		if(count($json->response)>=49) $this->sync();
	}
}


