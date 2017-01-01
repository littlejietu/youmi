<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 取得商品缩略图的完整URL路径，接收商品信息数组，返回所需的商品缩略图的完整URL
 *
 * @param array $goods 商品信息数组
 * @param string $type 缩略图类型  值为60,240,360,1280
 * @return string
 */
function thumb($goods = array(), $type = ''){
    $type_array = explode(',_', ltrim(GOODS_IMAGES_EXT, '_'));
    if (!in_array($type, $type_array)) {
        $type = '240';
    }
    if (empty($goods)){
        return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    }
    if (array_key_exists('pic', $goods)) {
        $goods['goods_image'] = $goods['pic'];
    }
    if (array_key_exists('pic_path', $goods)) {
        $goods['goods_image'] = $goods['pic_path'];
    }
    if (empty($goods['goods_image'])) {
        return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    }
    $search_array = explode(',', GOODS_IMAGES_EXT);
    $file = str_ireplace($search_array,'',$goods['goods_image']);
    $fname = basename($file);
    //取店铺ID
    if (preg_match('/^(\d+_)/',$fname) ||preg_match('/^(-\d+_)/',$fname)){
        $shop_id = substr($fname,0,strpos($fname,'_'));
    }else{
        if(!empty($goods['shop_id']))
            $shop_id = $goods['shop_id'];
        else
            $shop_id = -1;
    }
    $file = $type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file);
    if (!file_exists(BASE_UPLOAD_PATH.'/'.ATTACH_GOODS.'/'.$shop_id.'/'.$file)){
        return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    }
    $thumb_host = UPLOAD_SITE_URL.'/'.ATTACH_GOODS;
    return $thumb_host.'/'.$shop_id.'/'.$file;

}
/**
 * 取得商品缩略图的完整URL路径，接收图片名称与店铺ID
 *
 * @param string $file 图片名称
 * @param string $type 缩略图尺寸类型，值为60,240,360,1280
 * @param mixed $shop_id 店铺ID 如果传入，则返回图片完整URL,如果为假，返回系统默认图
 * @return string
 */
function cthumb($file, $type = '', $shop_id = false) {

    $type_array = explode(',_', ltrim(GOODS_IMAGES_EXT, '_'));
    if (!in_array($type, $type_array)) {
        $type = '240';
    }
    if (empty($file)) {
        return UPLOAD_SITE_URL . '/' . defaultGoodsImage ( $type );
    }
    $search_array = explode(',', GOODS_IMAGES_EXT);
    $file = str_ireplace($search_array,'',$file);
    $fname = basename($file);
    // 取店铺ID
    if ($shop_id === false || !is_numeric($shop_id)) {
        $shop_id = substr ( $fname, 0, strpos ( $fname, '_' ) );
    }else{
        if (preg_match('/^(\d+_)/',$fname) ||preg_match('/^(-\d+_)/',$fname))
            $shop_id = substr($fname,0,strpos($fname,'_'));
        else
            $shop_id = 0;
    }
    // 本地存储时，增加判断文件是否存在，用默认图代替
    //if ( !file_exists(BASE_UPLOAD_PATH . '/' . ATTACH_GOODS . '/' . $shop_id . '/' . ($type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file)) )) {
    //    return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    //}
    $thumb_host = UPLOAD_SITE_URL . '/' . ATTACH_GOODS;
    return $thumb_host . '/' . $shop_id . '/' . ($type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file));
}



function doInput($input_type, $name_id, $is_required, $valList, $attr_checked){
    $strReturn = '';
    switch ($input_type) {
        case 1:
            $value = '';
            if(!empty($attr_checked) && !empty($attr_checked['attr_txt'][$name_id]))
                $value = $attr_checked['attr_txt'][$name_id];

            $strReturn = '<input type="text" maxlength="30" id="prop_'.$name_id.'" name="prop_'.$name_id.'" class="text text-short " value="'.$value.'">';
            break;
        case 2:
            $strReturn = '<select name="prop_'.$name_id.'"><option></option>';
            if(!empty($valList))
            {
                foreach ($valList as $key => $value) {
                    $checked = '';
                    if(!empty($attr_checked) && !empty($attr_checked['attr_id']) && in_array($key, $attr_checked['attr_id']))
                        $checked = " selected='selected'";
                    $strReturn .= '<option value="'.$key.'"'.$checked.'>'.$value.'</option>';
                }
            }
            $strReturn .= '</select>';
            break;
        default:
            # code...
            break;
    }

    if($is_required)
        $strReturn .= ' <i class="required">*</i>';

    return $strReturn;
}

function doView($input_type, $name_id, $valList, $attr_checked){
    $strReturn = '';
    switch ($input_type) {
        case 1:
            $value = '';
            if(!empty($attr_checked) && !empty($attr_checked['attr_txt'][$name_id]))
                $value = $attr_checked['attr_txt'][$name_id];

            $strReturn = $value;
            break;
        case 2:
            if(!empty($valList))
            {
                foreach ($valList as $key => $value) {
                    $checked = '';
                    if(!empty($attr_checked) && !empty($attr_checked['attr_id']) && in_array($key, $attr_checked['attr_id']))
                        $strReturn .= $value.' ';
                }
            }
            break;
        default:
            # code...
            break;
    }

    return $strReturn;
}


function is_tpl_follow($attr){
    $aFollowAttr = C('Goods_Tpl.Follow');
    if(in_array($attr, $aFollowAttr))
        return true;
    else
        return false;
}

?>