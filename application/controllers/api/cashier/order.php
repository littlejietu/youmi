<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends TokenOAdminApiController {
    
	public function __construct()
    {
        parent::__construct();
        //$this->load->helper('goods');
        $this->load->model('trd/Order_model');
        $this->load->service('order_service');
        //$this->load->helper('Goods');
        $oAdmin = $this->oadminUser;
        $this->admin_id = $oAdmin['admin_id'];
        $this->site_id = $oAdmin['site_id'];
        $this->user_name = $oAdmin['user_name'];
        $this->name = $oAdmin['name'];
    }

    public function index(){
    	$token = $this->input->post('token');
        $order_sn = $this->input->post('order_sn');
        $type = $this->input->post('type');
        $pay_type = $this->input->post('pay_type');
        $cashier_id = $this->input->post('cashier_id');
        $begin_time = $this->input->post('begin_time');
        $end_time = $this->input->post('end_time'); 
    	$page = $this->input->post('page');
    	$pagesize = $this->input->post('pagesize');

        $buyer_userid = C('basic_info.TEMP_USER_ID'); //$this->oadminUser['admin_id'];
        $site_id = $this->oadminUser['site_id'];

        $this->load->model('trd/Order_oil_model');


        if(!$page) $page=1;
        if(!$pagesize) $pagesize=10;

    	$arrWhere = array('site_id'=>$site_id,'platform_id'=>C('basic_info.PLATFORM_ID'),'delete_status'=>1);
        if(!empty($order_sn))
            $arrWhere['order_sn'] = $order_sn;
        //if($type==1)
            $arrWhere['status'] = C('OrderPayStatus.Payed');
        //else if($type==2)
        //    $arrWhere['status'] = C('OrderPayStatus.UnPay');
        if($pay_type==1)
            $arrWhere['netpay_method'] = C('PayWXALI.WXPAY');
        else if($pay_type==2)
            $arrWhere['netpay_method'] = C('PayWXALI.ALIPAY');
        if(!empty($cashier_id))
            $arrWhere['cashier_id'] = $cashier_id;
        if(!empty($begin_time))
            $arrWhere['createtime >= '] = strtotime($begin_time);
        if(!empty($end_time))
            $arrWhere['createtime < '] = strtotime($end_time.' 23:59:59');

    	$aList = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'*','order_id desc');
        //echo $this->Order_model->db->last_query();
    	foreach ($aList['rows'] as $key => $a) {
    		$aItem = $aList['rows'][$key];

            $aItem['payed_time'] = !empty($aItem['payed_time'])?date('Y-m-d H:i:s', $aItem['payed_time']):'';
            $aItem['pay_method'] = !empty($aItem['netpay_method'])?C('PayMethodName.'.$aItem['netpay_method']):'';
    		/*//商品列表
    		$aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$a['order_id']),'goods_id,sku_id,title,num,pic_path,spec');
    		foreach ($aGoodsList as $kk => $aa) {
    			$aGoodsList[$kk]['pic_path'] = cthumb($aa['pic_path']);
    		}
            */
    		if( in_array($aItem['status'], array(C('OrderStatus.Create'),C('OrderStatus.WaitPay'))))
    			$aItem['status_name'] = '未支付';
    		else
    			$aItem['status_name'] = '已支付';
    		$aItem['goods'] = null;

            /*//油品
            $arrOil = null;
            $oilInfo = $this->Order_oil_model->get_by_id($a['order_id']);
            if(!empty($oilInfo)){
                $oil_name = getOilName($oilInfo['oil_no']);
                $arrOil = array('oil_name'=>$oil_name, 'oil_no'=>$oilInfo['oil_no'], 'oil_amt'=>$oilInfo['oil_amt'],'discount_amt'=>$oilInfo['act_discount']);
            }
            */
            $aItem['oil'] = null;//$arrOil;


    		$aList['rows'][$key] = $aItem;
    	}
    	$aList['page']=$page;
    	$aList['pagesize']=$pagesize;



        //收银员,已支付金额
        if($page==1){
            //收银员
            $this->load->model('oil/O_admin_model');
            $cashier_list = $this->O_admin_model->get_list(array('site_ids'=>"".$site_id,'is_cashier'=>1,'status'=>1),'id,name');
            $aList['cashiers'] = $cashier_list;

            //已支付金额
            if($type==2)
                $aList['order_payed_amt'] = 0;
            else{
                $arrWhereSum = array_merge($arrWhere, array('status'=>C('OrderPayStatus.Payed')));
                $aList['order_payed_amt'] = $this->Order_model->sum($arrWhereSum,'pay_amt');
            }

        }else{
            $aList['cashiers'] = null;
            $aList['order_payed_amt'] = null;
        }



        output_data($aList);

    }


    /**
    * 创建订单
    */
    public function create() {

        $gun_no = $this->input->post('gun_no');
        $oil_amt = $this->input->post('oil_amt');
        $goods_amt = $this->input->post('goods_amt');
        $scan_code = $this->input->post('scan_code');
        $paymethod = 0;
        $extparam = "{\"auth_code\":\"$scan_code\"}";
        $arrCashier = array('cashier_id'=>$this->admin_id, 'cashier_name'=>$this->name);
        if(in_array( intval(substr($scan_code, 0, 2)) , array(10,11,12,13,14,15))  )
            $paymethod = 13;
        else
            $paymethod = 15;

        $this->load->model('trd/Order_model');
        $this->load->service('printapi_service');


        $oil = '';
        if($oil_amt>0)
            $oil = $gun_no.',0,'.$oil_amt;
        $goods = '';
        if($goods_amt>0)
            $goods = '-1,'.$goods_amt.',1';
        $cart = array($this->site_id=>array('oil'=>$oil,'coupon'=>0,'activity'=>0,'goods'=>$goods));
        $address_id = 0;
        $ifcart =  0;
        $invoiceId = 0;
        $arrBuy = array('buyer_userid'=>C('basic_info.TEMP_USER_ID'),'buyer_username'=>C('basic_info.TEMP_USER_NAME'),'user_level'=>0);
        if($cart){
            $arrOrderIds = $this->order_service->createOrderList($cart, $arrBuy, $arrCashier, $address_id, $invoiceId, $ifcart);
            //同一个站点，产生唯一一个订单
            if(!empty($arrOrderIds) && count($arrOrderIds)==1){
                $orderIds = implode(',', $arrOrderIds);
                //刷卡支付
                $arrReturn = $this->order_service->gotoPay($orderIds, $paymethod, $extparam);

                $bPayed = true;
                $data = array();
                foreach ($arrReturn as $order_id => $v) {
                    if($v['code']!=C('OrderResultError.Success')){
                        $bPayed = false;
                        $data = $v;
                        break;
                    }

                    //推送订单
                    $this->printapi_service->orderprint_internal_push($order_id);
                }

                if($bPayed){
                    //订单详情
                    $feild = 'order_id,order_sn,title,pay_amt,discount_amt,coupon_amt,netpay_method,payed_time';
                    $orderInfo = $this->Order_model->get_by_id($orderIds, $feild);

                    $order_method = C('PayMethodName.'.$orderInfo['netpay_method']);
                    $order_time = !empty($orderInfo['payed_time'])?date('Y-m-d H:i:s',$orderInfo['payed_time']):'';
                    $data = array('order_method'=>$order_method,'order_time'=>$order_time);
                    $data = array_merge($data,$orderInfo);
                    output_data($data);
                    exit;
                }
                else{
                    if($data['code']=='NetPaying')
                        output_data($data);
                    else
                        output_error(-1,$data['errInfo']);
                }
                
            }
        }
        
        //output_error(-1,'ERROR');
        output_error('ERROR','错误');
    }

    public function detail(){
        $order_id = $this->input->post('order_id');
        $order_sn = $this->input->post('order_sn');
        $feild = 'order_id,order_sn,title,pay_amt,discount_amt,coupon_amt,netpay_method,payed_time';

        $orderInfo = null;
        if(!empty($order_id))
            $orderInfo = $this->Order_model->get_by_id($order_id, $feild);
        else
            $orderInfo = $this->Order_model->get_by_where(array('order_sn'=>$order_sn), $feild);
        if(!empty($orderInfo))
        {
            $order_method = C('PayMethodName.'.$orderInfo['netpay_method']);
            $order_time = !empty($orderInfo['payed_time'])?date('Y-m-d H:i:s',$orderInfo['payed_time']):'';
            $data = array('order_method'=>$order_method,'order_time'=>$order_time);
            $data = array_merge($data,$orderInfo);

            output_data($data);
        }else
            output_error('ERROR','错误');
    }

    public function printing(){
        $order_id = $this->input->post('order_id');
        $this->load->service('printapi_service');
        $data = $this->printapi_service->orderprint_data($order_id);

        if(!empty($data))
            output_data($data);
        else
            output_error('ERROR','错误');
    }


    /*
    public function paying(){
        $orderIds = $this->input->post('order_ids');
        $ip = $this->input->ip_address;//$this->input->post('ip');
        $paymethod = $this->input->post('paymethod');
        $extparam = $this->input->post('extparam');

        $this->load->service('printapi_service');

        //余额支付:验证支付密码是否正确
        if($paymethod==C('PayMethodType.AllBalance')){
            $user_id = $this->input->post('user_id');
            $paypwd = $this->input->post('paypwd');

            if(empty($paypwd)){
                output_error(-2,'支付密码不正确');//Pay_Password_Error
                exit;
            }
            else{
                $this->load->model('user/User_pwd_model');
                $aUser = $this->User_pwd_model->get_by_id($user_id);
                if($aUser['pay_pwd']!=$paypwd)
                {
                    output_error(-2,'支付密码不正确');//Pay_Password_Error
                    exit;
                }
            }
        }

        //刷卡支付
        if($paymethod==C('PayMethodType.WeixinPayMicro')){
            

        }


        $arrReturn = $this->order_service->gotoPay($orderIds, $paymethod, $extparam);

        $bPayed = true;
        $data = array();
        foreach ($arrReturn as $order_id => $v) {
            if($v['code']!='Success'){
                $bPayed = false;
                $data = $v;
                break;
            }

            //推送订单
            $this->printapi_service->orderprint_internal_push($order_id);
        }

        if($bPayed){
            output_data();
            exit;
        }
        else{
            if($data['code']=='NetPaying')
                output_data($data);
            else
                output_error(-1,$data['errInfo']);
            // $result = array('data'=>$data,
            //     'code'=>'-1',
            //     'msg'=>$data['code'],
            //     'action'=>'m_order_paying'
            // );

            // echo json_encode($result);
            // exit;
        }

    }
    */

    public function close(){
        $orderId = $this->input->post('order_id');

        $bResult = $this->order_service->close($orderId);
        if($bResult)
            output_data();
        else
            output_error('FAIL','关闭失败');
    }

    public function refund(){
        $order_id = $this->input->post('order_id');
        $pwd = $this->input->post('pwd');

        $this->load->model('oil/O_admin_model');

        if(empty($order_id) || empty($pwd)){
            output_error('NOT_NULL','订单id,密码不能为空');
            exit;
        }


        $aAdmin = $this->O_admin_model->get_by_id($this->admin_id);
        if($aAdmin['password']!=md5($pwd) )
        {
            output_error('PWDERR','密码不正确');//Pay_Password_Error
            exit;
        }

        $bResult = $this->order_service->close($order_id);
        if($bResult)
            output_data();
        else
            output_error('FAIL','关闭失败');

    }

    public function del(){
        $orderId = $this->input->post('order_id');

        $bResult = $this->order_service->delete($orderId);
        if($bResult)
            output_data();
        else
            output_error('FAIL','失败');

    }


}