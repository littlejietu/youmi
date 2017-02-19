<?php

class WeixinPayMicro{
	public $respond_name = 'WeixinPayMicro';

	public function __construct()
	{
		//require_once "PayConfig.php";
		require_once "WxPayApi.php";
	}

	/**
	 * 
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200, 
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
	public $data = null;

    public function payRequest($arrFundOrder, $wxConfig) {
    	$input = new WxPayMicroPayObj();
    	$extParam = str_replace("\&quot;", "\"", $arrFundOrder['extparam']);
    	$arrExtParam = json_decode($extParam,true);
    	if(empty($arrExtParam['auth_code']))
    		return false;

    	$auth_code = $arrExtParam['auth_code'];
		$input->SetAuth_code($auth_code);
		$input->SetBody($arrFundOrder['title']);
		$input->SetTotal_fee($arrFundOrder['netpay_amt']*100);
		$input->SetOut_trade_no($arrFundOrder['fund_order_id']);
		//print_r($wxConfig);echo 'xxxx';die;
    	$result = $this->topay($input, $wxConfig);
    	return $result;
    }

    //返回PayNoticeResult数组
    // $arrParam['data']: xml字符串
    //$arrPayNoticeResult('fund_order_id','seq_no', 'amount','isSuccess')
    public function parseNotice($result){
    	return null;
    }

    public function parseJump($result){
    	$arrPayNoticeResult = array();
    	if($result['result_code'] == "SUCCESS" && $result['return_code'] == "SUCCESS")
		{
			$arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
			$arrPayNoticeResult['seq_no'] = $result['transaction_id'];
			$arrPayNoticeResult['amount'] = $result['total_fee']/100;
			$arrPayNoticeResult['isSuccess'] = true;

		}
		else
		{	
			$arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
			$arrPayNoticeResult['isSuccess'] = false;
		}
	
		return $arrPayNoticeResult;
    }

//    public function checkOrder($arrFundOrder){
//
//    }

    /**
	 * 
	 * 提交刷卡支付，并且确认结果，接口比较慢
	 * 刷卡支付实现类
	 * 该类实现了一个刷卡支付的流程，流程如下：
	 * 1、提交刷卡支付
	 * 2、根据返回结果决定是否需要查询订单，如果查询之后订单还未变则需要返回查询（一般反复查10次）
	 * 3、如果反复查询10订单依然不变，则发起撤销订单
	 * 4、撤销订单需要循环撤销，一直撤销成功为止（注意循环次数，建议10次）
	 * 
	 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发，为了防止
	 * 查询时hold住后台php进程，商户查询和撤销逻辑可在前端调用
	 * @param WxPayMicroPay $microPayInput
	 * @throws WxpayException
	 * @return 返回查询接口的结果
	 */
	public function topay($microPayInput, $wxConfig)
	{
		//①、提交被扫支付
		$result = WxPayApi::micropay($microPayInput, $wxConfig, 30);

		//如果返回成功
		if(!array_key_exists("return_code", $result)
			|| !array_key_exists("result_code", $result))
		{
			return "接口调用失败！错误：".$result['return_msg'];
		}
		
		//签名验证
		$out_trade_no = $microPayInput->GetOut_trade_no();
		
		//②、接口调用成功，明确返回调用失败
		if($result["return_code"] == "SUCCESS" && $result["result_code"] == "FAIL" && 
		   !in_array($result["err_code"], array('USERPAYING','SYSTEMERROR','OUT_TRADE_NO_USED','ORDERPAID'))
		  )
		{
			return $result["err_code_des"];
		}

		//③、确认支付是否成功
		sleep(3);
		$queryTimes = 10;
		while($queryTimes > 0)
		{
			$queryTimes--;
			$succResult = 0;
			$queryResult = $this->query($out_trade_no, $wxConfig, $succResult);
			//如果需要等待1s后继续
			if($succResult == 2){
				sleep(2);
				continue;
			} else if($succResult == 1){//查询成功
				return $queryResult;
			} else if($succResult == -1){//用户取消
				break;
			}else {//订单交易失败
				return false;
			}
		}

		//④、次确认失败，则撤销订单
		if(!$this->cancel($out_trade_no, $wxConfig))
		{
			return "支付失败！";
		}

		return "支付失败.";
	}
	
	/**
	 * 
	 * 查询订单情况
	 * @param string $out_trade_no  商户订单号
	 * @param int $succCode         查询订单结果
	 * @return 0 订单不成功，1表示订单成功，2表示继续等待
	 */
	public function query($out_trade_no, $wxConfig, &$succCode)
	{
		$queryOrderInput = new WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($out_trade_no);
		$result = WxPayApi::orderQuery($queryOrderInput, $wxConfig);

		if($result["return_code"] == "SUCCESS" 
			&& $result["result_code"] == "SUCCESS")
		{
			//支付成功
			if($result["trade_state"] == "SUCCESS"){
				$succCode = 1;
			   	return $result;
			}else if($result["trade_state"] == "USERPAYING"){
				//用户支付中
				$succCode = 2;
				return false;
			}else if($result["trade_state"] == "NOTPAY"){
				//用户取消支付
				$succCode = -1;
				return false;
			}
		}
		
		//如果返回错误码为“此交易订单号不存在”则直接认定失败
		if($result["err_code"] == "ORDERNOTEXIST")
		{
			$succCode = 0;
		} else{
			//如果是系统错误，则后续继续
			$succCode = 2;
		}
		return false;
	}
	
	/**
	 * 
	 * 撤销订单，如果失败会重复调用10次
	 * @param string $out_trade_no
	 * @param 调用深度 $depth
	 */
	public function cancel($out_trade_no, $wxConfig, $depth = 0)
	{
		if($depth > 10){
			return false;
		}
		
		$clostOrder = new WxPayReverse();
		$clostOrder->SetOut_trade_no($out_trade_no);
		$result = WxPayApi::reverse($clostOrder, $wxConfig);
		
		//接口调用失败
		if(empty($$result) || $result["return_code"] != "SUCCESS"){
			return false;
		}
		
		//如果结果为success且不需要重新调用撤销，则表示撤销成功
		if($result["result_code"] != "SUCCESS" 
			&& $result["recall"] == "N"){
			return true;
		} else if($result["recall"] == "Y") {
			return $this->cancel($out_trade_no, $wxConfig, ++$depth);
		}
		return false;
	}

}