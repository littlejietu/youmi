<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 该模块已转移到m文件夹下
 * @author Administrator
 *
 */
class Comment extends TokenApiController {

    const  TMP_PAGE_TOTAL =10;
    
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Comment_goods_model');
        $this->load->model('Goods_num_model');
        $this->load->model('Goods_sku_model');
        $this->load->model('User_model');
        $this->load->model('Order_model');
        $this->load->service('goodsnum_service');
    }
	
	/**
	 * @param 新增评论
	 * @param $_POST['order_id']
	 * @param $_POST['buyer_id']
	 * @param $_POST['goods_id']
	 * @param $_POST['sku_id']
	 * @param $_POST['comment']
	 * @param $_POST['pic_path']
	 * @param $_POST['score_level']
	 * 
	 */
	public function xxxxxadd()
	{
	    $order_id = $this->input->post('order_id');
	    $buyer_id = $this->input->post('buyer_id');
	    $goods_id = $this->input->post('goods_id');
	    $sku_id = $this->input->post('sku_id');
	    $comment = $this->input->post('comment');
	    $pic_path = $this->input->post('pic_path');
	    $score_level = $this->input->post('score_level');
	    if (empty($order_id))
	    {
	        //output_error(-1,'ORDER_ID_NULL');exit;
	        output_error(-1,'订单ID为空');exit;
	    }
	    if (empty($goods_id))
	    {
	        //output_error(-1,'GOODS_ID_NULL');exit;
	        output_error(-1,'商品ID为空');exit;
	    }
	    if (empty($sku_id))
	    {
	        //output_error(-1,'GOODS_SKUID_NULL');exit;
	        output_error(-1,'商品SKUID为空');exit;
	    }
	    if (empty($comment))
	    {
	        //output_error(-1,'COMMENT_NULL');exit;
	        output_error(-1,'评论为空');exit;
	        
	    }
	    if (empty($score_level))
	    {
	        //output_error(-1,'SCORE_LEVEL_NULL');exit;
	        output_error(-1,'评分为空');exit;
	    }
	    $data= array(
	        'order_id' => $order_id,
	        'buyer_id' => $buyer_id,
	        'goods_id' => $goods_id,
	        'sku_id' => $sku_id,
	        'comment' => $comment,
	        'pic_path' => $pic_path,
	        'score_level' => $score_level,
	        'status' => 1,
	        'addtime' => time(),
	  	    );
	    $this->Comment_goods_model->insert($data);
	    output_data();exit;
	}

	
	
	public function add()
	{
	    $comment = $this->input->post('comment');
	    $order_id = $this->input->post('order_id');
	    $user = $this->loginUser;
	    $user_id = $user['user_id'];
	    if (empty($order_id))
	    {
	        //output_error(-1,'ORDER_ID_NULL');exit;
	        output_error(-1,'订单ID为空');exit;
	    }
	    if ($this->Comment_goods_model->add_comment($order_id,$user_id,$comment))
	    {
	        foreach ($comment as $key => $value)
	        {
	            if (empty($value['score_level']))
	            {
	                $value['score_level'] = 5;
	            }
	            $pic = empty($value['pic_path'])?0:1;
	            $this->goodsnum_service->onComment($value['goods_id'],$user_id,$value['score_level'],$pic);
	        }
	        $where['order_id'] = $order_id;
	        $data['comment_status'] = 1;
	        $this->Order_model->update_by_where($where,$data);
	        output_data();exit;
	    }
	    else
	    {
	        //output_error(-1,'FAILED');exit;
	        output_error(-1,'失败');exit;
	    }
	}
	
}
