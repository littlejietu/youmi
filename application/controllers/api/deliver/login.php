<?php

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**派送员登录*/
    public function reg(){
        $this->load->service('sms_service');
        $this->load->service('user_service');

        $this->load->model('User_pwd_model');

        $uin = $this->input->post('uin');
        $ip = $this->input->post('ip');
        $type_id =$this->input->post('type_id');
        $client_type = $this->input->post('client_type');  //登录类型：ios、android、wap
        $platform_id = $this->input->post('platform_id');  //平台ID
        $code = $this->input->post('code');

        $arrRes['action'] = 'user_reg';
        $config = array(
            array(
                'field'=>'uin',
                'label'=>'手机号码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'平台ID',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'ip',
                'label'=>'IP',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            //验证码验证
            /*$arrData = array(
                'mobile' => $uin,
                'code' => $code,
                'type_id' => 6,
                'platform_id' =>$platform_id,
            );*/

            $check_code = $this->sms_service->check_code($uin, $code, $type_id, $platform_id);

            if (!$check_code)
            {
                output_error('-1','验证码错误！');exit;
            }

            $userData['mobile'] = $uin;
            $userData['platform_id'] = $platform_id;
            $userData['ip'] = $ip;

            $arrRes = $this->user_service->reg_deliveruser($userData);
            output_data($arrRes);exit;
        }
        else
        {
            if (empty($uin))
            {
                output_error('-1','手机号码不能为空！');exit;
            }
            if (empty($platform_id))
            {
                output_error('-1','平台不能为空！');exit;
            }
            if (empty($code))
            {
                output_error('-1','验证码不能为空！');exit;
            }
        }
    }

}