<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Thirdapi extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function wx($appid) {
        $appid = ltrim($appid, '/');
        $flag = $this->input->get('flag');

        $this->load->model('oil/Company_model');
        $this->load->model('oil/Company_config_model');
        $account = array();
        if ($appid == C('component_appid')) {
            $account = array(
                'type' => 3,
                'key' => $appid,
                'level' => 4,
                'token' => 'platformtestaccount'
            );
        } else {
            
            $info  = $this->Company_config_model->get_by_where(array('wx_appid'=>$appid));
            $id = $info['company_id'];
        }

        if(empty($id)) {
            $id = intval($this->input->get('id'));
        }
        if (!empty($id)) {
            $com_info = $this->Company_model->get_by_id($id);
            if(empty($info))
                $info = $this->Company_config_model->get_by_id($id);

            $account = array_merge($info, $com_info);
        }

        if(empty($account)) {
            exit('initial error hash or id');
        }
        if(empty($account['wx_token'])) {
            exit('initial missing token');
        }

        $engine = new MiRong($account);
        if ($account['status']!=1) {
            $engine->died('抱歉，站点已关闭，关闭原因：' . $account['reason']);
        }
        if (!empty($account['prd_end_time']) && $account['prd_end_time']<time()) {
            $engine->died('抱歉，您的公众号已过期，请及时联系管理员');
        }

        $account['key'] = $account['wx_appid'];

        $is_ajax = $this->input->is_ajax_request();
        if($is_ajax && $this->input->is_post() && $flag == 1) {
            $engine->encrypt();
        }
        if($is_ajax && $this->input->is_post() && $flag == 2) {
            $engine->decrypt();
        }
        //load()->func('compat.biz');
        $is_ajax = false;
        $engine->start();
    }



}




class MiRong {
    
    private $account = null;
    
    public $keyword = array();
    
