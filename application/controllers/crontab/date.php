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
     * 每天定时处理任务
     * @date: 2016年3月16日 下午2:59:57
     * @author : hbb
     */
    public function index()
    {
        //$this->set_distribution_commission();
        $data['ip']=$this->input->ip_address();
        $this->cron_service->set_commis($data);

        $this->cron_service->comm_send();
    }

    private function set_distribution_commission()
    {
        
        $this->cron_service->set_commis($data);


    }

    public function countsale(){
        $this->cron_service->countSale();
    }

}
?>