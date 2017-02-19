<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msg_log_model extends XT_Model {

	protected $mTable = 'wx_msg_log';

	public function log($keyword, $company_id, $message, $params){

		switch ($message['type']) {
            case 'text':
            	$original = !empty($message['original'])?$message['original']:'';
            	$redirection = !empty($message['redirection'])?$message['redirection']:'';
            	$source = !empty($message['source'])?$message['source']:'';
            	
                $content = iserializer(array('content' => $message['content'], 'original' =>$original , 'redirection' => $redirection, 'source' => $source));
                break;
            case 'image':
                $content = $message['url'];
                break;
            case 'voice':
                $content = iserializer(array('media' => $message['media'], 'format' => $message['format']));
                break;
            case 'video':
                $content = iserializer(array('media' => $message['media'], 'thumb' => $message['thumb']));
                break;
            case 'location':
                $content = iserializer(array('x' => $message['location_x'], 'y' => $message['location_y']));
                break;
            case 'link':
                $content = iserializer(array('title' => $message['title'], 'description' => $message['description'], 'url' => $message['url']));
                break;
            case 'subscribe':
                $content = iserializer(array('scene' => $message['scene'], 'ticket' => $message['ticket']));
                break;
            case 'qr':
                $content = iserializer(array('scene' => $message['scene'], 'ticket' => $message['ticket']));
                break;
            case 'click':
                $content = $message['content'];
                break;
            case 'view':
                $content = $message['url'];
                break;
            case 'trace':
                $content = iserializer(array('location_x' => $message['location_x'], 'location_y' => $message['location_y'], 'precision' => $message['precision']));
                break;
            default:
                $content = $message['content'];
        }

        $data = array(
            'company_id' => $company_id,
            'module' => $params['module'],
            'from_user' => $message['from'],
            'rid' => intval($params['rule']),
            'keyword' => $keyword,
            'message' => $content,
            'type' => $message['type'],
            'createtime' => $message['time'],
        );
        $this->insert_string($data);

	}
		
}