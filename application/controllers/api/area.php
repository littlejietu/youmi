<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Area_model');
    }

    /**
     * 获取所有省份列表
     */
    public function get_province_list(){

        $privinceList = $this->Area_model->getTopLevelAreas();

        if(empty($privinceList)){
            //output_error('-1','AREA_NULL');
            output_error('-1','地区为空');
        }

        $priList =array();
          foreach($privinceList as $k => $v){
              $priList[] = array(
                  'id'    =>  $k,
                  'name'  =>  $v,
              );
          }
        output_data(array('data' => $priList));
        }

    /**
     * 根据父级ID获取列表
     */
    public function get_parent_id(){

        $parent_id = $this->input->post('parent_id');

        if(empty($parent_id)){
            //output_error('-1','PARENT_ID');
            output_error('-1','父级ID为空');
        }

        $parentList = $this->Area_model->get_list(array('parent_id' => $parent_id),'id,name');

        if(empty($parentList)){
            //output_error('-1','AREA_NULL');
            output_error('-1','地区为空');
        }

        output_data(array('data' => $parentList));
    }

    /**
     * 全国店铺城市
     */
    public function get_nationwide_area(){

        $this->load->model('Shop_model');

        $shopList =  $this->Shop_model->get_list(array('status !=' => -1),'DISTINCT(city_id)');

        if(!empty($shopList)){

           $arr = '';
            foreach($shopList as $k => $v){
                $arr[$k] = $v['city_id'];

            }

          $areaList =  $this->Area_model->get_list(array('id' => $arr),'id,name');

            output_data(array("data"=>$areaList));
        }

        output_data();

    }

}