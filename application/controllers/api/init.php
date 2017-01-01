<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Init extends ApiController
{
	public function __construct()
    {
      	parent::__construct();
    }


    public function index(){
    	$lng = $this->input->post('lng');
    	$lat = $this->input->post('lat');

        $aShop = array();
        $this->load->model('Shop_model');
        $aShop = $this->Shop_model->getNearShop($lng, $lat);
        $aShop = array('shop_id'=>1,'shop_name'=>'远方..');

    	$arrReturn = array('code'=>'1','data'=>$aShop,'msg'=>'Success','action'=>'init');
        echo json_encode($arrReturn);

    }

    public function update(){
        $channel_id = $this->input->post('channel_id');
        $version_no = $this->input->post('version_code');
        //$version_name = $this->input->post('version_name');
        $pkg_name = $this->input->post('pkg_name');

        $arrReturn = array();

        $this->load->model('App_version_model');
        $aVersion = $this->App_version_model->get_list(array('pkg_name'=>$pkg_name),'*','version_no desc',1);
        if(!empty($aVersion))
        {
            $aVersion = $aVersion[0];
            if($aVersion['version_no']>$version_no){
                $arrReturn = array('pkg_name'=>$aVersion['pkg_name'],'download_url'=>$aVersion['url'],'update_version'=>$aVersion['version_no'],
                    'release_note'=>$aVersion['desc'],'md5'=>$aVersion['md5'],'version_name'=>$aVersion['version_name'],'update_tip'=>$aVersion['title'],
                    'update_type'=>$aVersion['is_force_update'],'channel_id'=>$channel_id,
                );
                output_data($arrReturn);
            }
            else {
                output_data(null);exit;
            } 
        }
        else
        {
            output_data(null);exit;
        }

    }

    public function api(){
        $arrReturn = array('wap_host'=>'http://data.zooernet.com/wap','api_host'=>'http://data.zooernet.com','active_url'=>'http://m.zooernet.com');
        output_data($arrReturn);exit;
    }
}