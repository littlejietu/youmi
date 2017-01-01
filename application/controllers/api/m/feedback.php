<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends TokenApiController {
    
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Feedback_model');
    }

    public function add(){
        $user = $this->loginUser;
        $user_id = $user['user_id'];
    	$user_name = $user['user_name'];
        $mobile = $this->input->post('mobile');	
    	$content = $this->input->post('content');
        $platform_id = C('basic_info.PLATFORM_ID');

    	
    	 if ($this->input->is_post()){
    	 	$config = array(
    	 		array(
	                'field'   => 'content',
	                'label'   => '内容',
	                'rules'   => 'trim|required'
	            ),
	            
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
	        {
	           
	            $this->load->model('Feedback_model');
	            $data = array(
	                'user_id'=>$user_id,
                    'user_name'=>$user_name,
	                'mobile'=>$mobile,
	                'content'=>$content,
	                'addtime'=>time(),
                    'platform_id'=>$platform_id,
                    'status'=>1,
	                
	            );		
					
	            //保存至数据库
	            $this->Feedback_model->insert($data);

                output_data();exit;
// 	            $result = array('data'=>null,
// 				    'code'=>'SUCCESS',
// 				    'message'=>'操作成功'
// 				);
// 			    echo json_encode($result);
	        }
	        else
	        {
	            output_error(-1,'FAILED');exit;
// 	        	$result = array('data'=>null,
// 				    'code'=>'FAIL',
// 				    'message'=>'数据不全'
// 				);
// 			    echo json_encode($result);
	        }
    	 }
    }

     public function del(){
        $token = $this->input->post('token');
        $id = $this->input->post('id');
        
        $data = array('status'=>-1);
        $this->Feedback_model->update_by_id($id,$data);
            
            
    }



    

    

}