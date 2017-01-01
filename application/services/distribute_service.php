<?php
/**
 * 分销service
 * @date: 2016年3月17日 上午11:19:06
 * @author: hbb
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class distribut_service {
	public function __construct() {
		$this->ci = &get_instance();
	    $this->ci->load->model('Distribute_model');
		$this->ci->load->model('User_model');
	}

    /**
    * 设置分销关系佣金比
    * @date: 2016年3月23日 下午5:59:13
    * @author: hbb
    * @param: variable
    * @return:
    */
	public function set_sale_distribute()
	{
	    $parentid = $this->input->get('recommended');
	    //没有推荐人
	    if(empty($parentid))
	    {
	        if(!empty($this->userinfo->userid)){
	            $this->add_distribute();
	            return true;
	        }
	        return false;
	    }
	
	    //取出配置中10级分销比例
	    for($i=0;$i<10;$i++){
	        $distribute_rates[$i]=C('distribute_rates_'.$i);
	    }
	    $this->rates = $distribute_rates;
	    $this->rate_num = count($this->rates);
	    if($this->rate_num>0){
	        $this->add_distribute($parentid);
	    }
	}
	
	public function add_distribute($parentid=0)
	{
	    static $deep=0;
	  
	    $userid=(int)$this->userinfo->userid;
	
	    $insert_data = array(
	        'userid'    => (int)$this->userinfo->userid,
	        'username'  => $this->userinfo->username
	    );
	    if($parentid>0){
	        //todo  get userinfo form user table by $parentid (userid=$parent)
	        //...
	        //...
	        $user = self::userid_username_map();
	        //if(!isset($user[$parentid])) return('over');
	        if(!isset($user[$parentid])) exit('over');
	
	        $names = $user[$parentid];
	        list($username,$parent_id,$parent_username) = explode('|', $names);
	        $parent_user=array(
	            'userid'    => $parentid,
	            'username'  => $username
	        );
	
	
	        //分佣比
	        $rates=$this->rates;
	
	        $insert_data_part = array(
	            'deep'              => $deep+1,
	            'parent_id'         => $parent_user['userid'],
	            'parent_username'   => $parent_user['username'],
	            'rate'              => $rates[$deep]
	        );
	        $deep++;
	        $insert_data = array_merge($insert_data,$insert_data_part);
	    }else{
	        $insert_data['deep'] =$deep;
	        $insert_data['parent_id'] =0;
	    }
	    $insert_data['createtime'] = date('Y-m-d H:i:s');
	
	    if($this->ci->Distribute_model->get_count('userid='.$userid.' and parent_id=' .$parentid)>0){
	        return false;
	    }
	
	    if($this->ci->Distribute_model->get_count('userid='.$userid.' and deep=' .$deep)>0){
	        return false;
	    }
	
	    //递归save
	    if($this->Distribute_model->insert($insert_data)){
	        if(!empty($parent_id) && $deep<$this->rate_num){
	            $this->add_distribute($parent_id);
	        }
	    }
	}
	
	
	
	
	public function  temp_set_user_info()
	{
	    $userid = !empty($_GET['uid'])?$_GET['uid']:0;
	    $username =!empty($_GET['name'])?$_GET['name']:'';
	    return  array(
	        'userid'=>$userid,
	        'username'=>$username
	    );
	
	}
	
	static public function userid_username_map()
	{
	    return array(
	        111     => 'qqq|11111|qqqqq',
	        222     => 'www|22222|wwwww',
	        333     => 'eee|33333|eeeee',
	        555     => 'rrr|55555|rrrrr',
	        666     => 'ttt|66666|ttttt',
	        777     => 'yyy|77777|yyyyy',
	        888     => 'ooo|88888|ooooo',
	        66666   => 'ttttt|99999|mmmmm',
	    );
	}

}