<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model('pmt/Activity_model'); 
	}

    public function index() {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        $act_name = $this->input->post_get('act_name');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $site_id = $this->input->post_get('site_id');
        $level_id = $this->input->post_get('level_id');
        $type = $this->input->post_get('type');

        $this->load->model(array('oil/Site_model','sys/Level_model'));

        $site_list = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1),'id,site_name');
        $level_list = $this->Level_model->get_list(array('company_id'=>$sellerInfo['company_id']),'level_id,level_name');

        $page     = _get_page();
        $pagesize = 10;
        $arrParam = array();
        $arrWhere = array('status<>'=>-1, 'company_id'=>$company_id);

        if(!empty($act_name)){
            $arrWhere['title like '] = "'%$act_name%'";
            $arrParam['act_name'] = $act_name;
        }

        if(!empty($type)){
            $arrWhere['type'] = $type;
            $arrParam['type'] = $type;
        }

        if(!empty($site_id)){
            $arrWhere['(site_ids="" or concat(",",site_ids,",") like'] = "'%,$site_id,%')";
            $arrParam['site_id'] = $site_id;
        }

        if(!empty($level_id)){
            $arrWhere['(user_level_ids="" or concat(",",user_level_ids,",") like'] = "'%,$level_id,%')";
            $arrParam['level_id'] = $level_id;
        }

        $arrFileldTime = array('start_time','end_time');
        if(!empty($time1) && in_array($search_time,$arrFileldTime)){
            $arrWhere[$search_time.' >= '] = strtotime($time1);
            $arrParam['time1'] = $time1;
            $arrParam['search_time'] = $search_time;
        }
        if(!empty($time2) && in_array($search_time, $arrFileldTime)){
            $arrWhere[$search_time.' <= '] = strtotime($time2.' 23:59:59');
            $arrParam['time2'] = $time2;
            $arrParam['search_time'] = $search_time;
        }
        $dbprefix = $this->Activity_model->prefix();
        $tb = $dbprefix.'pmt_activity a left join '.$dbprefix.'pmt_activity_type b on(a.type=b.id)';
        $list = $this->Activity_model->fetch_page($page, $pagesize, $arrWhere,'a.id,title,type,start_time,end_time,is_period,weekdays,time1,time2,site_ids,user_level_ids,status,b.name as activity_name', 'a.status,a.type,start_time desc',$tb);
        foreach ($list['rows'] as $k => $v) {
            $v['user_level_name'] = '所有会员';
            if(!empty($v['user_level_ids'])){
                $tmp_level_names = '';
                $tmp_level_list = $this->Level_model->get_list('id in('.$v['user_level_ids'].')','level_name');
                foreach ($tmp_level_list as $l_k => $l_v) {
                    $tmp_level_names .= $l_v['level_name'].',';
                }
                $v['user_level_name'] = trim($tmp_level_names, ',');
            }
            $v['period_time'] = '任意时段';
            if($v['is_period']==1){
                $strWeek = '';
                $strTime = '';
                if(!empty($v['weekdays'])){
                    $arrWeek = array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日');
                    $arrTmp = explode(',', $v['weekdays']);
                    $arrTmpWeek = array();
                    foreach ($arrTmp as $vv) {
                        $arrTmpWeek[] = $arrWeek[$vv];
                    }
                    $strWeek = implode(',', $arrTmpWeek);
                    $strTime1 = zerofill(intval($v['time1']/60)).':'.zerofill($v['time1']%60);
                    $strTime2 = zerofill(intval($v['time2']/60)).':'.zerofill($v['time2']%60);
                    $strTime = $strTime1.' - '.$strTime2;
                }
                $v['period_time'] = $strWeek.' '.$strTime;
            }
            $v['site_names'] = '所有加油站';
            if(!empty($v['site_ids'])){
                $tmp_stie_names = '';
                $tmp_site_list = $this->Site_model->get_list('id in('.$v['site_ids'].')','site_name');
                foreach ($tmp_site_list as $s_k => $s_v) {
                    $tmp_stie_names .= $s_v['site_name'].',';
                }
                $v['site_names'] = trim($tmp_stie_names, ',');
            }
            $list['rows'][$k] = $v;
        }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/activity', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();
        
        $result = array(
            'list' =>$list,
            'site_list' => $site_list,
            'level_list' => $level_list,
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/pmt/activity',$result);
    }
    
    //新增
    public function add()
    {
        $id = $this->input->get('id');
        $sellerInfo = $this->seller_info;

        $this->load->model('oil/Site_model');
        $this->load->model('sys/Level_model');
        $this->load->model('pmt/Discount_step_model');
        $this->load->model('pmt/Discount_oil_model');

        $info = array();
        $discount_list = array();
        if(!empty($id)){
            $info = $this->Activity_model->get_by_id($id);
            if(!empty($info)){
                if($info['type']==1 || $info['type']==2)
                    $discount_list = $this->Discount_step_model->get_list(array('act_id'=>$id,'discount_type'=>$info['type'],'status'=>1),'*', 'order_amount');
                elseif($info['type']==3)
                    $discount_list = $this->Discount_oil_model->get_list(array('act_id'=>$id,'discount_type'=>$info['type'],'status'=>1),'*', 'oil_no');
            }
        }
        $site_list = $this->Site_model->get_list(array('company_id'=>$sellerInfo['company_id'],'status'=>1));
        $level_list = $this->Level_model->get_list(array('company_id'=>$sellerInfo['company_id']),'*','level_id');

        $result = array(
            'discount_list' => $discount_list,
            'site_list' => $site_list,
            'level_list' => $level_list,
            'info' => $info,
        );
        
        $this->load->view('seller/pmt/activity_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;
        $this->load->model('pmt/Discount_step_model');
        $this->load->model('pmt/Discount_oil_model');

        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'title',
                    'label'   => '名称',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'intro',
                    'label'   => '说明',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $step = $this->input->post('step');
                $start_time = $this->input->post('start_time');
                $start_time = !empty($start_time)?strtotime($start_time.':00'):0;
                $end_time = $this->input->post('end_time');
                $end_time = !empty($end_time)?strtotime($end_time.':00'):0;
                $time1 = $this->input->post('time1');
                $minute1 =  substr($time1, 0, strpos($time1,':'))*60 + substr($time1, strpos($time1,':')+1);
                $time2 = $this->input->post('time2');
                $minute2 =  substr($time2, 0, strpos($time2,':'))*60 + substr($time2, strpos($time2,':')+1);
                $discount_type = $this->input->post('discount_type');

                $data = array(
                    'title' => $this->input->post('title'),
                    'words' => $this->input->post('words'),
                    'type' => $discount_type,    //1:满就送 2:限时打折
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'is_period' => $this->input->post('is_period'),
                    'weekdays' => $this->input->post('weekdays'),
                    'time1' => $minute1,
                    'time2' => $minute2,
                    'intro' => $this->input->post('intro'),
                    'is_limit_site' => $this->input->post('is_limit_site'),
                    'site_ids' => $this->input->post('site_ids'),
                    'user_level_ids' => $this->input->post('user_level_ids'),
                    'is_limit_total_num' => $this->input->post('is_limit_total_num'),
                    'limit_total_num' => $this->input->post('limit_total_num'),
                    'is_limit_per_total_num' => $this->input->post('is_limit_per_total_num'),
                    'limit_per_total_num' => $this->input->post('limit_per_total_num'),
                    'is_limit_per_day_num' => $this->input->post('is_limit_per_day_num'),
                    'limit_per_day_num' => $this->input->post('limit_per_day_num'),
                    'discount_top_amount' => $this->input->post('discount_top_amount'),
                    'company_id' => $sellerInfo['company_id'],
                    'memo' => $this->input->post('memo'),
                    'status' => $this->input->post('status'),
                );

                if(empty($id))
                    $id = $this->Activity_model->insert($data);
                else
                    $this->Activity_model->update_by_id($id, $data);

                foreach ($step as $k_id => $v) {
                    if($v['type']==$discount_type){
                        
                        //1:满立减 2:满立折 3:限时折扣
                        if($discount_type==1 || $discount_type==2){
                            $data_step = array('act_id' => $id, 
                                'order_amount' => $v['order_amount'],
                                'discount_type' => $discount_type,
                                'status' => 1,
                            );

                            if($discount_type == 1)
                                $data_step['discount_amount'] = $v['discount_amount'];
                            elseif($discount_type == 2)
                                $data_step['discount_percent'] = $v['discount_percent']/10;


                            if($k_id<0)
                                $this->Discount_step_model->insert($data_step);
                            else
                                $this->Discount_step_model->update_by_id($k_id, $data_step);
                        }elseif($discount_type==3){
                            $data_oil = array('act_id' => $id,
                                'oil_no' => $v['oil_no'],
                                'price' => $v['price'],
                                'discount_type' => $discount_type,
                                'status' => 1,
                            );

                            if($k_id<0)
                                $this->Discount_oil_model->insert($data_oil);
                            else
                                $this->Discount_oil_model->update_by_id($k_id, $data_oil);
                        }

                    }
                }

                redirect(SELLER_SITE_URL.'/activity');
            }
        }
    }
    
  
    
    //删除操作
    public function del()
    {
        if ($this->input->is_post())
        {
            $id = $this->input->post('del_id');
        }
        else
        {
            $id = $this->input->get('id');
        }
        $where = array('id'=>$id);
        $data = array('status'=>-1);
        $this->Activity_model->update_by_where($where,$data);
        redirect( SELLER_SITE_URL.'/activity' );
    }

    public function ajax_step_del(){
        $step_id = $this->input->post('step_id');
        $act_id = $this->input->post('act_id');
        $type = $this->input->post('type');

        $this->load->model('pmt/Discount_step_model');
        $this->load->model('pmt/Discount_oil_model');
        if($type==1 || $type==2){
            $where = array('id'=>$step_id, 'act_id'=>$act_id);
            $data = array('status'=>-1);
            $this->Discount_step_model->update_by_where($where, $data);
        }elseif($type==3){
            $where = array('id'=>$step_id, 'act_id'=>$act_id);
            $data = array('status'=>-1);
            $this->Discount_oil_model->update_by_where($where, $data);
        }

        echo 'true';exit;
    }
    
}
