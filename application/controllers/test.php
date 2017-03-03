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
    $this->load->service('printapi_service');

    $aab = $this->printapi_service->orderprint_internal_push(275);
    echo $aab.'dfdf';
  }


}
