<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bind extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

	}

	public function onekey(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$this->load->model('oil/Company_config_model');
		$info  = $this->Company_config_model->get_by_id($company_id);

		$authurl = '';
		if(!empty($info) && !empty($info['wx_username'])){
			$authurl = '1';
		}else{
			$this->load->library('WeixinThird');
			$weixin = new WeixinThird($company_id);
			$authurl = $weixin->getAuthLoginUrl();
		}

		$data = array('authurl'=>$authurl);
		$this->load->view('seller/wx/bind_onekey',$data);
	}

	public function alipay_auth(){

		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$this->load->model('oil/Company_config_model');
		$info  = $this->Company_config_model->get_by_id($company_id);

		$authurl = '';
		if(!empty($info) && !empty($info['ali_appid'])){
			$authurl = '1';
		}else{
			// $this->load->library('AlipayThird');
			// $ali3 = new AlipayThird($info);

			$authurl = BASE_SITE_URL.'/api/aliauth/go?url='.BASE_SITE_URL.'/seller/bind/alipay_auth$auth3=1$company_id='.$company_id;

		}
		$data = array('authurl'=>$authurl);
		$this->load->view('seller/ali/bind_onekey',$data);
	}

	public function clean(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$this->load->model('oil/Company_config_model');
		$data = array('wx_username'=>'');
		$this->Company_config_model->update_by_id($company_id, $data);

		redirect(SELLER_SITE_URL.'/bind/onekey');
	}

	public function ali_clean(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$this->load->model('oil/Company_config_model');
		$data = array('ali_appid'=>'');
		$this->Company_config_model->update_by_id($company_id, $data);

		redirect(SELLER_SITE_URL.'/bind/alipay_auth');
	}


}