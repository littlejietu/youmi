<?php
/**
 * 分销邀请service
 * @date: 2016年3月17日 上午11:19:06
 * @author: hbb
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class invite_service
{
    const LEVEL = 10;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('pmt/Invite_model');
        $this->ci->load->model('user/User_model');
    }

    /**
     * 注册后绑定邀请人关系
     * @date: 2016年3月25日 下午1:13:18
     * @author: hbb
     * @param: int $user_id
     * @param: int $invite_id
     * @return:
     */
    public function add_invites_record($user_id, $invite_id = 0)
    {
        $user_id = (int)$user_id;

        if ($this->ci->Invite_model->get_by_id($user_id)) {
            return false;
        }

        $data['user_id'] = $user_id;

        if ($invite_id) {
            $invite_relation_info = $this->ci->Invite_model->get_by_id($invite_id);
            if (!empty($invite_relation_info)) {
                $data['parent_id_1'] = $invite_relation_info['user_id'];
                for ($i = 1; $i < self::LEVEL; $i++) {
                    $data['parent_id_' . ($i + 1)] = $invite_relation_info['parent_id_' . $i];
                }
            } else
                $data['parent_id_1'] = $invite_id;
        }

        $data['addtime'] = time();

        if ($user_id) {
           return $this->ci->Invite_model->add_invites_record($data,$invite_id);
        } else {
            return false;
        }

    }




    /**
     * 获取邀请排行
     * @date: 2016年3月24日 下午6:52:18
     * @author: hbb
     * @param: variable
     * @return:
     */
    public function get_ranking()
    {
        $num_desc_list = $this->ci->Invite_model->get_invite_num_list();

        $arrUserid = array_column($num_desc_list, 'user_id');

        $user_list = $this->ci->User_model->get_list(array('user_id' => $arrUserid), 'user_id,user_name,logo');
        $user_list = array_combine(array_column($user_list, 'user_id'), $user_list);

        return array_map(function ($v) use ($user_list) {
            if (!empty($user_list[$v['user_id']])) {
                $v['user_name'] = substr_replace($user_list[$v['user_id']]['user_name'], '****', 2, -2);
                if (strpos('http://', $user_list[$v['user_id']]['logo']) === 0) {
                    $v['avatar'] = $user_list[$v['user_id']]['logo'];
                } else {
                    $v['avatar'] = $user_list[$v['user_id']]['logo'] ? BASE_SITE_URL . '/' . $user_list[$v['user_id']]['logo'] : '';
                }
            } else {
                $v['user_name'] = '';
                $v['avatar'] = '';
            }
            return $v;
        }, $num_desc_list);

    }

    /**
     * 获取我的一级邀请用户列表
     * @date: 2016年3月25日 下午6:20:40
     * @author: hbb
     * @param: variable
     * @return:
     */
    public function get_my_list($user_id, $page = 1, $pagesize = 10)
    {
        $data = array();

        $my_list = $this->ci->Invite_model->fetch_page($page, $pagesize, array('parent_id_1' => $user_id), 'user_id,addtime', 'addtime desc');
        $data['num'] = $my_list['count'];
        $data['list'] = array();
        if (!empty($my_list['rows'])) {
            $arrUserid = array_column($my_list['rows'], 'user_id');
            $user_list = $this->ci->User_model->get_list(array('user_id' => $arrUserid), 'user_id,user_name,logo');
            if (!empty($user_list)) {
                $user_list = array_combine(array_column($user_list, 'user_id'), $user_list);
                $data['list'] = array_map(function ($v) use ($user_list) {
                    if (!empty($user_list[$v['user_id']])) {
                        $v['user_name'] = substr_replace($user_list[$v['user_id']]['user_name'], '****', 2, -2);
                        $v['avatar'] = $user_list[$v['user_id']]['logo'] ? $user_list[$v['user_id']]['logo'] : '';
                    } else {
                        $v['user_name'] = '';
                        $v['avatar'] = '';
                    }
                    $v['date'] = date('Y-m-d', $v['addtime']);
                    unset($v['addtime']);
                    return $v;
                }, $my_list['rows']);
            }
        }

        return $data;

    }

}