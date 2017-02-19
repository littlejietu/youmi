<?php
/**
* 统计service
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Stat_service
{

	public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('User_num_model');
	}


	

}