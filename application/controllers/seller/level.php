<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Level extends BaseSellerController {

	public function __construct()
    {
        
        parent::__construct();
        $this->load->model(array('sys/Level_model','sys/Level_def_model','oil/Company_config_model'));
    }

    public function index(){
		$sellerInfo = $this->getSellerInfo();
		$company_id = $sellerInfo['company_id'];

		$list = $this->Level_model->get_list(array('company_id'=>$company_id),'*','level_id');
		$info = $this->Company_config_model->get_by_id($company_id);

        $result = array(
            'list' => $list,
            'info' => $info,
        );
		$this->load->view('seller/sys/level',$result);
    }

    public function save(){
    	$sellerInfo = $this->getSellerInfo();
		$company_id = $sellerInfo['company_id'];
		$level_day = $this->input->post('level_day');
		$level_day = !empty($level_day)?$level_day:30;


		$this->Company_config_model->update_by_id($company_id, array('level_day'=>$level_day));

		redirect(SELLER_SITE_URL.'/level');
    }

    public function ajax(){
		$branch = $this->input->get('branch');
		$column = $this->input->get('column');

		$sellerInfo = $this->getSellerInfo();

		if(!empty($column) && !in_array($column, array('level_name','integral_num','next_msg')))
			exit('false');

		switch ($branch) {
			case 'check_level_name':
				//编辑时判断是否重复
				$name = $this->input->get('name');
				$id = $this->input->get('id');
				$where = array('name'=>$name,
        			'status <>'=>-1,
        			'id<>'=>$id,
        		);
            
	            $info = $this->Level_model->get_by_where($where);
	            
	            if (empty($info))
	                exit('true');
	        	else
	        		exit('false');
				break;
			case 'level_name':
			case 'integral_num':
			case 'next_msg':
				$id = $this->input->get('id');
				$value = $this->input->get('value');
				$data = array($column=>$value);



				$this->Level_model->update_by_where(array('level_id'=>$id,'company_id'=>$sellerInfo['company_id']), $data);
				//echo $this->Level_model->db->last_query();
				//die;
				exit('true');
				break;

			default:
				break;
		}

	}

}