<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class fundOrder_service
{
	public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('trd/Fundorder_model');
		$this->ci->load->model('user/User_model');
		//$this->ci->load->model('order_goods_model');
		$this->ci->load->model('acct/Account_model');
	}

	//返回对象
	public function tryGetPay($payMethod){
		$obj = null;
		if($payMethod==12)
		{
			$this->ci->load->library('WeixinPayJs');
			$obj = new WeixinPayJs();
		}
		
		if($payMethod==11)
		{
			$this->ci->load->library('WeixinPayApp');
			$obj = new WeixinPayApp();
		}

		if($payMethod==13)
		{
			$this->ci->load->library('WeixinPayMicro');
			$obj = new WeixinPayMicro();
		}

		return $obj;
	}

	public function tryGetPayByName($payMethodName){
		$obj = null;
		if($payMethodName=='WeixinPayApp' || $payMethodName=='WeixinPayJs')
		{
			$this->ci->load->library($payMethodName);
			$obj = new $payMethodName();
		}
		return $obj;
	}

	/**
	 * 创建资金订单
	 *
	 * @param array $arrFundOrder 订单
	 *
	 * @return array(code=>'Failure',errInfo=>'',fund_order_id=>1111)
	 */
	private function createOrder($arrFundOrder){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		if(!empty($arrFundOrder['order_id']))
		{
			$aExist = $this->ci->Fundorder_model->findByOrderId($arrFundOrder['order_id']);
			if(!empty($aExist))
			{
				$arrReturn['code'] = 'Success';
				$arrReturn['fund_order_id'] = $aExist['fund_order_id'];
				return $arrReturn;
			}
		}

		if(!empty($arrFundOrder['refund_id']))
		{
			$aExist = $this->ci->Fundorder_model->findByRefundId($arrFundOrder['refund_id']);
			if(!empty($aExist))
			{
				$arrReturn['code'] = 'Success';
				$arrReturn['fund_order_id'] = $aExist['fund_order_id'];
				return $arrReturn;
			}
		}

		$arrFundOrder = $this->initFundOrderBaseData($arrFundOrder);
		//初始化支付方式
		// 内部支付方式==> 1:现金红包 2:消费红包 3.手动充值
		if($arrFundOrder['netpay_method']>10)
		{
			$payObj = $this->tryGetPay($arrFundOrder['netpay_method']);
			if(empty($payObj))
			{
				$arrReturn['code'] = 'Failure';
				$arrReturn['errInfo'] = '支付接口不存在';

				//todo:系统日志
				//...
			}

			if(!empty($payObj->respond_name)){
				$arrFundOrder['return_url'] = BASE_SITE_URL.'/api/respond/jump_'.$payObj->respond_name;
				$arrFundOrder['notice_url'] = BASE_SITE_URL.'/api/respond/notice_'.$payObj->respond_name;
			}else{
				$arrFundOrder['return_url'] = BASE_SITE_URL.'/api/respond/jump';
				$arrFundOrder['notice_url'] = BASE_SITE_URL.'/api/respond/notice';
			}
		}

		// 检查订单是否有效
		$arrResult = $this->checkOrder($arrFundOrder);
		if($arrResult['code']!='Success')
			return $arrResult;

		$result = $this->ci->Fundorder_model->insert_string($arrFundOrder);
		if($result>0)
		{
			$arrReturn['code'] = 'Success';
			$arrReturn['errInfo'] = '订单创建成功';
			$arrReturn['fund_order_id'] = $result;
		}
		else
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单创建失败';
		}

		return $arrReturn;

	}

	//初始化数据
	private function initFundOrderBaseData($arrFundOrder){
		if(empty($arrFundOrder['fund_order_sn']))
			$arrFundOrder['fund_order_sn'] = getOrderSn($arrFundOrder['seller_userid']);
		if(empty($arrFundOrder['create_time']))
			$arrFundOrder['create_time'] = time();
		if(empty($arrFundOrder['last_update_time']))
			$arrFundOrder['last_update_time'] = time();
		if(empty($arrFundOrder['status']))
			$arrFundOrder['status'] = C('FundOrderStatus.Waiting');
		if(empty($arrFundOrder['refund']))
			$arrFundOrder['refund'] = 0;
		if(empty($arrFundOrder['balance_amt']))
			$arrFundOrder['balance_amt'] = 0;
		if(empty($arrFundOrder['bonus_amt']))
			$arrFundOrder['bonus_amt'] = 0;
		if(empty($arrFundOrder['netpay_amt']))
			$arrFundOrder['netpay_amt'] = 0;
		if(empty($arrFundOrder['netrecharge_amt']))
			$arrFundOrder['netrecharge_amt'] = 0;
		if(empty($arrFundOrder['cash_coupon_amt']))
			$arrFundOrder['cash_coupon_amt'] = 0;
		if(empty($arrFundOrder['pay_proof_amt']))
			$arrFundOrder['pay_proof_amt'] = 0;
		if(empty($arrFundOrder['fee_amt']))
			$arrFundOrder['fee_amt'] = 0;
		

		if(empty($arrFundOrder['order_id']))
		{
			$arrFundOrder['order_id'] = 0;
			$arrFundOrder['order_sn'] = $arrFundOrder['fund_order_sn'];
		}

		if($arrFundOrder['type_id']==2 && $arrFundOrder['netpay_method']>10)
			$arrFundOrder['netpay_status'] = C('NetPayStatus.WAIT');
		else if(!empty($arrFundOrder['netpay_status']))
			$arrFundOrder['netpay_status'] = '';

		return $arrFundOrder;
	}

	/**
	 * 检查订单
	 *
	 * @param array $arrFundOrder 订单
	 *
	 * @return array(code=>'Failure',errInfo=>'',fund_order_id=>1111)
	 */
	private function checkOrder($arrFundOrder){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		//检查 null->0
		if(empty($arrFundOrder['buyer_userid']))
			$arrFundOrder['buyer_userid'] = 0;
		if(empty($arrFundOrder['seller_userid']))
			$arrFundOrder['seller_userid'] = 0;
		if(empty($arrFundOrder['fund_order_sn']))
			$arrFundOrder['fund_order_sn'] = '';
		if(empty($arrFundOrder['total_amt']))
			$arrFundOrder['total_amt'] = 0;
		if(empty($arrFundOrder['balance_amt']))
			$arrFundOrder['balance_amt'] = 0;
		if(empty($arrFundOrder['bonus_amt']))
			$arrFundOrder['bonus_amt'] = 0;
		if(empty($arrFundOrder['netpay_amt']))
			$arrFundOrder['netpay_amt'] = 0;
		if(empty($arrFundOrder['netrecharge_amt']))
			$arrFundOrder['netrecharge_amt'] = 0;
		if(empty($arrFundOrder['cash_coupon_amt']))
			$arrFundOrder['cash_coupon_amt'] = 0;
		if(empty($arrFundOrder['pay_proof_amt']))
			$arrFundOrder['pay_proof_amt'] = 0;
		if(empty($arrFundOrder['fee_amt']))
			$arrFundOrder['fee_amt'] = 0;

		//检查主订单参数
		if($arrFundOrder['buyer_userid']==0 || $arrFundOrder['seller_userid']==0 || $arrFundOrder['fund_order_sn']=='' || $arrFundOrder['total_amt']==0 || $arrFundOrder['title']=='')
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单基本参数不齐全';
			return $arrReturn;
		}

		//检查订单资金
		if($arrFundOrder['total_amt']<=0 || $arrFundOrder['balance_amt']<0 || $arrFundOrder['bonus_amt']<0 || $arrFundOrder['netpay_amt']<0 || $arrFundOrder['netrecharge_amt']<0 || $arrFundOrder['cash_coupon_amt']<0 || $arrFundOrder['pay_proof_amt']<0)
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '主订单非法金额';
			return $arrReturn;
		}

		//金额是否一致
		$totalAmt = $arrFundOrder['balance_amt']+$arrFundOrder['bonus_amt']+$arrFundOrder['netpay_amt']+$arrFundOrder['netrecharge_amt']+$arrFundOrder['cash_coupon_amt']+$arrFundOrder['pay_proof_amt']+$arrFundOrder['fee_amt'];
		if($arrFundOrder['type_id']==C('OrderType.Cash'))
			$totalAmt = $arrFundOrder['balance_amt'];
		if($arrFundOrder['type_id']==C('OrderType.AfterSalesRefund'))
			$totalAmt = $arrFundOrder['refund'];
		if(bccomp(floatval($arrFundOrder['total_amt']),floatval($totalAmt),2 )!==0)	//$arrFundOrder['total_amt']!=$totalAmt
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单总金额与需支付的金额不一致';
			return $arrReturn;
		}

		//检查买家金额是否足够
		$aUserAccount = $this->ci->Account_model->get_by_id($arrFundOrder['buyer_userid']);
		if(empty($aUserAccount))
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '用户帐户不存在';
			return $arrReturn;
		}

		if($arrFundOrder['balance_amt']>0){
			if($aUserAccount['acct_balance']<$arrFundOrder['balance_amt']){
				$arrReturn['code'] = 'Failure';
				$arrReturn['errInfo'] = '用户余额不足';
			}
			else
			{
				$bValid = $this->ci->Account_model->check($arrFundOrder['buyer_userid']);
				if(!$bValid){
					$arrReturn['code'] = 'Failure';
					$arrReturn['errInfo'] = '非法帐户';
				}
			}
		}

		$arrReturn['code'] = 'Success';
		$arrReturn['errInfo'] = '';

		return $arrReturn;

	}


	/**
	 * 支付ing..
	 *
	 * @param int fundOrderId 
	 *
	 * @return array(code=>'Failure',errInfo=>'',fund_order_id=>1111)
	 */
	public function paying($fundOrderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);

		//买家-余额支付
		if($aFundOrder['balance_amt']>0){
			//检查买家金额是否足够
			$aUserAccount = $this->ci->Account_model->get_by_id($aFundOrder['buyer_userid']);

			//余额不足，返回扣款失败
			if($aUserAccount['acct_balance']<$aFundOrder['balance_amt']){
				$arrReturn['code'] = 'Failure';
				$arrReturn['errInfo'] = '用户余额不足';
				return $arrReturn;
			}
		}

		//售后退款
		if($aFundOrder['type_id']==C('OrderType.AfterSalesRefund') && $aFundOrder['refund']>0){
			//检查卖家金额是否足够
			$aUserAccount = $this->ci->Account_model->get_by_id($aFundOrder['seller_userid']);

			//余额不足，返回扣款失败
			if($aUserAccount['acct_balance']<$aFundOrder['refund']){
				$arrReturn['code'] = 'Failure';
				$arrReturn['errInfo'] = '卖家余额不足';
				return $arrReturn;
			}
		}



		if($this->ci->Fundorder_model->paying($aFundOrder)){
			$arrReturn['code'] = 'Success';
			$arrReturn['errInfo'] = '';
		}
		else
		{
			$arrReturn['code'] = 'Failure';
			$arrReturn['errInfo'] = '订单状态变更或买家扣款失败';
		}

		return $arrReturn;
	}

	public function settle($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		$aFundOrder = $this->ci->Fundorder_model->findByOrderId($orderId);
		if(empty($aFundOrder)){
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = '没有找到订单';
			return $arrReturn;
		}
		$arrReturn['fund_order_id'] = $aFundOrder['fund_order_id'];
		//$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);

		if($aFundOrder['status']==C('FundOrderStatus.Settled')){
			$arrReturn['code'] = C('FundOrderStatus.Settled');
		}
		else{
			if(!$this->ci->Fundorder_model->Settled($aFundOrder)){
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '结算操作失败';
			}else{
				$arrReturn['code'] = C('FundOrderStatus.Settled');
			}
		}

		return $arrReturn;
	}

	public function nextStep($fundOrderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		$arrReturn['fund_order_id'] = $fundOrderId;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);

		// 只有是在线支付时,才需要到第三方界面  getNetPayStatus:在线交互总状态
		if($aFundOrder['netpay_amt']>0 && $aFundOrder['netpay_status']!=C('NetPayStatus.SUCCESS')){
			// 找到指定的接口，生成处理地址，完成加款
			if ($aFundOrder['netpay_method'] > 10) //外部支付:>10
			{
				//支付配置参数
				$this->ci->load->model('oil/Site_config_model');
				$aPayConfig = $this->ci->Site_config_model->getPayConfig($aFundOrder['site_id'],$aFundOrder['company_id']);
				if(empty($aPayConfig) || empty($aPayConfig['APPID'])){
					$arrReturn['code'] = C('OrderResultError.Failure');
					$arrReturn['errInfo'] = '支付配置参数未配置';
					return $arrReturn;
				}

				$obj = $this->tryGetPay($aFundOrder['netpay_method']);
				if(empty($obj)){
					//todo:系统日志
					//logger.error(String.format("订单({0})没有找到支付接口:{1}", fundOrderId, fundOrder.getNetpayMethod()));
					$arrReturn['code'] = C('OrderResultError.Failure');
					$arrReturn['errInfo'] = '订单不存在支付接口';
					return $arrReturn;
				}

				//JSAPI需要openid
				$openid = null;
				if($aFundOrder['netpay_method']==12){
					$this->ci->load->model('user/User_auth_model');
					$aAuth = $this->ci->User_auth_model->get_by_where(array('user_id'=>$aFundOrder['buyer_userid']));
					if(!empty($aAuth))
						$openid = $aAuth['openid'];
				}

				$urlOrPage = $obj->payRequest($aFundOrder, $aPayConfig, $openid);
				if(empty($urlOrPage))
					$arrReturn['code'] = C('OrderResultError.Failure');
				else{
					$arrReturn['errInfo'] = $urlOrPage;

					if(!is_array($urlOrPage) && strpos($urlOrPage, 'http://')===false)
						$arrReturn['code'] = C('OrderResultError.Failure');
					else{
						if($aFundOrder['netpay_method']==13){
							//修改资金订单状态
							$arrJmpReturn = $this->jump(13,$urlOrPage);
							$arrReturn['code'] = $arrJmpReturn['code'];
							if($arrReturn['code'] != C('OrderResultError.Success'))
								$arrReturn['errInfo'] = $arrJmpReturn['errInfo'];
						}
						else
							$arrReturn['code'] = C('OrderResultError.NetPaying');
					}
				}

				
				return $arrReturn;
			}else{//内部支付方式:1:现金红包 2:消费红包 3.手动充值
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '内部接口';
				return $arrReturn;
			}

		}else{
			$bDid = false;
			// 如果已经完成，则进行支付订单的状态转换处理
			if( ($aFundOrder['type_id'] == C('OrderType.Recharge') && $aFundOrder['netpay_status']==C('NetPayStatus.SUCCESS') ) ||
				$aFundOrder['type_id'] == C('OrderType.Promote') ){
				// 支付可能有多个处理接口:比如有同步jump和异步notice两个接口
				if($aFundOrder['status']==C('FundOrderStatus.Settled'))
					$bDid = true;
				else
					$bDid = $this->onRechargePromoteFinish($fundOrderId);
			}else if( $aFundOrder['type_id'] == C('OrderType.Cash') ){
				if ($aFundOrder['status'] == C('FundOrderStatus.Paying'))
					$bDid =  $this->onTakeCashPayed($fundOrderId);
				else if ($aFundOrder['status'] == C('FundOrderStatus.Payed') && $aFundOrder['netpay_status']==C('NetPayStatus.FAILED')) // NetPay,网络/手动转帐失败
					$bDid =  $this->onTakeCashFailed($fundOrderId);
				else if ($aFundOrder['status'] == C('FundOrderStatus.Payed') && $aFundOrder['netpay_status']==C('NetPayStatus.SUCCESS')) // NetPay,网络/手动转帐成功
					$bDid =  $this->onTakeCashFinish($fundOrderId);
				else if ($aFundOrder['status'] == C('FundOrderStatus.Settled'))
					$bDid = true;
			}else if($aFundOrder['type_id'] == C('OrderType.Consume') &&  $aFundOrder['netpay_amt']==0){
				//余额支付
				$arrReturn = $this->payed($fundOrderId,true);
				if ($arrReturn['code'] == C('OrderResultError.Success')){
					$bDid = true;
					$aFundOrder['return_url'] = null;
				}
			}else if($aFundOrder['type_id'] == C('OrderType.Consume') &&  $aFundOrder['netpay_amt']>0 && $aFundOrder['netpay_status']==C('NetPayStatus.SUCCESS')){
				//在线支付
				$arrReturn = $this->payed($fundOrderId,false);
				if ($arrReturn['code'] == C('OrderResultError.Success'))
					$bDid = true;
			}else if($aFundOrder['type_id'] == C('OrderType.AfterSalesRefund')){
				if ($aFundOrder['status'] == C('FundOrderStatus.Paying'))
					$bDid =  $this->onRefundFinish($fundOrderId);
				else if ($aFundOrder['status'] == C('FundOrderStatus.Refunded'))
					$bDid = true;
			}

			if($bDid){
				$arrReturn['code'] = C('OrderResultError.Success');
				$arrReturn['errInfo'] = $aFundOrder['return_url'];
			}else{
				$arrReturn['code'] = C('OrderResultError.Failure');
			}

			return $arrReturn;
		}

	}

	public function doNextStep($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		$aFundOrder = $this->ci->Fundorder_model->findByOrderId($orderId);
		if(empty($aFundOrder)){
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = '未找到订单';
			return $arrReturn;
		}

		$arrReturn = $this->nextStep($aFundOrder['fund_order_id']);

		return $arrReturn;
	}

	// 加款/推广订单完成处理流程
	private function onRechargePromoteFinish($fundOrderId){
		$result = false;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		if($this->ci->Fundorder_model->payed($aFundOrder) )
			$aFundOrder['status'] = C('FundOrderStatus.Payed');

		//加款订单，直接完成
		if(isRechargeOrder($aFundOrder) && $aFundOrder['status'] = C('FundOrderStatus.Payed')){
			if ($this->ci->Fundorder_model->waitingSettle($aFundOrder)) {
				$aFundOrder['status'] = C('FundOrderStatus.WaitingSettle');
			}

			if ($aFundOrder['status']== C('FundOrderStatus.WaitingSettle')) {
				if ($this->ci->Fundorder_model->settled($aFundOrder)) {
					$result = true;
					$aFundOrder['status'] = C('FundOrderStatus.Settled');

					// [start] todo:发消息
					if ($aFundOrder['type_id'] == C('OrderType.Recharge')) {// 充值成功,发消息


						/*$templateId = 0;
						if (fundOrder.getNetrechargeAmt().compareTo(BigDecimal.ZERO) == 1)
							templateId = Integer.parseInt(Constant.getStPara("SEND_PROMOTE_RECHARGE_MESSAGE_ID"));
						else
							templateId = Integer.parseInt(Constant.getStPara("SEND_RECHARGE_MESSAGE_ID"));

						if (templateId > 0) {
							Map<String, Object> paramMap = new HashMap<String, Object>();
							paramMap.put("{USERNAME}", fundOrder.getBuyerUserName());
							paramMap.put("{MONEY}", fundOrder.getNetpayAmt().toString());
							paramMap.put("{GIFT_MONEY}", fundOrder.getNetrechargeAmt().toString());
							inter_Message_CenterService.addMessage(0, templateId, fundOrder.getBuyerUserId().toString(), MessageReceiveTypeEnum.Batch_User.getIndex(), fundOrder.getPlatformId(), paramMap);
						}*/

					}

					// [end]
				}
			}


		}
		// 用户支付额度...
		//todo系统日志
		//logger.info("recharge " + fundOrder.getFundOrderId() + ":" + result);
		return $result;
	}

	private function onTakeCashPayed($fundOrderId){
		$result = false;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		// 提现订单，先将用户的余额扣除
		if($this->ci->Fundorder_model->payed($aFundOrder)){
			$result = true;
			$aFundOrder['status'] = C('FundOrderStatus.Payed');
		}

		return $result;
	}

	private function onTakeCashFailed($fundOrderId) {
		$result = false;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		// 提现失败，将用户扣除的余额退回
		if ($aFundOrder['status'] != C('FundOrderStatus.Payed')) {
			$result = false;
		} else {
			return $this->ci->Fundorder_model->refund($aFundOrder);
		}

		return $result;
	}

	// 提现订单完成处理流程
	private function onTakeCashFinish($fundOrderId) {
		$result = false;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);

		// 提现失败，将用户扣除的余额退回
		if (isTakeCashOrder($aFundOrder) && $aFundOrder['status'] == C('FundOrderStatus.Payed')) {
			if ($this->ci->Fundorder_model->waitingSettle($aFundOrder)) {
				$aFundOrder['status'] = C('FundOrderStatus.WaitingSettle');
			}

			if ($aFundOrder['status']==C('FundOrderStatus.WaitingSettle')) {
				if ($this->ci->Fundorder_model->settledTakeCash($aFundOrder)) {
					$result = true;
					$aFundOrder['status'] = C('FundOrderStatus.Settled');
				}
			}

			/*
			 * 结算--异步加载 if
			 * (!fundOrder.getStatus().equals(FundOrderStatusEnum.Settled
			 * .to$())) { TaskManager.AddSysTasks(); }
			 */

		}

		return $result;
	}

	private function onRefundFinish($fundOrderId){
		$result = false;
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		//扣除卖家的余额
		if($this->ci->Fundorder_model->payedRefund($aFundOrder)){
			$result = true;
			$aFundOrder['status'] = C('FundOrderStatus.Payed');
		}

		if($aFundOrder['status'] == C('FundOrderStatus.Payed')){
			if ($this->ci->Fundorder_model->waitingSettle($aFundOrder)) {
				$aFundOrder['status'] = C('FundOrderStatus.WaitingSettle');
			}
		}

		if ($aFundOrder['status']==C('FundOrderStatus.WaitingSettle')) {
			if ($this->ci->Fundorder_model->refundStep($aFundOrder)) {
				$result = true;
				$aFundOrder['status'] = C('FundOrderStatus.Refunded');
			}
		}

		return $result;
	}


	// 余额/在线 支付
	private function payed($fundOrderId, $isBalance=true) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		// 查询订单（检查订单是否存在）
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		if(empty($aFundOrder)){
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = '未找到订单';
			return $arrReturn;
		}
		$arrReturn['fund_order_id'] = $aFundOrder['fund_order_id'];

		if($isBalance){
			if($aFundOrder['total_amt']!=$aFundOrder['balance_amt']+$aFundOrder['cash_coupon_amt']){
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '余额支付与总金额不相等';
				return $arrReturn;
			}
		}else{
			if($aFundOrder['total_amt']!=$aFundOrder['netpay_amt']+$aFundOrder['cash_coupon_amt']){
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '在线支付与总金额不相等';
				return $arrReturn;
			}
		}

		if($aFundOrder['status'] == C('FundOrderStatus.Waiting')){
			// 支付
			if(!$this->ci->Fundorder_model->paying($aFundOrder)){
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '支付失败,可用余额不足';

				$this->ci->Fundorder_model->tranStatus($aFundOrder, C('FundOrderStatus.Waiting'), C('FundOrderStatus.Closed'));
				return $arrReturn;
			}

			$aFundOrder['status'] = C('FundOrderStatus.Paying');
		}

		if($aFundOrder['status'] == C('FundOrderStatus.Paying')){
			if (!$this->ci->Fundorder_model->payed($aFundOrder)) {
				$arrReturn['code'] = C('OrderResultError.Failure');
				$arrReturn['errInfo'] = '支付失败,可用余额不足.';

				$this->ci->Fundorder_model->tranStatus($aFundOrder, C('FundOrderStatus.Paying'), C('FundOrderStatus.Closed'));
				return $arrReturn;

			}

			$aFundOrder['status'] = C('FundOrderStatus.Payed');
		}

		if ($aFundOrder['status']==C('FundOrderStatus.Payed')) {
			$this->ci->Fundorder_model->waitingSettle($aFundOrder);
			$aFundOrder['status'] = C('FundOrderStatus.WaitingSettle');
		}

		if($aFundOrder['status']==C('FundOrderStatus.WaitingSettle')){
			$arrReturn['code'] = C('OrderResultError.Success');
			$arrReturn['errInfo'] = '支付成功，等待结算';
		}

		return $arrReturn;

	}

	// 更新退款 & 手续费
	public function updateByRefund($fundOrderId, $refundAmt, $tradeProfitRate, $tradeProfitRateType) {
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		if ($aFundOrder['status']!=C('FundOrderStatus.WaitingSettle'))
			return false;

		// [start]手续费规则
		// 手续费(feeAmount) = 利润分成(profitAmount) + 个人所得税(taxAmount)
		$feeAmount = 0;

		/*$subtractRefundAmt = $aFundOrder['total_amt']-$refundAmt;
		$TRADE_PROFIT_RATE_TYPE_MULTIPLY = C("TRADE_PROFIT_RATE_TYPE_MULTIPLY");
		$profitAmount = $subtractRefundAmt.multiply($tradeProfitRate);
		if ($tradeProfitRateType != $TRADE_PROFIT_RATE_TYPE_MULTIPLY && $subtractRefundAmt-$tradeProfitRate)>0)
			$profitAmount = $subtractRefundAmt - $tradeProfitRate;

		// 专家-个人所得税
		$TRADE_PROFIT_INCOME_TAX = C("TRADE_PROFIT_INCOME_TAX");
		$incomeAmount = $subtractRefundAmt - $profitAmount;
		if ($incomeAmount > $TRADE_PROFIT_INCOME_TAX) {
			// 缴税 taxAmount
			$TRADE_PROFIT_INCOME_TAX_RATE = C("TRADE_PROFIT_INCOME_TAX_RATE");
			$taxAmount = ($incomeAmount - $TRADE_PROFIT_INCOME_TAX)*$TRADE_PROFIT_INCOME_TAX_RATE;
			$feeAmount = $profitAmount.add($taxAmount);
		} else
			$feeAmount = $profitAmount;*/
		// [end]

		$this->ci->Fundorder_model->update_by_id($fundOrderId, array('refund'=>$refundAmt, 'feeAmount'=>$feeAmount));

		return true;

	}

	public function giveCashBonus($title, $toUserId, $toUserName, $bonusAmt, $ip, $platformId) {
		return $this->addMoney($title, $toUserId, $toUserName, $bonusAmt, C('OrderType.Promote'), C('PayMethodType.Cash_Bonus'), null, null, null, $ip, $platformId);
	}

	public function giveConsumeBonus($title, $toUserId, $toUserName, $bonusAmt, $ip, $platformId) {
		return $this->addMoney($title, $toUserId, $toUserName, $bonusAmt, C('OrderType.Promote'), C('PayMethodType.Consume_Bonus'), null, null, null, $ip, $platformId);
	}

	public function rechargeByHand($title, $toUserId, $toUserName, $amount, $payMethod, $ip, $platformId) {
		return $this->addMoney($title, $toUserId, $toUserName, $amount, C('OrderType.Recharge'), $payMethod, null, null, null, $ip, $platformId);
	}

	public function recharge($title, $toUserId, $toUserName, $amount, $payMethod, $netpayAccount, $netpayAccountid, $extParam, $ip, $platformId) {
		return $this->addMoney($title, $toUserId, $toUserName, $amount, C('OrderType.Recharge'), $payMethod, $netpayAccount, $netpayAccountid, $extParam, $ip, $platformId);
	}

	private function addMoney($title, $toUserId, $toUserName, $amount, $type, $payMethod, $netpayAccount, $netpayAccountid, $extParam, $ip, $platformId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		if ($type != C('OrderType.Promote') && $type != C('OrderType.Recharge')) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = '只能是推广或充值';
			return $arrReturn;
		}

		// 创建订单
		// [start] 赋值
		$oCreate = array('title'=>$title,'buyer_userid'=>$toUserId, 'buyer_username'=>$toUserName,
			'seller_userid'=>$toUserId, 'seller_username'=>$toUserName, 'type_id'=>$type, 
			'total_amt'=>$amount, 'netpay_method'=>$payMethod);
		if($payMethod==C('PayMethodType.Balance_Hand'))	// 后台手动加款
			$oCreate['netpay_status'] = C('NetPayStatus.SUCCESS');
		else if($payMethod==C('PayMethodType.Cash_Bonus')){
			$oCreate['return_url'] = C('PayUrl.PAY_JUMP_URL') + '/'+ $payMethod;
			$oCreate['notice_url'] = C('PayUrl.PAY_NOTICE_URL') + '/'+ $payMethod;
		}

		if($type == C('OrderType.Recharge'))
			$oCreate['netpay_amt'] = $amount;
		else
			$oCreate['bonus_amt'] = $amount;

		if(!empty($netpayAccount))
			$oCreate['netpay_account']=$netpayAccount;

		if (!empty($extParam) )
			$oCreate['extparam']=$extParam;
		$oCreate['ip']=$ip;

		if ($platformId == 0)
			$platformId = C("basic_info.PLATFORM_ID");
		$oCreate['platform_id']=$platformId;
		// [end]

		// [start] Active活动
		//oCreate = promoteMngService.initDataByActive(new PromoteActiveEnum[] { PromoteActiveEnum.PROMOTE_RECHARGE }, oCreate);
		// [end]

		// 创建订单
		$arrReturn = $this->createOrder($oCreate);
		if ($arrReturn['code']==C('OrderResultError.Success')) {
			$fundOrderId = $arrReturn['fund_order_id'];
			$arrReturn = $this->paying($fundOrderId);
			if ($arrReturn['code']==C('OrderResultError.Failure'))
				return $arrReturn;

			// 跳转到支付页面
			$arrReturn = $this->nextStep($fundOrderId);
		}

		return $arrReturn;
	}

	// 提现
	public function takeCash($title, $toUserId, $toUserName, $amount, $payMethod, $netpayAccount, $extParam, $ip, $platformId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		$limitMax = C("basic_info.TAKE_CASH_LIMIT_MAX");
		$limitMin = C("basic_info.TAKE_CASH_LIMIT_MIN");
		$eachFee = C("basic_info.TAKE_CASH_EACH_FEE");
		if ($amount<=$limitMin || $amount>$limitMax) {
			$arrReturn['code'] = 'FUND_AMOUNT_ERROR';//C('OrderResultError.Failure');
			$arrReturn['errInfo'] = '金额超出合理范围';
			return $arrReturn;
		}

		// 可提现额度
		$aUserAccount = $this->ci->Account_model->get_by_id($toUserId);
		if (empty($aUserAccount)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = '用户帐户不存在';
			return $arrReturn;
		}

		/*if ($aUserAccount['acct_withdraw_amt_limit'] < $amount) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = '可提现额度不够';
			return $arrReturn;
		}*/

		// 创建订单
		// [start] 赋值
		$oCreate = array('title'=>$title,'buyer_userid'=>$toUserId, 'buyer_username'=>$toUserName,
			'seller_userid'=>$toUserId, 'seller_username'=>$toUserName, 'type_id'=>C('OrderType.Cash'), 
			'total_amt'=>$amount, 'netpay_method'=>$payMethod, 'balance_amt'=>$amount,// Payed时,扣除买家余额
			'fee_amt'=>$eachFee, 'ip'=>$ip );
		if ($platformId == 0)
			$platformId = C("basic_info.PLATFORM_ID");
		$oCreate['platform_id']=$platformId;
		if(!empty($netpayAccount))
			$oCreate['netpay_account']=$netpayAccount;

		if (!empty($extParam) )
			$oCreate['extparam']=$extParam;
		// [end]
		$arrReturn = $this->createOrder($oCreate);
		if ($arrReturn['code']==C('OrderResultError.Success')) {
			$fundOrderId = $arrReturn['fund_order_id'];
			$arrReturn = $this->paying($fundOrderId);
			if ($arrReturn['code']==C('OrderResultError.Failure'))
				return $arrReturn;

			// 跳转到支付页面
			$arrReturn = $this->nextStep($fundOrderId);
		}

		return $arrReturn;
	}

	public function doConsume($orderId, $orderSn, $title, $shop_id, $company_id, $buyerUserId, $buyerUserName, $amount, $sellerUserId, $sellerUserName, $netpayMethod, $extParam, $ip, $platformId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		$aFundOrder = $this->ci->Fundorder_model->findByOrderId($orderId);
		if (empty($aFundOrder)) {
			// [start] 赋值
			if($netpayMethod>10)
				$netpay_amt = $amount;
			else if($netpayMethod==C('PayMethodType.AllBalance'))
				$netpay_amt = 0;

			$oCreate = array('order_id'=>$orderId, 'order_sn'=>$orderSn,'type_id'=>C('OrderType.Consume'),
				'title'=>$title, 'site_id'=>$shop_id, 'company_id'=>$company_id,
				 'total_amt'=>$amount,'bonus_amt'=>0, 'netpay_amt'=>$netpay_amt,
				'netpay_method'=>$netpayMethod,'cash_coupon_amt'=>0,
				'pay_proof_amt'=>0,'fee_amt'=>0,'refund'=>0,'buyer_userid'=>$buyerUserId,
				'buyer_username'=>$buyerUserName,'seller_userid'=>$sellerUserId, 'seller_username'=>$sellerUserName,
				'ip'=>$ip,'platform_id'=>$platformId,
			);
			if (!empty($extParam) )
				$oCreate['extparam']=$extParam;
			// [end]

			// [start] Active活动
			//fundOrder = promoteMngService.initDataByActive(new PromoteActiveEnum[] { PromoteActiveEnum.PROMOTE_REDUCE }, fundOrder);
			// [end]

			$balanceAmt = $oCreate['total_amt'] - $oCreate['netpay_amt'] - $oCreate['cash_coupon_amt'] - $oCreate['pay_proof_amt'] - $oCreate['fee_amt'];
			$oCreate['balance_amt'] = $balanceAmt;

			$arrReturn = $this->createOrder($oCreate);
			if ($arrReturn['code']!=C('OrderResultError.Success'))
				return $arrReturn;
		} else {
			$data = array();
			if($aFundOrder['netpay_method']!=$netpayMethod){
				$data['netpay_method'] = $netpayMethod;
				if($netpayMethod==C('PayMethodType.AllBalance')){
					$data['balance_amt'] = $aFundOrder['balance_amt']+$aFundOrder['netpay_amt'];
					$data['netpay_amt'] = 0;
				}else{
					$data['balance_amt'] = 0;
					$data['netpay_amt'] = $aFundOrder['balance_amt']+$aFundOrder['netpay_amt'];
				}
			}
			if (!empty($extParam) )
				$data['extparam']=$extParam;

			if(!empty($data))
				$this->ci->Fundorder_model->update_by_id($aFundOrder['fund_order_id'],$data);

			$arrReturn['code'] = C('OrderResultError.Success');
			$arrReturn['fund_order_id'] = $aFundOrder['fund_order_id'];
			$arrReturn['errInfo'] = '订单已存在';
		}

		return $arrReturn;
	}

	// 解析页面跳转返回的函数
	//$param  array
	public function jump($payMethodName, $param) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0,'order_id'=>0);
		$obj = $this->tryGetPay($payMethodName);

		if (empty($obj)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "jump:接口($payMethodName)不存在";

			//系统日志
			//logger.error(result.getErrInfo());
			return $arrReturn;
		}

		$payNoticeResult = $obj->parseJump($param);
		if (empty($payNoticeResult)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "jump:接口($payMethodName))跳转参数解析失败";

			//logger.error(result.getErrInfo());
			return $arrReturn;
		}
		//系统日志
		//logger.info(String.format("jump fundOrderId:%d", payNoticeResult.getFundOrderId()));
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($payNoticeResult['fund_order_id']);
		$arrReturn = $this->dealNetPayed($payNoticeResult, $aFundOrder);

		// if (payNoticeResult.getExtResult() != null)
		// result.setErrInfo(payNoticeResult.getExtResult());

		return $arrReturn;
	}

	// 解析页面跳转返回的函数
	//$param  array
	public function notice($payMethodName, $param) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0,'order_id'=>0);	//,'order_sn'=>''  多订单
		$this->ci->load->model('Site_config_model');
		$obj = $this->tryGetPayByName($payMethodName);

		if (empty($obj)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "notice:接口($payMethodName)不存在";

			//系统日志
			//logger.error(result.getErrInfo());
			return $arrReturn;
		}

		$aFundOrder = $this->ci->Fundorder_model->get_by_id($payNoticeResult['fund_order_id']);
		$aPayConfig = $this->ci->Site_config_model->getPayConfig($aFundOrder['site_id'],$aFundOrder['company_id']);
		if(empty($aPayConfig) || empty($aPayConfig['APPID'])){
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = '支付配置参数未配置.';
			return $arrReturn;
		}

		$payNoticeResult = $obj->parseNotice($param, $aPayConfig);
		if (empty($payNoticeResult) || !$payNoticeResult['isSuccess']) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "notice:接口($payMethodName))跳转参数解析失败";

			//logger.error(result.getErrInfo());
			return $arrReturn;
		}
		//系统日志
		//logger.info(String.format("jump fundOrderId:%d", payNoticeResult.getFundOrderId()));

		
		//$arrReturn['order_sn'] = $aFundOrder['order_sn'];  //多订单用order_sn
		$arrReturn['order_id'] = $aFundOrder['order_id'];
		$arrReturn = $this->dealNetPayed($payNoticeResult, $aFundOrder);

		return $arrReturn;
	}

	public function checkedCashByHand($fundOrderId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		// 提现状态需为Payed
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		if (empty($aFundOrder)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 不存在";
			return $arrReturn;
		} else if ($aFundOrder['status']!=C('FundOrderStatus.Payed')) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 提现余额未扣";
			return $arrReturn;
		}

		// 修改订单-NetPay_Status ->SUCCESS
		if (!$this->ci->Fundorder_model->updateNetPayStatus($fundOrderId, C('NetPayStatus.SUCCESS'), null)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 修改netpayStatus状态失败";
		} else {
			$arrReturn = $this->nextStep($fundOrderId);
		}

		return $arrReturn;
	}

	public function refundCashByHand($fundOrderId) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);

		// 提现状态需为Payed
		$aFundOrder = $this->ci->Fundorder_model->get_by_id($fundOrderId);
		if (empty($aFundOrder)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 不存在";
			return $arrReturn;
		} else if ($aFundOrder['status']!=C('FundOrderStatus.Payed')) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 提现余额未扣";
			return $arrReturn;
		}

		// 修改订单-NetPay_Status ->FAILED
		if (!$this->ci->Fundorder_model->updateNetPayStatus($fundOrderId, C('NetPayStatus.FAILED'), null)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:$fundOrderId 在线提现失败";
		} else {
			$arrReturn = $this->nextStep($fundOrderId);
		}

		return $arrReturn;
	}

	// 基本检查金额是否正确
	private function checkNoticeParam($payNoticeResult, $arrFundOrder) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0);
		$arrReturn['fund_order_id'] = $arrFundOrder['fund_order_id'];
		if ($arrFundOrder['netpay_amt']!=$payNoticeResult['amount']) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "金额不对";
			//系统日志
			//logger.error(String.format("订单(%d)金额不对,订单金额:%f,传参金额:%f", fundOrder.getFundOrderId(), fundOrder.getNetpayAmt(), payNoticeResult.getAmount()));
			return $arrReturn;
		}

		$arrReturn['code'] = C('OrderResultError.Success');
		$arrReturn['errInfo'] = "";

		return $arrReturn;
	}

	private function dealNetPayed($payNoticeResult, $arrFundOrder) {
		$arrReturn = array('code'=>'Empty','errInfo'=>'','fund_order_id'=>0,'order_id'=>0);
		if (empty($arrFundOrder)) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单不存在";
			//系统日志
			//logger.error(String.format("订单(%d)不存在", payNoticeResult.getFundOrderId()));
			return $arrReturn;
		}

		// 基本检查金额是否正确
		$arrReturn = $this->checkNoticeParam($payNoticeResult, $arrFundOrder);
		if ($arrReturn['code']!=C('OrderResultError.Success'))
			return $arrReturn;

		// 判断是否被其他接口已处理,比如notice
		if ($arrFundOrder['status']==C('FundOrderStatus.Settled')) {
			$arrReturn['code'] = C('OrderResultError.Success');
			$arrReturn['errInfo'] = '';
			return $arrReturn;
		}

		// 修改订单-NetPay_Status ->SUCCESS
		if (!$this->ci->Fundorder_model->updateNetPayStatus($payNoticeResult['fund_order_id'], C('NetPayStatus.SUCCESS'), $payNoticeResult['seq_no'])) {
			$arrReturn['code'] = C('OrderResultError.Failure');
			$arrReturn['errInfo'] = "订单:".$payNoticeResult['fund_order_id']." 修改netpayStatus状态失败";
			return $arrReturn;
		} else {
			$arrFundOrder['netpay_status'] = C('NetPayStatus.SUCCESS');
		}

		$bResult = false;
		$arrReturn['errInfo'] = "";
		if (( intval($arrFundOrder['type_id'])  == C('OrderType.Recharge') && $arrFundOrder['netpay_status']==C('NetPayStatus.SUCCESS')) || $arrFundOrder['type_id'] == C('OrderType.Promote'))
			$bResult = $this->onRechargePromoteFinish($arrFundOrder['fund_order_id']);
		//消费订单处理 ->Payed
		else if($arrFundOrder['type_id'] == C('OrderType.Consume') &&  $arrFundOrder['netpay_amt']>0 && $arrFundOrder['netpay_status']==C('NetPayStatus.SUCCESS')){
			//在线支付
			$arrReturn = $this->payed($payNoticeResult['fund_order_id'],false);
			if ($arrReturn['code'] == C('OrderResultError.Success'))
				$bResult = true;
		}

		//系统日志
		//logger.info("dealNetPayed fundOrderId:" + fundOrder.getFundOrderId() + "--" + bResult);
		if ($bResult)
			$arrReturn['code'] = C('OrderResultError.Success');
		else
			$arrReturn['code'] = C('OrderResultError.Failure');

		$arrReturn['order_id'] = $arrFundOrder['order_id'];

		return $arrReturn;
	}

	public function refund($orderId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');

		$aFundOrder = $this->ci->Fundorder_model->findByOrderId($orderId);
		if(empty($aFundOrder))
		{
			$arrReturn['code'] = C('OrderResultError.OrderNotExits');
			$arrReturn['errInfo'] = "订单不存在";
			return $arrReturn;
		}

		// if($aFundOrder['refund']==0){
		// 	$aFundOrder['refund'] = $aFundOrder['balance_amt']+$aFundOrder['netpay_amt'];
		// 	$this->ci->Fundorder_model->update_by_id($aFundOrder['fund_order_id'], array('refund'=>$aFundOrder['refund']));
		// }

		$bResult = $this->ci->Fundorder_model->refund($aFundOrder);
		if ($bResult)
			$arrReturn['code'] = C('OrderResultError.Success');
		else
			$arrReturn['code'] = C('OrderResultError.Failure');


		return $arrReturn;
	}

	public function refundStep($refundId, $buyer_userid, $buyer_username, $seller_userid, $seller_username, $refundAmt, $ip, $platformId){
		$arrReturn = array('code'=>'Empty','errInfo'=>'');

		$aFundOrder = $this->ci->Fundorder_model->findByRefundId($refundId);
		if (empty($aFundOrder)) {
			// 创建订单
			// [start] 赋值
			$oCreate = array('title'=>'退款','buyer_userid'=>$buyer_userid, 'buyer_username'=>$buyer_username,
			'seller_userid'=>$seller_userid, 'seller_username'=>$seller_username, 'type_id'=>C('OrderType.AfterSalesRefund'), 
			'total_amt'=>$refundAmt, 'netpay_method'=>C('PayMethodType.Balance_Refund'));
			$oCreate['refund_id'] = $refundId;
			$oCreate['refund'] = $refundAmt;
			$oCreate['ip']=$ip;
			if ($platformId == 0)
				$platformId = C("basic_info.PLATFORM_ID");
			$oCreate['platform_id']=$platformId;
			// [end]

			$arrReturn = $this->createOrder($oCreate);
			if ($arrReturn['code']!=C('OrderResultError.Success'))
				return $arrReturn;

			$fundOrderId = $arrReturn['fund_order_id'];
		}
		else
			$fundOrderId = $aFundOrder['fund_order_id'];

		if($aFundOrder['status']==C('FundOrderStatus.Refunded')) {
			$arrReturn['code'] = C('OrderResultError.Success');
			return $arrReturn;
		}

		$arrReturn = $this->paying($fundOrderId);
		if ($arrReturn['code']==C('OrderResultError.Failure'))
			return $arrReturn;

		//下一步
		$arrReturn = $this->nextStep($fundOrderId);

		return $arrReturn;
	}
	
}