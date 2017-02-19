<?php
define('ALIPAY_OPEN_AUTH_TOKEN', 'https://openauth.alipaydev.com/oauth2/appToAppAuth.htm?app_id=%s&redirect_uri=%s');
require_once 'alipay-sdk/AopLoader.php';

class AlipayThird {
	public $appid;
	// public $appsecret;
	// public $token;
	// public $refreshtoken;
	public $account;
	public $alipay_config;

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
		$this->alipay_config = C('PayConfig.ALIPAY3');
		$this->appid = $this->alipay_config['app_id'];
		$this->account = $account;
		if(!empty($this->account['ali_appid']))
			$this->account['account_appid'] = $this->account['ali_appid'];
		$this->account['key'] = $this->appid;
	}

	function getAuthToken22($code=''){
		$accesstoken = rkcache('aliaccount:auth:accesstoken:'.$this->account['account_appid']);
		if(empty($accesstoken) || empty($accesstoken['value']) || $accesstoken['expire'] < time()){
			$grant_type = 'authorization_code';
			$refresh_token = '';
			if(!empty($accesstoken) && $accesstoken['expire']<time()){
				$grant_type = 'refresh_token';
				$refresh_token = $accesstoken['refresh_token'];
			}

			$aop = new AopClient();
			$aop->gatewayUrl = $this->alipay_config['gatewayUrl'];
			$aop->appId = $this->alipay_config['app_id'];
			$aop->rsaPrivateKey = $this->alipay_config['merchant_private_key'];
			$aop->alipayrsaPublicKey=$this->alipay_config['alipay_public_key'];
			$aop->apiVersion = '1.0';
			$aop->signType = $this->alipay_config['sign_type'];
			$aop->postCharset=$this->alipay_config['charset'];
			$aop->format='json';
			$request = new AlipayOpenAuthTokenAppRequest ();
			$request->setBizContent("{" .
			"    \"grant_type\":\"$grant_type\"," .
			"    \"code\":\"$code\"," .
			"    \"refresh_token\":\"$refresh_token\"" .
			"  }");
			$result = $aop->execute ( $request); 
			 
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$resultCode = $result->$responseNode->code;
			if(!empty($resultCode)&&$resultCode == 10000){
				$accesstoken = array(
					'value' => $result->$responseNode->app_auth_token,
					'expire' => time() + intval($result->$responseNode->expires_in),
					'refresh_token' => $result->$responseNode->app_refresh_token,
					're_expires_in' => time() + intval($result->$responseNode->expires_in),
				);
				wkcache('aliaccount:auth:accesstoken:'.$this->account['account_appid'], $accesstoken);
			}
		}

		return $accesstoken['value'];
	}

	/**
	 * 使用SDK执行提交页面接口请求
	 * @param unknown $request
	 * @param string $token
	 * @param string $appAuthToken
	 * @return string $$result
	 */
	private function aopclientRequestExecute($request, $token = NULL, $appAuthToken = NULL) {

		$aop = new AopClient ();
		$aop->gatewayUrl = $this->alipay_config['gatewayUrl'];
		$aop->appId = $this->alipay_config['app_id'];
		$aop->signType = $this->alipay_config['sign_type'];
		//$aop->rsaPrivateKeyFilePath = $this->private_key;
		$aop->rsaPrivateKey = $this->alipay_config['merchant_private_key'];
		//$aop->alipayPublicKey = $this->alipay_public_key;
		$aop->alipayrsaPublicKey = $this->alipay_config['alipay_public_key'];
		$aop->apiVersion = "1.0";
		$aop->postCharset = $this->alipay_config['charset'];


		$aop->format='json';
		// 开启页面信息输出
		$aop->debugInfo=true;
		$result = $aop->execute($request,$token,$appAuthToken);

		//打开后，将url形式请求报文写入log文件
		//$this->writeLog("response: ".var_export($result,true));
		return $result;
	}

	public function getAuthToken($code){
		$accesstoken = array();
		if(!empty($this->account['account_appid']))
			$accesstoken = rkcache('aliaccount:auth:accesstoken:'.$this->account['account_appid']);
		if(empty($accesstoken) || empty($accesstoken['value']) || $accesstoken['expire'] < time()){
			$grant_type = 'authorization_code';
			$refresh_token = '';
			if(!empty($accesstoken) && $accesstoken['re_expires_in']<time()){
				$grant_type = 'refresh_token';
				$refresh_token = $accesstoken['refresh_token'];
			}

			$request = new AlipayOpenAuthTokenAppRequest ();
			$request->setBizContent("{" .
			"    \"grant_type\":\"$grant_type\"," .
			"    \"code\":\"$code\"," .
			"    \"refresh_token\":\"$refresh_token\"" .
			"  }");
			$result = $this->aopclientRequestExecute($request);
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$resultCode = $result->$responseNode->code;
			if(!empty($resultCode)&&$resultCode == 10000){
				$accesstoken = array(
					'value' => $result->$responseNode->app_auth_token,
					'expire' => time() + intval($result->$responseNode->expires_in),
					'refresh_token' => $result->$responseNode->app_refresh_token,
					're_expires_in' => time() + intval($result->$responseNode->expires_in),
				);
				if(empty($this->account['account_appid']))
					$this->account['account_appid'] = $result->$responseNode->auth_app_id;
				wkcache('aliaccount:auth:accesstoken:'.$this->account['account_appid'], $accesstoken);

				$accesstoken['app_id'] = $result->$responseNode->auth_app_id;
			}
		}

		return $accesstoken;
	}

	public function authz($redirect_uri)
	{
		$url = sprintf(ALIPAY_OPEN_AUTH_TOKEN, $this->appid, $redirect_uri);

		header('location:'.$url);exit;
	}
}