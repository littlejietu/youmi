<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respond extends CI_Controller {

	public function __construct()
    {
      	parent::__construct();
      	
      	$this->load->service('fundOrder_service');
    }

	public function jump_WeixinPayJs(){

	}

	public function notice_WeixinPayJs(){
		$xml = file_get_contents("php://input");
		file_put_contents('wx_js.log', date('Y-m-d H:i:s').'=>: '.$xml."\r\n\r\n",FILE_APPEND);
		$result = $this->notice('WeixinPayJs', $xml);

		echo $result;
	}

	public function notice_WeixinPayApp(){
		$xml = file_get_contents("php://input");
		//file_put_contents('wx.log', date('Y-m-d H:i:s').'=>: '.$xml."\r\n\r\n",FILE_APPEND);
		$result = $this->notice('WeixinPayApp', $xml);

		echo $result;
	}



	/*private function jump($payMethodName, $data){

		Ctm_OrderResultModel result = $this->fundorder_service->jump($payMethodName, $data);
	}*/

	private function notice($payMethodName, $data){
		$czResult = '';
		// if($payMethodName=='WeixinPayJs' || $payMethodName=='WeixinPayApp'){

		// }

		$arrReturn = $this->fundorder_service->notice($payMethodName, $data);

		if ($payMethodName == C('PayMethod.WeixinPayApp'))
			$czResult = "<xml><return_code><![CDATA[". $arrReturn['code'] . "]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		else
			$czResult = $arrReturn['code'];

		if( strtoupper($arrReturn['code'])  =='SUCCESS'){
			$this->load->service('order_service');
			$this->order_service->pay($arrReturn['order_id']);
			//多个订单时，都处理成等待发货
			//$arrReturn['order_sn']
		}

		return $czResult;
	}




}