<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
	public function __construct()
    {
    	parent::__construct();
    	$this->load->service('Order_service');
    }

    public function buy(){
    	$cart = $this->input->post('cart');
        $arrBuy = array('buyer_userid'=>5,'buyer_username'=>'test');
        $addressId = 0;
        $invoiceId = 0;

    	$arrOrderIds = $this->order_service->createOrderList($cart, $arrBuy, null, $addressId, $invoiceId);

        $result = array(
            'orderids'=>  implode(',', $arrOrderIds)
            );

        $this->load->view('front/buy',$result);

    }

    public function paying(){
        $orderIds = $this->input->post('orderIds');

        $arrReturn = $this->order_service->gotoPay($orderIds, C('PayMethodType.AllBalance'));

        print_r($arrReturn);
    }

    public function payed(){
        $order_id = 8;
        $arrReturn = $this->order_service->pay($order_id);
        print_r($arrReturn);
    }

    public function jump(){

    }

    public function coupon(){
        $coupon_amt = $this->ci->buying_service->get_coupon_price($arrBuy['buyer_userid'],$coupon_id,$shop_id,$arrGoods['goods_amt']);
        if($coupon_amt>0){
            $bUseIt = $this->ci->buying_service->use_coupon($coupon_id);
            if($bUseIt){
                $arrTrdOrder['coupon_id'] = $coupon_id;
                $arrTrdOrder['coupon_amt'] = $coupon_amt;
            }
        }
    }


}