<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oil extends TokenOAdminApiController { 
	public function __construct() {
		parent::__construct();
	}

	public function index(){

		$oAdmin = $this->oadminUser;
		$site_id = $oAdmin['site_id'];

		$this->load->model('oil/Gun_model');
		$list = $this->Gun_model->get_list(array('site_id'=>$site_id), 'gun_no,oil_no');
		
		$arrSite = array('site_id'=>$site_id,
							'gun_oil_no'=>$list,
							'amts'=>array(100,200,300,400),
						);

		$data = array('site'=>$arrSite);

		output_data($data);
	}

	public function act(){
		$oAdmin = $this->oadminUser;

		$gun_no = $this->input->post('gun_no');
		$amount = $this->input->post('amt');
		$site_id = $oAdmin['site_id'];

		$this->load->service('buying_service');
		$this->load->model('oil/Gun_model');
		$this->load->model('pmt/Activity_model');

		$info = $this->Gun_model->get_info_by_no($gun_no, $site_id);
		if(empty($info) || empty($info['price'])){
			output_error('GunErr','该枪号不存在');
			exit;
		}
		
		$arrAct = $this->buying_service->getOilDiscount($info['oil_no'], $info['price'], $amount, C('basic_info.TEMP_USER_ID'), 1, $site_id);
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