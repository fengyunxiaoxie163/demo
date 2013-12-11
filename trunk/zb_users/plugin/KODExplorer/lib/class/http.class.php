<?php

/*  
 * http �����ࣻģ��get��post�ύ���ļ��ϴ���cookie�ȵ�
demo
session_start();
$http = new http;
$http->debug = true;
$http->bind_cookie($_SESSION['baidu_cookies']);
$http->post('https://passport.baidu.com/?login&tpl=mn',
	array('username'=>'�ٶ��˺�','password'=>'����'),
	'https://passport.baidu.com/?login&tpl=mn'
);
$http->debug($http);
$http->get('http://hi.baidu.com/');
$http->debug($http->recv,'Content');
$http->get('http://tieba.baidu.com/');
$http->debug($http->recv,'Content');
*/

class http
{
	protected $use_cookie;
	private $cookie;
	private $request_header = array(
		'Host'				=>	'',
		'Connection'		=>	'keep-alive',
		'User-Agent'		=>	'PHP_HTTP/5.2 (compatible; Chrome; MSIE; Firefox; Opera; safari;)',
		//'User-Agent'		=>	'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Sicent1; Sicent1)',
		//'User-Agent'		=>	'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.79 Safari/535.11',
		'Accept'			=>	'*/*',
		'Accept-Encoding'	=>	'gzip,deflate',
		'Accept-Language'	=>	'zh-cn',
		'Cookie'			=>	'',
	);
	
	public $debug;
	private $url;
	private $url_info;
	protected $referer;
	protected $timeout = 5;
	
	protected $header;
	protected $recv;
	protected $status;
	protected $mime;
	protected $length;
	
	public function __construct($use_cookie = true)
	{
		$this->use_cookie = (bool)$use_cookie;
	}
	
