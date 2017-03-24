<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('TIMESTAMP', time());
$config['cache_open'] = 0;

$config['AESAPPCLIENT_KEY'] = 'AesAppCLIENT_Xtt';

//test
$config['url'] = array(
	'base_site_url'=>'http://www.youmi1.cn',
	'shop_site_url'=>'http://www.youmi1.cn/shop',
	'seller_site_url'=>'http://www.youmi1.cn/seller',
	'admin_site_url'=>'http://www.youmi1.cn/admin',
	//'mobile_site_url'=>'http://www.youmi1.cn/mobile',
	'wap_site_url'=>'http://www.youmi1.cn/wap',
	'upload_site_url'=>'http://www.youmi1.cn/upload',
);

// $config['url'] = array(
// 	'base_site_url'=>'http://ym.mirongnet.cn',
// 	'shop_site_url'=>'http://ym.mirongnet.cn/shop',
// 	'seller_site_url'=>'http://ym.mirongnet.cn/seller',
// 	'admin_site_url'=>'http://ym.mirongnet.cn/admin',
// 	//'mobile_site_url'=>'http://data.zooernet.cn/mobile',
// 	'wap_site_url'=>'http://ym.mirongnet.cn/wap',
// 	'upload_site_url'=>'http://ym.mirongnet.cn/upload',
// );

$config['basic_info'] = array(
	'PLATFORM_ID' =>1,
	'TAKE_CASH_LIMIT_MAX'=>2000,
	'TAKE_CASH_LIMIT_MIN'=>1,
	'TAKE_CASH_EACH_FEE'=>0,
	'MD5_KEY'=>'d@36$hS(',
	'SHOP_DEFAULT_ID'=>1,	//默认店铺id
	'TEMP_USER_ID'=>-1,		//临时用户
	'TEMP_USER_NAME'=>'temp_user',
	'EncodingAesKey'=>'a27a0E4991d3edecfec2E8qf520fdDS949dP2Kdi835',
	'SERVER_MSG_IP'=>'192.168.1.10',
	);

//develop
// $config['url'] = array(
//      'base_site_url'=>'http://www.youmi1.cn',
//      'shop_site_url'=>'http://www.youmi1.cn/shop',
//      'seller_site_url'=>'http://www.youmi1.cn/seller',
//      'admin_site_url'=>'http://www.youmi1.cn/admin',
//      //'mobile_site_url'=>'http://www.youmi1.com/mobile',
//      'wap_site_url'=>'http://www.youmi1.cn/wap',
//      'upload_site_url'=>'http://www.youmi1.cn/upload',
// );


$config['cookie_pre'] = 'xshop';
$config['lang_type'] = 'zh_cn';
$config['url_model'] = false;

$config['cfg_path'] = array(
	'res'=>'/res/',
	'css'=>'/res/front/css/',
	'js'=>'/res/front/js/',
	'images'=>'/res/front/images/',
	'font'=>'/res/font/',
	'lib'=>'/res/lib/',
	'admin'=>'/res/admin/',
	'admin_css'=>'/res/admin/css/',
	'admin_js'=>'/res/admin/js/',
	'admin_js_fileupload'=>'/res/admin/js/fileupload/',
	'admin_images'=>'/res/admin/images/',
	'seller'=>'/res/seller/',
	'seller_css'=>'/res/seller/css/',
	'seller_js'=>'/res/seller/js/',
	'seller_images'=>'/res/seller/images/',
	'resource'=>'/resource/',
	'resource_css'=>'/resource/css/',
	'resource_js'=>'/resource/js/',
);

