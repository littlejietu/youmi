/**
 * Created by Administrator on 2016/3/28.
 */
/*
*
* streetNumber: 门牌号码,
* town:
 street: 街道名称,
 district: 区县名称,
 city: 城市名称,
 province: 省份名称,
 country: 国家
* */
function codeLatLng(lat,lng) {
    //获取经纬度数值   按照,分割字符串 取出前两位 解析成浮点数
    var latLng = new qq.maps.LatLng(lat, lng);
    //调用获取位置方法
    //tipsAlert('lat:'+lat+'---lng:'+lng);
    var geocoder = new qq.maps.Geocoder({
        complete:function(result){
            addarr.push(result.detail.addressComponents.province);
            addarr.push(result.detail.addressComponents.city);
            addarr.push(result.detail.addressComponents.district);

            $('#city_address').val(result.detail.addressComponents.province+' '+result.detail.addressComponents.city+' '+result.detail.addressComponents.district);
            $('#detail_address').val(result.detail.addressComponents.street+result.detail.addressComponents.town+result.detail.addressComponents.streetNumber);
         },
        error:function(){
            tipsAlert("出错了，请输入正确的经纬度！！！");
        }
    });
    geocoder.getAddress(latLng);
}
//var geo;
//function codeLatLng(lat,lng) {
//    //获取经纬度数值   按照,分割字符串 取出前两位 解析成浮点数
//    var point = new BMap.Point(lat, lng);
//    var address = "";
//
////坐标转换为地理位置
//    if(!geo){
//        geo = new BMap.Geocoder();
//    }
//    geo.getLocation(point, function(result) {
//        if (result){
//            address = result.address;
//        }
//    });
//}



function getLatlngByAddress(addr){

}

$(function(){
    initWx(['getLocation'],null,location.href);

    $('#complete_btn').click(function(){

    });
    $('#local_pos').click(function(){
        wxGetLocation({
            success:function(res){
                //tipsAlert(JSON.stringify(res))
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                codeLatLng(latitude,longitude);

            },
            cancel:function(res){
                tipsAlert("用户拒绝获得当前位置")
            }
        });
    });
    $('#addr_click').click(function(){
        $('#area').show();
    });
    if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }
});

