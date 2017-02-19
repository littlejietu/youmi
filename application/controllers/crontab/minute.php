<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Minute extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('cron_service');
    }

    /**
     * 每分钟定时处理任务
     */
    public function index()
    {
        $this->cron_service->push_message();
        $this->cron_service->batch_send();
        $this->cron_service->print_push();
        $this->cron_service->third_refund();
    }

    private function push_message()
    {
        $this->cron_service->push_message();
    }

    public function batch_send(){
        $this->cron_service->batch_send();
    }

    public function print_push(){
        $this->cron_service->print_push();
    }

    public function third_refund(){
        $this->cron_service->third_refund();
    }
}
?>