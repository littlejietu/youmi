<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wxauth extends CI_Controller {
	public function __construct()
    {
      	parent::__construct();

      	$this->load->library('WeixinAuth');
        $this->load->model('oil/Site_model');
        $this->load->model('oil/Site_config_model');
    }

    public function index(){
        $this->weixinauth->token();
    }

    public function menu_create()
    {
        $this->weixinauth->menu_create();
    }

    public function token(){
    	$arrReturn = array('code'=>'EMPTY','message'=>'','data'=>'');
        $site_id = $this->input->get('site_id');
        $wxConfig = C('PayConfig.WXPAY');
        if(!empty($site_id)){
            $info = $this->Site_model->get_by_id($site_id,'company_id');
            $wxConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
        }
    	$arrReturn['data'] = $this->weixinauth->token($wxConfig);

    	echo json_encode($result);
    }

    public function authorize(){
    	$code = $this->input->post_get('code');
    	$url = $this->input->post_get('url');
        $invite_id = $this->input->post_get('invite_id');
        $client_type = $this->input->post_get('client_type');
        if(!$client_type)
            $client_type = 'app';

        $wxConfig = C('PayConfig.WXPAY');
        $arrUrl=parse_url($gotoUrl);
        parse_str($arrUrl['query'],$arrParam);
        if(!empty($arrParam['site_id'])){
            $site_id = $arrParam['site_id'];
            $info = $this->Site_model->get_by_id($site_id,'company_id');
            $wxConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
        }

    	$this->weixinauth->init_auth($code, $wxConfig, $url, $invite_id,$client_type);

    }

    public function go(){
        $gotoUrl = str_replace('$', '&', $this->input->get('url') );
        $invite_id = $this->input->get('invite_id');
        $invite_param = '';
        if(!empty($invite_id))
            $invite_param = "&invite_id=$invite_id";

        $wxConfig = C('PayConfig.WXPAY');
        $arrUrl=parse_url($gotoUrl);
        parse_str($arrUrl['query'],$arrParam);
        if(!empty($arrParam['site_id'])){
            $site_id = $arrParam['site_id'];
            $info = $this->Site_model->get_by_id($site_id,'company_id');
            $wxConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
        }

        $redirect_uri = BASE_SITE_URL.'/api/wxauth/authorize?url='.$gotoUrl.$invite_param.'&client_type=wap';
        $this->weixinauth->authz($redirect_uri, $wxConfig);
    }

    public function jsapi(){
    	$url = $_POST['url'];
        $site_id = $_POST['site_id'];
    	$this->load->library('WxJsApi');

        $wxConfig = C('PayConfig.WXPAY');
        if(!empty($site_id)){
            $info = $this->Site_model->get_by_id($site_id,'company_id');
            $wxConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
        }

    	$arrReturn = $this->wxjsapi->getSignPackage($url, $wxConfig);
        output_data($arrReturn);

    }

}

