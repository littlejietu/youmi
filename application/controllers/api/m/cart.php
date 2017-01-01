<?php
/**
 * 购物车接口
 * @author: txj
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Cart extends TokenApiController {

	public function __construct() {
		parent::__construct();
		//$this->load->service('cart_service');

	}

	
	public function oil() {
        $id =(int)$this->input->post('id');
        //删除非当前店铺的所有购物车
        $this->Cart_model->update_to_del_state(array(), $this->uid,$shop_id);
		$cart_list = $this->cart_service->initGoodsList($this->uid);
		if (!empty($cart_list)) {
			output_data(array('cart_list'=>$cart_list['buy']));
		} else {
			//output_error(0, 'CART IS EMPTY!');
		    output_error(0, '购物车没有商品');
		}

	}

	/**
	 * 添加购物车
	 * @param:  gun_no 	油枪号,
	 			oil_amt 金额
	 * @return:
	 */
	public function addoil() {
		$user = $this->loginUser;
		$gun_no = $this->input->post('gun_no');
		$amount = $this->input->post('amt');
		$site_id = $this->input->post('site_id');

		$this->load->model('oil/Gun_model');
		$this->load->model('trd/Cart_oil_model');
		$this->load->service('buying_service');


		$info = $this->Gun_model->get_info_by_no($gun_no, $site_id);
		if(empty($info) || empty($info['price'])){
			output_error('GunErr','该枪号不存在');
			exit;
		}
		
		$oil_num = rand($amount/$info['price'],2);
		$arrAct = $this->buying_service->getOilDiscount($info['oil_no'], $info['price'], $amount, $user['user_id'], 1, $site_id);
		// if(!empty($arrAct)){
		// 	$actInfo = $this->Activity_model->get_by_id($arrAct['act_id']);
		// 	$arrAct['name'] = $actInfo['title'];
		// 	$arrAct['act_words'] = $actInfo['words'];
		// }


		$data = array(
			'gun_no' => $gun_no,
			'oil_amt' => $amount,
			'oil_no' => $info['oil_no'],
			'oil_price' => $info['price'],
			'oil_num' => $oil_num,
			'act_id' => !empty($arrAct['act_id'])?$arrAct['act_id']:0,
			'act_discount' => !empty($arrAct['discount_amt'])?$arrAct['discount_amt']:0,
			'addtime' => time(),
			'buyer_userid' => $user['user_id'],
			'site_id' => $site_id,
		);

		$cart_id = $this->Cart_oil_model->insert_string($data);
		if ($cart_id) {
			//删除该用户该枪号其他数据
			$this->Cart_oil_model->delete_by_where(array('gun_no' => $gun_no,'buyer_userid' => $user['user_id'],'site_id' => $site_id,'id<>'=>$cart_id));
			output_data(array('cart_id'=>$cart_id));
		} else {
			//output_error(-1, 'UNKNOW ERROR');
			output_error(-1, '未知错误');
		}

	}

}
?>