<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_config_model extends XT_Model {

	protected $mTable = 'oil_site_config';
	protected $mPkId = 'site_id';
	

	public function getPayConfig($site_id, $company_id, $type='wx'){
		$info = $this->get_by_id($site_id);
		$arr = null;
		if(!empty($info)){
			if($type=='wx'){
				if($info['is_agent']==1)
					$arr = C('PayConfig.WXPAY');
				else if($info['is_agent']==2){
					//公司支付
					$arr = M('oil/Company_config')->getPayConfig($company_id, $type);
				}else{
					$arr = array('APPID'=>$info['wx_appid'],'MCHID'=>$info['wx_mchid'],
							'KEY'=>$info['wx_key'],'APPSECRET'=>$info['wx_appsecret'],
							'TOKEN'=>$info['wx_token'],
						);
				}
			}
		}else{
			$arr = M('oil/Company_config')->getPayConfig($company_id, $type);
		}

		return $arr;
	}
}
