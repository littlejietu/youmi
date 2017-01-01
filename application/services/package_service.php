<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('Goods_model');
		$this->ci->load->model('Goods_sku_model');
		$this->ci->load->model('Order_package_model');

	}

	//
	public function packing($order_id, $order_goods_id, $sku_id, $goods_id, $num){
		$mix_way = 0;
		$aDeliver = $this->ci->Goods_sku_model->deliver($sku_id, $goods_id, $num);

		if(!empty($aDeliver)){
			foreach ($aDeliver['detail'] as $deliver_way => $num) {
				if($mix_way==0)
					$mix_way = $deliver_way;
				if($mix_way!=$deliver_way)
					$mix_way = 3;	//'3'=>'由九号街区+快递配送'
				$this->ci->Order_package_model->insert_string(array('order_id'=>$order_id,'order_goods_id'=>$order_goods_id, 'deliver_way'=>$deliver_way, 'num'=>$num));
			}
		}

		return $mix_way;
	}

	public function packed($order_id){
		$aPack = $this->ci->Order_package_model->get_by_where(array('order_id'=>$order_id,'status'=>0));
		if(empty($aPack))
			return true;
		else
			return false;
	}

}