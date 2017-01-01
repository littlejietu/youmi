<?php
/**
* 统计service
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Goodsnum_service
{
	public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('Goods_num_model');

		
	}

	public function onComment($goods_id, $buyer_userid, $score_level, $pic_num=0){
		$prefix = $this->ci->Goods_num_model->prefix();

		$sql = "update ".$prefix."goods_num a set be_comment=(select count(1) from ".$prefix."trd_comment_goods where goods_id=a.goods_id  and status <> -1) where goods_id=$goods_id";
		$this->ci->Goods_num_model->execute($sql);
		
		if($score_level==5)
		{
			$sql = "update ".$prefix."goods_num a set be_comment_good=(select count(1) from ".$prefix."trd_comment_goods where goods_id=a.goods_id and score_level=5 and status <> -1)   where goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);
		}
		if($score_level==3 || $score_level==4)
		{
			$sql = "update ".$prefix."goods_num a set be_comment_mid=(select count(1) from ".$prefix."trd_comment_goods where goods_id=a.goods_id and (score_level=3 or score_level=4) and status <> -1)   where goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);
		}

		if($score_level==1 || $score_level==2)
		{
			$sql = "update ".$prefix."goods_num a set be_comment_bad=(select count(1) from ".$prefix."trd_comment_goods where goods_id=a.goods_id and (score_level=1 or score_level=2) and status <> -1)   where goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);
		}
		if($pic_num>0){
			$sql = "update ".$prefix."goods_num a set be_comment_pic=(select count(1) from ".$prefix."trd_comment_goods where goods_id=a.goods_id and pic_path<>''  and status <> -1 ) where goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);
		}

	}

	public function onCollect(){
		
	}

	/**
    * 商品减库存
    * @param: $sku_id: sku id, $goods_id:商品id, $num:数量
    * @return:none
    */
	public function onOrderPackage($sku_id, $goods_id, $num){
		$aSku = array();
		$this->ci->load->model('Goods_sku_model');
		$this->ci->load->model('Goods_num_model');
		$prefix = $this->ci->Goods_sku_model->prefix();

		if(!empty($sku_id)){
			$aSku = $this->ci->Goods_sku_model->get_by_where( array('id'=>$sku_id) );
		}
		
		if(!empty($aSku)){
			//减sku
			$left_num = $aSku['num'] - $num;
			if($left_num<0)
				$left_num = 0;
			$this->ci->Goods_sku_model->update_by_where(array('id'=>$sku_id), array('num'=>$left_num));
			//统计所有sku的库存
			$sql = "update ".$prefix."goods_num a set stock_num=(select sum(num) from ".$prefix."goods_sku where goods_id=a.goods_id ) where goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);
		}else{
			//减商品库存
			$sql = "update ".$prefix."goods_num a set stock_num=( CASE WHEN (stock_num-$num)<0 THEN 0 ELSE (stock_num-$num) END ) WHERE goods_id=$goods_id";
			$this->ci->Goods_num_model->execute($sql);

		}
	
	}

	//不用实时调用，该方法暂无用
	public function onOrderSale($orderId){	    
	    $prefix = $this->ci->Goods_num_model->prefix();
	    $sql = "UPDATE ".$prefix."goods_num a SET be_buy_num=(SELECT SUM(num) FROM ".$prefix."trd_order_goods b WHERE b.goods_id=a.goods_id AND b.order_id IN(SELECT order_id FROM ".$prefix."trd_order WHERE STATUS='Finished')) 
                    WHERE a.goods_id IN(SELECT goods_id FROM ".$prefix."trd_order_goods WHERE order_id=".$orderId.")";
	    $this->ci->Goods_num_model->execute($sql);
	}


}