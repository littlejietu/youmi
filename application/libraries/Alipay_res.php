<?php
/* *
 * mao
 *20160506
 * 支付宝接口RSA函数
 */
class Alipay_res{

        /**
         * RSA签名
         * @param $data 待签名数据
         * @param $private_key_path 商户私钥文件路径
         * return 签名结果
         */
        public function rsaSign($data, $private_key_path) {
            $priKey = file_get_contents($private_key_path);
            $res = openssl_get_privatekey($priKey);
            openssl_sign($data, $sign, $res);
            openssl_free_key($res);
        	//base64编码
            $sign = base64_encode($sign);
            return $sign;
        }

        /**
         * RSA验签
         * @param $data 待签名数据
         * @param $ali_public_key_path 支付宝的公钥文件路径
         * @param $sign 要校对的的签名结果
         * return 验证结果
         */
        public function rsaVerify($data, $ali_public_key_path, $sign)  {
        	$pubKey = file_get_contents($ali_public_key_path);
            $res = openssl_get_publickey($pubKey);
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
            openssl_free_key($res);    
            return $result;
        }

        /**
         * 对数组排序
         * @param $para 排序前的数组
         * return 排序后的数组
         */
        function argSort($para) {
            ksort($para);
            reset($para);
            return $para;
        }
}        