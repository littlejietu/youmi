<?php
/**
 * 购物车service
 * @date: 2016年3月17日 上午11:19:06
 * @author: hbb
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart_service
{
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('Cart_model');
        $this->ci->load->model('Goods_model');
        $this->ci->load->model('Goods_sku_model');
        $this->ci->load->model('Shop_model');
        $this->ci->load->model('Transport_tpl_model');
        $this->ci->load->service('buying_service');
    }

    /**
     * 构造购物车列表数据
     * @date: 2016年3月17日 上午11:19:34
     * @author: hbb
     * @param: int $uid
     * @return: array
     */
    public function initGoodsList($uid, $cart_id = array(), $city_id = 0)
    {
        $this->ci->load->helper('goods');
        //区分购物车还是订单确认页
        if ($cart_id) {
            $buy_items = parseBuyItems($cart_id);
            if (empty($buy_items)) {
                return -2;
            }
            if (count($buy_items) > 50) {
                return -3;
            }

            $goods_sku_ids = array_keys($buy_items);

            foreach ($goods_sku_ids as $v) {
                $arrIds = explode('-', $v);
                $cart_ids[] = $arrIds[0];
            }

            $cart_list = $this->ci->Cart_model->get_cart_buy_list($cart_ids, $uid);

        } else {
            $cart_list = $this->ci->Cart_model->get_user_cart_list($uid);
        }

        $data = array();
        $data['buy'] = array();
        $data['delivery'] = array();
        $total_goods_price = 0;
        $fare = 0;
        foreach ($cart_list as $v) {
            //组装商品信息和SKU信息
            $goods_info = $this->ci->Goods_model->get_info_by_id($v['goods_id'], '*', array('goods_num'));
            if (!empty($goods_info)) {
                //店铺
                $shop_info = $this->ci->Shop_model->get_by_id($goods_info['shop_id'], 'id,name');
                $data['buy'][$shop_info['id']]['shop'] = array(
                    'id' => $shop_info['id'],
                    'name' => $shop_info['name'],
                    'url' => '',
                );

                $sku_info = $v['sku_id'] ? $this->ci->Cart_model->get_sku_info($v['sku_id']) : array();
                $goods_info['sku_info']=$sku_info;
                //$this->ci->buying_service->sku_info=$sku_info;
                $this->ci->buying_service->goods_info=$goods_info;
                $stock = $this->ci->buying_service->getRealStock();

                //价格
                $price = empty($sku_info) ? $goods_info['price'] : $sku_info['price'];
                $activity_price=$this->ci->buying_service->getrealPriceInActivity($goods_info['tpl_id'],$price);
                $price = $activity_price>0 ? $activity_price :$price;

                $sku = empty($sku_info) ? '' : str_replace('_', ';', $sku_info['sku_title']);

                unset($sku_info);


                $data['buy'][$shop_info['id']]['goods_list'][] = array(
                    'cart_id' => $v['id'],
                    'num' => $v['num'],
                    'goods_id' => $goods_info['id'],
                    'tpl_id' => $goods_info['tpl_id'],
                    'goods_price' => (string)$price,
                    'goods_title' => $goods_info['title'],
                    'goods_point' => $goods_info['point'],
                    'pic_url' => thumb($goods_info),
                    'sku_id' => $v['sku_id'],
                    'sku' => $sku
                );

                if (!$cart_id) {
                    continue;
                }

                //总价格
                $total_goods_price += (float)$price * $v['num'];

                $arrDeliverParam[] = array('sku_id' => $v['sku_id'], 'goods_id' => $goods_info['id'], 'num' => $v['num']);

                $goods_info['goods_num'] = $v['num'];
                $goods_list[] = $goods_info;
            }

        }
        unset($v,$goods_info);

        //只取数组val，方便格式化为list
        !empty($data['buy']) && $data['buy'] = array_values($data['buy']);

        if ($cart_id) {
            //配送方式
           if(!empty($arrDeliverParam)){
               $delivery_type = $this->ci->Goods_sku_model->delivers_display($arrDeliverParam);
               switch ($delivery_type) {
                   case 1:
                       $data['delivery'][] = array('title' => '九号街区配送');
                       break;
                   case 2:
                       $data['delivery'][] = array('title' => '快递配送');
                       break;
                   case 3:
                       $data['delivery'] = array(
                           array('title' => '九号街区配送'),
                           array('title' => '快递配送')
                       );
                       break;
               }
           }

            //计算总运费
            if(!empty($goods_list)){
                $fare=$this->ci->buying_service->getFare($goods_list,$city_id);
                unset($goods_list);
            }

            //计算费用
            $data['amount'] = array(
                'total_goods' => $total_goods_price,
                'fare' => $fare
            );
        }

        return $data;
    }

    /**
     * 获取所选购物车商品总价
     * @date: 2016年3月17日 下午4:27:56
     * @author: hbb
     * @param: string $cart_ids
     * @param: int $buyer_id
     * @return: array
     */
    public function get_cart_total_price($cart_ids, $buyer_id)
    {
        $cart_ids_list = explode(',', $cart_ids);
        $cart_list = $this->ci->Cart_model->get_list(array('id' => $cart_ids_list, 'buyer_id' => $buyer_id, 'status'=>1));
        $total_price = 0;
        if (!empty($cart_list)) {
            $total_price = array_reduce($cart_list, function ($v, $vv) {
                $goods_info = $this->ci->Goods_model->get_info_by_id($vv['goods_id']);
                $sku_info = $vv['sku_id'] ? $this->ci->Cart_model->get_sku_info($vv['sku_id']) : null;
                $price = empty($sku_info) ? $goods_info['price'] : $sku_info['price'];
                $activity_price=$this->ci->buying_service->getrealPriceInActivity($goods_info['tpl_id'],$price);
                $price = $activity_price>0 ? $activity_price :$price;
                if ($price > 0) {
                    return $v + $price * $vv['num'];
                }
            }, 0);
        }

        return $total_price;

    }

    /**
     * 添加购物车
     * @date: 2016年3月18日 上午10:50:47
     * @author: hbb
     * @param:  array $cart_info
     * @return: array
     */
    public function add_cart_goods($cart_info)
    {
        $result = false;
        $sql = sprintf("UPDATE x_trd_shopcart SET num=num+%d WHERE buyer_id=%d AND goods_id=%d AND sku_id=%d AND status=%d",
            $cart_info['num'],
            $cart_info['buyer_id'],
            $cart_info['goods_id'],
            $cart_info['sku_id'],
            1
        );
        //购物车存在同规格商品 直接更新数量
        if ($this->ci->Cart_model->execute($sql)) {
            if (!$this->ci->Cart_model->affected_rows()) {
                $result = $this->ci->Cart_model->add_cart_info($cart_info);
            }
            $result = true;
        }
        return $result;
    }

    /**
     * 删除购物车商品
     * @date: 2016年3月18日 下午3:54:00
     * @author: hbb
     * @param: string $cart_ids
     * @param: int $buyer_id
     * @return: boolean
     */
    public function del_cart_goods($cart_ids, $buyer_id)
    {
        if (!empty($cart_ids)) {
            $cart_ids_list = explode(',', $cart_ids);
            if ($this->ci->Cart_model->update_to_del_state($cart_ids_list, $buyer_id)) {
                return true;
            }
        }
        return false;
    }

}