    public $message = array();

    
    public function __construct($account) {
        $this->ci = & get_instance();
        $this->ci->load->library('WeixinThird');
        $this->account = new WeixinThird($account);
    }

    
    public function encrypt() {
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        $timestamp = time();
        $nonce = random(5);
        $token = $this->account['wx_token'];
        $signkey = array($token, $timestamp, $nonce);
        sort($signkey, SORT_STRING);
        $signString = implode($signkey);
        $signString = sha1($signString);

        $_GET['timestamp'] = $timestamp;
        $_GET['nonce'] = $nonce;
        $_GET['signature'] = $signString;
        $postStr = file_get_contents('php://input');
        if(!empty($this->account['wx_encodingaeskey']) && strlen($this->account['wx_encodingaeskey']) == 43 && !empty($this->account['key']) ) {
            $data = $this->account->encryptMsg($postStr);
            $array = array('encrypt_type' => 'aes', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
        } else {
            $data = array('', '');
            $array = array('encrypt_type' => '', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
        }
        exit(json_encode($array));
    }

    
    public function decrypt() {
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        $postStr = file_get_contents('php://input');
        if(!empty($this->account['wx_encodingaeskey']) && strlen($this->account['wx_encodingaeskey']) == 43 && !empty($this->account['key'])) {
            $resp = $this->account->local_decryptMsg($postStr);
        } else {
            $resp = $postStr;
        }
        exit($resp);
    }

    
    public function start() {
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        if(!$this->account->checkSign()) {
            exit('Check Sign Fail.');
        }
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
            $this->Company_model->update_by_id($this->account['company_id'], array('isconnect'=>1));
            exit(htmlspecialchars($_GET['echostr']));
        }
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $postStr = file_get_contents('php://input');
            if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
                $postStr = $this->account->decryptMsg($postStr);
            }
            logging('trace', $postStr);
            $message = $this->account->parse($postStr);
            
            $this->message = $message;
            if(empty($message)) {
                logging('waring', 'Request Failed');
                exit('Request Failed');
            }
            // $_W['openid'] = $message['from'];
            // $_W['fans'] = array('from_user' => $_W['openid']);
            
            $this->booking($message);
            /*if($message['event'] == 'unsubscribe') {
                $this->receive(array(), array(), array());
                exit();
            }
            */
            //$sessionid = md5($message['from'] . $message['to'] . $_W['company_id']);
            //session_id($sessionid);
            //WeSession::start($_W['company_id'], $_W['openid']);
            
            //$_SESSION['openid'] = $_W['openid'];
            $pars = $this->analyze($message);
            $pars[] = array(
                'message' => $message,
                'module' => 'default',
                'rule' => '-1',
            );
            $hitParam['rule'] = -2;
            $hitParam['module'] = '';
            $hitParam['message'] = $message;

            $hitKeyword = array();
            $response = array();
            foreach($pars as $par) {
                if(empty($par['module'])) {
                    continue;
                }
                $par['message'] = $message;
                $response = $this->process($par);
                if($this->isValidResponse($response)) {
                    $hitParam = $par;
                    if(!empty($par['keyword'])) {
                        $hitKeyword = $par['keyword'];
                    }
                    break;
                }
            }
            $response_debug = $response;
            $pars_debug = $pars;
            if($hitParam['module'] == 'default' && is_array($response) && is_array($response['params'])) {
                foreach($response['params'] as $par) {
                    if(empty($par['module'])) {
                        continue;
                    }
                    $response = $this->process($par);
                    if($this->isValidResponse($response)) {
                        $hitParam = $par;
                        if(!empty($par['keyword'])) {
                            $hitKeyword = $par['keyword'];
                        }
                        break;
                    }
                }
            }
            logging('params', $hitParam);
            logging('response', $response);
            $resp = $this->account->response($response);
                        if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
                $resp = $this->account->encryptMsg($resp);
                $resp = $this->account->xmlDetract($resp);
            }
            /*
            if($_W['debug']) {
                $_W['debug_data'] = array(
                    'resp' => $resp,
                    'is_default' => 0
                );
                if(count($pars_debug) == 1) {
                    $_W['debug_data']['is_default'] = 1;
                    $_W['debug_data']['params'] = $response_debug['params'];
                } else {
                    array_pop($pars_debug);
                    $_W['debug_data']['params'] = $pars_debug;
                }
                $_W['debug_data']['hitparam'] = $hitParam;
                $_W['modules']['cover'] = array('title' => '入口封面', 'name' => 'cover');

                load()->web('template');
                $process = template('utility/emulator', TEMPLATE_FETCH);
                echo json_encode(array('resp' => $resp, 'process' => $process));
                exit();
            }
            */
            $mapping = array(
                '[from]' => $this->message['from'],
                '[to]' => $this->message['to'],
                '[rule]' => $hitParam['rule']
            );
            $resp = str_replace(array_keys($mapping), array_values($mapping), $resp);
            echo $resp;
            ob_flush();
            flush();
            $this->receive($hitParam, $hitKeyword, $response);
            ob_end_clean();
            exit();
        }
        logging('waring', 'Request Failed');
        exit('Request Failed');
    }

    private function isValidResponse($response) {
        if(is_array($response)) {
            if($response['MsgType'] == 'text' && !empty($response['Content'])) {
                return true;
            }
            if($response['MsgType'] == 'news' && !empty($response['Articles'])) {
                return true;
            }
            if(!in_array($response['MsgType'], array('text', 'news', 'image'))) {
                return true;
            }
        }
        return false;
    }

 
    private function booking($message) {

        $company_id = $this->account->account['company_id'];
        if ($message['event'] == 'unsubscribe' || $message['event'] == 'subscribe') {
            $this->ci->load->model('stat/Stat_fans_model');
            $todaystat = $this->ci->Stat_fans_model->get_by_where(array('date' => date('Ymd'), 'company_id' => $company_id));
            $id = 0;
            if(empty($todaystat)){
                $insert_data = array(
                    'company_id' => $company_id,
                    'date' => date('Ymd'),
                );
                $id = $this->ci->Stat_fans_model->insert_string($insert_data);
            }else
                $id = $todaystat['id'];

            if ($message['event'] == 'unsubscribe')
                $this->ci->Stat_fans_model->operate_by_id($id, array('cancel_num'=>'cancel_num+1'));
            elseif ($message['event'] == 'subscribe')
                $this->ci->Stat_fans_model->operate_by_id($id, array('new_num'=>'new_num+1'));
        }

        $this->ci->load->model('wx/Fans_model');        
        $fans = $this->ci->Fans_model->get_info($message['from'], 0, $company_id);
        // $default_groupid = rkcache("defaultgroupid:{$company_id}");
        // if (empty($default_groupid)) {
        //     $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE company_id = :company_id AND isdefault = 1', array(':company_id' => $_W['company_id']));
        //     cache_write("defaultgroupid:{$_W['company_id']}", $default_groupid);
        // }

        if(!empty($fans)) {
            if ($message['event'] == 'unsubscribe') {
                $this->ci->Fans_model->update_by_id($fans['id'], array('follow' => 0, 'unfollowtime' => TIMESTAMP));
            } elseif ($message['event'] != 'ShakearoundUserShake' && $message['msgtype'] != 'trace') {
                $rec = array();
                if (empty($fans['follow'])) {
                    $rec['follow'] = 1;
                    $rec['followtime'] = $message['time'];
                    $rec['unfollowtime'] = 0;
                }
            }
        } else {
            if ($message['event'] == 'subscribe' || $message['msgtype'] == 'text' || $message['msgtype'] == 'image') {
                $rec = array();
                if(!empty($this->account->account['site_id']))
                    $rec['site_id'] = $this->account->account['site_id'];
                $rec['company_id'] = $company_id;
                $rec['user_id'] = 0;
                $rec['openid'] = $message['from'];
                $rec['salt'] = random(8);
                $rec['follow'] = 1;
                $rec['followtime'] = $message['time'];
                $rec['unfollowtime'] = 0;
                $this->ci->Fans_model->insert_string($rec);
            }
        }
    }

    private function receive($par, $keyword, $response) {
        $company_id = $this->account->account['company_id'];
        $this->ci->load->model('wx/Qrcode_model');
        $this->ci->load->model('wx/Qrcode_log_model');
        if($this->message['event'] == 'subscribe' && !empty($this->message['ticket'])) {
            $sceneid = $this->message['scene'];
            $company_id = $this->company_id;
            $ticket = trim($this->message['ticket']);
            if(!empty($ticket)) {
                $qr = $this->ci->Qrcode_model->get_by_where(array('company_id'=>$company_id,'ticket'=>$ticket),'`id`, `keyword`, `name`');
                if(empty($qr)) {
                    $qr = array();
                }
            }
            if(empty($qr)) {
                $sceneid = trim($this->message['scene']);
                $where = array('company_id'=>$company_id);
                if(is_numeric($sceneid)) 
                    $where['qrcid'] = $sceneid;
                else
                    $where['scene_str'] = $sceneid;

                $qr = $this->ci->Qrcode_model->get_by_where($where);
            }
            $insert = array(
                'company_id' => $company_id,
                'qid' => $qr['id'],
                'openid' => $this->message['from'],
                'type' => 1,
                'qrcid' => intval($sceneid),
                'scene_str' => $sceneid,
                'name' => $qr['name'],
                'createtime' => TIMESTAMP,
            );
            $this->ci->Qrcode_log_model->insert_string($insert);
        } elseif($this->message['event'] == 'SCAN') {
            $company_id = $this->company_id;
            $sceneid = trim($this->message['scene']);
                $where = array('company_id'=>$company_id);
                if(is_numeric($sceneid)) 
                    $where['qrcid'] = $sceneid;
                else
                    $where['scene_str'] = $sceneid;

            $row = $this->ci->Qrcode_model->get_by_where($where);
            $insert = array(
                'company_id' => $company_id,
                'qid' => $row['id'],
                'openid' => $this->message['from'],
                'type' => 2,
                'qrcid' => intval($sceneid),
                'scene_str' => $sceneid,
                'name' => $row['name'],
                'createtime' => TIMESTAMP,
            );
            $this->ci->Qrcode_log_model->insert_string($insert);
        }

        if ($this->message['event'] == 'subscribe' && !empty($this->account->account) && ($this->account->account['level'] == ACCOUNT_SERVICE_VERIFY || $this->account['level'] == ACCOUNT_SUBSCRIPTION_VERIFY)) {
            $this->ci->load->library('WeixinThirdAuth');
            $this->ci->load->model('wx/Fans_model');
            $account_obj = new WeixinThirdAuth($this->account);
            $userinfo = $account_obj->fansQueryInfo($this->message['from']);
            if(!is_error($userinfo) && !empty($userinfo) && !empty($userinfo['subscribe'])) {
                $userinfo['nickname'] = stripcslashes($userinfo['nickname']);
                if (!empty($userinfo['headimgurl'])) {
                    $userinfo['headimgurl'] = rtrim($userinfo['headimgurl'], '0') . 132;
                }
                $userinfo['avatar'] = $userinfo['headimgurl'];
                $fans = array(
                    'unionid' => $userinfo['unionid'],
                    'nickname' => $userinfo['nickname'],
                    'tag' => base64_encode(iserializer($userinfo)),
                );

                $this->ci->Fans_model->update_by_where(array('openid' => $this->message['from']), $fans);
                
                if (!empty($this->account['member']['uid'])) {
                    $this->ci->load->model('user/User_model');
                    $user_id = $this->account['member']['uid'];
                    $member = array();
                    if (!empty($userinfo['nickname'])) {
                        $member['nickname'] = $fans['nickname'];
                    }
                    if (!empty($userinfo['headimgurl'])) {
                        $member['avatar'] = $userinfo['headimgurl'];
                    }
                    $this->ci->User_model->update_by_id($user_id, $member);
                }
            }
        }
        

        $this->ci->load->model('wx/Msg_log_model');
        $this->ci->load->model('stat/Stat_rule_model');
        $this->ci->load->model('stat/Stat_keyword_model');
        $this->ci->Msg_log_model->log($this->message['content'], $company_id, $this->message, $par);
        if(!empty($par['rule'])) {
            $where = array('rid'=>$par['rule'], 'createtime' => strtotime(date('Y-m-d')));
            $rule_stat_found = $this->ci->Stat_rule_model->get_by_where($where);
            if (empty($rule_stat_found)) {
                $data = array(
                        'company_id' => $company_id,
                        'rid' => $par['rule'],
                        'createtime' => strtotime(date('Y-m-d')),
                        'hit' => 1,
                        'lastupdate' => $this->message['time'],
                    );
                $this->ci->Stat_rule_model->insert_string($data);
            }else
                $this->ci->Stat_rule_model->operate_by_id($rule_stat_found['id'], array('hit'=>'hit + 1', 'lastupdate'=>TIMESTAMP) );
        }
        if (!empty($keyword)) {
            $where = array('keyword'=>$keyword, 'createtime' => strtotime(date('Y-m-d')));
            $key_stat_found = $this->ci->Stat_keyword_model->get_by_where($where);
            if (empty($key_stat_found)) {
                $data = array(
                        'company_id' => $company_id,
                        'rid' => $par['rule'],
                        'keyword' => $keyword,
                        'createtime' => strtotime(date('Y-m-d')),
                        'hit' => 1,
                        'lastupdate' => $this->message['time'],
                    );
                $this->ci->Stat_keyword_model->insert_string($data);
            }else
                $this->ci->Stat_keyword_model->operate_by_id($key_stat_found['id'], array('hit'=>'hit + 1', 'lastupdate'=>TIMESTAMP) );
        }

    }

    
    private function analyze(&$message) {
        $params = array();
        if(in_array($message['type'], array('event', 'qr'))) {
            $params = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
            if(!empty($params)) {
                return (array)$params;
            }
        }

        
        if(!empty($_SESSION['__contextmodule']) && in_array($_SESSION['__contextmodule'], $this->modules)) {
            if($_SESSION['__contextexpire'] > TIMESTAMP) {
                $params[] = array(
                    'message' => $message,
                    'module' => $_SESSION['__contextmodule'],
                    'rule' => $_SESSION['__contextrule'],
                    'priority' => $_SESSION['__contextpriority'],
                    'context' => true
                );
                return $params;
            } else {
                unset($_SESSION);
                session_destroy();
            }
        }
        

        if(method_exists($this, 'analyze' . $message['type'])) {
            $temp = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
            if(!empty($temp) && is_array($temp)){
                $params += $temp;
            }
        } else {
            $params += $this->handler($message['type']);
        }

        return $params;
    }
    
    private function analyzeSubscribe(&$message) {
        global $_W;
        $params = array();
        $message['type'] = 'text'; 
        $message['redirection'] = true;
        if(!empty($message['scene'])) {
            $message['source'] = 'qr';
            $sceneid = trim($message['scene']);
            $scene_condition = '';
            if (is_numeric($sceneid)) {
                $scene_condition = " `qrcid` = '{$sceneid}'";
            }else{
                $scene_condition = " `scene_str` = '{$sceneid}'";
            }
            $qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE {$scene_condition} AND `company_id` = '{$_W['company_id']}'");
            if(!empty($qr)) {
                $message['content'] = $qr['keyword'];
                $params += $this->analyzeText($message);
            }
        }
        $message['source'] = 'subscribe';
        $setting = uni_setting($_W['company_id'], array('welcome'));
        if(!empty($setting['welcome'])) {
            $message['content'] = $setting['welcome'];
            $params += $this->analyzeText($message);
        }

        return $params;
    }

    private function analyzeQR(&$message) {
        global $_W;
        $params = array();
        $message['type'] = 'text';
        $message['redirection'] = true;
        if(!empty($message['scene'])) {
            $message['source'] = 'qr';
            $sceneid = trim($message['scene']);
            $scene_condition = '';
            if (is_numeric($sceneid)) {
                $scene_condition = " `qrcid` = '{$sceneid}'";
            }else{
                $scene_condition = " `scene_str` = '{$sceneid}'";
            }
            $qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE {$scene_condition} AND `company_id` = '{$_W['company_id']}'");
            if(!empty($qr)) {
                $message['content'] = $qr['keyword'];
                $params += $this->analyzeText($message);
            }
        }
        return $params;
    }

    public function analyzeText(&$message, $order = 0) {
       
        $pars = array();
        
        $order = intval($order);
        if(!isset($message['content'])) {
            return $pars;
        }

        $company_id = $this->account->account['company_id'];
        $key = $message['content'];
        $condition = <<<EOF
`company_id` IN ( 0, {$company_id} )
AND 
(
    ( `item_type` = 1 AND `search_key` = {$key})
    or
    ( `item_type` = 2 AND instr({$key}, `search_key`) )
    or
    ( `item_type` = 3 AND {$key} REGEXP `search_key` )
    or
    ( `item_type` = 4 )
)
AND `status`=1
EOF;
        
        if (intval($order) > 0) {
            $condition .= " AND `sort` > $order";
        }
        
        $this->ci->load->model('wx/Reply_model');
        $list = $this->ci->Reply_model->get_list($condition,'*','sort DESC, `item_type` ASC, id DESC',3);
        if(empty($list)) {
            return $pars;
        }
        foreach($list as $v) {
            $params = array(
                'message' => $message,
                'module' => $v['item_type'],
                'rule' => $v['id'],
                'priority' => $v['sort'],
                'keyword' => $key,
                'reply' => $v['replies'],
            );
            $pars[] = $params;
        }
        return $pars;
    }
    
    private function analyzeEvent(&$message) {
        if (strtolower($message['event']) == 'subscribe') {
            return $this->analyzeSubscribe($message);
        }
        if (strtolower($message['event']) == 'click') {
            $message['content'] = strval($message['eventkey']);
            return $this->analyzeClick($message);
        }
        if (in_array($message['event'], array('pic_photo_or_album', 'pic_weixin', 'pic_sysphoto'))) {
            pdo_query("DELETE FROM ".tablename('menu_event')." WHERE createtime < '".($GLOBALS['_W']['timestamp'] - 100)."' OR openid = '{$message['from']}'");
            if (!empty($message['sendpicsinfo']['count'])) {
                foreach ($message['sendpicsinfo']['piclist'] as $item) {
                    pdo_insert('menu_event', array(
                        'company_id' => $GLOBALS['_W']['company_id'],
                        'keyword' => $message['eventkey'],
                        'type' => $message['event'],
                        'picmd5' => $item,
                        'openid' => $message['from'],
                        'createtime' => TIMESTAMP,
                    ));
                }
            } else {
                pdo_insert('menu_event', array(
                    'company_id' => $GLOBALS['_W']['company_id'],
                    'keyword' => $message['eventkey'],
                    'type' => $message['event'],
                    'picmd5' => $item,
                    'openid' => $message['from'],
                    'createtime' => TIMESTAMP,
                ));
            }
            return true;
        }
        if (!empty($message['eventkey'])) {
            $message['content'] = strval($message['eventkey']);
            $message['type'] = 'text';
            $message['redirection'] = true;
            $message['source'] = $message['event'];
            return $this->analyzeText($message);
        }
        return $this->handler($message['event']);
    }
    
    private function analyzeClick(&$message) {
        if(!empty($message['content']) || $message['content'] !== '') {
            $message['type'] = 'text';
            $message['redirection'] = true;
            $message['source'] = 'click';
            return $this->analyzeText($message);
        }

        return array();
    }
    
    

    
    private function handler($type) {
        if(empty($type)) {
            return array();
        }
        global $_W;
        $params = array();
        $setting = uni_setting($_W['company_id'], array('default_message'));
        $df = $setting['default_message'];
        if(is_array($df) && isset($df[$type])) {
            if (!empty($df[$type]['type']) && $df[$type]['type'] == 'keyword') {
                $message = $this->message;
                $message['type'] = 'text';
                $message['redirection'] = true;
                $message['source'] = $type;
                $message['content'] = $df[$type]['keyword'];
                return $this->analyzeText($message);
            } else {
                $params[] = array(
                    'message' => $this->message,
                    'module' => $df[$type]['module'],
                    'rule' => '-1',
                );
                return $params;
            }
        }
        return array();
    }

    
    private function process($param) {

        $processor = new WeModuleProcessor();
        $processor->param = $param;
        $processor->message = $param['message'];
        $processor->rule = $param['rule'];
        $processor->priority = intval($param['priority']);
        $processor->inContext = !empty($param['context']) ? $param['context']===true:false;
        $response = $processor->respond();
        if(empty($response)) {
            return false;
        }

        return $response;
    }
    
    
    public function died($content = '') {
        //global $_W, $engine;
        if (empty($content)) {
            exit('');
        }
        
        if(!empty($this->message)){
            $response['FromUserName'] = $this->message['to'];
            $response['ToUserName'] = $this->message['from'];
            $response['MsgType'] = 'text';
            $response['Content'] = htmlspecialchars_decode($content);
            $response['CreateTime'] = TIMESTAMP;
            $response['FuncFlag'] = 0;
            $xml = array2xml($response);
            if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
                $resp = $this->account->encryptMsg($xml);
                $resp = $this->account->xmlDetract($resp);
            } else {
                $resp = $xml;
            }
            exit($resp);
        }
    }
}