$config['OrderStatus'] = array(
	'Create'=>'Create',
	'WaitPay'=>'WaitPay',
	'WaitSend'=>'WaitSend',
	'WaitConfirm'=>'WaitConfirm',
	'Refunded'=>'Refunded',
	'Finished'=>'Finished',
	'Closed'=>'Closed',
	'ClosedBySys'=>'ClosedBySys',
);
$config['OrderPayStatus'] = array(
	'UnPay'=>array('Create', 'WaitPay', 'Closed', 'ClosedBySys'),
	'Payed'=>array('WaitSend', 'WaitConfirm','Finished')
);
$config['OrderStatusName'] = array(
	'Create'=>'待付款',
	'WaitPay'=>'待付款',
	'WaitSend'=>'待发货',
	'WaitConfirm'=>'待收货',
	'Refunded'=>'已退款',
	'Finished'=>'已完成',
	'Closed'=>'关闭',
	'ClosedBySys'=>'系统关闭',
);
$config['FundOrderStatus'] = array(
	'Waiting'=>'Waiting',
	'Paying'=>'Paying',
	'Payed'=>'Payed',
	'WaitingSettle'=>'WaitingSettle',
	'Settled'=>'Settled',
	'Closed'=>'Closed',
	'Refunded'=>'Refunded',
);

//1:消费 2:充值 3:提现 4.推广
$config['OrderType'] = array(
	'Consume'=>1,'Recharge'=>2,'Cash'=>3,'Promote'=>4,'AfterSalesRefund'=>5
);
$config['OrderTypeName'] = array(
	'1'=>'购买','2'=>'充值','3'=>'提现','4'=>'推广','5'=>'售后退款'
);

$config['NetPayStatus'] = array(
	'WAIT'=>'WAIT',
	'SUCCESS'=>'SUCCESS',
	'FAILED'=>'FAILED',
	'UNKNOW'=>'UNKNOW'
);

// 收入,支出,转入,转出,退款收入,提现手续费用转入
$config['BalanceLogType'] = array(
	'TradeIn'=>'TradeIn',
	'TradeOut'=>'TradeOut',
	'TradeInerIn'=>'TradeInerIn',
	'TradeInerOut'=>'TradeInerOut',
	'RefundTradeIn'=>'RefundTradeIn',
	'RefundTradeOut'=>'RefundTradeOut',
	'CashFeeIn'=>'CashFeeIn',
	'CashFeeOut'=>'CashFeeOut',
	'ConsumeFeeIn'=>'ConsumeFeeIn'
);

// 账户额度类型
// 充值金额, 交易收入, 交易待收, 现金红包, 消费红包, 可提现限额, 可开票额度
$config['LimitType'] = array(
	'Recharge'=>'Recharge',
	'Income'=>'Income',
	'Income_Due'=>'Income_Due',
	'Cash_Bonus'=>'Cash_Bonus',
	'Consume_Bonus'=>'Consume_Bonus',
	'Withdraw'=>'Withdraw',
	'Invoice'=>'Invoice',
);

$config['OrderResultError'] = array(
	'Empty'=>'Empty',		//空
	'Success'=>'Success',	//处理成功
	'SignErr'=>'SignErr',	//签名错误
	'ParamErr'=>'ParamErr',	//参数错误
	'OrderNotExits'=>'OrderNotExits',	//订单不存在
	'OrderExists'=>'OrderExists',	//订单已存在
	'Failure'=>'Failure',	//操作失败
	'NetPaying'=>'NetPaying'
);

$config['PayMethod'] = array(
	'Cash_Bonus'=>'Cash_Bonus',
	'Consume_Bonus'=>'Consume_Bonus',
	'Balance_Hand'=>'Balance_Hand',
	'WeixinPayApp'=>'WeixinPayApp',
	'WeixinPayJs'=>'WeixinPayJs',
);
$config['PayMethodType'] = array(
	'AllBalance'=>1,	//余额支付
	'OilBalance'=>6,	//余油支付
	'Cash_Bonus'=>2,
	'Consume_Bonus'=>3,
	'Balance_Hand'=>4,
	'Balance_Refund'=>5,
	'WeixinPayApp'=>11,
	'WeixinPayJs'=>12,
	'WeixinPayMicro'=>13,
	'AlipayApp'=>21,
	'AlipayJs'=>22,
	'AlipayBar'=>23,
);

$config['PayMethodName'] = array(
	1=>'余额',	//余额支付
	2=>'现金红包',
	3=>'消费红包',
	4=>'手动',
	5=>'余额退款',
	11=>'微信APP',
	12=>'微信Wap',
	13=>'微信刷卡',
	21=>'支付宝APP',
	22=>'支付宝Wap',
	23=>'支付宝刷卡',
);

