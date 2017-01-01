<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('Message_def_model');
		$this->ci->load->model('Message_model');
        $this->ci->load->model('Message_receiver_model');
        $this->ci->load->model('User_pwd_model');

        $this->ci->load->service('usernum_service');
	}

    

    //站内信($tpl_id,$sender_id,$receiver,$receiver_type,$type_id,$arrParam)
    public function send($sender_id,$receiver,$receiver_type,$title,$message,$type_id,$action_title,$web_url,$app_url){
        $arrReturn = array('code'=>'ENPIY','message'=>'');
        if(empty($receiver))
        {
            $arrReturn['code'] = 'FAIL';
            $arrReturn['message'] = '接受者为空';
            return $arrReturn;
        }

        $data = array(
            'sender_id'=>$sender_id,
            'parent_id'=>0,
            'tpl_id'=>0,
            'receiver'=>$receiver,
            'send_time'=>0,
            'reply_time'=>0,
            'kind'=>1,
            'is_batch'=>1,
            'is_send'=>0,
            'receiver_type'=>$receiver_type,
            'title'=>$title,
            'content'=>$message,
            'type_id'=>$type_id,
            'action_title'=>$action_title,
            'web_url'=>$web_url,
            'app_url'=>$app_url,
            'status'=>1,
        );
        
        //保存至数据库
        $this->ci->Message_model->insert_string($data);
    
        $aReceiver = explode(',',$arrReturn['receiver']);
        foreach ($aReceiver as $key => $v) {
            $tada = array(
                'receiver_id'=>$v,
                'message_id'=>$arrReturn['id'],
                'is_read'=>0,
                'read_time'=>time(),
                'is_del'=>1,
                'push_status'=>0,
                );
            //保存至数据库
            $this->ci->Message_receiver_model->insert_string($tada);
        }
        $this->ci->Message_model->update_by_id($arrReturn['id'],array('is_send'=>1,'send_time'=>time()));
    
        $arrReturn['code'] = 'SUCCESS';
        $arrReturn['message'] = '成功';
        return $arrReturn;
        
    }


    //系统消息
    public function send_sys($tpl_id,$receiver,$receiver_type,$arrParam){
        $sender_id = 0; //系统消息
        $arrReturn = array('code'=>'ENPIY','message'=>'');
        if(empty($receiver))
        {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '接受者为空';
            return $arrReturn;
        }
        if(empty($tpl_id))
        {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '模板id不能为空';
            return $arrReturn;
        }

        $aMessageDef = $this->ci->Message_def_model->get_by_id($tpl_id);
    	if(empty($aMessageDef)){
    		$arrReturn['code'] = 'Failure';
        	$arrReturn['message'] = '消息模板不存在';
        	return $arrReturn;
    	}

    	$title = $aMessageDef['message_title'];
    	$message = $aMessageDef['message_content'];
    	$action_title = $aMessageDef['action_title'];
    	$web_url = $aMessageDef['web_url'];
    	$app_url = $aMessageDef['app_url'];
        $need_push = $aMessageDef['need_push'];
        $type_id = $aMessageDef['type_id'];

    	if( strpos($message,'||')>0 ){
        	$arrMsg = explode('\|\|', $message);
        	if(count($arrMsg)>1){
        		$idx = rand(0,count($arrMsg)-1);
        		$message = $arrMsg[$idx];
        	}
        }

        if(!empty($arrParam)){
        	foreach ($arrParam as $key => $value) {
        		if(!empty($title))
        			$title = str_replace($key, $value, $title);
        		if(!empty($message))
        			$message = str_replace($key, $value, $message);
        		if(!empty($action_title))
        			$action_title = str_replace($key, $value, $action_title);
        		if(!empty($web_url))
        			$web_url = str_replace($key, $value, $web_url);
        		if(!empty($app_url))
        			$app_url = str_replace($key, $value, $app_url);
        	}
        }

        $kind = 0;
        if(in_array($type_id, array(1,2,3)) )
        	$kind = 1;

        $is_batch = 1;
        if($receiver_type==6)
        	$is_batch = 0;

        $data = array(
            'sender_id'=>$sender_id,
            'parent_id'=>0,
            'tpl_id'=>$tpl_id,
            'title'=>$title,
            'content'=>$message,
            'receiver'=>$receiver,
            'send_time'=>0,
            'reply_time'=>0,
            'kind'=>$kind,
            'is_batch'=>$is_batch,
            'is_send'=>0,
            'receiver_type'=>$receiver_type,
            'type_id'=>$type_id,
            'action_title'=>$action_title,
            'web_url'=>$web_url,
            'app_url'=>$app_url,
            'status'=>1,
        );
        //保存至数据库
        $message_id = $this->ci->Message_model->insert_string($data);
        $aReceiver = explode(',',$receiver);
        foreach ($aReceiver as $key => $v) {
            $data_receiver = array(
                'receiver_id'=>$v,
                'message_id'=>$message_id,
                'is_read'=>0,
                'read_time'=>0,
                'is_del'=>1,
                'push_status'=>($need_push==1?1:0),
            );
            $this->ci->Message_receiver_model->insert_string($data_receiver);

            //消息统计
            $this->ci->usernum_service->onMessage($v);
        }
        $this->ci->Message_model->update_by_id($message_id,array('is_send'=>1,'send_time'=>time()));
        
        $arrReturn['code'] = 'SUCCESS';
        $arrReturn['message'] = '成功';
        return $arrReturn;
    }

	
	
  
    
     
    
}