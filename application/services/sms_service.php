<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('sys/Smscode_model');
		$this->ci->load->model('user/User_pwd_model');
		$this->ci->load->model('user/User_model');
		$this->ci->load->model('user/User_num_model');
		$this->ci->load->model('user/User_token_model');
	}
	

	/*
	type_id   1:Resgister 2:ForgetPassword 3:BankCert 4:ModifyUserName 5:TiedUserName 6:DeliverResgister
	*/
	public function check_code($mobile, $code, $type_id, $platform_id)
	{
		$where = array(
			'mobile' => $mobile,
            'code' => $code,
            'type_id' => $type_id,
            'platform_id' =>$platform_id
            );
	    $aSmscode = $this->ci->Smscode_model->get_by_where($where);
	    if(!empty($aSmscode)){
	        if($aSmscode['expiretime']>time())
	        {
	        	$this->ci->Smscode_model->update_by_id($aSmscode['id'],array('is_valid'=>1));
	        	return true;
	        }
	        else{
				return false;
	        }
	    }
	    else{
			return false;
	    }

	    return false;
	}
}