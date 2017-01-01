<?php

function getMillisecond() {
	list($t1, $t2) = explode(' ', microtime());
	return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/**
 * 生成支付单编号(两位随机 + y[2位年份]+时间戳微秒+会员ID%1000)，该值会传给第三方支付接口
 * 长度 =2位 + 10位 + 3位 + 3位  = 18位
 * 1000个会员同一微秒提订单，重复机率为1/100
 * @return string
 */
function getOrderSn($seller_id=0) {
	$rnd = mt_rand(10, 99);
	$now = date("y") . getMillisecond();
	$uid = sprintf('%03d', (int) $seller_id % 1000);

	$id =  $rnd . $now . $uid;
	return $id;

	/*
	$id = mt_rand(10,99)
    . sprintf('%010d',time() - 946656000)
    . sprintf('%03d', (float) microtime() * 1000)
    . sprintf('%03d', (int) $seller_id % 1000);

	return $id;
	*/

}

function isRechargeOrder($arrFundOrder) {
	return $arrFundOrder['buyer_userid'] == $arrFundOrder['seller_userid'] && ($arrFundOrder['type_id'] == C('OrderType.Recharge') || $arrFundOrder['type_id'] == C('OrderType.Promote'));
}

function isTakeCashOrder($arrFundOrder) {
	return $arrFundOrder['buyer_userid'] == $arrFundOrder['seller_userid'] && $arrFundOrder['type_id'] == C('OrderType.Cash');
}

/**
 * 得到所购买的id和数量
 *
 */
function parseBuyItems($cart_id) {
	//存放所购商品ID-SKU_ID和数量组成的键值对
	$buy_items = array();
	if (is_array($cart_id)) {
		foreach ($cart_id as $value) {
			if (preg_match_all('/^(\d{1,10})\,(\d{1,10})\,(\d{1,6})$/', $value, $match)) {
				if (intval($match[3][0]) > 0) {
					$buy_items[$match[1][0] . '-' . $match[2][0]] = $match[3][0];
				}
			}
		}
	}
	return $buy_items;
}

?>