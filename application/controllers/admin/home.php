<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Admin_Controller {
	
	public function index()
	{
		
		$this->lang->load('admin_layout');
		$this->getNav('',$top_nav,$left_nav,$map_nav);

		$admin_info = $this->getAdminInfo();

		$result = array(
			'output'=>array(
				'html_title'=>lang('login_index_title_02'),
				'map_nav' => $map_nav,
				'admin_info' => $admin_info,
				'top_nav' => $top_nav,
				'left_nav'=>$left_nav,
				)
			);
		
		$this->load->view('admin/home',$result);
	}
	public function tmp_send(){
		$this->load->service('Message_service');
		$this->message_service->tmp_send();
	}

	/**
	 * 取得后台菜单
	 *
	 * @param string $permission
	 * @return
	 */
	protected final function getNav($permission = '',&$top_nav,&$left_nav,&$map_nav){

		$act = $this->input->post_get('act');
		$op = $this->input->post_get('op');

		$admin_info = $this->getAdminInfo();

		$this->load->model('sys/Admin_role_model');
		if ($this->admin_info['is_super'] != 1 && empty($this->permission)){
			
            $gadmin = $this->Admin_role_model->get_by_id($this->admin_info['role_id']);
			$permission = $this->encrypt->decode($gadmin['limits']);
			$this->permission = $permission = explode('|',$permission);
		}

		$this->lang->load('common');
		//Language::read('common');
		$array = $this->get_menu();

		$array = $this->parseMenu($array);
		//管理地图
		$map_nav = $array['left'];
		unset($map_nav[0]);

		$model_nav = "<li><a class=\"link actived\" id=\"nav__nav_\" href=\"javascript:;\" onclick=\"openItem('_args_');\"><span>_text_</span></a></li>\n";
		$top_nav = '';

		//顶部菜单
		foreach ($array['top'] as $k=>$v) {
			$v['nav'] = $v['args'];
			$top_nav .= str_ireplace(array('_args_','_text_','_nav_'),$v,$model_nav);
		}
		$top_nav = str_ireplace("\n<li><a class=\"link actived\"","\n<li><a class=\"link\"",$top_nav);

		//左侧菜单
		$model_nav = "
          <ul id=\"sort__nav_\">
            <li>
              <dl>
                <dd>
                  <ol>
                    list_body
                  </ol>
                </dd>
              </dl>
            </li>
          </ul>\n";
		$left_nav = '';
		foreach ($array['left'] as $k=>$v) {
			$left_nav .= str_ireplace(array('_nav_'),array($v['nav']),$model_nav);
			$model_list = "<li nc_type='_pkey_'><a href=\"JavaScript:void(0);\" name=\"item__opact_\" id=\"item__opact_\" onclick=\"openItem('_args_');\">_text_</a></li>";
			$tmp_list = '';

			$current_parent = '';//当前父级key

			foreach ($v['list'] as $key=>$value) {
				$model_list_parent = '';
				$args = explode(',',$value['args']);
				if ($admin_info['is_super'] != 1){
					if (!@in_array($args[1],$permission)){
						//continue;
					}
				}

				if (!empty($value['parent'])){
					if (empty($current_parent) || $current_parent != $value['parent']){
						$model_list_parent = "<li nc_type='parentli' dataparam='{$value['parent']}'><dt>{$value['parenttext']}</dt><dd style='display:block;'></dd></li>";
					}
					$current_parent = $value['parent'];
				}

				$value['op'] = $args[0];
				$value['act'] = $args[1];
				//$tmp_list .= str_ireplace(array('_args_','_text_','_op_'),$value,$model_list);
				$tmp_list .= str_ireplace(array('_args_','_text_','_opact_','_pkey_'),array($value['args'],$value['text'],$value['op'].$value['act'], !empty($value['parent'])?$value['parent']:0 ),$model_list_parent.$model_list);
			}

			$left_nav = str_replace('list_body',$tmp_list,$left_nav);

		}
	}


	public function get_menu(){
		$this->lang->load('common');

		$menuList = array(
			'top' => array(
				0 => array(
					'args' 	=> 'dashboard',
					'text' 	=> lang('nc_console') ),
				1 => array(
					'args' 	=> 'setting',
					'text' 	=> lang('nc_config') ),
				2 => array(
					'args' 	=> 'goods',
					'text' 	=> '公司'),
				/*3 => array(
					'args' 	=> 'store',
					'text' 	=> lang('nc_store')),*/
				/*4 => array(
					'args'	=> 'member',
					'text'	=> lang('nc_member')),*/
				5 => array(
					'args' 	=> 'trade',
					'text'	=> lang('nc_trade')),
				/*6 => array(
					'args'	=> 'website',
					'text' 	=> lang('nc_website')),*/
				/*7 => array(
					'args'	=> 'operation',
					'text'	=> lang('nc_operation')),*/
				/*8 => array(
					'args'	=> 'stat',
					'text'	=> lang('nc_stat')),*/
			),
			'left' =>array(
				0 => array(
					'nav' => 'dashboard',
					'text' => lang('nc_normal_handle'),
					'list' => array(
						array('args'=>'welcome,dashboard,dashboard',					'text'=>lang('nc_welcome_page')),
						//array('args'=>'aboutus,dashboard,dashboard',					'text'=>lang('nc_aboutus')),
						//array('args'=>',base_setting,dashboard',						'text'=>lang('nc_web_set')),
	     				// array('args'=>',goods,dashboard',							'text'=>lang('nc_goods_manage')),
						//array('args'=>'index,order,dashboard',			       			'text'=>lang('nc_order_manage')),
					)
				),
				1 => array(
					'nav' => 'setting',
					'text' => lang('nc_config'),
					'list' => array(
						array('args'=>',operation,setting',			    			'text'=>lang('nc_operation_set')),
						//array('args'=>',base_setting,setting',						'text'=>lang('nc_web_set')),
						//array('args'=>'qq,account,setting',		    				'text'=>lang('nc_web_account_syn')),
						//array('args'=>',upload,setting',								'text'=>lang('nc_upload_set')),
						//array('args'=>'seo,setting,setting',							'text'=>lang('nc_seo_set')),
						//array('args'=>',message,setting',								'text'=>lang('nc_message_set').'模板'),
						//array('args'=>',payment,setting',								'text'=>lang('nc_pay_method')),
						//array('args'=>',admin,setting',									'text'=>lang('nc_limit_manage')),
						//array('args'=>',shop_transport,setting',					'text'=>'物流工具'),
						// array('args'=>'index,express,setting',						'text'=>lang('nc_admin_express_set')),
						//array('args'=>',waybill,setting',           					'text'=>'运单模板'),
						//array('args'=>'index,offpay_area,setting',					'text'=>lang('nc_admin_offpay_area_set')),
						//array('args'=>'clear,cache,setting',							'text'=>lang('nc_admin_clear_cache')),
						//array('args'=>'db,db,setting',								'text'=>'数据备份'),
						//array('args'=>'perform,perform,setting',						'text'=>lang('nc_admin_perform_opt')),
						//array('args'=>'search,search,setting',						'text'=>lang('nc_admin_search_set')),

						//array('args'=>',log,setting',									'text'=>lang('nc_admin_log')),
					)
				),
				2 => array(
					'nav' => 'goods',
					'text' => '公司',
					'list' => array(
						array('args'=>',company,goods',									'text'=>'公司管理'),
						array('args'=>',site,goods',									'text'=>'加油站管理'),
						//array('args'=>',category,goods',								'text'=>lang('nc_class_manage')),
						//array('args'=>',spec_name,goods',								'text'=>'规格管理'),
						//array('args'=>',brand,goods',									'text'=>lang('nc_brand_manage')),
						//array('args'=>',goods_tpl,goods',								'text'=>'标准商品模板管理'),
						//array('args'=>',goods_audit,goods',								'text'=>'商品价格审核管理'),
						//array('args'=>'type,type,goods',								'text'=>lang('nc_type_manage')),
						//array('args'=>'spec,spec,goods',								'text'=>lang('nc_spec_manage')),
						//array('args'=>'list,goods_album,goods',						'text'=>lang('nc_album_manage')),

					)
				),
				/*3 => array(
					'nav' => 'store',
					'text' => lang('nc_store'),
					'list' => array(
						array('args'=>',shop,store',									'text'=>lang('nc_store_manage')),
						//array('args'=>'store_grade,store_grade,store',				'text'=>lang('nc_store_grade')),
						//array('args'=>'store_class,store_class,store',				'text'=>lang('nc_store_class')),
						//array('args'=>'store_domain_setting,domain,store',			'text'=>lang('nc_domain_manage')),
						//array('args'=>'stracelist,sns_strace,store',					'text'=>lang('nc_s_snstrace')),
						//array('args'=>'help_store,help_store,store',					'text'=>'店铺帮助'),
						//array('args'=>'edit_info,store_joinin,store',					'text'=>'开店首页'),
						//array('args'=>'list,ownshop,store',							'text'=>'自营店铺'),
					)
				),*/
				/*4 => array(
					'nav' => 'member',
					'text' => lang('nc_member'),
					'list' => array(
						array('args'=>',user,member',									'text'=>lang('nc_member_manage')),
						//array('args'=>'index,member_grade,member',					'text'=>'会员级别'),
						//array('args'=>'index,exppoints,member',						'text'=>lang('nc_exppoints_manage')),
						array('args'=>'send,message,member',							'text'=>lang('nc_member_notice')),
						array('args'=>',shop,member',								    'text'=>lang('nc_store_manage')),
						array('args'=>'index,deliver,member',						    'text'=>'派送员管理'),
						//array('args'=>',integral_goods,member',						'text'=>lang('nc_member_pointsmanage')),
						//array('args'=>'predeposit,predeposit,member',					'text'=>lang('nc_member_predepositmanage')),
						//array('args'=>'sharesetting,sns_sharesetting,member',			'text'=>lang('nc_binding_manage')),
						//array('args'=>'class_list,sns_malbum,member',					'text'=>lang('nc_member_album_manage')),
						//array('args'=>'tracelist,snstrace,member',					'text'=>lang('nc_snstrace')),
						//array('args'=>'member_tag,sns_member,member',					'text'=>lang('nc_member_tag')),
						//array('args'=>'chat_log,chat_log,member',						'text'=>'聊天记录')
					)
				),*/
				5 => array(
					'nav' => 'trade',
					'text' => lang('nc_trade'),
					'list' => array(
						array('args'=>'index,order,trade',				        		'text'=>lang('nc_order_manage')),
						//array('args'=>',recharge,trade',				    			'text'=>'充值管理'),
						//array('args'=>',cash,trade',				    				'text'=>'提现管理'),
						//array('args'=>'index,vr_order,trade',				    		'text'=>'虚拟订单'),
						//array('args'=>'refund_manage,refund,trade',						'text'=>'退款管理'),
						//array('args'=>'index_deliver,order,trade',						'text'=>'派送管理'),
						//array('args'=>'return_manage,return,trade',					'text'=>'退货管理'),
						//array('args'=>'refund_manage,vr_refund,trade',	    		'text'=>'虚拟订单退款'),
						//array('args'=>'consulting,consulting,trade',					'text'=>lang('nc_consult_manage')),
						//array('args'=>'inform_list,inform,trade',						'text'=>lang('nc_inform_config')),
						//array('args'=>',comment,trade',									'text'=>lang('nc_goods_evaluate')),
					    //array('args'=>'export_all,excel,trade',									'text'=>'数据导出'),
						//array('args'=>'complain_new_list,complain,trade',				'text'=>lang('nc_complain_config')),
					)
				),
				/*6 => array(
					'nav' => 'website',
					'text' => lang('nc_website'),
					'list' => array(
						//array('args'=>'article_class,article_class,website',			'text'=>lang('nc_article_class')),
						array('args'=>',article,website',								'text'=>lang('nc_article_manage')),
						//array('args'=>'document,document,website',					'text'=>lang('nc_document')),
						//array('args'=>'navigation,navigation,website',				'text'=>lang('nc_navigation')),
						//array('args'=>'ap_manage,adv,website',						'text'=>lang('nc_adv_manage')),
						//array('args'=>'web_config,web_config,website',				'text'=>lang('nc_web_index')),
						//array('args'=>'rec_list,rec_position,website',				'text'=>lang('nc_admin_res_position')),
						array('args'=>',link,website',									'text'=>'友情链接'),
						
					)
				),*/
				/*7 => array(
					'nav' => 'operation',
					'text' => lang('nc_operation'),
					'list' => array(
						
						//array('args'=>'groupbuy_template_list,groupbuy,operation',	'text'=>lang('nc_groupbuy_manage')),
	                    //array('args'=>'index,vr_groupbuy,operation',               	'text'=>'虚拟抢购设置'),
						//array('args'=>'xianshi_apply,promotion_xianshi,operation',	'text'=>lang('nc_promotion_xianshi')),
						//array('args'=>'mansong_apply,promotion_mansong,operation',	'text'=>lang('nc_promotion_mansong')),
						//array('args'=>'bundling_list,promotion_bundling,operation',	'text'=>lang('nc_promotion_bundling')),
						//array('args'=>'goods_list,promotion_booth,operation',			'text'=>lang('nc_promotion_booth')),
						//array('args'=>'voucher_apply,voucher,operation',           	'text'=>lang('nc_voucher_price_manage')),
						//array('args'=>'index,bill,operation',					    	'text'=>lang('nc_bill_manage')),
						//array('args'=>'index,vr_bill,operation',						'text'=>'虚拟订单结算'),
						array('args'=>',activity,operation',							'text'=>lang('nc_activity_manage')),
						array('args'=>',coupon,operation',								'text'=>'优惠券管理'),
						array('args'=>',integral_goods,operation',						'text'=>lang('nc_pointprod')),
						array('args'=>',first,operation',								'text'=>'推首管理'),
						array('args'=>'index,first_category,operation',					'text'=>lang('nc_class_index_push_manage')),
					    array('args'=>'index,invite,operation',							'text'=>lang('nc_invite_bonus_list')),
					    array('args'=>'userlist,invite,operation',						'text'=>'分佣关系'),
                        array('args'=>'index,service,operation',						'text'=>'客服'),
                        array('args'=>',feedback,operation',						'text'=>'意见反馈'),
						//array('args'=>'index,mall_consult,operation',             	'text'=>'平台客服'),
	                    //array('args'=>'index,rechargecard,operation',             	'text'=>'平台充值卡'),
	                    //array('args'=>'index,delivery,operation',                  	'text'=>'物流自提服务站')
					)
				),*/
				/*8 => array(
					'nav' => 'stat',
					'text' => lang('nc_stat'),
					'list' => array(
				        array('args'=>'general,stat_general,stat',						'text'=>lang('nc_statgeneral')),
						array('args'=>'scale,stat_industry,stat',						'text'=>lang('nc_statindustry')),
				        array('args'=>'newmember,stat_member,stat',						'text'=>lang('nc_statmember')),
						array('args'=>'newstore,stat_store,stat',						'text'=>lang('nc_statstore')),
						array('args'=>'income,stat_trade,stat',							'text'=>lang('nc_stattrade')),
						array('args'=>'pricerange,stat_goods,stat',						'text'=>lang('nc_statgoods')),
						array('args'=>'promotion,stat_marketing,stat',					'text'=>lang('nc_statmarketing')),
						array('args'=>'refund,stat_aftersale,stat',						'text'=>lang('nc_stataftersale')),

					)
				),*/
			),
		);

		return $menuList;
	}

	/**
	 * 过滤掉无权查看的菜单
	 *
	 * @param array $menu
	 * @return array
	 */
	private final function parseMenu($menu = array()){
		if ($this->admin_info['is_super'] == 1) return $menu;
		foreach ($menu['left'] as $k=>$v) {
			foreach ($v['list'] as $xk=>$xv) {
				$tmp = explode(',',$xv['args']);
				//以下几项不需要验证
				$except = array('index','dashboard','login','common');
				if (in_array($tmp[1],$except)) continue;
				if (!in_array($tmp[1],$this->permission) && !in_array($tmp[1].'.'.$tmp[0],$this->permission)){
					unset($menu['left'][$k]['list'][$xk]);
				}
			}
			if (empty($menu['left'][$k]['list'])) {
				unset($menu['top'][$k]);unset($menu['left'][$k]);
			}
		}
		return $menu;
	}

}
