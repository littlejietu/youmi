<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gift extends BaseSellerController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pmt/Gift_model');
    }
    
    
    public function index() {
        $sellerInfo = $this->seller_info;
        $cKey = $this->input->post_get('search_field_value');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $is_limit_per_num = $this->input->post_get('is_limit_per_num');
        $company_id = $sellerInfo['company_id'];

        $page     = _get_page();
        $pagesize = 5;
        $arrParam = array();
        $arrWhere = array('status<>'=>-1,'company_id'=>$company_id);

        if($cKey)
        {
            $arrParam['search_field_value'] = $cKey;
            $arrWhere['name like '] = "'%$cKey%'";
        }

        $arrFileldTime = array('addtime');
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

        if(!empty($is_limit_per_num)){
            $arrWhere['is_limit_per_num'] = $is_limit_per_num;
            $arrParam['is_limit_per_num'] = $is_limit_per_num;
        }

        $list = $this->Gift_model->fetch_page($page, $pagesize, $arrWhere,'*');
        //print_r($list);die;
        // foreach($list['rows'] as $k => $v){

        //    $aName = $this->Product_model->get_by_id($v['product_id'],'name');

        //     $list['rows'][$k]['product_name'] = $aName['name'];
        // }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/pmt/gift', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        
        $result = array(
            'list' =>$list,
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/pmt/gift',$result);
    }
    
    //新增
    public function add()
    {
         //$this->lang->load('admin_layout');
         //$this->lang->load('admin_admin');
        $this->load->model('pmt/Gift_model');

        $id = $this->input->get('id');
        $company_id = $this->input->post_get('company_id');

        $info = array();
        if(!empty($id))
            $info = $this->Gift_model->get_by_id($id);
        
        //print_r($product_list);die;
        $result = array(
            'info' => $info,
        );
        
        $this->load->view('seller/pmt/gift_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'name',
                    'label'   => '名称',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'org_price',
                    'label'   => '原价',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'integral',
                    'label'   => '积分',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $company_id = $sellerInfo['company_id'];
                $is_limit_per_num = $this->input->post('is_limit_per_num');
                $is_limit_per_num = !empty($is_limit_per_num)?$is_limit_per_num:2;

                $data = array(
                    'name' => $this->input->post('name'),
                    'org_price' => $this->input->post('org_price'),
                    'integral' => $this->input->post('integral'),
                    'no' => $this->input->post('no'),
                    'stock_num' => $this->input->post('stock_num'),
                    'is_limit_per_num' => $is_limit_per_num,
                    'limit_per_num' => $this->input->post('limit_per_num'),
                    'company_id' => $company_id,
                    'status' => $this->input->post('status'),
                );
                $img = $this->input->post('img');
                if($img)
                    $data['img'] = $img;
                else 
                    $data['img'] = $this->input->post('orig_img');

                if(empty($id)){
                    $data['addtime'] = time();
                    $this->Gift_model->insert($data);
                }else
                    $this->Gift_model->update_by_id($id, $data);
                
                redirect(SELLER_SITE_URL.'/gift');
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
        $where = array('id'=>$id);
        $this->Gift_model->update_by_where($where, $data);
        redirect( SELLER_SITE_URL.'/gift' );
    }

    public function change(){
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        $cKey = $this->input->post_get('search_field_value');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $change_type = $this->input->post_get('change_type');
        $site_id = $this->input->post_get('site_id');


        $this->load->model('pmt/Gift_user_model');
        $this->load->model('oil/Site_model');

        $site_list = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1),'id,site_name');

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('a.status<>'=>-1,'a.company_id'=>$company_id);

        if($cKey)
        {
            $arrParam['search_field_value'] = $cKey;
            $arrWhere['b.name like '] = "'%$cKey%'";
        }

        if(!empty($time1))
        {
            $arrWhere['change_time >= '] = strtotime($time1);
            $arrParam['time1'] = $time1;
        }
        if(!empty($time2))
        {
            $arrWhere['change_time <= '] = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
        }

        if(!empty($change_type)){
            $arrWhere['change_type'] = $change_type;
            $arrParam['change_type'] = $change_type;
        }

        if(!empty($site_id)){
            $arrWhere['site_id'] = $site_id;
            $arrParam['site_id'] = $site_id;
        }

        $dbprefix = $this->Gift_user_model->prefix();
        $tb = $dbprefix.'pmt_gift_user a left join '.$dbprefix.'pmt_gift b on(a.gift_id=b.id) left join '.$dbprefix.'user u on(a.user_id=u.user_id) left join '.$dbprefix.'oil_site s on(a.site_id=s.id)';
        $field = 'a.*,b.name,b.img,u.user_name,s.site_name';
        $list = $this->Gift_user_model->fetch_page($page, $pagesize, $arrWhere, $field, 'a.change_time desc',$tb);
        //print_r($list);die;
        // foreach($list['rows'] as $k => $v){

        //    $aName = $this->Product_model->get_by_id($v['product_id'],'name');

        //     $list['rows'][$k]['product_name'] = $aName['name'];
        // }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/pmt/gift_change', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        
        $result = array(
            'list' => $list,
            'site_list' => $site_list,
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/pmt/gift_change',$result);

    }
    

    
}
