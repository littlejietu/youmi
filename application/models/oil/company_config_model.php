<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_config_model extends XT_Model {

	protected $mTable = 'oil_company_config';
	protected $mPkId = 'company_id';
	
	public function getPayConfig($company_id, $type='wx'){
		$info = $this->get_by_id($company_id);
		$arr = null;
		if(!empty($info)){
			if($type=='wx'){
				if($info['is_agent']==1)
					$arr = C('PayConfig.WXPAY');
				else{
					$arr = array('APPID'=>$info['wx_appid'],'MCHID'=>$info['wx_mchid'],
							'KEY'=>$info['wx_key'],'APPSECRET'=>$info['wx_appsecret'],
							'TOKEN'=>$info['wx_token'],
						);
				}
			}
		}
		return $arr;
	}
}
