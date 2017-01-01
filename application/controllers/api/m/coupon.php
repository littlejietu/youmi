<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupon extends TokenApiController
{

    public $user_id;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Coupon_User_model');
        $this->load->model('Coupon_model');
        $this->load->model('shop_model');
        $this->load->model('Coupon_User_model');
        $this->load->service('coupon_service');
        $this->user_id = $this->loginUser['user_id'];
//        $this->user_id = 1;
    }

    public function collect_coupon(){

        $id = $this->input->post_get('coupon_id');

        $info = $this->Coupon_model->get_by_id($id);
        if($info){
            if(intval($info['status']) >= 1){
                output_error(3,'优惠券已失效');
            }
            if(intval($info['coupon_count']) <= intval($info['receive_count'])){
                output_error(4,'已经领取完了');
            }
            $count = $this->Coupon_User_model->count(array('user_id' => $this->user_id,'coupon_id' =>$id));
            if($count >=1){
                output_error(6,'你已经领取过这个优惠券了');
            }
            $now = time();
            $data = array(
                'status' => 0,
                'coupon_id'=>$info['id'],
                'user_id'=> $this->user_id,
                'used' =>0,
                'total' =>1,
                'get_date'=>$now,
                'overdue_date' => $now+$info['effective_time']*86400,

            );
            $code = $this->Coupon_User_model->insert_string($data);
            if($code){
                $info['receive_count'] = intval($info['receive_count'])+1;
                $this->Coupon_model->update_by_id($id,$info);
                output_data(array());
            }else{
                output_error(5,'未知错误');
            }
        }else{

            output_error(2,'没有这个优惠券');
        }

    }



    public function index()
    {
        $page = $this->input->post_get('page');
        if(!$page){
            $page = 1;
        }
        $pagesize = $this->input->post_get('pagesize');
        if(!$pagesize){
            $pagesize = 10;
        }
        $type = $this->input->post_get('type');
        if(empty($type)){
            $type = 0;
        }
        $arrWhere = array(
            'user_id'=> $this->user_id,


        );
        if($type == '0'){
            $arrWhere['overdue_date >=']=time();
            $arrWhere['status'] =$type;
        }else if($type == '1'){
            $arrWhere['status'] =$type;
        }else{
            $arrWhere['overdue_date <'] = time();
            $arrWhere['status !='] =2;
        }
        $coupon_list= $this->coupon_service->get_usable_coupons($this->user_id,$page,$pagesize,$type);
        $total = $this->Coupon_User_model->get_count($arrWhere);
        foreach($coupon_list as $k => $v){
            if($v['use_type'] == 2){
                $shop = $this->shop_model->get_by_id($v['shop_id'],"name");
                if($shop){
                    $coupon_list[$k]['shop_name'] = $shop['name'];
                }
            }else{
                $coupon_list[$k]['shop_name'] = '全站';
            }
            $coupon_list[$k]['status'] = $type;
        }
        $data = array(
            "list"=>$coupon_list,
            "page"=>$page,
            "total"=>intval((intval($total) + intval($pagesize) -1)/intval($pagesize)),
        );
        output_data($data);
//        echo "dfsdfsdfsd";
    }

//    public function get_order_coupon(){
//       $data =  $this->coupon_service->get_order_use_coupons(1,0,100);
//        output_data($data);
//    }
//
//    public function use_coupon(){
//        $this->coupon_service->use_coupon(1);
//    }

//    public function test(){
//        $data = $this->coupon_service->get_collect_coupon(0);
//        output_data($data);
//    }
}
