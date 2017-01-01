<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goods_album extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model( 'Goods_album_model' );
		$this->load->model( 'Goods_album_pic_model' );
        $this->lang->load('member_store_album');
	}



	/**
	 * 图片列表，外部调用
	 */
	public function pic_list(){
		$this->load->library('PageStyle');
		$this->load->helper('goods');

		$shop_id = $this->loginUser['shop_id'];
		$album_id = $this->input->get('id');
		$item = $this->input->get('item');

		$arrParam = array();
		$arrWhere = array();
		$arrParam['shop_id']=$shop_id;
		$arrWhere['shop_id']=$shop_id;
		if($album_id)
		{
		    $arrParam['id'] = $album_id;
		    $arrWhere['album_id']=intval($album_id);
		}
		else
			$arrParam['id'] = 0;

		
		$page	= new PageStyle();
        if(in_array($item , array('goods_image'))) {
            $page->setEachNum(12);
        } else {
            $page->setEachNum(14);
        }
		$page->setStyle('admin');

		/**
		 * 图片列表
		 */		
		if(!empty($album_id)){
			//分类列表
			$aAlbum	= $this->Goods_album_model->get_by_where(array('id'=>$album_id));
			Tpl::output('class_name',$aAlbum['name']);
		}

		$pic_list = $this->Goods_album_pic_model->fetch_page($page->getNowPage(),$page->getEachNum(),$arrWhere);
		$page->setTotalNum($pic_list['count']);
		Tpl::output('pic_list',$pic_list);
		Tpl::output('show_page',$page->show());
		Tpl::output('param',$arrParam);
		/**
		 * 分类列表
		 */
		$classList = $this->Goods_album_model->get_list(array('shop_id'=>$shop_id));
		Tpl::output('class_list',$classList);

        switch($item) {
        case 'goods':
            Tpl::showpage('goods_add_step2_master_image','null_layout');
            break;
        case 'goods_color':
        	Tpl::output('type', 'color');
            Tpl::showpage('goods_add_step2_master_image','null_layout');
            break;
        case 'des':
            Tpl::showpage('goods_add_step2_desc_image','null_layout');
            break;
        // case 'groupbuy':
        //     Tpl::showpage('groupbuy.album','null_layout');
        //     break;
        // case 'store_sns_normal':
        //     Tpl::showpage('sns_add.album', 'null_layout');
        //     break;
        case 'goods_image':
            Tpl::showpage('goods_add_step3_goods_image', 'null_layout');
            break;
        case 'mobile':
            Tpl::output('type', $this->input->get('type'));
            Tpl::showpage('goods_add_step2_mobile_image', 'null_layout');
            break;
        }
    }
}