class WeModuleProcessor {
    
    public $priority;
    
    public $message;
    
    public $inContext;
    
    public $rule;

    public $param;

    public function __construct(){
        
        // $_W['member'] = array();
        // if(!empty($_W['openid'])){
        //     load()->model('mc');
        //     $_W['member'] = mc_fetch($_W['openid']);
        // }
    }
    
    /*
    protected function beginContext($expire = 1800) {
        if($this->inContext) {
            return true;
        }
        $expire = intval($expire);
        //WeSession::$expire = $expire;
        $_SESSION['__contextmodule'] = $this->module['name'];
        $_SESSION['__contextrule'] = $this->rule;
        $_SESSION['__contextexpire'] = TIMESTAMP + $expire;
        $_SESSION['__contextpriority'] = $this->priority;
        $this->inContext = true;
        
        return true;
    }
    
    protected function refreshContext($expire = 1800) {
        if(!$this->inContext) {
            return false;
        }
        $expire = intval($expire);
        //WeSession::$expire = $expire;
        $_SESSION['__contextexpire'] = TIMESTAMP + $expire;
        
        return true;
    }

    
    protected function endContext() {
        unset($_SESSION['__contextmodule']);
        unset($_SESSION['__contextrule']);
        unset($_SESSION['__contextexpire']);
        unset($_SESSION['__contextpriority']);
        unset($_SESSION);
        session_destroy();
    }
    */
    
