<?php
/**
 * 地址service
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Discount_service {
	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model('discount_goods_model');
		$this->ci->load->model('goods_model');
		$this->ci->load->model('Discount_activity_model');
	}

	public function buy_discount_goods($id){
		$goods = $this->ci->discount_goods_model->get_by_id($id);
		if($goods){
			if($goods['status'] == 1){
				return -3;//已经失效；

			}
			if($goods['from_sale'] <time()){
				return -4;//活动物品已过期
			}
			if(intval($goods['total']) <= intval($goods['saled'])){
				return -2;//活动物品卖完了
			}else{
				$saled = intval($goods['saled'])+1;
				$this->ci->discount_goods_model->update_by_id($id,array('saled'=>$saled));
				return 1;
			}
		}else{
			return -1;//活动物品不存在
		}
	}

}