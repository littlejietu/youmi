<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gun_model extends XT_Model {

	protected $mTable = 'oil_gun';
	protected $tb_price = 'oil_price';

	public function get_info_by_no($gun_no, $site_id){
		$aInfo = $this->get_by_where(array('gun_no'=>$gun_no,'site_id'=>$site_id));

		$this->set_table($this->tb_price);
		$aPrice = $this->get_by_where( array("oil_no"=>$aInfo['oil_no'],'site_id'=>$site_id) );
        if ($aInfo && $aPrice) {
            $aInfo = array_merge($aInfo, $aPrice);
        }

        $this->set_table($this->mTable);
        return $aInfo;
	}

    
	
}
