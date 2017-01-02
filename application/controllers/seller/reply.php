<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reply extends BaseSellerController {

	function __construct()
	{
		parent::__construct();
		$this->load->model('wx/Reply_model');

	}

	public function index(){

		$this->lang->load('admin_layout');
		$this->lang->load('admin_article_class');
		$sellerInfo = $this->seller_info;

		$page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('company_id'=>$sellerInfo['company_id']);

        $list = $this->Reply_model->fetch_page($page, $pagesize, $arrWhere,'*');


        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(SELLER_SITE_URL.'/reply', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();


		$data = array(
			'list' => $list,
			'arrParam' => $arrParam,
		);

		$this->load->view('seller/wx/reply',$data);

	}

	function txt_add(){

		$id = $this->input->get('id');
        $company_id = $this->input->post_get('company_id');

        $info = array();
        if(!empty($id))
            $info = $this->Reply_model->get_by_id($id);
       

		$data = array(
			'info' => $info,
		);

		$this->load->view('seller/wx/txt_add',$data);

	}

	function imgtxt_add(){

		$data = array(
		);

		$this->load->view('seller/wx/imgtxt_add',$data);

	}

	function txt_save(){
		print_r($_POST);
		die;
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
            $id	= $this->input->get('id');
        }
        
        $where['id'] = $id;
        $this->Reply_model->delete_by_id($id);
        redirect( SELLER_SITE_URL.'/reply' );
    }

	



}