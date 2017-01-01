<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Net extends BaseSellerController {
	
    public function __construct()
	{
	    parent::__construct();
	    $this->load->model('oil/Net_model');
	}
	
	public function index()
	{
		$this->lang->load('admin_layout');
		$this->lang->load('admin_article_class');

		$sellerInfo = $this->getSellerInfo();
		$list = $this->Net_model->getTreeList($sellerInfo['company_id'],'',2);

        $result = array(
            'list' => $list,
        );
		$this->load->view('seller/oil/net',$result);
	}
	
	public function add()
	{
	    $this->lang->load('admin_layout');
	    $this->lang->load('admin_article_class');

	    $id = $this->input->get('id');
	    $parent_id = $this->input->get('parent_id');

	    $sellerInfo = $this->getSellerInfo();
	    $info = array();
	    if(!empty($id))
	    	$info = $this->Net_model->get_by_id($id);
	    $list = $this->Net_model->getTreeList($sellerInfo['company_id'], '&nbsp;&nbsp;');

	    $result = array(
	        'parent_id' => $parent_id,
	        'list' => $list,
	        'info' => $info,
	    );
	    $this->load->view('seller/oil/net_add',$result);
	}
	
	public function edit()
	{
	    $this->lang->load('admin_layout');
	    $this->lang->load('admin_article_class');
	    if ($this->input->post())
	    {
	        $config = array(
	            array(
	                'field'   => 'ac_id',
	                'label'   => '分类id',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'ac_name',
	                'label'   => '分类名称',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'ac_sort',
	                'label'   => '分类排序',
	                'rules'   => 'trim|required'
	            ),
	        );
	        
	        $this->form_validation->set_rules($config);
	        if ($this->form_validation->run() === TRUE)
	        {
	            $id = $this->input->post('ac_id');
	            $data['name'] = $this->input->post('ac_name');
	            $data['sort'] = $this->input->post('ac_sort');
	        }
	        if ($this->Article_Class_model->update_by_id($id,$data))
	        {
	            redirect(ADMIN_SITE_URL.'/article_class');
	        }
	        var_dump($data);exit;
	    }
	    
	    if ($this->input->get('id'))
	    {
	        $id = $this->input->get('id');
	        $info = $this->Article_Class_model->get_by_id($id);
	        $result =array(
	            'info' => $info
	        );
	    }
	    
	    $this->load->view('admin/article_class_edit',$result);
	}

	public  function del()
	{
	    if ($this->input->get('id'))
	    {
	        $id = $this->input->get('id');
	        $data['status'] = -1;
	        $this->Net_model->update_by_id($id,$data);

	        $this->Net_model->update_by_where(array('parent_id'=>$id), $data);
	    }
	    if ($this->input->post())
	    {
	        $ids = $this->input->post('check_id');
	        $data['status'] = -1;
	        foreach ($ids as $key => $value)
	        {
	            $this->Net_model->update_by_id($value,$data);

	            $this->Net_model->update_by_where(array('parent_id'=>$value), $data);
	        }
	    }
	    redirect(SELLER_SITE_URL.'/net');
	}
	
	public function save()
	{
	    if ($this->input->post())
	    {
	        $config = array(
	            array(
	                'field'   => 'name',
	                'label'   => '分类名称',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'sort',
	                'label'   => '分类排序',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'parent_id',
	                'label'   => '父类类id',
	                'rules'   => 'trim|required'
	            ),
	        );
	        
	        $this->form_validation->set_rules($config);
	        if ($this->form_validation->run() === TRUE)
	        {
	        	$sellerInfo = $this->getSellerInfo();

	        	$id = $this->input->post('id');
	        	$parent_id = $this->input->post('parent_id');

	        	if( !empty($id)){
	        		if($id == $parent_id){
		        		showDialog('分类不能设自己为自己的上级', SELLER_SITE_URL.'/net/add?id='.$id);
		    			exit;
	    			}else{
	    				$list = $this->Net_model->getTreeList($sellerInfo['company_id'], '');
	    				$arrChild = $list[$id]['children'];
	    				if(in_array($parent_id, $arrChild)){
	    					showDialog('分类不能将自己的下级设置为自己的上级', SELLER_SITE_URL.'/net/add?id='.$id);
		    				exit;
	    				}

	    			}
	    				
	        	}

	        	$deep = 1;
	        	if(!empty($parent_id)){
	        		$infoParent = $this->Net_model->get_by_id($parent_id);
	        		if(!empty($infoParent))
	        			$deep = $infoParent['deep'] +1;
	        	}

	            $data = array(
	                'name' => $this->input->post('name'),
	                'parent_id' => $parent_id,
	                'sort' => $this->input->post('sort'),
	                'company_id' => $sellerInfo['company_id'],
	                'deep' => $deep,
	                'status' => 1,
	            );

	            if(empty($id))
	            	$this->Net_model->insert($data);
	            else
	            	$this->Net_model->update_by_id($id, $data);
	            
	            redirect(SELLER_SITE_URL.'/net');
	            
	        }
	    }
	}
	
	public function ajax()
	{
		$branch = $this->input->get('branch');
		$column = $this->input->get('column');

		if(!empty($column) && !in_array($column, array('name','sort')))
			exit('false');

		switch ($branch) {
			case 'check_net_name':
				//编辑时判断是否重复
				$name = $this->input->get('name');
				$id = $this->input->get('id');
				$where = array('name'=>$name,
        			'status <>'=>-1,
        			'id<>'=>$id,
        		);
            
	            $info = $this->Net_model->get_by_where($where);
	            
	            if (empty($info))
	                exit('true');
	        	else
	        		exit('false');
				break;
			case 'class_name':
			case 'class_sort':
				$id = $this->input->get('id');
				$value = $this->input->get('value');
				$data = array($column=>$value);
				$this->Net_model->update_by_id($id, $data);
				echo $this->Net_model->db->last_query();
				die;
				exit('true');
				break;

			default:
				$id = $this->input->get('id');
			    //获得所选分类的子分类列表
			    $listWhere = array('parent_id'=>$id,
			    	'status <>'=>-1,
			    	);
			    $list = $this->Net_model->get_list($listWhere);
			    foreach ($list as $key => $value) {
			    	$childList = $this->Net_model->get_list(array('parent_id'=>$value['id'],'status'=>1));
			    	$value['have_child'] = count($childList);

			    	$list[$key] = $value;
			    }
			    
			    echo json_encode($list);
			    exit;
				break;
		}

	}
}
