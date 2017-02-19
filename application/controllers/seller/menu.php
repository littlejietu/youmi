<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Menu extends BaseSellerController {

	public function __construct()
    {
        
        parent::__construct();
        $this->load->model('wx/Menu_model');
        $this->load->library('WeixinThird');
    }

    public function index(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$status = $this->input->post_get('status');
		
		$account = new WeixinThird($company_id);
		$result = $account->menuQuery();
		if(is_error($result)) {
			message($result['message'], '', 'error');
		}
		$this->Menu_model->update_by_where(array('company_id' => $company_id,'status'=>1), array('status' => 0));
		$default_menu = $result['menu'];
		if(!empty($default_menu)) {
			$condition_menu = !empty($result['conditionalmenu'])?$result['conditionalmenu']:array();
			$condition_menu[] = array(
				'button' => $default_menu['button'],
				'type' => 1,
				'matchrule' => array(),
			);
			if(!empty($condition_menu)) {
				foreach($condition_menu as $menu) {
					$data = array(
						'company_id' => $company_id,
						'type' => empty($menu['matchrule']) ? 1 : 3,
						'data' => base64_encode(iserializer($menu)),
						'menuid' => !empty($menu['menuid'])?$menu['menuid']:0,
						'status' => 1,
					);

					if(!empty($menu['matchrule'])){
						$data['sex'] = intval($menu['matchrule']['sex']);
						$data['group_id'] = isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : -1;
						$data['client_platform_type'] = intval($menu['matchrule']['client_platform_type']);
						$data['area'] = trim($menus['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']);
					}

					$where = array('company_id'=>$company_id);
					if(empty($menu['matchrule']))
						$where['type'] = 1;
					else
						$where['menuid'] = $menu['menuid'];
					$info = $this->Menu_model->get_by_where($where);
					if(!empty($info)) {
						$this->Menu_model->update_by_where(array('company_id' => $company_id, 'id' => $info['id']), $data);
					} else {
						$this->Menu_model->insert_string($data);
					}
				}
			}
		}

		$page     = _get_page();
        $pagesize = 20;
        $arrParam = array();
        $arrWhere = array('company_id'=>$company_id);
        if(!empty($status))
			$arrWhere['status'] = $status == 'history' ? -1 : 1;
		else
			$arrWhere['status<>'] = -1;
        $list = $this->Menu_model->fetch_page($page, $pagesize, $arrWhere,'*','type asc');

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url($_SERVER['PATH_INFO'], $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
        
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

		$result = array(
			'list' => $list,
			'arrParam' => $arrParam,
			'default_menu'=>$default_menu,
		);



		$this->load->view('seller/wx/menu',$result);
    }

	public function add(){
		$sellerInfo = $this->seller_info;
        $company_id = $this->seller_info['company_id'];
		$type = intval($this->input->get('type'));
		$id = intval($this->input->get('id'));

		$this->load->model('Menu_model');
		$params = array();
		$menu = array();
		if($id > 0) {

			$menu = $this->Menu_model->get_by_id($id);
			if(empty($menu) || $menu['company_id']!=$company_id){
				exit('err');
			}
			if(!empty($menu)) {
				$menu['data'] = iunserializer(base64_decode($menu['data']));
				if(!empty($menu['data'])) {
					if(!empty($menu['data']['matchrule']['province'])) {
						$menu['data']['matchrule']['province'] .= '省';
					}
					if(!empty($menu['data']['matchrule']['city'])) {
						$menu['data']['matchrule']['city'] .= '市';
					}
					$params = $menu['data'];
					$params['title'] = $menu['title'];
					$params['type'] = $menu['type'];
					$params['id'] = $menu['id'];
					$params['status'] = $menu['status'];
				}
				$type = $menu['type'];
			}
		}
		// $groups = mc_fans_groups();
		// if(empty($groups)) {
		// 	message($groups['message'], '', 'error');
		// }

		$result = array('menu'=>$menu,
			'params'=>$params,
        	);

		$this->load->view('seller/wx/menu_add',$result);
	}

	public function save(){
		$sellerInfo = $this->seller_info;
        $company_id = $this->seller_info['company_id'];

		set_time_limit(0);
		$post = $this->input->raw_input_stream;
		$post = json_decode($post,true);
		$post = $post['group'];
		$menu = array();
		if(!empty($post['button'])) {
			foreach($post['button'] as &$button) {
				$temp = array();
				$temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $button['name']);
				$temp['name'] = urlencode($temp['name']);
				if (empty($button['sub_button'])) {
					$temp['type'] = $button['type'];
					if($button['type'] == 'view') {
						$temp['url'] = urlencode($button['url']);
					} elseif ($button['type'] == 'media_id' || $button['type'] == 'view_limited') {
						$temp['media_id'] = urlencode($button['media_id']);
					} else {
						$temp['key'] = urlencode($button['key']);
					}
				} else {
					foreach($button['sub_button'] as &$subbutton) {
						$sub_temp = array();
						$sub_temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $subbutton['name']);
						$sub_temp['name'] = urlencode($sub_temp['name']);
						$sub_temp['type'] = $subbutton['type'];
						if($subbutton['type'] == 'view') {
							$sub_temp['url'] = urlencode($subbutton['url']);
						} elseif ($subbutton['type'] == 'media_id' || $subbutton['type'] == 'view_limited') {
							$sub_temp['media_id'] = urlencode($subbutton['media_id']);
						} else {
							$sub_temp['key'] = urlencode($subbutton['key']);
						}
						$temp['sub_button'][] = $sub_temp;
					}
				}
				$menu['button'][] = $temp;
			}
		}

		if($post['type'] == 3 && !empty($post['matchrule'])) {
			if($post['matchrule']['sex'] > 0) {
				$menu['matchrule']['sex'] = $post['matchrule']['sex'];
			}
			if($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['group_id'] = $post['matchrule']['group_id'];
			}
			if($post['matchrule']['client_platform_type'] > 0) {
				$menu['matchrule']['client_platform_type'] = $post['matchrule']['client_platform_type'];
			}

			if(!empty($post['matchrule']['province'])) {
				$menu['matchrule']['country'] = urlencode('中国');
				$menu['matchrule']['province'] = urlencode(str_replace('省', '', $post['matchrule']['province']));
				if(!empty($post['matchrule']['city'])) {
					$menu['matchrule']['city'] = urlencode(str_replace('市', '', $post['matchrule']['city']));
				}
			}
		}

		$account = new WeixinThird($company_id);
		$ret = $account->menuCreate($menu);
		if(is_error($ret)) {
			message($ret, '', 'ajax');exit;
		} else {
			$menu = json_decode(urldecode(json_encode($menu)), true);
			if(!isset($menu['matchrule'])) {
				$menu['matchrule'] = array();
			}

			$insert = array(
				'company_id' => $company_id,
				//'menuid' => $ret['errcode'],
				'title' => $post['title'],
				'type' => $post['type'],
				'data' => base64_encode(iserializer($menu)),
				'status' => 1,
				'createtime' => time(),
			);
			if(!empty($menu['matchrule'])){
				$insert['sex'] = intval($menu['matchrule']['sex']);
				$insert['group_id'] = isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : -1;
				$insert['client_platform_type'] = intval($menu['matchrule']['client_platform_type']);
				$insert['area'] = trim($menus['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']);
			}

			if($post['type'] == 1) {
				//历史
				$history = $this->Menu_model->get_by_where(array('company_id'=>$company_id, 'type'=>2,'status<>'=>-1));
				if(empty($history)) {
					$data = $insert;
					$data['type'] = 2;
					$data['status'] = 0;
					$this->Menu_model->insert_string($data);
				} 
				/*else {
					$data = $insert;
					$data['type'] = 2;
					$data['status'] = 0;
					$this->Menu_model->update_by_id($history['id'], $data);
				}*/

				$default = $this->Menu_model->get_by_where(array('company_id'=>$company_id, 'type'=>1));
				if(!empty($default)) {
					$this->Menu_model->update_by_id($default['id'], $insert);
				} else {
					$this->Menu_model->insert_string($insert);
				}
				message(error(0, ''), '', 'ajax');
			} elseif($post['type'] == 3) {
				if($post['status'] == 0 && $post['id'] > 0) {
					$this->Menu_model->update_by_where(array('id'=>$post['id'],'company_id'=>$company_id, 'type'=>3), $insert);
					
				} else {
					$this->Menu_model->insert_string($insert);
				}
				message(error(0, ''), '', 'ajax');
			}
		}
	}

	public function push(){
		$sellerInfo = $this->seller_info;
		$company_id = $sellerInfo['company_id'];

		$id = intval($this->input->get('id'));
		$data = $this->Menu_model->get_by_where(array('company_id' => $company_id, 'id' => $id));
		if(empty($data)) {
			message('菜单不存在或已删除', '', 'error');
		}

		$post = iunserializer(base64_decode($data['data']));
		if(empty($post)) {
			message('菜单数据错误', '', 'error');
		}
		$menu = array();
		if(!empty($post['button'])) {
			foreach($post['button'] as &$button) {
				$temp = array();
				$temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $button['name']);
				$temp['name'] = urlencode($temp['name']);
				if (empty($button['sub_button'])) {
					$temp['type'] = $button['type'];
					if($button['type'] == 'view') {
						$temp['url'] = urlencode($button['url']);
					} elseif ($button['type'] == 'media_id' || $button['type'] == 'view_limited') {
						$temp['media_id'] = urlencode($button['media_id']);
					} else {
						$temp['key'] = urlencode($button['key']);
					}
				} else {
					foreach($button['sub_button'] as &$subbutton) {
						$sub_temp = array();
						$sub_temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $subbutton['name']);
						$sub_temp['name'] = urlencode($sub_temp['name']);
						$sub_temp['type'] = $subbutton['type'];
						if($subbutton['type'] == 'view') {
							$sub_temp['url'] = urlencode($subbutton['url']);
						} elseif ($subbutton['type'] == 'media_id' || $subbutton['type'] == 'view_limited') {
							$sub_temp['media_id'] = urlencode($subbutton['media_id']);
						} else {
							$sub_temp['key'] = urlencode($subbutton['key']);
						}
						$temp['sub_button'][] = $sub_temp;
					}
				}
				$menu['button'][] = $temp;
			}
		}

		if(!empty($post['matchrule'])) {
			if($post['matchrule']['sex'] > 0) {
				$menu['matchrule']['sex'] = $post['matchrule']['sex'];
			}
			if($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['group_id'] = $post['matchrule']['group_id'];
			}
			if($post['matchrule']['client_platform_type'] > 0) {
				$menu['matchrule']['client_platform_type'] = $post['matchrule']['client_platform_type'];
			}
			if(!empty($post['matchrule']['province'])) {
				$menu['matchrule']['country'] = urlencode('中国');
				$menu['matchrule']['province'] = urlencode(rtrim($post['matchrule']['province'], '省'));
				if(!empty($post['matchrule']['city'])) {
					$menu['matchrule']['city'] = urlencode(rtrim($post['matchrule']['city'], '市'));
				}
			}
		}

		$account = new WeixinThird($company_id);
		$ret = $account->menuCreate($menu);

		if(is_error($ret)) {
			message($ret['message'], '', 'error');
		} else {
			if($data['type'] = 2) {
				$this->Menu_model->update_by_where(array('company_id'=>$company_id,'type'=>1), array('type'=>2));
				$this->Menu_model->update_by_where(array('company_id'=>$company_id,'id'=>$id), array('type'=>1,'status'=>1));
			}

			$this->Menu_model->update_by_where(array('company_id'=>$company_id,'id'=>$id), array('status'=>1,'data' => base64_encode(iserializer($menu))));
			message('推送成功', SELLER_SITE_URL.'/menu', 'success');
		}
	}

	public function del(){
		$sellerInfo = $this->seller_info;
        $company_id = $this->seller_info['company_id'];
		$id = intval($this->input->get('id'));

		$where = array('company_id'=>$company_id, 'id'=>$id);
		$info = $this->Menu_model->get_by_where($where);
		if(empty($info)) {
			message('菜单不存在或已经删除', '', 'error');
		}
		if($info['type'] == 1 || ($info['type'] == 3 && $info['menuid'] > 0)) {
			$account = new WeixinThird($company_id);
			$menuid = !empty($info['menuid'])?$info['menuid']:0;
			$ret = $account->menuDelete($menuid);
			if(is_error($ret)) {
				$message = "调用微信接口删除失败:{$ret['message']}<br>";
				message($message, '', 'error');
			}
		}
		
		if($info['type'] == 1) {
			$this->Menu_model->update_by_where(array('company_id'=>$company_id), array('status'=>-1));
		} else {
			$this->Menu_model->update_by_where($where, array('status'=>-1));

		}
		
		message('删除菜单成功', SELLER_SITE_URL.'/menu/', 'success');
	}


}