	public function set_header($name,$value)
	{
		$this->request_header[$name] = $value;
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	public function debug($var,$name = '')
	{
		if ($this->debug && $var){
			print '<div style="border: 1px solid red; padding: 8px; margin: 8px;"><strong>&lt;<font color="red">'.$name.'</font>&gt; - http debug</strong> ';
			ob_start();
			print_r($var);
			$content = htmlspecialchars(ob_get_contents());
			ob_end_clean();
			print '<pre>'.$content.'</pre>';
			print '</div>';
		}
	}
	
	public function open($method,$url,$form = '',$referer = '')
	{
		$this->prepare($url,$referer);		
		//��socket
		$fp = $this->getfp();
		if(!$fp)return false;
		
		//���������
		$query = self::build_query($form);
		
		//��������
		if( strtolower($method) == 'get' ){
			if( strlen($query) > 0 )
				$this->url_info['query'] = '?'.$query;
				
			$header = $this->build_request('get');
			if($this->debug)$this->debug($header,'Request Header');
			fputs($fp,$header);
		}
		elseif( strtolower($method) == 'post' ){
			$content_type = 'application/x-www-form-urlencoded';
			$header = $this->build_request('post',$content_type,strlen($query));
			if($this->debug)$this->debug($header,'Request Header');
			fputs($fp,$header);
			fputs($fp,$query);
		}
		else{
			$this->error = '��֧�ֵ�����';
			fclose($fp);
			return false;
		}
		
		$this->receive_header($fp);
		$this->process_header(true);
		
		return $fp;
	}
	
	public function get($url,$form='', $referer='')
	{
		$this->prepare($url,$referer);
		
		//���������
		if( $query = self::build_query($form) )
			$this->url_info['query'] = '?'.$query;
		
		//��socket
		$fp = $this->getfp();
		if(!$fp)return false;
		
		//��������
		$header = $this->build_request('get');
		if($this->debug)$this->debug($header,'Request Header');
		fputs($fp,$header);
		
		//��������
		$this->receive_data($fp);
		fclose($fp);
		$this->process_header();
		
		return $this->recv;
	}
	
	public function post($url,$form='',$referer='')
	{
		$this->prepare($url,$referer);		
		//���������
		$query = self::build_query($form);		
		//��socket
		$fp = $this->getfp();
		if(!$fp)return false;
		
		//��������
		$content_type = 'application/x-www-form-urlencoded';
		$header = $this->build_request('post',$content_type,strlen($query));
		if($this->debug)$this->debug($header,'Request Header');
		fputs($fp,$header);
		fputs($fp,$query);
		
		//��������
		$this->receive_data($fp);
		fclose($fp);
		$this->process_header();
		
		return $this->recv;
	}
	
	public function post2($url,$form='',$files=array(),$referer='')
	{
		$this->prepare($url,$referer);		
		//���������
		$boundary = '----WebKitFormBoundary'.md5(microtime());
		$form_data = http_build_query($form,'',"&");
		preg_match_all('/(.*?)=(.*?)(&|$)/',$form_data,$matches);
		$form_data = '';
		foreach($matches[1] as $k=>$v){
			$form_data .= "--{$boundary}\r\n".
				"Content-Disposition: form-data; name=\"".
				urldecode($matches[1][$k])."\"\r\n\r\n".
				urldecode($matches[2][$k])."\r\n";
		}
		$s1	=	"--{$boundary}\r\n".
				"Content-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\n".
				"Content-Type: application/octet-stream\r\n\r\n%s\r\n";
		foreach($files as $file){
			$form_data .= sprintf($s1,$file['name'],basename($file['file']),file_get_contents($file['file']));
		}
		$form_data .= "--{$boundary}--\r\n";
		
		//��socket
		$fp = $this->getfp();
		if(!$fp)return false;
		
		//��������
		$content_type = 'multipart/form-data; boundary='.$boundary;
		$content_length = strlen($form_data);
		$header = $this->build_request('post',$content_type,$content_length);
		if($this->debug)$this->debug($header,'Request Header');
		fputs($fp,$header);
		fputs($fp,$form_data);
		
		//��������
		$this->receive_data($fp);
		fclose($fp);
		$this->process_header();
		
		return $this->recv;
	}
	
	private function prepare($url,$referer)
	{
		$this->error = '';
		$this->url = $url;
		$this->url_info = $this->parse_url($url);
		$this->referer = (string)$referer;
		
		$this->header = '';
		$this->recv = '';
		$this->length = 0;
	}
	
	private function getfp()
	{
		$ui = $this->url_info;
		if( !in_array($ui['scheme'],array('http','https')) ){
			$this->error = '��֧�ֵ�Э�飡';
			return false;
		}		
		$fp = $ui['scheme']=='https' ? 
					fsockopen('ssl://'.$ui['host'],$ui['port'],$errno,$errstr,$this->timeout):
					fsockopen($ui['host'],$ui['port'],$errno,$errstr,$this->timeout);
		
		if(!$fp){
			$this->error = "{$errno} : {$errstr}";
		}
		return $fp;
	}
	
	public static function parse_url($url)
	{
		$url = parse_url($url);
		if(!isset($url['port'])) $url['port'] = ($url['scheme'] == 'https' ? 443 : 80);
		$url['query'] = isset($url['query']) ? ('?'.$url['query']) : '';
		if(!isset($url['path'])){$url['path'] = '/';}
		return $url;
	}
	
	public static function build_query($form)
	{
		//����������
		return ( is_array($form) || is_object($form) ) ? 
			http_build_query($form) : (string)$form ;
	}
	
	private function build_request($method, $content_type='', $content_length='')
	{
		$header = strtoupper($method)." {$this->url_info["path"]}{$this->url_info["query"]} HTTP/1.1\r\n";
		//�޸�Ĭ������ͷ
		$headers = $this->request_header;
		if( !empty($this->referer) ) 
			$headers['Referer'] = $this->referer;
		
		$headers['Host'] = $this->url_info['host'];
		$headers['Content-Length']	= $content_length;
		$headers['Content-Type']	= $content_type;
		
		if($this->use_cookie && $cookie = $this->get_cookie($this->url_info['host'],$this->url_info['path']))
			$headers['Cookie'] = $cookie;
		foreach($headers as $k=>$v){
			if( strlen($v)>0 )
				$header .= "{$k}: {$v}\r\n";
		}
		$header .= "\r\n";		
		return $header;
	}
	
	public function get_cookie($domain = '',$path = '/')
	{
		if(empty($domain))
			$domain = $this->url_info['host'];
		if(empty($path))
			$path = '/';
		
		$domain = explode('.',$domain);
		$count = count($domain);
		
		$pCookie = $this->cookie;
		$Cookie = '';
		for($i=$count-1;$i>=0;$i--){
			//������������
			$subdomain = $domain[$i];
			if(trim($subdomain) == '')continue;
			
			if(isset($pCookie[$subdomain]))
				$pCookie = &$pCookie[$subdomain];
			else
				break;
			
			if( isset($pCookie['']) && is_array($pCookie['']) ){
				foreach($pCookie[''] as $_path=>$_cookie){
					//����·��
					if( strpos($path,$_path)===0 ){
						foreach($_cookie as $key=>$val){
							if(empty($key))contine;
							//$Cookie.=rawurlencode($key).'='.rawurlencode($val).';';
							$Cookie .= $key.'='.$val.';';
						}
					}
				}
			}
			if( isset($pCookie["\0"]) && is_array($pCookie["\0"]) ){
				foreach($pCookie["\0"] as $_path=>$_cookie){
					if( strpos($path,$_path)===0 ){
						foreach($_cookie as $key=>$val){
							if(empty($key))contine;
							//$Cookie.=rawurlencode($key).'='.rawurlencode($val).';';
							$Cookie .= $key.'='.$val.';';
						}
					}
				}
			}
		}
		return $Cookie;
	}
	
	public static function fread_my($fp,$length)
	{
		$data = '';
		$length = intval($length);
		
		while( strlen($data) < $length && !feof($fp) )
			$data .= fread($fp, $length - strlen($data));
		
		return $data;
	}
	
	private function receive_data($fp)
	{
		//socket_set_timeout($fp,$this->recTimeOut);
		//��ȡ��Ӧͷ		
		$this->receive_header($fp);
		if( strlen($this->header) == 0 ) return ;
		//chunkģʽ
		if(preg_match("|transfer-encoding:\s*?chunked|i",$this->header))
		{
			while( !feof($fp) && hexdec($pack_len = fgets($fp)) ){
				$this->recv .= self::fread_my($fp,hexdec($pack_len));
				fgets($fp);//��ȡ\r\n
			}
			//fgets($fp);//��ȡ\r\n
			return;
		}
		
		//keep-alive ��ʽ�Ĵ���;
		$connection = $this->request_header['Connection'];
		if(strtolower($connection) == "keep-alive")
		{
			if(preg_match("|content-length:\s*?([0-9]{1,})|i",$this->header,$length)){
				$length=(int)$length[1];
				$this->length = $length;
				$this->recv = self::fread_my($fp, $length);
				return;
			}
					
			else if(preg_match("|Connection:\s*?Close|i",$this->header)){
				//������ǿ��ʹ��close��ʽ
				NULL;//����û��return;�Ա������close��ʽ��������
			}
			else{
				$this->error = '�޷������������ݣ�';
				return;
			}
		}
		//close ��ʽ
		while(true){
			if(feof($fp))break;
			$this->recv .= fread($fp,8192);
		}
	}
	
	private function receive_header($fp)
	{
		do{
			$this->header .= fgets($fp);
		}
		while(!strpos($this->header,"\r\n\r\n") && !feof($fp));
		if($this->debug)$this->debug($this->header,'Response Header');		
		//��ȡ��Ӧͷ
		$this->header = substr($this->header,0,-4);
	}
	
	private function process_header($open_mode = false)
	{
		$http_headers = explode("\r\n",$this->header);		
		preg_match('|HTTP/1.[10]\s*([0-9]{1,3})\s*(.*)$|i',$http_headers[0],$match);
		$this->status = (int)$match[1];
		unset($http_headers[0]);
		
		foreach($http_headers as $header){
			list($header_name,$header_content) = explode(":",$header,2);
			$header_name	=	strtolower(trim($header_name));
			$header_content	=	trim($header_content);
			
			//��ģʽֻ����cookie��mime��Ϣ,��������
			if($open_mode && !in_array($header_name,array('set-cookie','content-type','content-length')))
				continue;
			
			switch($header_name){
				case "content-type":
					$mime = explode(';',$header_content,2);
					$this->mime = trim($mime[0]);
					unset($mime);
					break;
				case "set-cookie":
					if($this->use_cookie)
						$this->parse_cookie($header_content);
					break;
				case "content-length":
					$this->length = (int)$header_content;
					break;
				case "content-encoding":
					if($header_content == 'gzip')
					{
						$this->recv = gzinflate(substr($this->recv,10,-8));
					}
					elseif($header_content == 'deflate')
					{
						$this->recv = gzinflate($this->recv);
					}
					$decode_length = strlen($this->recv);
					break;
				case "location":
					$location = self::convert_url($header_content,$this->url);
					break;
				default:
					continue;
			}
		}
		
		//����ٸ���length,ȷ���ǽ����ĳ���
		if(isset($decode_length))
			$this->length = $decode_length;
		
		if(in_array($this->status,array(302,301)) && !empty($location) )
			$this->get($location,'',$this->referer);
		return ;
	}
	
	private function parse_cookie($header)
	{
		//ƥ��cookie������ֵ
		preg_match('|([^=]*?)=([^;]*)|',$header,$_cookie);
		
		//$cookie_name = rawurldecode(trim($_cookie[1]));
		//$cookie_val = rawurldecode(trim($_cookie[2]));
		$cookie_key = trim($_cookie[1]);
		$cookie_val = trim($_cookie[2]);
		
		//��header��ƥ������
		if(preg_match('|domain=([^;]*)|i',$header,$domain))	{
			$domain = $domain[1];
			$global_flag = true;
		}
		else{
			$domain = $this->url_info['host'];
			$global_flag = false;
		}
		
		//��header��ƥ��·��
		if(preg_match('|path=([^;]*)|i',$header,$path)){
			$path = $path[1];
		}
		else{
			$path = $this->url_info['path'];
			$path = substr($path,0,strrpos($path,'/'));
			if(empty($path))$path = '/';
		}
		
		//��header��ƥ��ʱ��
		if(preg_match('|expires=([^;]*)|i',$header,$expires)){
			$expires = $expires[1];
			
			//32λ��ʱ����������
			if(preg_match('|\d{2}-[a-z]{3,4}-(\d*)|i',$expires,$match)){
				if($match[1] > 38 && $match[1] < 100)
					$expires = '2038-01-18';
			}
		}
		else{
			$expires = '2038-01-18';
		}
		
		//��ȡ�洢cookie�ı���������
		$domain = explode('.',$domain);
		$count = count($domain);
		$pCookie = &$this->cookie;
		for($i=$count-1;$i>=0;$i--){
			$subdomain = $domain[$i];
			if(trim($subdomain) == '')continue;
			$pCookie = &$pCookie[$subdomain];
		}
		
		if($global_flag)
			$pCookie = &$pCookie[''];
		else
			$pCookie = &$pCookie["\0"];
			
		$pCookie = &$pCookie[$path];
		
		if(strtotime($expires)<time())
			unset($pCookie[$cookie_key]);
		else
			$pCookie[$cookie_key] = $cookie_val;
	}
	
	/**
	 * url���·��ת����·��
	 **/
	static public function convert_url($url,$pos)
	{
		if(empty($url) || strpos($url,'#') ===0 )
			return $pos;
		elseif(strpos($url,'http://') === 0 || strpos($url,'https://')===0)
			return $url;
		else{
			$p = parse_url($pos);
			$prefix  = $p['scheme'].'://'.$p['host'];
			$prefix .= (isset($p['port']) && $p['port']!='80' ? ':'.$p['port'] : '');
			
			if(strpos($url,'/')===0)
				//����·��
				return $prefix.$url;
			elseif(strpos($url,'?')===0){
				//���ʺſ�ʼ,��$url��Ϊ�������ӵ�$pos��
				return $prefix.'/'.$p['path'].$url;
			}
			else{
				//���·��
				$p1 = (empty($p['path']) || $p['path'] == '/')? array() : explode('/',substr($p['path'],1));
				array_pop($p1);
				
				list($p2,$q) = explode('?',$url,2);
				$p2 = explode('/',$p2);
				while(($e = array_shift($p2)) !== NULL){
					if($e == '.')
						continue;
					elseif($e == '..')
						array_pop($p1);
					else
						array_push($p1,$e);
				}
				$path = join('/',$p1);
				return $prefix . '/' . $path . ($q ? '?'.$q : '') ;
			}
		}
	}
	
	public function bind_cookie(&$ptr)
	{
		$this->cookie = &$ptr;
	}
}
