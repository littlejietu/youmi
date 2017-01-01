<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Favorite extends TokenApiController {
    
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Favorite_model');
    }

    public function add(){
    	$item_id = $this->input->post('item_id');

        $user_id = $this->loginUser['user_id'];
        //$user_name = $this->loginUser['user_name'];

    	$item_type = $this->input->post('item_type');
    	$platform_id = C('basic_info.PLATFORM_ID');
    	if(!$item_type)
    		$item_type = 1;

    	 if ($this->input->is_post()){
    	 	$config = array(
    	 		array(
	                'field'   => 'item_id',
	                'label'   => '项类型 ',
	                'rules'   => 'trim|required'
	            ),
	            
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
	        {
                $this->load->model('Favorite_model');
                $aFav = $this->Favorite_model->get_by_where(array('user_id'=>$user_id, 'item_type'=>$item_type, 'item_id'=>$item_id,'status'=>1));
	            if(empty($aFav)){
                    $data = array(
                        'user_id'=>$user_id,
                        'item_type'=>$item_type,
                        'item_id'=>$item_id,
                        'addtime'=>time(),
                        'platform_id'=>$platform_id,
                        'status'=>1,
                    );      
                        
                    //保存至数据库
                    $this->Favorite_model->insert($data);
                }
                output_data();
                exit;	            
	        }
	        else
	        {
	        	output_error(-1,'FAILED');exit;
	        }
    	 }
    }



    public function index(){
    	$token = $this->input->post('token');
    	$page = $this->input->post('page');
    	$pagesize = $this->input->post('pagesize');
        $type = $this->input->post('type');
        
    	$user_id = $this->loginUser['user_id'];
    	
        if(!$page)
            $page = 1;
        if(!$pagesize)
            $pagesize = 10;
        
    	$this->load->helper('goods');
    	$this->load->model('Goods_model');

        $prefix = $this->Favorite_model->prefix();
        //清除不存在收藏数据
        $this->Favorite_model->update_by_where('item_id in(select id from '.$prefix.'goods where status<>1) and item_type=1 and status<>-1',array('status'=>-1));
        //echo $this->Favorite_model->db->last_query();die;

    	$arrWhere = array('user_id'=>$user_id,'platform_id'=>C('basic_info.PLATFORM_ID'),'item_type'=>1, 'status'=>1);
    	$aList = $this->Favorite_model->fetch_page($page, $pagesize, $arrWhere,'*','addtime desc');
    	//echo $this->Favorite_model->db->last_query();die;
    	foreach ($aList['rows'] as $key => $a) {
    		$aItem = $aList['rows'][$key];
    		//商品信息
    		$aGoods = $this->Goods_model->get_by_id($a['item_id'],'id as goods_id,tpl_id,title,pic_path,price,market_price');

    		if(!empty($aGoods))
    		{
    			$aGoods['pic_path'] = cthumb($aGoods['pic_path']);
    			$aItem = array_merge($aGoods, $aItem);

    			$aList['rows'][$key] = $aItem;
    		}
    		//else
    			//unset($aList['rows'][$key]);
    	}
    	$aList['page']=$page;
    	$aList['pagesize']=$pagesize;

        output_data($aList);
    }

    public function del(){
    	$token = $this->input->post('token');
	 	$ids = $this->input->post('ids');
        $arrIds = explode(',', $ids);

	 	$data = array('status'=>-1);
    	$this->Favorite_model->update_by_where(array('item_id'=>$arrIds, 'item_type'=>1),$data);
	    	
		output_data();
    }

}