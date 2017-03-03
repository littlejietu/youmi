<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_service
{
	public function __construct()
	{
		$this->ci = & get_instance();
		// $this->ci->load->library('Trd_OrderVO');
		//$this->ci->load->model('Goods_model');
		$this->ci->load->model(array('oil/Price_model','trd/Order_model','user/User_model','trd/Order_goods_model','trd/Order_oil_model','trd/Cart_oil_model'));
		// $this->ci->load->model('oil/Price_model');
		// $this->ci->load->model('trd/Order_model');
		// $this->ci->load->model('user/User_model');
		// $this->ci->load->model('trd/Order_goods_model');
		// $this->ci->load->model('trd/Order_oil_model');
		// $this->ci->load->model('trd/Cart_oil_model');

		$this->ci->load->service('fundOrder_service');
		$this->ci->load->service('buying_service');
	}

	/*
	 * 初始化加油数据
	 * @param oil:gun_no,oil_num,oil_amt     (act_id,discount_amt?验证是否与页面上享受优惠相同?)
	 * @return array(gun_no,oil_no,oil_num,oil_amt,act_id,discount_amt)
	 */
	private function initOilData($oil, $buyer_userid, $user_level, $site_id){
		$arrOil = array();
		$arrOilTmp = explode(',', $oil);

		if(count($arrOilTmp)>=3){
			list($gun_no, $oil_num, $oil_amt) = explode(',', $oil);

			$aOil = $this->ci->Gun_model->get_info_by_no($gun_no, $site_id);

			if(empty($aOil))
				return null;
			$oil_no = $aOil['oil_no'];
			$price = empty($aOil['price'])?1:$aOil['price'];
			if($oil_num==0)
				$oil_num = sprintf("%.2f",$oil_amt/$price);
			if($oil_amt==0)
				$oil_amt = round($oil_num*$price, 2);
			if(empty($oil_num) && empty($oil_amt))
				return null;

			//判断是否有活动buy_service
			$act_id = $discount_amt = 0;
			$arrAct = $this->ci->buying_service->getOilDiscount($oil_no, $price, $oil_amt, $buyer_userid, $user_level, $site_id);
			if(!empty($arrAct)){
				$act_id = $arrAct['act_id'];
				$discount_amt = $arrAct['discount_amt'];
			}

			$arrOil = array('gun_no'=>$gun_no,'oil_no'=>$oil_no,'oil_num'=>$oil_num,'oil_price'=>$price,'oil_amt'=>$oil_amt,'act_id'=>$act_id,'discount_amt'=>$discount_amt);
		}

		return $arrOil;
	}

	/**
	 * 初始化数据
	 *
	 * @param goods: goods_id,sku_id,num|goods_id,sku_id,num,activity_id,bundle_id|...
	 * @return arrOrderList
	 */
	private function initGoodsData($goods){

		$arrGoodsTmp = explode('|', $goods);
		$goods_amt = 0;
		$discount_amt = 0;
		$comm_amt = 0;
		$arrGoods = array();
		$arrTmp = array();
    	foreach ($arrGoodsTmp as $v) {
    		$aItem = explode(',', $v);

    		if(count($aItem)>=3)
    		{
    			$good_id = $aItem[0];
    			$sku_id = $aItem[1];
    			$num = $aItem[2];
    			$activity_id = 0;
    			if(!empty($aItem[3]))
    				$activity_id = $aItem[3];
    			$bundle_id = 0;
    			if(!empty($aItem[4]))
    				$bundle_id = $aItem[4];

    			//未知商品
    			if($good_id==-1){
    				$goods_amt = $price = $sku_id;
    				$comm_amt = 0;
    				$sku_id = 0;
    				$arrTmp[] = array('goods_id'=>$good_id,'sku_id'=>$sku_id,'num'=>$num,'bundle_id'=>$bundle_id,
    					'activity_id'=>$activity_id, 'price'=>$price, 'comm_price'=>0, 'spec'=>'',
    					//运费计算用到
    					'is_free_transport'=>1, 'transport_id'=>0, 'goods_num'=>$num,
    				);
    			}

    			/*
    			$aGoods = $this->ci->Goods_model->get_info_by_id($good_id);
    			if(!empty($aGoods) && $aGoods['status']==1){
    				$spec = '';
    				$price = $aGoods['price'];
    				$price_active = $this->ci->buying_service->getRealPriceInActivity($aGoods['tpl_id'], $aGoods['price']);
    				if($price_active>0){
    					//减活动库存
    					$this->ci->buying_service->buy_goods_in_activity($aGoods['tpl_id'], $num);
    					$price = $price_active;
    				}
    				if($sku_id>0){
    					$aSku = $this->ci->Goods_sku_model->get_by_where(array('id'=>$sku_id,'goods_id'=>$good_id));
    					//echo $this->ci->Goods_sku_model->db->last_query();die;
    					if(!empty($aSku)){
    						$price = $aSku['price'];
    						$price_active = $this->ci->buying_service->getRealPriceInActivity($aGoods['tpl_id'], $aSku['price']);
    						if($price_active>0)
    							$price = $price_active;
    						$spec = $aSku['sku_title'];
    					}
    				}


    				$arrTmp[] = array('goods_id'=>$good_id,'sku_id'=>$sku_id,'num'=>$num,'bundle_id'=>$bundle_id,
    					'activity_id'=>$activity_id, 'price'=>$price, 'comm_price'=>$aGoods['comm_price'], 'spec'=>$spec,
    					//运费计算用到
    					'is_free_transport'=>$aGoods['is_free_transport'], 'transport_id'=>$aGoods['transport_id'], 'goods_num'=>$num, //'shop_id'=>$aGoods['shop_id']
    				);

    				$goods_amt = $goods_amt + $price * $num;
    				$comm_amt = $comm_amt + $aGoods['comm_price'] * $num;
    			}
    			*/
    		}
    	}

    	if(!empty($arrTmp)){
	    	$arrGoods['goods'] = $arrTmp;

	    	$arrGoods['goods_amt'] = $goods_amt;

	    	//discount_amt:通过商品activity_id得出discount_amt
			$arrGoods['discount_amt'] = 0;

			//分润金额
			$arrGoods['comm_amt'] = $comm_amt;
		}

    	return $arrGoods;
	}
	

	/**
	 * 创建订单列表
	 *
	 * @param $arrCart array('shop_id1'=>array('goods'=>$goods, 'activity'=>1, $coupon=>2),
	 *				'shop_id2'=>array('goods'=>$goods, '组activity'=>2, $coupon=>3),
	 *				...
	 * 			)
	 *			goods: goods_id,sku_id,num|goods_id,sku_id,num,bundle_id,商品activity_id|...
	 *		  $arrBuy array(buyer_userid, buyer_username, user_level)
	 * @param coupon
	 * @return $arrReturn 订单id
	 */
	public function createOrderList($arrCart, $arrBuy, $arrCashier=null, $addressId=0, $invoiceId=0, $ifcart=0){
		$this->ci->load->model('oil/Site_model');
		$this->ci->load->model('oil/Gun_model');
		$this->ci->load->service('buying_service');

		$arrReturn = array();

		foreach ($arrCart as $site_id => $aItem) {
			//店铺信息
			$aShop = $this->ci->Site_model->get_by_id($site_id);
			if(empty($aShop)){
				continue;
			}

			$arrOil = $arrGoods = array();
			if(!empty($aItem['oil']))
				$arrOil = $this->initOilData($aItem['oil'], $arrBuy['buyer_userid'], $arrBuy['user_level'], $site_id);
			//todo 商品 三期?
			if(!empty($aItem['goods']))
				$arrGoods = $this->initGoodsData($aItem['goods']);
			//print_r($arrOil);die;
			if(empty($arrOil) && empty($arrGoods))
				continue;

			$arrTrdOrder['title'] = C('OrderTypeName.1');
			if(!empty($arrOil)){
				$arrTrdOrder['title'] = C('OrderTypeName.1').$arrOil['oil_no'].'号'.$arrOil['oil_num'].'L';
				if(!empty($arrGoods))
					$arrTrdOrder['title'] .= '+商品';
			}
			if(empty($arrOil) && !empty($arrGoods))
				$arrTrdOrder['title'] .= '商品';
			$coupon_id = !empty($aItem['coupon'])?$aItem['coupon']:0;
			$activity_id = !empty($aItem['activity'])?$aItem['activity']:0;

			$arrTrdOrder['oil'] = $arrOil;
			$oil_amt = $oil_discount_amt = 0;
			if(!empty($arrOil)){
				$oil_amt = $arrOil['oil_amt'];
				$oil_discount_amt = $arrOil['discount_amt'];
			}

			$arrTrdOrder['goods'] = $arrGoods;
			$goods_amt = $goods_discount_amt = $goods_comm_amt = 0;
			if(!empty($arrGoods)){
				$goods_amt = $arrGoods['goods_amt'];
				$goods_discount_amt = $arrGoods['discount_amt'];
				$goods_comm_amt = $arrGoods['comm_amt'];
			}
			
			if(!empty($arrCashier)){
				$arrTrdOrder['cashier_id'] = $arrCashier['cashier_id'];
				$arrTrdOrder['cashier_name'] = $arrCashier['cashier_name'];
			}
			$arrTrdOrder['addressId'] = $addressId;
			$arrTrdOrder['invoiceId'] = $invoiceId;
			$arrTrdOrder['ifcart'] = $ifcart;

			//coupon_amt:通过coupon_id得出优惠券金额
			$arrTrdOrder['coupon_amt'] = 0;
			/* todo:二期
			$coupon_amt = $this->ci->buying_service->get_coupon_price($arrBuy['buyer_userid'],$coupon_id,$site_id, $oil_amt, $goods_amt);
			if($coupon_amt>0){
				$bUseIt = $this->ci->buying_service->use_coupon($coupon_id);
				if($bUseIt){
					$arrTrdOrder['coupon_id'] = $coupon_id;
					$arrTrdOrder['coupon_amt'] = $coupon_amt;
				}
			}
			*/

			//fare_amt:在一个商店计算运费
			$arrTrdOrder['fare_amt'] = 0;
			/*无配送
			if(!empty($addressId)){
				$aAddress = $this->ci->Address_model->get_by_id($addressId);
				if(!empty($aAddress))
					$arrTrdOrder['fare_amt'] = $this->ci->buying_service->getFare($arrGoods['goods'], $aAddress['city_id']);
			}*/

			//todo:
			//$discount_amt:组活动
			$discount_amt = 0;
			$arrTrdOrder['discount_amt'] = $oil_discount_amt + $goods_discount_amt + $discount_amt;

			$arrTrdOrder['total_amt'] =  $oil_amt + $goods_amt + $arrTrdOrder['fare_amt'];
			$pay_amt = $arrTrdOrder['total_amt'] - $arrTrdOrder['discount_amt'] - $arrTrdOrder['coupon_amt'];
			$arrTrdOrder['pay_amt'] = $pay_amt<0?0:$pay_amt;
			$arrTrdOrder['comm_amt'] = $goods_comm_amt;
			$arrTrdOrder['site_id'] = $site_id;
			$arrTrdOrder['pay_type'] = 1;	//目前只有在线支付
			$arrTrdOrder['comment_status'] = 0;
			$arrTrdOrder['buyer_userid'] = $arrBuy['buyer_userid'];
			$arrTrdOrder['buyer_username'] = $arrBuy['buyer_username'];
			$arrTrdOrder['seller_userid'] = $aShop['seller_userid'];
			$arrTrdOrder['seller_username'] = $aShop['seller_username'];
			$arrTrdOrder['company_id'] = $aShop['company_id'];

			$arrReturn[] = $this->createAOrder($arrTrdOrder);
		}
		return $arrReturn;
	}

	/**
	 * 创建订单
	 *
	 * @param //array $arrTrdOrder 订单
	 * 		商品数组$arrTrdOrder['goods'] = array()	
	 *				keys:bundle_id,goods_id,sku_code,sku_id,price,num,activity_id
	 * @return boolean 创建成功/失败
	 */
	private function createAOrder($arrTrdOrder){
		if(empty($arrTrdOrder['order_sn']))
			$arrTrdOrder['order_sn'] = getOrderSn($arrTrdOrder['seller_userid']);
		$arrTrdOrder['status'] = C('OrderStatus.Create');
		$arrTrdOrder['createtime'] = time();
		if(empty($arrTrdOrder['buyer_userid']))
			$arrTrdOrder['buyer_userid'] = 0;
		if(empty($arrTrdOrder['seller_userid']))
			$arrTrdOrder['seller_userid'] = 0;
		if(empty($arrTrdOrder['total_amt']))
			$arrTrdOrder['total_amt'] = 0;
		if(empty($arrTrdOrder['pay_amt']))
			$arrTrdOrder['pay_amt'] = 0;
		if(empty($arrTrdOrder['discount_amt']))
			$arrTrdOrder['discount_amt'] = 0;
		if(empty($arrTrdOrder['coupon_amt']))
			$arrTrdOrder['coupon_amt'] = 0;
		if(empty($arrTrdOrder['coupon_id']))
			$arrTrdOrder['coupon_id'] = 0;
		if(empty($arrTrdOrder['fare_amt']))
			$arrTrdOrder['fare_amt'] = 0;
		if(empty($arrTrdOrder['comment_status']))
			$arrTrdOrder['comment_status'] = 0;
		if(empty($arrTrdOrder['platform_id']))
			$arrTrdOrder['platform_id'] = C('basic_info.PLATFORM_ID');

		if(empty($arrTrdOrder['oil']) && empty($arrTrdOrder['goods']))
			return 0;
		
		$arrOil = $arrTrdOrder['oil'];
		$arrGoods = $arrTrdOrder['goods'];
		$addressId = $arrTrdOrder['addressId'];
		$invoiceId = $arrTrdOrder['invoiceId'];
		$ifcart = $arrTrdOrder['ifcart'];
		unset($arrTrdOrder['oil']);
		unset($arrTrdOrder['goods']);
		unset($arrTrdOrder['addressId']);
		unset($arrTrdOrder['invoiceId']);
		unset($arrTrdOrder['ifcart']);

		

		// 初始化订单
		$arrTrdOrder = $this->initTradeOrderBaseData($arrTrdOrder);
		$order_id = $this->ci->Order_model->insert_string($arrTrdOrder);

		if(!empty($arrOil)){
			//1.保存订单油品信息
			
			$aOil = array('order_id'=>$order_id,'gun_no'=>$arrOil['gun_no'],'oil_no'=>$arrOil['oil_no'],'oil_price'=>$arrOil['oil_price'],'oil_num'=>$arrOil['oil_num'],'oil_amt'=>$arrOil['oil_amt'],
				'act_id'=>$arrOil['act_id'],'act_discount'=>$arrOil['discount_amt'],'buyer_userid'=>$arrTrdOrder['buyer_userid'],
				'site_id'=>$arrTrdOrder['site_id'],'company_id'=>$arrTrdOrder['company_id'],'addtime'=>time()
				);
			$oil_order_id = $this->ci->Order_oil_model->insert_string($aOil);
			if($oil_order_id){
				$this->ci->Cart_oil_model->delete_by_where(array('gun_no' => $arrOil['gun_no'],'buyer_userid' => $arrTrdOrder['buyer_userid'],'site_id' => $arrTrdOrder['site_id']));
			}
			//2.减库存---支付成功后再减库存

			//3.发票快照

		}

		// todo:商品 三期?
		if(!empty($arrGoods)){
			//未知商品
			foreach ($arrGoods['goods'] as $a) {
				$real_price = $a['price'];
				$aItem = array('order_id'=>$order_id, 'bundle_id'=>$a['bundle_id'], 'goods_id'=>$a['goods_id'], 'sku_id'=>$a['sku_id'],
						'price'=>$a['price'], 'real_price'=>$real_price, 'comm_price'=>$a['comm_price'], 'num'=>$a['num'], 'activity_id'=>$a['activity_id'], 'sort'=>0, 'addtime'=>time(),
					);
				$order_goods_id = $this->ci->Order_goods_model->insert_string($aItem);
			}

			/*
			
			$this->ci->load->model('Shot_goods_model');
			$this->ci->load->model('Goods_sku_model');
			//$this->ci->load->model('Address_model');
			$this->ci->load->model('Order_detail_model');
			$this->ci->load->service('package_service');
			$this->ci->load->service('goodsnum_service');


			$i = 0;
			foreach ($arrGoods['goods'] as $a) {
				$i++;

				//1.保存订单商品信息
				//1.1商品信息
				//计算优惠后真实单价
				$real_price = $a['price'] * (($arrTrdOrder['pay_amt']-$arrTrdOrder['fare_amt'])/$arrGoods['goods_amt']);
				$aItem = array('order_id'=>$order_id, 'bundle_id'=>$a['bundle_id'], 'goods_id'=>$a['goods_id'], 'sku_id'=>$a['sku_id'],
						'price'=>$a['price'], 'real_price'=>$real_price, 'comm_price'=>$a['comm_price'], 'num'=>$a['num'], 'activity_id'=>$a['activity_id'], 'sort'=>$i, 'addtime'=>time(),
					);
				$order_goods_id = $this->ci->Order_goods_model->insert_string($aItem);
				//1.2分配包裹
				$delivery_way = $this->ci->package_service->packing($order_id, $order_goods_id, $a['sku_id'], $a['goods_id'], $a['num']);

				//1.3减库存
				$this->ci->goodsnum_service->onOrderPackage($a['sku_id'], $a['goods_id'], $a['num']);
				//-1.保存订单商品信息

				
				//2.保存快照信息
				//2.1商品快照
				$aGoods = $this->ci->Goods_model->get_info_by_id($a['goods_id']);
				//todo:$attr 暂时没用到，PC端有快照
				$attr = '';
				if(!empty($aGoods)){
					$aShot = array('order_id'=>$order_id, 'goods_id'=>$a['goods_id'], 'sku_id'=>$a['sku_id'], 'title'=>$aGoods['title'], 'price'=>$a['price'],'comm_price'=>$aGoods['comm_price'], 'num'=>$a['num'],
						'brand_id'=>$aGoods['brand_id'], 'shop_id'=>$arrTrdOrder['shop_id'], 'category_id'=>$aGoods['category_id'], 'pic_path'=>$aGoods['pic_path'],
						'content'=>$aGoods['content'], 'm_content'=>$aGoods['m_content'], 'attr'=>$attr, 'spec'=>$a['spec']
						);
					$this->ci->Shot_goods_model->insert_string($aShot);
				}

				//2.2地址快照$addressId
				
				// $aAddress = $this->ci->Address_model->get_by_id($addressId);
				// if(!empty($aAddress)){
				// 	$aShotAddress = array('order_id'=>$order_id,'delivery_way'=>$delivery_way, 'buyer_userid'=>$arrTrdOrder['buyer_userid'],'real_name'=>$aAddress['real_name'],
				// 		'province_id'=>$aAddress['province_id'], 'province_name'=>$aAddress['province_name'], 'city_id'=>$aAddress['city_id'], 'city_name'=>$aAddress['city_name'], 'area_id'=>$aAddress['area_id'], 'area_name'=>$aAddress['area_name'], 'address'=>$aAddress['address'],
				// 		'mobile'=>$aAddress['mobile'], 'phone'=>$aAddress['phone'], 'zip_code'=>$aAddress['zip_code'],
				// 		);
				// 	$this->ci->Order_detail_model->insert($aShotAddress);
				// }

				//2.3发票快照$invoiceId
				//-2.保存快照信息

				//3.删除购物车
				if($ifcart){
					$aCartWhere = array('buyer_id'=>$arrTrdOrder['buyer_userid'], 'shop_id'=>$arrTrdOrder['shop_id'], 'goods_id'=>$a['goods_id'], 'sku_id'=>$a['sku_id'],'status'=>1);
					$this->ci->Cart_model->update_by_where($aCartWhere, array('status'=>-1));
				}
				//-3.删除购物车
			}

			*/

		}

		return $order_id;
	}

	/**
	 * 评论状态
	 *
	 * @param 订单id
	 * @param 评论状态 0:未评价 1:买家已评 2:双方已双评
	 * //@return boolean 修改成功/失败
	 */
	public function updateCommentStatus($orderId, $commentStatus){
		$data = array('comment_status'=>$commentStatus);

		$this->ci->Order_model->update_by_id(array('order_id'=>$orderId), $data);
	}

	private function tranCreate2Waiting($orderId){
		$aOrder = $this->ci->Order_model->get_by_id($orderId);
		if(!empty($aOrder)){
			if($aOrder['status']==C('OrderStatus.WaitPay'))
				return true;
			else
			{
				if($this->tranStatus($aOrder, C('OrderStatus.Create'),C('OrderStatus.WaitPay')) )
					return true;
				else
					return false;
			}
		}
		else
			return false;
	}

	/**
	 * 去支付
	 *
	 * @param orderIds 订单ids  多个订单，一起支付
	 * @param netpayMethod 支付方式 
	 * @param extParam	其他参数
	 * //@return boolean 修改成功/失败
	 */
	public function gotoPay($orderIds, $netpayMethod,  $extParam){
		
		$arrReturn = array();

		$arrOrderId = explode(',', $orderIds);
		foreach ($arrOrderId as $orderId) {

			//创建资金订单
			$aOrder = $this->ci->Order_model->get_info_by_id($orderId);
			// 检查订单是否有效
			$arrReturn_Tmp = $this->checkOrder($aOrder);
			if($arrReturn_Tmp['code']!=C('OrderResultError.Success'))
			{
				$arrReturn[$orderId] = $arrReturn_Tmp;
				continue;
			}

			if( $aOrder['status']==C('OrderStatus.Create') || $aOrder['status']==C('OrderStatus.WaitPay') ){
				if( $aOrder['pay_amt']>0 ){
					$this->tranCreate2Waiting($orderId);
					//支付方式
					/*$pay_type = 1;		//在线支付
					if($netpayMethod<=10)
						$pay_type = 2;	//余额支付
					if($aOrder['pay_type']!=$pay_type)
						$this->ci->Order_model->update_by_id($orderId, array('pay_type'=>$pay_type));*/
					$ip = $this->ci->input->ip_address();
					//消费资金订单
					$arrReturn_Tmp = $this->ci->fundorder_service->doConsume($aOrder['order_id'], $aOrder['order_sn'], $aOrder['title'], $aOrder['site_id'], $aOrder['company_id'], $aOrder['buyer_userid'], $aOrder['buyer_username'], $aOrder['pay_amt'], $aOrder['seller_userid'], $aOrder['seller_username'], $netpayMethod, $extParam, $ip, $aOrder['platform_id']);
					if($arrReturn_Tmp['code']!=C('OrderResultError.Success'))
					{
						$arrReturn[$orderId] = $arrReturn_Tmp;
						continue;
					}else{
						//修改订单支付方式
						$this->ci->Order_model->update_by_id($orderId, array('netpay_method'=>$netpayMethod));
						$this->ci->Order_oil_model->update_by_id($orderId, array('netpay_method'=>$netpayMethod));
						$this->ci->Order_goods_model->update_by_id($orderId, array('netpay_method'=>$netpayMethod));

						$arrReturn[$orderId] = $this->pay($orderId);

						/*if($netpayMethod==C('PayMethodType.AllBalance')){

						}
						else
							$arrReturn[$orderId] = $arrReturn_Tmp;*/
					}
				}else{
					//免费--todo
					//$arrReturn[$orderId] = array('code'=>C('OrderResultError.Success'), 'errInfo'=>'免费');
				}
			}
			else{	
				//支付后，自动确认收货并结算
				if(C('PayConfig.WaitSendAutoFinish')==1){
					if($aOrder['status']==C('OrderStatus.Finished'))
						$arrReturn[$orderId] = array('code'=>C('OrderResultError.Success'), 'errInfo'=>'已支付');
					else
						$arrReturn[$orderId] = $this->pay($orderId);
				}else
					$arrReturn[$orderId] = array('code'=>C('OrderResultError.Success'), 'errInfo'=>'已支付');
			}
		}

		return $arrReturn;
	}

	public function pay($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		
		$arrReturn = $this->ci->fundorder_service->doNextStep($orderId);
		if($arrReturn['code']==C('OrderResultError.Success')){
			// 扣款成功
			if($this->tranWaitPay2WaitSend($orderId) )
			{
				if(C('PayConfig.WaitSendAutoFinish')==1){
					//自动确认并结算
					if($this->tranWaitSend2WaitConfirm($orderId))
						$arrReturn = $this->finishOrder($orderId);
				}else{
					$arrReturn['code'] = C('OrderResultError.Success');
					$arrReturn['errInfo'] = '';
				}
			}
			else{
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '交易订单修改状态失败';
			}

		}

		return $arrReturn;
	}

	public function tranWaitPay2WaitSend($orderId){
		$aOrder = $this->ci->Order_model->get_by_id($orderId);

		if ($this->tranStatus($aOrder, C('OrderStatus.WaitPay'), C('OrderStatus.WaitSend')) )
			return true;
		else
			return false;
	}

	/**订单状态处理*/
	public function tranWaitSend2WaitConfirm($orderId){
		$aOrder = $this->ci->Order_model->get_by_id($orderId);

		if ($this->tranStatus($aOrder, C('OrderStatus.WaitSend'), C('OrderStatus.WaitConfirm')) )
			return true;
		else
			return false;
	}

	/**
	 * 初始化数据
	 *
	 * @param array $arrTrdOrder 订单
	 * //@return boolean 修改成功/失败
	 */
	public function initTradeOrderBaseData($arrTrdOrder){
		if(empty($arrTrdOrder['coupon_amt']))
			$arrTrdOrder['coupon_amt'] = 0;
		if(empty($arrTrdOrder['coupon_id']))
			$arrTrdOrder['coupon_id'] = 0;
		if(empty($arrTrdOrder['comment_status']))
			$arrTrdOrder['comment_status'] = 0;
		if(empty($arrTrdOrder['discount_amt']))
			$arrTrdOrder['discount_amt'] = 0;
		if(empty($arrTrdOrder['pay_amt']))
			$arrTrdOrder['pay_amt'] = $arrTrdOrder['total_amt'] - $arrTrdOrder['discount_amt'] - $arrTrdOrder['coupon_amt'];

		//todo:买家/卖家
		if(empty($arrTrdOrder['buyer_username']))
		{
			//..
		}
		if(empty($arrTrdOrder['seller_username']))
		{
			//..
		}
		return $arrTrdOrder;
	}

	/**
	 * 检查订单
	 *
	 * @param array $arrTrdOrder 订单
	 *			商品数组$arrTrdOrder['goods'] = array()
	 *					keys:bundle_id,goods_id,sku_code,sku_id,price,num,activity_id
	 * @return array('fundOrderId'=>0,
	 *			'code'=>'empty',
	 *			'errInfo'=>'错误信息'
	 *		) 修改成功/失败
	 */
	public static function checkOrder($arrTrdOrder){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		// 检查主订单参数
		if(empty($arrTrdOrder['buyer_userid']) || empty($arrTrdOrder['seller_userid']) || empty($arrTrdOrder['total_amt']) || empty($arrTrdOrder['pay_amt']) )
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单基本参数不全';
			return $arrReturn;
		}
		
		if(empty($arrTrdOrder['oil']) && empty($arrTrdOrder['goods']))
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '没有油品或商品信息';
			return $arrReturn;
		}

		// 检查订单资金
		if($arrTrdOrder['total_amt']<=0 ||$arrTrdOrder['pay_amt']<0)
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单非法金额';
			return $arrReturn;
		}
		$total_amt = 0;
		if(!empty($arrTrdOrder['goods'])){
			foreach ($arrTrdOrder['goods'] as $key => $a) {
				if( empty($a['price']) || empty($a['num']) || $a['price']<=0 || $a['num']<=0  )
				{
					$arrReturn['code'] = 'Failure';
					$arrReturn['errInfo'] = '订单非法金额';
					break;
				}
				$total_amt = $total_amt + $a['price'] * $a['num'];
			}
		}
		if(!empty($arrTrdOrder['oil'])){
			$total_amt = $total_amt + $arrTrdOrder['oil']['oil_amt'];
		}
		$total_amt = $total_amt + $arrTrdOrder['fare_amt'];

		if($arrReturn['code']!='Empty')
			return $arrReturn;

		// 金额是否一致
		//if(bccomp(floatval($arrTrdOrder['total_amt']),floatval($total_amt),2 )!==0 )
		if( intval($arrTrdOrder['total_amt']*100)!=intval($total_amt*100) )
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单金额与商品数量计算所得金额不一致';
			return $arrReturn;
		}

		$arrReturn['code'] = 'Success';
		$arrReturn['errInfo'] = '';

		return $arrReturn;
	}

	/**
	 * 订单状态检查及更新，先检查订单当前状态与预期状态是否一致
	 * @param array $arrTrdOrder 订单
	 * @return boolean true/false
	 */
	private function tranStatus($arrTrdOrder, $statusFrom, $statusTo)
	{
		if(empty($arrTrdOrder['status']))
			return false;
		if($arrTrdOrder['status']==$statusTo)
			return true;
		if($arrTrdOrder['status']!=$statusFrom)
			return false;

		$result = $this->ci->Order_model->updateStatus($arrTrdOrder['order_id'],$statusFrom, $statusTo);
		if($result>0){
			$data = array();
			if($statusTo==C('OrderStatus.Finished'))
				$data = array('payed_status'=>1);
			elseif($statusTo==C('OrderStatus.Refunded'))
				$data = array('payed_status'=>2);
			if(!empty($data)){
				$this->ci->Order_oil_model->update_by_id($arrTrdOrder['order_id'], $data);
				$this->ci->Order_goods_model->update_by_id($arrTrdOrder['order_id'], $data);
			}
		}

		return $result>0;
	}

	public function finishOrder($orderId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		$arrOrder = $this->ci->Order_model->get_by_id($orderId);
		if(empty($arrOrder))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "订单不存在";
			return $arrReturn;
		}

		if ($arrOrder['pay_amt']==0 && $arrOrder['total_amt']<=$arrOrder['discount_amt']+$arrOrder['coupon_amt'] && $arrOrder['fare_amt']==0 ) {
			if ( $this->tranStatus($arrOrder, C('OrderStatus.WaitConfirm'), C('OrderStatus.Finished')) ) {
				$arrReturn['code'] = C('OrderResultError.Success');
				if (!empty($arrOrder['coupon_id'])) {
					$arrReturn['errInfo'] = $arrOrder['coupon_id'];
				}
			} else {
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = "修改交易订单状态失败";
			}
		} else {// 有资金订单

			$arrReturn = $this->ci->fundorder_service->settle($orderId);
			if ($arrReturn['code']==C('FundOrderStatus.Settled')) {
				// 已结算--修改交易订单状态为Finished
				if ($this->tranStatus($arrOrder, C('OrderStatus.WaitConfirm'), C('OrderStatus.Finished'))) {
					$arrReturn['code'] = C('OrderResultError.Success');
					$arrReturn['errInfo'] = '';

					if($arrOrder['buyer_userid']>0){
						//积分
						$this->ci->load->service('integral_service');
						$this->ci->integral_service->consume($arrOrder['buyer_userid'], $arrOrder['order_id'], $arrOrder['order_sn'], $arrOrder['pay_amt']);
						//-积分
					}

					if (!empty($arrOrder['coupon_id'])) {
						$arrReturn['errInfo'] = $arrOrder['coupon_id'];
					}
				} else {
					$arrReturn['code'] = C('OrderResultError.Failure');
					$arrReturn['errInfo'] = '修改交易订单状态失败';
				}
			}

		}

		return $arrReturn;
	}

	//未发货前，关闭订单
	//1.若未支付，关闭订单，
	//2.若已支付，先退款，再关闭订单
	public function close($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		$aOrder = $this->ci->Order_model->get_by_id($orderId);
		if(empty($aOrder))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "订单不存在";
			return $arrReturn;
		}
		
		if($aOrder['status']==C('OrderStatus.Create'))
			$this->tranStatus($aOrder, $aOrder['status'], C('OrderStatus.Closed'));
		else if($aOrder['status']==C('OrderStatus.WaitPay'))
			$this->tranStatus($aOrder, $aOrder['status'], C('OrderStatus.Closed'));
		else if($aOrder['status']==C('OrderStatus.WaitSend') || $aOrder['status']==C('OrderStatus.Finished'))
		{
			$arrReturn = $this->ci->fundorder_service->refund($aOrder['order_id']);
			$bResult = ( $arrReturn['code']== C('OrderResultError.Success') );
			if($bResult)
				$this->tranStatus($aOrder, $aOrder['status'], C('OrderStatus.Closed'));

			return $bResult;
		}

		return true;
	}

	public function delete($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		$aOrder = $this->ci->Order_model->get_by_id($orderId);
		if(empty($aOrder))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "订单不存在";
			return $arrReturn;
		}

		if($aOrder['status']==C('OrderStatus.Create') || $aOrder['status']==C('OrderStatus.WaitPay'))
			$this->close($orderId);

		$data = array('delete_status'=>-1);
		$where = array('order_id'=>$orderId);
		return $this->ci->Order_model->update_by_where($where,$data);
	}

	//售后退款
	public function afterSalesRefund($orderId, $refundId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');
		$aOrder = $this->ci->Order_model->get_by_id($orderId);
		if(empty($aOrder))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "订单不存在";
			return $arrReturn;
		}

		if($aOrder['status']!=C('OrderStatus.Finished')){
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单需先确认收货";
			return $arrReturn;
		}

		$aRefund = $this->ci->Order_refunds_model->get_by_id($refundId);
		if(empty($aRefund))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "退款订单不存在";
			return $arrReturn;
		}

		//6:商家同意退款
		if($aRefund['status']!=6){
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "退货流程有误";
			return $arrReturn;
		}

		$ip = $this->ci->input->ip_address();
		$platformId = C("basic_info.PLATFORM_ID");

		$arrReturn = $this->ci->fundorder_service->refundStep($refundId, $aOrder['buyer_userid'], $aOrder['buyer_username'], $aOrder['seller_userid'], $aOrder['seller_username'], $aRefund['refunds_money'], $ip, $platformId);
		if($arrReturn['code']==C('OrderResultError.Success')){
			//退货后，佣金计算
            $this->ci->Order_model->calcCommAmt($orderId);
		}

		return $arrReturn;
	}


}