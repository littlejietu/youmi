<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Other extends BaseSellerController {

	public function index()
	{
	}

	/**管理员修改密码*/
    public function  modifypw() {

        $this->load->model('oil/O_admin_model');
        $this->load->library('encrypt');
        $this->lang->load('admin_admin');
        if($this->input->is_post()){
            $admin_info = unserialize($this->encrypt->decode($this->session->userdata('seller_key'),C('basic_info.MD5_KEY') ) );
            $admin_id = $admin_info['admin_id'];
            $old_pw = $this->input->post('old_pw');
            $new_pw = $this->input->post('new_pw');
            $new_pw2 = $this->input->post('new_pw2');

            $config = array(
                array(
                    'field'   => 'old_pw',
                    'label'   => '原始密码不能为空！',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'new_pw',
                    'label'   => '新密码不能为空！',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'old_pw',
                    'label'   => '二次密码不能为空！',
                    'rules'   => 'trim|required'
                ),
            );

            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
            {
                $adminInfo = $this->O_admin_model->get_by_id($admin_id,'password');
                $old_pw = md5(trim($old_pw));
                $new_pw = md5(trim($new_pw));
                $new_pw2 = md5(trim($new_pw2));

                if(!empty($adminInfo) && $adminInfo['password']== $old_pw) {
                    if($new_pw==$new_pw2){
                        $this->O_admin_model->update_by_id($admin_id,array('password' => $new_pw2));
                        showMessage('密码修改成功，请重新登录！','/seller/login/logout');

                    }else{
                        showMessage('二次密码不一致！','/seller/other/modifypw');
                    }
                }else
                    showMessage('原始密码错误i！','/seller/other/modifypw');
            }
        }

        $this->load->view('seller/modifypw');
    }
}