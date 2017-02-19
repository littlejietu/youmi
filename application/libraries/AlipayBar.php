<?php

class AlipayBar{
	//
	public $respond_name = 'AlipayBar';

	public function __construct()
	{
		
		require_once 'alipay-sdk/model/builder/AlipayTradePrecreateContentBuilder.php';
		require_once 'alipay-sdk/model/builder/AlipayTradePayContentBuilder.php';
		require_once "AlipayTradeService.php";
	}

	public $data = null;

    public function payRequest($arrFundOrder, $aliConfig) {

    	//$outTradeNo = "barpay" . date('Ymdhis') . mt_rand(100, 1000);
	    $outTradeNo = $arrFundOrder['fund_order_id'];

	    // (必填) 订单标题，粗略描述用户的支付目的。如“XX品牌XXX门店消费”
	    $subject = $arrFundOrder['title'];

	    // (必填) 订单总金额，单位为元，不能超过1亿元
	    // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
	    $totalAmount = $arrFundOrder['netpay_amt'];

	    // (必填) 付款条码，用户支付宝钱包手机app点击“付款”产生的付款条码
	    $extParam = str_replace("\&quot;", "\"", $arrFundOrder['extparam']);
    	$arrExtParam = json_decode($extParam,true);
    	if(empty($arrExtParam['auth_code']))
    		return false;

    	$authCode = $arrExtParam['auth_code'];	//28开头18位数字

	    // (可选,根据需要使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
	    // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
	    //String discountableAmount = "1.00"; //

	    // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
	    // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
	    //$undiscountableAmount = "0.01";

	    // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
	    // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
	    $sellerId = "";

	    // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
	    $body = $subject.' 共'.$arrFundOrder['total_amt'].'元';

	    //商户操作员编号，添加此参数可以为商户操作员做销售统计
	    //$operatorId = "test_operator_id";

	    // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
	    //$storeId = "test_store_id";

	    // 支付宝的店铺编号
	    //$alipayStoreId = "test_alipay_store_id";

	    // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，详情请咨询支付宝技术支持
	    $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
	    $extendParams = new ExtendParams();
	    $extendParams->setSysServiceProviderId($providerId);
	    $extendParamsArr = $extendParams->getExtendParams();

	    // 支付超时，线下扫码交易定义为5分钟
	    $timeExpress = "5m";

	    /*
	    // 商品明细列表，需填写购买商品详细信息，
	    $goodsDetailList = array();

	    // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
	    $goods1 = new GoodsDetail();
	    $goods1->setGoodsId("good_id001");
	    $goods1->setGoodsName("XXX商品1");
	    $goods1->setPrice(3000);
	    $goods1->setQuantity(1);
	    //得到商品1明细数组
	    $goods1Arr = $goods1->getGoodsDetail();

	    // 继续创建并添加第一条商品信息，用户购买的产品为“xx牙刷”，单价为5.05元，购买了两件
	    $goods2 = new GoodsDetail();
	    $goods2->setGoodsId("good_id002");
	    $goods2->setGoodsName("XXX商品2");
	    $goods2->setPrice(1000);
	    $goods2->setQuantity(1);
	    //得到商品1明细数组
	    $goods2Arr = $goods2->getGoodsDetail();

	    $goodsDetailList = array($goods1Arr, $goods2Arr);
	    */

	    //第三方应用授权令牌,商户授权系统商开发模式下使用
	    $appAuthToken = $aliConfig['ali_auth_token'];//根据真实值填写

	    // 创建请求builder，设置请求参数
	    $barPayRequestBuilder = new AlipayTradePayContentBuilder();
	    $barPayRequestBuilder->setOutTradeNo($outTradeNo);
	    $barPayRequestBuilder->setTotalAmount($totalAmount);
	    $barPayRequestBuilder->setAuthCode($authCode);
	    $barPayRequestBuilder->setTimeExpress($timeExpress);
	    $barPayRequestBuilder->setSubject($subject);
	    $barPayRequestBuilder->setBody($body);
	    //$barPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
	    $barPayRequestBuilder->setExtendParams($extendParamsArr);
	    //$barPayRequestBuilder->setGoodsDetailList($goodsDetailList);
	    //$barPayRequestBuilder->setStoreId($storeId);
	    //$barPayRequestBuilder->setOperatorId($operatorId);
	    //$barPayRequestBuilder->setAlipayStoreId($alipayStoreId);

	    $barPayRequestBuilder->setAppAuthToken($appAuthToken);
	    $alipay_config = C('PayConfig.ALIPAY3');
	    $barPay = new AlipayTradeService($alipay_config);
	    $barPayResult = $barPay->barPay($barPayRequestBuilder);

	    $result = null;
	    switch ($barPayResult->getTradeStatus()) {
	        case "SUCCESS":
	            $result = (array)$barPayResult->getResponse();
	            break;
	        case "FAILED":
	        case "UNKNOWN":
	            $arrTmp = $barPayResult->getResponse();
	            if (!empty($arrTmp))
	            	$result = $arrTmp->sub_msg;
	            break;
	        default:
	            $result = "不支持的交易状态，交易返回异常!!!";
	            break;
	    }


    	
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
    	if($result['code'] == 10000)
		{
			$arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
			$arrPayNoticeResult['seq_no'] = $result['trade_no'];
			$arrPayNoticeResult['amount'] = $result['total_amount'];
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

    

}