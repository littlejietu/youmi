<?php

class Reg extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('User_pwd_model');
        $this->load->model('User_model');
        $this->load->model('User_token_model');
        $this->load->model('User_auth_model');
    }
        
    /**
     * @param 用户注册
     *
     * @param $_POST['uin']
     * @param $_POST['code']
     * @param $_POST['pwd']
     * @param $_POST['name']
     * @param $_POST['platform_id']
     *
     * @return{
     *              "data":"",
     *              "code":"SUCCESS",
     *              "msg":"\u6ce8\u518c\u6210\u529f",
     *               }
     */
    public function reg()
    {
        $uin = $this->input->post('mobile');
        $pwd = $this->input->post('pwd');
        $code = $this->input->post('code');
        $client_type = $this->input->post('client_type');
        $site_id = $this->input->post('site_id');
        $platform_id = 1;
        $ip = $this->input->post('ip');
        $user_name = $uin;
        $arrRes['action'] = 'user_reg';
        $config = array(
            array(
                'field'=>'mobile',
                'label'=>'手机号码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'密码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'验证码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'site_id',
                'label'=>'站点id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
    
        if($this->form_validation->run() === TRUE)
        {

            //1:Resgister 2:ForgetPassword 3:BankCert 4:ModifyUserName 5:TiedUserName 6:DeliverResgister
            //验证码验证
            $check_code = $this->sms_service->check_code($uin, $code, 1, $site_id);
            if (!$check_code)
            {
                //echo json_encode($check_code);exit();
                output_error('-1','验证码错误');exit;
            }

            
            $userData['mobile'] = $uin;
            $userData['pwd'] = $pwd;
            $userData['user_name'] = $user_name;
            $userData['site_id'] = $site_id;
            $userData['platform_id'] = $platform_id;
            $userData['reg_ip'] = $ip;
            $arrRes = $this->user_service->reg($userData);
            if ($arrRes['code'] == 'SUCCESS')
            {

                $tokenData = array(
                    'user_id' => $arrRes['data']['user_id'],
                    'user_name' => $arrRes['data']['user_name'],
                    'token' => md5(time().mt_rand(0,1000)),
                    'refresh_token' => md5(time().mt_rand(1000,2000)),
                    'addtime' => time(),
                    'expire_time' => time()+86400*7,
                    'client_type' => $client_type,
                );
                if ($this->User_token_model->insert_string($tokenData))
                {
                    $arrRes['data']['token'] = $tokenData['token'];
                    $arrRes['data']['refresh_token'] = $tokenData['refresh_token'];
                    output_data($arrRes['data']);exit;
                }
                else
                {
                    output_error(-1,'用户自动登录错误');exit;//USER_AUTO_LOGIN_ERROR
                }
            }
            else
            {
                output_error(-1,$arrRes['message']);exit;
            }
        }
        else
        {
            if (empty($uin))
            {
                output_error(-1,'手机号不能为空');exit;    //USER_PHONE_NULL
            }
            if (empty($pwd))
            {
                output_error(-1,'密码不能为空');exit;  //USER_PWD_NULL
            }
            if (empty($platform_id))
            {
                output_error(-1,'平台id不能为空');exit;   //PLATFORM_ID_NULL
            }
            if (empty($ip))
            {
                output_error(-1,'IP不能为空');exit;    //IP_NULL
            }
        }
    }

    /**
     * @param 忘记密码
     *
     * @param $_POST['code']
     * @param $_POST['mobile']
     * @param $_POST['pwd']
     * @param $_POST['platform_id']
     * @param
     *
     * @return {"data":"","code":"USER_PWD_UPDATED","msg":"\u5bc6\u7801\u4fee\u6539\u6210\u529f"}
     */
    public function newpwd()
    {
        $user_name = $this->input->post('mobile');
        $code = $this->input->post('code');
        $pwd = $this->input->post('pwd');
        $platform_id = $this->input->post('platform_id');
    
        $config = array(
            array(
                'field'=>'mobile',
                'label'=>'手机号',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'验证码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'pwd',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'platform_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            /*$arrData = array(
                'mobile' => $user_name,
                'code' => $code,
                'type_id' => 2,
                'platform_id' =>$platform_id,
            );*/
            $check_code = $this->sms_service->check_code($user_name, $code, 2, $platform_id);
            if (!$check_code)
            {
                $result = array('data'=>null,
                    'code'=>-1,
                    'msg'=>'验证码错误',
                    'action' =>'sms_check_code',
                );
                echo json_encode($result);exit();
            }
            $where['user_name'] = "'".$user_name."'";
            $userInfo = $this->User_pwd_model->get_by_where($where);
            if (empty($userInfo))
            {
                output_error('-1','用户不存在');exit;   //USER_NOT_EXIST
            }
            if (md5($pwd) == $userInfo['pwd'])
            {
                output_error('-1','密码相同');exit; //USER_PWD_NO_SAME
            }
            if (preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/",$pwd))
            {
                $data['pwd'] = md5($pwd);
                $where2['user_name'] = $user_name;
                if ($this->User_pwd_model->update_by_where($where2,$data))
                {
                    output_data();
                }
                else
                {
                    output_error('-1','FAILED');exit;
                }
            }
            else
            {
                output_error('-1','请输入6-20位(英文+数字)作为登录密码');exit;    //USER_PWD_FORMAT_ERROR
            }
        }
        else
        {
            if (empty($user_name))
            {
                output_error('-1','手机号不能为空');exit;
            }
            if (empty($pwd))
            {
                output_error('-1','密码不能为空');exit;
            }
            if (empty($code))
            {
                output_error('-1','验证码不能为空');exit;
            }
            if (empty($latform_id))
            {
                output_error('-1','平台id不能为空');exit;
            }
        }
    }
    
    /**
     * 绑定第三方用户
     *
     * @param $_POST['uin']
     * @param $_POST['pwd']
     * @param $_POST['nickname']
     * @param $_POST['unionid']
     * @param $_POST['client_type']
     * @param $_POST['sex']
     * @param $_POST['platform_id']
     *
     */
    public function bunding()
    {
        $uin = $this->input->post('uin');
        $pwd = $this->input->post('pwd');
        $name = $this->input->post('nickname');
        $unionid = $this->input->post('unionid');
        $client_type = $this->input->post('client_type');
        $sex = $this->input->post('sex');
        $platform_id = $this->input->post('platform_id');
        $ip = $this->input->post('ip');
        $user_name = $uin;
        $arrRes['action'] = 'user_bunding';
        $config = array(
            array(
                'field'=>'uin',
                'label'=>'手机号码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'密码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'platform_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
    
        if($this->form_validation->run() === TRUE)
        {
            $where['user_name'] = $uin;
            $bunding_user = $this->User_model->get_by_where($where);
            unset($where);
            $where['user_id'] = $bunding_user['user_id'];
            $user = $this->User_auth_model->get_by_where($where);
            if (!empty($bunding_user))
            {
                if (!empty($user))
                {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                    output_error(-1,'该手机号码已经绑定过其他微信号');exit;
                }
                else 
                {
                    $arrRes['data'] = $bunding_user;
                    $str['pwd'] = md5($pwd);
                    $this->User_pwd_model->update_by_id($bunding_user['user_id'],$str);
                }
            }
            else 
            {
                $userData['mobile'] = $uin;
                $userData['pwd'] = $pwd;
                $userData['name'] = $user['nickname'];
                $userData['platform_id'] = $platform_id;
                $userData['user_name'] = $user_name;
                $userData['reg_ip'] = $ip;
                $arrRes = $this->user_service->reg($userData);
            }
            if (!empty($arrRes['data']))
            {
                $data['user_id'] = $arrRes['data']['user_id'];
                $data['user_name'] = $arrRes['data']['user_name'];
                $tokenData = array(
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'token' => md5($data['user_name'].time().mt_rand(0,1000)),
                    'refresh_token' => md5($data['user_name'].time().mt_rand(1000,2000)),
                    'addtime' => time(),
                    'expire_time' => time()+86400*7,
                    'client_type' => $client_type,
                );
                if ($this->User_token_model->insert($tokenData))
                {
                    $data['token'] = $tokenData['token'];
                    $data['refresh_token'] = $tokenData['refresh_token'];
                    $data['expire_time'] = $tokenData['expire_time'];
                    unset($where);
                    $where['unionid'] = $unionid;
                    if ($this->User_auth_model->update_by_where($where,$data))
                    {
                        $arrRes['data']['token'] = $tokenData['token'];
                        
                        $arrRes['refresh_token'] = $tokenData['refresh_token'];
                        if (!empty($arrRes['logo']))
                        {
                            $arrRes['logo'] = BASE_SITE_URL.'/'.$arrRes['logo'];
                        }
                        else 
                        {
                            $arrRes['logo'] ='';
                        }
                        output_data($arrRes['data']);exit;
                    }
                    else
                    {
                        output_error(-1,'授权失败');exit;
                    }
                }
            }
            else 
            {
                output_error(-1,'授权失败');
            }
        }
        else
        {
            if (empty($uin))
            {
                output_error(-1,'手机号不能为空');exit;    //USER_PHONE_NULL
            }
            if (empty($pwd))
            {
                output_error(-1,'密码不能为空');exit;  //USER_PWD_NULL
            }
            if (empty($platform_id))
            {
                output_error(-1,'平台id不能为空');exit;   //PLATFORM_ID_NULL
            }
        }
    }
    
    
}