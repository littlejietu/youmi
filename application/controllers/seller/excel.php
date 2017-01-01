<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel extends BaseSellerController {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->service('excel_service');
        $this->load->model('Order_model');
    }
	
    
    /**
     * 导出店铺报表
     */
	public function export_by_shop()
	{
	    $this->lang->load('admin_layout');
	    $this->lang->load('admin_excel');
	    
	    $result = array(
	        'output'    => array('loginUser'=>$this->loginUser),
	    );
	    $this->load->view('seller/excel_shop',$result);
	}
	
	
	/**
	 * 生成(下载)店铺报表文件
	 */
	public function export_excel_shop()
	{
	    $field_name = $this->input->post('field_name')?$this->input->post('field_name'):'data';
	    $start_time = strtotime($this->input->post('start_time').'00:00:00');
	    $end_time = strtotime($this->input->post('end_time').'23:59:59');
	    $user = $this->loginUser;
	    $seller_username = $user['user_name'];
	    $action = $this->input->post('action');
	    
	    $data = array();
	    $title = array();
	    $title = array('商品名','规格','成本价','销售价','销量','总成本价','总销售额','','店铺总成本','店铺总销售额','起始时间','结束时间');
	    $data = $this->excel_service->get_excel_datas_by_shop($seller_username,$start_time,$end_time);
	    
	    $this->excel_service->push_to_excel($data,$field_name,$start_time,$end_time,$title,$action);
	}
	
	
	/**
	 * 查询用户名是否重复
	 */
	function repeat_seller_username(){
	
	    $this->load->model('User_model');
	
	    $user_id 		 = $this->input->get('user_id');
	    $seller_username = $this->input->get('seller_username');
	
	    if(!empty($user_id)) {
	        $userInfo = $this->User_model->get_by_where('user_id !='.$user_id.' and user_name = "'.$seller_username.'"');
	        if(!empty($userInfo)){
	            echo 'true';
	            exit;
	        }
	    }else{
	        $userInfo = $this->User_model->get_by_where('user_name = "'.$seller_username.'"');
	        if(!empty($userInfo)){
	            echo 'true';
	            exit;
	        }
	    }
	    echo 'false';
	    exit;
	
	}
	
	
}