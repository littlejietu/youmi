<?php
/**
 * 地址service
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Buy_service
{
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('Goods_model');
        $this->ci->load->model('Cart_model');
        $this->ci->load->model('Shop_model');
        $this->ci->load->model('Transport_tpl_model');
    }

    /**
     * 函数用途描述
     * @date: 2016年3月21日 上午11:13:22
     * @author: hbb
     * @param: variable
     * @return:
     */
    public function get_buy_goods($cart_id, $city_id = 0)
    {
        $this->ci->load->service('buying_service');
        $buy_items = parseBuyItems($cart_id);
        if (empty($buy_items)) {
            return -1;
        }
        $this->ci->load->helper('goods');
        $goods_sku_id = key($buy_items);
        list($goods_id, $sku_id) = explode('-', $goods_sku_id);
        $quantity = current($buy_items);
        $goods_info = $this->ci->Goods_model->get_info_by_id($goods_id, '*', array('goods_num'));

        //处理SKU
        $sku_info = $sku_id ? $this->ci->Cart_model->get_sku_info($sku_id) : array();

        $goods_info['sku_info']=$sku_info;

        $this->ci->buying_service->goods_info=$goods_info;

        //真实库存
        $stock = $this->ci->buying_service->getRealStock();


        $price =  empty($sku_info) ? $goods_info['price'] : $sku_info['price'];
        $activity_price=$this->ci->buying_service->getrealPriceInActivity($goods_info['tpl_id'],$price);
        $price = $activity_price>0 ? $activity_price : $price;

        $sku = empty($sku_info) ? '' : str_replace('_', ';', $sku_info['sku_title']);

        unset($sku_info);

        //店铺信息
        $shop_info = $this->ci->Shop_model->get_by_id($goods_info['shop_id'], 'id,name');
        if (empty($goods_info)) {
            return 0;
        }

        //进一步处理数组
        $data = array();
        $data['buy'][0]['shop'] = array(
            'id' => $shop_info['id'],
            'name' => $shop_info['name'],
            'url' => '',
        );
        $data['buy'][0]['goods_list'][0] = array(
            'cart_id' => '',
            'num' => $quantity,
            'goods_id' => $goods_info['id'],
            'goods_price' => (string)$price,
            'goods_title' => $goods_info['title'],
            'goods_point' => $goods_info['point'],
            'pic_url' => thumb($goods_info),
            'sku_id' => $sku_id,
            'sku' => $sku,
        );

        //配送方式
        $arrDeliverParam[] = array('sku_id' => $sku_id, 'goods_id' => $goods_info['id'], 'num' => $quantity);
        $delivery_type = $this->ci->Goods_sku_model->delivers_display($arrDeliverParam);
        switch ($delivery_type) {
            case 1:
                $data['delivery'][] = array('title' => '九号街区配送');
                break;
            case 2:
                $data['delivery'][] = array('title' => '快递配送');
                $goods_info['goods_num'] = $quantity;
                break;
            case 3:
                $data['delivery'] = array(
                    array('title' => '九号街区配送'),
                    array('title' => '快递配送')
                );
                break;
        }

        //计算运费
        $fare = 0;
        $goods_info['goods_num']=$quantity;
        $goods_list[0] = $goods_info;
        $fare=$this->ci->buying_service->getFare($goods_list,$city_id);

        //计算费用
        $data['amount'] = array(
            'total_goods' => $price * $quantity,
            'fare' => $fare
        );

        return $data;
    }
}