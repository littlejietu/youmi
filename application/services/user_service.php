<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('user/User_pwd_model');
		$this->ci->load->model('user/User_model');
		$this->ci->load->model('user/User_num_model');
		$this->ci->load->model('user/User_token_model');
        $this->ci->load->model('user/User_detail_model');
		$this->ci->load->library('encryption');
        $this->ci->load->model('acct/Account_model'); //账户模块
	}
	
	
    /**
     * @param $arrUserData=array('uin'=>'uin','a1'=>'a1')
     * 
     * @return $arrRes = array('data'=>array('user_id'=>1,'username'=>'用户名','name'=>'昵称'.....) ,'code'=>'SUCCESS','msg'=>'登录成功')
     */
    public function login($arrUserData)
    {
        $this->ci->load->model('Shop_model');
        $uin = $arrUserData['uin'];
        $a1 = $arrUserData['a1'];
        $client_type = $arrUserData['client_type'];
        if(empty($uin))
        {
            output_error(-1,'用户名不能为空');exit;
        }


        $arrWhere = array('user_name'=>"'$uin'", 'pwd'=>"'$a1'");
        $aUserInfo = $this->ci->User_pwd_model->get_by_where($arrWhere);
        if (empty($aUserInfo))
        {
            output_error(-1,'用户名或密码不正确');exit;
        }
        
        if($client_type==2 || $client_type==3){
            $arrWhere = array('user_name'=>$uin, 'client_type'=>$client_type,'status'=>1);
            $this->ci->User_token_model->update_by_where($arrWhere,array('status'=>-2));
        }
//         $s2 = $aUserInfo['pwd'];
//         //解密传过来的a1
//         $iv = 'AESAPPCLIENT_KEY';//16或16的倍数长个char
//         $key = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $s2, base64_decode($arrUserData['a1']), MCRYPT_MODE_CBC,$iv);
//         //验证是否能够解密
//         if (empty($key))
//         {
//             output_error(-1,'USERNAME_PASSWORD_NO_RIGHT');exit;
// //             $arrRes['code'] = -1;
// //             $arrRes['msg'] = 'USERNAME_PASSWORD_NO_RIGHT';
// //             return $arrRes;
//         }
//         $arrUin = explode('_',$key);
//         //验证解密出的uin是否和提交的uin相同
//         if ($uin != $arrUin[0])
//         {
//             output_error(-1,'USERNAME_PASSWORD_NO_RIGHT');exit;
// //             $arrRes['code'] = -2;
// //             $arrRes['msg'] = 'USERNAME_PASSWORD_NO_RIGHT';
// //             return $arrRes;
//         }
//         //验证生成的s2是否与数据库中的数据相同
//         if (md5($arrUin[0].$arrUin[1]) != $aUserInfo['pwd'])
//         {
//             output_error(-1,'USERNAME_PASSWORD_NO_RIGHT');exit;
// //             $arrRes['code'] = -3;
// //             $arrRes['msg'] = 'USERNAME_PASSWORD_NO_RIGHT';
// //             return $arrRes;
//         }
//         if (empty($aUserInfo))
//         {
//             output_error(-1,'USER_NOT_EXITS');exit;
// //             $arrRes['code'] = -1;
// //             $arrRes['msg'] = 'USER_NOT_EXITS';
// //             return $arrRes;
//         }
        if ($aUserInfo['status'] == 2)
        {
            //USER_LOCKED
            output_error(-2,'用户被禁用');exit;
//             $arrRes['code'] = 2;
//             $arrRes['msg'] = 'USER_LOCKED';
//             return $arrRes;
        }

        //判断用户是否店主帐号
        $arrShopUser = $this->ci->Shop_model->get_by_where(array('seller_userid'=>$aUserInfo['id']));
        if(!empty($arrShopUser)){
            output_error(-3,'商店用户不能登录app');exit;
        }


        // unset($where);
        // $where['user_name'] = "'$uin'";
        $arrWhere = array('user_name'=>"'$uin'");
        $data = $this->ci->User_model->get_by_where($arrWhere);
        $tokenData = array(
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'company_id' => $data['company_id'],
            'token' => md5(time().mt_rand(0,1000)),
            'refresh_token' => md5(time().mt_rand(1000,2000)),
            'addtime' => time(),
            'expire_time' => time()+86400*7,
            'client_type' => $arrUserData['client_type'],
        );
        $data['token'] = $tokenData['token'];
        $data['refresh_token'] = $tokenData['refresh_token'];
//         $randomkey = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, rtrim($arrUin[3]), $data['token'], MCRYPT_MODE_CBC,$iv);
//         $data['randomkey'] = rtrim((base64_encode($randomkey)));
//        echo $data['randomkey'];
        if ($this->ci->User_token_model->insert($tokenData))
        {
            $arrRes = $data;
            return $arrRes;
        }
        else 
        {
            output_error(-1,'FAILED');exit;
//             $arrRes['code'] = -1;
//             $arrRes['msg'] = 'FAILED';
//             return $arrRes;
        }
    }

    /**
     * @param $arrUserInfo=array('user_name'=>'用户名','mobile'=>'手机号码','pwd'=>'密码','name'=>'昵称','platform_id' =>1,'ip'=>'ip')
     * 
     * @return
     * $arrRes = Array ( 
     * [code] => SUCCESS
     * [msg] => 注册成功
     * )
     */
    public function reg($arrUserData)
    {
        $arrReturn  = array('code'=>'EMPTY','message'=>'','data'=>0);
        if (!preg_match("/^1[34578]\d{9}$/",$arrUserData['mobile']))
        {
            $arrReturn = array('code'=>'USER_PHONE_FORMAT_ERROR','message'=>'手机号格式','data'=>0);
            return $arrReturn;
        }

        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/",$arrUserData['pwd']))
        {
            $arrReturn = array('code'=>'USER_PWD_FORMAT_ERROR','message'=>'密码格式不正确，须为数字+英文(大小写)','data'=>0);
            return $arrReturn;
        }

        $arrReturn = $this->reg_user($arrUserData);
        if($arrReturn['code']=='SUCCESS')
        {
            $where = array('user_name'=>"'".$arrUserData['user_name']."'");
            $aUser = $this->ci->User_model->get_by_where($where);
            $arrReturn['data'] = $aUser;
        }
        
        return $arrReturn;

    }

    /**
     * @param $arrUserData=array('user_name'=>'用户名','mobile'=>'手机号','pwd'=>'密码','name'=>'昵称','platform_id' =>1,'ip'=>'ip','site_id'=>1,'company_id'=>12)
     * 
     * @return
     * $arrReturn = Array ( 
     * [code] => SUCCESS
     * [message] => 注册成功
     * )
     */
    public function reg_user($arrUserData){
        $arrReturn  = array('code'=>'EMPTY','message'=>'','data'=>0);
        $where = array('user_name'=>"'".$arrUserData['user_name']."'");
        if(empty($arrUserData['reg_ip']))
            $arrUserData['reg_ip'] = $this->ci->input->ip_address();

        $this->ci->load->model('Account_model');

        $aUser = $this->ci->User_model->get_by_where($where);
        if (!empty($aUser))
        {
            $arrReturn  = array('code'=>'USER_NAME_EXIST','message'=>'用户名已存在','data'=>$aUser['user_id']);
            return $arrReturn;
        }

        $user_pwd_data = array(
            'user_name' => $arrUserData['user_name'],
            'pwd' => md5($arrUserData['pwd']),
            'status' => 1,
            'platform_id' => 1,
        );

        if ($user_id = $this->ci->User_pwd_model->insert_string($user_pwd_data))
        {
            $data = array(
                'user_id' => $user_id,
                'user_name' => $arrUserData['user_name'],
                'mobile' => $arrUserData['mobile'],
                'mobile_verify' => ($arrUserData['mobile']==$arrUserData['user_name']?1:0),
                'reg_time' => time(),
                'reg_ip' => $arrUserData['reg_ip'],
                'update_time' => time(),
                'status' => 1,
                'reg_site_id' => $arrUserData['site_id'],
                'platform_id' => 1,
            );
            if(!empty($arrUserData['company_id']))
                $data['company_id'] = $arrUserData['company_id'];
            if(!empty($arrUserData['name']))
                $data['name'] = $arrUserData['name'];
            if(!empty($arrUserData['logo']))
                $data['logo'] = $arrUserData['logo'];
            $this->ci->User_model->insert($data);

            $this->ci->Account_model->init($user_id);

            $user_num_data = array(
                'user_id' => $user_id,
            );
            $this->ci->User_num_model->insert($user_num_data);

            $user_detail_data = array(
                'user_id'=>$user_id
            );
            $this->ci->User_detail_model->insert($user_detail_data);

            

            $arrReturn  = array('code'=>'SUCCESS','message'=>'注册成功','data'=>$data);
            return $arrReturn;
        }
        else 
        {
            $arrReturn  = array('code'=>'FAILED','message'=>'注册失败','data'=>0);
            return $arrReturn;
        }

    }

    /**
     * @param 根据token获取用户信息
     */
    public function get_userid($token)
    {
        $arrRes = array('data'=>'','code' =>'','msg'=>'');
        $a['token'] = "$token";
        $loginUser = $this->ci->User_token_model->get_by_where($a);
        if (empty($loginUser))
        {
            return array();
        }
        else 
        {
            return $loginUser;
        }
    }
    /*
     *
     * 更新用户积分
     * */
    public function updateIntegral($uid,$num){
        $whereArr  = array(
            'user_id'=>$uid,
        );
        $userData = $this->ci->Account_model->get_by_where($whereArr,'acct_id,acct_integral');
        $userData['acct_integral'] +=$num;
        if($userData['acct_integral'] <0){
            $userData['acct_integral'] = 0;
        }
        $this->ci->Account_model->update_by_where(array('user_id'=>$uid),$userData);

    }

}