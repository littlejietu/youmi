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
        $this->ci->load->model('Orderprint_log_model');
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
    
   
    
}