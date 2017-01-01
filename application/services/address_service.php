<?php
/**
 * 地址service
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Address_service {
	public function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model('Address_model');
		$this->ci->load->model('Area_model');
	}

	public function get_default_address($uid) {
		$data = array();
		$aAddress = $this->ci->Address_model->get_default_info($uid);
		if (!empty($aAddress)) {
			/*$area_data = $this->ci->Area_model->getAreaList(
				array(
					'id' => array(
						$aAddress['province_id'],
						$aAddress['city_id'],
						$aAddress['area_id'],
					),
				),
				'name',
				'deep asc'
			);
			
			$data = array(
			    'id' => $aAddress['id'],
			    'real_name' => $aAddress['real_name'],
			    'phone' => $aAddress['mobile'],
			    'province' =>!empty($area_data[0]['name'])?$area_data[0]['name']:'',
			    'city' => !empty($area_data[1]['name'])?$area_data[1]['name']:'',
			    'area' => !empty($area_data[2]['name'])?$area_data[2]['name']:'',
			    'street' => $aAddress['address'],
			    'city_id' => $aAddress['city_id'],
			);*/
			$data = array(
			    'id' => $aAddress['id'],
			    'real_name' => $aAddress['real_name'],
			    'phone' => $aAddress['mobile'],
			    'province' =>$aAddress['province_name']==$aAddress['city_name']?'':$aAddress['province_name'],
			    'city' => $aAddress['city_name'],
			    'area' => $aAddress['area_name'],
			    'street' => $aAddress['address'],
			    'city_id' => $aAddress['city_id'],
			);
		}

		return $data;
	}
	/*
	* 根据地址id获得地址信息
	 * tong
	* */
	public  function get_address_info($addrid){
		$data =array();
		$address_info = $this->ci->Address_model->get_by_id($addrid);
		if (!empty($address_info)) {
			$area_data = $this->ci->Area_model->getAreaList(
				array(
					'id' => array(
						$address_info['province_id'],
						$address_info['city_id'],
						$address_info['area_id'],
					),
				),
				'name',
				'deep asc'
			);
			$data['id'] = $address_info['id'];
			$data['real_name'] = $address_info['real_name'];
			$data['phone'] = $address_info['mobile'];
			$data['province'] = !empty($area_data[0]['name'])?$area_data[0]['name']:'';
			$data['city'] = !empty($area_data[1]['name'])?$area_data[1]['name']:'';
			$data['area']= !empty($area_data[2]['name'])?$area_data[2]['name']:'';
			$data['street'] = $address_info['address'];
			return $data;

		}
		return $data;
	}

}