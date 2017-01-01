<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('goods_service');
        $this->load->service('first_service');
        $this->load->model('First_model');
        $this->load->model('First_place_model');
    }

    /**
     * 首页
     */
    public function index()
    {
        $lng = $this->input->get_post('lng');
        $lat = $this->input->get_post('lat');

        $this->load->model('Shop_model');
        $aShop = $this->Shop_model->getNearShop($lng, $lat);
        $aShop = array('shop_id'=>'1','shop_name'=>'远方..');

        $goods_id = $this->input->post('goods_id');
        $shop_id = $aShop['shop_id'];//!empty($_REQUEST['shop_id']) ? (int)$_REQUEST['shop_id']:1;

        //海报
        $banner = $this->first_service->get_first_by_place(1,5,'pic as pic_url,url as to_url');
        if (!empty($banner))
        {
            $data[] =array(
                'type' => 1,
                'data' => $banner,
            );
        }
        
        //专题
        $icon = $this->first_service->get_first_by_place(2,10,'title,pic as pic_url,url as to_url');
        if (!empty($icon))
        {
            $data[] =array(
                'type' => 2,
                'data' => $icon,
            );
        }
        
        //文字广告
        $title = $this->first_service->get_first_by_place(3,10,'title,url as to_url,title_style as title_color');
        if (!empty($title))
        {
            $data[] =array(
                'type' => 3,
                'data' => $title,
            );
        }
        
        //大广告1
        $ad1 = $this->first_service->get_first_by_place(4,1,'pic as pic_url,url as to_url');
        if (!empty($ad1))
        {
            $data[] =array(
                'type' => 4,
                'data' => $ad1,
            );
        }
        
        //小广告
        $ad2 = $this->first_service->get_first_by_place(5,5,'pic as pic_url,url as to_url');
        if (!empty($ad2))
        {
            $data[] =array(
                'type' => 5,
                'data' => $ad2,
            );
        }
        
        //今日抢购
        $rush_purchase = $this->first_service->get_home_activity(1,10,4,$shop_id);
        if (!empty($rush_purchase['goods_list']['rows']))
        {
            foreach ($rush_purchase['goods_list']['rows'] as $key => $value)
            {
                $rush[] = array(
                    'goods_id' => $value['goods_id'],
//                    'sku_id' => $value['sku_id'],
                    'shop_id' => $value['shop_id'],
                    'name' => $value['name'],
                    'pic_url' => $value['pic_url'],
                    'price' => $value['price'],
                    'original_price' => $value['original_price'],
                    'to_url' => $value['to_url'],
                    'tpl_id' => $value['tpl_id'],
                );
            }
            $data[] =array(
                'type' => 6,
                'data' => array(
                    'startTime' =>$rush_purchase['startTime'],
                    'timeleft'=>$rush_purchase['end_time_left'],
                    'goods_list'=> $rush,
                )
            );
            //大广告2
            $ad3 = $this->first_service->get_first_by_place(6,1,'pic as pic_url,url as to_url');
            if (!empty($ad3))
            {
                $data[] =array(
                    'type' => 4,
                    'data' => $ad3,
                );
            }
        }

        //特价秒杀
        $data[] =array(
                'type' => 7,
                'data' => array(
                    'tip_bg_url' =>RES_SITE_URL.'front/images/seckill_image.png',
                    'title' => '特价秒杀',
                    'title_color' => '#121212',
                    'subtitle' => 'SECKILL',
                    'subtitle_color' => '#D6D6D6',
                    'goods_list' =>$this->first_service->get_seckill($shop_id),
                ),
            );
        
        //大广告3
        $ad4 = $this->first_service->get_first_by_place(9,1,'pic as pic_url,url as to_url');
        if (!empty($ad4))
        {
            $data[] = array(
                'type' => 4,
                'data' => $ad4,
            );
        }
        
        //发现好货
        $discovery = $this->first_service->get_discovery();
        if (!empty($discovery))
        {
            $data[] = array(
                'type' => 8,
                'data' => array(
                    'tip_bg_url' =>RES_SITE_URL.'front/images/discovery_image.png',
                    'title' => '发现好货',
                    'title_color' => '#121212',
                    'subtitle' => 'DISCOVERY',
                    'subtitle_color' => '#D6D6D6',
                    'goods_list' => $discovery,
                ),
            );
            //大广告4
            $ad5 = $this->first_service->get_first_by_place(12,1,'pic as pic_url,url as to_url');
            if (!empty($ad5))
            {
                $data[] = array(
                    'type' => 4,
                    'data' => $ad5,
                );
            }
        }
        
        
        
        //精品推荐
        $choice_goods = $this->first_service->get_home_activity(1,10,6,$shop_id);
        if (!empty($choice_goods['goods_list']['rows']))
        {
            foreach ($choice_goods['goods_list']['rows'] as $key => $value)
            {
                $choiceness[] = array(
                    'goods_id' => $value['goods_id'],
//                    'sku_id' => $value['sku_id'],
                    'shop_id' => $shop_id,
                    'name' => $value['name'],
                    'pic_url' => $value['pic_url'],
                    'price' => $value['price'],
                    'original_price' => $value['original_price'],
                    'to_url' => $value['to_url'],
                    'tpl_id' => $value['tpl_id'],
                );
            }
            $data[] = array(
                'type' => 9,
                'data' => array(
                    'tip_bg_url' =>RES_SITE_URL.'front/images/choiceness_image.png',
                    'title' => '精选推荐',
                    'title_color' => '#121212',
                    'subtitle' => 'CHOICENESS',
                    'subtitle_color' => '#D6D6D6',
                    'goods_list'=> $choiceness,
                ),
            );
            //大广告5
            $ad6 = $this->first_service->get_first_by_place(13,1,'pic as pic_url,url as to_url');
            if (!empty($ad6))
            {
                $data[] = array(
                    'type' => 4,
                    'data' => $ad6,
                );
            }
        }
        
        
        
        //热卖推荐
        $hot_sale = $this->goods_service->hot_sale($shop_id,10);
        if (!empty($hot_sale))
        {
            $data[] =array(
                'type' => 9,
                'data' => array(
                    'tip_bg_url' =>RES_SITE_URL.'front/images/hot_image.png',
                    'title' => '热卖推荐',
                    'title_color' => '#121212',
                    'subtitle' => 'HOT',
                    'subtitle_color' => '#D6D6D6',
                    'goods_list'=> $hot_sale,
                ),
            );
        }
        
        //猜你喜欢
        $wish_goods = $this->goods_service->wish_goods($goods_id,$shop_id,8);
        if (!empty($wish_goods))
        {
            $data[] = array(
                'type' => 10,
                'data' => array(
                    'tip_icon_url'=>RES_SITE_URL.'front/images/wish_image.png',
                    'title' =>"猜你喜欢",
                    'title_color'=>"#707070",
                    'goods_list'=>$wish_goods,
                ),
            );
        }
                   
        $list['data'] = $data;
        $list['shop'] = $aShop;
        output_data($list);exit;
        // $result = array(
        //     'code' => 1,
        //     'msg' => 'SUCCESS',
        //     'content' => $data
        // );
        // echo json_encode($result);
    }

    /**
     * 获取起始广告页
     */
    public function get_start_ad()
    {
        $this->load->model('Wordbook_model');
        
        $client_type = $this->input->post('client_type');
        $dev_width = $this->input->post('dev_width');
        $dev_height = $this->input->post('dev_height');
        
        $ad = $this->Wordbook_model->get_list(array('k'=>array('start_ad','start_ad_time')),'val');
        $data =array('ad_pic_url'=>BASE_SITE_URL.'/'.$ad[0]['val'],'ad_show_time'=>$ad[1]['val']);
        output_data($data);
    }
}