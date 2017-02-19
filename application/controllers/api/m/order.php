<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends TokenApiController {
    
	public function __construct()
    {
        parent::__construct();
        //$this->load->helper('goods');
        $this->load->model('trd/Order_model');
        $this->load->service('order_service');
        //$this->load->helper('Goods');
    }

    public function index(){
    	$token = $this->input->post('token');
    	$page = $this->input->post('page');
    	$pagesize = $this->input->post('pagesize');
        $type = $this->input->post('type');
        $buyer_userid = $this->loginUser['user_id'];

        $this->load->model('oil/Site_model');
    	// $this->load->model('Shot_goods_model');

        if(!$page) $page=1;
        if(!$pagesize) $pagesize=10;

    	$arrWhere = array('buyer_userid'=>$buyer_userid,'platform_id'=>C('basic_info.PLATFORM_ID'),'delete_status'=>1);
        if($type==1)
            $arrWhere['status'] = array(C('OrderStatus.Create'),C('OrderStatus.WaitPay'));
        else if($type==2)
            $arrWhere['status'] = "'".C('OrderStatus.WaitSend')."'";
        else if($type==3)
            $arrWhere['status'] = "'".C('OrderStatus.WaitConfirm')."'";
        else if($type==4)
        {
            $arrWhere['status'] = "'".C('OrderStatus.Finished')."'";
            $arrWhere['comment_status'] = 0;
        }

    	$aList = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'*','order_id desc');
    	foreach ($aList['rows'] as $key => $a) {
    		$aItem = $aList['rows'][$key];
    		//商品列表
    		// $aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$a['order_id']),'goods_id,sku_id,title,num,pic_path,spec');
    		// foreach ($aGoodsList as $kk => $aa) {
    		// 	$aGoodsList[$kk]['pic_path'] = cthumb($aa['pic_path']);
    		// }
    		if($aItem['status']==C('OrderStatus.Finished'))
    		{
    			if($aItem['comment_status']==0)
    				$aItem['status_name'] = '待评价';
    			else
    				$aItem['status_name'] = '已评';
    		}
    		else
    			$aItem['status_name'] = C('OrderStatusName.'.$aItem['status']);
    		// $aItem['goods'] = $aGoodsList;

            $site_info = $this->Site_model->get_by_id($aItem['site_id']);
            if(!empty($site_info))
                $aItem['site_name'] = $site_info['site_name'];


    		$aList['rows'][$key] = $aItem;
    	}
    	$aList['page']=$page;
    	$aList['pagesize']=$pagesize;

        output_data($aList);
//    	$result = array('data'=>$aList,

//		    'code'=>'1',
//		    'msg'=>'SUCCESS',
//            'action'=>'m_order'
//		);
//	    echo json_encode($result);

    }

    public function paying(){
        $user_id = $this->loginUser['user_id'];

        $orderIds = $this->input->post('order_ids');
        $ip = $this->input->ip_address;//$this->input->post('ip');
        $paymethod = $this->input->post('paymethod');
        $paypwd = $this->input->post('paypwd');
        $extparam = $this->input->post('extparam');

        $this->load->service('printapi_service');
        
        //验证支付密码是否正确
        if($paymethod==C('PayMethodType.AllBalance')){
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
        }else{
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

    public function cash(){
        $goods_amt = $this->input->post('goods_amt');
        $site_id = $this->input->post('site_id');
        $paymethod = 12;

        $this->load->model('trd/Order_model');
        $this->load->service('printapi_service');


        $oil = '';
        $goods = '';
        if($goods_amt>0)
            $goods = '-1,'.$goods_amt.',1';
        $cart = array($site_id=>array('oil'=>$oil,'coupon'=>0,'activity'=>0,'goods'=>$goods));
        $address_id = 0;
        $ifcart =  0;
        $invoiceId = 0;
        $arrBuy = array('buyer_userid'=>$this->loginUser['user_id'],'buyer_username'=>$this->loginUser['user_name'],'user_level'=>0);
        if($cart){
            $arrOrderIds = $this->order_service->createOrderList($cart, $arrBuy, null, $address_id, $invoiceId, $ifcart);
            //同一个站点，产生唯一一个订单
            if(!empty($arrOrderIds) && count($arrOrderIds)==1){
                $orderIds = implode(',', $arrOrderIds);
                //刷卡支付
                $arrReturn = $this->order_service->gotoPay($orderIds, $paymethod, null);

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
                    output_data();
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
    }

    public function close(){
        $orderId = $this->input->post('order_id');

        $bResult = $this->order_service->close($orderId);

        $result = array('data'=>null,'code'=>'-1','msg'=>'Failure','action'=>'m_order_close');

        if($bResult)
            $result = array('data'=>null,'code'=>'1','msg'=>'Success','action'=>'m_order_close');

        echo json_encode($result);
        exit;
    }

    public function del(){
        $orderId = $this->input->post('order_id');

        $bResult = $this->order_service->delete($orderId);

        $result = array('data'=>null,'code'=>'-1','msg'=>'Failure','action'=>'m_order_delete');

        if($bResult)
            $result = array('data'=>null,'code'=>'1','msg'=>'Success','action'=>'m_order_delete');

        echo json_encode($result);
        exit;

    }

    //确认收货
    public function confirm(){
        $orderId = $this->input->post('order_id');

        $result = array();

        $arrReturn = $this->order_service->finishOrder($orderId);
        if($arrReturn['code'] == C('OrderResultError.Success')){
            $result = array('data'=>null,'code'=>'1','msg'=>'成功','action'=>'m_order_confirm'); //Success
        }
        else
            $result = array('data'=>$arrReturn,'code'=>'-1','msg'=>$arrReturn['errInfo'],'action'=>'m_order_confirm'); //$arrReturn['code']


        echo json_encode($result);
        exit;

    }

    /**订单详细信息*/
    public function detail(){

        // $this->load->model('Order_refunds_model'); //退款列表
        // $this->load->model('Coupon_model');
        // $this->load->model('Coupon_User_model');

        $orderId = $this->input->post('order_id');
        // $this->load->model(array('Order_detail_model','Area_model','Shot_goods_model','Deliver_order_log_model') );
        // $this->load->model('Area_model');
        // $this->load->model('Shot_goods_model');
        $this->load->model('trd/Order_oil_model');
        $this->load->model('oil/Site_model');


        $aOrder = $this->Order_model->get_by_id($orderId);
        $aOrderOil = $this->Order_oil_model->get_by_id($orderId);
        if(empty($aOrder)){
            output_error(-1,'订单不存在');//ORDER_NOT_EXIST
        }
        if($aOrder['buyer_userid']!=$this->loginUser['user_id']){
            output_error(-1,'订单不存在');//ORDER_NOT_EXIST
        }

        $aOrder['status_name'] = C('OrderStatusName.'.$aOrder['status']);

        $info = $this->Site_model->get_by_id($aOrder['site_id']);
        if(!empty($info))
            $aOrder['site_name'] = $info['site_name'];

        if(!empty($aOrderOil))
            $aOrder = array_merge($aOrderOil, $aOrder);


        /*
        //商品列表
        $aGoodsList = $this->Order_goods_model->get_list( array('order_id'=>$orderId),'id as order_goods_id,goods_id,sku_id,price,real_price,num','sort');
        foreach ($aGoodsList as $k => $a) {

            //根据订单产品信息读取是否有退款信息
            $refundsInfo = $this->Order_refunds_model->get_by_where('order_goods_id = '.$a['order_goods_id'],'status');

            if(!empty($refundsInfo)){
                $aGoodsList[$k]['refunds_status'] = $refundsInfo['status'];
            }else{
                $aGoodsList[$k]['refunds_status'] = 0;
            }

            $aGoddsInfo = $this->Shot_goods_model->get_by_where( array('order_id'=>$orderId,'goods_id'=>$a['goods_id'],'sku_id'=>$a['sku_id'] ),'title,pic_path,spec' );
            if(!empty($aGoddsInfo)){
                $aGoodsList[$k]['title'] = $aGoddsInfo['title'];
                $aGoodsList[$k]['spec'] = str_replace('_', ' ', $aGoddsInfo['spec']);
                $aGoodsList[$k]['pic_path'] = cthumb($aGoddsInfo['pic_path']);
            }
            else{
                $aGoodsList[$k]['title'] = null;
                $aGoodsList[$k]['spec'] = null;
                $aGoodsList[$k]['pic_path'] = null;
            }
        }
        $aGoodsList = empty($aGoodsList)?null:$aGoodsList;
        



        //优惠券
        $aCoupon = null;
        if($order_coupon_id>0){
            $aCouponUser = $this->Coupon_User_model->get_by_id($order_coupon_id,'coupon_id');
            //print_r($aCouponUser);
            //echo $this->Coupon_User_model->db->last_query();
            //die;
            if(!empty($aCouponUser))
                $aCoupon = $this->Coupon_model->get_by_id($aCouponUser['coupon_id'],'coupon_name,price');
        }
        */

        output_data(
            array(
                'order_detail' => $aOrder,
                // 'goods' => $aGoodsList,
                // 'coupon' => $aCoupon,
            )
        );
    }

    

}