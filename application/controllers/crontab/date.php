<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Date extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('cron_service');
    }

    /**
     * 每天定时处理任务 0点1分
     */
    public function index()
    {
        //$this->set_distribution_commission();
        
        // $this->cron_service->set_commis($data);

        // $this->cron_service->comm_send();
        // $this->cron_service->stat_customer();

        $this->integral_level();

        $this->doSettle();
    }

    private function set_distribution_commission()
    {
        
        $this->cron_service->set_commis($data);
    }

    public function stat_customer(){
        $this->cron_service->stat_customer();
    }

    public function stat_customer_oil(){
        $this->cron_service->stat_customer_oil();
    }

    public function integral_level(){
        $this->load->model(array('acct/Account_model'));
        $prefix = $this->Account_model->prefix();

        $today = strtotime('today');

        $sql = 'update '.$prefix.'acct_user_account a set a.acct_integral=('
            .' select sum(num) from '.$prefix.'sys_integral_log b where add_time> ( '.$today.' - (select level_day*60*60 from '.$prefix.'oil_company_config where company_id=b.company_id) ) '
            .' and b.user_id = a.user_id)';

        $this->Account_model->excute($sql);

        $sql = 'update '.$prefix.'user a, '.$prefix.'acct_user_account b set a.user_level=(select level_id from '.$prefix.'sys_level c where c.`integral_num`<=b.`acct_integral` and c.company_id=a.company_id order by integral_num desc limit 1) where a.user_id=b.user_id and a.member_status=1';
        $this->Account_model->excute($sql);

        /*
        $this->load->model(array('oil/Company_config_model','user/User_model'));
        $list = $this->Company_config_model->get_list(array(),'company_id,level_day');
        $prefix = $this->Company_config_model->prefix();

        foreach ($list as $k => $v) {
            $daytime = strtotime('today')-$v['level_day']*24*60*60;

            $sql = 'update '.$prefix.'acct_user_account a set a.acct_integral=(select sum(num) from '.$prefix.'sys_integral_log b where add_time>'.$daytime.'  and b.user_id = a.user_id and b.company_id)'
        }
        */
    }

    private function doSettle(){
        /*

// 24小时未评论的订单,系统自动好评 并结算
    private void doSettle() {
        Integer time1 = (int) ((System.currentTimeMillis() - 3 * 24 * 60 * 60 * 1000) / 1000);
        Integer time2 = (int) ((System.currentTimeMillis() - 24 * 60 * 60 * 1000) / 1000);
        // [start]取得超过24小时未评论(等待结算)的订单
        HashMap<String, Object> map = new HashMap<String, Object>();
        map.put("status", OrderStatusEnum.WaitConfirm.toString());
        map.put("commentstatus", 0);
        map.put("createTime1", time1);
        map.put("createTime2", time2);
        Integer pageNumber = 1;
        Integer pageSize = 1000;
        String orderBy = "Order_Id asc";
        List<Trd_OrderModel> list = trd_OrderService.findListByKey(map, pageNumber, pageSize, orderBy);
        for (int i = 0; i < list.size(); i++) {
            Trd_OrderModel tradeOrder = list.get(i);
            Long orderId = tradeOrder.getOrderId();

            // [start] 自动好评
            Trd_Order_AssessModel trd_Order_AssessModel = trd_Order_AssessService.selectByOrderId(orderId);
            if (trd_Order_AssessModel == null) {
                trd_Order_AssessModel = new Trd_Order_AssessModel();
                trd_Order_AssessModel.setAcessType(2); // 系统自动评价
                trd_Order_AssessModel.setAssessGrade(1); // 1好评2中评3差评
                trd_Order_AssessModel.setOrderId(orderId);
                trd_Order_AssessModel.setAssessTime(new Date());
                trd_Order_AssessModel.setBuyerUserId(tradeOrder.getBuyerUserId());
                trd_Order_AssessModel.setSellerUserId(tradeOrder.getSellerUserId());
                trd_Order_AssessModel.setSellerPtyId(tradeOrder.getSellerPtyId());
                trd_Order_AssessService.createSelective(trd_Order_AssessModel);
            }
            // [end]

            // [start] 评价后,完成订单
            boolean isBad = false;
            if (trd_Order_AssessModel.getAssessGrade().intValue() == 3)
                isBad = true;
            Ctm_OrderResultModel result = trd_OrderService.finishByBusinessRule(tradeOrder, isBad);
            // [end]

            // [start] 修改订单评论状态
            if (result.equals(OrderResultErrorEnum.Success.toString()) && tradeOrder.getCommentstatus() == (byte) 0) {
                trd_OrderService.updateCommentStatus(orderId, (byte) 1);
            }
            // [end]

            logger.info("资金订单:" + result.getFundOrderId() + " code:" + result.getCode() + " info:" + result.getErrInfo());
        }
        // [end]

        // [start]已结算已评论的订单-修改订单状态
        HashMap<String, Object> map2 = new HashMap<String, Object>();
        map2.put("status", OrderStatusEnum.Finished.toString());
        map2.put("commentstatus", 0);

        List<Trd_OrderModel> list2 = trd_OrderService.findListByKey(map2, pageNumber, pageSize, orderBy);
        for (int i = 0; i < list2.size(); i++) {
            Trd_OrderModel tradeOrder = list2.get(i);
            Long orderId = tradeOrder.getOrderId();

            Trd_Order_AssessModel trd_Order_AssessModel = trd_Order_AssessService.selectByOrderId(orderId);
            if (trd_Order_AssessModel != null)
                trd_OrderService.updateCommentStatus(orderId, (byte) 1);

        }
        // [end]

    }
        */
    }

}
?>