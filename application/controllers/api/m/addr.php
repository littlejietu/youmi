<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Addr extends TokenApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_address_model');
        $this->load->service('user_service');
        $this->load->model('Area_model');
    }
    
    /**
     * @param 我的收货地址列表
     * @param $_POST['token']
     * 
     * @return 
     */
    public function addr_list()
    {
        $user = $this->loginUser;
        $userid = $user['user_id'];
        $areas = $this->Area_model->getAreas();
        $config = array(
            array(
                'field'=>'token',
                'label'=>'token',
                'rules'=>'trim|required',
            ),
        );
        
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() === TRUE)
        {
            $where['status'] = 1;
            $where['userid'] = $userid;
            $orderby = 'is_default DESC, id DESC';
            $arrAddr = $this->User_address_model->get_list($where,'*',$orderby);
            foreach ($arrAddr as $key => $value)
            {
                $arrAddr[$key]['province_name'] = $areas['name'][$value['province_id']];
                $arrAddr[$key]['city_name'] = !empty($value['city_id'])?$areas['name'][$value['city_id']]:$value['city_name'];
                $arrAddr[$key]['area_name'] = !empty($value['area_id'])?$areas['name'][$value['area_id']]:$value['area_name'];
            }
            if (empty($arrAddr))
            {
                output_data();exit;
            }
            else 
            {
                $addr_list['data'] = $arrAddr;
                output_data($addr_list);exit;
            }
        }
        else 
        {
            if (empty($token))
            {
                output_error(-1,'USER_TOKEN_NULL');exit;
            }
        }  
    }
    
    /**
     * @param 新增/修改收货地址
     * 
     * @param $_POST['id']
     * @param $_POST['token']
     * @param $_POST['real_name']
     * @param $_POST['province_id']
     * @param $_POST['city_id']
     * @param $_POST['area_id']
     * @param $_POST['address']
     * @param $_POST['mobile']
     * @param $_POST['zip_code']
     * 
     * @return 
     */
    public function wap_add()
    {
        $id = $this->input->post('id');
        $real_name = $this->input->post('real_name');
        $province_id = $this->input->post('province_id');
        $city_id = $this->input->post('city_id');
        $area_id = $this->input->post('area_id');
        $address = $this->input->post('address');
        $mobile = $this->input->post('mobile');
        $zip_code = $this->input->post('zip_code');
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $config = array(
            array(
                'field'=>'real_name',
                'label'=>'real_name',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'province_id',
                'label'=>'province_id',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'city_id',
                'label'=>'city_id',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'area_id',
                'label'=>'area_id',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'address',
                'label'=>'address',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'mobile',
                'label'=>'mobile',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'zip_code',
                'label'=>'zip_code',
                'rules'=>'trim|required',
            ),
        );
        
        $this->form_validation->set_rules($config);
        
        if ($this->form_validation->run() === TRUE)
        {
            if (!preg_match("/^1[34578]\d{9}$/",$mobile))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_PHONE_FORMAT_ERROR';
                output_error(-1,'手机号码格式不正确');exit;
            }
            if (!preg_match("/^[1-9]\d{5}$/",$zip_code))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_ZIP_CODE_ERROR';
                output_error(-1,'邮编格式不正确');exit;
            }
            else 
            {
                $where['status <>'] = -1;
                $where['userid'] = $user_id;
                $addr = $this->User_address_model->get_list($where);
                if (empty($addr))
                {
                    $is_default = 1;
                }
                else 
                {
                    $is_default = 0;
                }
                $data = array(
                    'userid' => $user_id,
                    'real_name' => $real_name,
                    'province_id' => $province_id,
                    'city_id' =>$city_id,
                    'area_id' => $area_id,
                    'address' => $address,
                    'mobile' => $mobile,
                    'zip_code' => $zip_code,
                    'is_default' => $is_default,
                    'status' => 1,
                );
                if ($id)
                {
                    $arrRes['action'] = 'my_addr_edit';
                    if ( $this->User_address_model->update_by_id($id,$data))
                    {
                        $arrRes['code'] = 1;
                        $arrRes['msg'] = 'SUCCESS';
                        output_data();exit;
                    }
                    else
                    {
                        $arrRes['code'] = -1;
                        $arrRes['msg'] = 'FAILED';
                        output_error(-1,'添加失败');exit;
                    }
                }
                $arrRes['action'] = 'my_addr_add';
                if ($this->User_address_model->insert($data))
                {
                    $arrRes['code'] = 1;
                    $arrRes['msg'] = 'SUCCESS';
                    output_data();exit;
                }
                else 
                {
                    $arrRes['code'] = -1;
                    $arrRes['msg'] = 'FAILED';
                    output_error(-1,'添加失败');exit;
                }
                
            }
        }
        else
        {
            if (empty($real_name))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_REAL_NAME_NULL';
                output_error(-1,'请输入真实姓名');exit;
            }
            if (empty($mobile))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_PHONE_NULL';
                output_error(-1,'请输入手机号码');exit;
            }
            if (empty($province_id) || empty($city_id) || empty($area_id) || empty($address))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_ADDRESS_ERROR';
                output_error(-1,'请输入收货地址');exit;
            }
            if (empty($zip_code))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_ZIP_CODE_NULL';
                output_error(-1,'请输入邮编');exit;
            }
        }
    }
    
    /**
     * @param 删除收货地址
     * 
     * @param $_POST['id']
     * @param $_POST['token']
     * 
     * @return 
     */
    public function del()
    {
        $id = $this->input->post('id');
        $token = $this->input->post('token');
        $user = $this->loginUser;
        $userid = $user['user_id'];
        $arrRes = array('data'=>new \stdClass,'code' =>'','msg'=>'','action' =>'my_addr_del');
        $where['id'] = $id;
        $where['userid'] = $userid;
        $arrAddr = $this->User_address_model->get_by_where($where);
        if (empty($arrAddr))
        {
            output_error(-1,'收货地址不存在');
        }
        if ($arrAddr['is_default'] == 1)
        {
            $data['is_default'] = 0;
        }
        $data['status'] = -1; 
        $this->User_address_model->update_by_where($where,$data);
        $mark = $this->User_address_model->db->affected_rows();
        unset($where);
        $where['status'] = 1;
        $where['userid'] = $userid;
        $arrAddr = $this->User_address_model->get_list($where,'*','id DESC');
        if (!empty($arrAddr))
        {
            $id = $arrAddr[0]['id'];
            $this->User_address_model->update_by_id($id,$data = array('is_default' => 1));
        }
        if ($mark == 1)
        {
            $arrRes['code'] = 1;
            $arrRes['msg'] = 'SUCCESS';
            output_data();exit;
        }
        else 
        {
            $arrRes['code'] = -1;
            $arrRes['msg'] = 'FAILED';
            output_error(-1,'删除失败');exit;
        }
        
    }
    
    /**
     * @param 设置默认收货地址
     * 
     * @param $_POST['id']
     * @param $_POST['token']
     * 
     * @return 
     */
    public function is_default()
    {
        $id = $this->input->post('id');
        $user = $this->loginUser;
        $userid = $user['user_id'];
        $arrRes = array('data'=>new \stdClass,'code' =>'','msg'=>'','action' =>'my_addr_isdefault');
        $where['userid'] = $userid;
        $where['is_default'] = 1;
        $data['is_default'] = 0;
        $this->User_address_model->update_by_where($where,$data);
        $data['is_default'] = 1;
        $where2['userid'] = $userid;
        $where2['id'] = $id;
        if ($this->User_address_model->update_by_where($where2,$data))
        {
            $arrRes['code'] = 1;
            $arrRes['msg'] = 'SUCCESS';
            output_data();exit;
        }
        else
        {
            $arrRes['code'] = -1;
            $arrRes['msg'] = 'FAILED';
            output_error(-1,'设置失败');exit;
        }
    }
    
    public function add()
    {

        $id = $this->input->post('id');
        $real_name = $this->input->post('real_name');
        $province_name = $this->input->post('province_name');
        $city_name = $this->input->post('city_name');
        $area_name = $this->input->post('area_name');
        $address = $this->input->post('address');
        $mobile = $this->input->post('mobile');
        $zip_code = $this->input->post('zip_code');
        
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $arrRes = array('data'=>new \stdClass,'code' =>'','msg'=>'');
        $config = array(
            array(
                'field'=>'real_name',
                'label'=>'real_name',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'province_name',
                'label'=>'city_name',
                'rules'=>'trim|required',
            ),
//             array(
//                 'field'=>'city_name',
//                 'label'=>'city_name',
//                 'rules'=>'trim|required',
//             ),
//             array(
//                 'field'=>'area_name',
//                 'label'=>'area_name',
//                 'rules'=>'trim|required',
//             ),
            array(
                'field'=>'mobile',
                'label'=>'mobile',
                'rules'=>'trim|required',
            ),
            // array(
            //     'field'=>'zip_code',
            //     'label'=>'zip_code',
            //     'rules'=>'trim|required',
            // ),
        );
        
        $this->form_validation->set_rules($config);
        
        if ($this->form_validation->run() === TRUE)
        {
            $arrWhere = array('parent_id'=>0,'name like'=>"'%$province_name%'");
            $province = $this->Area_model->get_by_where($arrWhere);

            $arrWhere = array('parent_id'=>$province['id'],'name like'=>"'%$city_name%'");
            $city = $this->Area_model->get_by_where($arrWhere);
            
            $arrWhere = array('parent_id'=>$city['id'],'name like'=>"'%$area_name%'");
            $area = $this->Area_model->get_by_where($arrWhere);
            if (empty($province))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_ADDRESS_ERROR';
                output_error(-1,'请填写正确的收货地址');exit;
            }
            
            if (!preg_match("/^1[34578]\d{9}$/",$mobile))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_PHONE_FORMAT_ERROR';
                output_error(-1,'手机号错误');
            }
            
            $data = array(
                'userid' => $user_id,
                'real_name' => $real_name,
                'province_id' => $province['id'],
                'province_name' => $province_name,
                'city_id' =>$city['id'],
                'city_name' => $city_name,
                'area_id' => $area['id'],
                'area_name' => $area_name,
                'address' => $address,
                'mobile' => $mobile,
                'zip_code' => $zip_code,
                'status' => 1,
            );
            if ($id)
            {
                $arrRes['action'] = 'my_addr_edit';
                if ( $this->User_address_model->update_by_id($id,$data) )
                {
                    $arrRes['code'] = 1;
                    $arrRes['msg'] = 'SUCCESS';
                    output_data();exit;
                }
                else
                {
                    $arrRes['code'] = -1;
                    $arrRes['msg'] = 'FAILED';
                    output_error(-1,'添加失败');exit;
                }
            }
            unset($where);
            $where['status <>'] = -1;
            $where['userid'] = $user_id;
            $addr = $this->User_address_model->get_list($where);
            if (empty($addr))
            {
                $data['is_default'] = 1;
            }
            else 
            {
                $data['is_default'] = 0;
            }
            $arrRes['action'] = 'my_addr_add';
            if ($insert_id = $this->User_address_model->insert_string($data))
            {
                $arrReturn = $this->User_address_model->get_by_id($insert_id);
                $areas = $this->Area_model->getAreas();
                $arrReturn['province_name'] = $areas['name'][$arrReturn['province_id']];
                $arrReturn['city_name'] = !empty($city)?$areas['name'][$arrReturn['city_id']]:'';
                $arrReturn['area_name'] = !empty($area)?$areas['name'][$arrReturn['area_id']]:'';
                $arrRes['code'] = 1;
                $arrRes['msg'] = 'SUCCESS';
                output_data($arrReturn);exit;
            }
            else
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'FAILED';
                output_error(-1,'添加失败');exit;
            }
        
            
        }
        else
        {
            if (empty($real_name))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_REAL_NAME_NULL';
                output_error(-1,'姓名不能为空');exit;
            }
            if (empty($mobile))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_PHONE_NULL';
                output_error(-1,'手机号不能为空');exit;
            }
            if (empty($province_name) || empty($city_name) || empty($area_name))
            {
                $arrRes['code'] = -1;
                $arrRes['msg'] = 'USER_ADDRESS_ERROR';
                output_error(-1,'地址不能为空');exit;
            }
            // if (empty($zip_code))
            // {
            //     $arrRes['code'] = -1;
            //     $arrRes['msg'] = 'USER_ZIP_CODE_NULL';
            //     output_error(-1,'USER_ZIP_CODE_NULL');exit;
            // }
        }
    }

}
