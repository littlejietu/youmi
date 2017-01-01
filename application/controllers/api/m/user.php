<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class User extends TokenApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('user/User_pwd_model');
        $this->load->model('user/User_model');
        $this->load->model('user/User_token_model');
        $this->load->model('user/User_auth_model');
    }
    
    
    /**
     * @param 获取用户信息
     *
     * @param $_POST['token'];
     *
     * @return
     */
    public function get()
    {
        $user = $this->loginUser;
        $data = $this->User_model->get_by_id($user['user_id']);
        if (!empty($data['logo']))
        {
            $tmp = strtolower(substr($data['logo'], 0, 7));
            if($tmp !='http://')
                $data['logo'] = BASE_SITE_URL.'/'.$data['logo'];
        }
        output_data($data);exit;
    }
    
    /**
     * @param 用户注销
     * 
     * @param $_POST['token'];
     * @param $_POST['client_type'];
     * 
     * @return 
     */
    public function logout()
    {
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $token = $user['token'];
        $client_type = $this->input->post('client_type');
                
        if($client_type==1){
            $where['token'] = $user['token'];
            $where['client_type'] = 1;
        }else{
            $where['client_type >'] = 1;
            $where['token'] = $token;
        }
        $data['status'] = -1;
        $this->User_token_model->update_by_where($where,$data);
        output_data();exit;
    }
    

    /**
     * @param 更新用户信息
     * @param $_POST['token']
     * @param $_POST['logo']
     * @param $_POST['name']
     * @param $_POST['sign']
     * @param $_POST['token']
     * 
     * @return {"data":{"user_id":"38","user_name":"user19","name":"\u54c8\u54c8\u54c8\u54c8","mobile":"238657348",
	 *                             "mobile_verify":null,"logo":"12.png","sign":"smile","sex":"0","reg_time":"1457950812","reg_ip":"127.0.0.1",
	 *                             "update_time":"1457950812","status":"1","platform_id":"1"
	 *                             },
     *                  "code":"USER_UPDATED",
     *                  "msg":"\u66f4\u65b0\u6210\u529f"
     *      }
     */
    public function modify()
    {
        $name = $this->input->post('name');
        $logo = $this->input->post('user_logo');
        $sign = $this->input->post('user_sign');
        $token = $this->input->post('token');
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        
        
        $config = array(
            array(
                'field'=>'token',
                'label'=>'token',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        
        if($this->form_validation->run() === TRUE)
        {
            
            $where['user_id'] = $user_id;
            if (!empty($name))
            {
                $data['name'] = $name;
            }
            if (!empty($logo))
            {
                $data['logo'] = $logo;
            }
            if (!empty($sign))
            {
                $data['sign'] = $sign;
            }
            $data['update_time'] = time();
            if ($this->User_model->update_by_where($where,$data))
            {
                $userInfo = $this->User_model->get_by_where($where);
                
                if (!empty($userInfo['logo']))
                {
                    $userInfo['logo'] = BASE_SITE_URL.'/'.$userInfo['logo'];
                }
                output_data($userInfo);exit;
            }
            else
            {
                //output_error(-1,'FAILED');exit;
                output_error(-1,'失败');exit;
            }

        }

    }

    //申请成为正式会员
    public function member()
    {
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $mobile = $this->input->post('mobile');
        $code = $this->input->post('code');
        $invoice_title = $this->input->post('invoice_title');
        $car_no = $this->input->post('car_no');
        $car_model = $this->input->post('car_model');
        $company_id = $this->input->post('company_id');
        
        $config = array(
            array(
                'field'=>'mobile',
                'label'=>'mobile',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'code',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'company_id',
                'label'=>'company_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        
        if($this->form_validation->run() === TRUE)
        {
            $platform_id = C('basic_info.PLATFORM_ID');
            $check_code = $this->sms_service->check_code($mobile, $code, 8, $platform_id);
            if (!$check_code){
                output_error(-1,'验证码错误或过期');exit;
            }

            $aExist = $this->User_model->get_by_where(array('mobile'=>$mobile,'company_id'=>$company_id,'user_id<>'=>$user_id));
            if(!empty($aExist)){
                output_error(-1,'手机已存在');exit;  //USER_MOBILE_EXIST
            }

            $data = $data_detail = array();
            if (!empty($invoice_title))
                $data_detail['invoice_title'] = $invoice_title;
            if (!empty($car_no))
                $data_detail['car_no'] = $car_no;
            if (!empty($car_model))
                $data_detail['car_model'] = $car_model;

            $data['mobile'] = $mobile;
            $data['member_status'] = 3; //申请
            $data['update_time'] = time();
            $data_detail['member_time'] = time();

            $this->User_model->update_by_id($user_id,$data);
            $this->User_detail_model->update_by_id($user_id,$data_detail);

            output_data();exit;
        }else{
            output_error(-1,'数据不全');exit; 
        }

    }
    
    
    /**
     * @param 修改密码
     * 
     * @param $_POST['token']
     * @param $_POST['old_pwd']
     * @param $_POST['pwd']
     * @param $_POST['repwd']
     * 
     * @return {"data":"","code":"USER_PWD_UPDATED","msg":"\u5bc6\u7801\u4fee\u6539\u6210\u529f"}
     */
    public function setpwd()
    {
        $old_pwd = $this->input->post('old_pwd');
        $pwd = $this->input->post('pwd');
        $repwd = $this->input->post('repwd');
        $user = $this->loginUser;
        $token = $user['token'];
        $user_id = $user['user_id'];
        $config = array(
            array(
                'field'=>'token',
                'label'=>'token',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'old_pwd',
                'label'=>'old_pwd',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'密码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'repwd',
                'label'=>'密码',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            $where['user_id'] = $user_id;
            $aUser = $this->User_model->get_by_where($where);
            $aUser_pwd = $this->User_pwd_model->get_by_id($aUser['user_id']);
            if (empty($aUser))
            {
                //output_error(-1,'UESER_NOT_EXIST');exit;
                output_error(-1,'用户不存在');exit;
            }
            if (md5($old_pwd) != $aUser_pwd['pwd'])
            {
                //output_error(-1,'USER_OLDPWD_ERROR');exit;
                output_error(-1,'原密码错误');exit;
            }
            if ($pwd == $old_pwd)
            {
                //output_error(-1,'USER_PWD_NOCHANGE');exit;
                output_error(-1,'密码没有修改');exit;
            }
            if ($repwd != $pwd)
            {
                //output_error(-1,'USER_PWD_DIFFERENCE');exit;
                output_error(-1,'两次密码不一致');exit;
            }
            if (preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/",$pwd))
            {
                $data['pwd'] = md5($pwd);
                $where2['id'] = $aUser['user_id'];
                if ($this->User_pwd_model->update_by_where($where2,$data))
                {
                    output_data();exit;
                }
                else 
                {
                    //output_error(-1,'FAILED');exit;
                    output_error(-1,'失败');exit;
                }
            }
            else
            {
                //output_error(-1,'USER_PWD_FORMAT_ERROR');exit;
                output_error(-1,'密码格式错误');exit;
            }
        }
        else 
        {
            if (empty($old_pwd))
            {
                //output_error(-1,'USER_OLDPWD_NULL');exit;
                output_error(-1,'旧密码为空');exit;
            }
            if (empty($pwd))
            {
                //output_error(-1,'USER_PWD_NULL');exit;
                output_error(-1,'用户密码为空');exit;
            }
            if (empty($repwd))
            {
                //output_error(-1,'USER_REPWD_NULL');exit;
                output_error(-1,'确认密码为空');exit;
            }
        }
    }
    
    
//     /**
//      * @param 修改手机号码步骤1:验证原号码
//      * 
//      * @param $_POST['code']
//      * //@param $_POST['mobile']
//      * @return 
//      */
//     public function mod_mobile1()
//     {
//         $mobile = $this->input->post('mobile');
//         $code = $this->input->post('code');
//         $platform_id = $this->input->post('platform_id');
        
//         $config = array(
//             array(
//                 'field'=>'mobile',
//                 'label'=>'mobile',
//                 'rules'=>'trim|required',
//             ),
//             array(
//                 'field'=>'code',
//                 'label'=>'code',
//                 'rules'=>'trim|required',
//             ),
//         );
//         $this->form_validation->set_rules($config);
        
//         if($this->form_validation->run() === TRUE)
//         {
//             /*$arrData = array(
//                 'mobile' => $mobile,
//                 'code' => $code,
//                 'type_id' => 4,
//                 'platform_id' =>$platform_id,
//             );*/
            
//             $check_code = $this->sms_service->check_code($mobile,$code,4,$platform_id);
//             if ($check_code)
//             {
//                 $result = array('data'=>null,
//                     'code'=>1,
//                     'msg'=>'验证成功',
//                     'action' =>'sms_check_code',
//                 );
//                 echo json_encode($check_code);exit();
//             }
//             else 
//             {
//                 $arrRes = array();
//                 $arrRes['code'] = -1;
//                 $arrRes['msg'] = '验证码错误';    //USER_PHONE_SMS_ERROR
//                 $arrRes['action'] = 'user_mod_mobile';
//                 echo json_encode($arrRes);exit();
//             }
//         }
//     }
    
    /**
     * @param 修改手机号码
     * 
     * @param $_POST['token']
     * @param $_POST['code']
     * @param $_POST['mobile']
     * @param $_POST['platform_id']
     * 
     * @return 
     * 
     */
    public function mod_mobile()
    {
        $mobile = $this->input->post('mobile');
        $code = $this->input->post('code');
        $platform_id = $this->input->post('platform_id');
        $user = $this->loginUser;
        $user_id = $user['user_id'];

        $config = array(
            array(
                'field'=>'token',
                'label'=>'token',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'mobile',
                'label'=>'mobile',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'code',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        
        if($this->form_validation->run() === TRUE)
        {
            /*$arrData = array(
                'mobile' => $mobile,
                'code' => $code,
                'type_id' => 5,
                'platform_id' =>$platform_id,
            );*/
            
            $check_code = $this->sms_service->check_code($mobile, $code, 5, $platform_id);
            if (!$check_code)
            {
                output_error('-1','验证码错误');exit;
            }
            $arrUser = $this->User_model->get_by_where(array('user_name'=>$mobile));
            if (!empty($arrUser))
            {
                output_error('-1','该用户已被注册');exit;
            }
            if (preg_match("/^1[34578]\d{9}$/",$mobile))
            {
                $where['user_id'] = $user_id;
                $data['mobile'] = $mobile;
                $data['user_name'] = $mobile;
                if ($this->User_model->update_by_where($where,$data))
                {
                    $userData['user_name'] = $mobile; 
                    if ($this->User_pwd_model->update_by_id($user_id,$userData))
                    {
                        $arrRes['code'] = 1;
                        $arrRes['msg'] = 'SUCCESS';
                        $arrRes['action'] = 'm_user_mod_mobile';
                        $arrRes['data'] = null;
                        echo json_encode($arrRes);exit();
                    }
                }
                else 
                {
                    $arrRes['code'] = -1;
                    $arrRes['msg'] = 'FAILED';
                    output_error(-1,'号码修改失败');exit;
                    //echo json_encode($arrRes);exit();
                }
            }
            else 
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = '手机号码格式不正确';
                //echo json_encode($arrRes);exit();
            }
        }
    }

    /**
     * @param 消息提醒开关
     * 
     * @param $_POST['msg_status']
     * @param $_POST['token']
     */
    public function msg_switch()
    {
        $status = $this->input->post('msg_status');
        
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        
        $config = array(
            array(
                'field'=>'msg_status',
                'label'=>'status',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'token',
                'label'=>'token',
                'rules'=>'trim|required',
            ),
        );
        
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            $where['user_id'] = $user_id;
            $data['msg_status'] = $status;
            if ($this->User_model->update_by_where($where,$data))
            {
                output_data();exit;
            }
            else 
            {
                //output_error(-1,'FAILED');exit;
                output_error(-1,'失败');exit;
            }
        }
        else
        {
            if (empty($status))
            {
                //output_error(-1,'USER_MSG_STATUS_NULL');exit;
                output_error(-1,'用户消息状态为空');exit;
            }
        }
    }
    
}


