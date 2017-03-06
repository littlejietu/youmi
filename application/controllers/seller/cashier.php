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
        $cFieldName = $this->input->post_get('search_field_name');
        $cKey = $this->input->post_get('search_field_value');

        $site_list = array();
        $site_where = array('company_id'=>$sellerInfo['company_id'],'status<>'=>-1);
        $site_list_all = $this->Site_model->get_list($site_where,'id,site_name');
        foreach ($site_list_all as $k => $v) {
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
        $arrFileld = array('name','mobile');
        if($cKey && in_array($cFieldName,$arrFileld))
        {
            $arrParam['search_field_name'] = $cFieldName;
            $arrParam['search_field_value'] = $cKey;
            $arrWhere[$cFieldName.' like '] = "'%$cKey%'";
        }

        $list = $this->O_admin_model->fetch_page($page, $pagesize, $arrWhere,'*');

        foreach($list['rows'] as $k => $v){

           	$site_info = $site_list[$v['site_ids']];

            $list['rows'][$k]['site_name'] = $site_info['site_name'];
        }
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
            'site_list' => $site_list,
        );

        $this->load->view('seller/oil/cashier',$result);

    }

    //新增
    public function add()
    {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        $site_id = $this->input->post_get('site_id');
        $id = $this->input->get('id');

        $this->load->model('oil/Company_model');
        $this->load->model('oil/Site_model');
        $this->load->model('oil/Price_model');

        $site_list = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status<>'=>-1),'id,site_name');
        $info = array();
        if(!empty($id)){
            $info = $this->O_admin_model->get_by_id($id);
        }

        $arrParam = array('site_id'=>$site_id);
        $result = array(
            'info' => $info,
            'arrParam' => $arrParam,
            'site_list' => $site_list,
        );
        
        $this->load->view('seller/oil/cashier_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];

        $this->load->service('oiladmin_service');
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'name',
                    'label'   => '姓名',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'site_id',
                    'label'   => '加油站',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $site_id = $this->input->post('site_id');
                $user_name = $this->input->post('user_name');
                $user_pwd = $this->input->post('user_pwd');

                if(!empty($user_name)){
	                $aUser = $this->O_admin_model->get_by_where(array('username'=>"'$user_name'"));
	                if(!empty($aUser)){
	                	showDialog('用户名已存在，请换一个');
		            	exit;
	                }
                }
                
                $data = array(
                    'name' => $this->input->post('name'),
                    'mobile' => $this->input->post('mobile'),
                    'site_ids' => $site_id,
                    'company_id' => $company_id,
                    'status' => $this->input->post('status'),
                );
               
                if(empty($id)){
                	$data['username'] = $user_name;
                	$data['password'] = md5($user_pwd);
                	$data['is_cashier'] = 1;
                	$data['is_super'] = 0;
                	$this->O_admin_model->insert_string($data);
                }else{
                	if(!empty($user_pwd))
                		$data['password'] = md5($user_pwd);
                    $this->O_admin_model->update_by_id($id, $data);
                }

                redirect(SELLER_SITE_URL.'/cashier?site_id='.$site_id);
            }
        }
    }
  
    //删除操作
    public function del()
    {
        if ($this->input->is_post())
        {
            $id = $this->input->post('del_id');
        }
        else
        {
            $id	= $this->input->get('id');
        }
        
        $where = array('id'=>$id);
        $tmp_id = $id;
        if(is_array($id))
            $tmp_id = $id[0];
        $info = $this->O_admin_model->get_by_id($tmp_id);
        $site_id = $info['site_id'];

        $this->O_admin_model->update_by_where($where,array('status'=>-1));
        redirect( SELLER_SITE_URL.'/cashier?site_id='.$site_id );
    }

    public function ajax_check_name()
	{
	    $user_name = $this->input->get('user_name');
	    if (!empty($user_name))
	    {
	        $where = array('username'=>"$user_name");
	        $res = $this->O_admin_model->get_by_where($where);
	        if (!empty($res))
	            exit('false');
	        else 
	            exit('true');
	    }
	    else 
	        exit('false');
	}

}