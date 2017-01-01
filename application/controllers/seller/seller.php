<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class seller extends BaseSellerController
{

    function __construct()
    {
        parent::__construct();
    }

    /**修改密码*/
    public function modifypw() {

        $this->load->model('User_pwd_model');


        if($this->input->is_post()){

            $user_id = $this->loginUser['user_id'];
            $old_pw = $this->input->post('old_pw');
            $new_pw = $this->input->post('new_pw');
            $new_pw2 = $this->input->post('new_pw2');

            $config = array(
                array(
                    'field'   => 'old_pw',
                    'label'   => '原始密码不能为空！',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'new_pw',
                    'label'   => '新密码不能为空！',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'old_pw',
                    'label'   => '二次密码不能为空！',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
            {
                $userInfo = $this->User_pwd_model->get_by_id($user_id,'pwd');

                $old_pw = md5(trim($old_pw));
                $new_pw = md5(trim($new_pw));
                $new_pw2 = md5(trim($new_pw2));
                if(!empty($userInfo) && $userInfo['pwd']== $old_pw) {
                    if($new_pw==$new_pw2){
                        $this->User_pwd_model->update_by_id($user_id,array('pwd' => $new_pw2));

                        showMessage('密码修改成功！','/seller/login/logout');

                    }else{
                        showMessage('二次密码不一致！','/seller/seller/modifypw');
                    }
                }else
                {
                    showMessage('原始密码错误i！','/seller/seller/modifypw');
                }
            }
        }

        $result = array(
            'output'=>array(
                'loginUser'=>$this->loginUser,
            ),
        );
        $this->load->view('seller/modifypw',$result);
    }
}