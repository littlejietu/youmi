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
        $this->load->model(array('sys/Product_model','oil/O_admin_model','oil/Company_config_model'));

        $info = array();
        if(!empty($id)){
            $info = $this->Company_model->get_by_id($id);

            $config_info = $this->Company_config_model->get_by_id($id);
            $config_info = empty($config_info)?array():$config_info;
            $info = array_merge($info, $config_info);

            $admin_info = $this->O_admin_model->get_by_where(array('company_id'=>$info['id'],'is_super'=>1,'status'=>1),'id as admin_id,username');
            $admin_info = empty($admin_info)?array():$admin_info;
            $info = array_merge($info, $admin_info);
        }

        
        
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
                $user_name = $this->input->post('user_name');
                $user_pwd = $this->input->post('user_pwd');
                $admin_id = $this->input->post('admin_id');
                $wx_appid = $this->input->post('wx_appid');
                $wx_mchid = $this->input->post('wx_mchid');

                $this->load->model(array('sys/Level_model','oil/Company_config_model','oil/O_admin_model'));

                if(!empty($user_name)){
                    $aUser = $this->O_admin_model->get_by_where(array('username'=>"$user_name"));
                    if(!empty($aUser)){
                        showDialog('管理员用户名已存在，请换一个');
                        exit;
                    }
                }

                $company = $this->input->post('company');
                $data = array(
                    'company' => $company,
                    'company_long' => $this->input->post('company_long'),
                    'product_id' => $this->input->post('product_id'),
                    'prd_start_time' => $prd_start_time,
                    'prd_end_time' => $prd_end_time,
                    'linkman' => $this->input->post('linkman'),
                    'phone' => $this->input->post('phone'),
                    'status' => $this->input->post('status'),
                );

                if(empty($id)){
                    //自动seller添加
                    $this->load->service('user_service');
                    $seller_userid = 0;
                    $seller = $user_name.'@com';
                    $user_data = array('user_name'=>$seller,'mobile'=>'','pwd'=>$seller,'site_id'=>0,
                        'name'=>$company, 'platform_id' =>1);
                    $arrReturn = $this->user_service->reg_user($user_data);
                    if($arrReturn['code']=='SUCCESS')
                        $seller_userid = $arrReturn['data']['user_id'];
                    else{
                        showDialog('初始化商户失败');
                        exit;
                    }

                    //初始化帐户
                    $this->load->model('acct/Account_model');
                    $this->Account_model->init($seller_userid);

                    $data['addtime'] = time();
                    $data['seller_userid'] = $seller_userid;
                    $data['seller_username'] = $seller;
                    $company_id = $this->Company_model->insert_string($data);
                    

                    $data_config = array('company_id'=>$company_id,'is_agent'=>0);
                    if(!empty($wx_appid))
                        $data_config['wx_appid'] = $wx_appid;
                    if(!empty($wx_mchid))
                        $data_config['wx_mchid'] = $wx_mchid;
                    $this->Company_config_model->insert_string($data_config);

                    $prefix = $this->Level_model->prefix();
                    $sql = 'insert '.$prefix.'sys_level(level_id,level_name,integral_num, next_msg,company_id) select id as level_id,level_name,integral_num, next_msg,'.$company_id.' from '.$prefix.'sys_level_def where not exists(select 1 from '.$prefix.'sys_level where company_id='.$company_id.')';
                    $this->Level_model->execute($sql);

                    //添加管理员
                    $data_admin = array('username'=>$user_name,'password'=>md5($user_pwd),'name'=>'管理员','is_super'=>1,'is_cashier'=>0,'company_id'=>$company_id,'site_ids'=>'','status'=>1);
                    $this->O_admin_model->insert_string($data_admin);

                }else{
                    $this->Company_model->update_by_id($id, $data);

                    $data_config = array();
                    if(!empty($wx_appid))
                        $data_config['wx_appid'] = $wx_appid;
                    if(!empty($wx_mchid))
                        $data_config['wx_mchid'] = $wx_mchid;
                    if(!empty($data_config))
                        $this->Company_config_model->update_by_id($id, $data_config);

                    //修改密码
                    if(!empty($user_pwd)&&!empty($admin_id))
                        $this->O_admin_model->update_by_id($admin_id, array('password'=>md5($user_pwd)));
                    
                }

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
        $this->Company_model->update_by_id($id, $data);
        redirect( ADMIN_SITE_URL.'/company' );
    }
    
   

   
    
    
    
}
