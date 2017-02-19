<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fans_model extends XT_Model {

	protected $mTable = 'wx_fans';

	public function get_info($openidOruid, $site_id = 0, $company_id = 0){
		if(empty($openidOruid)){
			return array();
		}
		
		$where = array();
		if (is_numeric($openidOruid)) 
			$where['user_id'] = $openidOruid;
		else
			$where['openid'] = $openidOruid;
		if(!empty($site_id))
			$where['site_id'] = $site_id;
		if(!empty($company_id))
			$where['company_id'] = $company_id;

		$fan = $this->get_by_where($where);
		if(!empty($fan)){
			if (!empty($fan['tag']) && is_string($fan['tag'])) {
				if (is_base64($fan['tag'])){
					$fan['tag'] = @base64_decode($fan['tag']);
				}
				if (is_serialized($fan['tag'])) {
					$fan['tag'] = @iunserializer($fan['tag']);
				}
				if(!empty($fan['tag']['headimgurl'])) {
					$fan['tag']['avatar'] = tomedia($fan['tag']['headimgurl']);
					unset($fan['tag']['headimgurl']);
				}
			} else {
				$fan['tag'] = array();
			}
		}
		
		return $fan;

	}
		
}