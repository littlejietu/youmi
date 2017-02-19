<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_config_model extends XT_Model {

	protected $mTable = 'oil_company_config';
	protected $mPkId = 'company_id';
	
	public function getPayConfig($site_id, $company_id, $type='wx'){
		$info = $this->get_by_id($company_id);
		$arr = C('PayConfig.WXPAY3');
		if(!empty($info)){
			if($type=='wx'){
				if($info['is_agent']==1)
					$arrSub = C('PayConfig.WXPAY');
				else{
					$arrSub = array('wx_appid'=>$info['wx_appid'],'wx_mchid'=>$info['wx_mchid'],
							'wx_key'=>$info['wx_key'],'wx_appsecret'=>$info['wx_appsecret'],
							'wx_token'=>$info['wx_token'],'site_id'=>$site_id, 'company_id'=>$company_id,
							'ali_appid'=>$info['ali_appid'],'ali_auth_token'=>$info['ali_auth_token'],
						);
				}
				$arr = array_merge($arr, $arrSub);
			}
		}
		return $arr;
	}
}
