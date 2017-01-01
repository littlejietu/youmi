<?php
/**
 * 购买相关API
 * @date: 2016年3月18日 下午4:29:59
 * @author: hbb
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Buy extends TokenApiController {
	public function __construct() {
		parent::__construct();
		//$this->load->service('address_service');
		//$this->load->service('cart_service');
		//$this->load->service('buy_service');
		$this->load->service('order_service');
		$this->load->service('coupon_service');
		
		$user = $this->loginUser;
		$this->user_id = $user['user_id'];
		$this->user_name = $user['user_name'];
	}

	/**
	 * 函数用途描述
	 * @date: 2016年3月18日 下午4:29:55
	 * @author: hbb
	 * @param: variable
	 * @return:
	 */
	public function index() {
		$this->confirm();
	}

	/**
	 * 订单确认
	 * @date: 2016年3月18日 下午4:31:23
	 * @author: hbb
	 * @param: string $_POST['cart_id']  goods_id(cart_id)|sku_id|num,goods_id(cart_id)|sku_id|num
	 * @param: string $_POST['ifcart'] 是否购物车提交
	 * @return: array $data
	 */
	public function confirm() {
		$oil_cart_id = $this->input->post('oil_cart_id');

		$this->load->model('trd/Cart_oil_model');

		$info = $this->Cart_oil_model->get_by_id($oil_cart_id);
    	if(empty($info))
    		output_error(-1,'错误');

    	output_data(
		    array(
		    	'oil' => array('gun_no'=>$info['gun_no'], 'oil_amt'=>$info['oil_amt'], 'act_id'=>$info['act_id'],'discount_amt'=>$info['act_discount']),
    		    'goods' => null,
		        'coupon' => null,
		        'amount' => 0
		    )
		);

		/*
		$cart_id = $this->input->post('cart_id');
		$ifcart = $this->input->post('ifcart');

		$cart_id = explode('|', $cart_id);
		$user_id = $this->user_id;
		$shop_id = 0;
		$total_amt = 0;

		
		//购买商品和金额
		if ($ifcart) {
			$buy_data = $this->cart_service->initGoodsList($user_id, $cart_id, $city_id);
		} else {
			$buy_data = $this->buy_service->get_buy_goods($cart_id, $city_id);
		}

        if(!is_array($buy_data)){
            switch($buy_data){
                case -1:
                    output_error(-1,'错误');
                    break;
                case -2:
                    output_error(-2,'所购商品无效');
                    break;
                case -3:
                    output_error(-3,'一次最多只可购买50种商品');
                    break;
                case 0:
                    output_error(0,'商品已售罄');
                    break;
            }
            exit;
        }

        //先考虑一家店铺
        if(!empty($buy_data['buy'][0]['shop']['id'])){
            $shop_id=$buy_data['buy'][0]['shop']['id'];
        }

        if(!empty($buy_data['amount']['total_goods'])){
            $total_amt=$buy_data['amount']['total_goods'];
        }

		//优惠券
		$coupon_list=$this->coupon_service->get_order_use_coupons($user_id, $shop_id, $total_amt);
		$coupons=array(
		    'num'=>count($coupon_list),
		    'list'=>empty($coupon_list)?null:$coupon_list,
		);
		
		output_data(
		    array(
    		    'goods' => $buy_data['buy'],
		        'coupon'=>$coupons,
		        'amount'=>$buy_data['amount']
		    )
		);
		*/
	}

	/**
	* 创建订单
	*/
	public function create() {
	    $cart = $this->input->post('cart');
	    $address_id = $this->input->post('address_id');
        $ifcart =  $this->input->post('ifcart');
	    $invoiceId = 0;
        $arrBuy = array('buyer_userid'=>$this->user_id,'buyer_username'=>$this->user_name,'user_level'=>1);
        if($cart){
            $arrOrderIds = $this->order_service->createOrderList($cart, $arrBuy, null, $address_id, $invoiceId, $ifcart);
            if($arrOrderIds){
                output_data(array('order_ids'=>implode(',', $arrOrderIds)));
            }
        }
        
    	//output_error(-1,'ERROR');
        output_error('ERROR','错误');
	}

	public function pay(){
		$cart_id = $this->input->post('cart_id');
		$agetn_type= $this->input->post('agent_type')?1:0;

		$this->load->model('Cart_oil_model');

	}
	
	
	
	/**
	* 付款界面 收银台
	*/
	public function cashier(){
	    $order_ids = $this->input->post('order_ids');
	    $agetn_type= $this->input->post('agent_type')?1:0;

	    $this->load->model('user/User_model');
	    $this->load->model('trd/Order_model');
	    $this->load->model('trd/Order_oil_model');

	    if(empty($order_ids))
	        output_error(-1,'错误');

	    $userInfo = $this->User_model->get_by_id($this->user_id);

	    $arrIds=explode(',', $order_ids);
	    if(is_array($arrIds)){
	        //支付金额
	        
	        $orderInfo=$this->Order_model->get_list(
	            array(
	                'order_id'=>$arrIds,
	                'status'=>array(
	                    C('OrderStatus.Create'),
	                    C('OrderStatus.WaitPay')
	                )
	            ),
	            'order_id,pay_amt'
	        );
	        //$pay_amount = array_reduce($orderInfo, function ($foo, $v) {return $foo + $v['pay_amt'];});
	        $pay_amount=0;
	        $order_ids_now=array();
	        foreach ($orderInfo as $v)
	        {
	            $pay_amount +=$v['pay_amt'];
	            $order_ids_now[]=$v['order_id'];
	        }
	        if(array_diff($arrIds,$order_ids_now)){
	            //output_error(-1,'ORDERID IS INVALID');
	            output_error(-1,'非法的订单ID');
	        }
	        $pay_amount=number_format($pay_amount,2,'.','');

	        $arrOil = null;
	        if(count($arrIds)==1){
	        	$oilInfo = $this->Order_oil_model->get_by_id($order_ids);
	        	if(!empty($oilInfo)){
	        		$oil_name = '国五';
	        		$oil_no = $oilInfo['oil_no'];
	        		if($oil_no==0)
	        			$oil_name .= $oil_no.'#柴油';
	        		else
	        			$oil_name .= $oil_no.'#车用汽油';
	        		$arrOil = array('oil_name'=>$oil_name, 'oil_no'=>$oilInfo['oil_no'], 'oil_amt'=>$oilInfo['oil_amt'],'discount_amt'=>$oilInfo['act_discount']);
	        	}
	        }

	        $arrPayMethod = array(
		        	array('title'=>'微信支付',
		                  'code'=>$agetn_type ? C('PayMethodType.WeixinPayApp') : C('PayMethodType.WeixinPayJs')
		                ),
		        	array('title'=>'支付宝支付',
		                  'code'=>$agetn_type ? C('PayMethodType.AliPayApp') : C('PayMethodType.AliPayJs')
		                ),
	        	);
	        if($userInfo['member_status']==1){
	        	$arrPayMethod[] = array(
	                   'title'=>'余额',
	                    'code'=>C('PayMethodType.AllBalance'),
	                );
	        	$arrPayMethod[] = array(
	                   'title'=>'蜜油',
	                    'code'=>C('PayMethodType.OilBalance'),
	                );
	        }



	        
	        //用户余额
    		$this->load->model('acct/Account_model');
    		$user_account = $this->Account_model->init_get($this->user_id);
	        
	        output_data(array(
	            'order_ids'=>implode(',', $order_ids_now),
	            'oil'=>$arrOil,
	            'pay_amount'=>$pay_amount,
	            'user_amount'=>$user_account['acct_balance'],
	            'paymethod'=>$arrPayMethod,
	        ));
	    }
	}
	
	
}