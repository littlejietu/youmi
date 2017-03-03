<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Integral_log_model extends XT_Model {

	protected $mTable = 'sys_integral_log';

	public function opt($fields){

		$user_id = $fields['user_id'];
        if(empty($fields['num']))
            return;

		$aLog = $this->get_by_where(array('user_id'=>$user_id, 'type_id'=>$fields['type_id'], 'item_id'=>$fields['item_id']));
		if(empty($aLog)){
			M('acct/Account')->operate_by_id($user_id, array('acct_integral'=>'acct_integral+'.$fields['num']));
			$aAccount = M('acct/Account')->get_by_id($user_id);
			$fields['integral'] = $aAccount['acct_integral'];
			
			$user_info = M('user/User')->get_by_id($user_id);
			$fields['company_id'] = $user_info['company_id'];
			M('sys/Integral_log')->insert_string($fields);

			//update level
			$integral = $fields['integral'];
			$data = array();
			$info = M('sys/Level')->get_by_where(array('integral_num<='=>$integral, 'company_id'=>$user_info['company_id']),'level_id','integral_num desc');
			if($user_info['user_level']!=$info['level_id'])
				$data['user_level'] = $info['level_id'];
				
			if(!empty($data))
				M('user/User')->update_by_id($user_id, $data);
		}
    }
	
}
