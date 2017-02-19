<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aliauth extends CI_Controller {

	public function __construct()
    {
      	parent::__construct();

      	$this->load->library('AlipayThird');
        $this->load->model('oil/Site_model');
        $this->load->model('oil/Site_config_model');
    }

    public function authorize(){
    	
		$auth3 = $this->input->post_get('auth3');
		if($auth3==1){

			$app_auth_code = $this->input->post_get('app_auth_code');
			$company_id = $this->input->post_get('company_id');

			$this->load->model('oil/Company_config_model');

			$aliConfig = $this->Company_config_model->get_by_id($company_id);
			if(empty($aliConfig)){
	            show_error('该公司不存在');
	            //exit;
		    }

			$objThird = new AlipayThird($aliConfig);
			$result = $objThird->getAuthToken($app_auth_code);
			if(!empty($result)){
				$data = array('ali_auth_token'=>$result['value'],'ali_appid'=>$result['app_id'],'ali_refresh_token'=>$result['refresh_token'],'ali_token_expire'=>$result['expire'],'ali_refresh_expires_in'=>$result['re_expires_in']);
				$this->Company_config_model->update_by_id($company_id, $data);

				showMessage('授权成功',BASE_SITE_URL.'/seller/bind/alipay_auth');
			}
		}else{
			/*
	    	$code = $this->input->post_get('code');
	    	$url = $this->input->post_get('url');
	        $invite_id = $this->input->post_get('invite_id');
	        $client_type = $this->input->post_get('client_type');
	        if(!$client_type)
	            $client_type = 'app';

	        $client_type = $this->input->post_get('client_type');

	        $aliConfig = array();
	        $arrUrl=parse_url($url);
	        if(!empty($arrUrl['query'])){
		        parse_str($arrUrl['query'],$arrParam);
		        if(empty($arrParam['site_id'])){
		            show_error('站id参数错误');
		            exit;
		        }
	        
	        
		        $site_id = $arrParam['site_id'];
		        $info = $this->Site_model->get_by_id($site_id,'company_id');
		        if(empty($info)){
		            show_error('该加油站不存在');
		            exit;
		        }
		        $aliConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
		        
		        $objThird = new AlipayThird($aliConfig);
		        $objThird->init_auth($code, $url, $site_id,$invite_id,$client_type);
	        }
			*/
		}


    }

    public function go(){
        $gotoUrl = str_replace('$', '&', $this->input->get('url') );
        $invite_id = $this->input->get('invite_id');
        $invite_param = '';
        if(!empty($invite_id))
            $invite_param = "&invite_id=$invite_id";

        $aliConfig = array();
        $arrUrl=parse_url($gotoUrl);
        parse_str($arrUrl['query'],$arrParam);
        if(!empty($arrParam['site_id'])){
            $site_id = $arrParam['site_id'];
            $info = $this->Site_model->get_by_id($site_id,'company_id');
            if(empty($info))
                show_error('该加油站不存在');
            
            $aliConfig = $this->Site_config_model->getPayConfig($site_id, $info['company_id']);
        }

        $objThird = new AlipayThird($aliConfig);
        $redirect_uri = BASE_SITE_URL.'/api/aliauth/authorize?url='.$gotoUrl.$invite_param.'&client_type=wap';
        $objThird->authz($redirect_uri);
    }


}