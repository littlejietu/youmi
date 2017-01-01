<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Discount_goods extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('discount_goods_model');
        $this->load->model('goods_model');
        $this->load->helper('goods_helper');
        $this->load->model('Goods_tpl_model');
        $this->load->model('Discount_activity_model');
        $this->load->model('Category_model');

    }

    public function index()
    {

        $page = $this->input->post('page');
        if($page <1){
            $page = 1;
        }
        $shop_id = $this->input->get_post('shop_id');
        $pagesize = $this->input->post('pagesize');
        if($pagesize <1){
            $pagesize = 10;
        }
        if(!$shop_id){
            $shop_id =1;
        }
//        $typeArr  = array(2,3,4,5,6);
        $type = $this->input->post('type');
        $time = time();
//        if(!in_array($type,$typeArr)){
//            $type = 3;
//        }
        $whereArr = array(
            'status' =>1,
            'activity_id' => $type,
            "start_time <=" =>$time,
            "from_sale >" =>$time,
        );
        $category = $this->input->post('category');
        if(isset($category)){
            $whereArr['category_id_1'] = $category;
        }
        $activity = $this->Discount_activity_model->get_by_id($type);
        $goods_list = $this->discount_goods_model->fetch_page($page,$pagesize,$whereArr,'id,goods_id,category_id_1,price,total,saled,add_time,from_sale')['rows'];
        foreach($goods_list as $key => $value) {
            $goods = $this->goods_model->get_by_where(array('tpl_id' => $value['goods_id'],'shop_id' => $shop_id),'id,price,market_price,pic_path,title');
            if(empty($goods)){
                unset($goods_list[$key]);
                continue;
            }
//            $goods_list[$key]['add_time'] = date('Y-m-d h:m:s',$goods_list[$key]['add_time']);
//            $goods_list[$key]['from_sale'] = date('Y-m-d h:m:s',$goods_list[$key]['from_sale']);
            $goods_list[$key]['market_price'] = (string)$goods['market_price'];
            $goods_list[$key]['price'] = (string)round((float)$goods['price']*(float)$activity['discount']/10000,2);
            $goods_list[$key]['pic_path'] = cthumb($goods['pic_path']);
            $goods_list[$key]['goods_id'] = $goods['id'];
//            $goods_list[$key]['pic_path'] = ($goods['pic_path']);
            $goods_list[$key]['title'] = $goods['title'];
            $goods_list[$key]['tpl_id'] = $value['goods_id'];
//            $goods_list[$key]['to_url'] = 'zooer://productdetail?tpl_id='.$value['goods_id'];
        }




        $total = $this->discount_goods_model->get_count($whereArr);
        $now = getdate();
        $time = time();
        $startime =strtotime($now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$activity['start_time'].':0:0') - $time;
        $endtime = strtotime($now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$activity['end_time'].':0:0') - $time;

        if($startime <0){
            $startime =0;//开始
        }
        if($endtime <0 || $startime>0){
            $endtime = 0;//结束
        }

        $data = array(
            'page' =>$page,
            'pagesize' =>$pagesize,
            'totalpage' =>intval(($total+$pagesize -1)/$pagesize),
            'type' => $type,
            'end_time' =>$endtime,
            'start_time' =>$startime,
            'startTime' =>$activity['start_time'],
            'goods_list' =>$goods_list,

        );

        output_data($data);

    }

    public function activity(){
//        $typeArr  = array(2,3,4,5);
        $type = $this->input->post('type');
//        if(!in_array($type,$typeArr)){
//            $type = 3;
//        }
        $activity = $this->Discount_activity_model->get_by_id($type);
        output_data($activity);
    }

    public function get_category(){
        $data = $this->Category_model->getListByParentId(0);
        output_data($data);
    }


}
