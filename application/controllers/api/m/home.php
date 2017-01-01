  <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends TokenApiController { 
	public function __construct() {
		parent::__construct();
	}

	public function index(){
		$user_id = $this->loginUser['user_id'];

		$where = array('buyer_userid'=>$user_id,'delete_status'=>1);

		$this->load->model('Order_model');
		$this->load->model('Account_model');
		$this->load->service('coupon_service');
		$this->load->model('Order_refunds_model');

		if(empty($user_id))
		{
			output_error(-1,'用户不存在');
		}

		$feild = "IFNULL( SUM( CASE STATUS WHEN 'Create' THEN 1 WHEN 'WaitPay' THEN 1 ELSE 0 END),0 ) AS 'waitpay_num', IFNULL( SUM( CASE STATUS WHEN 'WaitSend' THEN 1 ELSE 0 END),0) AS 'waitsend_num', IFNULL(SUM( CASE STATUS WHEN 'WaitConfirm' THEN 1 ELSE 0 END),0) AS 'waitconfirm_num', IFNULL(SUM( CASE WHEN STATUS='Finished' AND comment_status=0 THEN 1 ELSE 0 END),0) AS 'waitcomment_num'";

		$aNum = $this->Order_model->get_by_where($where, $feild);
		$status ='2,3,4,6';
		$refundInfo = $this->Order_refunds_model->get_by_where('user_id='. $user_id.' and status in(2,3,4,6)','count(*) as count');

		$refund_num = $refundInfo['count'];

		//钱包
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
		$coupon_num = count($this->coupon_service->get_usable_coupons($user_id));

		$data = array('acct_balance'=>$acct_balance,'acct_integral'=>$acct_integral,'coupon_num'=>$coupon_num,'refund_num' => $refund_num);

		$aNum = array_merge($data, $aNum);

		output_data($aNum);

	}
}