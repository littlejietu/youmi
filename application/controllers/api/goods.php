<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends ApiController {

	public function __construct()
  {
      parent::__construct();
      $this->load->model('Goods_model');
      $this->load->model('User_model');
      $this->load->model('Goods_num_model');
      $this->load->model('Goods_sku_model');
      $this->load->model('Comment_goods_model');
      $this->load->service('goods_service');
  }
	
	public function index()
	{
		
	}




  //商品详情
  public function detail(){
    $id = $this->input->post('id');
    $token = $this->input->post_get('token');
    $tpl_id = $this->input->post('tpl_id');
    $shop_id = $this->input->post('shop_id');

    $this->load->helper('goods');
    $this->load->model('User_token_model');
    $this->load->model('Goods_detail_model');
    $this->load->model('Goods_num_model');
    $this->load->model('Goods_sku_model');
    $this->load->model('Goods_tpl_spec_attr_val_model');
    $this->load->model('Goods_spu_attr_val_model');
    $this->load->model('Goods_tpl_pic_model');
    $this->load->model('Comment_goods_model');
    $this->load->model('User_model');
    $this->load->model('Favorite_model');
    $this->load->service('coupon_service');
    $this->load->service('buying_service');

    if($tpl_id>0 && $shop_id>0){
      $aGood_Now = $this->Goods_model->get_by_where(array('tpl_id'=>$tpl_id, 'shop_id'=>$shop_id),'id');
      if(!empty($aGood_Now))
        $id = $aGood_Now['id'];
    }

    if(empty($id)){
      output_error(-1,'商品参数不对');exit;
    }

    if(empty($token)){
      output_error(-1,'请先登录');exit;
    }


    $introduction_url = BASE_SITE_URL.'/wap/home/productdetails/introduction.html?id='.$id;
    $service_url = BASE_SITE_URL.'/wap/home/productdetails/service.html?id='.$id;
    $specs_url = BASE_SITE_URL.'/wap/home/productdetails/specs.html?id='.$id;

    $is_fav = 0;
    //if(!empty($token)){
    $loginUser = $this->User_token_model->get_by_where(array('token'=>"'$token'"));

    $aFav = $this->Favorite_model->get_by_where(array('item_type'=>1,'item_id'=>$id,'user_id'=>$loginUser['user_id'],'status'=>1),'id');
    if(!empty($aFav))
      $is_fav = 1;
    //}

    //商品详情
    $aGood = $this->Goods_model->get_by_where(array('id'=>$id,'status'=>1));
    if(empty($aGood)){
      output_error(-1,'商品不存在');exit;
    }

    $price_active_precent = $this->buying_service->getRealPriceInActivity($aGood['tpl_id']);
    if($price_active_precent>0)
      $aGood['price'] = (string) ($price_active_precent*$aGood['price']);
    //$aGood_Detail = $this->Goods_detail_model->get_by_id($id);
    //$aGood = array_merge($aGood);
    unset($aGood['category_id_1']);unset($aGood['category_id_2']);unset($aGood['category_id_3']);
    unset($aGood['province_id']);unset($aGood['city_id']);

    $notice = '不支持7天无理由退货';
    if($aGood['service_sort']==1)
      $notice = '支持7天无理由退货';

    //其他
    $aOhter = array('service'=>'由九号街区从杭州市西湖区为您配送','notice'=>$notice,'address'=>'浙江 杭州市 西湖区',
        'deliver'=>'市区18:00前完成订单，预计1至两天送达','introduction_url'=>$introduction_url,
        'service_url'=>$service_url,'specs_url'=>$specs_url,'is_fav'=>$is_fav, 'service_title'=>'九号街区客服为您服务','service_id'=>"20",
        'service_logo'=>'http://data.zooernet.com/res/front/images/kefu.png',
      );

    $arrPic_Data = array();
    if(!empty($aGood['pic_path'])){
      $aGood['pic_path'] = cthumb($aGood['pic_path'],360);

      $arrPic_Data[0] = array('pic'=>$aGood['pic_path']);
    }

    //商品图片
    $arrPic = $this->Goods_tpl_pic_model->get_list(array('goods_id'=>$aGood['tpl_id'],'pic<>'=>''),'pic','sort asc,id asc');
    if(!empty($arrPic))
    {
      foreach ($arrPic as $k=>$a) {
        $arrPic_Data[$k]['pic'] = cthumb($a['pic'],360);
      }
    }
    $aGood['pic_list'] = empty($arrPic_Data)?null:$arrPic_Data;

    //规格--取模板的
    $spec_list = $this->Goods_tpl_spec_attr_val_model->getCheckList($aGood['tpl_id'], $aGood['category_id']);
    $aGood['spec_list'] = empty($spec_list)?null:$spec_list;

    //商品sku  
    $arrSkuList = $this->Goods_sku_model->getList($id);
    if($price_active_precent>0){
      foreach ($arrSkuList as $key => $a) {
        $a['price'] = (string) ($price_active_precent*$a['price']);
        $arrSkuList[$key] = $a;
      }
    }
    $aGood['sku'] = empty($arrSkuList)?null:$arrSkuList;
    if($aGood['sku']==null)
      $aGood['spec_list']=null;

    //商品属性值
    $aGood['attr'] = $this->Goods_spu_attr_val_model->getCheckList($id, $aGood['category_id']);
    $aGood['attr'] = empty($aGood['attr'])?null:$aGood['attr'];

    //运费--不提供，因需根据库存计算，此时不知
    $aGood['fare_amt'] = 0;

    //优惠券
    $aCoupon = array();
    $aColor = array('#ff0000','#ff000f','#000000');
    $coupon_list = $this->coupon_service->get_collect_coupon($aGood['shop_id']);
    $i = 0;
    foreach ($coupon_list as $key => $a) {
      $i++;
      $aCoupon[$key]['title']=$a['coupon_name'];
      $aCoupon[$key]['id']=$a['id'];
      $aCoupon[$key]['color']=$aColor[$key];

      if($i>=count($aColor))
        break;
    }
    /*$aCoupon = array(
        array('id'=>1,'title'=>'满499减50','color'=>'ff0000'),
        array('id'=>2,'title'=>'满499减502','color'=>'ff000f'),
        );*/

    //评价
    $aGood_num = $this->Goods_num_model->get_by_id($id,'stock_num,be_comment,be_comment_good');
    $aComment = $this->Comment_goods_model->get_list(array('goods_id'=>$id,'status'=>1),'*','id desc',1);
    if(!empty($aComment))
    {
      $aComment = $aComment[0];
      $aUser = array();
      if (empty($aComment['comment']))
      {
          $aComment['comment'] = '好评!';
      }
      if(!empty($aComment['buyer_id']))
      {
        $aUser = $this->User_model->get_by_id($aComment['buyer_id'],'user_name,logo');
        if(!empty($aUser)){
          $aUser['name'] = substr_replace($aUser['user_name'], '****', 2, -2);
          $aUser['logo'] = strstr($aUser['logo'],'http://')?$aUser['logo']:BASE_SITE_URL.'/'.$aUser['logo'];
          $aComment = array_merge($aUser, $aComment);
        }
      }
      if(!empty($aComment['pic_path'])){
        $aPicTmp = explode('|', $aComment['pic_path']);
        foreach ($aPicTmp as $k => $v) {
            $aPicTmp[$k] = array('thumb'=> BASE_SITE_URL.'/'.$v);
        }
        $aComment['pic_path'] = $aPicTmp;
      }
      else
        $aComment['pic_path'] = null;
    }
    $aComment = empty($aComment)?null:$aComment;
    $aGood['stock_num']=$aGood_num['stock_num'];

    //参与的活动
    $aGood['activity'] =  $this->buying_service->getActivityName($aGood['tpl_id']);;



    //分享
    $share_url = BASE_SITE_URL.'/api/wxauth/go?url='.WAP_SITE_URL.'/home/productdetails/index.html?id='.$aGood['id'].'&invite_id='.$loginUser['user_id'];
    $aShare = array('title'=>$aGood['title'],'desc'=>$aGood['title'],'url'=>$share_url );

    $data = array('info'=>$aGood,'coupon'=>$aCoupon,'other'=>$aOhter,'a_comment'=>$aComment,'comment'=>$aGood_num, 'share'=>$aShare);

    output_data($data);
  }

  //商品介绍
  public function intro(){
    $id = $this->input->post('id');

    $content = '';
    $this->load->model('Goods_detail_model');
    $aGood_Detail = $this->Goods_detail_model->get_by_id($id,'m_content,content');
    if(!empty($aGood_Detail)){
      $content = $aGood_Detail['m_content'];
      if(empty($content))
        $content = $aGood_Detail['content'];
    }
    output_data(array('content'=>$content));
  }

  //商品属性
  public function attr(){
    $id = $this->input->post('id');
    $this->load->model('Goods_spu_attr_val_model');

    $aGood = $this->Goods_model->get_by_id($id);
    if(empty($aGood)){
      output_error(-1,'商品不存在');exit;
    }

    $arrAttr = $this->Goods_spu_attr_val_model->getCheckList($id, $aGood['category_id']);
    $arrAttr = empty($arrAttr)?null:$arrAttr;

    output_data(array('attr'=>$arrAttr));
  }
  
  /**
   * @param 产品详情中的评价列表
   *
   * @param $_POST['goods_id']
   * @param $_POST['page']
   * @param $_POST['pagesize']
   * @return
   */
  public function comment_list()
  {
      $type = $this->input->post('type');
      $page = $this->input->post('page');
      $pagesize = $this->input->post('pagesize');
      $goods_id = $this->input->post('goods_id');
      $arrRes = array('data'=>'','code' =>'','message'=>'');

      if (empty($page)) $page = 1;
      if (empty($pagesize)) $pagesize = 10;
      
      $arrWhere = array('status'=>1);
      if ($goods_id)
          $arrWhere['goods_id'] = $goods_id;
      else
      {
        $arrRes = array('data'=>'','code' =>-1,'message'=>'商品ID不能为空','action' => 'comment_list');//GOODS_ID_NULL
        echo json_encode($arrRes);exit;
      }
      if($type==1)
        $arrWhere['score_level'] = 5;
      else if($type==2)
        $arrWhere['score_level'] = array(3,4);
      else if($type==3)
        $arrWhere['score_level'] = array(1,2);
      else if($type==4)
        $arrWhere['pic_path !='] = "''";

      
      $comment = $this->Comment_goods_model->fetch_page($page, $pagesize, $arrWhere,'*');
      //echo $this->Comment_goods_model->db->last_query();die;
      $data = array();
      foreach ($comment['rows'] as $key => $value)
      {
          $data[$key] =array(
              'id' => $value['id'],
              'content' => !empty($value['comment'])?$value['comment']:'好评!',
              'rating' => $value['score_level'],
              'add_time' => $value['addtime'],
          );
          $img = explode('|',$value['pic_path']);
          $data[$key]['comment_images'] = explode('|',$value['pic_path']);
          
          if(empty($data[$key]['comment_images'][0]))
          {
              array_shift($data[$key]['comment_images']);
          }
          else
          {
              foreach ($data[$key]['comment_images'] as $k=>$v)
              {
                  $data[$key]['comment_images'][$k] = BASE_SITE_URL.'/'.$v;
              }
          }
          if(empty($data[$key]['comment_images']))
            $data[$key]['comment_images'] = null;
          $buyer = $this->User_model->get_by_id($value['buyer_id'],'user_name,logo');
          $data[$key]['name'] = substr_replace($buyer['user_name'], '****', 2, -2);
          
          if (!empty($buyer['logo']))
          {
              $data[$key]['avatar'] = strstr($buyer['logo'],'http://')?$buyer['logo']:BASE_SITE_URL.'/'.$buyer['logo'];
          }
          else 
          {
              $data[$key]['avatar'] = '';
          }
          $gs_where['id'] = $value['sku_id'];
          $goods_sku = $this->Goods_sku_model->get_by_where($gs_where,'id,goods_id,sku_title');
          $data[$key]['sku'] =  $goods_sku['sku_title'];
      }
      $gn_where['goods_id'] = $goods_id;
      $content = $this->Goods_num_model->get_list($gn_where,'goods_id,be_comment as all_num,be_comment_good as positive_num,be_comment_mid as neutral_num,be_comment_bad as negative_num,be_comment_pic as haspic_num');
      $content[0]['curpage'] = "$page";
      if ($type == 0)
      {
          $content[0]['page_total'] = "".ceil($content[0]['all_num']/$pagesize)."";
      }
      if ($type == 1)
      {
          $content[0]['page_total'] = "".ceil($content[0]['positive_num']/$pagesize)."";
      }
      if ($type == 2)
      {
          $content[0]['page_total'] = "".ceil($content[0]['neutral_num']/$pagesize)."";
      }
      if ($type == 3)
      {
          $content[0]['page_total'] = "".ceil($content[0]['negative_num']/$pagesize)."";
      }
      if ($type == 4)
      {
          $content[0]['page_total'] = "".ceil($content[0]['haspic_num']/$pagesize)."";
      }
      
      
      $result['comment_list'] = empty($data)?null:$data;
      $result['content'] = $content[0];
      $result['content']['type'] = $type;
      output_data($result);
  }
  
  /**
   * 猜你喜欢
   * @param $_POST['goods_id']
   */
  public function wish_goods()
  {
      $goods_id = $this->input->post('goods_id');
      $shop_id = $this->input->post('shop_id');
      $goods = $this->goods_service->wish_goods($goods_id, $shop_id,10);
      $list['wish_list'] = $goods;
      output_data($list);
  }
  
  
  /**
   * 热卖商品
   */
  public function hot_sale()
  {
      $shop_id = $this->input->post('shop_id');
      $limit = $this->input->post('limit');

      $list = $this->goods_service->hot_sale($shop_id,$limit);
      $goods['data'] = $list;
      output_data($goods);
  }

	

}
