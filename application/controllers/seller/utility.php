<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Utility extends BaseSellerController {


	public function file(){
		$sellerInfo = $this->seller_info;
        $company_id = $sellerInfo['company_id'];
        $admin_id = $sellerInfo['admin_id'];

        $this->load->model('wx/Attach_model');
        $this->load->helper('ifile');

		$do = $this->input->get('do');
		if (!in_array($do, array('upload', 'fetch', 'browser', 'delete', 'local'))) {
			exit('Access Denied');
		}

		$result = array(
			'error' => 1,
			'message' => '',
			'data' => ''
		);

		$type = !empty($_COOKIE['__fileupload_type'])?$_COOKIE['__fileupload_type']:'image';
		$type = in_array($type, array('image','audio')) ? $type : 'image';
		$option = array();
		$option = array_elements(array('uploadtype', 'global', 'dest_dir'), $_POST);
		$option['width'] = intval(!empty($option['width'])?$option['width']:0);
		$option['global'] = !empty($_COOKIE['__fileupload_global']);

		$dest_dir = !empty($_COOKIE['__fileupload_dest_dir'])?$_COOKIE['__fileupload_dest_dir']:'';
		if (preg_match('/^[a-zA-Z0-9_\/]{0,50}$/', $dest_dir, $out)) {
			$dest_dir = trim($dest_dir, '/');
			$pieces = explode('/', $dest_dir);
			if(count($pieces) > 3){
				$dest_dir = '';
			}
		} else {
			$dest_dir = '';
		}

		$upload_dir = BASE_UPLOAD_PATH.'/wxattach';

		$setting = array('extentions'=>array('gif','jpg','jpeg','png'),'limit'=>5000);

		$setting['folder'] = "{$type}s/{$company_id}";
		if(empty($dest_dir)){
			$setting['folder'] .= '/'.date('Y/m/');
		} else {
			$setting['folder'] .= '/'.$dest_dir.'/';
		}
		

		if ($do == 'fetch') {
			$url = trim($this->input->post_get('url'));
			$this->load->helper('ihttp');
			$resp = ihttp_get($url);
			if (is_error($resp)) {
				$result['message'] = '提取文件失败, 错误信息: '.$resp['message'];
				die(json_encode($result));
			}
			if (intval($resp['code']) != 200) {
				$result['message'] = '提取文件失败: 未找到该资源文件.';
				die(json_encode($result));
			}
			$ext = '';
			if ($type == 'image') {
				switch ($resp['headers']['Content-Type']){
					case 'application/x-jpg':
					case 'image/jpeg':
						$ext = 'jpg';
						break;
					case 'image/png':
						$ext = 'png';
						break;
					case 'image/gif':
						$ext = 'gif';
						break;
					default:
						$result['message'] = '提取资源失败, 资源文件类型错误.';
						die(json_encode($result));
						break;
				}
			} else {
				$result['message'] = '提取资源失败, 仅支持图片提取.';
				die(json_encode($result));
			}
			
			if (intval($resp['headers']['Content-Length']) > $setting['limit'] * 1024) {
				$result['message'] = '上传的媒体文件过大('.sizecount($size).' > '.sizecount($setting['limit'] * 1024);
				die(json_encode($result));
			}
			$originname = pathinfo($url, PATHINFO_BASENAME);
			$filename = file_random_name($setting['folder'], $ext);
			$pathname = $setting['folder'] . $filename;
			$fullname = $upload_dir . '/' . $pathname;
			if (file_put_contents($fullname, $resp['content']) == false) {
				$result['message'] = '提取失败.';
				die(json_encode($result));
			}
		}


		if ($do == 'upload') {
			if (empty($_FILES['file']['name'])) {
				$result['message'] = '上传失败, 请选择要上传的文件！';
				die(json_encode($result));
			}
			if ($_FILES['file']['error'] != 0) {
				$result['message'] = '上传失败, 请重试.';
				die(json_encode($result));
			}
			/*if (!file_is_image($_FILES['file']['name'])) {
				$result['message'] = '上传失败, 请重试.';
				die(json_encode($result));
			}*/
			$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			$size = intval($_FILES['file']['size']);
			$originname = $_FILES['file']['name'];
			$filename = file_random_name($setting['folder'], $ext);
			$file = file_upload($_FILES['file'], $type, $setting['folder'] . $filename);
			if (is_error($file)) {
				$result['message'] = $file['message'];
				die(json_encode($result));
			}
			$pathname = $file['path'];
			$fullname = $upload_dir . '/' . $pathname;
		}

		if ($do == 'fetch' || $do == 'upload') {
			if($type == 'image'){
				$thumb = empty($setting['thumb']) ? 0 : 1;
				$width = intval(!empty($setting['width'])?$setting['width']:0); 
				if(isset($option['thumb'])){
					$thumb = empty($option['thumb']) ? 0 : 1;
				}
				if (isset($option['width']) && !empty($option['width'])) {
					$width = intval($option['width']);
				}
				if ($thumb == 1 && $width > 0) {
					$thumbnail = file_image_thumb($fullname, '', $width);
					@unlink($fullname);
					if (is_error($thumbnail)) {
						$result['message'] = $thumbnail['message'];
						die(json_encode($result));
					} else {
						$filename = pathinfo($thumbnail, PATHINFO_BASENAME);
						$pathname = $thumbnail;
						$fullname = ATTACHMENT_ROOT .'/'.$pathname;
					}
				}
			}

			$info = array(
				'name' => $originname,
				'ext' => $ext,
				'filename' => $pathname,
				'attachment' => $pathname,
				'url' => tomedia($pathname),
				'is_image' => $type == 'image' ? 1 : 0,
				'filesize' => filesize($fullname),
			);
			if ($type == 'image') {
				$size = getimagesize($fullname);
				$info['width'] = $size[0];
				$info['height'] = $size[1];
			} else {
				$size = filesize($fullname);
				$info['size'] = sizecount($size);
			}
			if (!empty($_W['setting']['remote']['type'])) {
				$remotestatus = file_remote_upload($pathname);
				if (is_error($remotestatus)) {
					$result['message'] = '远程附件上传失败，请检查配置并重新上传';
					file_delete($pathname);
					die(json_encode($result));
				} else {
					file_delete($pathname);
					$info['url'] = tomedia($pathname);
				}
			}

			$data = array('company_id'=>$company_id,
				'admin_id'=>$admin_id,
				'filename' => $originname,
				'attach' => 'upload/wxattach/'.$pathname,
				'type' => $type == 'image' ? 1 : 2,
				'createtime' => time(),
				);
			$this->Attach_model->insert_string($data);
			die(json_encode($info));
		}

		if ($do == 'delete') {
			$id = intval($this->input->post_get('id'));
			$media = pdo_get('core_attachment', array('uniacid' => $_W['uniacid'], 'id' => $id));
			if(empty($media)) {
				exit('文件不存在或已经删除');
			}
			if(empty($_W['isfounder']) && $_W['role'] != 'manager') {
				exit('您没有权限删除该文件');
			}
			load()->func('file');
			if (!empty($_W['setting']['remote']['type'])) {
				$status = file_remote_delete($media['attachment']);
			} else {
				$status = file_delete($media['attachment']);
			}
			if(is_error($status)) {
				exit($status['message']);
			}
			pdo_delete('core_attachment', array('uniacid' => $uniacid, 'id' => $id));
			exit('success');
		}

		if ($do == 'local') {
			$types = array('image', 'audio');
			$type = in_array($this->input->post_get('type'), $types) ? $this->input->post_get('type') : 'image';
			$typeindex = array('image' => 1, 'audio' => 2);

			$arrParam = array();
			$arrWhere = array('company_id'=>$company_id,'type'=>$typeindex[$type]);
			$arrParam['type'] = $typeindex[$type];

			$year = intval($this->input->post_get('year'));
			$month = intval($this->input->post_get('month'));
			if($year > 0 || $month > 0) {
				if($month > 0 && !$year) {
					$year = date('Y');
					$starttime = strtotime("{$year}-{$month}-01");
					$endtime = strtotime("+1 month", $starttime);
				} elseif($year > 0 && !$month) {
					$starttime = strtotime("{$year}-01-01");
					$endtime = strtotime("+1 year", $starttime);
				} elseif($year > 0 && $month > 0) {
					$year = date('Y');
					$starttime = strtotime("{$year}-{$month}-01");
					$endtime = strtotime("+1 month", $starttime);
				}
				//$condition .= ' AND createtime >= :starttime AND createtime <= :endtime';
				$arrWhere['createtime>='] = $starttime;
				$arrWhere['createtime<='] = $endtime;
				$arrParam['starttime'] = $starttime;
				$arrParam['endtime'] = $endtime;
			}

			$page = intval($this->input->post_get('page'));
			$page = max(1, $page);
			$pagesize = $this->input->post_get('pagesize') ? intval($this->input->post_get('pagesize')) : 32;
			$list = $this->Attach_model->fetch_page($page, $pagesize, $arrWhere,'*', 'id desc');

			$data = array();
			foreach ($list['rows'] as $item) {
				$item['url'] = BASE_SITE_URL.'/'.$item['attach'];//tomedia($item['attach']);
				$item['createtime'] = date('Y-m-d', $item['createtime']);
				$data[$item['id']]=$item;
			}

			message(array('page'=> pagination($list['count'], $page, $pagesize, '', array('before' => '2', 'after' => '2', 'ajaxcallback'=>'null')), 'items' => $data), '', 'ajax');
		}

	}


}