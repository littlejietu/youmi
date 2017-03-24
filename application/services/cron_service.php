<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron_service
{
    
    // 定时任务执行频率
    const EXE_TIMES = 86400;
    
    // 订单返佣时间 15天
    const ORDER_ABLE_COMMIS_DAY = 15;
    
    //分佣每次
    const LIMIT_START_TIMES = 172800;
    
    // 分佣层级
    const LEVEL = 10;

    //打印间隔-分钟
    const PRINT_SPAN_MINUTE = 15;

    public function __construct()
    {
        $this->ci = & get_instance();
    }

    public function set_commis($arrParam)
    {
        $this->ci->load->model('Order_model');
        $this->ci->load->model('Invite_model');
        $this->ci->load->model('Invite_bonus_model');
        
       /* $last_day_start = strtotime(date('Y-m-d 0:00:00'),strtotime('-1 day'));
        $last_day_end = strtotime(date('Y-m-d 23:59:59'),strtotime('-1 day'));*/
        //TODO:考虑有退款退货，不能时间约束。无处理状态。待处理返佣订单优化，日后再说
        $condition=array(
            'status' => C('OrderStatus.Finished'),
            'comm_amt >'=>0,
            // 'finished_time <' => $last_day_end - self::ORDER_ABLE_COMMIS_DAY * self::EXE_TIMES,
            // 'finished_time >' => $last_day_start - self::ORDER_ABLE_COMMIS_DAY * self::EXE_TIMES
        );
        $data_todo_comm = $this->ci->Order_model->get_list($condition, '*', 'finished_time asc');
        

        if(!empty($data_todo_comm)){
            array_walk($data_todo_comm, function (&$v) use($arrParam)  {

                //存在未完成退款，暂不返佣
                if($this->ci->Order_refunds_model->get_list(array('order_id'=>$v['order_id'],'status<>'=>1,'status<>'=>5),'id')){
                    return;
                }

                $invite_info = $this->ci->Invite_model->get_by_id($v['buyer_userid']);

                if(empty($invite_info['parent_id_1'])){
                    return;
                }
                $data['title'] = '推广佣金';
                $data['type'] = 1;
                $data['order_id'] = $v['order_id'];
                $data['order_sn'] = $v['order_sn'];
                $data['buyer_id'] = $v['buyer_userid'];
                $data['platform_id']=$v['platform_id'];
                $data['ip']=$arrParam['ip'];
                $data['addtime']=time();

                for ($i = 0; $i < self::LEVEL; $i ++) { 
                    if (! empty($invite_info['parent_id_' . ($i + 1)])) {
                        $data['to_user_id']=$invite_info['parent_id_' . ($i + 1)];
                        if($this->ci->Invite_bonus_model->get_by_where(array('order_id'=>$data['order_id'],'to_user_id'=>$data['to_user_id']),'id')){
                            continue;
                        }
                        $data['comm_level'] = $i + 1;
                        $data['rate']=C('distribute_rate_'.($i+1));
//                        $data['bonus_amt']=round($v['comm_amt']*(float)C('distribute_rate_'.($i+1)),2);
                        $data['bonus_amt']=(int)($v['comm_amt']*(float)C('distribute_rate_'.($i+1))*100)/100;
                        $this->ci->Invite_bonus_model->insert_string($data);
                    }
                }
            });
        }
    }

    //发放佣金
    public function comm_send(){
        $this->ci->load->model('Invite_bonus_model');
        $this->ci->load->model('User_model');
        $this->ci->load->service('fundOrder_service');
        $this->ci->load->service('message_service');

        $ip = $this->ci->input->ip_address();
        $arrList = $this->ci->Invite_bonus_model->get_list(array('status'=>1));

        foreach ($arrList as $key => $a) {
            if($a['bonus_amt']==0){
                $this->ci->Invite_bonus_model->update_by_id($a['id'], array('status'=>3));
                continue;
            }

            $aUser = $this->ci->User_model->get_by_id($a['to_user_id']);
            $title = '佣金';
            $toUserName = '';
            if(!empty($aUser))
                $toUserName = $aUser['user_name'];

            $arrReturn = $this->ci->fundorder_service->giveCashBonus($title, $a['to_user_id'], $toUserName, $a['bonus_amt'], $ip, 1);

            if($arrReturn['code']=='Success')
            {
                $this->ci->Invite_bonus_model->update_by_id($a['id'], array('status'=>2));
                //发消息
                $tpl_id = 4;
                $receiver = $a['to_user_id'];
                $arrParam = array('{order_sn}'=>$a['order_sn'],'{money}'=>$a['bonus_amt']);

                $this->ci->message_service->send_sys($tpl_id,$receiver,6,$arrParam);    //6普通单个用户
            }
            
        }
    }

    //发消息
    public function batch_send(){
        //$this->ci->load->model('Message_def_model');
        $this->ci->load->model('Message_model');
        $this->ci->load->model('Message_receiver_model');

        $this->ci->load->service('usernum_service');
       
        //取得所有未发送的消息
        $arrMsg = $this->ci->Message_model->get_list(array('is_send'=>0));
        if(!empty($arrMsg)){
            foreach ($arrMsg as $key => $a){
                if(empty($a['receiver']))
                    $this->ci->Message_model->update_by_id($a['id'],array('is_send'=>2));

                else{
                    //全部
                    if($a['receiver']=='all'){
                        $is_push = $a['is_push']==1?1:0;
                        $sql = "INSERT ".$this->ci->Message_model->prefix()."inter_message_receiver(receiver_id,message_id,is_read,is_del,push_status) SELECT id,".$a['id'].",0,1,".$is_push." FROM x_user_pwd WHERE STATUS=1";
                        /*echo $sql;
                        die;*/
                        $this->ci->Message_receiver_model->execute($sql);
                        
                        $this->ci->Message_model->update_by_id($a['id'],array('is_send'=>1));
                    }
                    else{
                        $aReceiver = explode(',',$a['receiver']);
                        foreach ($aReceiver as $key => $v) {
                            $data = array(
                                'receiver_id'=>$v,
                                'message_id'=>$a['id'],
                                'is_read'=>0,
                                'read_time'=>time(),
                                'is_del'=>1,
                                'push_status'=>$a['is_push']==1?1:0,
                                );
                            $this->ci->Message_receiver_model->insert_string($data);

                            //消息统计
                            $this->ci->usernum_service->onMessage($v);
                        }
                        $this->ci->Message_model->update_by_id($a['id'],array('is_send'=>1));
                    }
                }//if(empty($a['receiver']))
            }//foreach ($arrMsg as $key => $a)
        }
    }
    
    //打印推送
    public function print_push(){
        $this->ci->load->model('inter/Orderprint_log_model');
        $this->ci->load->service('printapi_service');

        $time = time() - PRINT_SPAN_MINUTE*60;
        $arrOrderId = array();
        $list = $this->ci->Orderprint_log_model->get_list(array('status'=>0,'addtime>'=>$time),'order_id');
        foreach ($list as $key => $a) {
            $this->ci->printapi_service->orderprint_internal_push($a['order_id']);
            $arrOrderId[] = $a['order_id'];
        }

        if(!empty($arrOrderId))
            $this->ci->Orderprint_log_model->update_by_where(array('order_id'=>$arrOrderId), array('status'=>2));
    }

    public function third_refund(){
        $this->ci->load->model('trd/Third_refund_log_model');
        $this->ci->load->model('oil/Site_config_model');
        
        $time = strtotime('today-3');
        $list = $this->ci->Third_refund_log_model->get_list(array('status'=>0,'netpay_method'=>array(10,11,12,13,14,15)));

        if(!empty($list)){
            $this->ci->load->library('WxPayApi');
            $input = new WxPayRefund();
            foreach ($list as $key => $a) {
                $wxConfig = $this->ci->Site_config_model->getPayConfig($a['site_id'], $a['company_id']);
                $input->SetOut_trade_no($a['fund_order_id']);
                $input->SetTotal_fee($a['total_amt']*100);
                $input->SetRefund_fee($a['refund_amt']*100);
                $input->SetOut_refund_no($wxConfig['MCHID'].$a['fund_order_id']);
                $input->SetOp_user_id($wxConfig['MCHID']);
                $wx_result = WxPayApi::refund($input, $wxConfig);
                if(!empty($wx_result['result_code']) && !empty($wx_result['return_code']) && $wx_result['result_code']=='SUCCESS' && $wx_result['return_code']=='SUCCESS')
                    $this->ci->Third_refund_log_model->update_by_where(array('fund_order_id'=>$a['fund_order_id']),array('status'=>1));
            }
        }

        $list = $this->ci->Third_refund_log_model->get_list(array('status'=>0,'netpay_method'=>array(21,22,23)));
        $result2 = array();
        if(!empty($list)){
            require_once APPPATH.'/libraries/alipay-sdk/model/builder/AlipayTradeRefundContentBuilder.php';
            require_once APPPATH.'/libraries/AlipayTradeService.php';
            $config = C('PayConfig.ALIPAY3');
            $refundResponse = new AlipayTradeService($config);
            foreach ($list as $key => $a) {
                $aliConfig = $this->ci->Site_config_model->getPayConfig($a['site_id'], $a['company_id']);
                $refundRequestBuilder = new AlipayTradeRefundContentBuilder();
                $refundRequestBuilder->setOutTradeNo($a['fund_order_id']);
                $refundRequestBuilder->setRefundAmount($a['total_amt']);
                //$refundRequestBuilder->setOutRequestNo($out_request_no);

                $refundRequestBuilder->setAppAuthToken($aliConfig['ali_auth_token']);

                $ali_result = $refundResponse->refund($refundRequestBuilder);
                if($ali_result->getTradeStatus()=='SUCCESS')
                    $this->ci->Third_refund_log_model->update_by_where(array('fund_order_id'=>$a['fund_order_id']),array('status'=>1));
                

            }
        }

        echo 'ok';
    }

    public function stat_customer(){
        // $this->ci->load->model('trd/Order_model');
        $this->ci->load->model('rpt/Rpt_customer_model');
        $prefix = $this->ci->Rpt_customer_model->prefix();
        $time1 = 1479484800;//strtotime('today');
        $time2 = 1479571200;//strtotime('today+1');
        
        $info = $this->ci->Rpt_customer_model->get_by_where(array('stat_date'=>$time1));
        if(empty($info)){
            $field = $time1.',site_id,company_id,count(1) as payed_order_num,sum(pay_amt) as payed_amt, sum(case when (netpay_method>10 and netpay_method<20) then pay_amt else 0 end) as wxpay_amt,sum(case when (netpay_method>20 and netpay_method<30) then pay_amt else 0 end) as alipay_amt,count(distinct(buyer_userid)) as payed_person_num,count(distinct(case when (netpay_method>10 and netpay_method<20) then buyer_userid else null end)) as wxpay_person_num,count(distinct(case when (netpay_method>20 and netpay_method<30) then buyer_userid else null end)) as alipay_person_num';
            $sql = 'insert '.$prefix.'rpt_customer(stat_date,site_id,company_id,payed_order_num,payed_amt,wxpay_amt,alipay_amt,payed_person_num,wxpay_person_num,alipay_person_num) '.
                    'select '.$field.' from '.$prefix.'trd_order where status="Finished" and payed_time>='.$time1.' and payed_time<'.$time2.' group by site_id';
            $this->ci->Rpt_customer_model->execute($sql);
        }
        /*
        $arrWhere = array('status'=>"'Finished'",'payed_time>='=>$time1,'payed_time<'=>$time2);
            $list = $this->ci->Order_model->get_list($arrWhere, $field,'',0,'','site_id');
        print_r($list);die;
        */

    }

    public function stat_customer_oil(){
        // $this->ci->load->model('trd/Order_oil_model');
        $this->ci->load->model('rpt/Rpt_customer_oil_model');
        $prefix = $this->ci->Rpt_customer_oil_model->prefix();
        $time1 = 1479484800;//strtotime('today');
        $time2 = 1479571200;//strtotime('today+1');
        $info = $this->ci->Rpt_customer_oil_model->get_by_where(array('stat_date'=>$time1));
        if(empty($info)){
            $field = $time1.',oil_no,site_id,company_id,count(1) as oil_payed_order_num,sum(oil_amt) as oil_payed_amt, sum(case when (netpay_method>10 and netpay_method<20) then oil_amt else 0 end) as wxpay_amt,sum(case when (netpay_method>20 and netpay_method<30) then oil_amt else 0 end) as alipay_amt,count(distinct(buyer_userid)) as oil_payed_person_num,count(distinct(case when (netpay_method>10 and netpay_method<20) then buyer_userid else null end)) as wxpay_person_num,count(distinct(case when (netpay_method>20 and netpay_method<30) then buyer_userid else null end)) as alipay_person_num';
            $sql = 'insert '.$prefix.'rpt_customer_oil(stat_date,oil_no,site_id,company_id,oil_payed_order_num,oil_payed_amt,wxpay_amt,alipay_amt,oil_payed_person_num,wxpay_person_num,alipay_person_num) '.
                    'select '.$field.' from '.$prefix.'trd_order_oil where payed_status=1 and addtime>='.$time1.' and addtime<'.$time2.' group by site_id,oil_no';
            $this->ci->Rpt_customer_oil_model->execute($sql);
        }


    }
    
   
    
}