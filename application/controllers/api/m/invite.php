<?php
/**
 * 购物车接口
 * @date: 2016年3月16日 下午2:59:01
 * @author: hbb
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Invite extends TokenApiController {

	public function __construct() {
		parent::__construct();
		$this->load->service('invite_service');
	}

	/**
	 * 邀请排行榜
	 * @date: 2016年3月16日 下午2:59:57
	 * @author: hbb
	 * @param: variable
	 * @return:
	 */
	public function billboard() {

	    $invite_action_info = new stdClass();
	    $invite_action_info->title='最给力的购物平台[九号街区]';
	    $invite_action_info->desc='欢迎选购~~';
	    $invite_action_info->url=BASE_SITE_URL.'/api/wxauth/go?url='.BASE_SITE_URL.'/wap/home/index.html&invite_id='.$this->loginUser['user_id'];
	    $invite_action_info->img=BASE_SITE_URL.'/res/front/images/kefu.png';
	    
	    $data['invite_action_info']=$invite_action_info;
		$data['ranking_list']=$this->invite_service->get_ranking();
		output_data($data);
	}
    
	
	
	/**
	 * 我的邀请列表
	 * @date: 2016年3月16日 下午2:59:57
	 * @author: hbb
	 * @param: variable
	 * @return:
	 */
	public function mylist() {
	   $page = $this->input->post('page')>0?(int)$this->input->post('page'):1;
	   $pagesize = $this->input->post('pagesize')?(int)$this->input->post('pagesize'):10;
	   $user_id = $this->input->post('user_id')?$this->input->post('user_id'):$this->loginUser['user_id'];
	   
	   $invites=$this->invite_service->get_my_list($user_id,$page,$pagesize);
	   
	   $data['user_list']=$invites['list'];
	   
	   $data['total'] = $invites['num'];
	   $data['curpage'] = $page;
	   $data['totalpage'] = ceil($invites['num']/$pagesize);
	   output_data($data);
	}
	
	
	
	/**
	* 函数用途描述
	* @date: 2016年3月25日 下午1:36:11
	* @author: hbb
	* @param: variable
	* @return:
	*/
	
	public function test(){
	    $userid=$this->input->post('userid');
	    $inviteid=$this->input->post('inviteid');
	    $this->load->service('invite_service');
	    $this->invite_service->add_invites_record($userid,$inviteid);
	}
	
}
?>