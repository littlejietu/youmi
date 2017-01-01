<?php
/**
* 统计service
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Usernum_service
{
	public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('User_num_model');

		
	}

	public function onCollect(){
		
	}

	public function cleanNum($user_id, $field){
		if(in_array($field, array('un_read_num'))){
			$this->ci->User_num_model->update_by_id($user_id, array($field=>0));
		}
	}

	public function onMessage($user_id){
		$prefix = $this->ci->User_num_model->prefix();

		$sql = "update ".$prefix."user_num a set un_read_num=(select count(1) from ".$prefix."inter_message_receiver where receiver_id=a.user_id and is_read=0 and is_del=1) where user_id=$user_id";
		$this->ci->User_num_model->execute($sql);

	}



}