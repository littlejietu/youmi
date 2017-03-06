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


}