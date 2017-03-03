<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respond extends CI_Controller {

	public function __construct()
    {
      	parent::__construct();
      	
      	$this->load->service('fundOrder_service');
    }

    public function ticket(){

   		$post   =file_get_contents('php://input');
   		//file_put_contents('t3.html', date('Y-m-d H:i:s').'--post--'.$post.PHP_EOL, FILE_APPEND); 
        
        $encode_ticket = isimplexml_load_string($post, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (empty($post) || empty($encode_ticket)) {
        	file_put_contents('t.html', date('Y-m-d H:i:s').'--post is null--'.$content.PHP_EOL, FILE_APPEND); 
            exit('fail');
        }
        $decode_ticket = aes_decode($encode_ticket->Encrypt, C('basic_info.EncodingAesKey'));
        $ticket_xml = isimplexml_load_string($decode_ticket, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (empty($ticket_xml)) {
        	file_put_contents('t.html', date('Y-m-d H:i:s').'--ticket_xml is null--'.PHP_EOL, FILE_APPEND); 
            exit('fail');
        }
        if (!empty($ticket_xml->ComponentVerifyTicket) && $ticket_xml->InfoType == 'component_verify_ticket') {
        	file_put_contents('t.html', date('Y-m-d H:i:s').'--ticket_xml:'.$ticket_xml->ComponentVerifyTicket.PHP_EOL, FILE_APPEND);
            wkcache('account:component:ticket', strval($ticket_xml->ComponentVerifyTicket));
        }
        exit('success');
    }

    public function jump_AliPay(){}
    public function notice_AliPay(){}

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
			$this->load->service('printapi_service');
			
			$order_id = $arrReturn['order_id'];
			$arrResultPay = $this->order_service->pay($order_id);

			$data_tmp = json_encode($arrResultPay);
			file_put_contents('wx_js.log', date('Y-m-d H:i:s').'=>: '.$data_tmp."\r\n\r\n",FILE_APPEND);

			if(strtoupper($arrResultPay['code'])=='SUCCESS'){
				file_put_contents('wx_js.log', date('Y-m-d H:i:s').'=>: 打印订单'.$order_id."\r\n\r\n",FILE_APPEND);
				$this->printapi_service->orderprint_internal_push($order_id);	//推送订单
			}
		}

		return $czResult;
	}




}