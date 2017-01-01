<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends BaseSellerController {
	
	public function welcome()
	{
		$this->lang->load('admin_dashboard');

		$result = array(
			'output'=>array(
				'html_title'=>'',
				'map_nav' => array(),
				'admin_info' => array('name'=>''),
				'top_nav' => '',
				'left_nav'=>'',

				'statistics'=>array('setup_date'=>'','os'=>'','web_server'=>'','php_version'=>'','sql_version'=>''),
			)
		);

		$this->load->view('seller/dashboard_welcome',$result);
	}

	public function statistics(){

	}
}
