<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invite_model extends XT_Model {

    protected $mTable = 'promote_invite';
    protected $mPkId = 'user_id';
    
    
    /**
    * 函数用途描述
    * @date: 2016年3月25日 下午4:27:04
    * @author: hbb
    * @param: $user_id
    * @return:
    */
    public function get_invite_num_list(){
        $this->mTable='user_num';
        return $this->get_list(array(), 'user_id,invite_num', 'invite_num desc,user_id asc', 10);
    }

    /**
     * 更新用户邀请数统计(一级邀请)
     * @date: 2016年3月25日 下午2:28:51
     * @author: hbb
     * @param:  int $invite_id 邀请人ID
     */
    public function update_user_stat($invite_id)
    {
        if ($invite_id) {
            $sql = "update " . $this->prefix() . "user_num a set invite_num=(select count(1) from x_promote_invite where parent_id_1=a.user_id) where user_id=$invite_id";
            $this->execute($sql);
        }
    }

    /**
     * 添加分佣关系
     */
    public function add_invites_record($data,$invite_id){
        $this->db->trans_begin();
        $this->insert_string($data);
        if($this->affected_rows()){
            $this->update_user_stat($invite_id);
            if($this->affected_rows()){
                $this->db->trans_commit();
                return true;
            }
        }
        $this->db->trans_rollback();
        return false;
    }
}
