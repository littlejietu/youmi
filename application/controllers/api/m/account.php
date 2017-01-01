<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends TokenApiController {  
	public function __construct() {
		parent::__construct();
		$this->load->model('Account_model');
		$this->load->model('Coupon_user_model');
		$this->load->service('coupon_service');
	}

	//帐户明细
	public function index(){
		//$token = $this->input->post_get('token');
    	$page = $this->input->post('page');
    	$pagesize = $this->input->post('pagesize');
        $type = $this->input->post('type');
    	$user_id = $this->loginUser['user_id'];
    	if(!$type) $type = 1;
    	if(!$page) $page = 1;
    	if(!$pagesize) $pagesize = 10;

    	$data = array();
    	$aList = array();
    	$arrWhere = array();

    	$this->load->model('Fundorder_model');
	
		if($type==1){//交易
			$arrWhere = array('buyer_userid'=>$user_id,'platform_id'=>C('basic_info.PLATFORM_ID'));
        	$arrWhere['status'] = array(C('FundOrderStatus.Payed'),C('FundOrderStatus.WaitingSettle'),C('FundOrderStatus.Settled'),C('FundOrderStatus.Refunded'));
		}
		else if($type==2){//充值
			$arrWhere = array('buyer_userid'=>$user_id,'platform_id'=>C('basic_info.PLATFORM_ID'),'type_id'=>C('OrderType.Recharge'));
        	$arrWhere['status'] = array(C('FundOrderStatus.Settled'));
		}
		else if($type==3){//提现
			$arrWhere = array('buyer_userid'=>$user_id,'platform_id'=>C('basic_info.PLATFORM_ID'),'type_id'=>C('OrderType.Cash'));
        	$arrWhere['status'] = array(C('FundOrderStatus.Payed'),C('FundOrderStatus.WaitingSettle'),C('FundOrderStatus.Settled'),C('FundOrderStatus.Refunded'));
		}
		else if($type==4){//退款
			$arrWhere = array('buyer_userid'=>$user_id,'platform_id'=>C('basic_info.PLATFORM_ID'),'type_id'=>array( C('OrderType.Consume'),C('OrderType.AfterSalesRefund') ) );
        	$arrWhere['status'] = array(C('FundOrderStatus.Refunded'));
		}

    	//$aList = $this->Fundorder_model->fetch_page($page, $pagesize, $arrWhere,'fund_order_id,type_id,title,total_amt,balance_amt,bonus_amt,netpay_amt,netpay_method,refund,status,create_time','fund_order_id desc');
        //$tb = $this->Fundorder_model->prefix().'acct_log a join '.$this->Fundorder_model->prefix().'trd_fundorder b on(a.fund_order_id=b.fund_order_id)'
        $aList = $this->Fundorder_model->fetch_page($page, $pagesize, $arrWhere,'fund_order_id,type_id,title,total_amt,balance_amt,bonus_amt,netpay_amt,netpay_method,refund,status,create_time','fund_order_id desc');
        // echo $this->Fundorder_model->db->last_query();die;
    	$aList['page'] = $page;
    	$aList['pagesize'] = $pagesize;
    	foreach ($aList['rows'] as $k => $a) {
    		$amount = 0;
    		$inOut = '';
    		if($a['type_id'] == C('OrderType.Consume')){
                if ($a['status'] == C('FundOrderStatus.Refunded')) {
                    $inOut = '退款';
                    if($a['netpay_method']==C('PayMethodType.WeixinPayApp') || $a['netpay_method']==C('PayMethodType.WeixinPayJs')){
                        $inOut = '微信退款';
                    }
                    $amount = '+'.$a['refund'];
                }else{
	    			$inOut = '余额支出';
	    			if($a['netpay_method']==C('PayMethodType.WeixinPayApp') || $a['netpay_method']==C('PayMethodType.WeixinPayJs')){
	    				$inOut = '微信支出';
	    			}
	    			$amount = '-'.$a['total_amt'];
	    		}
    		}else if($a['type_id'] == C('OrderType.Recharge')){
    			$inOut = '充值';
    			if($a['netpay_method']==C('PayMethodType.WeixinPayApp') || $a['netpay_method']==C('PayMethodType.WeixinPayJs')){
    				$inOut = '微信充值';
    			}
    			$amount = '+'.$a['netpay_amt'];
    		}else if($a['type_id'] == C('OrderType.Cash')){
                if($a['status']==C('FundOrderStatus.Settled')){
        			$inOut = '提现';
        			if($a['netpay_method']==C('PayMethodType.WeixinPayApp') || $a['netpay_method']==C('PayMethodType.WeixinPayJs')){
        				$inOut = '微信提现';
        			}
                    $inOut = '已提现';
                    $amount = '-'.$a['total_amt'];
                }else if($a['status']==C('FundOrderStatus.Payed')){
                    $inOut = '审核中';
                    $amount = '-'.$a['total_amt'];
                }else if($a['status']==C('FundOrderStatus.Refunded')){
                    $inOut = '不通过';
                    $amount = '+'.$a['total_amt'];
                }
    		}else if($a['type_id'] == C('OrderType.Promote')){
                $inOut = '佣金';
                $amount = '+'.$a['bonus_amt'];
            }else if($a['type_id'] == C('OrderType.AfterSalesRefund')){
                $inOut = '退款';
                $amount = '+'.$a['refund'];
            }
    		
    		$aList['rows'][$k] = array('title'=>$a['title'], 'inout'=>$inOut, 'amount'=>$amount,'create_time'=>$a['create_time']);
    	}

        output_data($aList);exit;
	}

	//帐户详情
	public function detail(){
		$acct_balance = 0;
		$acct_integral = 0;
		$coupon_num = 0;
		
		$user = $this->loginUser;
		$user_id = $user['user_id'];

		$aAccount = $this->Account_model->get_by_id($user_id);
		if(!empty($aAccount)){
			$acct_balance = $aAccount['acct_balance'];
			$acct_integral = $aAccount['acct_integral'];
		}
		else
			$this->Account_model->init($user_id);
		$coupon_num =  count($this->coupon_service->get_usable_coupons($user_id));

		$data = array('acct_balance'=>$acct_balance,'acct_integral'=>$acct_integral,'coupon_num'=>$coupon_num);

		output_data($data);exit;
	}

	//充值
	public function recharge(){
        $amount = $this->input->post_get('amount');
        $payMethod = $this->input->post_get('paymethod');
        $netpayAccount = $this->input->post('netpayAccount');
        $netpayAccountid = $this->input->post('netpayAccountid');
        $ip = $this->input->post('ip');
        $extParam = $this->input->post('extParam');
        $platformId = C('basic_info.PLATFORM_ID');
        $user = $this->loginUser;
		$toUserId = $user['user_id'];
        $toUserName = $user['user_name'];

        $this->load->service('fundOrder_service');

        $config = array(
            array(
                'field'   => 'amount',
                'label'   => '金额',
                'rules'   => 'trim|required'
            ),
            array(
                'field'   => 'paymethod',
                'label'   => '支付方式',
                'rules'   => 'trim|required'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() !== TRUE)
        {
            //todo...
            exit;
        }
        
        $title = C('OrderTypeName.2').' '.$amount.'元';

        $arrReturn = $this->fundorder_service->recharge($title, $toUserId, $toUserName, $amount, $payMethod, $netpayAccount, $netpayAccountid, $extParam, $ip, $platformId);

        if($arrReturn['code']==C('OrderResultError.Success')){
            $strResult = $arrReturn['errInfo'];
            if(!empty($strResult) && !is_array($strResult) && strpos(strtolower($strResult), '<form')===0){
                $arrReturn['errInfo'] = C("PAY_AUTO_HTML").replace("{FORM}", $strResult);
            }
        }
        if ($arrReturn['code'] == 'Failure')
        {
            output_error(-1,$arrReturn['errInfo']);exit;
        }
        else 
        {
            output_data($arrReturn['errInfo']);exit;
        }
//         $result = array('data'=>$arrReturn['errInfo'],
//             'code'=>$arrReturn['code'],
//             'message'=>'操作成功'
//         );
//         echo json_encode($result);
    }

    //提现
    public function cash(){
    	//$token = $this->input->post_get('token');
    	$amount = $this->input->post('amount');
        $paypwd = $this->input->post('paypwd');
    	$netpayAccount = $this->input->post('netpay_account');
        //$netpayAccountid = $this->input->post_get('netpay_accountid');
        $ip = $this->input->post('ip');

        $toUserId = $this->loginUser['user_id'];
        $toUserName = $this->loginUser['user_name'];
        $platformId = C('basic_info.PLATFORM_ID');

        $this->load->service('fundOrder_service');
        $this->load->model('Fundorder_model');
        $this->load->model('Audit_model');
        $this->load->model('User_pwd_model');

        //验证支付密码是否正确
        if(empty($paypwd)){
            output_error(-2,'支付密码不正确');
            exit;
        }
        else{
            $aUser = $this->User_pwd_model->get_by_id($toUserId);
            if($aUser['pay_pwd']!=$paypwd)
            {
                output_error(-2,'支付密码不正确');
                exit;
            }
        }

        $title = C('OrderTypeName.3').' '.$amount.'元';

        $arrReturn = $this->fundorder_service->takeCash($title, $toUserId, $toUserName, $amount, C('PayMethod.Balance_Hand'), $netpayAccount, null, $ip, $platformId);
        if($arrReturn['code']==C('OrderResultError.Success')){
        	$fundOrderId = $arrReturn['fund_order_id'];
        	$aFundOrder = $this->Fundorder_model->get_by_id($fundOrderId);

        	if(!empty($aFundOrder) && $aFundOrder['status']==C('FundOrderStatus.Payed') ){
        		// 扣钱成功-->提交审核
        		$data = array('item_type'=>1,'item_id'=>$fundOrderId, 'user_id'=>$toUserId, 'audit_status'=>0, 'request_time'=>time(),'status'=>1,'platform_id'=>$platformId);
        		$this->Audit_model->sysAuditRequest($data);
        	}
        }

        $message = $arrReturn['errInfo'];
        if($arrReturn['code']=='Success')
        {
        	$message = '申请已提交';
            output_data();
        }
        else
        {
            output_error(-1,$message);
        }
        
//         $result = array('data'=>null,
//             'code'=>$arrReturn['code'],
//             'message'=>$message
//         );
//         echo json_encode($result);
    }

    /**
     * @param 修改支付密码
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
        $platform_id = $this->input->post('platform_id');
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $this->load->model('User_pwd_model');
        $config = array(
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
            array(
                'field'=>'platform_id',
                'label'=>'platform_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            $aUser = $this->User_pwd_model->get_by_id($user_id);
            if (empty($aUser))
            {
                //UESER_NOT_EXIST
                output_error(-1,'用户不存在');exit;
            }
            if ($old_pwd != $aUser['pay_pwd'])
            {
                //USER_OLDPWD_ERROR
                output_error(-1,'原密码错误');exit;
            }
            if ($pwd == $old_pwd)
            {
                //USER_PWD_NOCHANGE
                output_error(-1,'新密码不能与旧密码相同');exit;
            }
            if ($repwd != $pwd)
            {
                //USER_REPWD_DIFFERENCE
                output_error(-1,'确认密码与新密码不同');exit;
            }
            if (strlen($pwd) == 32)
            {
                $data['pay_pwd'] = $pwd;
                if ($this->User_pwd_model->update_by_id($user_id,$data))
                {
                    output_data();exit;
                }
                else 
                {
                    output_error(-1,'FAILED');exit;
                }
            }
            else
            {
                //USER_PWD_FORMAT_ERROR
                output_error(-1,'密码格式错误');exit;
            }
        }
        else 
        {
            if (empty($old_pwd))
            {
                //USER_OLDPWD_NULL
                output_error(-1,'旧密码不能为空');exit;
            }
            if (empty($pwd))
            {
                //USER_PWD_NULL
                output_error(-1,'密码不能为空');exit;
            }
            if (empty($repwd))
            {
                //USER_REPWD_NULL
                output_error(-1,'确认密码不能为空');exit;
            }
            // if (empty($platform_id))
            // {
            //     //USER_PLATFORMID_NULL
            //     output_error(-1,'USER_PLATFORMID_NULL');exit;
            // }
        }
    }
    
/**
     * @param 忘记支付密码
     *
     * @param $_POST['mobile']
     * @param $_POST['pwd']
     * @param $_POST['repwd']
     * @param $_POST['platform_id']
     * @param
     *
     * @return {"data":"","code":"USER_PWD_UPDATED","msg":"\u5bc6\u7801\u4fee\u6539\u6210\u529f"}
     */
    public function newpwd()
    {
        $pwd = $this->input->post('pwd');
        $repwd = $this->input->post('repwd');
        $platform_id = $this->input->post('platform_id');
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        
        $this->load->model('User_pwd_model');
        $config = array(
            array(
                'field'=>'pwd',
                'label'=>'pwd',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'repwd',
                'label'=>'repwd',
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
            $userInfo = $this->User_pwd_model->get_by_id($user_id);
            if (empty($userInfo))
            {
                //USER_NOT_EXIST
                output_error('-1','用户不存在');exit;
            }
            if ($pwd == $userInfo['pay_pwd'])
            {
                //USER_PWD_NO_SAME
                output_error('-1','密码不能与支付密码相同');exit;
            }
            if (strlen($pwd) == 32)
            {
                $data['pay_pwd'] = $pwd;
                if ($this->User_pwd_model->update_by_id($user_id,$data))
                {
                    $status['paypwd_status'] =1;
                    $this->load->model('User_model');
                    $this->User_model->update_by_id($user_id,$status);
                    output_data();
                }
                else
                {
                    output_error('-1','FAILED');exit;
                }
            }
            else
            {
                //USER_PWD_FORMAT_ERROR
                output_error('-1','密码格式不对');exit;
            }
        }
        else
        {
            if (empty($pwd))
            {
                //USER_PWD_NULL
                output_error('-1','密码不能为空');exit;
            }
            if (empty($repwd))
            {
                //USER_REPWD_NULL
                output_error('-1','确认密码不能为空');exit;
            }
            if (empty($latform_id))
            {
                //USER_PLATFORMID_NULL
                output_error('-1','平台id不能为空');exit;
            }
        }
    }
}
