<?php
/**
 * 地址service
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_service {
	protected $couponModelArr = array();
	public function __construct() {
		$this->ci = &get_instance();
		//$this->ci->load->model('Coupon_model');
		//$this->ci->load->model('Coupon_User_model');
	}

	private function getCouponModel($id){
		if(!isset($this->couponModelArr[$id])){
			$this->couponModelArr[$id] = $this->ci->Coupon_model->get_by_id($id);
		}
		return  $this->couponModelArr[$id];
	}

	public function get_usable_coupons($uid,$page=0,$pagesize=10,$status=0) {
		//$coupon_list = $this->ci->Coupon_User_model->get_usable_coupons($uid);
		$coupon_list=array();
		$arrWhere = array('user_id'=>$uid);
		if($status == '0'){
			$arrWhere['overdue_date >=']=time();
			$arrWhere['status'] =$status;
		}else if($status == '1'){
			$arrWhere['status'] =$status;
		}else{
			$arrWhere['overdue_date <'] = time();
			$arrWhere['status !='] =2;
		}
		if($page>0){
		    $aList = $this->ci->Coupon_User_model->fetch_page($page, $pagesize,$arrWhere);
		    $aList = $aList['rows'];
		}else{
		    $aList = $this->ci->Coupon_User_model->get_list($arrWhere);
		    
		}
		
		foreach($aList as $value){
			$model1  = $this->getCouponModel($value['coupon_id']);
			if(!$model1){
				continue;
			}
			$value['coupon_type'] = $model1['coupon_type'];
			$value['price'] = $model1['price'];
			$value['condition'] = $model1['condition'];
			$value['use_type'] = $model1['use_type'];
			$value['shop_id'] = $model1['shop_id'];
			$value['img_url'] = $model1['img_url'];
			$value['desc'] = $model1['desc'];
			$value['coupon_name'] = $model1['coupon_name'];

			array_push($coupon_list,$value);
		}
	    return $coupon_list;
			
	}

	

	public function get_order_use_coupons($uid,$shop_id,$price){
		$time = time();
		$aList = $this->ci->Coupon_User_model->get_list(array('user_id'=>$uid,'status'=>0,'overdue_date >'=>$time));
		$coupon_list=array();

		foreach($aList as $value){
			$model1  = $this->getCouponModel($value['coupon_id']);
			if(floatval($model1['condition']) > floatval($price)){
				//删除
				continue;
			}
			if(intval($model1['use_type']) == 1){

			}else{
				if(intval($model1['shop_id']) != intval($shop_id)){
//
					//删除
					continue;
				}


			}
			$value['coupon_type'] = $model1['coupon_type'];
			$value['price'] = $model1['price'];
			$value['condition'] = $model1['condition'];
			$value['use_type'] = $model1['use_type'];
			$value['shop_id'] = $model1['shop_id'];
			$value['img_url'] = $model1['img_url'];
			$value['desc'] = $model1['desc'];
			$value['coupon_name'] = $model1['coupon_name'];
			array_push($coupon_list,$value);
		}

		return $coupon_list;
	}

	

	public function get_collect_coupon($shop_id){
		if(!$shop_id){
			$shop_id = 1;
		}
		$whereArr = '(shop_id = '.$shop_id.' or use_type = 1) and status = 0 and coupon_count > receive_count';
		$data  = $this->ci->Coupon_model->get_list($whereArr);
		return $data;
	}



}