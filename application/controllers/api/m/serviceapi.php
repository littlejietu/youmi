<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceAPI extends TokenApiController{
	public function __construct(){
		parent::__construct();
		$this->load->model('User_third_model');
		$this->load->library('RonghubApi');
		$this->targetId='KEFU145820074629693';

		$this->appSecret = 'FatV8x1bzl2Lww';
		$this->appKey = 'k51hidwq1yt4b';

	}
	
	//请求token
	public function getToken(){
		$user_id = $this->loginUser['user_id'];
		if(empty($user_id)){
			output_error(-1,'没有找到用户信息');exit;
		}else{
			$aUser = $this->User_third_model->get_info_by_id($user_id);
			
			if (empty($aUser)) {
				output_error(-1,'没有找到用户信息');exit;
			}


			$userName = empty($aUser['name'])?'用户'.$aUser['user_id']:$aUser['name'];
			if (empty($aUser['rong_token'])) {
				//请求融云token
				$ronghubApi = new RonghubApi();
				if (empty($aUser['logo']))
				    $aUser['logo'] = 'xxx.png';//可以设置默认头像
				$res = $ronghubApi->getToken($aUser['user_id'],$userName, BASE_SITE_URL.$aUser['logo']);
				$res_Arr = json_decode($res,true);
				if ($res_Arr['code']=='200') {
					$data = array('user_id'=>$aUser['user_id'],'rong_token'=> $res_Arr['token']);
					$update_res = $this->User_third_model->insert($data);//更新融云token到数据库
				}
				$rongToken = $res_Arr['token'];
			}else{
				$rongToken = $aUser['rong_token'];
			}

			$result = array('status'=>1,'userId'=>$aUser['user_id'],'userName'=>$userName,'token'=>$rongToken, 'service_id'=>'20');
		}
		output_data($result);exit;
	}

	//无用???刷新token (当status值为1时，代表刷新成功)
	public function refreshToken(){
		$user_id = (int)$this->input->get('user_id');
		if (empty($user_id)) {
			output_error(-1,'参数错误');exit;
		}

		$aUser = $this->User_third_model->get_info_by_id($user_id);
		if (empty($aUser)) {
			output_error(-1,'没有找到用户信息');exit;
		}

		$userName = empty($aUser['name'])?'用户'.$aUser['user_id']:$aUser['name'];
		if (!empty($aUser['rong_token'])) {//如果没有token,则请求
			//请求刷新融云token
			$ronghubApi = new RonghubApi();
			$res = $ronghubApi->userRefresh($aUser['id'],$userName,$aUser['id'].$userName);
			$res_Arr = json_decode($res,true);
			if($res_Arr['code']=='200'){
				$result = array('status'=>1,'userId'=>$aUser['id'],'userName'=>$userName,'token'=>$aUser['rong_token']);
			}
		}else{
			output_error(-1,'没有找到用户信息');exit;
		}
		
		output_data($result);exit;
	}

	//生成签名
	public function createAutoGraph(){
		srand((double)microtime()*1000000);
		$appSecret = $this->appSecret; // 开发者平台分配的 App Secret。
		$nonce = rand(); // 获取随机数。
		$timestamp = time(); // 获取时间戳。
		$signature = sha1($appSecret.$nonce.$timestamp);
		//echo $nonce.','.$timestamp.',';
		echo $signature;exit();
	}

	//校验签名
	public function checkAutoGraph(){
		$appSecret = $this->appSecret; // 开发者平台分配的 App Secret。
		$nonce = $_GET['nonce']; // 获取随机数。
		$timestamp = $_GET['timestamp']; // 获取时间戳。
		$signature = $_GET['signature']; // 获取数据签名。
		$local_signature = sha1($appSecret.$nonce.$timestamp); // 生成本地签名。
		if(strcmp($signature, $local_signature)===0){
			//相关处理
		    echo 'OK';
		} else {
		    echo 'Error';
		}
	}
	
}