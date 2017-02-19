<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        
    }

    public function index(){
        $username = $this->input->post('username');
        $pwd = $this->input->post('pwd');
        $client_type = $this->input->post('client_type');

        $this->load->service('oiladmin_service');
        $this->load->model('oil/Site_model');

        if (empty($username)){
            output_error('-1','用户名为空');exit;
        }
        if (empty($pwd)){
            output_error('-1','密码为空');exit;
        }
        if (empty($client_type)){
            output_error('-1','客户端类型为空');exit;
        }

        $config = array(
            array(
                'field'=>'username',
                'label'=>'username',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'pwd',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'client_type',
                'label'=>'client_type',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if (!$this->form_validation->run() === TRUE)
        {
        	output_error('-1','参数有误');exit;
        }

        $arrUserData = array('user_name'=>$username, 'pwd'=>$pwd, 'client_type'=>$client_type);
        $arrRes = $this->oiladmin_service->login($arrUserData);

        if(!empty($arrRes['data'])){
            $info = $this->Site_model->get_by_id($arrRes['data']['site_id']);
            if($info['status']!=1){
                output_error('-1','油站已关闭');exit;
            }
            $aServ['site_name'] = $info['site_name'];
            $aServ['site_logo'] = $info['logo'];
            $aServ['key'] = md5($arrRes['data']['admin_id']);
        	$aServ['msg_server_ip'] = C('basic_info.SERVER_MSG_IP');
        	$aServ['msg_server_port'] = '1234';
        	$arrRes['data'] = array_merge($arrRes['data'], $aServ);
            
        }

        output_all($arrRes);exit;
    }

    public function logout(){
        $token = $this->input->post_get('token');
        $this->load->model('oil/O_admin_token_model');
        $this->O_admin_token_model->update_by_where(array('token'=>$token),array('status'=>-1));
        output_data();
    }

}
