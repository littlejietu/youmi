<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->helper('goods');
		$this->ci->load->model('Goods_model');
		$this->ci->load->model('Goods_num_model');
		$this->ci->load->model('First_model');
		$this->ci->load->model('First_place_model');
		$this->ci->load->model('Category_model');
	}
	
// 	/**
// 	 * 获取某推首位的推首列表
// 	 * @param unknown $place_id  推首位
// 	 * @param unknown $num  推首条数
// 	 * @param string $fields  所取字段
// 	 */
// 	public function get_first($place_id,$num,$fields='*')
// 	{
// 	    $where['place_id'] = $place_id;
// 	    $where['status'] = 1;
// 	    $result = $this->ci->First_model->get_list($where,$fields,'sort ASC',$num);
// 	    return $result;
// 	}
	
	
	/**
	 * 猜你喜欢
	 * @param string $goods_id
	 */
	public function wish_goods($tpl_id = '', $shop_id, $num)
	{
	    $limit = $num;
	    $id = array();
	    $post_id = explode(',',$tpl_id);

	    $fields = 'id as goods_id,tpl_id,sku_id,title as name,price,market_price as original_price,pic_path as pic_url,shop_id,category_id';
	    $orderBy = 'rand()';
	    $where = array();
	    if (!empty($tpl_id))
	    {
	    	$where = array('id'=>$post_id);
	        $category = $this->ci->Goods_model->get_list($where);
	        foreach ($category as $value)
	        {
	            $id[] = $value['category_id'];
	        }
	        unset($where);
	        $where = array('id NOT'=>$post_id, 'shop_id'=>$shop_id,'status'=>1);
	        if (!empty($id))
	        {
	            $where['category_id'] = $id;
	        }
	        $orderBy = 'updatetime DESC';
	    }else{
	    	$where = array('shop_id'=>$shop_id,'status'=>1);
	    }

        $goods = $this->ci->Goods_model->get_list($where, $fields,$orderBy,$limit);
       	if(empty($goods)){
       		$goods = $this->ci->Goods_model->get_list(array('shop_id'=>$shop_id,'status'=>1),$fields,'rand()',$limit);
       	}

        foreach ($goods as $key => $value)
        {
            $type_name = $this->ci->Category_model->get_by_id($value['category_id']);
            $goods[$key]['cate_name'] = $type_name['name'];
            $goods[$key]['to_url'] = 'zooer://productdetail?tpl_id='.$value['tpl_id'];
            $goods[$key]['pic_url'] = cthumb($value['pic_url']);
            $goods[$key]['cate_url'] = 'zooer://search?keyword='.$type_name['name'].'&category_id='.$value['category_id'];
        }

        return empty($goods)?null:$goods;


	    /*if (!empty($goods_id))
	    {
	    	
	        // $where['category_id'] = $id;
	        // $where['id NOT'] = $post_id;
	        $goods = $this->ci->Goods_model->get_list($where,'id as goods_id,sku_id,title as name,price,market_price as original_price,pic_path as pic_url,shop_id,category_id','updatetime DESC',$limit);
	        foreach ($goods as $key => $value)
	        {
	            $type_name = $this->ci->Category_model->get_by_id($value['category_id']);
	            $goods[$key]['cate_name'] = $type_name['name'];
	            $goods[$key]['to_url'] = 'zooer://productdetail?id='.$value['goods_id'];
	            $goods[$key]['pic_url'] = cthumb($value['pic_url']);
	            $goods[$key]['cate_url'] = 'zooer://search?keyword='.$type_name['name'].'&category_id='.$value['category_id'];
	        }
	        if (!empty($goods))
	        {
	            return $goods;
	        }
	    }
	    if (empty($goods_id) || empty($goods))
	    {
	        $where = array();
// 	        $a = $this->ci->Goods_num_model->get_by_buy_num($limit);
// 	        foreach ($a as $value)
// 	        {
// 	            $id[] = $value['goods_id'];
// 	        }
	        $goods = $this->ci->Goods_model->get_list($where,'id as goods_id,sku_id,title as name,price,pic_path as pic_url,shop_id,category_id','rand()',$limit);
	        foreach ($goods as $key => $value)
	        {
	            $type_name = $this->ci->Category_model->get_by_id($value['category_id']);
	            $goods[$key]['cate_name'] = $type_name['name'];
	            $goods[$key]['to_url'] = 'zooer://productdetail?id='.$value['goods_id'];//.'&shopid='.$value['shop_id'];
	            $goods[$key]['pic_url'] = cthumb($value['pic_url']);
	            $goods[$key]['cate_url'] = 'zooer://search?keyword='.$type_name['name'].'&category_id='.$value['category_id'];
	        }
	        return $goods;
	    }*/
	    
	}
	
	
	/**
	 * 热卖商品
	 */
	public function hot_sale($shop_id,$limit)
	{
	    
	    if (empty($limit))
	    {
	        $limit = 8;
	    }
	    $result = $this->ci->Goods_num_model->get_list_by_buy_num($limit,$shop_id);
	    
	    foreach ($result as $key => $value)
	    {
			$result[$key]['pic_url'] = cthumb($value['pic_url']);
			$result[$key]['to_url'] = 'zooer://productdetail?tpl_id='.$result[$key]['tpl_id'];
			
	    }
	    return $result;
	}
	
	/**
	 * 热搜关键词
	 */
	public function get_hot_search_keywords($limit)
	{
	    $this->ci->load->model('Search_key_model');
	    $res = $this->ci->Search_key_model->get_list($where = array(),'k','times DESC',$limit);
	    $result = array();
	    if (!empty($res))
	    {
	        foreach ($res as $key => $value)
	        {
	            $result[$key] = array(
	                'word' => $value['k'],
	                'type'=>'',
	                'actionUrl'=>'',
	                'hotWordCategory'=> '',
	                'position'=>'',
	            );
	        }
	    }
	    return $result;
	}
	
}