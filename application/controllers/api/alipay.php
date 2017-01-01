<?php
defined('BASEPATH') or exit('No direct script access allowed');
/* *
 * mao
 *20160506
 * 支付宝支付接口
 */
class Alipay extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library('Alipay_res');
        $this->private_key_path = './res/admin/alipay/rsa_private_key.pem';//商户私钥文件路径
        $this->public_key_path = './res/admin/alipay/rsa_public_key.pem';////商户公钥文件路径（检验才用到,其他不用）

    }

    /**
     * RSA签名
     * @param $sign 签名结果
     * @param 
     * return 验证结果
     */
    public function getAlipay(){

        $ali_Arr = array(
           'partner' => '2088021466056978',//请勿改动
           'seller_id' => 'hzqxnet@163.com',//请勿改动
           'out_trade_no' => '1040783670',//订单号
           'subject' => '商品一',
           'body' => '商品一111',
           'total_fee' => '0.1',//总金额
           'notify_url' => 'data.zooernet.com/wap/home/index.html',//回调地址urlencode
           'service' => 'mobile.securitypay.pay',//请勿改动
           'payment_type' => 1,//支付类型
           '_input_charset' => 'utf-8',//请勿改动
           'it_b_pay'=>'30m',//请勿改动
        );

        $alipayObj = new Alipay_res();

        $ali_Arr = $alipayObj->argSort($ali_Arr);

        $str = '';
        foreach ($ali_Arr as $key => $value) {
            if ($str=='') {
                $str = $key.'='.'"'.$value.'"';
            }else{
                $str = $str.'&'.$key.'='.'"'.$value.'"';
            }
        }
        
        $sign = urlencode($alipayObj->rsaSign($str,$this->private_key_path));//签名结果
        if (!empty($sign)) {
            $alipayStr = $str.'&sign='.'"'.$sign.'"'.'&sign_type='.'"RSA"';//传给支付宝接口的数据 
        }
        echo $alipayStr;
    }

     /**
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    public function rsaVerify($str)  {
        $alipayObj = new Alipay_res();
        echo $alipayObj->rsaVerify($str,$alipayObj->rsaSign($str,$this->private_key_path));
    }
    
    
}