<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shop_transport extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model( 'Transport_model' ); 
		$this->load->model( 'Transport_tpl_model' );
		$this->load->model('Area_model');
		$this->load->model('Deliver_model');
        $this->lang->load('transport');
	}

	public function index(){
	    $shop_id = $this->input->post('shop_id');
	    $page     = _get_page();
	    $pagesize = 7;
	    $arrParam = array();
	    $arrWhere = array();
	    $arrWhere['shop_id'] = 1;
	    $arrWhere['status <>'] = -1;
        $trans = $this->Transport_model->fetch_page($page, $pagesize, $arrWhere,'*');
        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/shop_transport', $arrParam);
        $pagecfg['total_rows']   = $trans['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);
        $trans['pages'] = $this->pagination->create_links();
        $id = array();
        foreach ($trans['rows'] as $value)
        {
            $id[] = $value['id'];
        }
        
        unset($where);
        $tpl_trans = array();
        
        if (!empty($id))
        {
            $where['transport_id'] = $id;
            $where['is_default'] = 1;
            $where['status <>'] = -1;
            $tpl_trans = $this->Transport_tpl_model->get_list($where);
        }
        
        $result = array(
            'output'=>array('loginUser'=>$this->loginUser),
            'trans' => $trans['rows'],
            'tpl_trans' => $tpl_trans,
            'pages' => $trans['pages'],
        );
        
        
	    $this->load->view('seller/shop_transport',$result);
	}
	
	public function add()
	{
        $areas=$this->Area_model->getAreas();
	    $result = array(
            'output'=>array('loginUser'=>$this->loginUser),
	        'extends' =>'',
            'areas'=>$areas
	    );
	    $this->load->view('seller/shop_transport_add',$result);
	}
	
	public function edit()
	{
	    $id = $this->input->get('id');
	    $where['id'] = $id;
	    $extend_tpl = $this->Transport_model->get_by_where($where);
	    $where2['transport_id'] = $id;
	    $where2['status'] = 1;
	    $extends = $this->Transport_tpl_model->get_list($where2);
        $areas=$this->Area_model->getAreas();
	    $result = array(
	        'extend_tpl' => $extend_tpl,
	        'extends' => $extends,
            'areas'=>$areas
	    );
	    $this->load->view('seller/shop_transport_add',$result);
	}
	
	public function del()
	{
	    $id = $this->input->get('id');
	    $data['status'] = -1;
	    $this->Transport_model->update_by_id($id,$data);
	    $where['transport_id'] = $id;
	    $this->Transport_tpl_model->update_by_where($where,$data);
	    redirect(SELLER_SITE_URL.'/shop_transport');
	}
	
	public function clone_tpl()
	{
	    $id = $this->input->get('id');
	    $data = $this->Transport_model->get_by_id($id,'title,send_tpl_id,shop_id,status');
	    $data['updatetime'] = time();
	    $data['title'] .= '的副本';
	    $insert_id = $this->Transport_model->insert_string($data);
	    $where['transport_id'] = $id;
	    $tpl_data = $this->Transport_tpl_model->get_list($where,'area_id,top_area_id,area_name,snum,sprice,xnum,xprice,is_default,transport_id,transport_title,status');
	    foreach ($tpl_data as $value)
	    {
	        $value['transport_id'] = $insert_id;
	        $this->Transport_tpl_model->insert($value);
	    }
	    
	    redirect(SELLER_SITE_URL.'/shop_transport');
	}
	
	public function save()
	{
	    $transport_id = $this->input->post('transport_id');
	    $title = $this->input->post('title');
	    $shop_id = $this->input->post('shop_id');
	    $default = $this->input->post('default');
	    $areas = $this->input->post('areas');
	    $special = $this->input->post('special');
	    $data1 = array(
	        'shop_id' => $shop_id,
	        'title' => $title,
	        'updatetime' => time(),
	        'status' => 1,
	    );
	    if (empty($transport_id))
	    {
	        $insert_id = $this->Transport_model->insert_string($data1);
	    }
	    else
	    {
	        $this->Transport_model->update_by_id($transport_id,$data1);
	        $insert_id = $transport_id;
	    }
	    if (isset($insert_id))
	    {
	        $where['transport_id'] = $transport_id;
	        $arr['status'] = -1;
	        $this->Transport_tpl_model->update_by_where($where,$arr);
	        $data2 = array(
	                'transport_id' => $insert_id,
	                'transport_title' => $title,
	                'snum' => $default['kd']['start'],
	                'sprice' =>$default['kd']['postage'],
	                'xnum' => $default['kd']['plus'],
	                'xprice' => $default['kd']['postageplus'],
	                'is_default' => 1,
	                'status' => 1,
	            );
	        $this->Transport_tpl_model->insert($data2);
	    }
	    
	    foreach ($special['kd'] as $key => $value)
	    {
            if(empty($areas['kd'][$key])) continue;
            $_areas[$key]=explode('|||',$areas['kd'][$key]);
	        $data2 = array(
	            'transport_id' => $insert_id,
	            'transport_title' => $title,
	            'snum' => $value['start'],
	            'sprice' =>$value['postage'],
	            'xnum' => $value['plus'],
	            'xprice' => $value['postageplus'],
	            'status' => 1,
                'area_id' => ','.$_areas[$key][0].',',
	            'area_name' => $_areas[$key][1],
	        );

            //计算省份ID
            $province = array();
            $tmp = explode(',',$_areas[$key][0]);
            if (!empty($tmp) && is_array($tmp)){
                $city = $this->Area_model->getCityProvince();
                foreach ($tmp as $t) {
                    if(!empty($city[$t])){
                        $pid =$city[$t];
                    }
                    if (!in_array($pid,$province) && !empty($pid))$province[] = $pid;
                }
            }
            if (count($province)>0){
                $data2['top_area_id'] = ','.implode(',',$province).',';
            }else{
                $data2['top_area_id'] = '';
            }

            $this->Transport_tpl_model->insert($data2);
	    }        
	    redirect(SELLER_SITE_URL.'/shop_transport');
	}
	
	public function area()
	{
	    $this->lang->load('admin_layout');
	    
	    $list = $this->Area_model->getAreaArrayForJson();
	    $area = $this->Area_model->getAreas();
	    $where['shop_id'] = 1;
	    $province = $list[0];
	    $area_checked = $this->Deliver_model->get_city_checked_child_array($where);
	    $city_checked_child_array = array();
	    if (!empty($area_checked['county']))
	    {
	        foreach ($area_checked['county'] as $key=>$value)
	        {
	            $city_checked_child_array[$area['parent'][$value]][] = $value;
	        }
	    }
	    foreach ($area_checked['city'] as $key=>$value)
	    {
	        if(!isset($city_checked_child_array[$value]))
	        {
	            $city_checked_child_array[$value] = array();
	        }
	    }
	    $result = array(
	        'province' => $province,
	        'list' => $list,
	        'city_checked_child_array' =>$city_checked_child_array,
	        'area' => $area,
	    );
	    
	    $this->load->view('seller/area',$result);
	}
	
	public function save_area()
	{
	    if ($this->input->post())
	    {
	        $county = $this->input->post('county');
	        $city = $this->input->post('city');
	        $province = $this->input->post('province');
	        $area = 'province|';
	        if (!empty($province))
	        {
	            foreach ($province as $key => $value)
	            {
	                $area .= $value.',';
	            }
	        }
	        $area .= '|city|';
	        if (!empty($city))
	        {
	            foreach ($city as $key => $value)
	            {
	                $area .= $value.',';
	            }
	        }
	        $area .= '|county|';
	        if (!empty($county))
	        {
	            $area .= $county;
	        }
	        $data['area_id'] = $area;
	         
	        $where['shop_id'] = 1;
	        $this->Deliver_model->update_by_where($where,$data);
	        redirect(SELLER_SITE_URL.'/shop_transport');
	    }    
	}
	

}