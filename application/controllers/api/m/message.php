<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends TokenApiController {
    
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Message_model');
    }

     public function index(){
    	// $page = $this->input->post('page');
    	// $pagesize = $this->input->post('pagesize');
        $page = 1;
        $pagesize = 10000;
        //$type = $this->input->post('type');
   	    $user_id = $this->loginUser['user_id'];

    	$this->load->helper('goods');
    	$this->load->model('Message_receiver_model');


    	$arrWhere = array('receiver_id'=>$user_id,'is_del'=>1,'is_read'=>0);
        $dbprefix = $this->Message_receiver_model->prefix();
        $tb = $dbprefix.'inter_message_receiver a left join '.$dbprefix.'inter_message b on(a.message_id=b.id)';
    	$aList = $this->Message_receiver_model->fetch_page($page, $pagesize, $arrWhere,'a.id, title,content,send_time,type_id,action_title,web_url,app_url,addtime','addtime desc',$tb);
    	//echo $this->Message_receiver_model->db->last_query();die;
        $aList['page']=$page;
    	$aList['pagesize']=$pagesize;

        //修改所有消息状态为已读--已读未读，客户端控制
        $this->Message_receiver_model->update_by_where(array('receiver_id'=>$user_id),array('is_read'=>1,'read_time'=>time()));

        $this->load->service('usernum_service');
        $this->usernum_service->cleanNum($user_id,'un_read_num');

        output_data($aList);
    }

    public function unread(){
        $user_id = $this->loginUser['user_id'];
        $this->load->model('User_num_model');
        $aUsernum = $this->User_num_model->get_by_id($user_id);
        $un_read_num = 0;
        if(!empty($aUsernum))
            $un_read_num = $aUsernum['un_read_num'];

        output_data(array('un_read_num'=>$un_read_num) );
    }

    /*public function del(){
        $token = $this->input->post('token');
        $id = $this->input->post('id');

        $data = array('status'=>-1);
        $this->Message_model->update_by_id($id,$data);
            
        output_data();
    }*/

    public function readed(){
        $token = $this->input->post('token');
        $id = $this->input->post('id');

        $data = array('is_read'=>1);
        $this->load->model('Message_receiver_model');
        $this->Message_receiver_model->update_by_id($id,$data);
            
        output_data();
    }



    

    

}