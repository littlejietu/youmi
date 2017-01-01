<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_class extends ApiController {

    const  TMP_PAGE_TOTAL =10;

	public function __construct()
    {
        parent::__construct();
        $this->load->model('First_category_model');
        $this->load->model('Category_model');
    }
	
	public function xxxxindex()
	{
	    $are_id=empty($_REQUEST['are_id'])?0:(int)$_REQUEST['are_id'];
	    $data=array(
	        'class_list'=>array(
	            array(
	                'id'=>'',
	                'name'=>'热门推荐',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/recommend2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/recommend@2x.png',
	                'type'=>0,
	            ),
	            array(
	                'id'=>1,
	                'name'=>'进口食品',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/food2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/food@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>2,
	                'name'=>'食品生鲜',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/fresh2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/fresh@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>3,
	                'name'=>'酒水饮料',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/drink2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/drink@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>4,
	                'name'=>'厨卫清洁',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/clean2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/clean@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>5,
	                'name'=>'母婴玩具',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/infant-&-mom2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/infant-&-mom@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>6,
	                'name'=>'家居家纺',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/furniture2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/furniture@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>7,
	                'name'=>'美容护理',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/nurse2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/nurse@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>8,
	                'name'=>'流行服饰',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/drees2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/drees@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>9,
	                'name'=>'箱包珠宝',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/bag2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/bag@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>10,
	                'name'=>'鞋靴运动',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/shoes2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/shoes@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>11,
	                'name'=>'家用电器',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/electrical2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/electrical@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>12,
	                'name'=>'电脑办公',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/computer2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/computer@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>13,
	                'name'=>'手机数码',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/figure2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/figure@2x.png',
	                'type'=>1,
	            ),
	            array(
	                'id'=>14,
	                'name'=>'医疗保健',
	                'icon_touch'=>UPLOAD_SITE_URL.'/img/9street/class/medicine2@2x.png',
	                'icon_untouch'=>UPLOAD_SITE_URL.'/img/9street/class/medicine@2x.png',
	                'type'=>1,
	            )
	        ) 
	    );
	    output_data($data);
	}
    
    
    public function index()
    {
        $result = array('class_list' => array());
        $where['status'] = 1;
        $orderby = 'sort desc';
        $data = $this->First_category_model->get_list($where,'name,icon_touch,icon_untouch,category_id',$orderby);
        $cate_list = $this->Category_model->getListByParentId(0);
        foreach ($data as $key => $value) {
			if($key ==0){
				$data[$key]['type'] = 0;
			}else{
				$data[$key]['type'] = 1;
			}
            $data[$key]['icon_touch'] = BASE_SITE_URL.'/'.$data[$key]['icon_touch'];
			$data[$key]['icon_untouch'] = BASE_SITE_URL.'/'.$data[$key]['icon_untouch'];
        }
        $result['class_list'] = $data;
        output_data($result);
    }
    
    
    public function more()
    {
        $this->load->service('first_service');
        $type = empty($_REQUEST['type'])?0:(int)$_REQUEST['type'];
        $parent_id = $this->input->post('category_id');
        $data = array();
        $result = array('banner_list' => array(),'class_list'=>array());
        if ($type == 0)
        {
            $banner = $this->first_service->get_first_by_place(14,5,'pic as pic_url,url as to_url');
            if (!empty($banner))
            {
                $data['banner_list'] = $banner;
            }
            
            $child = $this->first_service->get_first_by_place(15,9,'id,title as name,url as to_url');
            if (!empty($child))
            {
                $data['class_list'][] = array(
                    'id' => 15,
                    'name' => '常用分类',
                    'tag_color' => '#f89067',
                    'child' => $child,
                );

            }
            
            $child = $this->first_service->get_first_by_place(16,9,'id,title as name,url as to_url');
            if (!empty($child))
            {
                $data['class_list'][] = array(
                    'id' => 16,
                    'name' => '猜你喜欢',
                    'tag_color' => '#2db3e5',
                    'child' => $child,
                );
            }
            
            $child = $this->first_service->get_first_by_place(17,9,'id,title as name,url as to_url');
            if (!empty($child))
            {
                $data['class_list'][] = array(
                    'id' => 16,
                    'name' => '为你推荐',
                    'tag_color' => '#cd94d8',
                    'child' => $child,
                );
            }
            output_data($data);exit;
        }
        else 
        {
            $banner = $this->first_service->get_first_by_place(14,5,'pic as pic_url,url as to_url');
            if (!empty($banner))
            {
                $result['banner_list'] = $banner;
            }
            if (empty($parent_id))
            {
                output_error(-1,'该分类不存在');
            }
            $where['parent_id'] = $parent_id;
            
            $data = $this->Category_model->get_list($where,'id,name','sort desc');
			$colorArr = array(
					'#f89067',
					'#2db3e5',
					'#cd94d8',
			);

            if (!empty($data)) {

                foreach ($data as $key => $value) {
				 	$num =fmod($key,COUNT($colorArr));
					$data[$key]['tag_color'] = $colorArr[$num];
                    $where['parent_id'] = $value['id'];
                    $data[$key]['child'] = $this->Category_model->get_list($where,'id,name','sort desc');

					foreach($data[$key]['child'] as $k => $v){
						$data[$key]['child'][$k]['to_url'] = 'zooer://search?keyword='.$v['name'].'&category_id='.$v['id'].'';
					}

                }
                $result['class_list'] = $data;
            }
            output_data($result);
        }
    }
	
	public function xxxmore()
	{
	    $type = empty($_REQUEST['type'])?0:(int)$_REQUEST['type'];
	    if($type==0){
	        $data=array(
	            'banner_list'=>array(
	                array(
	                    'pic_url' => UPLOAD_SITE_URL.'/img/9street/paper1.png',
	                    'to_url' => ''
	                ),
	                array(
	                    'pic_url' => UPLOAD_SITE_URL.'/img/9street/paper2.png',
	                    'to_url' => ''
	                ),
	            ),
	            'class_list'=>array(
    	           array(
    	                'id'=>'',
    	                'name'=>'常用分类',
    	                'tag_color'=>'#f89067',//'#FF3030',
    	                'child'=>array(
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    )
    	                ),
    	            ),
    	            array(
    	                'id'=>'',
    	                'name'=>'猜你喜欢',
    	                'tag_color'=>'#2db3e5',//'#00BFFF',
    	                'child'=>array(
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    )
    	                ),
    	            ),
    	            array(
    	                'id'=>'',
    	                'name'=>'为你推荐',
    	                'tag_color'=>'#cd94d8',//'#EEC900',
    	                'child'=>array(
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>111,
    	                        'name'=>'进口奶粉',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>112,
    	                        'name'=>'进口食品',
    	                        'to_url'=>''
    	                    ),
    	                    array(
    	                        'id'=>113,
    	                        'name'=>'休闲零食',
    	                        'to_url'=>''
    	                    )
    	                )
    	            )
    	        )
	      );
	    }else{
	        $clsid = empty($_REQUEST['clsid'])?0:(int)$_REQUEST['clsid'];
	        $data=array(
	            'banner_list'=>array(
	                array(
	                    'pic_url' => UPLOAD_SITE_URL.'/img/9street/paper3.png',
	                    'to_url' => ''
	                ),
	                array(
	                    'pic_url' => UPLOAD_SITE_URL.'/img/9street/paper4.png',
	                    'to_url' => ''
	                ),
	            ),
	            'class_list'=>array(
	                array(
	                    'id'=>11,
	                    'name'=>'进口牛奶',
// 	                    "tag_color"=>"#00BFFF",
	                    'tag_color'=>'#f89067',
	                    'child'=>array(
	                        array(
	                            'id'=>111,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>112,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>113,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>114,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>115,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>116,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>117,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>118,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>119,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        )
	                    ),
	                ),
	                array(
	                    'id'=>12,
	                    'name'=>'进口饼干/糕点',
// 	                    'tag_color'=>'#EEC900',
	                    'tag_color'=>'#2db3e5',
	                    'child'=>array(
	                        array(
	                            'id'=>111,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>112,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>113,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>114,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>115,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>116,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>117,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>118,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>119,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        )
	                    ),
	                ),
	                array(
	                    'id'=>13,
	                    'name'=>'进口坚果',
// 	                    'tag_color'=>'#BF3EFF',
	                    'tag_color'=>'#cd94d8',
	                    'child'=>array(
	                        array(
	                            'id'=>111,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>112,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>113,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>114,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>115,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>116,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>117,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>118,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>119,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        )
	                    ),
	                ),
	                array(
	                    'id'=>13,
	                    'name'=>'进口饼干/糕点',
// 	                    'tag_color'=>'#FF3030',
                        'tag_color'=>'#a1db11',
	                    'child'=>array(
	                        array(
	                            'id'=>111,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''     
	                        ),
	                        array(
	                            'id'=>112,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>113,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>114,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>115,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>116,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>117,
	                            'name'=>'进口奶粉',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>118,
	                            'name'=>'进口食品',
	                            'to_url'=>''
	                        ),
	                        array(
	                            'id'=>119,
	                            'name'=>'休闲零食',
	                            'to_url'=>''
	                        )
	                    ),
	                )
	            )
	        );
	    }
	    output_data($data);
	}
	
}
