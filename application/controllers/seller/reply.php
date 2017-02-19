<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reply extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model('wx/Reply_model');

	}

	public function index($item_type=1){
		$item_type = intval($item_type);

		$this->lang->load('admin_layout');
		$this->lang->load('admin_article_class');
		$sellerInfo = $this->seller_info;

		$page     = _get_page();
        $pagesize = 20;
        $arrParam = array('item_type'=>$item_type);
        $arrWhere = array('company_id'=>$sellerInfo['company_id'],'item_type'=>$item_type);

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

	public function txt(){
		$this->index(1);
	}

	public function imgtxt(){
		$this->index(2);
	}

    public function txt_add(){
        $this->add(1);
    }

    public function imgtxt_add(){
        $this->add(2);
    }

	public function add($item_type=1){
        $sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
		$id = $this->input->get('id');


        $info = array();
        $info2 = array();
        if(!empty($id)){
            $info = $this->Reply_model->get_by_id($id);
            if(!empty($info)){
                if($info['company_id']!=$company_id)
                    exit('err');
                $item_type = $info['item_type'];
            	$info2 = $info;
            	$info2['keywords'] = !empty($info2['keywords'])?json_decode(htmlspecialchars_decode($info2['keywords']),true):'';
            	unset($info2['replies']);
            }
        }

        $page_name = 'txt_add';
        if($item_type==2)
            $page_name = 'imgtxt_add';
       
		$data = array(
			'info' => $info,
			'info2' => $info2,
		);

		$this->load->view('seller/wx/'.$page_name,$data);

	}

	public function save(){
		$sellerInfo = $this->seller_info;
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'name',
                    'label'   => '名称',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            
            if ($this->form_validation->run() === TRUE)
            {
                $id = $this->input->post('id');
                $company_id = $sellerInfo['company_id'];
                $name = $this->input->post('name');
                $status = $this->input->post('status');
                $status = empty($status)?1:$status;
                $item_type = $this->input->post('item_type');
                $keywords = $this->input->post('keywords');
                $replies = $_POST['replies'];

                $arrKeyTmp = json_decode(htmlspecialchars_decode($keywords),true);
                $arrSearchKey = array();
                foreach($arrKeyTmp as $kw) {
                    $arrSearchKey[] = $kw['content'];
                }
                $search_key = implode(',', $arrSearchKey);

                $data = array(
                	'company_id'=>$company_id,
                	'user_id'=>$sellerInfo['admin_id'],
                    'name' => $name,
                    'sort' => $this->input->post('sort'),
                    'search_key' => $search_key,
                    'keywords' => $keywords,
                    'replies' => $replies,
                    'status' => $status,
                    'is_top' => $this->input->post('is_top'),
                    'item_type'=>$item_type,
                );
                
                
                if(empty($id)){
                    $data['addtime'] = time();
                    $this->Reply_model->insert($data);
                }else
                    $this->Reply_model->update_by_id($id, $data);
                
                $page = 'txt';
                if($item_type==2)
                    $page = 'imgtxt';
                redirect(SELLER_SITE_URL.'/reply/'.$page);
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
            $id	= $this->input->get('id');
        }
        
        $where['id'] = $id;
        $this->Reply_model->update_by_id($id, array('status'=>-1));
        redirect( SELLER_SITE_URL.'/reply' );
    }

	

	public function search_key(){
		$sellerInfo = $this->seller_info;
        $company_id = $this->seller_info['company_id'];

		$arrWhere = array('status'=>1,'company_id'=>$company_id);
		$key_word = trim($this->input->post_get('key_word'));
		if(!empty($key_word)) {
			$arrWhere["concat(',',keywords,',') like "] =  "',%{$key_word}%,'";
		}
		$list = $this->Reply_model->get_list($arrWhere, '*', 'sort desc,id desc', 15);
		
		$exit_da = array();
		if(!empty($list)) {
			foreach($list as $a) {
				$arrKey = explode(',', $a['keywords']);
				foreach ($arrKey as $v) {
					$exit_da[] = $v;
				}
			}
		}
		exit(json_encode($exit_da));
	}

	public function check_key(){
		$sellerInfo = $this->seller_info;
        $company_id = $this->seller_info['company_id'];
		$keyword = trim($this->input->post_get('keyword'));
		$arrWhere = array('company_id'=>$company_id,'status'=>1);
		if(!empty($keyword)) {
			$arrWhere["concat(',',keywords,',') like "] =  "',%{$keyword}%,'";
		}
		$list = $this->Reply_model->get_list($arrWhere, 'id,item_type,name', 'sort desc,id desc', 15);

		if (!empty($list)) {
			exit(@json_encode($list));
		}
		exit('success');
	}



}