$config['PayWXALI'] = array(
	'WXPAY'=>array(10,11,12,13,14,15),
	'ALIPAY'=>array(21,22,23),
);



$config['PayConfig'] = array(
	'WaitSendAutoFinish'=>1,	//支付后，自动确认收货并结算
	'PAY_JUMP_URL'=>'',
	'PAY_NOTICE_URL'=>'',
	'WXPAY'=>array(
		'APPID'=> 'wx3a95840d3493c1d2',
		'MCHID'=> '1279497401',
		'KEY'=> 'yijU4Jwl8Kd30fsmVm7qp81dMn0hf8Ed',
		'APPSECRET'=> '92a7ea36408ea2d4993ca6917b947ba3',
		'TOKEN'=> '27a04991d3edecfec2980f520fd949d5',
		),
	'WXPAY3'=>array(
		'APPID'=> 'wx2a41555b4815bc57',
		'MCHID'=> '1416656202',
		'KEY'=> 'a7d30ec6i6e3h35vcb8c10y8ddfa90xd',
		'APPSECRET'=> '91b23132000ac74c128d1da13ffb6f4a',
		'TOKEN'=> '',
		),
	'ALIPAY3'=>array (
		//签名方式,默认为RSA2(RSA2048)
		'sign_type' => "RSA2",

		//支付宝公钥
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzqe/ZPcu/Af/eSqLmlCoDWTaH/JJSW8s+aJVR5ElflxlggcTq9e0AW1h3sSl8b7PEt/0NSqwpGJONo78HA9U7TkoG5kMIjSmuZrabfiJn3fQz/HJYvcmdDeYwhA+DAf4i6oTkroGH5iOu7BWGRPvlygFE2PgEN9iSTMj1WaW4D4U/B9HRSNqDAtK5itj5lYbPnzXT19iDEHAz0fktYJASKwy8RtPGESXHFxLyVWQToHMyJKM4RM1sR+2Mfir8eZiy9ES3VMs/peD6dn05g3a9ZxUWG4Al0cHu9Jn30Ob68DbKBAhzNxyLw7XhhD9Hk5WAVckPWbiVwMh6l7Ld0i3eQIDAQAB",

		//商户私钥
		'merchant_private_key' => 'MIIEpAIBAAKCAQEA3fy8cH2FcxBD3csrLXP+0cGgrNMO91huE0FdbJXsKpu16qRLF03tAWJ3WVUXzWd4Cr38t4FNKAhGt9T4feO5iZoFzkf1MKA+9dPzVcrl4uKjtQKA3rSH/ciO06F5CyBsAXef13CscRspNaRDMoB1QaxS4wxs0GoTL03hHv9C+u2t9dq1MceQCoXQUcxrlO2u/uiCe2gkyRm0O+j6Q8ym/+Lw3pLTXF85YX2MmNSgbAmkpdsyWZtAzCXxkW4+Ir2mJ3Pg4SCVveKfNX0ItFvo3JU4rmcLHx5BanHktTBI7ibpJJ5miAJ7/d8Fl19mQskRAtVYeZ90ubAVfAMG/QdyFQIDAQABAoIBAFmKb2wkhWazQdy97c2owzlSlAACJkBJECV+MTPyiU9qfYeG+68LxLTRRfjnpPE6ciZLGhve8QNby0KtigoliBpj4UGirtkNWjgIZVfOvsm0x3nk55xtT4ljk5DzygJd8pLo8LWTAkuhPA8iEeTWAfYaj1h8E4xBrTlgfZTmO9tAsWjRX3FSD/gAqHQhXwlDlV2nd9+kIrBHTirA4HEzSwBnS8IlBlN8ptR4SCJ5GImAdjumbJYZWIon53RpVLK7dcxldDs3NIhxwMR4q2fc4nYmDpCaBXgbmeWzuWovi4Lk5TiyJFI3ucE3ntNdf8x4ZPEX14tVDpOCsnFIRfxX9SECgYEA+g+mg9SxfcNIktSrU3I7R3uiqo1kS/Ou76LulRVhaQDd1vivOewCuUylsJY2X5LAMPVIChI9K6Je9/AL5Qpcpc0QLeej2+vVbxQwL9maIGtYqLfq3roPeC5q8pl747HJdmkWrm56hisIb3L3JbhJVO0pG2pp4eaTR94kN3HGp8kCgYEA40JmHyaWtmx1farl7qvvNHSrtyXdAqy+Z828oMHhub4xBGKh6Eg7AnA2PqozKAq77jtNpuw7FnS84Nr31Mo+7W6CuJM/Y5YzaMK9dwygnxJHVXlXTiSIHlXvk6jmUfDVpYAuqei0CHJcfRH0sJYyDeayeoXc9HbZYfpO4fAVte0CgYEA8E/2EJ9o65mutx0WgR04htv0Wb+D8dH//mAwtKlVcdnZV+yqSRndDELywJ+7JWIcvXAbiUI58yMZ7WCNrdGlc9l8gQEp4gHkndli7blLe3huPcZyOxXIJBFHylliTFHhcKBRodSefy3/70sm5OMT5j/8z1IW63fxdpGvcHExHrECgYBJasX7AkhmHpvGQVOOy1S3qYlf3YeMuam5dymc9YWqr9q+A+8yT0NhAkdD71Afe3hC+j2X03EzS/tI2PR/debmZXEtl8Q+s+Nf0KnMr/Oj8A/DeTPPNtYSlsiS91mQ1zDuCn8IvPiMUgW3EfnuolcBTqoSEvTStSJ9GJQrQZiB8QKBgQC0ntapRTvZoq+77UKF/HRma1K124+o8HKHo4EuUgcjJElU6a70037vlJ34NN7C3mBxv4g0m92w0oMYeK+DNTxBHCiGhHWrrF5eAfBj2h3pIhQN/MLjFY/Eq3gxtE7P3hIrS5fNO7tHoWnXVOmh8fiotM9bewuULg0NSWyA5sOJEw==',

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//应用ID
		'app_id' => "2016073000123823",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "http://www.youmi1.cn/api/respond/notice_AliPay",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
		),
);

