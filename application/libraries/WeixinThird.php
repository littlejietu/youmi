<?php

define('ACCOUNT_PLATFORM_API_ACCESSTOKEN', 'https://api.weixin.qq.com/cgi-bin/component/api_component_token');
define('ACCOUNT_PLATFORM_API_PREAUTHCODE', 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=');
define('ACCOUNT_PLATFORM_API_LOGIN', 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s');
define('ACCOUNT_PLATFORM_API_QUERY_AUTH_INFO', 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=');
define('ACCOUNT_PLATFORM_API_ACCOUNT_INFO', 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=');
define('ACCOUNT_PLATFORM_API_REFRESH_AUTH_ACCESSTOKEN', 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=');
define('ACCOUNT_PLATFORM_API_OAUTH_CODE', 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&component_appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=%s#wechat_redirect');
define('ACCOUNT_PLATFORM_API_OAUTH_USERINFO', 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s&component_appid=%s#wechat_redirect');
define('ACCOUNT_PLATFORM_API_OAUTH_INFO', 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&component_appid=%s&code=%s&grant_type=authorization_code&component_access_token=');

$CI =& get_instance();
$CI->load->library('WeixinThirdAuth');

class WeixinThird extends WeixinThirdAuth {
	public $appid;
	public $appsecret;
	public $encodingaeskey;
	public $token;
	public $refreshtoken;
	public $account;
	// public $weixinThirdAuth;

	function __construct($cidOrAccount = array()) {
		$CI =& get_instance();
		$account = $cidOrAccount;
		if(!empty($cidOrAccount)){
			
			if (is_array($cidOrAccount)) {
				$account = $cidOrAccount;
			} else {
				$CI->load->model('oil/Company_config_model');
				$account = $CI->Company_config_model->get_by_id($cidOrAccount);
			}
		}

		$CI->load->helper('ihttp');
		//$this->weixinThirdAuth = new WeixinThirdAuth();
		$this->appid = C('component_appid');
		$this->appsecret = C('component_appsecret');
		$this->token = C('component_message_token');
		$this->encodingaeskey = C('component_message_key');
		$this->account = $account;
		if (!empty($this->account['wx_appid']) && $this->account['wx_appid'] == 'wx570bc396a51b8ff8') {
			$this->account['wx_appid'] = $this->appid;
			$this->openPlatformTestCase();
		}
		if(!empty($this->account['wx_appid']))
			$this->account['account_appid'] = $this->account['wx_appid'];
		$this->account['key'] = $this->appid;
	}


	function getComponentAccesstoken() {
		$accesstoken = rkcache('account:component:assesstoken');
		if (empty($accesstoken) || empty($accesstoken['value']) || $accesstoken['expire'] < time()) {
			// $ticket = 'ticket@@@mAONMPArEtTMbIu1p8w-GQYqDXZhHpS7wQWtxvHN-lqEughksY2lhrH3Z2MHok5dC4ivgwJVPL6jUaKWdopWPg';
			$ticket = rkcache('account:component:ticket');
			if (empty($ticket)) {
				return error(1, '缺少接入平台关键数据，等待微信开放平台推送数据，请十分钟后再试或是检查“授权事件接收URL”..');
			}
			$data = array(
				'component_appid' => $this->appid,
				'component_appsecret' => $this->appsecret,
				'component_verify_ticket' => $ticket,
			);
			$response = $this->request(ACCOUNT_PLATFORM_API_ACCESSTOKEN, $data);
			if (is_error($response)) {
				$errormsg = $this->error_code($response['errno'], $response['message']);
				return error($response['errno'], $errormsg);
			}
			$accesstoken = array(
				'value' => $response['component_access_token'],
				'expire' => time() + intval($response['expires_in']),
			);
			wkcache('account:component:assesstoken', $accesstoken);
		}
		return $accesstoken['value'];
	}

	function getPreauthCode() {
		$preauthcode = rkcache('account:preauthcode');
		if (true || empty($preauthcode) || empty($preauthcode['value']) || $preauthcode['expire'] < time()) {
			$component_accesstoken = $this->getComponentAccesstoken();
			if (is_error($component_accesstoken)) {
				return $component_accesstoken;
			}
			$data = array(
				'component_appid' => $this->appid
			);
			$response = $this->request(ACCOUNT_PLATFORM_API_PREAUTHCODE . $component_accesstoken, $data);
			if (is_error($response)) {
				return $response;
			}
			$preauthcode = array(
				'value' => $response['pre_auth_code'],
				'expire' => time() + intval($response['expires_in']),
			);
			wkcache('account:preauthcode', $preauthcode);
		}
		return $preauthcode['value'];
	}

	public function getAuthInfo($code) {
		$component_accesstoken = $this->getComponentAccesstoken();
		if (is_error($component_accesstoken)) {
			return $component_accesstoken;
		}
		$post = array(
			'component_appid' => $this->appid,
			'authorization_code' => $code,
		);
		$response = $this->request(ACCOUNT_PLATFORM_API_QUERY_AUTH_INFO . $component_accesstoken, $post);
		if (is_error($response)) {
			return $response;
		}
		$this->setAuthRefreshToken($response['authorization_info']['authorizer_refresh_token']);
		return $response;
	}

	public function getAccountInfo($appid = '') {
		$component_accesstoken = $this->getComponentAccesstoken();
		if (is_error($component_accesstoken)) {
			return $component_accesstoken;
		}
		$appid = !empty($appid) ? $appid : $this->account['account_appid'];
		$post = array(
			'component_appid' => $this->appid,
			'authorizer_appid' => $appid,
		);
		$response = $this->request(ACCOUNT_PLATFORM_API_ACCOUNT_INFO . $component_accesstoken, $post);
		if (is_error($response)) {
			return $response;
		}
		return $response;
	}

	public function getAccessToken() {
		$cachename = 'account:auth:accesstoken:'.$this->account['account_appid'];
		$auth_accesstoken = rkcache($cachename);
		if (empty($auth_accesstoken) || empty($auth_accesstoken['value']) || $auth_accesstoken['expire'] < time()) {
			$component_accesstoken = $this->getComponentAccesstoken();
			if (is_error($component_accesstoken)) {
				return $component_accesstoken;
			}
			$this->refreshtoken = $this->getAuthRefreshToken();
			$data = array(
				'component_appid' => $this->appid,
				'authorizer_appid' => $this->account['account_appid'],
				'authorizer_refresh_token' => $this->refreshtoken,
			);
			$response = $this->request(ACCOUNT_PLATFORM_API_REFRESH_AUTH_ACCESSTOKEN . $component_accesstoken, $data);
			if (is_error($response)) {
				return $response;
			}
			if ($response['authorizer_refresh_token'] != $this->refreshtoken) {
				$this->setAuthRefreshToken($response['authorizer_refresh_token']);
			}
			$auth_accesstoken = array(
				'value' => $response['authorizer_access_token'],
				'expire' => time() + intval($response['expires_in']),
			);
			wkcache($cachename, $auth_accesstoken);
		}
		return $auth_accesstoken['value'];
	}

	public function fetch_token() {
		return $this->getAccessToken();
	}

	public function getAuthLoginUrl() {
		$preauthcode = $this->getPreauthCode();
		if (is_error($preauthcode)) {
			$authurl = "javascript:alert('{$preauthcode['message']}');";
		} else {
			$authurl = sprintf(ACCOUNT_PLATFORM_API_LOGIN, $this->appid, $preauthcode, urlencode(SELLER_SITE_URL . '/auth/forward'));
		}
		return $authurl;
	}

	public function getOauthCodeUrl($callback, $state = '') {
		return sprintf(ACCOUNT_PLATFORM_API_OAUTH_CODE, $this->account['account_appid'], $this->appid, $callback, $state);
	}

	public function getOauthUserInfoUrl($callback, $state = '') {
		return sprintf(ACCOUNT_PLATFORM_API_OAUTH_USERINFO, $this->account['account_appid'], $callback, $state, $this->appid);
	}

	public function getOauthInfo($code = '',$assoc=true) {
		$component_accesstoken = $this->getComponentAccesstoken();
		if (is_error($component_accesstoken)) {
			return $component_accesstoken;
		}
		$apiurl = sprintf(ACCOUNT_PLATFORM_API_OAUTH_INFO . $component_accesstoken, $this->account['account_appid'], $this->appid, $code);
		$response = $this->request($apiurl);
		if (is_error($response)) {
			return $response;
		}
		wkcache('account:oauth:refreshtoken:'.$this->account['account_appid'], $response['refresh_token']);
		return $response;
	}
	
	public function getJsApiTicket(){
		$cachename = 'account:jsapi_ticket'.$this->account['account_appid'];
		$js_ticket = rkcache($cachename);
		if (empty($js_ticket) || empty($js_ticket['value']) || $js_ticket['expire'] < time()) {
			$access_token = $this->getAccessToken();
			if(is_error($access_token)){
				return $access_token;
			}
			$apiurl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
			$response = $this->request($apiurl);
			$js_ticket = array(
					'value' => $response['ticket'],
					'expire' => time() + $response['expires_in'] - 200,
			);
			wkcache($cachename, $js_ticket);
		}
		$this->account['jsapi_ticket'] = $js_ticket;
		return $js_ticket['value'];
	}
	
	public function getJssdkConfig($url){
		$jsapiTicket = $this->getJsApiTicket();
		if(is_error($jsapiTicket)){
			$jsapiTicket = $jsapiTicket['message'];
		}
		$nonceStr = random(16);
		$timestamp = time();

		$string1 = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string1);
		$config = array(
			"appId" => $this->account['account_appid'],
			"nonceStr" => $nonceStr,
			"timestamp" => "$timestamp",
			"signature" => $signature,
		);
		if(DEVELOPMENT) {
			$config['url'] = $url;
			$config['string1'] = $string1;
			//$config['name'] = $this->account['name'];
		}

		return $config;
	}

	public function openPlatformTestCase() {
		$post = file_get_contents('php://input');
		//file_put_contents(BASE_ROOT_PATH.'/tmp/openPlatformTestCase.html', date('Y-m-d H:i:s').'--post string--'.var_export($post, true).PHP_EOL, FILE_APPEND);

		//WeUtility::logging('platform-test-message', $post);
		$encode_message = $this->xmlExtract($post);
		//file_put_contents('openPlatformTestCase.html', date('Y-m-d H:i:s').'--encrypt string--'.$encode_message['encrypt'].PHP_EOL, FILE_APPEND);
		$message = aes_decode($encode_message['encrypt'], $this->encodingaeskey);
		$message = $this->parse($message);
		//file_put_contents('openPlatformTestCase.html', date('Y-m-d H:i:s').'--message string--'.var_export($message, true).PHP_EOL, FILE_APPEND);
		$response = array(
			'ToUserName' => $message['from'],
			'FromUserName' => $message['to'],
			'CreateTime' => time(),
			'MsgId' => time(),
			'MsgType' => 'text',
		);
		if (!empty($message['content'])){
			if ($message['content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
				$response['Content'] = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
			}else if (strexists($message['content'], 'QUERY_AUTH_CODE')) {
				list($sufixx, $authcode) = explode(':', $message['content']);
				$auth_info = $this->getAuthInfo($authcode);
				//print_r($auth_info);die;
				//WeUtility::logging('platform-test-send-message', var_export($auth_info, true));
				if(!empty($auth_info['authorization_info'])){
					$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=". $auth_info['authorization_info']['authorizer_access_token'];
					$data = array(
						'touser' => $message['from'],
						'msgtype' => 'text',
						'text' => array('content' => $authcode.'_from_api'),
					);
					$response = ihttp_request($url, urldecode(json_encode($data)));
					exit('');
				}else
					exit('ticke error');
				
			}
		}

		if ($message['msgtype'] == 'event') {
			$response['Content'] = $message['event'] . 'from_callback';
		}
		
		$nonce = random(16);
		$time = time();
		$xml = array(
			'Nonce' => $nonce,
			'TimeStamp' => $time,
			'Encrypt' => aes_encode(array2xml($response), $this->encodingaeskey, $this->appid),
		);

		$signature = array($xml['Encrypt'], $this->token, $time, $nonce);
		sort($signature, SORT_STRING);
		$signature = implode($signature);
		$xml['MsgSignature'] = sha1($signature);
		exit(array2xml($xml));
	}

	private function request($url, $post = array()) {
		$response = ihttp_request($url, json_encode($post));
		$response = json_decode($response['content'], true);
		if (empty($response) || !empty($response['errcode'])) {
			return error($response['errcode'], $this->error_code($response['errcode'], $response['errmsg']));
		}
		return $response;
	}

	private function getAuthRefreshToken() {
		$auth_refresh_token = rkcache('account:auth:refreshtoken:'.$this->account['wx_appid']);
		if (empty($auth_refresh_token)) {
			$auth_refresh_token = $this->account['wx_auth_refresh_token'];
			wkcache('account:auth:refreshtoken:'.$this->account['wx_appid'], $auth_refresh_token);
		}
		return $auth_refresh_token;
	}

	private function setAuthRefreshToken($token) {
		$data = array('wx_auth_refresh_token' => $token);
		M('oil/Company_config')->update_by_id($this->account['company_id'], $data);
		
		wkcache('account:auth:refreshtoken:'.$this->account['wx_appid'], $token);
	}

	public function authz($redirect_uri)
	{
		$secret = !empty($this->account['wx_appsecret'])?$this->account['wx_appsecret']:$this->account['APPSECRET'];
		if(empty($appid))
			$appid = $this->account['wx_appid'];

		$url = $this->getOauthUserInfoUrl($redirect_uri, $this->account['company_id']);
		// if($this->isTest==1)
		// 	$url = $this->test_authorize_url.'?redirect_uri='.urlencode($redirect_uri);
		header('location:'.$url);exit;
	}
}