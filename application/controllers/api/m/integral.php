<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Integral extends TokenApiController {
	public $user_id;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Integral_goods_model');
//		$this->load->model('User_account_model');
		$this->load->model('Exchange_record_model');
		$this->load->model('Integral_op_record_model');
		$this->load->model('user_address_model');
		$this->load->model('Sign_record_model');
		$this->load->service('user_service');
		$this->user_id = $this->loginUser['user_id'];

//		$this->user_id = 1;

    }
    /*
     * 获得兑换物品列表 签到
     * */
	public function index()
	{
		$page = $this->input->post('page');
		if(!$page){
			$page = 1;
		}
		$pagesize = $this->input->post('pagesize');
		if(!$pagesize){
			$pagesize = 10;
		}
		$sign = 0;//签到成功1，已签到-1
		$now = getdate();
		$month = $now['year']*100+$now['mon'];
		$whereArr = array(
			'month' => $month,
			'user_id' => $this->user_id,
		);
		$signData = $this->Sign_record_model->get_list($whereArr);
		if(!$signData){
			$info = array(
				'user_id' =>$this->user_id,
				'sign_data' => pow(2,$now['mday']-1),
				'month' => $month,
				'add_time' => time()
			);
			$id = $this->Sign_record_model->insert_string($info);
			if($id){
				$this->user_service->updateIntegral($this->user_id,5);//todo 每次签到获得5积分
			}
			$sign = 1;
			$record = array(
				'user_id' => $this->user_id,
				'op_type' => 2,
				'num' => 5,
				'add_time' => time(),
				'status' => 1,
				'op_record_id' => $id
			);
			$this->Integral_op_record_model->insert_string($record);
		}else{
			$signData = $signData[0];
			if($signData['sign_data'] & pow(2,$now['mday']-1)){
				$sign = -1;
			}else{
				$signData['sign_data'] = $signData['sign_data'] | pow(2,$now['mday'] -1);
				$update = $this->Sign_record_model->update_by_where($whereArr,$signData);//更新签到记录
				if($update){
					$this->user_service->updateIntegral($this->user_id,5);//todo 每次签到获得5积分
					$sign = 1;
					$record = array(
						'user_id' => $this->user_id,
						'op_type' => 2,
						'num' => 5,
						'add_time' => time(),
						'status' => 1,
						'op_record_id' => $update
					);
					$this->Integral_op_record_model->insert_string($record);
				}
			}
		}
		$whereArr = array(
			'status'=>1
		);
//		$picstr = '[{"pic_url":"http:\/\/www.xshop.com\/upload\/img\/9street\/paper1.png","to_url":"http:\/\/www.baidu.com"},'.
//'{"pic_url":"http:\/\/www.xshop.com\/upload\/img\/9street\/paper2.png","to_url":"http:\/\/www.baidu.com"},'.
//'{"pic_url":"http:\/\/www.xshop.com\/upload\/img\/9street\/paper3.png","to_url":"http:\/\/www.baidu.com"}]';
//		$pic = json_decode($picstr,true);
		$total = $this->Integral_goods_model->get_count($whereArr);
		$goods_list = $this->Integral_goods_model->get_list($whereArr);
		foreach($goods_list as $k => $v){
			$goods_list[$k]['goods_url'] = BASE_SITE_URL.'/'.$goods_list[$k]['goods_url'];
		}
		$data = array(
			'integral' => $this->user_service->getUserIntegral($this->user_id),//todo this->User_account_model->,
			'goods_list' =>$goods_list,
			'page' =>$page,
			'total' =>intval((intval($total) + intval($pagesize) -1)/intval($pagesize)),
			'sign' =>$sign,
		);
//		for($i=0;$i < count($data['goods_list']);$i++){
//			$data['goods_list'][$i]['goods_url'] = 'http:\/\/www.jshgwsc.com\/data\/upload\/shop\/store\/goods\/1\/1_04418254218437108_240.jpg';
//			$data['goods_list'][$i]['goods_name'] = '漂亮的衣服';
//		}
		output_data($data);
	}


	/*
     * 兑换
     * */
	public function exchange(){
		$goods_id = $this->input->post_get('goods_id');
		$addr_id =  $this->input->post_get('addr_id');
		$goods = $this->Integral_goods_model->get_by_id($goods_id);
		$addr = $this->user_address_model->get_by_id($addr_id);
		if(empty($addr)){
			output_error(6,"没有这个地址");
		}
		if(empty($goods)){
			output_error(2,"没有这个物品");
		}
		if(empty($goods)){
			output_error(2,"没有这个物品");
		}
		if(intval($goods['total']) - intval($goods['exchange_num'])<=0){
			output_error(3,"这个物品已经兑换完了");
		}
		if(empty($this->user_id)){
			output_error(-1,"用户不存在");
		}
		$uinteg = intval($this->user_service->getUserIntegral($this->user_id));

		if($uinteg - intval($goods['integral_cost'])< 0){
			output_error(4,"您的兑换积分不足");
		}

		$data = array(
			'user_id' =>$this->user_id,
			'user_name' =>$this->loginUser['user_name'],
			'goods_id' =>$goods_id,
			'address_id' =>$addr_id,
			'logistical_status' =>1,
			'integral_cost' =>intval($goods['integral_cost']),
			'exchange_date' => time(),
			'shop_id' => 1001,//todo
			'num' => 1,
			'status' =>1

		);
		$v = $this->Exchange_record_model->insert_string($data);
		if($v){
			$record = array(
				'user_id' => $this->user_id,
				'op_type' => 3,
				'num' => -intval($goods['integral_cost']),
				'add_time' => time(),
				'status' => 1,
				'op_record_id' => $v
			);
			$this->Integral_op_record_model->insert_string($record);
			$this->user_service->updateIntegral($this->user_id,-intval($goods['integral_cost']));
			$this->Integral_goods_model->update_by_id($goods_id,array('exchange_num ' =>intval($goods['exchange_num']+1)));
			output_data();
		}else{
			output_error(5,"未知错误");
		}


	}

	public function integral_list(){

		$page = $this->input->post_get('page');
		if(!$page){
			$page = 1;
		}
		$pagesize = $this->input->post_get('pagesize');
		if(!$pagesize){
			$pagesize = 10;
		}
		$whereArr = array(
			'status' =>1,
			'user_id' =>$this->user_id,
		);
		$field = 'user_id,op_type,num,add_time,op_record_id';
		$list = $this->Integral_op_record_model->fetch_page($page,$pagesize,$whereArr,$field);
		$count = $this->Integral_op_record_model->count($whereArr);
		$data = array(
			'integral' => $this->user_service->getUserIntegral($this->user_id),//$this->User_account_model->,
			'page' =>$page,
			'total' => intval((intval($count) + intval($pagesize) -1)/intval($pagesize)),
			'pagesize'=>$pagesize,
			'list' => $list['rows']
		);

		output_data($data);
	}

