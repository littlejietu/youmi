<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gun extends BaseSellerController {

    public function __construct()
    {
        
        parent::__construct();
        $this->load->model('oil/Gun_model');
    }
    
    
    public function index() {
        $sellerInfo = $this->seller_info;
        $site_id = $this->input->post_get('site_id');

        $this->load->model('oil/Site_model');

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('site_id'=>$site_id,'company_id'=>$sellerInfo['company_id']);

        $arrParam['site_id'] = $site_id;

        $list = $this->Gun_model->fetch_page($page, $pagesize, $arrWhere,'*');

         foreach($list['rows'] as $k => $v){

           $siteName = $this->Site_model->get_by_id($v['site_id'],'site_name');

            $list['rows'][$k]['site_name'] = $siteName['site_name'];
        }
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/oil/gun',$result);
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

        
        $info = array();
        if(!empty($id)){
            $info = $this->Gun_model->get_by_id($id);
            $site_id = $info['site_id'];
        }

        $oilprice_list = $this->Price_model->get_list(array('company_id'=>$sellerInfo['company_id'],'site_id'=>$site_id),'id,oil_no');
    
        $company_site = '';
        $comInfo = $this->Company_model->get_by_id($company_id);
        if(!empty($comInfo))
            $company_site = $comInfo['company'];
        $siteInfo = $this->Site_model->get_by_id($site_id);
        if(!empty($siteInfo))
            $company_site .= ' - '.$siteInfo['site_name'];

        $arrParam = array('site_id'=>$site_id);
        $result = array(
            'info' => $info,
            'company_site' => $company_site,
            'arrParam' => $arrParam,
            'oilprice_list' => $oilprice_list,
        );
        
        $this->load->view('seller/oil/gun_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'gun_no',
                    'label'   => '油枪号',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'oil_no',
                    'label'   => '油品',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'pump_no',
                    'label'   => '泵码',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $site_id = $this->input->post('site_id');
                

                $data = array(
                    'gun_no' => $this->input->post('gun_no'),
                    'oil_no' => $this->input->post('oil_no'),
                    'pump_no' => $this->input->post('pump_no'),
                    'site_id' => $site_id,
                    'company_id' => $company_id,
                );
               
                if(empty($id)){
                    //$data['addtime'] = time();
                    $this->Gun_model->insert($data);
                }else{
                    //$data['updatetime'] = time();
                    $this->Gun_model->update_by_id($id, $data);
                }

                redirect(SELLER_SITE_URL.'/gun?site_id='.$site_id);
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
        $info = $this->Gun_model->get_by_id($tmp_id);
        $site_id = $info['site_id'];

        $this->Gun_model->delete_by_id($id);
        redirect( SELLER_SITE_URL.'/gun?site_id='.$site_id );
    }
    
   

   
    
    
    
}
