<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<header class="ncsc-head-layout w">
    <div class="wrapper">
        <div class="ncsc-admin">
            <dl class="ncsc-admin-info">
                <dt class="admin-avatar"><img src="/res/admin/images/default_user_portrait.gif" width="32"
                                              class="pngFix" alt=""/></dt>
                <dd class="admin-permission">当前用户</dd>
                <dd class="admin-name"><?php echo $output['loginUser']['user_name'] ?></dd>
            </dl>
            <div class="ncsc-admin-function">
                <!-- <a href="" title="前往店铺"><i class="icon-home"></i></a> -->
                <a href="<?php echo SELLER_SITE_URL;?>/seller/modifypw" title="修改密码" ><i class="icon-wrench"></i></a>
                <a href="<?php echo SELLER_SITE_URL;?>/login/logout" title="安全退出"><i class="icon-signout"></i></a>
            </div>
        </div>
        <div class="center-logo"><img src="/res/admin/images/logo.png" class="pngFix" alt=""/>

          <!--   <h1>商家中心</h1> -->
        </div>
        <div class="index-search-container">
            <div class="index-sitemap"><a href="javascript:void(0);">导航管理 <i class="icon-angle-down"></i></a>

                <div class="sitemap-menu-arrow"></div>
                <div class="sitemap-menu">
                    <div class="title-bar">
                        <h2><i class="icon-sitemap"></i>管理导航<em>小提示：添加您经常使用的功能到首页侧边栏，方便操作。</em></h2>
                        <span id="closeSitemap" class="close">X</span></div>
                    <div id="quicklink_list" class="content">
                        <dl>
                            <dt>商品</dt>
                            <!-- <dd><i nctype="btn_add_quicklink" data-quicklink-act="store_goods_add" class="icon-check"
                                   title="添加为常用功能菜单"></i><a href="<?php //echo SELLER_SITE_URL;?>/goods_add"> 商品发布 </a></dd> -->
                            <dd><i nctype="btn_add_quicklink" data-quicklink-act="store_goods_online" class="icon-check"
                                   title="添加为常用功能菜单"></i><a href="<?php echo SELLER_SITE_URL;?>/goods"> 出售中的商品 </a></dd>
                            <!-- <dd><i nctype="btn_add_quicklink" data-quicklink-act="store_goods_offline"
                                   class="icon-check" title="添加为常用功能菜单"></i><a href="<?php //echo SELLER_SITE_URL;?>/goods_add?status=2"> 仓库中的商品 </a></dd> -->
                        </dl>
                        <dl>
                            <dt>订单物流</dt>
                            <dd><i nctype="btn_add_quicklink" data-quicklink-act="store_order" class="icon-check"
                                   title="添加为常用功能菜单"></i><a href="/seller/order"> 交易订单 </a></dd>
                            <dd><i nctype="btn_add_quicklink" data-quicklink-act="store_deliver" class="icon-check"
                                   title="添加为常用功能菜单"></i><a href="/seller/order"> 发货 </a></dd>
                            <dd><!-- <i nctype="btn_add_quicklink" data-quicklink-act="store_waybill" class="icon-check"
                                   title="添加为常用功能菜单"></i><a href="/seller/shop_transport"> 运单模板 </a> --></dd>
                        </dl>

                    </div>
                </div>
            </div>
            <div class="search-bar">
                <form method="get" action="<?php echo SELLER_SITE_URL.'/goods'?>">
                    <input type="text" nctype="search_text" name="keyword" placeholder="商城商品搜索"
                           class="search-input-text">
                    <input type="submit" nctype="search_submit" class="search-input-btn pngFix" value="">
                </form>
            </div>
        </div>
        <?php
          $act = explode('/',$_SERVER['PATH_INFO']);
          $act = !empty($act[2])?$act[2]:'';
        ?>
        <nav class="ncsc-nav">
            <dl class="<?php if(empty($act)) echo 'current' ?>">
                <dt><a href="<?php echo SELLER_SITE_URL;?>">首页</a></dt>
                <dd class="arrow"></dd>
            </dl>
            <dl class="<?php if(in_array($act,array('goods','goods_add'))) echo 'current';?>">
                <dt><a href="<?php echo SELLER_SITE_URL;?>/goods">商品</a></dt>
                <dd>
                    <ul>
                        <!-- <li><a href="<?php //echo SELLER_SITE_URL;?>/goods_add"> 商品发布 </a></li> -->
                        <li><a href="<?php echo SELLER_SITE_URL;?>/goods"> 出售中的商品 </a></li>
                        <!-- <li><a href="<?php //echo SELLER_SITE_URL;?>/goods_add?status=2"> 仓库中的商品 </a></li> -->
                    </ul>
                </dd>
                <dd class="arrow"></dd>
            </dl>
            <dl class="<?php if(in_array($act,array('order','shop_transport'))) echo 'current';?>">
                <dt><a href="<?php echo SELLER_SITE_URL;?>/order">订单物流</a></dt>
                <dd>
                    <ul>
                        <li><a href="<?php echo SELLER_SITE_URL;?>/order"> 实物交易订单 </a></li>
                        <li><a href="<?php echo SELLER_SITE_URL;?>/order?type=2"> 发货 </a></li>
                        <li><a href="<?php echo SELLER_SITE_URL;?>/order?type=6"> 退货 </a></li>
                        <!-- <li><a href="index.php?act=store_deliver_set&op=daddress_list"> 发货设置 </a></li>
                        <li><a href="index.php?act=store_waybill&op=waybill_manage"> 运单模板 </a></li>
                       <li><a href="index.php?act=store_evaluate&op=list"> 评价管理 </a></li>-->
                        <li><!-- <a href="<?php //echo SELLER_SITE_URL;?>/shop_transport"> 物流工具 </a> --></li>
                    </ul>
                </dd>
                <dd class="arrow"></dd>
            </dl>
            
            <dl class="<?php if(in_array($act,array('order','shop_transport'))) echo 'current';?>">
                <dt><a href="<?php echo SELLER_SITE_URL;?>/excel/export_by_shop">销售报表</a></dt>
                <dd>
                    <ul>
                        <li><a href="<?php echo SELLER_SITE_URL;?>/excel/export_by_shop"> 导出销售报表 </a></li>
                        <!-- <li><a href="index.php?act=store_deliver_set&op=daddress_list"> 发货设置 </a></li>
                        <li><a href="index.php?act=store_waybill&op=waybill_manage"> 运单模板 </a></li>
                       <li><a href="index.php?act=store_evaluate&op=list"> 评价管理 </a></li>-->
                        <li><!-- <a href="<?php //echo SELLER_SITE_URL;?>/shop_transport"> 物流工具 </a> --></li>
                    </ul>
                </dd>
                <dd class="arrow"></dd>
            </dl>

            <!-- <dl class="">
        <dt><a href="index.php?act=store_account&op=account_list">账号</a></dt>
        <dd>
          <ul>
                                    <li> <a href="index.php?act=store_account&op=account_list"> 账号列表 </a> </li>
                        <li> <a href="index.php?act=store_account_group&op=group_list"> 账号组 </a> </li>
                        <li> <a href="index.php?act=seller_log&op=log_list"> 账号日志 </a> </li>
                        <li> <a href="index.php?act=store_cost&op=cost_list"> 店铺消费 </a> </li>
                                  </ul>
        </dd>
        <dd class="arrow"></dd>
      </dl> -->
        </nav>
    </div>
</header>