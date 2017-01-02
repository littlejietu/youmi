<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Menu extends BaseSellerController {

	public function add(){

		$result = array(
        );

		$this->load->view('seller/modifypw',$result);
	}

}