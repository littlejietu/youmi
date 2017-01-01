<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity_reg extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model('pmt/Activity_reg_model'); 
	}

    public function index() {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        $act_name = $this->input->post_get('act_name');
        $search_time = $this->input->post_get('search_time');
        $time1 = $this->input->post_get('time1');
        $time2 = $this->input->post_get('time2');
        $site_id = $this->input->post_get('site_id');
        $type = $this->input->post_get('type');

        $this->load->model('oil/Site_model');

        $site_list = $this->Site_model->get_list(array('company_id'=>$company_id,'status'=>1),'id,site_name');
        

        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('a.status<>'=>-1,'a.company_id'=>$company_id);

        if(!empty($act_name)){
            $arrWhere['title like '] = "'%$act_name%'";
            $arrParam['act_name'] = $act_name;
        }

        if(!empty($site_id)){
            $arrWhere['(site_ids="" or concat(",",site_ids,",") like'] = "'%,$site_id,%')";
            $arrParam['site_id'] = $site_id;
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

        $dbprefix = $this->Activity_reg_model->prefix();
        $tb = $dbprefix.'pmt_activity_reg a left join '.$dbprefix.'pmt_gift b on(a.gift_id=b.id)';
        $list = $this->Activity_reg_model->fetch_page($page, $pagesize, $arrWhere,'a.*,b.name as gift_name', 'status,start_time desc', $tb);
        foreach ($list['rows'] as $k => $v) {
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
            'arrParam' => $arrParam,
        );

        $this->load->view('seller/pmt/activity_reg',$result);
    }
    
    //新增
    public function add()
    {
        $id = $this->input->get('id');
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];

        $this->load->model('oil/Site_model');
        $this->load->model('pmt/Gift_model');

        $info = array();
        if(!empty($id)){
            $info = $this->Activity_reg_model->get_by_id($id);
        }

        $site_list = $this->Site_model->get_list(array('company_id'=>$company_id,'status'=>1));
        $gift_list = $this->Gift_model->get_list(array('company_id'=>$company_id,'status'=>1),'id,name');

        $result = array(
            'site_list' => $site_list,
            'gift_list' => $gift_list,
            'info' => $info,
        );
        
        $this->load->view('seller/pmt/activity_reg_add',$result);
    }
    
    public function save()
    {
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];

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
                $start_time = $this->input->post('start_time');
                $start_time = !empty($start_time)?strtotime($start_time.':00'):0;
                $end_time = $this->input->post('end_time');
                $end_time = !empty($end_time)?strtotime($end_time.':00'):0;
                $site_ids = $this->input->post('site_ids');
                $status = $this->input->post('status');

                $data = array(
                    'title' => $this->input->post('title'),
                    'type' => 4,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'intro' => $this->input->post('intro'),
                    'is_limit_site' => $this->input->post('is_limit_site'),
                    'site_ids' => $site_ids,
                    'is_limit_total_num' => $this->input->post('is_limit_total_num'),
                    'limit_total_num' => $this->input->post('limit_total_num'),
                    'memo' => $this->input->post('memo'),
                    'company_id' => $company_id,
                    'is_gift_integral' => $this->input->post('is_gift_integral'),
                    'gift_integral' => $this->input->post('gift_integral'),
                    'is_gift' => $this->input->post('is_gift'),
                    'gift_id' => $this->input->post('gift_id'),
                    'status' => $status,
                );

                //判断库中没有交叉的活动
                if($status==1){
                    $arrSiteId = explode(',', $site_ids);
                    $whereSite = 'site_ids=""';
                    foreach ($arrSiteId as $sid) {
                        $whereSite .= " or concat(',',site_ids,',') like '%,$sid,%'";
                    }
                    $whereId = '';
                    if(!empty($id))
                        $whereId = " and id<>$id";

                    $where = "status=1 and company_id=$company_id and ( (start_time<=$start_time and end_time>=$start_time) or  (start_time<=$end_time and end_time>=$end_time)) and ($whereSite)".$whereId;
                    $list = $this->Activity_reg_model->get_list($where);
                    //echo $this->Activity_reg_model->db->last_query();die;
                    if(!empty($list)){
                        showMessage('不能与该站点的其他活动时间重叠','');
                        exit;
                    }
                    
                }

                if(empty($id))
                    $id = $this->Activity_reg_model->insert($data);
                else
                    $this->Activity_reg_model->update_by_id($id, $data);

                redirect(SELLER_SITE_URL.'/activity_reg');
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
        $this->Activity_reg_model->update_by_where($where,$data);
        redirect( SELLER_SITE_URL.'/activity_reg' );
    }

}
