<?php
class Order extends TokenDeliverApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Deliver_user_model');
        $this->load->model('Order_model');
        $this->load->model('Order_detail_model');
        $this->load->model('Shop_model');
        //$this->load->model('Area_model');
        $this->load->model('Deliver_order_model');
        $this->load->model('Deliver_user_pwd_model'); //派送员密码表
        $this->load->helper('Goods');
        $this->load->model('Shot_goods_model');
        $this->load->model('User_model');
        $this->load->model('Deliver_order_log_model');
    }

    /**
     * 身份验证
     */
    public function Authenticated() {

        $name = $this->input->post('name');  //真实姓名
        $id_card = $this->input->post('id_card'); //身份证号码
        $id_card_a = $this->input->post('id_card_a'); //身份证A面
        $id_card_b = $this->input->post('id_card_b'); //身份证B面

        #region 验证参数是否空


        if(empty($name)){
            output_error('-1','真实姓名不能为空！');exit;
        }

        if(empty($id_card)){
            output_error('-1','身份证号码不能为空');exit;
        }

        if(empty($id_card_a)){
            output_error('-1','身份证A面不能为空！');exit;
        }

        if(empty($id_card_b)){
            output_error('-1','身份证B面不能为空！');exit;
        }

        #endregion

          $userInfo =  $this->Deliver_user_model->get_by_where('user_id = '.$this->loginUser['id']);

            if(empty($userInfo))
            {
                output_error('-1','非法用户！');exit;
            }

            $userData = array(
                'name'       => $name,
                'id_card'    => $id_card,
                'id_card_a'  => $id_card_a,
                'id_card_b'  => $id_card_b,
            );

            $this->Deliver_user_model->update_by_where('user_id ='.$this->loginUser['id'],$userData);

            //改变主表状态
            $this->Deliver_user_pwd_model->update_by_id($this->loginUser['id'],array('status' => 3 ));

            //读取状态
            $userpwd =  $this->Deliver_user_pwd_model->get_by_id($this->loginUser['id']);

            output_data($userpwd);
    }

    /**
     * 待配送列表
     */
    public function WaitDeliver() {
        $this->load->model('Order_model');
        //待审核
        if($this->loginUser['status']==0){
            output_error('-1','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==2){ //审核中
            output_error('-2','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==3){ //审核失败
            output_error('-3','用户状态不正确！');
            exit;
        }

        //查询派送员主表信息

        $page = $this->input->post('page');
        $pagesize = $this->input->post('pagesize');

        $deliverInfo = $this->Deliver_user_model->get_by_id($this->loginUser['id']);

        if(empty($deliverInfo)){
            output_error('-1','非法用户！');
        }

        $page     = !empty($page)?$page:1;//接收前台的页码
        $pagesize = !empty($pagesize)?$pagesize:999;
        $arrParam = array();

        $strOrder = 'order_id desc';
        if(!empty($deliverInfo['shop_id'])){
            $arrWhere = array('shop_id in('=> $deliverInfo['shop_id'].')', '(status ="' => 'WaitConfirm" or status = "" or status = "WaitSend")', 'deliver_status = "' => 'WaitDeliver"');
        }else{
            $arrWhere = array('status ="' => 'WaitConfirm1"', 'deliver_status = "' => 'WaitDeliver1"');
        }

        $list = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'order_id,shop_id',$strOrder);

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/DeliverList', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

         $this->pagination->initialize($pagecfg);

        unset($list['count']);
        foreach($list['rows'] as $k => $v){
            //获取买家信息
          $aOrderDetail = $this->Order_detail_model->get_by_id($v['order_id']);
            //根据省市区获取详细信息
            $buy_address = '';
            // $province_name   = $this->Area_model->getAreaId($aOrderDetail['province_id']);
            // $city_name       = $this->Area_model->getAreaId($aOrderDetail['city_id']);
            // $area_name       = $this->Area_model->getAreaId($aOrderDetail['area_id']);
            //$buy_address     = $province_name.$city_name.$area_name.$aOrderDetail['address'];
            $province_name = $aOrderDetail['province_name']==$aOrderDetail['city_name']?'':$aOrderDetail['province_name'];
            $city_name = $aOrderDetail['city_name'];
            $area_name = $aOrderDetail['area_name'];

            $buy_address = $province_name;
            if($city_name!=$province_name)
                $buy_address = $buy_address.$city_name;
            if($area_name!=$city_name)
                $buy_address = $buy_address.$area_name;
            $list['rows'][$k]['buy_address']  = $buy_address.$aOrderDetail['address'];

            //获取卖家信息
            $shopInfo = $this->Shop_model->get_by_id($v['shop_id']);

            // 根据省市区获取详细信息
            // $province_name  = $this->Area_model->getAreaId($shopInfo['province_id']);
            // $city_name      = $this->Area_model->getAreaId($shopInfo['city_id']);
            // $area_name      = $this->Area_model->getAreaId($shopInfo['area_id']);
            $province_name = $shopInfo['province_name']==$shopInfo['city_name']?'':$shopInfo['province_name'];
            $city_name = $shopInfo['city_name'];
            $area_name = $shopInfo['area_name'];

            $shop_address = $province_name;
            if($city_name!=$province_name)
                $shop_address = $shop_address.$city_name;
            if($area_name!=$city_name)
                $shop_address = $shop_address.$area_name;
            $list['rows'][$k]['shop_address']  = $shop_address.$shopInfo['address'];
            $list['rows'][$k]['name']  = $shopInfo['name'];
        }

        $result = array(
            'data'=> $list['rows'],
            'code'=>'1',
            'msg'=>'成功！',
            'action'=>'deliver_order_waitdeliver'
        );
        echo json_encode($result);
    }

    /**
     * 抢订单
     */
    public function RushOrders(){

        //待审核
        if($this->loginUser['status']==0){
            output_error('-1','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==2){ //审核中
            output_error('-2','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==3){ //审核失败
            output_error('-3','用户状态不正确！');
            exit;
        }

        $order_id = $this->input->post('order_id');
        if(empty($order_id)){
            $result = array('data' => '','code' => '-2','msg' => '订单号不能为空！','action' => 'RushOrders');
            echo json_encode($result);exit;
        }

        //根据订单id获取订单信息
        $orderInfo = $this->Order_model->get_by_id($order_id);


        if(empty($orderInfo)){

            $result = array('data' => '','code' => '-2','msg' => '非法订单！','action' => 'RushOrders');
            echo json_encode($result);exit;
        }

        if($orderInfo['status'] =='WaitConfirm'|| $orderInfo['status'] =='WaitSend')
        {

        }else
        {
            $result = array('data' => '','code' => '-2','msg' => '订单状态不为待收货！','action' => 'RushOrders');
            echo json_encode($result);exit;
        }

        //订单已被抢
        if($orderInfo['deliver_status']!='WaitDeliver')
        {
            $result = array('data' => '','code' => '-1','msg' => '订单已被抢！','action' => 'RushOrders');
            echo json_encode($result);exit;
        }

        $inData = array(
             'user_id'      => $this->loginUser['id'],
             'order_id'     => $order_id,
             'status'       => 0,
            'addtime'       => time(),
            'update_time'   => time(),
        );

        $this->Deliver_order_model->insert_string($inData);

        //修改订单派送状态
        $this->Order_model->update_by_id($order_id,array('deliver_status' => 'Delivere'));

       $this->Deliver_order_log_model->addDeliverLog(1,$order_id,$this->loginUser['id']);
        output_data();
    }

    /**
     * 配送中、已完成
     */
    public function DeliverList(){

        //待审核
        if($this->loginUser['status']==0){
            output_error('-1','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==2){ //审核中
            output_error('-2','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==3){ //审核失败
            output_error('-3','用户状态不正确！');
            exit;
        }

       $status = $this->input->post('status');//状态：0-配送中、1-完成
        $pagesize = $this->input->post('pagesize');
        $page = $this->input->post('page');


        $page     = !empty($page)?$page:1;//接收前台的页码
        $pagesize = !empty($pagesize)?$pagesize:999;
        $arrParam = array();
        $arrWhere = array('status' => $status,'user_id'=>$this->loginUser['id']);
        $strOrder = 'update_time desc';
        $list = $this->Deliver_order_model->fetch_page($page, $pagesize, $arrWhere,'id,order_id',$strOrder);

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/DeliverList', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        foreach($list['rows'] as $k => $v){

            $orderInfo = $this->Order_model->get_by_id($v['order_id'],'order_id,shop_id');
            //获取买家信息
            $aOrderDetail = $this->Order_detail_model->get_by_id($v['order_id']);

            //根据省市区获取详细信息
            // $province_name   = $this->Area_model->getAreaId($aOrderDetail['province_id']);
            // $city_name       = $this->Area_model->getAreaId($aOrderDetail['city_id']);
            // $area_name       = $this->Area_model->getAreaId($aOrderDetail['area_id']);
            //$buy_address     = $province_name.$city_name.$area_name.$aOrderDetail['address'];

            $province_name = $aOrderDetail['province_name']==$aOrderDetail['city_name']?'':$aOrderDetail['province_name'];
            $city_name = $aOrderDetail['city_name'];
            $area_name = $aOrderDetail['area_name'];
            $buy_address = $province_name;
            if($city_name!=$province_name)
                $buy_address = $buy_address.$city_name;
            if($area_name!=$city_name)
                $buy_address = $buy_address.$area_name;
            $list['rows'][$k]['buy_address']  = $buy_address.$aOrderDetail['address'];
            //$list['rows'][$k]['buy_address']  = $buy_address;

            //获取卖家信息
            $shopInfo = $this->Shop_model->get_by_id($orderInfo['shop_id']);
            // 根据省市区获取详细信息
            // $province_name  = $this->Area_model->getAreaId($shopInfo['province_id']);
            // $city_name      = $this->Area_model->getAreaId($shopInfo['city_id']);
            // $area_name      = $this->Area_model->getAreaId($shopInfo['area_id']);
            //$shop_address   = $province_name.$city_name.$area_name.$shopInfo['address'];
            $province_name = $shopInfo['province_name']==$shopInfo['city_name']?'':$shopInfo['province_name'];
            $city_name = $shopInfo['city_name'];
            $area_name = $shopInfo['area_name'];

            $shop_address = $province_name;
            if($city_name!=$province_name)
                $shop_address = $shop_address.$city_name;
            if($area_name!=$city_name)
                $shop_address = $shop_address.$area_name;
            $list['rows'][$k]['shop_address']  = $shop_address.$shopInfo['address'];

        
            $list['rows'][$k]['name']  = $shopInfo['name'];
        }

        unset($list['count']);
        unset($list['pages']);
        $list = $list['rows'];
        $result = array('data'=> $list,
            'code'=>'1',
            'msg'=>'SUCCESS',
            'action'=>'deliver_order_waitdeliver'
        );
        echo json_encode($result);
//        output_data($list);
    }

    /**
     * 根据订单ID获取订单详细信息
     */
    public function getDeliverId(){

        #region 用户状态判断

        //待审核
        if($this->loginUser['status']==0){
            output_error('-1','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==2){ //审核中
            output_error('-2','用户状态不正确！');
            exit;
        }elseif($this->loginUser['status']==3){ //审核失败
            output_error('-3','用户状态不正确！');
            exit;
        }

        #endregion

        $order_id = $this->input->post('order_id'); //订单ID

        //判断order_id不能为空
        if(empty($order_id)){
            $result = array('data' => '','code' => '-1','msg' => '订单ID不能为空！','action' => 'getDeliverId');
            echo json_encode($result);exit;
        }


        $orderInfo = $this->Order_model->get_by_id($order_id,'order_id,shop_id,seller_userid');

        if(empty($orderInfo)){
            $result = array('data' => '','code' => '-1','msg' => '非法订单！','action' => 'getDeliverId');
            echo json_encode($result);exit;
        }

        //获取买家信息
        $aOrderDetail = $this->Order_detail_model->get_by_id($order_id);

      //根据省市区获取详细信息
        // $province_name   = $this->Area_model->getAreaId($aOrderDetail['province_id']);
        // $city_name       = $this->Area_model->getAreaId($aOrderDetail['city_id']);
        // $area_name       = $this->Area_model->getAreaId($aOrderDetail['area_id']);
        //$buy_address     = $province_name.$city_name.$area_name.$aOrderDetail['address'];
        $province_name = $aOrderDetail['province_name']==$aOrderDetail['city_name']?'':$aOrderDetail['province_name'];
        $city_name = $aOrderDetail['city_name'];
        $area_name = $aOrderDetail['area_name'];
        $buy_address = $province_name;
        if($city_name!=$province_name)
            $buy_address = $buy_address.$city_name;
        if($area_name!=$city_name)
            $buy_address = $buy_address.$area_name;
        $orderInfo['buy_address']   = $buy_address.$aOrderDetail['address'];
        $orderInfo['buy_mobile']      = $aOrderDetail['mobile'];
        $orderInfo['buy_name']      = $aOrderDetail['real_name'];


        //获取卖家信息
        $shopInfo = $this->Shop_model->get_by_id($orderInfo['shop_id']);
        $userInfo = $this->User_model->get_by_where('user_id =' .$orderInfo['seller_userid'],'mobile');
        // 根据省市区获取详细信息
            // $province_name              = $this->Area_model->getAreaId($shopInfo['province_id']);
            // $city_name                  = $this->Area_model->getAreaId($shopInfo['city_id']);
            // $area_name                  = $this->Area_model->getAreaId($shopInfo['area_id']);
        //$shop_address               = $province_name.$city_name.$area_name.$shopInfo['address'];
        $province_name = $shopInfo['province_name']==$shopInfo['city_name']?'':$shopInfo['province_name'];
        $city_name = $shopInfo['city_name'];
        $area_name = $shopInfo['area_name'];
        $shop_address = $province_name;
        if($city_name!=$province_name)
            $shop_address = $shop_address.$city_name;
        if($area_name!=$city_name)
            $shop_address = $shop_address.$area_name;
        $orderInfo['shop_address']  = $shop_address.$shopInfo['address'];
        $orderInfo['shop_name']  = $shopInfo['name'];
        $orderInfo['shop_mobile']  = $userInfo['mobile'];

        //商品列表
        $aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$order_id),'goods_id,sku_id,title,num,pic_path,spec');
        foreach ($aGoodsList as $kk => $aa) {
            $aGoodsList[$kk]['pic_path'] = cthumb($aa['pic_path']);
        }
        $data = array('orderInfo' => $orderInfo,'aGoodsList' => $aGoodsList);
        output_data($data);
    }

    /**头像编辑*/
    public function upadtePhotos(){

        $logo = $this->input->post('logo');
        if(empty($logo)){

            output_error('-1','LOGO_NULL');
            exit;
        }

        $this->Deliver_user_model->update_by_where('user_id ='. $this->loginUser['id'], array('logo' => $logo,'update_time' => time()));
        output_data();
    }

    /**
     * 完成
     */
    public function deliverComplete(){

        $order_id = $this->input->post('order_id');
        if(empty($order_id)){
            output_error('-1','订单ID不能为空！');
            exit;
        }

        //根据派送订单查询派送信息
    //    $deliverOrderInfo = $this->Deliver_order_model->get_by_where('order_id = ' .$order_id,'order_id,');

//        if(empty($deliverOrderInfo)){
//
//            output_error('-2','ID_NULL');
//            exit;
//        }
//
//        if($deliverOrderInfo['status']!=0)
//        {
//            output_error('-1','STATUS_REEOR');
//            exit;
//        }

        // 查询订单信息并进行验证
 //       $orderInfo = $this->Order_model->get_by_id($deliverOrderInfo['order_id'],'deliver_status,status');
//        if(empty($orderInfo))
//        {
//            output_error('-1','ORDERINFO_NULL');
//            exit;
//        }
//
//        if($orderInfo['status']!='WaitConfirm' || $orderInfo['deliver_status']!='deliver_status'){
//            output_error('-1','ORDERSTATUS_NULL');
//            exit;
//        }

        $this->Order_model->update_by_id($order_id,array('deliver_status' => 'WaitConfirm'));
        $this->Deliver_order_model->update_by_where('order_id = '.$order_id,array('status' => 1));
        $this->Deliver_order_log_model->addDeliverLog(2,$order_id,$this->loginUser['id']);
        output_data();

    }
}
