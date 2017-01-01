<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxreply extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

	}

	function txt_add(){

		$data = array();

		$this->load->view('seller/wx/txt_add',$data);

	}

	function imgtxt_add(){

		$data = array();

		$this->load->view('seller/wx/imgtxt_add',$data);

	}

	function txt_save(){
		print_r($_POST);
		die;
	}

	



}