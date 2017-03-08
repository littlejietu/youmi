<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends BaseSellerController {
	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/User_pwd_model');
        $this->load->model('user/User_model');
        $this->load->model('sys/Level_model');
        //$this->load->model('sys/Type_model');
    }
    
	public function index()
	{
		$user_level = $this->input->post_get('user_level');
		$company_id = $this->seller_info['company_id'];
		$level = $this->input->post_get('level');

		$status = $this->input->post_get('status');

		$this->load->model('user/User_num_model');
		$this->load->model('user/User_detail_model');

		$cKey = $this->input->post_get('search_field_value');
		$search_field_name = $this->input->post_get('search_field_name');
		$page     = _get_page();
		$pagesize = 10;	
		$arrParam = array();
		$arrWhere = array();

		if($cKey)
		{
			$arrParam['search_field_name'] = $search_field_name;
			$search_key = 'user_name';
			if($search_field_name==2)
				$search_key = 'nickname';
		    $arrParam['search_field_value'] = $cKey;
		    $arrWhere[$search_key.' like '] = "'%$cKey%'";
		}
		if(!empty($user_level)){
            $arrWhere['user_level']  = $user_level;
            $arrParam['user_level'] = $user_level;
        }
		if(!empty($user_type)){
			$arrParam['user_type'] = $user_type;
			$arrWhere['user_type'] = $user_type;
		}
		if(!empty($level)){
			$arrParam['level'] = $level;
			$arrWhere['level'] = $level;
		}

	

		if(!empty($status)){
			$arrParam['status'] = $status;
			if($status==1)
				$arrWhere['status'] = 1;
			else
				$arrWhere['status'] = 2;
		}

		
		$arrWhere['status <>'] = -1;
		$strOrderBy = 'user_id desc';

		$level_list = array();
        $level_list_all = $this->Level_model->get_list(array('company_id'=>$company_id),'*','level_id');
        foreach ($level_list_all as $k => $v) {
            $level_list[$v['level_id']] = $v;
        }

		$field = 'a.user_id,user_name,nickname,name,mobile,sex,reg_time,member_status,status,birthday,car_no,car_model,invoice_title,member_time,user_level';
	    $dbprefix = $this->User_model->prefix();
	    $tb = $dbprefix.'user a left join '.$dbprefix.'user_detail b on(a.user_id=b.user_id)';
		$user_list = $this->User_model->fetch_page($page, $pagesize, $arrWhere,$field,$strOrderBy,$tb);
        //echo $this->User_model->db->last_query();die;
		foreach ($user_list['rows'] as $key => $value)
		{
			$rs = $user_list['rows'][$key];
			//$rs['logo'] = strstr($value['logo'],'http://')?$value['logo']:(empty($value['logo'])?'':BASE_SITE_URL.'/'.$value['logo']);

			$rs_num = $this->User_num_model->get_by_id($rs['user_id']);
			$rs_num = empty($rs_num)?array():$rs_num;
		    $rs_detail = $this->User_detail_model->get_by_id($rs['user_id']);
		    $rs_detail = empty($rs_detail)?array():$rs_detail;
		    $rs = array_merge($rs, $rs_detail, $rs_num);

		    $user_list['rows'][$key] = $rs;
		}
		
		//分页
		$pagecfg = array();
		$pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/user', $arrParam);
		$pagecfg['total_rows']   = $user_list['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		 
		$this->pagination->initialize($pagecfg);
		$user_list['pages'] = $this->pagination->create_links();
		
		$status = array(1=>'正常',2=>'锁定' );
		$result = array(
		    'user_list' => $user_list,
		    'status' => $status,
		    'cKey'		=> $cKey,
		    'arrParam'=>$arrParam,
		    'level_list'=>$level_list
		);
		$this->load->view('seller/user/user',$result);
	}



	public function edit()
	{


		$user_id = $this->input->get('id');

		$this->load->model('user/User_detail_model');
		$this->load->model('user/User_num_model');

	    if ($this->input->post())
	    {
	        $password = $this->input->post('member_passwd');
	        $user_id = $this->input->post('member_id');
			$status = $this->input->post('memberstate');
			$member_status = $this->input->post('member_status');
			$birthday_time = $this->input->post('birthday_time');
			$require_service = $this->input->post('require_service');
            $require_service = !empty($require_service)?implode(',', $require_service):'';
            $service_product = $this->input->post('service_product');
            $service_product = !empty($service_product)?implode(',', $service_product):'';
	    	

	        $data = array(
	            'name' => $this->input->post('member_truename'),
	            'nickname' => $this->input->post('nickname'),
	            'sex' => $this->input->post('member_sex'),
	            'mobile' => $this->input->post('mobile'),
	            'member_status' => $member_status,
	            'status' => $status,

	        );

	        if (!empty($password))
	        {
	            $this->load->model('User_pwd_model');
				$user_pwd['pwd'] = md5(trim($password));
				$user_pwd['status'] =  $status;
	            $this->User_pwd_model->update_by_id($user_id,$user_pwd);
	        }

	        $data_detail = array(
                'birthday'=>strtotime($birthday_time),
                'car_no' => $this->input->post('car_no'),
                'car_model' => $this->input->post('car_model'),
                'invoice_title' => $this->input->post('invoice_title'),
	        );
	        $this->User_detail_model->update_by_id($user_id, $data_detail);


	        $this->User_model->update_by_id($user_id,$data);

	      

	        $gotoUrl = SELLER_SITE_URL.'/user';
            showDialog(lang('nc_common_op_succ'), $gotoUrl, 'succ');
	    }

	    $where = array('user_id'=>$user_id);
	    $info = $this->User_model->get_by_where($where);
	    $info = empty($info)?array():$info;
	    $tmpInfo2 = $this->User_num_model->get_by_where($where);
	    $tmpInfo2 = empty($tmpInfo2)?array():$tmpInfo2;
	    $tmpInfo = $this->User_detail_model->get_by_where($where);
	    $tmpInfo = empty($tmpInfo)?array():$tmpInfo;

	    $info = array_merge($info, $tmpInfo2, $tmpInfo);
	    
	    $result = array(
	        'info' => $info,

	    );
	    $this->load->view('seller/user/user_edit',$result);
	}
	
	/*
	public function save()
	{
		$user_id = $this->input->post('member_id');

		$this->load->model('User_pwd_model');

	    if ($this->input->post())
	    {
	        $config = array(
	            array(
	                'field'   => 'member_name',
	                'label'   => '会员名称',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'member_passwd',
	                'label'   => '会员密码',
	                'rules'   => 'trim|required'
	            ),
	            array(
	                'field'   => 'member_tel',
	                'label'   => '电子邮箱',
	                'rules'   => 'trim|required'
	            ),
	        );
	    }
	    
	    $this->form_validation->set_rules($config);
	    if ($this->form_validation->run() === TRUE)
	    {

	    	$member_passwd = $this->input->post('member_passwd');
	    	$memberstate = $this->input->post('memberstate');
	    	$auth_2 = $this->input->post('auth_2');
	    	
	    	if(!empty($member_passwd)){
		        $user_pwd_data = array(
		            'pwd' => md5(trim($this->input->post('member_passwd'))),
		            'status' => $memberstate,
		        );

		        $this->User_pwd_model->update_by_id($user_id, $user_pwd_data);
		    }

	       
	            $this->load->model('user/User_num_model');
	            $user_data = array(
	                'name' =>$this->input->post('member_truename'),
	                'mobile' =>$this->input->post('member_tel'),
	                'logo' => $this->input->post('img'),
	                'sex' => $this->input->post('member_sex'),
	                'auth_2' => $auth_2,
	                
	                'reg_time' => time(),
	                'reg_ip' => $_SERVER["REMOTE_ADDR"],
	                'update_time' => time(),
	                'status' => $memberstate,
	                
	            );

	            $this->User_model->update_by_id($user_id,$user_data);

	            $gotoUrl = ADMIN_SITE_URL.'/user';
                showDialog(lang('nc_common_op_succ'), $gotoUrl, 'succ');
				exit;
	            
	       
	    }
	}
	*/

	public function del()
	{
	    $id = $this->input->post('del_id');
	    if ($id)
	    {
	        foreach ($id as $value)
	        {
	            $data['status'] = -1;
	            $data['user_name']=$value;
	            $this->User_pwd_model->update_by_id($value,$data);
	            $data2['mobile']=$value;
	            $data2['mobile_verify']=0;
	            $data2['status'] = -1;
	            $data2['user_name'] = $value;
	            $this->User_model->update_by_id($value,$data2);
	            //echo $this->User_model->db->last_query();die;
	        }
	    }
	    redirect(SELLER_SITE_URL.'/user');
	}

	
	public function ajax_check_name()
	{
	    $user_name = $this->input->get('user_name');
	    if (!empty($user_name))
	    {
	        $this->load->model('User_pwd_model');
	        $where['user_name'] = "'$user_name'";
	        $res = $this->User_pwd_model->get_by_where($where);
	        if (!empty($res))
	        {
	            exit('false');
	        }
	        else 
	        {
	            exit('true');
	        }
	    }
	    else 
	    {
	        exit('false');
	    }
	}
}
