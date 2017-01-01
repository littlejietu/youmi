<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_service {
	public function __construct()
	{
		$this->ci = & get_instance();

		
	}

	public function tplToGoods($tpl_id, $shop_id){
		$this->ci->load->model('Goods_model');
		$this->ci->load->model('Goods_detail_model');
		$this->ci->load->model('Goods_num_model');
		$this->ci->load->model('Goods_tpl_pic_model');
		$this->ci->load->model('Goods_pic_model');
		$this->ci->load->model('Goods_tpl_spu_attr_val_model');
		$this->ci->load->model('Goods_tpl_sku_model');
		$this->ci->load->model('Goods_sku_model');

		$data = array();
		$data_detail = array();
		$data_num = array();
		$aGoodsTpl = $this->ci->Goods_tpl_model->get_by_id($tpl_id);
		if(!empty($aGoodsTpl)){
			$data = array('shop_id'=>$shop_id, 'tpl_id'=>$aGoodsTpl['tpl_id'], 'title'=>$aGoodsTpl['title'],'market_price'=>$aGoodsTpl['market_price'],
				'point'=>$aGoodsTpl['point'], 'comm_precent'=>$aGoodsTpl['comm_precent'], 'comm_price'=>$aGoodsTpl['comm_price'], 'pic_path'=>$aGoodsTpl['pic_path'],
				'brand_id'=>$aGoodsTpl['brand_id'], 'spu_id'=>$aGoodsTpl['spu_id'], 
				'category_id'=>$aGoodsTpl['category_id'], 'category_id_1'=>$aGoodsTpl['category_id_1'],
				'category_id_2'=>$aGoodsTpl['category_id_2'], 'category_id_3'=>$aGoodsTpl['category_id_3'],
				'is_presell'=>0, 'is_own'=>1, 'have_gift'=>0, 'status'=>$aGoodsTpl['status'], 'service_sort'=>$aGoodsTpl['service_sort']
				);

			$data_detail['content'] = $aGoodsTpl['content'];
			$data_detail['m_content'] = $aGoodsTpl['m_content'];
			$data_detail['transport_id'] = $aGoodsTpl['transport_id'];

			//总店，才需同步价格
			if($shop_id==C('basic_info.SHOP_DEFAULT_ID')){
				$data['cost_price'] = $aGoodsTpl['cost_price'];
				$data['price']=$aGoodsTpl['price'];
				$data_num['stock_num'] = $aGoodsTpl['stock_num'];
			}
		}

		$goods_id = 0;
		$aGoods = $this->ci->Goods_model->get_by_where(array('tpl_id'=>$tpl_id, 'shop_id'=>$shop_id));
		//添加
		if(empty($aGoods)){
			$data['price']=$aGoodsTpl['price'];
			$data['cost_price']=$aGoodsTpl['cost_price'];
			$data['addtime'] = time();
			$goods_id = $this->ci->Goods_model->insert_string($data);

			$data_detail['goods_id'] = $goods_id;
			$this->ci->Goods_detail_model->insert_string($data_detail);

			$data_num['goods_id'] = $goods_id;
			$this->ci->Goods_num_model->insert($data_num);
		}
		else{
			
			$goods_id = $aGoods['id'];
			$this->ci->Goods_model->update_by_id($goods_id, $data);

			$this->ci->Goods_detail_model->update_by_id($goods_id, $data_detail);

			$data_num['goods_id'] = $goods_id;
			$this->ci->Goods_num_model->insert($data_num);

		}

		//spu
		$aGoodsTplSpuList = $this->ci->Goods_tpl_spu_attr_val_model->get_list(array('goods_id'=>$tpl_id));
		foreach ($aGoodsTplSpuList as $key => $a) {
			unset($a['id']);
			$this->ci->Goods_tpl_spu_attr_val_model->insert_update($a);
		}
		//-spu

		//sku
		$sku_list = $this->ci->Goods_tpl_sku_model->get_list(array('goods_id'=>$tpl_id));
		$strSku_code = '';
		$price_min = 0;
		$sku_code_default = '';
		foreach ($sku_list as $a) {
			$code = substr($a['sku_code'], strpos($a['sku_code'],':')+1);
			$sku_code = $goods_id.':'.$code;
			$strSku_code .= "'".$sku_code."',";
			$aSku = $this->ci->Goods_sku_model->get_by_id($sku_code);
			if(!empty($aSku)){

				$data_sku = array(
	                'goods_id'=>$goods_id,
	                'sku_title'=>$a['sku_title'],
	                //'sku_code'=>$sku_code,      //is key
	                //'num'=>0,
	                //'price'=>$aGoodsTpl['price'],
	                //'audit_price'=>0,
	                'market_price'=>$a['market_price'],
	                'comm_precent'=>$a['comm_precent'],
	            );
				//总店，才需同步价格
				if($shop_id==C('basic_info.SHOP_DEFAULT_ID')){
					$data_sku['price'] = $a['price'];
					$data_sku['cost_price'] = $a['cost_price'];
					$data_sku['comm_price'] = $a['comm_price'];
					$data_sku['num'] = $a['num'];
				}
				$this->ci->Goods_sku_model->update_by_id($sku_code, $data_sku);

				if($aSku['price']<=$price_min || $price_min==0){
					$price_min = $aSku['price'];
					$sku_code_default = $sku_code;
				}
			}
			else{
				$data_sku = array(
	                'goods_id'=>$goods_id,
	                'sku_title'=>$a['sku_title'],
	                'sku_code'=>$sku_code,      //is key
	                'num'=>0,
	                'price'=>$a['price'],
	                'audit_price'=>0,
	                'audit_cost_price'=>0,
	                'market_price'=>$a['market_price'],
	                'cost_price'=>$a['cost_price'],
	                'comm_price'=>$a['comm_price'],
	                'comm_precent'=>$a['comm_precent'],
	            );
	            
	            $this->ci->Goods_sku_model->insert($data_sku);

	            if($aGoodsTpl['price']<=$price_min || $price_min==0){
					$price_min = $aGoodsTpl['price'];
					$sku_code_default = $sku_code;
				}
            }
		}

		if(!empty($strSku_code) && $strSku_code != ','){
            $strSku_code = trim($strSku_code,',');
            $this->ci->Goods_sku_model->delete_by_where("goods_id=$goods_id and sku_code not in($strSku_code)");

            //默认sku_id
            //$arrSku_codeTmp = explode(',', $strSku_code);
            //$sku_codeFirst = trim($arrSku_codeTmp[0],"'");
            // $arrSkuTmp = $this->ci->Goods_sku_model->get_by_id($sku_codeFirst);
            // $sku_id_default = $arrSkuTmp['id'];
            // $this->ci->Goods_model->update_by_id($goods_id ,array('sku_id'=>$sku_id_default));
            /*if($shop_id==1){
            	echo 'sku_code:'.$sku_code_default;
            	echo '--price'.$price_min;
            	die;
            }*/
            if(!empty($sku_code_default)){
            	$arrSkuTmp = $this->ci->Goods_sku_model->get_by_id($sku_code_default);
            	$sku_id_default = $arrSkuTmp['id'];
            	$this->ci->Goods_model->update_by_id($goods_id ,array('price'=>$price_min,'sku_id'=>$sku_id_default));
            }
			//-默认sku_id
        }
        else{//没有选中规格
        	$this->ci->Goods_sku_model->delete_by_where("goods_id=$goods_id");
        	//默认sku_id
        	$this->ci->Goods_model->update_by_id($goods_id ,array('sku_id'=>0));
        }
		//-sku



		//pic
		/*$aGoodsTplPicList = $this->ci->Goods_tpl_pic_model->get_list(array('goods_id'=>$tpl_id));
		foreach ($aGoodsTplPicList as $key => $a) {
			unset($a['id']);
			$this->ci->Goods_pic_model->insert($a);
		}*/
		//-pic
	}

	public function tplToGoodsAll($tpl_id){
		$this->ci->load->model('Shop_model');
		$aShopList = $this->ci->Shop_model->get_list(array('status'=>1));
		foreach ($aShopList as $key => $a) {
			$this->tplToGoods($tpl_id, $a['id']);
		}
	}

	public function allTplByShopId($shop_id){
		$this->ci->load->model('Goods_tpl_model');
		$aTplList = $this->ci->Goods_tpl_model->get_list(array('status'=>1));
		foreach ($aTplList as $key => $a) {
			$this->tplToGoods($a['tpl_id'], $shop_id);
		}
	}

	public function tplOnOff($tpl_id, $bOnline){
		$this->ci->load->model('Goods_model');
		$status = 2;
		if($bOnline)
			$status = 1;
		$this->ci->Goods_model->update_by_where(array('tpl_id'=>$tpl_id), array('status'=>$status) );
	}

}