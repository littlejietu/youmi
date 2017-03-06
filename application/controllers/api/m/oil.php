<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oil extends TokenApiController { 
	public function __construct() {
		parent::__construct();
	}

	public function index(){

		$user = $this->loginUser;
		$site_id = $this->input->post('site_id');

		$this->load->model('oil/Gun_model');
		$this->load->model('oil/Site_model');

		$info = $this->Site_model->get_by_id($site_id);
		if(empty($info) || $info['status']!=1){
			output_error(-1,'此加油站不存在或已关闭');
			exit;
		}
		if($info['company_id']!=$this->loginUser['company_id'])
			output_error('NeedLogin2','请重新登录');
		
		$list = $this->Gun_model->get_list(array('site_id'=>$site_id), 'gun_no,oil_no');
		$arrSite = array('site_id'=>$site_id,
							'gun_oil_no'=>$list,
							'amts'=>array(100,200,300),
							'site_name'=>$info['site_name'],
						);
		$data = array('site'=>$arrSite);

		output_data($data);
	}

	public function act(){
		$user = $this->loginUser;

		$gun_no = $this->input->post('gun_no');
		$amount = $this->input->post('amt');
		$site_id = $this->input->post('site_id');

		$this->load->service('buying_service');
		$this->load->model(array('oil/Gun_model','user/User_model','pmt/Activity_model'));

		$info = $this->Gun_model->get_info_by_no($gun_no, $site_id);
		if(empty($info) || empty($info['price'])){
			output_error('GunErr','该枪号不存在');
			exit;
		}

		$user_info = $this->User_model->get_by_id($user['user_id']);
		
		$arrAct = $this->buying_service->getOilDiscount($info['oil_no'], $info['price'], $amount, $user['user_id'], $user_info['user_level'], $site_id);
		if(!empty($arrAct)){
			$actInfo = $this->Activity_model->get_by_id($arrAct['act_id']);
			$arrAct['name'] = $actInfo['title'];
			$arrAct['act_words'] = $actInfo['words'];
		}

		// $arrAct = array('id'=>25,'name'=>'93优惠','discount_amt'=>5.17,'act_words'=>'每升优惠0.18元'.$amount);
		// if($gun_no==2)
		// 	$arrAct = array('id'=>2,'name'=>'95优惠','discount_amt'=>2.17,'act_words'=>'每升优惠0.48元'.$amount);
		// elseif($gun_no==3)
		// 	$arrAct = array('id'=>3,'name'=>'92优惠','discount_amt'=>3.17,'act_words'=>'每升优惠0.38元'.$amount);
		// elseif($gun_no==6)
		// 	$arrAct = null;

		$data = array('act'=>$arrAct);
		output_data($data);
	}
}