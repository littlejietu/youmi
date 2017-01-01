<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->library('PHPExcel');
		$this->ci->load->model('Order_model');
	}
	
	//拉取销售报表数据
	public function get_all_excel_datas($start_time,$end_time)
	{
	    $prefix = $this->ci->Order_model->prefix();
	    $sql = "SELECT b.title,b.spec,cost_price,a.price,SUM(a.num) num, SUM( cost_price*a.num ) total_cost, SUM( a.price*a.num ) total_sale
                    FROM ".$prefix."trd_order_goods a JOIN ".$prefix."shot_goods b JOIN ".$prefix."trd_order c
                    ON(a.order_id=b.order_id AND a.order_id=c.order_id)
                    WHERE c.status='Finished'
                        AND (UNIX_TIMESTAMP()-c.finished_time)>86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day')
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))>".$start_time."
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))<".$end_time."
                    GROUP BY a.goods_id,a.sku_id,cost_price,a.price";
	    $result = $this->ci->Order_model->db->query($sql)->result_array();
	    return $result;
	}
	
	//根据店铺拉取销售报表数据
	public function get_excel_datas_by_shop($seller_username,$start_time,$end_time)
	{
	    $prefix = $this->ci->Order_model->prefix();
	    $sql = "SELECT b.title,b.spec,cost_price,a.price,SUM(a.num) num, SUM( cost_price*a.num ) total_cost, SUM( a.price*a.num ) total_sale
                    FROM ".$prefix."trd_order_goods a JOIN ".$prefix."shot_goods b JOIN ".$prefix."trd_order c
                    ON(a.order_id=b.order_id AND a.order_id=c.order_id)
                    WHERE c.shop_id=(select id from ".$prefix."shop where seller_username='".$seller_username."')
                        AND c.status='Finished'
                        AND (UNIX_TIMESTAMP()-c.finished_time)>86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day')
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))>".$start_time."
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))<".$end_time."
                    GROUP BY a.goods_id,a.sku_id,cost_price,a.price";
	    $result = $this->ci->Order_model->db->query($sql)->result_array();
	    return $result;
	}
	
	//根据地区拉取销售报表数据
	public function get_excel_datas_by_area($city_id,$start_time,$end_time)
	{
	    $prefix = $this->ci->Order_model->prefix();
	    $sql = "SELECT b.title,b.spec,cost_price,a.price,SUM(a.num) num, SUM( cost_price*a.num ) total_cost, SUM( a.price*a.num ) total_sale
                    FROM ".$prefix."trd_order_goods a JOIN ".$prefix."shot_goods b JOIN ".$prefix."trd_order c
                    ON(a.order_id=b.order_id AND a.order_id=c.order_id)
                    WHERE c.shop_id in (select id from ".$prefix."shop where city_id='".$city_id."')
                        AND c.status='Finished'
                        AND (UNIX_TIMESTAMP()-c.finished_time)>86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day')
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))>".$start_time."
                        AND (c.finished_time+86400*(select val from ".$prefix."sys_wordbook where k='order_commis_day'))<".$end_time."
                    GROUP BY a.goods_id,a.sku_id,cost_price,a.price";
	    $result = $this->ci->Order_model->db->query($sql)->result_array();
	    return $result;
	}
	
	/**
	 * 数据导出
	 * @param unknown $data 要导出的数据(二维数组)
	 * @param unknown $name 导出的文件名
	 * @param $start_time 开始时间
	 * @param $end_time 结束时间
	 * @param unknown $title  表格第一行标题数组
	 * @param $action 导出类型
	 * @param $area 地区
	 */
	public function push_to_excel($data,$name,$start_time,$end_time,$title='',$action='',$area='')
	{
	    error_reporting(E_ALL);
	    date_default_timezone_set('ASIA/shanghai');
	    
	    $objPHPExcel = new $this->ci->phpexcel;
	    $objPHPExcel->getProperties()->setCreator("9号街区")
	    ->setLastModifiedBy("9号街区")
	    ->setTitle("数据EXCEL导出")
	    ->setSubject("数据EXCEL导出")
	    ->setDescription("备份数据")
	    ->setKeywords("excel")
	    ->setCategory("result file");
	    $row = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','U','V','W','X','Y','Z');
	    $num = 1;
	    $total_cost = 0;
	    $total_sale = 0;
	    //写入标题
	    foreach ($title as $key => $value)
	    {
	        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($row[$key].$num, $value);
	    }
	    //写入内容
	    if (!empty($data))
	    {
	        foreach ($data as $key => $value)
	        {
	            $num = $key + 2;
	            $i = 0;
	            foreach ($value as $k => $v)
	            {
	                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($row[$i].$num, $v);
	                $i++;
	            }
	            $total_cost += $value['total_cost'];
	            $total_sale += $value['total_sale'];
	        }
	    }
	    if ($action == 'all')
	    {
	        $objPHPExcel->setActiveSheetIndex(0)
	        ->setCellValue('I2', $total_cost)
	        ->setCellValue('J2', $total_sale)
	        ->setCellValue('K2', date('Y/m/d',$start_time))
	        ->setCellValue('L2', date('Y/m/d',$end_time));
	    }
	    if ($action == 'shop')
	    {
	        $objPHPExcel->setActiveSheetIndex(0)
	        ->setCellValue('I2', $total_cost)
	        ->setCellValue('J2', $total_sale)
	        ->setCellValue('K2', date('Y/m/d',$start_time))
	        ->setCellValue('L2', date('Y/m/d',$end_time));
	    }
	    if ($action == 'area')
	    {
	        $this->ci->load->model('Area_model');
	        $province = $this->ci->Area_model->get_by_id($area['province_id']);
	        $city = $this->ci->Area_model->get_by_id($area['city_id']);
	        $province['name'] = $province['name'] == $city['name']?'':$province['name'];
	        $place = $province['name'].' '.$city['name'];
	        $objPHPExcel->setActiveSheetIndex(0)
	        ->setCellValue('I2',$place)
	        ->setCellValue('J2', $total_cost)
	        ->setCellValue('K2', $total_sale)
	        ->setCellValue('L2', date('Y/m/d',$start_time))
	        ->setCellValue('M2', date('Y/m/d',$end_time));
	    }
	    
	    $objPHPExcel->getActiveSheet()->setTitle('sheet1');
	    $objPHPExcel->setActiveSheetIndex(0);
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="'.$name.'.xls"');
	    header('Cache-Control: max-age=0');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	}
}