<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Printapi_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model(array('inter/Orderprint_log_model','trd/Order_model','trd/Order_oil_model','trd/Order_goods_model',
			'oil/O_admin_token_model','trd/Order_model','oil/Site_model'));
	}

	public function orderprint_data($order_id){
		$this->ci->load->model('user/User_model');
		$this->ci->load->model('user/User_detail_model');

		$info = $this->ci->Order_model->get_by_id($order_id);
		if(empty($info) || $info['status']!=C('OrderStatus.Finished'))
			return ;

		$adminInfo = array();
		$admin_name = '';
		if(!empty($info['cashier_id'])){
			$adminInfo = $this->ci->O_admin_token_model->get_by_where(array('site_id'=>$info['site_id'],'status'=>1,'admin_id'=>$info['cashier_id']),'admin_id,name');
			if(!empty($adminInfo))
				$admin_name = $adminInfo['name'];
		}
		if(empty($adminInfo))
			$adminInfo = $this->ci->O_admin_token_model->get_by_where(array('site_id'=>$info['site_id'],'status'=>1),'admin_id','addtime desc');
		if(empty($adminInfo))
			return ;

		$oilInfo = $this->ci->Order_oil_model->get_by_id($order_id);
		$goodsInfo = $this->ci->Order_goods_model->get_by_id($order_id);

		$userInfo = $this->ci->User_model->get_by_id($info['buyer_userid']);
		if(empty($userInfo))
			return ;

		$userDetailInfo = $this->ci->User_detail_model->get_by_id($info['buyer_userid']);
		if(empty($userDetailInfo))
			return ;

		$userInfo = array_merge($userInfo, $userDetailInfo);
		$mrid = $adminInfo['admin_id'];

		$data_log = array('order_id'=>$order_id, 'site_id'=>$info['site_id'], 'mrid'=>$mrid, 'addtime'=>time(), 'status'=>0 );
		$log_id = $this->ci->Orderprint_log_model->insert_string($data_log);

		$oil_name = '';
		$gun_no = $oil_no = $oil_num = $oil_price = 0;
		if(!empty($oilInfo)){
			$oil_name = getOilName($oilInfo['oil_no']).'('.$oilInfo['gun_no'].'号枪)';
			$gun_no = $oilInfo['gun_no'];
			$oil_no = $oilInfo['oil_no'];
			$oil_num = $oilInfo['oil_num'];
			$oil_price = $oilInfo['oil_price'];
		}
		$siteInfo = $this->ci->Site_model->get_by_id($info['site_id']);
		$user_level = '金卡会员';
		$discount_list = "其中：优惠券：￥10.00\n";
		$integal = intval($info['pay_amt']);
		$site_name = $siteInfo['site_name'];

		$oil_goods_text = "";
		if(!empty($oilInfo))
			$oil_goods_text = "油品：{oil_name}（{gun_no}号枪）";
		if(!empty($goodsInfo))
			$oil_goods_text = (!empty($oil_goods_text)?'+':'')."商品";


		$tpl_text = "Youme支付小票---商户联\n         {user_level}\n车号：{car_no}\n时间：{createtime}\n订单：{order_sn}\n"
			.$oil_goods_text."\n应付：{total_amt}元   数量：{oil_num}升\n单价：{oil_price}元/升  \n"
			."{discount_list}"
			."合计优惠：{discount_amt}元\n实付：{pay_amt}元\n本次积分：{integal}分\n"
			."油站：{site_name}\n发票抬头：{invoice_title}\n操作员：{admin_name}\n"
			."备注：\n\n客户签名：\n\n\n本人确认以上交易，同意支付。\n";
		$replace_search = array('{user_level}','{car_no}','{createtime}','{order_sn}','{oil_name}','{gun_no}',
			'{total_amt}','{oil_num}','{oil_price}','{discount_list}','{discount_amt}','{pay_amt}','{integal}',
			'{site_name}','{invoice_title}','{admin_name}'
			);
		$replace_subject = array($user_level, $userInfo['car_no'],date('Y-m-d H:i',time()),$info['order_sn'],$oil_name,$gun_no,
			$info['total_amt'],$oil_num,$oil_price,$discount_list,$info['discount_amt'],$info['pay_amt'],$integal,
			$site_name, $userInfo['invoice_title'],$admin_name
			);
		$print_text = str_replace($replace_search, $replace_subject, $tpl_text);

		$data = array('mrid'=>'mrid'.$mrid,'log_id'=>$log_id,'order_id'=>$order_id, 'order_sn'=>$info['order_sn'], 
			'createtime'=>time(),'oil_no'=>$oil_no,'gun_no'=>$gun_no,'oil_name'=>$oil_name,
			'total_amt'=>$info['total_amt'],'discount_amt'=>$info['discount_amt'],'pay_amt'=>$info['pay_amt'],
			'print_text'=>$print_text
			);

		return $data;
	}

	public function orderprint_internal_push($order_id){
		$data = $this->orderprint_data($order_id);	//订单完成，才推送消息-->打印
		$server_msg_ip = C('basic_info.SERVER_MSG_IP');
		if(!empty($data))
			return $this->push('tcp://'.$server_msg_ip.':5678', $data);
	}

	public function push($server, $data){

		error_reporting(E_ALL^E_WARNING); 

		// 建立socket连接到内部推送端口
		$client = stream_socket_client($server, $errno, $errmsg, 1,  STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT);
		// 推送的数据，包含mrid字段，表示是给这个mrid推送
		//$data = array('mrid'=>'mrid'.$data['mrid'], 'percent'=>rand(1,100).'%');
		// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
		if(!$client)
		{
			return "erreur : $errno - $errmsg<br />";
		}
		else
		{
			fwrite($client, json_encode($data)."\n");
			// 读取推送结果
			$result = trim(fread($client, 8192));
			$log_id = $data['log_id'];
			$status = 0;
			if($result=='ok')
				$status=2;
			else
				$status=-2;

			$this->ci->Orderprint_log_model->update_by_id($log_id,array('status'=>$status));
			return true;
		}
		
	}

}