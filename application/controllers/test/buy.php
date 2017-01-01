<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Buy extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }


    public function index(){
    	$cart_id = '1,2';

    	

    	$this->load->service('Order_service');
    	$this->order_service->createOrder($trd_OrderVO);

    	
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
}