$config['TPL_MESSAGE'] = array(
	'SEND_CODE_MESSAGE_ID'=>11
);
$config['DeliveryWay'] = array(
	'1'=>'由九号街区配送',
	'2'=>'由快递配送',
	'3'=>'由九号街区+快递配送',
);
$config['PayType'] = array(
	'1'=>'在线支付',
	//'2'=>'余额支付',		//????目前，只有在线支付
	'2'=>'货到付款',
);

$config['Goods_Tpl'] = array(
	'Follow'=>array('title','point','comm_price','pic_path','brand_name','spu','content','m_content' ),
);


define('BASE_SITE_URL', $config['url']['base_site_url']);
define('SHOP_SITE_URL', $config['url']['shop_site_url']);
define('SELLER_SITE_URL', $config['url']['seller_site_url']);
define('RES_SITE_URL', $config['url']['base_site_url'].$config['cfg_path']['res']);


define('ADMIN_SITE_URL', $config['url']['admin_site_url']);
//define('MOBILE_SITE_URL', $config['mobile_site_url']);
define('WAP_SITE_URL', $config['url']['wap_site_url']);
define('UPLOAD_SITE_URL',$config['url']['upload_site_url']);

define('BASE_UPLOAD_PATH',BASE_ROOT_PATH.'/upload');
define('LANG_TYPE',$config['lang_type']);
define('URL_MODEL',$config['url_model']);

define('COOKIE_PRE',$config['cookie_pre']);

define('TPL_ADMIN_NAME','templates/default/');

define('ATTACH_PATH','shop');
define('ATTACH_GOODS',ATTACH_PATH.'/goods');

/**
 * 商品图片
 */
define('GOODS_IMAGES_WIDTH', '60,240,360,1280');
define('GOODS_IMAGES_HEIGHT', '60,240,360,12800');
define('GOODS_IMAGES_EXT', '_60,_240,_360,_1280');


define('Failure','Failure');
define('Success','Success');
define('DEVELOPMENT', 1);

