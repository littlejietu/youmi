<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends MY_Admin_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sys/Wordbook_model');
    }
	
	public function index()
	{
	    
		$this->lang->load('admin_setting');
		$this->lang->load('admin_layout');
        
		$result = $this->Wordbook_model->get_list();
		$list = array();
		foreach ($result as $key=>$value)
		{
		    $list[$value['k']] = $value['val'];
		}
		$result = array(
		    'list'=>$list,
		);

		$this->load->view('admin/operation',$result);
	}
	
	public function save()
	{
	    if ($this->input->is_post())
	    {
	        $data = array(
	            'site_name'=>$this->input->post('site_name'),
	            'component_appid'=>$this->input->post('component_appid'),
	           	'component_appsecret'=>$this->input->post('component_appsecret'),
	           	'component_message_token'=>$this->input->post('component_message_token'),
	           	'component_message_key'=>$this->input->post('component_message_key'),
	        );
	        $this->Wordbook_model->updateSetting($data);
	            
	        redirect(ADMIN_SITE_URL.'/operation');
	        
	    }
	}
}
