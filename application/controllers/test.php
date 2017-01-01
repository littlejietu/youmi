<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
    {
      parent::__construct();
    }


	public function index() {
       $redirect_uri = $this->input->get('redirect_uri').'&code=1';
       echo "<a href='$redirect_uri'>同意</a>";

    }

    public function aa(){

      $this->load->library('Testt');
      $obj = new Testt();
      $obj->init(11,12);
      $obj->abab();

      $obj = new Testt();
      $obj->init(12,13);
      $obj->abab();
      /*
      $this->load->library('WeixinPayMicro');
      $obj = new WeixinPayMicro();
      $ab = $obj->cancel(1230);
    	echo $ab;
    	die;
      */
    }


}
