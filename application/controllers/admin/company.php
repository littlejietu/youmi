<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends MY_Admin_Controller {

    public function __construct()
    {
        
        parent::__construct();
        $this->load->model('oil/Company_model');
    }
    
    
    public function index() {
        
        //$this->lang->load('admin_layout');
        //$this->lang->load('admin_admin');
        $cFieldName = $this->input->post_get('search_field_name');
        $cKey = $this->input->post_get('search_field_value');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $product_id = $this->input->post_get('product_id');

        if($cFieldName=='li_nkman')
            $cFieldName = 'linkman';

        $this->load->model('sys/Product_model');
        $page     = _get_page();
        $pagesize = 5;
        $arrParam = array();
        $arrWhere = array('status<>'=>-1);

        $arrFileld = array('company','linkman','phone');
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
        if(!empty($product_id))
        {
            $arrWhere['product_id'] = $product_id;
            $arrParam['product_id'] = $product_id;
        }

        $list = $this->Company_model->fetch_page($page, $pagesize, $arrWhere,'*');
        //echo $this->Company_model->db->last_query();
        foreach($list['rows'] as $k => $v){

           $aName = $this->Product_model->get_by_id($v['product_id'],'name');

            $list['rows'][$k]['product_name'] = $aName['name'];
        }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/company', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
        );

        $this->load->view('admin/oil/company',$result);
    }
    
    //新增
    public function add()
    {
         //$this->lang->load('admin_layout');
         //$this->lang->load('admin_admin');

        $id = $this->input->get('id');

        $info = array();
        if(!empty($id))
            $info = $this->Company_model->get_by_id($id);

        
        $this->load->model('sys/Product_model');
        $product_list = $this->Product_model->get_list();
        //print_r($product_list);die;
        $result = array(
            'product_list' => $product_list,
            'info' => $info,
        );
        
        $this->load->view('admin/oil/company_add',$result);
    }
    
    public function save()
    {
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'company',
                    'label'   => '公司名称',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'company_long',
                    'label'   => '公司全称',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $prd_start_time = $this->input->post('prd_start_time');
                $prd_start_time = !empty($prd_start_time)?strtotime($prd_start_time):0;
                $prd_end_time = $this->input->post('prd_end_time');
                $prd_end_time = !empty($prd_end_time)?strtotime($prd_end_time):0;

                $data = array(
                    'company' => $this->input->post('company'),
                    'company_long' => $this->input->post('company_long'),
                    'product_id' => $this->input->post('product_id'),
                    'prd_start_time' => $prd_start_time,
                    'prd_end_time' => $prd_end_time,
                    'linkman' => $this->input->post('linkman'),
                    'phone' => $this->input->post('phone'),
                    'status' => $this->input->post('status'),
                );

                if(empty($id)){
                    $data['addtime'] = time();
                    $this->Company_model->insert($data);
                }
                else
                    $this->Company_model->update_by_id($id, $data);
                
                redirect(ADMIN_SITE_URL.'/company');
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
        
        $data['status'] = -1;
        $where['id'] = $id;
        $this->Company_model->delete_by_id($id);
        redirect( ADMIN_SITE_URL.'/company' );
    }
    
   

   
    
    
    
}
