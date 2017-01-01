<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Net_model extends XT_Model {

	protected $mTable = 'oil_net';
	
	private function _tree($data, $space='-', $show_deep=10, $parent_id = 0, $deep = 1, $i=0){
		static $treeList = array();//树状的平行数组

		if(is_array($data) && !empty($data)) {
            $size = count($data);
            if($i == 0) $treeList = array();//从0开始时清空数组，防止多次调用后出现重复
            for ($i;$i < $size; $i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
                $val = $data[$i];
                if($val['parent_id'] == $parent_id) {
                	$tmp = '';
                	for($j=1;$j<$val['deep'];$j++){$tmp.=$space;}
                	$treeList[$val['id']]= array_merge($val, array('space'=>$tmp,'children'=>array()));
                	if(isset($treeList[$parent_id]))
                		$treeList[$parent_id]['children'][] = $val['id'];
                    // $treeList[] = $val;
                    if($val['deep'] < $show_deep) {//本次深度小于显示深度时执行，避免取出的数据无用
                        $this->_tree($data,$space,$show_deep,$val['id'],$val['deep'],$i+1);
                    }
                }
                if($val['parent_id'] > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
            }
        }
        return $treeList;
	}

	public function getTreeList($company_id, $space='-', $show_deep=10){
		$where =  array('status'=>1, 'company_id'=>$company_id);
		$list = $this->get_list($where,'*','parent_id asc, sort desc, id asc');
		$result = $this->_tree($list, $space, $show_deep);
		return $result;
	}

}
