<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Integral_service
{
	public function __construct()
    {
        $this->ci = & get_instance();
        $this->ci->load->model(array('sys/Integral_log_model','user/User_model'));
    }

    //1 注册
    public function reg($user_id){
        $type_id = 1;
        $num = C('exp_register');
        $item_id = $user_id;
        $item_no = '';
        $arrParam = array();

        if($num>0)
            $this->opt($user_id, $type_id, $num, $item_id, $item_no, $arrParam);
    }


    //2 订单
    public function consume($user_id, $order_id, $order_sn, $pay_amt){
        $user_info = $this->ci->User_model->get_by_id($user_id);
        if($user_info['status']!=1 || $user_info['member_status']!=1)
            return;

        $type_id = 2;
        $item_id = $order_id;
        $item_no = $order_sn;
        $num = intval($pay_amt);
       
        $arrParam = array('{pay_amt}'=>$pay_amt, '{num}'=>$num);
        $this->opt($user_id, $type_id, $num, $item_id, $item_no, $arrParam);
    }

    /*
    type_id 1:注册 2:订单
    */
    public function opt($user_id, $type_id, $num, $item_id, $item_no, $arrParam)
    {
        $item_desc = '';
        $item_tb = 0;

        //描述
        $a = M('sys/Integral_type')->get_by_id($type_id);
        if(!empty($a)){
            $item_desc = $a['desc_tpl'];
            $item_tb = $a['item_tb'];
        }

        foreach ($arrParam as $key => $value) {
            $item_desc = str_replace($key, $value, $item_desc);
        }
        $item_desc = str_replace( '{next}', '', str_replace('{prev}', '', $item_desc) );

        $fields = array('user_id'=>$user_id,
            'add_time'=>time(),
            'num'=>$num,
            'type_id'=>$type_id,
            'item_id'=>$item_id,
            'item_no'=>$item_no,
            'item_desc'=>$item_desc,
            'item_tb'=>$item_tb,
        );
        $this->ci->Integral_log_model->opt($fields);
        
    }





}