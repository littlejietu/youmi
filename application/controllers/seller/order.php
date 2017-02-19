<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends BaseSellerController {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('goods_helper');
        $this->load->model('oil/Site_model');
        //$this->load->model('Shot_goods_model');
        //$this->load->model('Shop_model');
        //$this->load->model('Order_detail_model');
        $this->load->model('trd/Order_model');
        //$this->load->model('Area_model');
        $this->load->model('trd/Order_goods_model');
        //$this->load->model('Goods_model');
        //$this->load->model('Order_refunds_model');
    }

    public function index() {
        $company_id = $this->seller_info['company_id'];
        $order_sn = $this->input->post_get('order_sn');
        $site_id = $this->input->post_get('site_id');
        $buyer_username = $this->input->post_get('buyer_username');
        $status = $this->input->post_get('status');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $paymethod = $this->input->post_get('paymethod');

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('company_id' => $company_id, 'delete_status'=>1,'platform_id'=>C('basic_info.PLATFORM_ID'));
        $order_by = 'order_id desc';


        if(!empty($order_sn)){
            $arrWhere['order_sn']  = "'$order_sn'";
            $arrParam['order_sn'] = $order_sn;
        }
        if(!empty($site_id)){
            $arrWhere['site_id']  = $site_id;
            $arrParam['site_id'] = $site_id;
        }
        if(!empty($buyer_username)){
            $arrWhere['buyer_username']  = $buyer_username;
            $arrParam['buyer_username'] = $buyer_username;
        }
        if(!empty($status)){
            $arrWhere['status'] = "'".C('OrderStatus.'.$status)."'";
            if($status == 'WaitPay')
                $arrWhere['status'] = array(C('OrderStatus.Create'),C('OrderStatus.WaitPay'));
            $arrParam['status'] = $status;
        }
        if(!empty($time1)){
            $arrWhere['createtime >= ']  = strtotime($time1);
            $arrParam['time1'] = $time1;
        }
        if(!empty($time2)){
            $arrWhere['createtime <= ']  = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
        }
        if(!empty($paymethod)){
            $arrWhere['netpay_method']  = $paymethod;
            $arrParam['paymethod'] = $paymethod;
        }


        $site_list = array();
        $site_list_all = $this->Site_model->get_list(array('status'=>1,'company_id'=>$company_id));
        foreach ($site_list_all as $k => $v) {
            $site_list[$v['id']] = $v;
        }
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
            // $aGoodsList = $this->Order_goods_model->get_list(array('order_id'=>$v['order_id']),'id,price,num,order_id,goods_id,sku_id');

            // $list['rows'][$k]['GoodsList'] = $aGoodsList;
            if(!empty($site_list[$v['site_id']]))
                $list['rows'][$k]['site_name'] = $site_list[$v['site_id']]['site_name'];

        }

        $data = array(
            'list' => $list,
            'arrParam' => $arrParam,
            'site_list' => $site_list,
            //'output'=>array('loginUser'=>$this->loginUser),
        );
        $this->load->view('seller/trd/order',$data);
    }

    

    
}