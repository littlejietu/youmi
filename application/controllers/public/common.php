<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Common extends CI_Controller {

	/**
	 *前台验证码
	 */
	public function captcha(){
		$this->load->helper('captcha');
		create_captcha(4,90,26,'verify');
	}

	public function captcha_seller(){
		$this->load->helper('captcha');
		create_captcha(4,90,26,'verify_seller');
	}
}