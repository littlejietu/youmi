<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
      parent::__construct();
    }


	public function index($site_id=1) {
       
       redirect(BASE_SITE_URL.'/api/wxauth/go?url='.BASE_SITE_URL.'/wap/?site_id='.$site_id);

    }
/*
    public function test(){
    	 
    	$this->load->service('printapi_service');
    	echo $this->printapi_service->orderprint_internal_push(38);
    }
*/

}
