<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends BaseSellerController {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('goods_helper');
        $this->load->model('Shot_goods_model');
        $this->load->model('Shop_model');
        $this->load->model('Order_detail_model');
        $this->load->model('Order_model');
        $this->load->model('Area_model');
        $this->load->model('Order_goods_model');
        $this->load->model('Goods_model');
        $this->load->model('Order_refunds_model');
    }

    public function index() {

        $type = $this->input->post_get('type');

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('shop_id' => $this->loginUser['shop_id'], 'delete_status'=>1,'platform_id'=>C('basic_info.PLATFORM_ID'));
        $order_by = 'order_id desc';

        $query_start_date = $this->input->post_get('query_start_date');
        $query_end_date = $this->input->post_get('query_end_date');

        if(!empty($query_start_date)){
            $arrWhere['createtime >= ']  = strtotime($query_start_date);
            $arrParam['query_start_date'] = $query_start_date;
        }

        if(!empty($query_end_date)){
            $arrWhere['createtime <= ']  = strtotime($query_end_date.' 23:59:59');
            $arrParam['query_end_date'] = $query_end_date;
        }

        $order_sn = $this->input->post_get('order_sn');
        if(!empty($order_sn)){
            $arrWhere['order_sn']  = "'$order_sn'";
            $arrParam['order_sn'] = $order_sn;
        }
        
        if($type==1)
            $arrWhere['status'] = array(C('OrderStatus.Create'),C('OrderStatus.WaitPay'));
        else if($type==2)
            $arrWhere['status'] = "'".C('OrderStatus.WaitSend')."'";
        else if($type==3)
            $arrWhere['status'] = "'".C('OrderStatus.WaitConfirm')."'";
        else if($type==4)
            $arrWhere['status'] = "'".C('OrderStatus.Finished')."'";
        else if($type==5)
            $arrWhere['status'] = "'".C('OrderStatus.Closed')."'";
        else if($type==6){
            $prefix = $this->Order_model->prefix();
            $arrWhere['order_id IN'] = " (SELECT order_id FROM ".$prefix."trd_order_refunds WHERE STATUS NOT IN(1,5,6)) ";
        }

        if(!empty($type))
            $arrParam['type'] = $type;

        $list = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'*',$order_by);
        //echo $this->Order_model->db->last_query();die;

        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/order', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

        foreach($list['rows'] as $k => $v ){

            //商品列表
            $aGoodsList = $this->Order_goods_model->get_list(array('order_id'=>$v['order_id']),'id,price,num,order_id,goods_id,sku_id');

            foreach ($aGoodsList as $kk => $aa) {
                $aGoodsInfo = $this->Shot_goods_model->get_by_where('order_id = '.$aa['order_id'].' and goods_id = '.$aa['goods_id'] ,'title,pic_path');
                $aGoodsList[$kk]['pic_path'] = $aGoodsInfo['pic_path'];
                $aGoodsList[$kk]['title'] = $aGoodsInfo['title'];

                //读取当前产品退款状态
                $refundsInfo = $this->Order_refunds_model->get_by_where('order_goods_id = '.$aa['id'],'status');
                $aGoodsList[$kk]['refunds_status'] = $refundsInfo['status'];
            }

            $list['rows'][$k]['GoodsList'] = $aGoodsList;

            $orderDetailInfo = $this->Order_detail_model->get_by_id($v['order_id']);
            if(!empty($orderDetailInfo)){
                $list['rows'][$k]['real_name']  = $orderDetailInfo['real_name'];
                $list['rows'][$k]['mobile']  = $orderDetailInfo['mobile'];
                $list['rows'][$k]['phone']  = $orderDetailInfo['phone'];
                $list['rows'][$k]['address']  = $this->Area_model->getAreaId($orderDetailInfo['province_id']) . $this->Area_model->getAreaId($orderDetailInfo['city_id']) . $this->Area_model->getAreaId($orderDetailInfo['area_id']) . $orderDetailInfo['address'];
            }
        }

        $data = array(
            'list' => $list,
            'arrParam' => $arrParam,
            'type'     => $type,
            'order_sn'  => $order_sn,
            'query_start_date'  => $query_start_date,
            'query_end_date'  => $query_end_date,
            'output'=>array('loginUser'=>$this->loginUser),
        );
        $this->load->view('seller/order',$data);
    }

    /**
     * 发货
     */
    public function order_deliver(){
        $order_id = $this->input->post_get('order_id');
        //获取订单信息

        $orderInfo = $this->Order_model->get_by_id($order_id);

        //商品列表
        $aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$order_id),'goods_id,sku_id,title,price,num,pic_path,spec');
        foreach ($aGoodsList as $kk => $aa) {
            $aGoodsList[$kk]['pic_path'] = cthumb($aa['pic_path']);
        }

        $orderDetailInfo = $this->Order_detail_model->get_by_id($order_id);
        if(!empty($orderDetailInfo)){
            $orderDetailInfo['real_name']   = $orderDetailInfo['real_name'];
            $orderDetailInfo['mobile']      = $orderDetailInfo['mobile'];
            $orderDetailInfo['phone']       = $orderDetailInfo['phone'];
            $provice_name = $orderDetailInfo['province_name']==$orderDetailInfo['city_name']?'':$orderDetailInfo['province_name'];
            $orderDetailInfo['address']     = $provice_name.$orderDetailInfo['city_name'].$orderDetailInfo['area_name'].$orderDetailInfo['address'];
        }

        $shopInfo = $this->Shop_model->get_by_id($orderInfo['shop_id']);
        if(!empty($shopInfo)) {
            $provice_city = "";
            $provice_name = $this->Area_model->getAreaId($shopInfo['province_id']);
            $provice_city = $provice_name;

            $city_name = $this->Area_model->getAreaId($shopInfo['city_id']);
            $orderDetailInfo['seller_address']     =  $provice_city . $this->Area_model->getAreaId($shopInfo['area_id']) . $shopInfo['address'];
        }

        #region  读取待配送 订单列表信息

        $this->load->model('Order_goods_model');
        $this->load->model('Order_package_model');  //订单快递表
        $this->load->model('Shot_goods_model');  //产品主表

        $orderGoodsList = $this->Order_goods_model->get_list(array('order_id' => $order_id));

        $nineBlocks = array(); //九号截取配送
        $mainDesk = array(); //后台发布
        $this->load->helper('Goods');
        foreach($orderGoodsList as $k => $v){
            $shotGoodsInfo = $this->Shot_goods_model->get_by_where('order_id ='.$v['order_id'].' and goods_id = '.$v['goods_id'].' and sku_id = '.$v['sku_id']);
            $packagInfo = $this->Order_package_model->get_list(array('order_goods_id' => $v['id']));
            //根据ordergoods获取产品配送信息
            foreach($packagInfo as $kk => $vv){
                //根据配送类型 组合数据
                switch($vv['deliver_way']){
                    case 1: //九号街区
                        $nineBlocks[] = array(
                            'order_goods_id'    => $v['id'],  //order_goods_id
                            'packs_id'          => $vv['id'],//订单产品配送表
                            'title'             => $shotGoodsInfo['title'], //产品名称
                            'pic_path'          => cthumb($shotGoodsInfo['pic_path']),
                            'num'               => $vv['num'], //配送数量
                            'deliver_way'       => $vv['deliver_way'], //配送方式
                            'status'            => $vv['status'], //配送方式
                            'spec'              => $shotGoodsInfo['spec'], //配送方式
                        );
                        break;
                    case 2: //快递
                        $mainDesk[] = array(
                            'order_goods_id'    => $v['id'],  //order_goods_id
                            'packs_id'          => $vv['id'],//订单产品配送表
                            'title'             => $shotGoodsInfo['title'], //产品名称
                            'pic_path'          => cthumb($shotGoodsInfo['pic_path']),
                            'num'               => $vv['num'], //配送数量
                            'deliver_way'       => $vv['deliver_way'], //配送方式
                            'status'            => $vv['status'], //配送方式
                            'spec'              => $shotGoodsInfo['spec'], //配送方式
                        );
                        break;
                    case 3: //混合配送--暂无
                        break;
                }
            }
        }

        #endregion

        $data = array(
            'orderInfo'             => $orderInfo,
            'aGoodsList'            => $aGoodsList,
            'orderDetailInfo'       => $orderDetailInfo,
            'order_id'              => $order_id,
            'nineBlocks'            => $nineBlocks,
            'mainDesk'              => $mainDesk,
            'output'=>array('loginUser'=>$this->loginUser),
        );
        $this->load->view('seller/order_deliver',$data);
    }

    /**
     * 发货提交
     */
    public function save_order_deliver(){

        $hidtype  = $this->input->post('hidtype');
        $order_id = $this->input->post('order_id');
        $ship_memo = $this->input->post('deliver_explain'); //发货备注
        $this->load->service('order_service');

        //判断订单是否重复发货
        $orderInfo = $this->Order_model->get_by_id($order_id);
        if($hidtype==1 && !empty($orderInfo)&& $orderInfo['status']!='WaitSend'){

            showDialog('订单已发货或已完成！！！', ADMIN_SITE_URL.'/order/detail?id='.$order_id);
            exit;
        }elseif($hidtype==2 && !empty($orderInfo)&& !empty($orderInfo['deliver_status'])){

            showDialog('订单已发过货！！！', ADMIN_SITE_URL.'/order/detail?id='.$order_id);
            exit;
        }

        if(!empty($ship_memo)){

          $this->Order_detail_model->update_by_where('order_id = '.$order_id,array('ship_memo' => $ship_memo));
        }

        if($hidtype==1) {  //快递配送

            $couriertype    = $this->input->post('couriertype');
            $courierno      = $this->input->post('courierno');
            $content        = $this->input->post('content');

            $data = array(
                'logistic'              => $couriertype,
                'logisticnumber'        => $courierno,
                'logisticcontent'       => $content,
            );

            $this->Order_detail_model->update_by_where('order_id = '.$order_id,$data);
            $this->Order_model->update_by_where('order_id = '.$order_id,array( 'status'=> 'WaitConfirm'));

        } else {  //接单员配送

            //根据订单ID查找
            $this->load->model('Order_goods_model');
            $this->load->model('Order_package_model');  //订单快递表

            $mainDesk = array(); //后台发布
            $orderGoodsList = $this->Order_goods_model->get_list(array( 'order_id' => $order_id,'goods_id'));
            foreach($orderGoodsList as $k => $v) {

                $packagInfo = $this->Order_package_model->get_list(array('order_goods_id' => $v['id']));
                //根据ordergoods获取产品配送信息
                foreach($packagInfo as $kk => $vv) {
                    //根据配送类型 组合数据
                    switch($vv['deliver_way']) {
                        case 1: //九号街区配送
                        //修改配送状态
                        $this->Order_package_model->update_by_id($vv['id'],array('status' => 1));
                        //修改订单派送状态
                        $this->Order_model->update_by_id($order_id,array('deliver_status' => 'WaitDeliver'));
                            break;
                        case 2: //快递
                            $mainDesk[] = $vv;
                            break;
                        case 3: //混合配送--暂无
                            break;
                    }
                }
            }

            $this->order_service->deliverstatus($order_id);

            // 查询商铺 配货状态
            $num =0;
            if(!empty($mainDesk)){
                foreach($mainDesk as $k => $v){
                    if($v['status'] == 0){
                        $num++;
                    }
                }
            }

            if($num < 1) {

                $this->order_service->deliver($order_id);
            }
        }

        //  站内信发送
        $orderDetail = $this->Order_detail_model->get_by_where('order_id = '.$order_id,'buyer_userid');

        if(!empty($orderDetail) &&!empty($orderDetail['buyer_userid'])) {
            $this->load->service('message_service');
            $tpl_id = 1;
            //$sender_id = 0;
            $receiver = $orderDetail['buyer_userid'];
            $receiver_type = 6;
            $arrParam = array('{order_sn}'=>$orderInfo['order_sn']);

            $this->message_service->send_sys($tpl_id,$receiver,$receiver_type,$arrParam);
        }

        showDialog('发货成功！',SELLER_SITE_URL.'/Order/order_deliver?order_id='.$order_id,'succ');
        //redirect(SELLER_SITE_URL.'/Order/order_deliver?order_id='.$order_id);
        exit;
    }

    /**
     * 订单详情
     */
    public function orderinfo(){
        $order_id = $this->input->post_get('order_id');
        $this->load->model('Order_refunds_reason_model');
        $this->load->model('Order_package_model');
        //获取订单信息
        $orderInfo = $this->Order_model->get_by_id($order_id);
        $orderDetailInfo = $this->Order_detail_model->get_by_id($order_id);
        if(!empty($orderDetailInfo)){
            $orderInfo['real_name']   = $orderDetailInfo['real_name'];
            $orderInfo['mobile']      = $orderDetailInfo['mobile'];
            $orderInfo['phone']       = $orderDetailInfo['phone'];
            $orderInfo['address']     = $this->Area_model->getAreaId($orderDetailInfo['province_id']) . $this->Area_model->getAreaId($orderDetailInfo['city_id']) . $this->Area_model->getAreaId($orderDetailInfo['area_id']) . $orderDetailInfo['address'];
        }


        //商品列表
        $aGoodsList = $this->Order_goods_model->get_list(array('order_id'=>$order_id),'id,price,num,order_id,goods_id,sku_id');

        foreach ($aGoodsList as $kk => $aa) {
            $aGoodsInfo = $this->Shot_goods_model->get_by_where('order_id = '.$aa['order_id'].' and goods_id = '.$aa['goods_id'] ,'title,pic_path');
            $aGoodsList[$kk]['pic_path'] =cthumb($aGoodsInfo['pic_path']);
            $aGoodsList[$kk]['title'] = $aGoodsInfo['title'];

            //根据订单ID跟产品ID获取 退货信息
          $OrderRrefunds =  $this->Order_refunds_model->get_by_where('order_goods_id = '.$aa['id']);

            if(!empty($OrderRrefunds)) {

                $aGoodsList[$kk]['order_goods_id']  = $OrderRrefunds['order_goods_id'];
                $aGoodsList[$kk]['refunds_id']      = $OrderRrefunds['id'];
                $aGoodsList[$kk]['refunds_money']   = $OrderRrefunds['refunds_money'];
                $OrderRrefunds['reason_title']      = $this->Order_refunds_reason_model->getClassName($OrderRrefunds['reason_id']);

                //图片处理
                if(!empty($OrderRrefunds['pic'])){
                    //图片处理
                    $img = $OrderRrefunds['pic'];
                    $img = explode(',',$img);
                    $imgArr = '';
                    foreach($img as $ka => $va){
                        $imgArr[$ka] = cthumb($va);
                    }
                    $OrderRrefunds['pic'] = $imgArr;
                }
            }
            $aGoodsList[$kk]['OrderRrefunds'] = $OrderRrefunds;
        }

        //获取快递拆包信息
        $orderPackageList = $this->Order_package_model->get_list(array('order_goods_id' => $aa['id']));
        foreach($orderPackageList as $ka => $va){
            $aGoodsList[$kk]['orderPackageList'][$ka] = $va;
        }

        $data = array(
            'orderInfo'         => $orderInfo,
            'aGoodsList'        => $aGoodsList,
            'order_id'          => $order_id,
            'output'=>array('loginUser'=>$this->loginUser),
        );

        $this->load->view('seller/order_info',$data);
    }

    /**
     * 退款提交
     */
    public function agree_reply(){
        $order_id       = $this->input->get('order_id');
        $refunds_id    = $this->input->get('refunds_id');

        $data = array('status' => 3);
        $this->Order_refunds_model->update_by_id($refunds_id,$data);

        redirect(SELLER_SITE_URL.'/order/orderinfo?order_id='.$order_id);
    }

    public  function agree_refund() {
        $order_id       = $this->input->get('order_id');
        $refunds_id    = $this->input->get('refunds_id');

        $this->load->service('message_service');
        $this->load->service('order_service');

        $aRefund = $this->Order_refunds_model->get_by_id($refunds_id);
        if($aRefund['status']==4 || $aRefund['status']==6){
            if($aRefund['status']==4){
                $data = array('status' => 6);
                $this->Order_refunds_model->update_by_id($refunds_id,$data);
            }

            //售后退款
            $arrReturn = $this->order_service->afterSalesRefund($aRefund['order_id'], $refunds_id);
            if($arrReturn['code']==C('OrderResultError.Success'))
            {
                $data = array('status' => 1);
                $this->Order_refunds_model->update_by_id($refunds_id,$data);

                $aOrder = $this->Order_model->get_by_id($aRefund['order_id']);

                //站内信发送
                $tpl_id = 2;
                $receiver = $aRefund['user_id'];
                $receiver_type = 6;
                $arrParam = array('{order_sn}'=>$aOrder['order_sn'],'{money}'=>$aRefund['refunds_money']);
                $this->message_service->send_sys($tpl_id,$receiver,$receiver_type,$arrParam);
                //-站内信发送
            }
            else{
                showMessage('操作失败：'.$arrReturn['errInfo'],SELLER_SITE_URL.'/order/orderinfo?order_id='.$order_id);
                exit;
            }
        }

        redirect(SELLER_SITE_URL.'/order/orderinfo?order_id='.$order_id);
    }

    /** 取消订单*/
    public function close(){

        $this->load->service('order_service');
        $orderId = $this->input->get('order_id');
        $bResult = $this->order_service->close($orderId);
        redirect(SELLER_SITE_URL.'/order');
    }
}