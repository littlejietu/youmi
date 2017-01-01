<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oiladmin_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('oil/O_admin_model');
        $this->ci->load->model('oil/O_admin_token_model');
		$this->ci->load->library('encryption');
	}
	
	
    /**
     * @param $arrUserData=array('user_name'=>'user_name','pwd'=>'pwd','client_type'=>1)
     * 
     * @return $arrRes = array('data'=>array('user_id'=>1,'username'=>'用户名','name'=>'昵称'.....) ,'code'=>'SUCCESS','msg'=>'登录成功')
     */
    public function login($arrUserData)
    {
        $aReturn = array('code'=>'EMPTY','msg'=>'');
        $user_name = $arrUserData['user_name'];
        $pwd = $arrUserData['pwd'];
        $client_type = $arrUserData['client_type'];
        if(empty($user_name)){
            $aReturn = array('code'=>-1,'msg'=>'用户名不能为空');
            return $aReturn;
        }


        $arrWhere = array('username'=>"".$user_name, 'password'=>"$pwd");
        $aUserInfo = $this->ci->O_admin_model->get_by_where($arrWhere);
        if (empty($aUserInfo)){
            $aReturn = array('code'=>-1,'msg'=>'用户名或密码不正确');
            return $aReturn;
        }
        
        if($client_type==2 || $client_type==3 || $client_type==4){
            $arrWhere = array('user_name'=>$user_name, 'client_type'=>$client_type,'status'=>1);
            $this->ci->O_admin_token_model->update_by_where($arrWhere,array('status'=>-2));
        }

        if ($aUserInfo['status'] == 2){
            $aReturn = array('code'=>-2,'msg'=>'用户被禁用');
            return $aReturn;
        }
        if (empty($aUserInfo['site_ids'])){
            $aReturn = array('code'=>'No_SiteId','msg'=>'帐号设置有问题');
            return $aReturn;
        }
        if (!strpos($aUserInfo['site_ids'],',')===false){
            $aReturn = array('code'=>'Not_Cashier','msg'=>'收银员帐号才能登录');
            return $aReturn;
        }

        $tokenData = array(
            'admin_id' => $aUserInfo['id'],
            'user_name' => $aUserInfo['username'],
            'name' => $aUserInfo['name'],
            'site_id' => $aUserInfo['site_ids'],
            'token' => md5(time().mt_rand(0,1000)),
            'refresh_token' => md5(time().mt_rand(1000,2000)),
            'addtime' => time(),
            'expire_time' => time()+86400*7,
            'client_type' => $client_type,
        );

        if ($this->ci->O_admin_token_model->insert_string($tokenData)){
            $data = array_merge($tokenData, array('name'=>$aUserInfo['name']));

            $aReturn['code']=1;
            $aReturn['msg']='';
            $aReturn['data']=$data;
            return $aReturn;
        }else{
            $aReturn = array('code'=>-1,'msg'=>'FAILED');
            return $aReturn;
        }

        return $aReturn;
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
     * @param $arrUserData=array('user_name'=>'用户名','mobile'=>'手机号','pwd'=>'密码','name'=>'昵称','platform_id' =>1,'ip'=>'ip','company_id'=>1)
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
            'site_id' => $arrUserData['site_id'],
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
                'site_id' => $arrUserData['site_id'],
                'platform_id' => 1,
            );
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

            

            $arrReturn  = array('code'=>'SUCCESS','message'=>'注册成功','data'=>$user_id);
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
        $a['token'] = "'".$token."'";
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
    /*
     *
     * 获得用户积分
     * */
    public function getUserIntegral($uid){
        $whereArr  = array(
            'user_id'=>$uid,
        );
        $userData = $this->ci->Account_model->get_by_where($whereArr,'acct_integral');
        return intval($userData['acct_integral']);
    }

}