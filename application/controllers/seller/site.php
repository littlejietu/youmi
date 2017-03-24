<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends BaseSellerController {

    public function __construct()
    {
        
        parent::__construct();
        $this->load->model('oil/Site_model');
    }
    
    
    public function index() {
        
        $sellerInfo = $this->seller_info;

        $cFieldName = $this->input->post_get('search_field_name');
        $cKey = $this->input->post_get('search_field_value');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $company_id = $this->input->post_get('company_id');


        // $this->load->model('sys/Product_model');
        // $this->load->model('oil/Company_model');

        if($cFieldName=='li_nkman')
            $cFieldName = 'linkman';

        $page     = _get_page();
        $pagesize = 5;
        $arrParam = array();
        $arrWhere = array('status<>'=>-1, 'company_id'=>$sellerInfo['company_id']);

        $company_name = '';
        if(!empty($company_id)){
            $arrParam['company_id'] = $company_id;
            $arrWhere['company_id'] = $company_id;

            $comInfo = $this->Company_model->get_by_id($company_id);
            if(!empty($comInfo))
                $company_name = $comInfo['company'];
        }

        $arrFileld = array('site_name','linkman','phone');
        if($cKey && in_array($cFieldName,$arrFileld))
        {
            $arrParam['search_field_name'] = $cFieldName;
            $arrParam['search_field_value'] = $cKey;
            $arrWhere[$cFieldName.' like '] = "'%$cKey%'";
        }

        $arrFileldTime = array('addtime','prd_start_time','prd_end_time');
        if(!empty($time1) && in_array($search_time,$arrFileldTime))
        {
            $arrWhere[$search_time.' >= '] = strtotime($time1);
            $arrParam['time1'] = $time1;
        }
        if(!empty($time2) && in_array($search_time, $arrFileldTime))
        {
            $arrWhere[$search_time.' <= '] = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
        }

        $list = $this->Site_model->fetch_page($page, $pagesize, $arrWhere,'*');
        //echo $this->Site_model->db->last_query();
        //print_r($list);die;
        // foreach($list['rows'] as $k => $v){

        //    $aName = $this->Product_model->get_by_id($v['product_id'],'name');

        //     $list['rows'][$k]['product_name'] = $aName['name'];
        // }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/site', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
            'company_name' => $company_name,
        );

        $this->load->view('seller/oil/site',$result);
    }
    
    //新增
    public function add()
    {

        $this->load->model('oil/Company_model');

        $id = $this->input->get('id');
        $company_id = $this->input->post_get('company_id');

        $info = array();
        if(!empty($id))
            $info = $this->Site_model->get_by_id($id);
        $company_name = '';
        if(!empty($company_id)){
            $comInfo = $this->Company_model->get_by_id($company_id);
            if(!empty($comInfo))
                $company_name = $comInfo['company'];
        }


        
        // $this->load->model('sys/Product_model');
        // $this->load->model('oil/Company_model');
        // $product_list = $this->Product_model->get_list();
        // $company_list = $this->Company_model->get_list(array('status'=>1));

        //print_r($product_list);die;
        $result = array(
            // 'product_list' => $product_list,
            // 'company_list' => $company_list,
            'info' => $info,
            'company_id' => $company_id,
            'company_name' => $company_name,
        );
        
        $this->load->view('seller/oil/site_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;

        $this->load->model('oil/Company_model');
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'site_name',
                    'label'   => '油站名称',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'site_long',
                    'label'   => '油站全称',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {

                $id = $this->input->post('id');
                $company_id = $sellerInfo['company_id'];
                $prd_start_time = $this->input->post('prd_start_time');
                $prd_start_time = !empty($prd_start_time)?strtotime($prd_start_time):0;
                $prd_end_time = $this->input->post('prd_end_time');
                $prd_end_time = !empty($prd_end_time)?strtotime($prd_end_time):0;
                $refund_pwd = $this->input->post('refund_pwd');
                if(empty($refund_pwd))
                    $refund_pwd = 'mirong';

                $com_info = $this->Company_model->get_by_id($company_id);

                $data = array(
                    'site_name' => $this->input->post('site_name'),
                    'site_long' => $this->input->post('site_long'),
                    'public_name' => $this->input->post('public_name'),
                    'refund_pwd' => md5($refund_pwd),
                    'reg_address' => $this->input->post('reg_address'),
                    'linkman' => $this->input->post('linkman'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'qq' => $this->input->post('qq'),
                    'product_license' => $this->input->post('product_license'),
                    'retail_license' => $this->input->post('retail_license'),
                    'risk_license' => $this->input->post('risk_license'),
                    'company_id' => $company_id,
                    'net_id' => $this->input->post('net_id'),
                    'status' => $this->input->post('status'),
                    'seller_userid' => $com_info['seller_userid'],
                    'seller_username' => $com_info['seller_username'],
                );
                $img = $this->input->post('img');
                if($img)
                    $data['product_license'] = $img;
                else 
                    $data['product_license'] = $this->input->post('orig_img');

                $retail_license_img = $this->input->post('retail_license_img');
                if($retail_license_img)
                    $data['retail_license'] = $retail_license_img;
                else 
                    $data['retail_license'] = $this->input->post('retail_license_orig_img');

                $risk_license_img = $this->input->post('risk_license_img');
                if($risk_license_img)
                    $data['risk_license'] = $risk_license_img;
                else 
                    $data['risk_license'] = $this->input->post('risk_license_orig_img');
                
                if(empty($id)){
                    $data['addtime'] = time();
                    $this->Site_model->insert_string($data);
                }else
                    $this->Site_model->update_by_id($id, $data);
                
                redirect(SELLER_SITE_URL.'/site');
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
        
        $data = array('status'=>-1);
        $this->Site_model->update_by_id($id,$data);
        redirect( SELLER_SITE_URL.'/site' );
    }
    
   

   
    
    
    
}
