<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends TokenApiController { 
	public function __construct() {
		parent::__construct();
	}

	public function index(){
		$user_id = $this->loginUser['user_id'];

		$this->load->model('user/User_model');
		// $this->load->model('Account_model');
		// $this->load->service('coupon_service');
		$info = $this->User_model->get_by_id($user_id);



		/*
		//钱包
		$acct_balance = 0;
		$acct_integral = 0;
		$coupon_num = 0;
		
		$user = $this->loginUser;
		$user_id = $user['user_id'];

		$aAccount = $this->Account_model->get_by_id($user_id);
		if(!empty($aAccount)){
			$acct_balance = $aAccount['acct_balance'];
			$acct_integral = $aAccount['acct_integral'];
		}
		else
			$this->Account_model->init($user_id);

		$coupon_num = count($this->coupon_service->get_usable_coupons($user_id));

		$data = array('acct_balance'=>$acct_balance,'acct_integral'=>$acct_integral,'coupon_num'=>$coupon_num,'refund_num' => $refund_num);

		$aNum = array_merge($data, $aNum);
*/
		output_data(array('info'=>$info));

	}
}