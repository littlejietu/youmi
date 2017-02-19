<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends BaseSellerController {

    public function __construct()
    {
        
        parent::__construct();
        $this->load->model('oil/Price_model');
    }
    
    
    public function cashier() {
        
        $sellerInfo = $this->seller_info;
        $site_id = $this->input->post_get('site_id');
        $status = $this->input->post_get('status');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $cashier_id = $this->input->post_get('cashier_id');
        $oil_no =  $this->input->post_get('oil_no');

        $this->load->model('oil/Site_model');
        $this->load->model('oil/O_admin_model');
        $this->load->model('trd/Order_model');

        $oil_list = $this->Price_model->get_list(array('company_id'=>$sellerInfo['company_id']),'distinct(oil_no) as oil_no');
        $site_list_data = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1));
        $site_list = array();
        foreach ($site_list_data as $k => $v) {
            $site_list[$v['id']] = $v;
        }
        $cashier = '无';
        $cashier_list = $this->O_admin_model->get_list(array('company_id'=>$sellerInfo['company_id'],'is_cashier'=>1,'status'=>1));
        foreach ($cashier_list as $k => $v) {
            $cashier_list[$k]['site_name'] = '';
            if(!empty($v['site_ids']) && is_numeric($v['site_ids']))
                $cashier_list[$k]['site_name']=$site_list[$v['site_ids']]['site_name'];

            if(!empty($cashier_id) && $v['id']==$cashier_id)
                $cashier = $v['name'];
        }

        $report_info = array();
        $arrParam = array();
        if($this->input->is_post()){
            // $this->load->model('sys/Product_model');
            $arrWhere = array('company_id'=>$sellerInfo['company_id']);
            
            if(!empty($site_id)){
                $arrWhere['site_id'] = $site_id;
                $arrParam['site_id'] = $site_id;
            }
            if(!empty($status)){
                $arrWhere['status'] = "".C('OrderStatus.'.$status);
                if($status == 'WaitPay')
                    $arrWhere['status'] = array(C('OrderStatus.Create'),C('OrderStatus.WaitPay'));
                $arrParam['status'] = $status;
            }
            if(!empty($time1)){
                $arrWhere['createtime >= ']  = strtotime($time1.':00');
                $arrParam['time1'] = $time1;
            }
            if(!empty($time2)){
                $arrWhere['createtime <= ']  = strtotime($time2.':59');
                $arrParam['time2'] = $time2;
            }
            if(!empty($cashier_id)){
                $arrWhere['cashier_id'] = $cashier_id=='null'?null:$cashier_id;
                $arrParam['cashier_id'] = $cashier_id;
            }
            $tb = '';
            if(!empty($oil_no)){
                $prefix = $this->Price_model->prefix();
                $tb = $prefix.'trd_Order inner join '.$prefix.'trd_Order_oil on('.$prefix.'trd_Order.order_id='.$prefix.'trd_Order.order_id)';
                $arrWhere['oil_no'] = $oil_no;
                $arrParam['oil_no'] = $oil_no;

                unset($arrWhere['company_id']);
                $arrWhere['trd_Order.company_id'] = $sellerInfo['company_id'];
                if(!empty($site_id)){
                    unset($arrWhere['site_id']);
                    $arrWhere['trd_Order.site_id'] = $site_id;
                }
            }

            /*
            $sql = 'select sum(total_amt) as total_amt_sum,sum(pay_amt) as pay_amt_sum,sum(discount_amt) as discount_amt_sum,sum(coupon_amt) as coupon_amt_sum from '.$tb.' where company_id='.$sellerInfo['company_id'];
            $where = '';
            foreach ($arrWhere as $k => $v) {
                if(strpos($k,'=')==false)
                    $where = $k.$v;
                else
                    $where = $k.'='.$v;
            }
            */

            $report_info = $this->Order_model->get_by_where($arrWhere, "count(1) as total_num,sum(total_amt) as total_amt_sum,sum(case status when 'Finished' then pay_amt else 0 end) as pay_amt_sum,sum(case status when 'Finished' then discount_amt else 0 end) as discount_amt_sum,sum(case status when 'Finished' then coupon_amt else 0 end) as coupon_amt_sum",'',$tb);
            $report_info['cashier'] = $cashier;
        }
        //echo $this->Order_model->db->last_query();


        
        $result = array(
            'oil_list' =>$oil_list,
            'site_list' => $site_list,
            'cashier_list' => $cashier_list,
            'arrParam' => $arrParam,
            'report_info' => $report_info,
        );

        $this->load->view('seller/rpt/cashier',$result);
    }

    public function customer_oil() {
        
        $sellerInfo = $this->seller_info;
        $site_id = $this->input->post_get('site_id');
        $time1 = $this->input->post_get('time1');
        $time1 = !empty($time1)?$time1:date('Y-m-1',strtotime('-2 month'));
        $time2 = $this->input->post_get('time2');
        $last_day_time = strtotime(date('Y-m-01', time()) . ' +1 month -1 day');
        $time2 = !empty($time2)?$time2:date('Y-m-d',$last_day_time);
        $oil_no =  $this->input->post_get('oil_no');
        $is_excel =  $this->input->post_get('is_excel');

        $this->load->model('oil/Site_model');
        $this->load->model('oil/O_admin_model');
        $this->load->model('rpt/Rpt_customer_oil_model');

        $oil_list = $this->Price_model->get_list(array('company_id'=>$sellerInfo['company_id']),'distinct(oil_no) as oil_no');
        $site_list_data = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1));
        $site_list = array();
        foreach ($site_list_data as $k => $v) {
            $site_list[$v['id']] = $v;
        }


        $page     = _get_page();
        $pagesize = 100;
        $arrParam = array();
        $arrWhere = array('company_id'=>$sellerInfo['company_id']);
        $arrParam = array();
        if(!empty($site_id)){
            $arrWhere['site_id'] = $site_id;
            $arrParam['site_id'] = $site_id;
        }
        if(!empty($time1)){
            $arrWhere['stat_date >= ']  = strtotime($time1);
            $arrParam['time1'] = $time1;
        }
        if(!empty($time2)){
            $arrWhere['stat_date <= ']  = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
        }
        $tb = '';
        $order_by = null;
        $group_by = 'site_id,stat_date';
        $field = 'stat_date,sum(oil_payed_order_num) as oil_payed_order_num,sum(oil_payed_amt) as oil_payed_amt,sum(oil_payed_person_num) as oil_payed_person_num,sum(wxpay_amt) as wxpay_amt,sum(wxpay_person_num) as wxpay_person_num,sum(alipay_amt) as alipay_amt,sum(alipay_person_num) as alipay_person_num';
        if(!empty($oil_no)){
            $arrWhere['oil_no'] = $oil_no;
            $arrParam['oil_no'] = $oil_no;

            $group_by = null;
            $field = '*';
            $order_by = 'stat_date desc';
        }
        
        $list = $this->Rpt_customer_oil_model->fetch_page($page, $pagesize, $arrWhere,$field, $order_by,'',$group_by);

        $pagecfg['base_url']     = _create_url($_SERVER['PATH_INFO'], $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        

        //echo $this->Order_model->db->last_query();

        if($is_excel && $list['count']>0){
            
            $this->load->helper('excel');
            $this->load->library('PHPExcel');
            $title = array('日期','交易笔数','实付金额','微信客单量(元/人)','支付宝客单量(元/人)','全站客单价(元/笔)');
            $site_name = '油蜜';
            if(!empty($site_id) && !empty($site_list[$site_id]))
                $site_name = $site_list[$site_id]['site_name'];
            $data_list = array();
            $oil_payed_order_num=$oil_payed_amt=$oil_payed_person_num=$wxpay_amt=$wxpay_person_num=$alipay_amt=$alipay_person_num = 0;
            foreach ($list['rows'] as $k => $v) {
                $data_list[$k]['stat_date'] = date('Y-m-d',$v['stat_date']);
                $data_list[$k]['oil_payed_order_num'] = $v['oil_payed_order_num'];
                $data_list[$k]['oil_payed_amt'] = $v['oil_payed_amt'];
                $data_list[$k]['wxpay_amt_person'] = null;
                if($v['wxpay_person_num']>0) 
                    $data_list[$k]['wxpay_amt_person'] = round($v['wxpay_amt']/$v['wxpay_person_num'],2);
                $data_list[$k]['alipay_amt_person'] = null;
                if($v['alipay_person_num']>0) 
                    $data_list[$k]['alipay_person_person'] = round($v['alipay_amt']/$v['alipay_person_num'],2);
                $data_list[$k]['all_amt_person'] = null;
                if($v['oil_payed_order_num']>0) 
                    $data_list[$k]['all_amt_person'] = round($v['oil_payed_amt']/$v['oil_payed_order_num'],2);

                $oil_payed_order_num += $v['oil_payed_order_num'];
                $oil_payed_amt += $v['oil_payed_amt'];
                $oil_payed_person_num += $v['oil_payed_person_num'];
                $wxpay_amt += $v['wxpay_amt'];
                $wxpay_person_num += $v['wxpay_person_num'];
                $alipay_amt += $v['alipay_amt'];
                $alipay_person_num += $v['alipay_person_num'];

            }

            $k = $k+1;
            $data_list[$k]['stat_date'] = '合计';
            $data_list[$k]['oil_payed_order_num'] = $oil_payed_order_num;
            $data_list[$k]['oil_payed_amt'] = $oil_payed_amt;
            $data_list[$k]['wxpay_amt_person'] = null;
            if($wxpay_person_num>0) 
                $data_list[$k]['wxpay_amt_person'] = round($wxpay_amt/$wxpay_person_num,2);
            $data_list[$k]['alipay_amt_person'] = null;
            if($alipay_person_num>0) 
                $data_list[$k]['alipay_person_person'] = round($alipay_amt/$alipay_person_num,2);
            $data_list[$k]['all_amt_person'] = null;
            if($oil_payed_order_num>0) 
                $data_list[$k]['all_amt_person'] = round($oil_payed_amt/$oil_payed_order_num,2);

            push_to_excel($data_list,$site_name,$title);
        }


        
        $result = array(
            'oil_list' =>$oil_list,
            'site_list' => $site_list,
            'arrParam' => $arrParam,
            'list' => $list,
        );

        $this->load->view('seller/rpt/customer_oil',$result);
    }

    public function rfm() {
        
        $sellerInfo = $this->seller_info;
        $site_id = $this->input->post_get('site_id');
        $mobile = $this->input->post_get('mobile');
        $time1 = $this->input->post_get('time1');
        $time1 = !empty($time1)?$time1:date('Y-m-1',strtotime('-1 year'));
        $time2 = $this->input->post_get('time2');
        $last_day_time = strtotime(date('Y-m-01', time()) . ' -1 day');
        $time2 = !empty($time2)?$time2:date('Y-m-d',$last_day_time);
        $is_excel =  $this->input->post_get('is_excel');

        $this->load->model('oil/Site_model');
        $this->load->model('trd/Order_oil_model');

        $site_list_data = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1));
        $site_list = array();
        foreach ($site_list_data as $k => $v) {
            $site_list[$v['id']] = $v;
        }

        $arrParam = array();
        $arrWhere = array('company_id'=>$sellerInfo['company_id']);
        $list = array();
        $prefix = $this->Price_model->prefix();
        if(!empty($site_id)){
            $arrWhere['site_id'] = $site_id;
            $arrParam['site_id'] = $site_id;
        }
        if(!empty($mobile)){
            $arrWhere['buyer_userid'] = '(select user_id from '.$prefix.'user where mobile="'.$mobile.'")';
            $arrParam['mobile'] = $mobile;
        }
        if(!empty($time1)){
            $arrWhere['addtime >= ']  = strtotime($time1);
            $arrParam['time1'] = $time1;
        }
        if(!empty($time2)){
            $arrWhere['addtime <= ']  = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
        }
        $tb = '';
        $order_by = 'stat_date';
        $group_by = 'stat_date';
        $field = 'FROM_UNIXTIME(addtime,"%Y-%m") as stat_date,count(1) as num,sum(oil_num) as oil_num,sum(oil_amt) as oil_amt';
        
        $list = $this->Order_oil_model->get_list($arrWhere,$field, $order_by,0,'',$group_by);


        //echo $this->Order_oil_model->db->last_query();

        if($is_excel && count($list)>0){
            
            $this->load->helper('excel');
            $this->load->library('PHPExcel');
            //$data_list = array('rows'=>$list);
            $title = array('月份','消费次数','加油数量','加油金额');
            $site_name = '油蜜';
            if(!empty($site_id) && !empty($site_list[$site_id]))
                $site_name = $site_list[$site_id]['site_name'];
            if(!empty($mobile))
                $site_name .= ' - '.$mobile;

            push_to_excel($list,$site_name,$title);
        }

        $result = array(
            'arrParam' => $arrParam,
            'site_list' => $site_list,
            'list' => $list,
        );

        $this->load->view('seller/rpt/rfm',$result);
    }

}