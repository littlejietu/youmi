<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		
	}

	function forward(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$this->load->model('oil/Company_config_model');
		$this->load->library('WeixinThird');
		$weixin = new WeixinThird(array('company_id'=>$company_id));

		$auth_code = $this->input->get('auth_code');
		if (empty($auth_code)) {
			showMessage('授权登录失败，请重试','/seller/bind/onekey');
			exit;
		}
		$auth_info = $weixin->getAuthInfo($auth_code);
		if(empty($auth_info['authorization_info'])){
			showMessage($auth_info['message'],'/seller/bind/onekey');
			exit;
		}
		$auth_refresh_token = $auth_info['authorization_info']['authorizer_refresh_token'];
		$auth_appid = $auth_info['authorization_info']['authorizer_appid'];

		$account_info = $weixin->getAccountInfo($auth_appid);
		if (is_error($account_info)) {
			showMessage('授权登录新建公众号失败，请重试','/seller/bind/onekey');
			exit;
		}
		if (!empty($_GPC['test'])) {
			echo "此为测试平台接入返回结果：<br/> 公众号名称：{$account_info['authorizer_info']['nick_name']} <br/> 接入状态：成功";
			exit;
		}
		if ($account_info['authorizer_info']['service_type_info'] = '0' || $account_info['authorizer_info']['service_type_info'] == '1') {
			if ($account_info['authorizer_info']['verify_type_info']['id'] > '-1') {
				$level = '3';
			} else {
				$level = '1';
			}
		} elseif ($account_info['authorizer_info']['service_type_info'] = '2') {
			if ($account_info['authorizer_info']['verify_type_info']['id'] > '-1') {
				$level = '4';
			} else {
				$level = '2';
			}
		}


		$data = array('wx_nickname'=>$account_info['authorizer_info']['nick_name'],
				'wx_account' => $account_info['authorizer_info']['alias'],
				'wx_username' => $account_info['authorizer_info']['user_name'],
				'wx_level' => $level,
				'wx_appid' => $auth_appid,
				'wx_auth_refresh_token' => $auth_refresh_token,
				// 'wx_encodingaeskey' => $weixin->encodingaeskey,
				// 'wx_token' => $weixin->token,
				'company_id'=>$company_id,
			);
		$this->Company_config_model->insert($data);
		$data = array();
		$headimg = '';
		if(!empty($account_info['authorizer_info']['head_img'])){
			$headimg = ihttp_request($account_info['authorizer_info']['head_img']);
			$headimg_path = 'headimg/headimg_'.$company_id.'.jpg';
			file_put_contents(BASE_UPLOAD_PATH .'/'. $headimg_path, $headimg['content']);
			$data['head_img'] = $headimg_path;
		}
		$qrcode = '';
		if(!empty($account_info['authorizer_info']['qrcode_url'])){
			$qrcode = ihttp_request($account_info['authorizer_info']['qrcode_url']);
			$qrcode_path = 'qrcode/qrcode_'.$company_id.'.jpg';
			file_put_contents(BASE_UPLOAD_PATH . '/'.$qrcode_path, $qrcode['content']);
			$data['wx_qrcode'] = $qrcode_path;
		}

		if(!empty($data))
			$this->Company_config_model->update_by_id($company_id, $data);

		showMessage('授权登录成功','/seller/bind/onekey');
	}




}