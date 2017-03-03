<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends TokenApiController { 
	public function __construct() {
		parent::__construct();
	}

	public function index(){
		$user_id = $this->loginUser['user_id'];

		$this->load->model(array('user/User_model','acct/Account_model','sys/Level_model'));
		// $this->load->model('Account_model');
		// $this->load->service('coupon_service');
		$info = $this->User_model->get_by_id($user_id);

		//钱包
		$coupon_num = 0;

		$aAccount = $this->Account_model->get_by_id($user_id);
		if(empty($aAccount))
			$this->Account_model->init($user_id);

		$level_list = $this->Level_model->get_list(array('company_id'=>$info['company_id']),'level_id,level_name,integral_num','level_id');

		$info['acct_balance'] = 0;
		$info['acct_integral'] = 0;
		$info['next_level_msg'] = '';
		$info['next_percent'] = 0;
		if($info['member_status']==1){
			$info['acct_balance'] = $aAccount['acct_balance'];
			$info['acct_integral'] = $aAccount['acct_integral'];

			$next_level_name = '';
			$next_level_integral = 0;
			foreach ($level_list as $k => $v) {
				if($info['user_level']+1==$v['level_id']){
					$next_level_name = $v['level_name'];
					$next_level_integral = $v['integral_num'];
				}
			}
			
			if($next_level_integral>0){
				$info['next_level_msg'] = '再获'.($next_level_integral-$info['acct_integral']).'积分可升级为'.$next_level_name;
				$info['next_percent'] = round($info['acct_integral']/$next_level_integral *100, 2) .'%';
			}

		}
		//$coupon_num = count($this->coupon_service->get_usable_coupons($user_id));

		

		//$aNum = array_merge($data, $aNum);

		output_data(array('info'=>$info,'level_list'=>$level_list));

	}
}