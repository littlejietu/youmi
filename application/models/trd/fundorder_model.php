<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FundOrder_model extends XT_Model {

	protected $mTable = 'trd_fundorder';
	protected $mPkId = 'fund_order_id';

	private $XT_ACCOUNT_USER_ID = 2;
	private $XT_ACCOUNT_USER_NAME = 'XTAccount';
	private $XT_ACCOUNT_COUPON_USER_ID = 3;
	private $XT_ACCOUNT_COUPON_USER_NAME = 'XTCoupon';
	
	public function findByOrderSn($orderSn)
	{
		$aFundOrder = $this->get_by_where(array('order_sn'=>$orderSn));
		return $aFundOrder;
	}
	public function findByOrderId($orderId)
	{
		$aFundOrder = $this->get_by_where(array('order_id'=>$orderId));
		return $aFundOrder;
	}

	public function findByRefundId($refundId){
		$aFundOrder = $this->get_by_where(array('refund_id'=>$refundId));
		return $aFundOrder;
	}

	/**
	 * 订单状态检查及更新，先检查订单当前状态与预期状态是否一致
	 * @param array $arrTrdOrder 订单
	 * @return boolean true/false
	 */
	public function tranStatus($arrFundOrder, $statusFrom, $statusTo)
	{
		if($arrFundOrder['status']==$statusTo)
			return true;
		if($arrFundOrder['status']!=$statusFrom)
			return false;

		$data = array('status'=>$statusTo,'last_update_time'=>time());
		$where = array('fund_order_id'=>$arrFundOrder['fund_order_id'], 'status'=>$statusFrom);
		if($statusTo==C('FundOrderStatus.Refunded') || $statusTo==C('FundOrderStatus.Settled')){
			$data['confirm_time'] = time();
		}

		$result = $this->update_by_where($where,$data);
		return $result>0;
	}

	public function updateNetPayStatus($fundOrderId, $statusTo, $seq_no){
		$where = array('fund_order_id'=>$fundOrderId);
		$data = array('netpay_status'=>$statusTo,'confirm_time'=>time(),'netpay_seqno'=>$seq_no);
		$result = $this->update_by_where($where,$data);

		return $result>0;
	}

	public function paying($arrFundOrder){
		if($arrFundOrder['status']==C('FundOrderStatus.Paying'))
			return true;

		return $this->tranStatus($arrFundOrder, C('FundOrderStatus.Waiting'), C('FundOrderStatus.Paying'));
	}

	public function payed($arrFundOrder){
		return $arrFundOrder['status']==C('FundOrderStatus.Payed') || $this->deductAmountFromBuyer($arrFundOrder);
	}

	public function payedRefund($arrFundOrder){
		return $arrFundOrder['status']==C('FundOrderStatus.Payed') || $this->deductAmountFromSeller($arrFundOrder);
	}

	public function waitingSettle($arrFundOrder){
		return $this->tranStatus($arrFundOrder, C('FundOrderStatus.Payed'), C('FundOrderStatus.WaitingSettle'));
	}

	public function settled($arrFundOrder){
		return $arrFundOrder['status']==C('FundOrderStatus.Settled') || $this->raiseAmountToSeller($arrFundOrder);
	}

	public function settledTakeCash($arrFundOrder){
		return $arrFundOrder['status']==C('FundOrderStatus.Settled') || $this->raiseFeeToAdmin($arrFundOrder);
	}

	public function refund($arrFundOrder){
		if($arrFundOrder['status']==C('FundOrderStatus.Waiting'))
			return $this->tranStatus($arrFundOrder, C('FundOrderStatus.Waiting'), C('FundOrderStatus.Closed'));

		if($arrFundOrder['status']==C('FundOrderStatus.Refunded'))
			return true;

		if($arrFundOrder['refund']>$arrFundOrder['balance_amt']+$arrFundOrder['netpay_amt'])
			return false;

		return $this->refundSettled($arrFundOrder);
	}

	public function refundStep($arrFundOrder){
		if($arrFundOrder['status']==C('FundOrderStatus.Refunded'))
			return true;

		if($arrFundOrder['refund']!=$arrFundOrder['total_amt'])
			return false;

		return $this->refundSettled($arrFundOrder);
	}

	private function raiseFeeToAdmin($arrFundOrder){
		//tran 修改状态,改变用户金额
		$this->db->trans_begin();
		if(!$this->tranStatus($arrFundOrder, C('FundOrderStatus.WaitingSettle'), C('FundOrderStatus.Settled'))){
			$this->db->trans_rollback();
			return false;
		}
		else
			$arrFundOrder['status'] = C('FundOrderStatus.Settled');

		// 判断是否提现订单
		if(isTakeCashOrder($arrFundOrder)){
			// 扣除手续费
			$remark = '提现手续费';
			if($arrFundOrder['fee_amt']>0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.CashFeeOut'), -1 * $arrFundOrder['fee_amt'], $remark, $arrFundOrder['platform_id']) ){
				$this->db->trans_rollback();
				return false;
			}

			if($arrFundOrder['fee_amt']>0 && !$this->accountBalanceChange($this->XT_ACCOUNT_USER_ID, $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.CashFeeIn'), $arrFundOrder['fee_amt'], $remark, $arrFundOrder['platform_id']) ){
				$this->db->trans_rollback();
				return false;
			}

		}

		$this->db->trans_commit();
		//-tran
		return true;
	}

	// 扣除买家资金
	// 当充值时直接Payed,扣款余额为0--消费
	// 1.扣除的是买家的余额,无需修改买家额度
	// 2.买家扣款并记录日志
	// 3.提现:扣除 可提现额度
	private function deductAmountFromBuyer($arrFundOrder){
		$aAccount = M('Account')->get_by_where(array('user_id'=>$arrFundOrder['buyer_userid']));
		// 余额不足，返回扣款失败
		if($aAccount['acct_balance']-$arrFundOrder['balance_amt']<0)
			return false;
		//tran 修改状态,改变用户金额
		$this->db->trans_begin();
		// 1.更新订单状态为已支付
		if (!$this->tranStatus($arrFundOrder, C('FundOrderStatus.Paying'), C('FundOrderStatus.Payed')) ) {
			$this->db->trans_rollback();
			return false;
		} else
			$arrFundOrder['status'] = C('FundOrderStatus.Payed');

		$type = C('BalanceLogType.TradeOut');
		if ($arrFundOrder['type_id'] == C('OrderType.Cash')) // 提现--转出
			$type = C('BalanceLogType.TradeInerOut');

		// 2.买家扣款并记录日志
		// 加款，推广，提现--余额支付均是0
		// 2.1买家扣款
		$remark = C('OrderTypeName.'.$arrFundOrder['type_id']);
		if ($arrFundOrder['balance_amt'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], $type, $arrFundOrder['balance_amt']*-1, $remark, $arrFundOrder['platform_id']) ) {
			$this->db->trans_rollback();
			return false;
		}
		// 2.2代金券帐户
		if ($arrFundOrder['cash_coupon_amt'] > 0 && !$this->accountBalanceChange($this->XT_ACCOUNT_COUPON_USER_ID, $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.TradeInerOut'), $arrFundOrder['cash_coupon_amt']*-1, $remark, $arrFundOrder['platform_id'])) {
			$this->db->trans_rollback();
			return false;
		}

		// 3.提现:买家用户减额度(提现)
		if ($arrFundOrder['type_id'] == C('OrderType.Cash') && !$this->accountLimitChangeFromBuyer($arrFundOrder)) {
			$this->db->trans_rollback();
			return false;
		}


		$this->db->trans_commit();
		//-tran
		return true;
	}

	// 售后退款：扣除卖家资金
	private function deductAmountFromSeller($arrFundOrder){
		$aAccount = M('Account')->get_by_where(array('user_id'=>$arrFundOrder['seller_userid']));
		// 余额不足，返回扣款失败
		if($aAccount['acct_balance']-$arrFundOrder['balance_amt']<0)
			return false;
		//tran 修改状态,改变用户金额
		$this->db->trans_begin();
		// 1.更新订单状态为已支付
		if (!$this->tranStatus($arrFundOrder, C('FundOrderStatus.Paying'), C('FundOrderStatus.Payed')) ) {
			$this->db->trans_rollback();
			return false;
		} else
			$arrFundOrder['status'] = C('FundOrderStatus.Payed');

		$type = C('BalanceLogType.RefundTradeOut');

		// 2.卖家扣款并记录日志
		// refund:退款金额
		$remark = C('OrderTypeName.'.$arrFundOrder['type_id']);
		if ($arrFundOrder['refund'] > 0 && !$this->accountBalanceChange($arrFundOrder['seller_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], $type, $arrFundOrder['refund']*-1, $remark, $arrFundOrder['platform_id']) ) {
			$this->db->trans_rollback();
			return false;
		}

		$this->db->trans_commit();
		//-tran
		return true;
	}

	// 结算流程处理--交易收入
	private function raiseAmountToSeller($arrFundOrder){
		// 加款总金额
		// 加款总金额
		$tradeInAmount = $arrFundOrder['total_amt']-$arrFundOrder['refund']-$arrFundOrder['fee_amt'];

		// [start] tran 修改状态,改变用户金额{
		$this->db->trans_begin();
		// 1.更新订单状态为已结算
		if (!$this->tranStatus($arrFundOrder, C('FundOrderStatus.WaitingSettle'), C('FundOrderStatus.Settled'))) {
			$this->db->trans_rollback();
			return false;
		} else
			$arrFundOrder['status'] = C('FundOrderStatus.Settled');

		// 2.给卖家加款并记录日志
		$remark = $arrFundOrder['title'];
		if ($tradeInAmount > 0 && !$this->accountBalanceChange($arrFundOrder['seller_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.TradeIn'), $tradeInAmount, $remark, $arrFundOrder['platform_id'])) {
			$this->db->trans_rollback();
			return false;
		}

		// 3.有退款,退给买家
		$refundRemark = $arrFundOrder['title']."退款";
		$userRefundAmount = $arrFundOrder['refund'];
		$couponRefundAmount = 0;
		// 存在代金券
		if ($arrFundOrder['cash_coupon_amt']>0) {
			$refundRate = $arrFundOrder['cash_coupon_amt']/$arrFundOrder['total_amt'];

			$userRefundAmount = $arrFundOrder['refund']-$arrFundOrder['refund']*$refundRate;
			$couponRefundAmount = $arrFundOrder['refund'] - $userRefundAmount;
		}
		// 3.1退回用户帐户
		if ($arrFundOrder['refund'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeIn'), $userRefundAmount, $refundRemark, $arrFundOrder['platform_id']) ) {
			$this->db->trans_rollback();
			return false;
		}
		$couponRefundRemark = $arrFundOrder['title']."退款-代金券";
		// 3.2 有代金,退回代金帐户
		if ($couponRefundAmount > 0 && $arrFundOrder['refund'] > 0
				&& !$this->accountBalanceChange($this->XT_ACCOUNT_COUPON_USER_ID, $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.TradeInerIn'), $couponRefundAmount, $refundRemark, $arrFundOrder['platform_id'])) {
			$this->db->trans_rollback();
			return false;
		}

		// 4.有手续费,加给投融界会计帐户
		$feeRemark = C('OrderTypeName.'.$arrFundOrder['type_id']) + "手续费";
		if ($arrFundOrder['fee_amt'] > 0 && !$this->accountBalanceChange($this->XT_ACCOUNT_USER_ID, $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.ConsumeFeeIn'), $arrFundOrder['fee_amt'], $feeRemark, $arrFundOrder['platform_id'])) {
			$this->db->trans_rollback();
			return false;
		}

		// 5.卖家用户加额度(充值,交易收入相同)
		if (!$this->accountLimitChangeToSeller($arrFundOrder)) {
			$this->db->trans_rollback();
			return false;
		}

		// ...分润处理--暂无分润,以后实现
		// addProfitOrder(order);

		$this->db->trans_commit();

		// }
		// [end]

		return true;

	}

	private function refundSettled($arrFundOrder){
		// [start] tran 修改状态,改变用户金额{
		$this->db->trans_begin();

		//1.提现
		if ($arrFundOrder['type_id'] == C('OrderType.Cash')) {
			// 1.1更新订单状态为已退款
			if(!$this->tranStatus($arrFundOrder, C('FundOrderStatus.Payed'), C('FundOrderStatus.Refunded'))){
				$this->db->trans_rollback();
				return false;
			}

			// 1.2提现失败,给买家加款并记录日志
			$remark = $arrFundOrder['title'].'退款';
			if ($arrFundOrder['balance_amt'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeIn'), $arrFundOrder['balance_amt'], $remark, $arrFundOrder['platform_id'])) {
				$this->db->trans_rollback();
				return false;
			}

			// 1.3增加买家提现额度
			// if (!$this->accountLimitChangeToBuyer($arrFundOrder)) {
			// 	$this->db->trans_rollback();
			// 	return false;
			// }
		}

		//2.消费  Settled可能已处理，无需这里再处理
		if ($arrFundOrder['type_id'] == C('OrderType.Consume')) {
			$refundAmount = $arrFundOrder['refund'];
			if($refundAmount==0){
				$refundAmount = $arrFundOrder['balance_amt']+$arrFundOrder['netpay_amt'];
			}

			//2.1已支付，未结算
			if($arrFundOrder['status']==C('FundOrderStatus.WaitingSettle')){
				//2.1.1修改订单状态
				if(!$this->tranStatus($arrFundOrder, C('FundOrderStatus.WaitingSettle'), C('FundOrderStatus.Refunded'))){
					$this->db->trans_rollback();
					return false;
				}

				//2.1.2买家退款-原路返回
				$remark = $arrFundOrder['title'].'退款';
				if ($arrFundOrder['balance_amt'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeIn'), $arrFundOrder['balance_amt'], $remark, $arrFundOrder['platform_id'])) {
					$this->db->trans_rollback();
					return false;
				}
				if($arrFundOrder['netpay_amt'] > 0){
					$data_log = array('fund_order_id'=>$arrFundOrder['fund_order_id'],
									'fund_order_sn'=>$arrFundOrder['fund_order_sn'],
									'order_id'=>$arrFundOrder['order_id'],
									'order_sn'=>$arrFundOrder['order_sn'],
									'netpay_method'=>$arrFundOrder['netpay_method'],
						);
					$log_id = M('trd/Third_refund_log')->insert_string($data_log);
					if(empty($log_id)){
						$this->db->trans_rollback();
						return false;
					}

				}

			}
			//2.2 已结算，退货退钱
			else if($arrFundOrder['status']==C('FundOrderStatus.Settled')){
				//2.2.1修改订单状态
				if(!$this->tranStatus($arrFundOrder, C('FundOrderStatus.Settled'), C('FundOrderStatus.Refunded'))){
					$this->db->trans_rollback();
					return false;
				}

				//2.2.2卖家扣钱
				$remark = $arrFundOrder['title'].'退货款';
				if ($refundAmount > 0 && !$this->accountBalanceChange($arrFundOrder['seller_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeOut'), $refundAmount, $remark, $arrFundOrder['platform_id'])) {
					$this->db->trans_rollback();
					return false;
				}
				//2.2.3买家退款-原路返回
				$remark = $arrFundOrder['title'].'退款';
				if ($arrFundOrder['balance_amt'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeIn'), $arrFundOrder['balance_amt'], $remark, $arrFundOrder['platform_id'])) {
					$this->db->trans_rollback();
					return false;
				}
				if($arrFundOrder['netpay_amt'] > 0){
					$data_log = array('fund_order_id'=>$arrFundOrder['fund_order_id'],
									'fund_order_sn'=>$arrFundOrder['fund_order_sn'],
									'order_id'=>$arrFundOrder['order_id'],
									'order_sn'=>$arrFundOrder['order_sn'],
									'netpay_method'=>$arrFundOrder['netpay_method'],
									'addtime'=>time(),
						);
					$log_id = M('trd/Third_refund_log')->insert_string($data_log);
					if(empty($log_id)){
						$this->db->trans_rollback();
						return false;
					}

				}

				/*// 增加买家提现额度
				if (!$this->accountLimitChangeToBuyer($arrFundOrder)) {
					$this->db->trans_rollback();
					return false;
				}*/
			}

			
		}

		//3.售后退款
		if($arrFundOrder['type_id'] == C('OrderType.AfterSalesRefund')){
			// 3.1更新订单状态为已退款
			if(!$this->tranStatus($arrFundOrder, C('FundOrderStatus.WaitingSettle'), C('FundOrderStatus.Refunded'))){
				$this->db->trans_rollback();
				return false;
			}

			// 3.2,给买家加款并记录日志
			$remark = $arrFundOrder['title'];
			if ($arrFundOrder['refund'] > 0 && !$this->accountBalanceChange($arrFundOrder['buyer_userid'], $arrFundOrder['order_id'], $arrFundOrder['fund_order_id'], C('BalanceLogType.RefundTradeIn'), $arrFundOrder['refund'], $remark, $arrFundOrder['platform_id'])) {
				$this->db->trans_rollback();
				return false;
			}
		}

		$this->db->trans_commit();

		// }
		// [end]
		return true;
	}

	/**
	 * 订单状态检查及更新，先检查订单当前状态与预期状态是否一致
	 * @param $userId:用户id 
	 * @param $orderId:订单id
	 * @param $fundOrderId:资金订单id
	 * @param $moneyType
	 * @param $amount:发生变化资金
	 * @param $remark:备注
	 * @param $platformId:平台id
	 * @return boolean true/false
	 */
	// balanceLogTypeEnum:交易类型 amount:发生变化资金
	private function accountBalanceChange($userId, $orderId, $fundOrderId, $moneyType, $amount, $remark, $platformId) {
		if(empty($platformId))
			$platformId = C('basic_info.PLATFORM_ID');
		if ($amount == 0)
			return true;

		/*$data = array('acct_balance'=>'acct_balance'+$amount,
				'acct_blob'=>"AES_ENCRYPT(convert(Acct_Balance, DECIMAL(12,2) ),$pwd)",
				);
		$where = array('user_id'=>$userId,
				'acct_balance'=>"convert(AES_DECRYPT(Acct_Blob,$pwd),DECIMAL(12,2)",
				"(acct_balance+$amount) >="=>0,
				"not EXISTS(select 1 from x_acct_log where fund_order_id=$fundOrderId and Money_Type=$moneyType)"
				);
		$result = $this->update_by_where($where, $data);*/
		$pwd = "@xt";
		$sql = "update ".$this->prefix()."acct_user_account set Acct_Balance=Acct_Balance+$amount,Acct_Blob = AES_ENCRYPT(convert(Acct_Balance, DECIMAL(12,2) ),'$pwd' ) where User_Id = $userId and Acct_Balance=convert(AES_DECRYPT(Acct_Blob,'$pwd'),DECIMAL(12,2)) and (Acct_Balance+$amount)>=0 and not EXISTS(select 1 from ".$this->prefix()."acct_log where Fund_Order_Id=$fundOrderId and Money_Type='$moneyType');";
      	$this->db->query($sql);
		$result = $this->db->affected_rows();

		if($result>0){
			$sqlLog = "insert into ".$this->prefix()."acct_log(user_id,acct_id,fund_order_id,order_id,money_type,amount,balance,remark,create_time,platform_id) select $userId, acct_Id,$fundOrderId,$orderId,'$moneyType',$amount,acct_balance,'$remark',".time().",$platformId from ".$this->prefix()."acct_user_account where user_id = $userId and acct_balance=convert(AES_DECRYPT(acct_blob,'$pwd'),DECIMAL(12,2)) and not EXISTS(select 1 from ".$this->prefix()."acct_log where Fund_Order_Id=$fundOrderId and Money_Type='$moneyType');";

      		$this->db->query($sqlLog);

		}

		return $result > 0;
	}

	
	// 支付时,减--给买家减
	private function accountLimitChangeFromBuyer($arrFundOrder) {
		return true;
		
		/*$result = 0;
		$amount = 0;
		$type = null;

		if ($arrFundOrder['status']==C('FundOrderStatus.Payed')) {
			// 3.提现:给买家(即提现者)减去可提现额度
			if ($arrFundOrder['type_id'] == C('OrderType.Cash')) {
				$amount = $arrFundOrder['balance_amt']*-1;

				$type = C('LimitType.Withdraw');
				
				$result = trd_User_AccountService.updateAcctLimit(fundOrder.getBuyerUserId(), amount, type);
			}
		}

		return $result > 0;*/
		
	}

	// 退款时,加--给买家加
	private function accountLimitChangeToBuyer($arrFundOrder){
		return true;
		/*$result = 0;
		$amount = 0;
		$type = null;

		if($arrFundOrder['Refunded']==C('FundOrderStatus.Refunded')){
			// 3.提现:退款时,给买家(即提现方)加上提现额度
			if ($arrFundOrder['type_id'] == C('OrderType.Cash') ) {
				$amount = $arrFundOrder['balance_amt'];
				$type = C('LimitType.Withdraw');
				
				
				$result = trd_User_AccountService.updateAcctLimit(fundOrder.getBuyerUserId(), amount, type);
			}
		}

		return $result > 0;*/
	}

	// 结算时,加--给卖家加
	private function accountLimitChangeToSeller($arrFundOrder) {
		return true;
		/*int result = 0;
		BigDecimal amount = BigDecimal.ZERO;
		LimitTypeEnum type = null;

		if (fundOrder.getStatus().equals(FundOrderStatusEnum.Settled.toString())) {

			// 1.消费:给卖家加上交易收入额度、可提现限额额度
			if (fundOrder.getOrderTypeId().intValue() == OrderTypeEnum.Consume.getIndex()) {
				// amount = fundOrder.getBalanceAmt();
				amount = fundOrder.getTotalAmt().subtract(fundOrder.getRefund()).subtract(fundOrder.getFeeAmt());

				type = LimitTypeEnum.Income;
				result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
				if (result <= 0)
					return false;

				type = LimitTypeEnum.Withdraw;
				result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
			}
			// 2.充值:给卖家(即充值方)加上充值额度、可开发票额度
			else if (fundOrder.getOrderTypeId().intValue() == OrderTypeEnum.Recharge.getIndex()) {
				amount = fundOrder.getNetpayAmt();

				type = LimitTypeEnum.Recharge;
				result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
				if (result <= 0)
					return false;

				type = LimitTypeEnum.Invoice;
				result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
			}

			// 4.400结算 暂无??
			else if (fundOrder.getOrderTypeId().intValue() == OrderTypeEnum.Settle400.getIndex()) {
				// ??
			}
			// 5.推广:给卖家(即推广方)加上现金红包额度、消费红包 及可提现额度
			else if (fundOrder.getOrderTypeId().intValue() == OrderTypeEnum.Promote.getIndex()) {
				amount = fundOrder.getBonusAmt();

				if (fundOrder.getNetpayMethod().intValue() == PayMethodEnum.Cash_Bonus.getIndex()) {
					type = LimitTypeEnum.Withdraw;
					result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
					if (result <= 0)
						return false;

					type = LimitTypeEnum.Cash_Bonus;
				} else if (fundOrder.getNetpayMethod().intValue() == PayMethodEnum.Consume_Bonus.getIndex())
					type = LimitTypeEnum.Consume_Bonus;

				result = trd_User_AccountService.updateAcctLimit(fundOrder.getSellerUserId(), amount, type);
			}
		}

		return result > 0;*/
	}

	// 开发票-减用户开票额度
	public function accountInvoiceChange($userId, $amount) {
		$bResult = true;

		// int result = trd_User_AccountService.updateAcctLimit(userId, amount, LimitTypeEnum.Invoice);

		// bResult = result > 0;

		return $bResult;
	}





}
