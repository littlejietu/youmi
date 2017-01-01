<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sms extends ApiController
{
	public function __construct()
    {
        parent::__construct();
        $this->load->service('sms_service');
        $this->load->model('user/User_pwd_model');
        $this->load->model('inter/Message_def_model');
        $this->load->service('user_service');
        $this->load->model('sys/Smscode_model');
    }

	public function send(){
		$mobile = $this->input->post_get('mobile');
		$type_id = $this->input->post_get('type_id');
		$ip = $this->input->ip_address();
		$platform_id = $this->input->post_get('platform_id');
		$token = $this->input->post_get('token');
		
		if(empty($platform_id))
			$platform_id = C('basic_info.PLATFORM_ID');
		
		$messge_id = 10;//验证码短信模版id		
		
		$arrReturn = array('code'=>'EMPTY','msg'=>'','action' =>'sms_send');
        
		$bMobile = preg_match("/^1[34578]\d{9}$/", $mobile);
		if(!$bMobile){
		    output_error(-1,'手机号格式不对');exit;	//USER_PHONE_FORMAT_ERROR
			return $arrReturn;
		}
		//1:Resgister 2:ForgetPassword 3:BankCert 4:ModifyUserName 5:TiedUserName
		if($type_id ==1)// 注册验证码:需检测手机是否已注册
		{
			$aExist = $this->User_pwd_model->get_by_where(array('user_name'=>$mobile));
			if(!empty($aExist)){
			    output_error(-1,'手机已存在');exit;	//USER_MOBILE_EXIST
				return $arrReturn;
			}
		}
		if($type_id ==2)// 忘记密码:需检测手机是否已注册
		{
			$aExist = $this->User_pwd_model->get_by_where(array('user_name'=>$mobile));
			if(empty($aExist)){
			    output_error(-1,'手机号不存在');exit;	//USER_MOBILE_NOT_EXIST
				return $arrReturn;
			}
			
		}
	    if($type_id ==5)// 注册验证码:需检测手机是否已注册
		{
			$aExist = $this->User_pwd_model->get_by_where(array('user_name'=>$mobile));
			if(!empty($aExist)){
			    output_error(-1,'手机已存在');exit;	//USER_MOBILE_EXIST
				return $arrReturn;
			}
		}
		if($type_id ==6)// 绑定用户名:新手机号需未注册
		{

		}

		if ($type_id == 7)//忘记支付密码/设置支付密码
		{
		    $user = $this->user_service->get_userid($token);
		    if (empty($user))
		    {
		        output_error(-1,'用户登录信息已失效，请重新登录');exit;
		    }
		    if ($mobile != $user['user_name'])
		    {
		        output_error(-1,'填写的手机号码与登录账号不匹配');exit;
		    }
		}

		if($type_id==8){
			$this->load->model('oil/Site_model');

			$site_id = $this->input->post_get('site_id');
			if(empty($site_id)){
				output_error(-1,'公司数据不全');exit;
			}

			$info = $this->Site_model->get_by_id($site_id);
			if(empty($info) || $info['status']!=1){
				output_error(-1,'加油站不存在或已关闭');exit;
			}
			$aExist = $this->User_model->get_by_where(array('mobile'=>$mobile,'company_id'=>$info['company_id']));
			if(!empty($aExist)){
			    output_error(-1,'手机已存在');exit;	//USER_MOBILE_EXIST
			}
		}


		$aSmscode = $this->Smscode_model->get_by_where(array('mobile'=>$mobile, 'type_id'=>$type_id, 'platform_id'=>$platform_id, 'expiretime>'=>time()));
		if(!empty($aSmscode)){
			$code = $aSmscode['code'];
		}
		else{
			$code = rand(100000,999999);
			$data = array('type_id'=>$type_id, 'mobile'=>$mobile, 'code'=>$code, 'addtime'=>time(),'expiretime'=>time()+30*60,'ip'=>$ip, 'is_valid'=>0,'platform_id'=>$platform_id);
			$this->Smscode_model->insert_string($data);
		}

		/*
		$msgId = C('TPL_msg.SEND_CODE_msg_ID');
		$amsg = $this->Message_def_model->get_by_id($msgId);
		if(!empty($amsg)){
		    output_error(-1,'失败');exit;	//FAILURE
		}
		*/

		//$msg = str_replace('{CODE}', $code, $amsg['sms_content']);
		//$sendtime = time();
		if ($this->sendSMS($code,$mobile)){
		    output_data();exit;
		}else{
		    output_error(-1,'短信发送失败');exit;	//USER_SMS_SEND_FAILED
		}

// 		$smsUrl = '';//C('smsUrl');
// 		_sendSMS($mobile, $msg, $sendtime, $smsUrl);
		//$codes['code'] = $code;
		output_data();exit;

	}

	public function check($value='')
	{
		$mobile = $this->input->post_get('mobile');
		$code = $this->input->post_get('code');
		$type_id = $this->input->post_get('type_id');
		$platform_id = $this->input->post_get('platform_id');
		
		if(empty($platform_id))
			$platform_id = C('basic_info.PLATFORM_ID');
		/*$arrData = array(
		    'mobile' => $mobile,
		    'code' => $code,
		    'type_id' => $type_id,
		    'platform_id' => $platform_id,
		);*/
		$result = $this->sms_service->check_code($mobile, $code, $type_id, $platform_id);
    	if ($result == true)
    	{
    	    output_data();exit;
    	}
    	else
    	{
    	    output_error(-1,'验证码不正确');exit;
    	}

	}
	
	/**
	 * 发送短信
	 * @param unknown_type $tel
	 * @param unknown_type $name
	 * @param unknown_type $type
	 * @param unknown_type $username
	 * @return mixed
	 * XXX--X级代理商wy777081198
	 */
	protected function sendSMS($code,$tel){
		$url = "http://api.app2e.com/smsBigSend.api.php";
		$msg = '【九号街区】验证码'.$code;
		$data = array(
				'pwd' => md5('wv1C13zO'),
				'username' => 'zhuoerwangluo',
				'p' => $tel,
				'isUrlEncode' => 'no',
				'charSetStr' => 'utf',
				'msg' => $msg
			);
		//dump($data);exit;
		$code = http_post_data($url, $data);
		return $code;
	}
	

}