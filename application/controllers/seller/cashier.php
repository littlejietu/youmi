<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier extends BaseSellerController {

    public function __construct()
    {
        
        parent::__construct();
        $this->load->model(array('oil/O_admin_model','oil/Site_model'));
    }
    
    
    public function index() {
    	$sellerInfo = $this->seller_info;
        $site_id = $this->input->post_get('site_id');

        $site_list = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status<>'=>-1),'id,site_name');
        foreach ($site_list as $k => $v) {
        	$site_list[$v['id']] = $v;
        }

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('company_id'=>$sellerInfo['company_id'],'is_cashier'=>1,'is_super<>'=>1,'status<>'=>-1);
        if(!empty($site_id)){
        	$arrWhere['site_ids'] = $site_id;
        	$arrParam['site_id'] = $site_id;
        }

        $list = $this->O_admin_model->fetch_page($page, $pagesize, $arrWhere,'*');

         foreach($list['rows'] as $k => $v){

           	$site_info = $site_list[$v['site_ids']];

            $list['rows'][$k]['site_name'] = $site_info['site_name'];
        }
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/oil/cashier',$result);

    }

}