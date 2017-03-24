<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_config_model extends XT_Model {

	protected $mTable = 'oil_site_config';
	protected $mPkId = 'site_id';
	

	public function getPayConfig($site_id, $company_id, $type='wx'){
		$info = $this->get_by_id($site_id);
		$arr = C('PayConfig.WXPAY3');
		if(!empty($info)){
			if($type=='wx'){
				if($info['is_agent']==1)
					$arrSub = C('PayConfig.WXPAY');
				else if($info['is_agent']==2){
					//公司支付
					$arrSub = M('oil/Company_config')->getPayConfig($company_id, $type);
				}else{
					$arrSub = array('wx_appid'=>$info['wx_appid'],'wx_mchid'=>$info['wx_mchid'],
							'wx_key'=>$info['wx_key'],'wx_appsecret'=>$info['wx_appsecret'],
							'wx_auth_refresh_token'=>$info['wx_auth_refresh_token'],
							'wx_token'=>$info['wx_token'],'site_id'=>$site_id, 'company_id'=>$company_id,
							'ali_appid'=>$info['ali_appid'],'ali_auth_token'=>$info['ali_auth_token'],
						);
				}
				$arr = array_merge($arr, $arrSub);
			}
		}else{
			$arr = M('oil/Company_config')->getPayConfig($site_id, $company_id, $type);
		}

		return $arr;
	}
}