//	public function add(){
//		if ($this->input->post())
//		{
//			$config = array(
//				array(
//					'field'   => 'txt_template_title',
//					'label'   => '优惠券名字',
//					'rules'   => 'trim|required'
//				),
//				array(
//					'field'   => 'select_template_price',
//					'label'   => '优惠额度',
//					'rules'   => 'greater_than[0]'
//				),
//				array(
//					'field'   => 'txt_template_total',
//					'label'   => '优惠券数量',
//					'rules'   => 'greater_than[0]'
//				),
//				array(
//					'field'   => 'txt_template_limit',
//					'label'   => '优惠券可使用额度',
//					'rules'   => 'greater_than[0]'
//				),
//				array(
//					'field'   => 'txt_template_limit',
//					'label'   => '优惠券描述',
//					'rules'   => 'trim|required'
//				),
//			);
//
//			$this->form_validation->set_rules($config);
//
//			if ($this->form_validation->run() === TRUE )
//			{
//				if($this->input->post('select_template_price') >= $this->input->post('txt_template_limit')){
//					$this->ajax_response(10001, '表单验证失败');
//					return false;
//				}
//
//				$data = array(
//					'coupon_name' => $this->input->post('txt_template_title'),
//					'price' => $this->input->post('select_template_price'),
//					'add_time' => time(),
//					'coupon_count' =>  $this->input->post('txt_template_total'),
//					'condition' => $this->input->post('txt_template_limit'),
//					'desc' => $this->input->post('txt_template_describe'),
//					'status' => 0,
//					'coupon_type'=>1,
//					'effective_time'=>3,
//					'use_type' =>1,
//					'img_url' =>"dddfsdfs"
//
//				);
//				$result = $this->Coupon_model->insert_string($data);
//				if ($result) {
//					$this->ajax_response(0, '删除成功');
//					return false;
//				} else {
//					$this->ajax_response(10002, '删除失败');
//					return false;
//				}
//			}else{
//				$this->ajax_response(10001, '表单验证失败');
//				return false;
//			}
//
//		}
//	}
	/*
         * 获得兑换记录列表
         * */
	public function record_list(){
		$page = $this->input->post_get('page');
		if(!$page){
			$page = 1;
		}
//		$pagesize = $this->input->post_get('pagesize');
//		if(!$pagesize){
//			$pagesize = 10;
//		}
		$pagesize = 1000;
		$whereArr = array(
			'user_id' => $this->user_id,

		);

		$info = $this->Exchange_record_model->fetch_page($page,$pagesize,$whereArr);
		$count = $info['count'];
		for($i = 0 ;$i<count($info['rows']);$i++){
			$goods = $this->Integral_goods_model->get_by_id($info['rows'][$i]['goods_id']);
			if($goods){
				$info['rows'][$i]['goods_name'] = $goods['goods_name'];
				$info['rows'][$i]['goods_url'] = BASE_SITE_URL.'/'.$goods['goods_url'];
			}

		}
		$data = array(
			'record_list' =>$info['rows'],
			'page' =>$page,
			'total' =>intval((intval($count) + intval($pagesize) -1)/intval($pagesize)),
			'integral' =>$this->user_service->getUserIntegral(1),
		);

		output_data($data);


	}
	/*
	 * 兑换记录详情
	 */
	public function detail(){

		$id = $this->input->post_get('record_id');
		$info = $this->Exchange_record_model->get_by_id($id);

		if($info){
			$goods_id = $info['goods_id'];
			if($goods_id){
				$goods = $this->Integral_goods_model->get_by_id($goods_id);
				$info['goods_name'] = $goods['goods_name'];
				$info['goods_url'] = BASE_SITE_URL.'/'.$goods['goods_url'];
			}
			output_data($info);

		}else{
			output_error(0,'没有这条记录');
		}

//		$this->load->view('api/coupon/coupon_edit',$result);
	}


	



}