    public function respond(){
        
        $module = $this->param['module'];
        if($module==1){
            $arrReply = json_decode(htmlspecialchars_decode($this->param['reply']),true);
            $reply = $arrReply[array_rand($arrReply)]['content'];
            $reply = str_replace(array('<br>', '&nbsp;'), array("\n", ' '), $reply);
            $reply = strip_tags($reply, '<a>');
            return $this->respText($reply);
        }elseif($module==2){

        }



    }
    
    protected function respText($content) {
        if (empty($content)) {
            return error(-1, 'Invaild value');
        }
        if(stripos($content,'./') !== false) {
            preg_match_all('/<a .*?href="(.*?)".*?>/is',$content,$urls);
            if (!empty($urls[1])) {
                foreach ($urls[1] as $url) {
                    $content = str_replace($url, $this->buildSiteUrl($url), $content);
                }
            }
        }
        $content = str_replace("\r\n", "\n", $content);
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'text';
        $response['Content'] = htmlspecialchars_decode($content);
        preg_match_all('/\[U\+(\\w{4,})\]/i', $response['Content'], $matchArray);
        if(!empty($matchArray[1])) {
            foreach ($matchArray[1] as $emojiUSB) {
                $response['Content'] = str_ireplace("[U+{$emojiUSB}]", utf8_bytes(hexdec($emojiUSB)), $response['Content']);
            }
        }
        return $response;
    }
    
