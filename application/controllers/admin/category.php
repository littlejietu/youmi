<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Admin_Controller {
	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
    }
    
    /**
     * 
     */
	public function index()
	{
		$this->lang->load('admin_layout');
		$this->lang->load('admin_prd_category');
		$list = array(array());
		$res = $this->Category_model->get_list($arrWhere=array(),'*',$strorder='sort desc');
		foreach ($res as $key=>$value)
		{
		    $list[$value['parent_id']][]=$value;
		}
        foreach ($list[0] as $key => $value)
        {
            if (isset($list[$value['id']]))
            {
                $list[0][$key]['have_child'] = 1;
            }
            else 
            {
                $list[0][$key]['have_child'] = 0;
            }
        }
		$result = array(
		    'list' => $list[0],
		);
		$this->load->view('admin/category',$result);
	}
	
	public function add()
	{
	    $this->lang->load('admin_layout');
	    $this->lang->load('admin_prd_category');
	    
	    $this->load->model('Spu_model');
	    $this->load->model('Spec_model');
	    $this->load->model('Spec_name_model');
	    $this->load->model('Word_model');

	    $res = $this->Category_model->get_list();
	    
	    $id = $this->input->get('id');
	    $parent_id = $this->input->get('parent_id');

	    $info = array();
	    if ($id)
	    {
	        $info = $this->Category_model->get_by_id($id);
	        //spu attr & val
	        $info['spu'] = $this->Spu_model->getDefaultSpu($id);
	        if(!empty($info['spu']))
	        {
	        	$spu_code = $info['spu']['spu_code'];
	        	$info['spu_attr_val'] = $this->Spu_model->getAttrValBySpuCode($spu_code);
	        }
	        //spec attr & val
	        $info['spec_attr_val'] = $this->Spec_model->getSpecVal($id);
	    }

	    //spec_attr/spec_name
	    $spec_list = $this->Spec_name_model->get_list(array('status'=>1));
	    foreach ($spec_list as $key => $a) {
	    	$a['name'] = $this->Word_model->getName($a['name_id']);
	    	$spec_list[$key] = $a;
	    }
	    $type = array(array());
	    foreach ($res as $key => $value)
	    {
	        $type[$value['parent_id']][] = $value;
	    }

	    $result = array(
	        'type' => $type,
	        'info' => $info,
	        'spec_list' => $spec_list,
	    );
	    if ($parent_id)
	    {
	        $result['parent_id'] = $parent_id;
	    }
	    $this->load->view('admin/category_add',$result);
	}
	
	public function save()
	{
// 	    var_dump($_POST);exit;
		$gc_name = $this->input->post('gc_name');
		$gc_parent_id = $this->input->post('gc_parent_id');
		$gc_sort = $this->input->post('gc_sort');
		$id = $this->input->post('edit_id');

		$this->load->model('Spu_model');
		$this->load->model('Spec_model');
		
		if($id==$gc_parent_id){
	    	showDialog('分类不能设自己为自己的上级');
	    	exit;
	    }
	    if (!empty($id))
	    {
	        $aCategory = $this->Category_model->get_by_id($gc_parent_id);
	        if ($aCategory['parent_id'] == $id)
	        {
	            showDialog('分类不能将自己的下级设置为自己的上级');
	            exit;
	        }
	    }

	    if ($this->input->post())
	    {
	        $config = array(
	            array(
	                'field'   => 'gc_name',
	                'label'   => '类目名称',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'gc_parent_id',
	                'label'   => '上级分类id',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'gc_sort',
	                'label'   => '排序',
	                'rules'   => 'trim|required'
	            ),
	        );
	        
	        $this->form_validation->set_rules($config);
	        if ($this->form_validation->run() === TRUE)
	        {
	        	$bMake = true;
	        	$data = array('name'=>$gc_name, 'parent_id'=>$gc_parent_id, 'sort'=>$gc_sort);

	            
		        if ($id){
		        	$aCategory = $this->Category_model->get_by_id($id);
		        	if(!empty($aCategory) && $aCategory['name']==$gc_name)
		        		$bMake = false;

		            $this->Category_model->update_by_id($id,$data);
		        }
		        else
		            $id = $this->Category_model->insert_string($data);

		        //处理spu
		        $arrSpu = $this->input->post('spu');
				$this->Spu_model->save($id, $arrSpu);

				//处理spec
				$arrSpec = $this->input->post('spec');
				if(!$arrSpec)$arrSpec=array();
				$this->Spec_model->save($id, $arrSpec);
		        
		        if($bMake)
					$this->Category_model->make_js_file();
	        }
			//showDialog('操作成功',ADMIN_SITE_URL.'/category/');
	        redirect(ADMIN_SITE_URL.'/category/');
	    }
	}

	public function ajax_spu(){
		$spu_id = $this->input->post('spu_id');
		$name_id = $this->input->post('name_id');
		$spu_code = $this->input->post('spu_code');

		$this->load->model('Spu_model');
		$this->Spu_model->delData($spu_id, $spu_code, $name_id);
		echo 'true';exit;
	}

	public function ajax_spec(){
		$cid = $this->input->post('cid');
		$name_id = $this->input->post('name_id');
		$this->load->model('Spec_model');
		$this->load->model('Goods_tpl_spec_attr_val_model');

		$bResult = $this->Goods_tpl_spec_attr_val_model->inNotUse($cid, $name_id);
		if($bResult){
			$this->Spec_model->delData($cid, $name_id);
			
		}
		echo $bResult?'true':'false';exit;
	}
	
	/**
	 * 删除
	 */
	public function del()
	{
	    if ($this->input->get('id'))
	    {
	        $id = $this->input->get('id');
	    }
	    
	    if ($this->input->post('check_gc_id'))
	    {
	        $id = $this->input->post('check_gc_id');
	    }
	    
	    if (isset($id))
	    {
	       $child_category = $this->Category_model->getListByParentId($id);
	       if (!empty($child_category))
	       {
	           showDialog('该分类下有子分类，无法删除');
	       }
	       $arrWhere['category_id'] = $id;
	       $this->load->model('Goods_model');
	       $goods = $this->Goods_model->get_list($arrWhere,'*','id',$limit=1);
	       if (!empty($goods))
	       {
	           showDialog('该分类下有商品，无法删除');
	       }
	       $this->Category_model->delete_by_id($id);
	       redirect(ADMIN_SITE_URL.'/category/'); 
	    }
	}
	
	/**
	 * ajax获取下级分类信息
	 * @parent_id  当前分类id
	 */
	public function ajax()
	{
	    $parent_id = $this->input->get('parent_id');
	    
	    $result = $this->Category_model->get_list($arrWhere=array(),'*',$orderby = 'sort desc');
	    $res = array(array());
	    foreach ($result as $key => $value)
	    {
	        $res[$value['id']] = $value;
	    }
	    $arr = array(array());
	    foreach ($result as $key=>$value)
	    {
	        $arr[$value['parent_id']][] = $value; 
	    }
	    foreach ($arr[$parent_id] as $key =>$value)
	    {
	        if (isset($arr[$value['id']]))
	        {
	            $arr[$parent_id][$key]['have_child'] = 1;
	        }
	        else 
	        {
	            $arr[$parent_id][$key]['have_child'] = 0;
	        }
	        if ($value['parent_id'] == 0)
	        {
	            $arr[$parent_id][$key]['deep'] = 1;
	        }
	        if ($res[$value['parent_id']]['parent_id'] == 0)
	        {
	            $arr[$parent_id][$key]['deep'] = 2;
	        }
	        else 
	        {
	            $arr[$parent_id][$key]['deep'] = 3;
	        }

	    }
	    
	    $str = json_encode($arr[$parent_id]);
	    echo $str;exit;
	}
	
	
	/**
	 * ajax判断新建分类是否重名
	 */
	public function ajax_check_name()
	{
	    $id = $this->input->get('id');
	    $where['name'] = $this->input->get('gc_name');
	    $where['parent_id'] = $this->input->get('gc_parent_id');
	    $res = $this->Category_model->get_list($where);
	    if (!empty($id))
	    {
	        $info = $this->Category_model->get_by_id($id);
	        if ($info['name'] == $where['name'])
	        {
	            exit('true');
	        }
	    }
	    if (empty($res[0]))
	    {
	        exit('true');
	    }
	    else
	    {
	        exit('false');
	    }
	}
}
