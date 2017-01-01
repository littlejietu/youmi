<script type="text/javascript">
$(document).ready(function(){
    //添加删除快捷操作
    $('[nctype="btn_add_quicklink"]').on('click', function() {
        var $quicklink_item = $(this).parent();
        var item = $(this).attr('data-quicklink-act');
        if($quicklink_item.hasClass('selected')) {
            $.post("http://www.shopnc1.com/shop/index.php?act=seller_center&op=quicklink_del", { item: item }, function(data) {
                $quicklink_item.removeClass('selected');
                $('#quicklink_' + item).remove();
            }, "json");
        } else {
            var count = $('#quicklink_list').find('dd.selected').length;
            if(count >= 8) {
                showError('快捷操作最多添加8个');
            } else {
                $.post("http://www.shopnc1.com/shop/index.php?act=seller_center&op=quicklink_add", { item: item }, function(data) {
                    $quicklink_item.addClass('selected');
                                            var $link = $quicklink_item.find('a');
                        var menu_name = $link.text();
                        var menu_link = $link.attr('href');
                        var menu_item = '<li id="quicklink_' + item + '"><a href="' + menu_link + '">' + menu_name + '</a></li>';
                        $(menu_item).appendTo('#seller_center_left_menu').hide().fadeIn();
                                    }, "json");
            }
        }
    });
    //浮动导航  waypoints.js
    $("#sidebar,#mainContent").waypoint(function(event, direction) {
        $(this).parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
        });
    });
    // 搜索商品不能为空
    $('input[nctype="search_submit"]').click(function(){
        if ($('input[nctype="search_text"]').val() == '') {
            return false;
        }
    });
</script>
<div id="faq">
  <div class="faq-wrapper">
      </div>
</div>
<div id="footer" class="wrapper">
    <p>Copyright 2016 启翔</p>
  <!-- <p><a href="http://www.shopnc1.com/shop">首页</a>
| <a  href="/shop/index.php?act=article&amp;article_id=24">招聘英才</a>
| <a  href="/shop/index.php?act=article&amp;article_id=25">合作及洽谈</a>
| <a  href="/shop/index.php?act=article&amp;article_id=23">联系我们</a>
| <a  href="/shop/index.php?act=article&amp;article_id=22">关于我们</a>
| <a  href="/shop/index.php?act=link">友情链接</a>
                              </p>
  Copyright 2015</a> 蜀ICP备13037466号<br /> -->
<script type="text/javascript">//var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_2076168'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/stat.php%3Fid%3D2076168' type='text/javascript'%3E%3C/script%3E"));</script> </div>
<!-- 对比 -->
<script type="text/javascript">
$(function(){
	// Membership card
	$('[nctype="mcard"]').membershipCard({type:'shop'});
});
</script>