    protected function respImage($mid) {
        if (empty($mid)) {
            return error(-1, 'Invaild value');
        }
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'image';
        $response['Image']['MediaId'] = $mid;
        return $response;
    }
    
    protected function respVoice($mid) {
        if (empty($mid)) {
            return error(-1, 'Invaild value');
        }
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'voice';
        $response['Voice']['MediaId'] = $mid;
        return $response;
    }
    
    protected function respVideo(array $video) {
        if (empty($video)) {
            return error(-1, 'Invaild value');
        }
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'video';
        $response['Video']['MediaId'] = $video['MediaId'];
        $response['Video']['Title'] = $video['Title'];
        $response['Video']['Description'] = $video['Description'];
        return $response;
    }
    
    protected function respMusic(array $music) {
        if (empty($music)) {
            return error(-1, 'Invaild value');
        }
        global $_W;
        $music = array_change_key_case($music);
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'music';
        $response['Music'] = array(
            'Title' => $music['title'],
            'Description' => $music['description'],
            'MusicUrl' => tomedia($music['musicurl'])
        );
        if (empty($music['hqmusicurl'])) {
            $response['Music']['HQMusicUrl'] = $response['Music']['MusicUrl'];
        } else {
            $response['Music']['HQMusicUrl'] = tomedia($music['hqmusicurl']);
        }
        if($music['thumb']) {
            $response['Music']['ThumbMediaId'] = $music['thumb'];
        }
        return $response;
    }
    
    protected function respNews(array $news) {
        if (empty($news) || count($news) > 10) {
            return error(-1, 'Invaild value');
        }
        $news = array_change_key_case($news);
        if (!empty($news['title'])) {
            $news = array($news);
        }
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'news';
        $response['ArticleCount'] = count($news);
        $response['Articles'] = array();
        foreach ($news as $row) {
            $response['Articles'][] = array(
                'Title' => $row['title'],
                'Description' => ($response['ArticleCount'] > 1) ? '' : $row['description'],
                'PicUrl' => tomedia($row['picurl']),
                'Url' => $this->buildSiteUrl($row['url']),
                'TagName' => 'item'
            );
        }
        return $response;
    }

    
    protected function respCustom(array $message = array()) {
        $response = array();
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'transfer_customer_service';
        if (!empty($message['TransInfo']['KfAccount'])) {
            $response['TransInfo']['KfAccount'] = $message['TransInfo']['KfAccount'];
        }
        return $response;
    }

}


