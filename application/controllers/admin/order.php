<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Admin_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
    }

	public function index() {

		$buyer_username = $this->input->post('buyer_username');
		$seller_username = $this->input->post('seller_username');
		$order_sn = $this->input->post('order_sn');
		$addtime = $this->input->post('addtime');
		$etime = $this->input->post('etime');
		$status = $this->input->post('status');
		$pay_type = $this->input->post('pay_type');
		$paymethod = $this->input->post('paymethod');
		$deliverytype = $this->input->get_post('deliverytype');

		if(!$buyer_username){
			$buyer_username = $this->input->get('buyer_username');
		}
		if(!$buyer_username){
			$seller_username = $this->input->get('seller_username');
		}
		if(!$buyer_username){
			$order_sn = $this->input->get('order_sn');
		}
		if(!$addtime){
			$addtime = $this->input->get('addtime');
		}
		if(!$etime){
			$etime = $this->input->get('etime');
		}
		if(!$status){
			$status = $this->input->get('status');
		}
		if(!$pay_type){
			$pay_type = $this->input->get('pay_type');
		}
		if(!$paymethod){
			$paymethod = $this->input->get('paymethod');
		}

		$page     = _get_page();//接收前台的页码
		
		$pagesize = 10;
		$arrParam = array();
		$arrWhere = array();
		/*if($ad_place)
		{
		    $arrParam['ad_place'] = $ad_place;
		    $arrWhere['ad_place'] = $ad_place;
		}*/

		if($buyer_username)
		{
		    $arrParam['buyer_username'] = $buyer_username;
		    $arrWhere['buyer_username like '] = "'%$buyer_username%'";
		}
		if($seller_username)
		{
		    $arrParam['seller_username'] = $seller_username;
		    $arrWhere['seller_username like '] = "'%$seller_username%'";
		}
		if($order_sn)
		{
		    $arrParam['order_sn'] = $order_sn;
		    $arrWhere['order_sn like '] = "'%$order_sn%'";
		}
		if(!empty($addtime))
		{
		    $arrWhere['createtime > '] = strtotime($addtime);
		    $arrParam['addtime'] = $addtime;
		}
		
		if(!empty($etime))
		{
		    $arrWhere['createtime < '] = strtotime($etime.' 23:59:59');
		    $arrParam['etime'] = $etime;
		}
		if($status)
		{
		    $arrParam['status'] = $status;
		    $arrWhere['status'] = "'$status'";
		    if($status==C('OrderStatus.WaitPay'))
		    	$arrWhere['status'] = array(C('OrderStatus.Create'), C('OrderStatus.WaitPay'));
		}
		if($pay_type)
		{
		    $arrParam['pay_type'] = $pay_type;
		    $arrWhere['pay_type'] = "'$pay_type'";
		}
		if($paymethod)
		{
		    $arrParam['paymethod'] = $paymethod;
		    $arrWhere['netpay_method'] = $paymethod;
		}

		if(!empty($deliverytype)){

			$this->load->model('Order_package_model');
			switch($deliverytype){
				case 1:  //平台发货
				$packlist = $this->Order_package_model->get_list(array('deliver_way' => 2,'order_id <>' => ""),' distinct(order_id) ');
				if(!empty($packlist)){
						$wherepack = array();
						foreach($packlist as $k => $v){

							if(empty($v['order_id'])){
								continue;
							}
							$wherepack[] = $v['order_id'];
						}

							$arrParam['deliverytype'] = $deliverytype;
							$arrWhere['order_id in('] = implode(',',$wherepack).')';
					}
					break;
				case 2:  //商家发货
					$packlist = $this->Order_package_model->get_list(array('deliver_way' => 1,'order_id <>' => ""),' distinct(order_id) ');
					if(!empty($packlist)) {
						$wherepack = array();
						foreach ($packlist as $k => $v) {

							if (empty($v['order_id'])) {
								continue;
							}
							$wherepack[] = $v['order_id'];
						}

						$arrParam['deliverytype'] = $deliverytype;
						$arrWhere['order_id in('] = implode(',', $wherepack) . ')';

					}
					break;
				case 3: //混合发货
					$packlist = $this->Order_package_model->get_list(array('deliver_way' => 3,'order_id <>' => ""),' distinct(order_id) ');
					if(!empty($packlist)) {
						$wherepack = array();
						foreach ($packlist as $k => $v) {

							if (empty($v['order_id'])) {
								continue;
							}
							$wherepack[] = $v['order_id'];
						}

						$arrParam['deliverytype'] = $deliverytype;
						$arrWhere['order_id in('] = implode(',', $wherepack) . ')';
					}
					break;
			}
		}

		$strOrder = 'order_id desc';
		$arrWhere['status <>'] = -1;
		
		$list = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'*',$strOrder);
		//var_dump($page, $pagesize);die;
		
		//echo $this->db->last_query();die;
		//$this->load->model('Link_Place_Model');
		//$ad_placeList = $this->Link_Place_Model->get_list();
		
		//分页
		$pagecfg = array();
		$pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/Order', $arrParam);
		$pagecfg['total_rows']   = $list['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		//$this->load->library('pagination');
		$this->pagination->initialize($pagecfg);
		$list['pages'] = $this->pagination->create_links();

		$result = array(
				'list' => $list,
				'arrParam' => $arrParam,
				'deliverytype'	=> $deliverytype
			);


			//var_dump($list);die;
		$this->load->view('admin/order',$result);
	}

	/**派单列表*/
	public function index_deliver(){

		$this->load->model('Deliver_order_model');
		$this->load->model('Order_detail_model');
		$this->load->model('Deliver_user_model');

		$status = $this->input->post_get('status');//状态：0-配送中、1-完成
		$pagesize = $this->input->post_get('pagesize');
		$page = $this->input->post_get('page');

		$page     = !empty($page)?$page:1;//接收前台的页码
		$pagesize = !empty($pagesize)?$pagesize:10;
		$arrParam = array();
		$arrWhere = array();
		//$list = array();
		$strOrder = 'update_time desc';

		if(!empty($status)){

			$where['status'] = $status;
		}

//echo ; die;
		$list = $this->Deliver_order_model->fetch_page($page, $pagesize, $arrWhere,'id,user_id as deliver_id,order_id,status,addtime',$strOrder);
		//echo $this->Deliver_order_model->db->last_query('page');//die;
		//分页
		$pagecfg = array();
		$pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/Order/index_deliver', $arrParam);
		$pagecfg['total_rows']   = $list['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$list['pages'] = $this->pagination->create_links();
		//echo $this->input->post_get('page'); //die;
		//$list = $this->Order_model->fetch_page($page, $pagesize, $arrWhere,'*',$strOrder);
		foreach($list['rows'] as $k => $v) {

			//获取订单信息
			$orderInfo = $this->Order_model->get_by_id($v['order_id'],'order_id,order_sn,status,deliver_status,seller_userid,seller_username,buyer_userid,buyer_username,shop_id');
			$list['rows'][$k]['orderInfo'] = $orderInfo;
			$deliveInfo = $this->Deliver_user_model->get_by_id($v['deliver_id']);
			$list['rows'][$k]['deliver_user_name'] = $deliveInfo['user_name'];
			$list['rows'][$k]['name'] = $deliveInfo['name'];

		}

		$data = array(
			'list' => $list,
		);

		$this->load->view('admin/order_deliver',$data);
	}
	
	public function receive()
	{
		//输出添加时间然后结束
		//echo time();die;//
		//var_dump($id);
	    //$this->lang->load('admin_Link');//语言包
	    
	    //需要修改
	    $id	= $this->input->get('id');
	    $result = array();
	    $info = array();
	    $this->load->model('Order_Model');
	    $arrPlace = $this->Order_Model->get_list();
	
	    if(!empty($id))
	    {
	        $info = $this->Order_model->get_by_id($id);
	        $place = $this->Order_Model->get_by_id($info['id'],'title');
	        $info['title'] = $place['title'];
	    }
	
	    $result = array(
	        'info'=>$info,
	        'arrPlace'=>$arrPlace,
	    );
	//var_dump($info);die;
	    $this->load->view('admin/Order_receive', $result);
	}

	public function detail(){

		$this->load->model('Order_Model');
		$info = array();
		$arrPlace = array();

		$orderId = $this->input->get('id');
		$orderId = !empty($orderId)?$orderId:8;
        $this->load->helper('Goods');
        $this->load->model('Order_detail_model');
        $this->load->model('Area_model');
        $this->load->model('Shot_goods_model');

        $aOrder = $this->Order_model->get_by_id($orderId);
        if(empty($aOrder)){
            output_error(-1,'ORDER_NOT_EXIST');
        }

        //收货地址
        $aOrderDetail = $this->Order_detail_model->get_by_id($orderId);
        if(!empty($aOrderDetail)){
            $aArea = $this->Area_model->getAreaList(
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
            $aOrderDetail['delivery_way'] = C('DeliveryWay.'.$aOrderDetail['delivery_way']);
            //print_r('DeliveryWay.'.$aOrderDetail['delivery_way']);die;
            $aOrderDetail['province'] = !empty($aArea[0]['name'])?$aArea[0]['name']:'';
            $aOrderDetail['city'] = !empty($aArea[1]['name'])?$aArea[1]['name']:'';
            $aOrderDetail['area']= !empty($aArea[2]['name'])?$aArea[2]['name']:'';
            $aOrderDetail['address'] = $aOrderDetail['address'];
        }

        $aOrderDetail['pay_type'] = C('PayType.'.$aOrder['pay_type']);
        $aOrderDetail['goods_amt'] = $aOrder['total_amt']-$aOrder['fare_amt'];
        $aOrderDetail['fare_amt'] = $aOrder['fare_amt'];
        $aOrderDetail['pay_amt'] = $aOrder['pay_amt'];
        $aOrderDetail['status'] = $aOrder['status'];

        $aOrderDetail = array_merge($aOrderDetail,$aOrder);
        unset($aOrder);

        //商品列表
        $aGoodsList = $this->Shot_goods_model->get_list(array('order_id'=>$orderId),'goods_id,sku_id,price,title,num,pic_path,spec,comm_price');

		#region  读取待配送 订单列表信息

		$this->load->model('Order_goods_model');
		$this->load->model('Order_package_model');  //订单快递表
		$this->load->model('Shot_goods_model');  //产品主表

		$orderGoodsList = $this->Order_goods_model->get_list(array('order_id' => $orderId));

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

          $result =  array(
                'order_detail' 	=> $aOrderDetail,
                'goods_list' 	=> $aGoodsList,
			    'nineBlocks'	=> $nineBlocks,
			    'mainDesk'		=> $mainDesk,
			    'orderId'		=> $orderId,
        );

	    $this->load->view('admin/order_detail', $result);
	}

	/**
	 * 发货
	 */
	public function save_order_deliver(){

		$this->load->model('Order_detail_model');
		$this->load->model('Order_goods_model');
		$this->load->model('Order_package_model');  //订单打包表
		$this->load->model('Order_goods_model');
		$this->load->service('message_service');
		$this->load->service('order_service');

		$hidtype  = $this->input->post('hidtype');
		$order_id = $this->input->post('order_id');
	    $ship_memo = $this->input->post('deliver_explain'); //发货备注

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

		//根据订单ID查找
		
		$nineBlocks = array(); //九号截取配送
		$mainDesk = array(); //后台发布

		$orderGoodsList = $this->Order_goods_model->get_list(array( 'order_id' => $order_id,'goods_id'));

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

			#region 发货

			$orderGoodsList = $this->Order_goods_model->get_list(array( 'order_id' => $order_id,'goods_id'));
			foreach($orderGoodsList as $k => $v) {
				$packagInfo = $this->Order_package_model->get_list(array('order_goods_id' => $v['id']));
				//根据ordergoods获取产品配送信息
				foreach($packagInfo as $kk => $vv) {
					//根据配送类型 组合数据
					switch($vv['deliver_way']) {
						case 1: //九号街区配送
							$nineBlocks[] = $vv;
							break;
						case 2: //快递
							$this->Order_package_model->update_by_id($vv['id'],array('status' => 1));
							break;
						case 3: //混合配送--暂无
							break;
					}
				}
			}

			// 查询商铺 配货状态
			$num =0;
			if(!empty($nineBlocks)){
				foreach($nineBlocks as $k => $v){
					if($v['status'] == 1){
						$num++;
					}
				}

				if($num >= 1 || empty($nineBlocks)) {

					$inData['deliver_status'] =  'WaitDeliver';
				}
			}

			#endregion状态操作

			$this->Order_model->update_by_id($order_id,array( 'status'=> 'WaitConfirm'));

		}
		else {  //接单员配送

			$this->order_service->deliverstatus($order_id);

			#region 发货状态操作

			foreach($orderGoodsList as $k => $v) {
				$packagInfo = $this->Order_package_model->get_list(array('order_goods_id' => $v['id']));
				//根据ordergoods获取产品配送信息
				foreach($packagInfo as $kk => $vv) {
					//根据配送类型 组合数据
					switch($vv['deliver_way']) {
						case 1: //九号街区配送
							$nineBlocks[] =$vv;
							$this->Order_package_model->update_by_id($vv['id'],array('status' => 1));
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

			// 查询商铺 配货状态
			$num =0;
			if(!empty($mainDesk)){
				foreach($mainDesk as $k => $v){
					if($v['status'] == 1){
						$num++;
					}
				}
			}

			if($num >= 1 || empty($mainDesk)) {

				$this->order_service->deliver($order_id);
			}

			#endregion
		}

		//  站内信发送
		$orderDetail = $this->Order_detail_model->get_by_id($order_id,'buyer_userid');

		if(!empty($orderDetail) &&!empty($orderDetail['buyer_userid'])) {

			$this->load->service('message_service');
			$tpl_id = 1;
			//$sender_id = 0;
			$receiver = $orderDetail['buyer_userid'];
			$receiver_type = 6;
			$arrParam = array('{order_sn}'=>$orderInfo['order_sn']);

			$this->message_service->send_sys($tpl_id,$receiver,$receiver_type,$arrParam);
		}

//		redirect(ADMIN_SITE_URL.'/order/detail?id='.$order_id);
		showMessage('发货成功！',$_POST['list_url']);
            exit;
	}
}

?>