<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {

		parent::__construct();
	}

	public function index() {
		$this->lang->load('admin_login');
		$this->load->view('seller/login');
	}

	public function login() {

		$this->load->model('oil/O_admin_model');

		if($this->input->post()) {

			$user_name 	= $this->input->post('user_name');
			$password 	=$this->input->post('pwd');
			$captcha	= $this->input->post('captcha');

			#region CI自带验证
			$config = array(
					array(
						'field'=>'user_name',
						'label'=>'用户名',
						'rules'=>'trim|required',
					),
					array(
						'field'=>'pwd',
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
			if(!check_captcha($captcha,'verify_seller')){
				showMessage('验证码不正确',SELLER_SITE_URL.'/login');
				exit;
			}

			$arrRes = array('code'=>'SUCCESS','msg'=>'','jumpUrl'=>SELLER_SITE_URL.'/login');

			//查询商家表信息
			$adminInfo =	$this->O_admin_model->get_by_where("username='$user_name'");

			//判断用户名是否正确
			if(empty($adminInfo)) {
				$arrRes['code'] = 'USER_PWD_ERR';
				$arrRes['msg'] = '用户名不存在！';
			}elseif($adminInfo['pwd'] !=  md5(trim($password))){
				$arrRes['code'] = 'PWD_ERROR';
				$arrRes['msg'] = '用户名或密码错误！';
			}

			if($arrRes['code']!='SUCCESS'){
				showMessage($arrRes['msg'],$arrRes['jumpUrl']);
				exit;
			}

			//会员登录次数+1
			$this->O_admin_model->update_by_id($adminInfo['id'],array('login_num' => intval($adminInfo['login_num']+1),'login_time'=>time() ));

			//$this->systemSetKey();
			$this->load->library('encrypt');
			$this->load->library('session');
			$user = array('admin_username'=>$adminInfo['username'], 'admin_name'=>$adminInfo['name'], 'admin_id'=>$adminInfo['id'],'company_id'=>$adminInfo['company_id'],'site_ids'=>$adminInfo['site_ids'], 'role_id'=>$adminInfo['role_id'],'is_super'=>$adminInfo['is_super']);
			//$this->session('sys_key',$this->encrypt->encode(serialize($user),C('basic_info.MD5_KEY')),36000);
			$this->session->set_userdata('seller_key',$this->encrypt->encode(serialize($user),C('basic_info.MD5_KEY')),36000);
			// set_cookie('admin_id',$adminInfo['id'],3600);
			// set_cookie('admin_name',$adminInfo['name'],3600);
			// set_cookie('is_super',$adminInfo['is_super'],3600);
			// set_cookie('role_id',$adminInfo['role_id'],3600);
			//print_r(get_cookie('sys_key') );die;
			redirect(SELLER_SITE_URL);
		}
		exit;
	}

	public function logout(){
		$this->session->set_userdata('seller_key',null,-1);
		redirect(SELLER_SITE_URL.'/login');
	}
}
