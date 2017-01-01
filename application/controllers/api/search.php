<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Search extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        //$this->load->model('First_model');
        $this->load->model('Goods_model');
        $this->load->service('goods_service');
        $this->load->model('Search_key_model');
        $this->load->model('Goods_num_model');
    }

       
    public function index()
    {
        $shop_id=!empty($_REQUEST['shop_id']) ? (int)$_REQUEST['shop_id']:1;        
        $hot_search = $this->goods_service->get_hot_search_keywords($limit = 9);
        if (!empty($hot_search))
        {
            $data['hot_search'] = $hot_search;
        }
        $hot_sale = $this->goods_service->hot_sale($shop_id,10);
        if (!empty($hot_sale))
        {
            $data['hot_sale'] = $hot_sale;
        }
        if (!empty($data))
        {
            output_data($data);exit;
        }
        else 
        {
            output_error(-1,'没有热搜关键词和热卖商品');exit;
        }
        
    }
    
    public function result()
    {
            $keyword=!empty($_REQUEST['keyword']) ? $_REQUEST['keyword' ] : '';
            $category_id=!empty($_REQUEST['category_id']) ? $_REQUEST['category_id' ] : '';
            $sort_by=!empty($_REQUEST['sort_by']) ? (int)$_REQUEST['sort_by' ] : 0;
            $sort_key=!empty($_REQUEST['sort_key']) ? 1 : 0;
            $shop_id=!empty($_REQUEST['shop_id']) ? (int)$_REQUEST['shop_id']:1;
            $page=!empty($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
            $page_size=!empty($_REQUEST['page_size']) ? (int)$_REQUEST['page_size'] : 8;
            $search_scene=!empty($_REQUEST['search_scene']) ? (int)$_REQUEST['search_scene'] : 0;
            $goods=array();
            $key = trim($keyword);
            
            if (empty($key))
            {
                output_error(-1,'请输入有效的关键词');exit;
            }
            $where['k'] = "'$keyword'";
            $arr = $this->Search_key_model->get_by_where($where);
            if (empty($arr))
            {
                $search_data = array(
                    'k' => $keyword,
                    'sort' => 255,
                    'times' => 1,
                    'search_time' => time(),
                );
                $this->Search_key_model->insert($search_data);
            }
            else 
            {
                $search_data['times'] = $arr['times']+1;
                $search_data['search_time'] = time();
                $this->Search_key_model->update_by_id($arr['id'],$search_data);
            }
            $goods=$this->map_keyword_result($category_id, $keyword,$shop_id,$sort_by,$sort_key,$page,$page_size);
            if (empty($goods))
            {
                output_error(-1,'没有符合条件的结果');exit;
            }
            $data=array(
                'goods'=>$goods['rows'],
                'total' => $goods['total'],
                'curpage'=>$page,
                'totalpage'=>ceil($goods['total']/$page_size),
            );
            output_data($data);exit;
    }
    
    /**
     * 根据关键词排序
     * @param unknown $keyword
     * @param unknown $bid
     * @param unknown $kid
     * @param unknown $page
     * @param unknown $page_size
     */
    private function map_keyword_result($category_id, $keyword,$shop_id,$bid,$kid,$page,$page_size){
        $this->load->helper('Goods');

        $where = array('status'=>1);
        if($category_id){
            $where['category_id'] = $category_id;
        }else{
            $where['title like'] = "'%$keyword%'";
        }
        $where['shop_id'] = $shop_id;
        $orderkey = '';
        $ordervalue = 'DESC';
        $orderby = '';
        if ($bid == 2)
        {
            $orderkey = 'addtime';
        }
        if ($bid == 3)
        {
            $orderkey = 'price';
        }
        if ($kid == 1)
        {
            $ordervalue = 'ASC';
        }
        if (!empty($orderkey))
        {
            $orderby = $orderkey.' '.$ordervalue;
        }        
        $list = $this->Goods_model->fetch_page($page,$page_size,$where,'id,tpl_id,sku_id,shop_id,title as name,price,pic_path as pic_url,addtime',$orderby);
        //echo $this->Goods_model->db->last_query();die;
        if (empty($list['rows']))
        {
            return array();
        }
        unset($where);
        $data = $list['rows'];
        foreach ($data as $key => $value)
        {
            $where['goods_id'] = $value['id'];
            $arr = $this->Goods_num_model->get_by_where($where);
            $data[$key]['comments_num'] = $arr['be_comment']+$arr['be_comment_fake'];
            $data[$key]['be_buy_num'] = $arr['be_buy_num']+$arr['be_buy_num_fake'];
            if ($data[$key]['comments_num'] == 0)
            {
                $data[$key]['fav_rate'] = '0%';
            }
            else 
            {
                $data[$key]['fav_rate'] = round($arr['be_comment_good']/$data[$key]['comments_num']*100).'%';
            }
        }
        if ($bid == 1 )
        {
            foreach ($data as $key=>$value){
                $id[$key] = $value['id'];
                $buy_num[$key] = $value['be_buy_num'];
            }
            if ($kid == 1)
            {
                array_multisort($buy_num,SORT_NUMERIC,SORT_ASC,$id,SORT_STRING,SORT_ASC,$data);
            }
            else 
            {
                array_multisort($buy_num,SORT_NUMERIC,SORT_DESC,$id,SORT_STRING,SORT_ASC,$data);
            }
        }
        $result = array();
        if (empty($data))
        {
            return array();
        }
        foreach ($data as $key => $value)
        {
            $result['rows'][$key] = array(
                'goods_id' => $value['id'],
                'tpl_id' => $value['tpl_id'],
                'sku_id'=> $value['sku_id'],
                'shop_id' => $value['shop_id'],
                'name' => $value['name'],
                'price' => $value['price'],
                'comments_num' => $value['comments_num'],
                'fav_rate' => $value['fav_rate'],
                'pic_url' => cthumb($value['pic_url']),
                'to_url' => 'zooer://productdetail?tpl_id='.$value['tpl_id'],

            );
        }
        $result['total'] = $list['count'];
        return $result;
    }

}
