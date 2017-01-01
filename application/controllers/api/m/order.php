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

    	$this->load->model('Shot_goods_model');

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
    		$aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$a['order_id']),'goods_id,sku_id,title,num,pic_path,spec');
    		foreach ($aGoodsList as $kk => $aa) {
    			$aGoodsList[$kk]['pic_path'] = cthumb($aa['pic_path']);
    		}
    		if($aItem['status']==C('OrderStatus.Finished'))
    		{
    			if($aItem['comment_status']==0)
    				$aItem['status_name'] = '待评价';
    			else
    				$aItem['status_name'] = '已评';
    		}
    		else
    			$aItem['status_name'] = C('OrderStatusName.'.$aItem['status']);
    		$aItem['goods'] = $aGoodsList;


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

        $this->load->model('Order_refunds_model'); //退款列表
        $this->load->model('Coupon_model');
        $this->load->model('Coupon_User_model');

        $orderId = $this->input->post('order_id');
        $this->load->model(array('Order_detail_model','Area_model','Shot_goods_model','Deliver_order_log_model') );
        // $this->load->model('Area_model');
        // $this->load->model('Shot_goods_model');
        $this->load->service('deliver_service');

        $aOrder = $this->Order_model->get_by_id($orderId);
        if(empty($aOrder)){
            output_error(-1,'订单不存在');//ORDER_NOT_EXIST
        }

        //收货地址
        $aOrderDetail = $this->Order_detail_model->get_by_id($orderId);
        if(!empty($aOrderDetail)){
            
            $aOrderDetail['delivery_way'] = C('DeliveryWay.'.$aOrderDetail['delivery_way']);
            
            /*$aArea = $this->Area_model->getAreaList(
                array(
                    'id' => array(
                        $aOrderDetail['province_id'],
                        $aOrderDetail['city_id'],
                        $aOrderDetail['area_id'],
                    ),
                ),
                'name',
                'deep asc'
            );
            $aOrderDetail['province'] = !empty($aArea[0]['name'])?$aArea[0]['name']:'';
            $aOrderDetail['city'] = !empty($aArea[1]['name'])?$aArea[1]['name']:'';
            $aOrderDetail['area']= !empty($aArea[2]['name'])?$aArea[2]['name']:'';
            $aOrderDetail['address'] = $aOrderDetail['address'];*/

            $aOrderDetail['province'] = $aOrderDetail['province_name']==$aOrderDetail['city_name']?'':$aOrderDetail['province_name'];
            $aOrderDetail['city'] = $aOrderDetail['city_name'];
            $aOrderDetail['area'] = $aOrderDetail['area_name'];
        }
        $aOrderDetail['pay_type'] = C('PayType.'.$aOrder['pay_type']);
        $aOrderDetail['goods_amt'] = $aOrder['total_amt']-$aOrder['fare_amt'];
        $aOrderDetail['fare_amt'] = $aOrder['fare_amt'];
        $aOrderDetail['pay_amt'] = $aOrder['pay_amt'];
        $aOrderDetail['createtime'] = $aOrder['createtime'];
        $aOrderDetail['status'] = $aOrder['status'];
        $aOrderDetail['comment_status'] = $aOrder['comment_status'];
        $aOrderDetail['coupon_amt'] = $aOrder['coupon_amt'];
        $aOrderDetail['order_sn'] = $aOrder['order_sn'];
        $order_coupon_id = $aOrder['coupon_id'];
        unset($aOrder);

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

        //配送日志
        $aDeliverLogList = $this->deliver_service->getOrderLog($orderId);

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

        output_data(
            array(
                'order_detail' => $aOrderDetail,
                'goods' => $aGoodsList,
                'coupon' => $aCoupon,
                'deliver_log' => $aDeliverLogList
            )
        );
    }

    /**
     * 获取订单详情
     */
    public function get_refundsinfo() {

        $this->load->model('Order_goods_model'); //订单产品信息表
        $this->load->model('Order_refunds_reason_model'); //退了留言
        $this->load->model('Order_refunds_model'); //退款列表
        $this->load->model('Shop_model'); //
        $this->load->model('Area_model'); //

        $order_goods_id  = $this->input->post('order_goods_id');//订单产品真实主键ID

        //退款流程状态
        $status  = 1;

        //根据订单快照ID获取产品信息
        $goodsInfo = $this->Order_goods_model->get_by_id($order_goods_id,'id as order_goods_id,order_id,goods_id,real_price,num');

        if(empty($goodsInfo)){
            output_error('-1','ID不正确');//ID_ERROR
        }

        //退款理由列表
        $reasonList = $this->Order_refunds_reason_model->get_list(array(),'id,title');
        // $aGoodsList['pic_path'] = cthumb($aGoodsList['pic_path']);
        $goodsInfo['num']  = empty($goodsInfo['num'])?1:$goodsInfo['num'];
        $goodsInfo['total_amount'] = $goodsInfo['num'] * $goodsInfo['real_price'];

        $data = array(
            'reasonList'    => $reasonList, //退款理由
            'goodsInfo'     => $goodsInfo,  //订单产品详细信息详细
            );

        //读取退款信息
        $refundsInfo = $this->Order_refunds_model->get_by_where('order_goods_id = '.$order_goods_id,'id,order_goods_id,goods_id,order_id,shop_id,refunds_money,reason_id,reason_content,status,addtime');

        if(!empty($refundsInfo)){

            //退款理由
          $reasonInfo =  $this->Order_refunds_reason_model->get_by_id($refundsInfo['reason_id']);
          $refundsInfo['reason_name'] = $reasonInfo['title'];
          $data['refundsInfo'] = $refundsInfo; //退款详细信息

            //获取退款地址
         $shopInfo =   $this->Shop_model->get_by_id($refundsInfo['shop_id'],'province_id,city_id,area_id,address,name');
            $whereArea =  array(
                'id' => array(
                    $shopInfo['province_id'],
                    $shopInfo['city_id'],
                    $shopInfo['area_id'],
                ),
            );

            unset( $shopInfo['province_id']);
            unset( $shopInfo['city_id']);
            unset( $shopInfo['area_id']);

            $aArea = $this->Area_model->getAreaList($whereArea,'name','deep asc');
            $shopInfo['province']       = !empty($aArea[0]['name'])?$aArea[0]['name']:'';
            $shopInfo['city']           = !empty($aArea[1]['name'])?$aArea[1]['name']:'';
            $shopInfo['area']           = !empty($aArea[2]['name'])?$aArea[2]['name']:'';
            $shopInfo['address']        = $shopInfo['address']; //详细地址
            $shopInfo['seller_name']    = $shopInfo['name'];//店铺名称
            $data['shopInfo']           = $shopInfo; //退货地址
        }

        output_data($data);
    }

    /**
     * 订单退款申请
     */
    public function refunds(){
        $order_goods_id = $this->input->post('order_goods_id');//订单产品详细信息
        $refunds_money  = $this->input->post('refunds_money'); //退款金额
        $reason_id      = $this->input->post('reason_id'); //退款申请主键ID
        $reason_content = $this->input->post('reason_content');
        $pic            = $this->input->post('pic');

        $user_id = $this->loginUser['user_id'];

        $this->load->model('Order_model');
        $this->load->model('Order_refunds_model'); //退款申请
        $this->load->model('Order_goods_model'); //订单产品信息表
        $this->load->model('Goods_model'); //订单产品信息表

        #region 判断不能为空
        if(empty($order_goods_id))
            output_error('-1','申请id为空');       //ORDER_GOODS_ID_NULL
        
        if(empty($refunds_money))
            output_error('-1','退款金额不能为空');     //REFUNDS_MONEY
        
        if(empty($reason_id))
            output_error('-1','申请理由不能为空');        //REASON_ID_NULL
        #endregion

       // 判断退款订单是否重复提交
        $ordergoodsInfo = $this->Order_refunds_model->get_by_where( array('order_goods_id'=>$order_goods_id) );
        //此种商品,申请已存在
        if(!empty($ordergoodsInfo))
            output_error('-1','ORDER_GOODS_REPEAT');

        $aOrderGoods = $this->Order_goods_model->get_by_id($order_goods_id,'id as order_goods_id,order_id,goods_id,real_price,real_price,num');
        //判断订单是否处于已完成状态
        $aOrder = $this->Order_model->get_by_id($aOrderGoods['order_id']);

        if(empty($aOrder))
            output_error('-1','订单不存在');    //ORDER_NOT_EXIST
        else if($aOrder['status']!=C('OrderStatus.Finished'))
            output_error('-1','订单未确认收货,需先确认收货');    //ORDER_NOT_FINISHED

        
        $goodsinfo = $this->Goods_model->get_by_id($aOrderGoods['goods_id'],'shop_id');

        //申请金额是否超过商品金额
        $totalPrice = $aOrderGoods['real_price'] * $aOrderGoods['num'];
        if($totalPrice<$refunds_money)
            output_error('-1','申请金额超过商品金额');       //REFUNDSPRICE_REPEAT

        $inData = array(
            'order_goods_id'    => $order_goods_id,
            'user_id'           => $user_id,
            'shop_id'           => $aOrder['shop_id'],
            'order_id'          => $aOrderGoods['order_id'],
            'goods_id'          => $aOrderGoods['goods_id'],
            'refunds_money'     => $refunds_money,
            'reason_id'         => $reason_id,
            'reason_content'    => $reason_content,
            'pic'               => $pic,
            'status'            => 2,
            'addtime'           => time(),
            'lasttime'          => time(),
        );

        $this->Order_refunds_model->insert_string($inData);

        output_data($aOrderGoods);
    }

    /**
     * 取消退款订单
     */
    public function cancelorder(){
        $this->load->model('Order_refunds_model'); //退款列表
        $id        = $this->input->post('id');//退款主键ID

        if(empty($id)){

            output_error(-1,'退款id不能为空');//ID_NULL
            exit;
        }

        $this->Order_refunds_model->update_by_id($id,array('status' => 5));
        output_data();
    }

    /**
     * 退货列表
     */
    public function refundslist(){

        $this->load->model('Order_refunds_model'); //退款列表
        $this->load->model('Shot_goods_model'); //订单产品信息表
        $page     = !empty($page)?$page:1;//接收前台的页码
        $pagesize = !empty($pagesize)?$pagesize:999;
        $arrParam = array();
        $arrWhere = array('user_id' => $this->loginUser['user_id']);
        $strOrder = 'lasttime desc';

        $list = $this->Order_refunds_model->fetch_page($page,$pagesize,$arrWhere,'id as order_goods_id,order_goods_id,goods_id,status,addtime',$strOrder);

        foreach($list['rows'] as $k => $v){

            $aList = $list['rows'][$k];
            $orderInfo = $this->Order_goods_model->get_by_id($v['order_goods_id']);
            $goodsInfo = $this->Goods_model->get_by_id($v['goods_id']);

            $aList['title'] = $goodsInfo['title'];
            $aList['pic_path'] = cthumb($goodsInfo['pic_path']);
            $aList['price'] = $orderInfo['price'];
            $aList['num'] = $orderInfo['num'];
            unset($aList['goods_id']);
            $list['rows'][$k] = $aList;
        }

        $list['page']=$page;
        $list['pagesize']=$pagesize;

        output_data($list);
    }

}