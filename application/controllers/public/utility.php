<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Utility extends CI_Controller {


	public function emoji(){
		$this->load->view('public/utility/emoji.html');
	}

	public function link(){
		
		$this->load->view('public/utility/link.html');


	}
}