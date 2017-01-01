<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sign extends TokenApiController {
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('Sign_record_model');
    }
    /*
     * 签到数据
     * */
	public function index()
	{
		//todo user
		$result = array(
			'data'=>"",
			"msg"=>"SUCCESS",
			'code' => 1,
			'action' => 'm_sign'
		);
		$now = getdate();
		$month = $now['year']*100+$now['mon'];
		$whereArr = array(
			'month' => $month,
			'user_id' => 1
		);
		$signData = $this->Sign_record_model->get_list($whereArr);
		if(!$signData){
			$result["msg"]="FAILE";
			$result['code'] = 0;
		}else{
			$result['data'] = $signData[0];
		}
		echo json_encode($result);//,decbin($signData[0]['sign_data']);
	}
/*
 * 请求签到
 * */
	public function sign_req(){
		//todo user
		$result = array(
			'data'=>"",
			"msg"=>"SUCCESS",
			'code' => 1,
			'action' => 'm_sign_sign_req'
		);
		$now = getdate();
		$month = $now['year']*100+$now['mon'];
		$whereArr = array(
			'month' => $month,
			'user_id' => 1
		);
		$signData = $this->Sign_record_model->get_list($whereArr);
		if(!$signData){
			$info = array(
				'user_id' =>1,
				'sign_data' => pow(2,$now['mday']-1),
				'month' => $month,
				'add_time' => time()
			);
			$id = $this->Sign_record_model->insert_string($info);
			if(!$id){
				$result['code'] = 0;
				$result['msg'] = "FAILE";
			}
		}else{
			$signData = $signData[0];
			if($signData['sign_data'] & pow(2,$now['mday']-1)){
				$result['code'] = 0;
				$result['msg'] = "FAILE";
				$result['data']="今日已签到";
			}else{
				$signData['sign_data'] = $signData['sign_data'] | pow(2,$now['mday'] -1);
				$this->Sign_record_model->update_by_where($whereArr,$signData);
			}
		}
		echo json_encode($result);
	}

	



}
