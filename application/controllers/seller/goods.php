<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goods extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model( 'Goods_model' ); 
        $this->lang->load('user_goods');
	}

    //list
    public function index() {

        $search_type = $this->input->post_get('search_type');
        $keyword = $this->input->post_get('keyword');
        $fieldDate = $this->input->post_get('fieldDate');
        $begtime = $this->input->post_get('begtime');
        $endtime = $this->input->post_get('endtime');
        $orderby = $this->input->post_get('orderby');
        // $status = $this->input->post_get('status');
        if(!$search_type)
            $search_type = 1;

        $shop_id = $this->loginUser['shop_id'];


        

        $this->load->model( 'Goods_num_model' );
        $this->lang->load(array('member_store_goods_index','home_layout','home_flea_goods_index','member_store_goods_index'));
        $this->load->helper('goods');


        $page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('shop_id'=>$shop_id,'status'=>1);
        if(!empty($keyword)){
            switch($search_type){
                case 1:
                    $arrParam['keyword'] = $keyword;
                    $arrParam['search_type'] = $search_type;
                    $arrWhere['title like'] = '"%'.$keyword.'%"';
                    break;
                case 2:
                    $arrParam['keyword'] = $keyword;
                    $arrParam['search_type'] = $search_type;
                    $arrWhere["id"] = $keyword;
                    break;
            }
        }
        if($begtime)
        {
            $arrParam['begtime'] = $begtime;
            $arrWhere["$fieldDate >="] = strtotime($begtime);
        }
        if($endtime)
        {
            $arrParam['endtime'] = $endtime;
            $arrWhere["$fieldDate <="] = strtotime("$endtime 23:59:59");
        }
        // if($status){
        //     $arrParam['status'] = 1;
        //     $arrWhere = $orderby;
        // }

        $strOrder = 'id desc';
        if($orderby)
        {
            $arrParam['orderby'] = $orderby;
            $strOrder = $orderby;
        }

        $list = $this->Goods_model->fetch_page($page, $pagesize, $arrWhere);
        //echo $this->db->last_query();die;

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/goods', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        //$this->load->library('pagination');
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

        foreach($list['rows'] as $k =>  $v){

            $goodsNum = $this->Goods_num_model->get_by_id($v['id']);
            $list['rows'][$k]['stock_num']    = $goodsNum['stock_num'];
        }

        $result = array(
            'list'      => $list,
            'arrParam'  => $arrParam,
            'type'      => $search_type,
            'keyword'   => $keyword,
            'output'    => array('loginUser'=>$this->loginUser),
            );

        $this->load->view('seller/goods',$result);
    }


    //delete
    function del()
    {
        if ($this->input->is_post())
        {
            $id = $this->input->post('del_ids');
        }
        else
        {
            $id = $this->input->get('id');
        }
        $page = _get_page();
        $this->Goods_model->delete_by_id($id);
        redirect( base_url('/seller/goods') );
    }
}
