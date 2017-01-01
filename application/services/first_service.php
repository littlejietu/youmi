<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class First_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('First_model');
		$this->ci->load->model('discount_goods_model');
		$this->ci->load->model('goods_tpl_model');
		$this->ci->load->model('goods_model');
		$this->ci->load->model('Discount_activity_model');
	}
	
	/**
	 * 获取某推首位的推首列表
	 * @param unknown $place_id  推首位
	 * @param unknown $num  推首条数
	 * @param string $fields  所取字段
	 */
	public function get_first_by_place($place_id,$num,$fields='*')
	{
	    $where['place_id'] = $place_id;
	    $where['status'] = 1;
	    $result = $this->ci->First_model->get_list($where,$fields,'sort desc',$num);
	    foreach ($result as $key => $value)
	    {
	        if (!empty($value['pic_url']))
	        {
	            $result[$key]['pic_url'] = BASE_SITE_URL.'/'.$value['pic_url'];
	        }
	    }
	    return $result;
	}
	
	public function get_discovery()
	{
	    $res1 = $this->get_first_by_place(10,1,'pic as pic_url,url as to_url');
	    $res2 = $this->get_first_by_place(11,13,'title,pic as pic_url,url as to_url');
	    if (empty($res1[0]) && empty($res2[0]))
	    {
	        return array();
	    }
	    $res1[0]['img_type'] = '0';
	    foreach ($res2 as $k => $v)
	    {
	        $res2[$k]['img_type'] = '1';
	    }
	    $result = array_merge($res1,$res2);
	    
	    return $result;
	}
	
	public function get_seckill($shop_id)
	{
	    $seckill_goods = $this->get_home_activity(1,1,2,$shop_id);
	    if (empty($seckill_goods['goods_list']['rows'][0]))
	    {
	        $res0[0] = array(
	            'pic_url' => '',
	            'to_url' => 'zooer://webview?url='.BASE_SITE_URL.'/wap/home/seckill.html&title=今日秒杀&showTitle=0',
	            'image_type' => '0',
	            'timeleft' =>'',
	        );
	    }
	    else 
	    {
	        $res0[0] = array(
	            'pic_url' => $seckill_goods['goods_list']['rows'][0]['pic_url'],
	            'to_url' => 'zooer://webview?url='.BASE_SITE_URL.'/wap/home/seckill.html&title=特价秒杀&showTitle=0',
	            'image_type' => '0',
	            'timeleft' =>$seckill_goods['end_time_left']
	             
	        );
	    }
	    $res1 = $this->get_first_by_place(7,1,'pic as pic_url,url as to_url');
	    if (empty($res1[0]))
	    {
	        $res1[0] = array(
	            'pic_url' => '',
	            'to_url' => '',
	            'image_type' => '1',
	        );
	    }
	    $res2 = $this->get_first_by_place(8,2,'pic as pic_url,url as to_url');
	    if (empty($res2[0]))
	    {
	        $res2[0] = array(
	            'pic_url' => '',
	            'to_url' => '',
	            'image_type' => '2',
	        );
	        $res1[1] = array(
	            'pic_url' => '',
	            'to_url' => '',
	            'image_type' => '2',
	        );
	    }
	    $res1[0]['img_type'] = '1';
	    foreach ($res2 as $k => $v)
	    {
	        $res2[$k]['img_type'] = '2';
	    }
	    $result = array_merge($res0,$res1,$res2);
	     
	    return $result;
	}
//	/*
//	 *
//	 *	今日抢购
//	 * */
//	public function get_today_rush($shop_id){
//		$time = time();
//		$whereArr = array(
//			'status' =>1,
//			'activity_id' => 3,
//			"start_time <=" =>$time,
//			"from_sale >" =>$time,
//		);
//		$goods_list = $this->ci->discount_goods_model->fetch_page(1,10,$whereArr,'id,goods_id,price,total,saled,add_time');
//		foreach($goods_list['rows'] as $key => $value) {
//			$goods = $this->ci->goods_model->get_by_where(array('tpl_id' => $value['goods_id'],'shop_id' => $shop_id));
//			if(!$goods){
//				unset($goods_list['rows'][$key]);
//				continue;
//			}
//			$goods_list['rows'][$key]['market_price'] = $goods['market_price'];
//			$goods_list['rows'][$key]['pic_path'] = $goods['pic_path'];
//			$goods_list['rows'][$key]['title'] = $goods['title'];
//			$goods_list['rows'][$key]['to_url'] = 'zooer://productdetail?id='.$goods['id'].'&shopid='.$shop_id;
//			return $goods_list;
//		}
//	}



	public function get_home_activity($page,$pagesize,$type,$shop_id){
//		$typeArr  = array(2,3,4,5,6);
//		if(!in_array($type,$typeArr)){
//			$type = 3;
//		}
		$activity = $this->ci->Discount_activity_model->get_by_id($type);
		if(!$activity){
			return;
		}
		$time = time();
		$whereArr = array(
			'status' =>1,
			'activity_id' => $type,
			"start_time <=" =>$time,
			"from_sale >" =>$time,
		);
		$goods_list = $this->ci->discount_goods_model->fetch_page($page,$pagesize,$whereArr,'id,goods_id,price,total,saled,add_time,from_sale');
		foreach($goods_list['rows'] as $key => $value) {
			$goods = $this->ci->goods_model->get_by_where(array('tpl_id' => $value['goods_id'],'shop_id' => $shop_id));
			if(!$goods){
				unset($goods_list['rows'][$key]);
				continue;
			}
//			$goods_list['rows'][$key]['sku_id'] = $goods['sku_id'];
			$goods_list['rows'][$key]['original_price'] = (string)$goods['market_price'];
			$goods_list['rows'][$key]['price'] =  (string)round((float)$goods['price']*(float)$activity['discount']/10000,2);
			$goods_list['rows'][$key]['pic_url'] = cthumb($goods['pic_path']);
			$goods_list['rows'][$key]['name'] = $goods['title'];
			$goods_list['rows'][$key]['shop_id'] = $shop_id;
			$goods_list['rows'][$key]['tpl_id'] = $value['goods_id'];
			$goods_list['rows'][$key]['goods_id'] = $goods['id'];
			$goods_list['rows'][$key]['to_url'] = 'zooer://productdetail?tpl_id='.$value['goods_id'];

		}




		$total = $this->ci->discount_goods_model->get_count($whereArr);
		$now = getdate();
		$time = time();
		$startime =strtotime($now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$activity['start_time'].':0:0');
		$endtime = strtotime($now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$activity['end_time'].':0:0');

		if($time>=$startime && $time <$endtime){
			$endtime = $endtime - $time;
		}else{
			$endtime = 0;
		}

		$data = array(
			'page' =>$page,
			'pagesize' =>$pagesize,
			'totalpage' =>intval(($total+$pagesize -1)/$pagesize),
			'type' => $type,
			'end_time_left' =>(string)$endtime,
			'start_time_left' =>$startime,
			'startTime' =>$activity['start_time'],
			'goods_list' =>$goods_list,

		);
		return $data;
	}
	
	
	
}