<?php
class PayConfig
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 *
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 *
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 *
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 *
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 
	const WxAPPID = 'wx4d8d716d6a610ca2';
	const WxMCHID = '1303493701';
	const WxKEY = '181d0ecef6e3d3a6cb8c1008ddfa9055';
	const WxAPPSECRET = 'e3d24fda456340f32f5ae5211a49bb36';
*/
	// const WapWxAPPID = 'wxf1d3385c898dca36';
	// const WapWxMCHID = '1271547101';
	// const WapWxKEY = '137d30ecef6e3d3a6cb8c1008ddfa90d';
	// const WapWxAPPSECRET = '425f17a1bc7f2e2be2deeeb9a85ad212';
	// const WapWxTOKEN = 'EVeOhxeU7PAMffjxl6AIniNq2N';

	/*//
	const WapWxAPPID = 'wxaf04a9b500505485';
	const WapWxMCHID = '1316706001';
	const WapWxKEY = '181d0ecef6e3d3a6cb8c1008ddfa9056';
	const WapWxAPPSECRET = '50b899b8e88bc51918e9c8ffa753a591';
	const WapWxTOKEN = 'kuFeOhxeU7PdXsfjxl6AIniNq2N';
	
	const WapWxAPPID = 'wx3a95840d3493c1d2';
	const WapWxMCHID = '1279497401';
	const WapWxKEY = 'yijU4Jwl8Kd30fsmVm7qp81dMn0hf8Ed';
	const WapWxAPPSECRET = '92a7ea36408ea2d4993ca6917b947ba3';
	const WapWxTOKEN = '27a04991d3edecfec2980f520fd949d5';
*/
	

	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const WxSSLCERT_PATH = '/cert/apiclient_cert.pem';
	const WxSSLKEY_PATH = '/cert/apiclient_key.pem';

	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const WxCURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const WxCURL_PROXY_PORT = 0;//8080;

	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const WxREPORT_LEVENL = 0;
}
?>