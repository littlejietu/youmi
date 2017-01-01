<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends XT_Model {

	protected $mTable = 'trd_order';
	protected $mPkId = 'order_id';
	
	/**
	 * 订单状态检查及更新，先检查订单当前状态与预期状态是否一致
	 * @param array $arrTrdOrder 订单
	 * @return boolean true/false
	 */
	public function updateStatus($orderId, $statusFrom, $statusTo){
		$data = array('status'=>$statusTo);
		$where = array('order_id'=>$orderId, 'status'=>$statusFrom);

		if($statusTo==C('OrderStatus.WaitSend'))
			$data['payed_time'] = time();
		else if($statusTo==C('OrderStatus.WaitConfirm'))
			$data['sended_time'] = time();
		else if($statusTo==C('OrderStatus.Finished'))
			$data['finished_time'] = time();
		

		return $this->update_by_where($where,$data);
	}


	/**
	 * 订单配送状态检查及更新，先检查配送订单当前状态与预期状态是否一致
	 * @ param array $arrTrdOrder 订单
	 * @ return boolean true/false
	 
	public function updateDeliverStatus($orderId, $statusFrom, $statusTo){
		$data = array('deliver_status'=>$statusTo);
		$where = array('order_id'=>$orderId, 'deliver_status'=> $statusFrom);

		return $this->update_by_where($where,$data);
	}
	*/


	/**
	 * 订单状态检查及更新，先检查订单当前状态与预期状态是否一致
	 * @param $orderId 订单id
	 * @return array   goods:商品
	 */
	public function  get_info_by_id($orderId){
		$where = array('order_id'=>$orderId);
		$arrOrder = $this->get_by_id($orderId);

		if(!empty($arrOrder)) {
			$arrGoods = M('trd/order_goods')->get_list(array('order_id'=>$orderId));
			$arrOrder['goods'] = $arrGoods;

			$arrOil = M('trd/Order_oil')->get_by_id($orderId);
			$arrOrder['oil'] = $arrOil;
		}

		return $arrOrder;
	}

	public function calcCommAmt($orderId){
		//退货进行中的状态:2,3,4		1:完成 5:取消 6:商家同意退款
		$prefix = $this->prefix();
		$aRefund = M('Order_refunds')->get_by_where("order_id=$orderId and status in('2','3','4')");
		if(empty($aRefund)){
			$sql = 'update '.$prefix.'trd_order set comm_amt=(SELECT SUM( comm_price*num ) FROM '.$prefix.'trd_order_goods a WHERE a.id NOT IN( SELECT order_goods_id FROM '.$prefix.'trd_order_refunds WHERE STATUS in(1,6) AND order_id=a.order_id ) AND a.order_id='.$orderId.') where order_id='.$orderId;
			$this->execute($sql);
		}
	}
	
}
