<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goods_add extends BaseSellerController {

	function __construct()
	{
		parent::__construct();

		$this->load->model('Category_model');
        $this->load->model('Goods_model');
        
	}

    //list
    public function index()
    {
        exit;   //商家无发布商品权限
        $this->add_step1();
    }

    public function add_step1()
    {
        exit;   //商家无发布商品权限
        $this->lang->load(array('user_goods','common'));

        $shop_id = $this->loginUser['shop_id'];
        $goods_class = $this->Category_model->getShopList($shop_id);

        $result = array(
            'output'=>array('loginUser'=>$this->loginUser)
            );

        $this->load->view('seller/goods_add_step1', $result);
    }

    public function add_step2()
    {

        $this->lang->load( array('member_layout','user_goods') );
        $this->load->model( array('Goods_tpl_model', 'Goods_detail_model')  );
        //$this->load->model('Goods_detail_model');
        $this->load->model('Goods_num_model');
        $this->load->model('Brand_model');
        $this->load->model('Spu_model');
        $this->load->model('Spec_model');
        //$this->load->model('Goods_spec_attr_val_model');
        $this->load->model('Goods_tpl_spec_attr_val_model');
        $this->load->model('Goods_spu_attr_val_model');
        $this->load->model('Goods_tpl_spu_attr_val_model');
        $this->load->model('Goods_sku_model');
        $this->load->helper('goods');

        $tpl_id =  $this->input->get( 'tpl_id' );
        //$cid = $this->input->get( 'cid' );
        $id = $this->input->get( 'id' );
        $shop_id = $this->loginUser['shop_id'];
        $cid = 0;

        if($id)
        {
            $aGoods = $this->Goods_model->get_by_where(array('id'=>$id,'shop_id'=>$shop_id));
            $aGoods_detail = $this->Goods_detail_model->get_by_id($id);
            $aGoods_num = $this->Goods_num_model->get_by_id($id);
            $aGoods = array_merge($aGoods_detail,$aGoods_num,$aGoods);
            unset($aGoods_detail);
            unset($aGoods_num);
            if(!empty($aGoods['tpl_id']))
                $tpl_id = $aGoods['tpl_id'];

            //品牌
            if(!empty($aGoods['brand_id']))
            {
                $aBrand = $this->Brand_model->get_by_id($aGoods['brand_id']);
                if(!empty($aBrand))
                    $aGoods['brand_name'] = $aBrand['name'];
            }  

            
            $cid = $aGoods['category_id'];

            //sp_value-->库存配置
            $sp_value = array();
            $aSku = $this->Goods_sku_model->get_list(array('goods_id'=>$id),'*');
            if(!empty($aSku))
            {
                foreach ($aSku as $v) {
                    $code = substr($v['sku_code'], strpos($v['sku_code'],':')+1);
                    $sp_value ['i_' . $code . '|marketprice'] = $v['market_price'];
                    $sp_value ['i_' . $code . '|price'] = $v['price'];
                    $sp_value ['i_' . $code . '|id'] = $v['goods_id'];
                    $sp_value ['i_' . $code . '|stock'] = $v['num'];
                    $sp_value ['i_' . $code . '|costprice'] = $v['cost_price'];
                }
            }
            Tpl::output ( 'sp_value', $sp_value );
            //-sp_value

            //选中的规格
            $aSpec = $this->Goods_tpl_spec_attr_val_model->get_list(array('goods_id'=>$tpl_id),'val_id,spec_val,pic','sort asc,id desc');
            if(!empty($aSpec))
            {
                $spec_checked = array();
                foreach ( $aSpec as $k => $v ) {
                    $spec_checked[$v['val_id']]['id'] = $v['val_id'];
                    $spec_checked[$v['val_id']]['name'] = $v['spec_val'];
                    $spec_checked[$v['val_id']]['pic'] = $v['pic'];
                }
                Tpl::output('spec_checked', $spec_checked);
            }


            //商品属性值
            if(!is_tpl_follow('spu')){
                $aAttr = $this->Goods_spu_attr_val_model->get_list(array('goods_id'=>$id),'val_id,name_id,val');
                if(!empty($aAttr))
                {
                    $attr_id = array();
                    $attr_txt = array();
                    foreach ( $aAttr as $val ) {
                        if(!empty($val['val']))
                            $attr_txt[$val['name_id']] = $val['val'];
                        else
                            $attr_id[] = $val ['val_id'];
                    }
                    $attr_checked = array('attr_id'=>$attr_id,'attr_txt'=>$attr_txt);
                    Tpl::output('attr_checked', $attr_checked);
                }
            }

            

        }//if($id)

        //读取模板数据
        if($tpl_id){    //取模板信息
            $aGoods_tpl = $this->Goods_tpl_model->get_by_id($tpl_id);
            if(empty($aGoods_tpl)){
                showMessage('商品发布有问题，请与平台联系','/seller/goods');
                exit;
            }

            $cid = $aGoods_tpl['category_id'];
            
            //品牌
            if(!empty($aGoods_tpl['brand_id']))
            {
                $aBrand = $this->Brand_model->get_by_id($aGoods_tpl['brand_id']);
                if(!empty($aBrand))
                    $aGoods_tpl['brand_name'] = $aBrand['name'];
            }

            //商品模板属性值
            $aAttr = $this->Goods_tpl_spu_attr_val_model->get_list(array('goods_id'=>$tpl_id),'val_id,name_id,val');
            if(!empty($aAttr))
            {
                $attr_id = array();
                $attr_txt = array();
                foreach ( $aAttr as $val ) {
                    if(!empty($val['val']))
                        $attr_txt[$val['name_id']] = $val['val'];
                    else
                        $attr_id[] = $val ['val_id'];
                }
                $attr_checked = array('attr_id'=>$attr_id,'attr_txt'=>$attr_txt);
                Tpl::output('attr_checked', $attr_checked);
            }

            //判断是否总台控制
            $arrFollow = C('Goods_Tpl.Follow');
            foreach ($arrFollow as $v) {
                if(!empty($aGoods_tpl[$v]))
                    $aGoods[$v] = $aGoods_tpl[$v];
            }
            //'price',?

        }else{
                showMessage('商品发布有问题，请与平台联系','/seller/goods');
                exit;
        }
        //-读取模板数据

        Tpl::output('goods',$aGoods);

        Tpl::output('loginUser', $this->loginUser);

        Tpl::output('cid', $cid);
        Tpl::output('id', $id);
        Tpl::output('tpl_id',$tpl_id);

        $arrCategory = $this->Category_model->getLine($cid);
        Tpl::output('goods_class', $arrCategory);

        //品牌列表//,'category_id'=>$cid
        $brand_list = $this->Brand_model->get_list();
        Tpl::output('brand_list', $brand_list);

        //通过cid，得到该类目下的默认的spu属性
        $spu_code = $cid.'-';
        $attrValList = $this->Spu_model->getAttrValBySpuCode($spu_code);
        Tpl::output('spucode', $spu_code);
        Tpl::output('attr_list', $attrValList);

        //通过cid，得到该类目下sku规格
        $arrSpecList = $this->Spec_model->getSpecVal($cid);

        Tpl::output('spec_list', $arrSpecList);
        Tpl::output('sign_i', count($arrSpecList));



        $hour_array = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
        Tpl::output('hour_array', $hour_array);
        $minute_array = array('05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');
        Tpl::output('minute_array', $minute_array);

        Tpl::showpage('goods_add_step2');
    }

    public function save()
    {
        $id = $this->input->post('id');
        $tpl_id = $this->input->post( 'tpl_id' );
        $cid = $this->input->post( 'cid' );
        $spu_code = $this->input->post('scode');
        $shop_id = $this->loginUser['shop_id'];
        //$arrSpecName = $this->input->post('sp_name'); 规格名不用保存，不自定义
        $arrSpecVal = $this->input->post('sp_val'); //规格值
        $arrSku = $this->input->post('spec');  //sku
        $price = round( floatval($this->input->post( 'price' ) ), 2);
        $cost_price = round( floatval($this->input->post( 'cost_price' ) ), 2);
        
        if ($this->input->is_post())
        {
            //验证规则
            $config = array(
                array('field'=>'tpl_id','label'=>'商品模板','rules'=>'trim|required'),
                array('field'=>'title','label'=>'商品名称','rules'=>'trim|required'),
                array('field'=>'price','label'=>'商品价格','rules'=>'trim|required'),
				//array('field'=>'market_price','label'=>'市场价格','rules'=>'trim|required'),
				array('field'=>'discount','label'=>'折扣','rules'=>'trim|required'),
                array('field'=>'stock_num','label'=>'库存数量','rules'=>'trim|required'),
				array('field'=>'image_path','label'=>'商品图片','rules'=>'trim|required'),
            );
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() === TRUE)
            {
                $this->load->model('Spu_model');
                $this->load->model('Goods_model');
                $this->load->model('Goods_tpl_model');
                $this->load->model('Goods_detail_model');
                $this->load->model('Goods_num_model');
                $this->load->model('Goods_spu_attr_val_model');
                //$this->load->model('Goods_spec_attr_name');
                $this->load->model('Goods_spec_attr_val_model');
                $this->load->model('Goods_sku_model');

                $aGoods_tpl = $this->Goods_tpl_model->get_by_id($tpl_id);
                if(empty($aGoods_tpl)){
                    showMessage('商品发布有问题，请与平台联系','/seller/goods');
                    exit;
                }

                //取得前面几级类目id
                $arrCategory = $this->Category_model->getLine($cid);
                $spu_id = $this->Spu_model->getSpuIdByCode($spu_code);

                //goods
                $data = array(
                    'tpl_id'=> $tpl_id,
                    'title' => $this->input->post( 'title' ),
    				'point' => $this->input->post( 'point' ),
    				//'price' => $price,
    				//'market_price' => round( floatval($this->input->post( 'market_price' )), 2),
    				'discount' => $this->input->post( 'discount' ),
    				'brand_id' => $this->input->post( 'b_id' ),
    				'shop_id' => $shop_id,
    				'category_id' => $cid,
    				'category_id_1' => $arrCategory['id_1'],
    				'category_id_2' => $arrCategory['id_2'],
    				'category_id_3' => $arrCategory['id_3'],
    				'trade_coding' => $this->input->post( 'trade_coding' ),
    				'barcode' => $this->input->post( 'barcode' ),
    				'pic_path' => $this->input->post( 'image_path' ),
    				//'video' => $this->input->post( 'video' ),
                    'spu_id' => $spu_id,
    				'is_presell' => 0,
    				'is_own' => 1,
    				'have_gift' => 0,
    				'status' => $this->input->post( 'g_state' ),
    				'addtime' => time(),
    				'updatetime' => time(),

                );

                // $cost_price = $this->input->post( 'cost_price' );
                // if(!empty($cost_price))
                // {
                //     $data['cost_price'] = round( floatval($this->input->post( 'cost_price' )), 2);
                // }
                
                if($id)
                {
                    //处理价格是否需要审核
                    $aGoods = $this->Goods_model->get_by_id($id);
                    if($price != $aGoods['price'])
                        $data['audit_price'] = $price;
                    if($aGoods['price']==0)
                        $data['price'] = $aGoods_tpl['price'];
                    if($aGoods['cost_price']==0)
                        $data['cost_price'] = $aGoods_tpl['cost_price'];
                    //-处理价格是否需要审核

                    $this->Goods_model->update_by_where(array('id'=>$id,'shop_id'=>$shop_id), $data);
                }
                else{
                    //处理价格是否需要审核
                    if($price == $aGoods_tpl['price'])
                        $data['price'] = $price;
                    else{
                        $data['price'] = $aGoods_tpl['price'];
                        $data['audit_price'] = $price;
                    }
                    if($cost_price == $aGoods_tpl['cost_price'])
                        $data['cost_price'] = $cost_price;
                    else{
                        $data['cost_price'] = $aGoods_tpl['cost_price'];
                        $data['audit_cost_price'] = $cost_price;
                    }

                    $id = $this->Goods_model->insert_string($data);
                }
                //-goods

                //goods_detail
                $publish_time = strtotime($this->input->post('starttime').' '.$this->input->post('starttime_H').':'.$this->input->post('starttime_i').':00');
                $m_content = str_replace('&quot;', '"', $this->input->post( 'm_body' ));
                $data_detail = array(
                    'goods_id'=>$id,
                    'is_self_get'=>0,
                    'publish_time'=>$publish_time,
                    'is_free_transport'=>$this->input->post('freight')==1?0:1,
                    'transport_id'=>$this->input->post('transport_id'),
                    'content'=>$this->input->post( 'content' ),
                    'm_content'=>$m_content,
                );
                $this->Goods_detail_model->insert($data_detail);
                //-goods_detail

                //goods_num
                $data_num = array(
                    'goods_id'=>$id,
                    'stock_num'=>$this->input->post( 'stock_num' ),
                    'stock_alert_num'=>$this->input->post( 'g_alarm' ),
                );
                $this->Goods_num_model->insert($data_num);
                //-goods_num

                //goods_spec规格
                /*
                //规格名:颜色,尺寸  --可自定义
                if(is_array($arrSpecName)){
                    $i=0;
                    foreach ($arrSpecName as $name_id=>$spec_name) {
                        $i++;
                        $data_spec_name = array(
                            'goods_id'=>$id,
                            'name_id'=>$name_id,
                            'spec_name'=>$spec_name,
                            'type'=>1,
                            'sort'=>$i,
                        );
                        $this->Goods_spec_attr_name->insert($data_spec_name);
                    }
                }*/
                $arrValId_NameId = array();
                if(is_array($arrSpecVal)){
                    foreach ($arrSpecVal as $name_id=>$aSpecVal) {
                        $i=0;
                        foreach ($aSpecVal as $key => $value) {
                            $i++;
                            $data_spec_val = array(
                                'goods_id'=>$id,
                                'name_id'=>$name_id,
                                'val_id'=>$key,
                                'spec_val'=>$value,
                                'sort'=>$i,
                            );
                            $this->Goods_spec_attr_val_model->insert_update($data_spec_val);

                            $arrValId_NameId[$key]=$name_id;
                        }
                    }
                }
                //-goods_spec

                //goods_sku
                if (is_array($arrSku)) {//print_r($arrValId_NameId);die;
                    $word_model = M('Word');
                    //规格
                    foreach ($arrSku as $aSku) {
                        $arrTitle = array();
                        foreach ($aSku['sp_value'] as $valId_tmp=>$v_tmp) {
                            $name_id = $arrValId_NameId[$valId_tmp];
                            $name = $word_model->getName($name_id);
                            $arrTitle[] = $name.':'.$v_tmp;
                        }



                        $sku_title = implode('_', array_values($arrTitle));
                        $sku_code = $id.':'.implode('_', array_keys($aSku['sp_value']));

                        //处理价格
                        $data_sku_audit_price = 0;
                        $data_sku_audit_cost_price = 0;
                        $aSkuExits = $this->Goods_sku_model->get_by_id($sku_code);
                        if(!empty($aSkuExits))
                        {
                            $data_sku_price = $aSkuExits['price'];
                            if($aSku['price'] == $aSkuExits['price'])
                                $data_sku_audit_price = 0;
                            else
                                $data_sku_audit_price = $aSku['price'];

                            $data_sku_cost_price = $aSkuExits['cost_price'];
                            if($aSku['costprice'] == $aSkuExits['cost_price'])
                                $data_sku_audit_cost_price = 0;
                            else
                                $data_sku_audit_cost_price = $aSku['costprice'];
                        }
                        else{
                            $data_sku_price = $aGoods['price'];
                            if($aSku['price'] == $aGoods['price'])
                                $data_sku_audit_price = 0;
                            else
                                $data_sku_audit_price = $aSku['price'];

                            $data_sku_cost_price = $aGoods['cost_price'];
                            if($aSku['costprice'] == $aGoods['cost_price'])
                                $data_sku_audit_cost_price = 0;
                            else
                                $data_sku_audit_cost_price = $aSku['costprice'];
                        }
                        //-处理价格
                        $data_sku = array(
                            'goods_id'=>$id,
                            'sku_title'=>$sku_title,
                            'sku_code'=>$sku_code,      //is key
                            'num'=>$aSku['stock'],
                            'price'=>$data_sku_price,
                            'audit_price'=>$data_sku_audit_price,
                            'cost_price'=>$data_sku_cost_price,
                            'audit_cost_price'=>$data_sku_audit_cost_price,
                            //'market_price'=>$aSku['marketprice'],
                            //'cost_price'=>$aSku['sku'],
                        );
                        
                        $this->Goods_sku_model->insert($data_sku);

                        //有审核价格，商品审核价也须审核
                        if($data_sku_audit_price>0)
                            $this->Goods_model->update_by_where(array('id'=>$id,'shop_id'=>$shop_id), array('audit_price'=>$price));

                    }
                }
                //-goods_sku
                
                //goods_spu
                $attrValList = $this->Spu_model->getAttrValBySpuCode($spu_code);
                foreach ($attrValList as $key => $a) {
                    $data_spu = array(
                        'goods_id'=>$id,
                        'name_id'=>$a['name_id'],
                    );
                    if($a['input_type']==1)
                        $data_spu['val']=$this->input->post( 'prop_'.$a['name_id'] );
                    else
                        $data_spu['val_id']=$this->input->post( 'prop_'.$a['name_id'] );
                    $this->Goods_spu_attr_val_model->insert_update($data_spu);
                }
                //-goods_spu
                
                $gotoUrl = urlShop('seller/goods_add', 'add_step4', array('id' => $id));
                showDialog(lang('nc_common_op_succ').'，图片由总台上传，无需上传图片', $gotoUrl, 'succ','',0);
                // go_redirect(urlShop('seller/goods_add', 'add_step4', array('id' => $id)));

                exit;
            }
            else
            {
                $gotoUrl  = getReferer();
                showDialog(lang('store_goods_index_goods_edit_fail'), $gotoUrl,'',0);
                // go_redirect($gotoUrl);
            }
        }
    }

    public function add_step3(){
        $id = $this->input->get('id');
        $shop_id = $this->loginUser['shop_id'];

        $this->load->helper('goods');
        $this->load->model('Goods_pic_model');
        //$this->load->model('Goods_album_model');
        //$this->load->model('Goods_album_pic_model');

        $goods_pic = '';
        $aGoods = $this->Goods_model->get_by_where(array('id'=>$id,'shop_id'=>$shop_id),'pic_path');
        if(!empty($aGoods))
            $goods_pic = $aGoods['pic_path'];
        Tpl::output('goods_pic',$goods_pic);

        //$cid = $aGoods['category_id'];
        $pic_list = $this->Goods_pic_model->get_list(array('goods_id'=>$id,'shop_id'=>$shop_id),'*','sort asc,id asc');

        Tpl::output('goods_id',$id);
        Tpl::output('pic_list',$pic_list);
        Tpl::showpage('goods_add_step3');

    }

    public function add_step4(){
        $id = $this->input->get('id');
        $this->lang->load(array('user_goods','common'));

        Tpl::output('loginUser',$this->loginUser);
        Tpl::output('goods_id',$id);
        Tpl::showpage('goods_add_step4');
    }

    /**
     * ajax获取商品分类的子级数据
     */
    public function ajax_category_list() {
        $cid = intval($this->input->get('cid'));
        $deep = intval($this->input->get('deep'));
        if ($cid <= 0 || $deep <= 0 || $deep >= 4) {
            exit();
        }

        $shop_id = $this->loginUser['shop_id'];
       
        $list = $this->Category_model->getShopList($shop_id, $cid, $deep);
        if (empty($list)) {
            exit();
        }
        echo json_encode($list);
    }

    /**
     * AJAX查询品牌
     */
    public function ajax_get_brand(){
        $this->load->model('Brand_model');
        $category_id = intval($this->input->get('category_id'));
        $initial = $this->input->get('letter');
        $keyword = $this->input->get('keyword');
        $type = $this->input->get('type');

        if (!in_array($type, array('letter', 'keyword')) || ($type == 'letter' && empty($initial)) || ($type == 'keyword' && empty($keyword))) {
            echo json_encode(array());die();
        }

        $brand_list = $this->Brand_model->getList($category_id);
        if(empty($brand_list))
        {
            $where ='status=1';
            if ($type == 'letter') {
                switch ($initial) {
                    case 'all':
                        break;
                    case '0-9':
                        $where .= ' and initial in(0,1,2,3,4,5,6,7,8,9)';
                        //$where['initial'] = array('in', array(0,1,2,3,4,5,6,7,8,9));
                        break;
                    default:
                        $where .= " and initial='$initial'";
                        //$where['initial'] = $initial;
                        break;
                }
            }
            else {
                $where .= " and (name like '%$keyword%' or initial like '%$keyword%')";
                //$where['name|initial'] = array('like', '%' . $keyword . '%');
            }
            $brand_list = $this->Brand_model->get_list($where);
        }

        echo json_encode($brand_list);die();
    }

    public function ajax_goods_tpl(){
        $this->load->model('Goods_tpl_model');
        $cid = intval($this->input->get('cid'));
        $keyword = $this->input->get('keyword');

        $strWhere = "title like '%$keyword%'";
        if($cid)
            $strWhere = "$strWhere and (category_id=$cid or category_id=$cid or category_id_1=$cid or category_id_2=$cid or category_id_3=$cid)";
        $aGoods_tpl_list = $this->Goods_tpl_model->get_list($strWhere,'tpl_id,title');

        echo json_encode($aGoods_tpl_list);exit();
    }

    /**
     * 上传图片
     */
    public function image_upload() {
        /*// 判断图片数量是否超限
        $model_album = Model('album');
        $album_limit = $this->store_grade['sg_album_limit'];
        if ($album_limit > 0) {
            $album_count = $model_album->getCount(array('store_id' => $_SESSION['store_id']));
            if ($album_count >= $album_limit) {
                $error = L('store_goods_album_climit');
                if (strtoupper(CHARSET) == 'GBK') {
                    $error = Language::getUTF8($error);
                }
                exit(json_encode(array('error' => $error)));
            }
        }*/
        $this->load->library('UploadFile');
        $this->load->helper('goods');
        $this->load->model('Goods_album_model');
        $shop_id = $this->loginUser['shop_id'];
        //取默认相册
        $aAlbum = $this->Goods_album_model->get_by_where(array('shop_id' => $shop_id, 'is_default' => 1));

        // 上传图片
        $upload = new UploadFile();
        $upload->set('default_dir', ATTACH_GOODS . '/' . $shop_id . '/' . $upload->getSysSetPath());
        $upload->set('max_size', C('image_max_filesize'));

        $upload->set('thumb_width', GOODS_IMAGES_WIDTH);
        $upload->set('thumb_height', GOODS_IMAGES_HEIGHT);
        $upload->set('thumb_ext', GOODS_IMAGES_EXT);
        $upload->set('fprefix', $shop_id);
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
        $result = $upload->upfile($_POST['name']);
        if (!$result) {
            $output = array();
            $output['error'] = $upload->error;
            $output = json_encode($output);
            exit($output);
        }

        $img_path = $upload->getSysSetPath() . $upload->file_name;

        // 取得图像大小
        list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . '/' . ATTACH_GOODS . '/' . $shop_id . '/' . $img_path);

        // 存入相册
        $this->load->model('Goods_album_pic_model');
        $image = explode('.', $_FILES[$_POST['name']]["name"]);
        $insert_array = array();
        $insert_array['pic_name'] = $image['0'];
        $insert_array['album_id'] = $aAlbum['id'];
        $insert_array['pic'] = $img_path;
        $insert_array['size'] = intval($_FILES[$_POST['name']]['size']);
        $insert_array['spec'] = $width . 'x' . $height;
        $insert_array['addtime'] = time();
        $insert_array['shop_id'] = $shop_id;
        $this->Goods_album_pic_model->insert_string($insert_array);

        $data = array ();
        $data ['thumb_name'] = cthumb($upload->getSysSetPath() . $upload->thumb_image, 240, $shop_id);
        $data ['name']      = $img_path;

        // 整理为json格式
        $output = json_encode($data);
        echo $output;
        exit();
    }

    /**
     * 保存商品颜色图片
     */
    public function save_image(){
        $goods_id = $this->input->post('goods_id');
        $arrImg = $this->input->post('img');
        $shop_id = $this->loginUser['shop_id'];

        $this->load->model('Goods_pic_model');

        // 保存
        $insert_array = array();
        foreach ($arrImg as $key => $value) {
            if(!empty($value['path']))
            {
                $data = array('shop_id'=>$shop_id,
                    'goods_id'=>$goods_id,
                    'pic'=>$value['path'],
                    'sort'=>$value['sort'],
                    );

                if(!empty($value['id']))
                    $data['id']=$value['id'];

                $this->Goods_pic_model->insert($data);
            }
            
            if($value['default']==1)
            {
                $this->Goods_model->update_by_where(array('id'=>$goods_id,'shop_id'=>$shop_id), array('pic_path'=>$value['path']));
            }
        }
        
        go_redirect(urlShop('seller/goods_add', 'add_step4', array('id' => $goods_id)));
    }
}
