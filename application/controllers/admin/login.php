<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {

		parent::__construct();
	}

	public function index() {
		$this->lang->load('admin_login');
		$this->load->view('admin/login');
	}

	public function login() {

		$this->load->model('Admin_model');

		if($this->input->post()) {

			$user_name 	= $this->input->post('user_name');
			$password 	=$this->input->post('password');
			$captcha	= $this->input->post('captcha');

			#region CI自带验证

			$config = array(
					array(
						'field'=>'user_name',
						'label'=>'用户名',
						'rules'=>'trim|required',
					),
					array(
						'field'=>'password',
						'label'=>'密码',
						'rules'=>'trim|required',
					),
					array(
						'field'=>'captcha',
						'label'=>'密码',
						'rules'=>'trim|required',
					)
			);

			$this->form_validation->set_rules($config);
			#endregion

			$this->load->helper('captcha');
			$this->load->library('encrypt');


			//验证验证码是否真正确
			if(!check_captcha($captcha,'verify')){
				$arrRes['code'] = '-1';
				$arrRes['msg'] = 'CODE_ERROR';
				showMessage('验证码不正确！','/admin/login');
				exit;
			}

			//查询商家表信息
			$adminInfo =	$this->Admin_model->get_by_where("name='$user_name'");


			//判断用户名是否正确
			if(empty($adminInfo)) {
				$arrRes['code'] = '-1';
				$arrRes['msg'] = 'SHOP_ERROR';
				showMessage('用户名不存在！','/admin/login');
				exit;
			}
	
			if($adminInfo['password'] !=  md5(trim($password))){
				$arrRes['code'] = '-1';
				$arrRes['msg'] = 'PWD_ERROR';
				showMessage('用户名或密码错误！','/admin/login');
				exit;
			}

			//添加会员登录次数
			$this->Admin_model->update_by_id($adminInfo['id'],array('login_num' => intval($adminInfo['login_num']+1),'login_time'=>time() ));

			//$this->systemSetKey();
			$this->load->library('encrypt');
			$this->load->library('session');
			$user = array('admin_name'=>$adminInfo['name'], 'admin_id'=>$adminInfo['id'],'role_id'=>$adminInfo['role_id'],'is_super'=>$adminInfo['is_super']);
			//$this->session('sys_key',$this->encrypt->encode(serialize($user),C('basic_info.MD5_KEY')),36000);
			$this->session->set_userdata('sys_key',$this->encrypt->encode(serialize($user),C('basic_info.MD5_KEY')),36000);
			// set_cookie('admin_id',$adminInfo['id'],3600);
			// set_cookie('admin_name',$adminInfo['name'],3600);
			// set_cookie('is_super',$adminInfo['is_super'],3600);
			// set_cookie('role_id',$adminInfo['role_id'],3600);
			//print_r(get_cookie('sys_key') );die;
			redirect(ADMIN_SITE_URL);
		}
		exit;
	}

	public function logout(){
		$this->session->set_userdata('sys_key',null,-1);
		redirect(ADMIN_SITE_URL);
	}
}
