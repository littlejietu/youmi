
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>菜单设计器 - 自定义菜单</title>
	<?php echo _get_html_cssjs('resource_css','bootstrap.min.css,font-awesome.min.css,common.css?v=20161011','css');?>
	<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}
	window.sysinfo = {
		'company_id': '<?php echo $this->seller_info["company_id"];?>',
		<?php if(!empty($info['openid'])):?>'openid': '<?php echo $info['openid'];?>',<?php endif;?>
		<?php if(!empty($info['user_id'])):?>'uid': '<?php echo $info['user_id'];?>',<?php endif;?>
		'siteroot': '<?php echo BASE_SITE_URL;?>',
		'siteurl': '<?php echo BASE_SITE_URL;?>',
		'attachurl': '<?php echo UPLOAD_SITE_URL;?>',
		'attachurl_local': '<?php echo UPLOAD_SITE_URL;?>',
		'attachurl_remote': '',
		'cookie' : {'pre': '_mr'}
		//,
		//<?php if(!empty($info)):?>'account' : <?php echo json_encode($info);?><?php endif;?>
		
	};
	</script>
	<script>var require = { urlArgs: 'v=20161012' };</script>
	<?php echo _get_html_cssjs('resource_js','lib/jquery-1.11.1.min.js,app/util.js,require.js,app/config.js','js');?>
</head>
<body>

	<div class="row" style="border-top: 4px solid #44b549;border-bottom: 1px solid #d9dadc;">
		<div class="col-xs-12 col-sm-9 col-lg-12">
			<div class="navbar navbar-default" style="margin-bottom: 0px;background-color: #FFFFFF;border-color: #FFFFFF;border: 0px solid transparent;">
				<div class="container-fluid">
				<ul class="nav navbar-nav">
				<li><a href="./?refresh"><i class="fa fa-reply-all"></i>返回系统</a></li>
													<li class="active"><a href="./index.php?c=home&a=welcome&do=platform&"><i class="fa fa-cog"></i>基础设置</a></li>									<li><a href="./index.php?c=home&a=welcome&do=site&"><i class="fa fa-life-bouy"></i>微站功能</a></li>									<li><a href="./index.php?c=home&a=welcome&do=mc&"><i class="fa fa-gift"></i>粉丝营销</a></li>									<li><a href="./index.php?c=home&a=welcome&do=setting&"><i class="fa fa-umbrella"></i>功能选项</a></li>									<li><a href="./index.php?c=home&a=welcome&do=ext&"><i class="fa fa-cubes"></i>扩展功能</a></li>									<li><a href="./index.php?c=home&a=welcome&do=members&"><i class="fa fa-cubes"></i>应用商店</a></li>									<li><a href="./index.php?c=home&a=welcome&do=fournet&"><i class="fa fa-cubes"></i>四网融合</a></li>								<li >
					<a href="./index.php?c=utility&a=emulator&" target="_blank"><i class="fa fa-mobile"></i> 模拟测试</a>
				</li>
			    </ul>
				<ul class="nav navbar-nav navbar-right">
				<li class="dropdown topbar-notice">
					<a type="button" data-toggle="dropdown">
						<i class="fa fa-bell"></i>
						<span class="badge" id="notice-total">0</span>
					</a>
					<div class="dropdown-menu" aria-labelledby="dLabel">
						<div class="topbar-notice-panel">
							<div class="topbar-notice-arrow"></div>
							<div class="topbar-notice-head">
								<span>系统公告</span>
								<a href="./index.php?c=article&a=notice-show&do=list&" class="pull-right">更多公告>></a>
							</div>
							<div class="topbar-notice-body">
								<ul id="notice-container"></ul>
							</div>
						</div>
					</div>
				</li>
					<li class="dropdown">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" style="display:block; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; "><i class="fa fa-comments"></i> 米荣科技 <b class="caret"></b></a>
						<ul class="dropdown-menu">
														<li><a href="./index.php?c=account&a=post&uniacid=3" target="_blank"><i class="fa fa-weixin fa-fw"></i> 编辑当前账号资料</a></li>
														<li><a href="./index.php?c=account&a=display&" target="_blank"><i class="fa fa-cogs fa-fw"></i> 管理其它公众号</a></li>
							<li><a href="./index.php?c=utility&a=emulator&" target="_blank"><i class="fa fa-mobile fa-fw"></i> 模拟测试</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" style="display:block; max-width:185px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; "><i class="fa fa-user"></i>  admin (系统管理员) <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="./index.php?c=user&a=profile&do=profile&" target="_blank"><i class="fa fa-weixin fa-fw"></i> 我的账号</a></li>
													<li class="divider"></li>
							<li><a href="./index.php?c=system&a=welcome&" target="_blank"><i class="fa fa-sitemap fa-fw"></i> 系统选项</a></li>
							<li><a href="./index.php?c=system&a=welcome&" target="_blank"><i class="fa fa-cloud-download fa-fw"></i> 自动更新</a></li>
							<li><a href="./index.php?c=system&a=updatecache&" target="_blank"><i class="fa fa-refresh fa-fw"></i> 更新缓存</a></li>
							<li class="divider"></li>
														<li><a href="./index.php?c=user&a=logout&"><i class="fa fa-sign-out fa-fw"></i> 退出系统</a></li>
						</ul>
					</li>
				</ul>
				</div>
			</div>
</div>
</div>
			<div class="container-fluid" style="margin-top: 36px;margin-bottom: 88px;min-height: 700px;max-width: 1400px;">
				<div class="row">
										<div class="col-xs-12 col-sm-3 col-lg-2 big-menu" style="padding-right: 0px;">
					<div id="search-menu">
						<input class="form-control input-lg" style="border-radius:0; font-size:14px; height:43px;" type="text" placeholder="输入菜单名称可快速查找">
					</div>
															<div class="panel panel-default">
						<div class="panel-heading" style='padding: 14px 15px;'>
							<h4 class="panel-title"><i class="fa fa-th-large"></i> 基本功能</h4>
							<a class="panel-collapse collapsed" data-toggle="collapse" href="#frame-0">
								<i class="fa fa-chevron-circle-down"></i>
							</a>
						</div>
						<ul class="list-group collapse in" id="frame-0">
																													<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=basic';" style="cursor:pointer;padding-left: 33px;" kw="文字回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=basic"><i class="fa fa-plus"></i></a>
									文字回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=news';" style="cursor:pointer;padding-left: 33px;" kw="图文回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=news"><i class="fa fa-plus"></i></a>
									图文回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=music';" style="cursor:pointer;padding-left: 33px;" kw="音乐回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=music"><i class="fa fa-plus"></i></a>
									音乐回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=images';" style="cursor:pointer;padding-left: 33px;" kw="图片回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=images"><i class="fa fa-plus"></i></a>
									图片回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=voice';" style="cursor:pointer;padding-left: 33px;" kw="语音回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=voice"><i class="fa fa-plus"></i></a>
									语音回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=video';" style="cursor:pointer;padding-left: 33px;" kw="视频回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=video"><i class="fa fa-plus"></i></a>
									视频回复								</li>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=userapi';" style="cursor:pointer;padding-left: 33px;" kw="自定义接口回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=userapi"><i class="fa fa-plus"></i></a>
									自定义接口回复								</li>
																																														<a class="list-group-item " href="./index.php?c=platform&a=special&do=display&" kw="系统回复" style="padding-left: 33px;">系统回复</a>
																																												<li class="list-group-item " onclick="window.location.href = './index.php?c=platform&a=reply&m=wxcard';" style="cursor:pointer;padding-left: 33px;" kw="微信卡券回复">
									<a class="pull-right" href="./index.php?c=platform&a=reply&do=post&m=wxcard"><i class="fa fa-plus"></i></a>
									微信卡券回复								</li>
																												</ul>
					</div>
										<div class="panel panel-default">
						<div class="panel-heading" style='padding: 14px 15px;'>
							<h4 class="panel-title"><i class="fa fa-th-large"></i> 高级功能</h4>
							<a class="panel-collapse collapsed" data-toggle="collapse" href="#frame-1">
								<i class="fa fa-chevron-circle-down"></i>
							</a>
						</div>
						<ul class="list-group collapse in" id="frame-1">
																															<a class="list-group-item " href="./index.php?c=platform&a=service&do=switch&" kw="常用服务接入" style="padding-left: 33px;">常用服务接入</a>
																																														<a class="list-group-item  active" href="./index.php?c=platform&a=menu&" kw="自定义菜单" style="padding-left: 33px;">自定义菜单</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=special&do=message&" kw="特殊消息回复" style="padding-left: 33px;">特殊消息回复</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=qr&" kw="二维码管理" style="padding-left: 33px;">二维码管理</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=reply&m=custom" kw="多客服接入" style="padding-left: 33px;">多客服接入</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=url2qr&" kw="长链接二维码" style="padding-left: 33px;">长链接二维码</a>
																												</ul>
					</div>
										<div class="panel panel-default">
						<div class="panel-heading" style='padding: 14px 15px;'>
							<h4 class="panel-title"><i class="fa fa-th-large"></i> 数据统计</h4>
							<a class="panel-collapse collapsed" data-toggle="collapse" href="#frame-2">
								<i class="fa fa-chevron-circle-down"></i>
							</a>
						</div>
						<ul class="list-group collapse in" id="frame-2">
																															<a class="list-group-item " href="./index.php?c=platform&a=stat&do=history&" kw="聊天记录" style="padding-left: 33px;">聊天记录</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=stat&do=rule&" kw="回复规则使用情况" style="padding-left: 33px;">回复规则使用情况</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=stat&do=keyword&" kw="关键字命中情况" style="padding-left: 33px;">关键字命中情况</a>
																																														<a class="list-group-item " href="./index.php?c=platform&a=stat&do=setting&" kw="参数" style="padding-left: 33px;">参数</a>
																												</ul>
					</div>
										<script type="text/javascript">
						require(['bootstrap'], function(){
							$('.ext-type').click(function(){
								var id = $(this).data('id');
								util.cookie.del('ext_type');
								util.cookie.set('ext_type', id, 8640000);
								location.reload();
								return false;
							});

							$('#search-menu input').keyup(function() {
								var a = $(this).val();
								$('.big-menu .list-group-item, .big-menu .panel-heading').hide();
								$('.big-menu .list-group-item').each(function() {
									$(this).css('border-left', '0');
									if(a.length > 0 && $(this).attr('kw').indexOf(a) >= 0) {
										$(this).parents(".panel").find('.panel-heading').show();
										$(this).show().css('border-left', '3px #428bca double');
									}
								});
								if(a.length == 0) {
									$('.big-menu .list-group-item, .big-menu .panel-heading').show();
								}
							});
						});
					</script>
				</div>
				<div class="col-xs-12 col-sm-9 col-lg-10">
										<link href="./resource/css/app.css" rel="stylesheet">
<ul class="nav nav-tabs">
	<li class="active"><a href="./index.php?c=platform&a=menu&do=display&status=normal">自定义菜单</a></li>
	<li class=""><a href="./index.php?c=platform&a=menu&do=display&status=history">历史菜单</a></li>
</ul>

<div class="conditionMenu" ng-controller="conditionMenuDesigner" id="conditionMenuDesigner">
	<div class="app clearfix">
		<div class="app-preview">
			<div class="app-header"></div>
			<div class="app-content">
				<div class="inner">
					<div class="title">
						<h1><span>{{context.group.type == 3 ? "个性化菜单" : "默认菜单"}}</span></h1>
					</div>
				</div>
				<div class="nav-menu">
					<div class="js-quickmenu nav-menu-wx clearfix" ng-class="{0 : 'has-nav-0', 1 : 'has-nav-1', 2: 'has-nav-2', 3: 'has-nav-3', 4 : 'has-nav-3'}[context.group.button.length + 1]">
						<ul class="nav-group designer-x">
							<li class="nav-group-item js-sortable" ng-repeat="but in context.group.button" ng-class="{0 : '', 1 : 'active'}[context.activeItem == but ? 1 : 0 ]">
								<input type="hidden" data-role="parent" data-hash="{{but.$$hashKey}}"/>
								<a href="javascript:void(0);" title="拖动排序" ng-click="context.editBut('', but);">
									<i class="fa fa-minus-circle" ng-show="but.sub_button.length > 0"></i>
									{{but.name}}
								</a>
								<dl class="designer-y">
									<dd ng-repeat="subBut in but.sub_button"  ng-class="{0 : '', 1 : 'active'}[context.activeItem == subBut ? 1 : 0 ]">
										<input type="hidden" data-role="sub" data-hash="{{subBut.$$hashKey}}"/>
										<a href="javascript:void(0)" ng-click="context.editBut(subBut, but);">{{subBut.name}}</a>
									</dd>
									<dd ng-if="but.sub_button.length < 5" class="js-not-sortable">
										<a href="javascript:void(0)" ng-click="context.addSubBut(but);"><i class="fa fa-plus"></i></a>
									</dd>
								</dl>
							</li>
							<li class="nav-group-item" class="js-not-sortable"ng-if="context.group.button.length < 3" ng-hide="context.group.disabled">
								<a href="javascript:void(0);" ng-click="context.addBut();" class="text-success">
									<i class="fa fa-plus"></i> 添加菜单
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="app-side">
			<div class="menu app-conditionMenu-edit">
				<div class="arrow-left"></div>
				<div class="inner">
					<div class="panel panel-default">
						<div class="panel-body form-horizontal">
							<div class="conditionMenu-wx">
								<div class="card">
									<div class="nav-region">
										<div class="first-nav">
											<h3>标题</h3>
											<div class="alert">
												<div class="form-group">
													<label class="control-label col-xs-2">标题</label>
													<div class="col-xs-10">
														<input type="text" class="form-control" ng-model="context.group.title" ng-disabled="context.group.disabled"/>
														<span class="help-block">仅用于区分个性化菜单</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
														<div class="card" ng-if="context.group.button.length > 0">
								<div class="btns">
									<a href="javascript:;" ng-click="context.removeBut(context.activeItem, context.activeType)"><i class="fa fa-times"></i></a>
								</div>
								<div class="nav-region">
									<div class="first-nav">
										<h3>菜单设置</h3>
										<div class="alert">
											<div class="form-group">
												<label class="control-label col-xs-2">菜单名称</label>
												<div class="col-xs-10">
													<div class="input-group">
														<input type="text" class="form-control" name="" id="title" ng-model="context.activeItem.name" ng-disabled="context.group.disabled"/>
														<div class="input-group-btn">
															<span class="btn btn-primary" ng-click="context.selectEmoji();" ng-disabled="context.group.disabled"><i class="fa fa-github-alt"></i> 添加表情</span>
														</div>
													</div>
												</div>
											</div>
											<div class="form-group" ng-if="context.activeType == 2 || (context.activeType == 1 && context.activeItem.sub_button.length == 0)">
												<label class="control-label col-xs-2">菜单动作</label>
												<div class="col-xs-10 menu-action">
													<span >
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="view" ng-disabled="context.group.disabled"> 链接
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="click" ng-disabled="context.group.disabled"> 触发关键字
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="scancode_push" ng-disabled="context.group.disabled"> 扫码
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="scancode_waitmsg" ng-disabled="context.group.disabled"> 扫码（等待信息）
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="pic_sysphoto" ng-disabled="context.group.disabled"> 系统拍照发图
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="pic_photo_or_album" ng-disabled="context.group.disabled"> 拍照或者相册发图
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="pic_weixin" ng-disabled="context.group.disabled"> 微信相册发图
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="location_select" ng-disabled="context.group.disabled"> 地理位置
													</label>
													</span>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="media_id" ng-disabled="context.group.disabled"> 回复素材
													</label>
													<label class="radio-inline">
														<input type="radio" name="ipt" ng-model="context.activeItem.type" value="view_limited" ng-disabled="context.group.disabled"> 跳转图文
													</label>
													<div ng-show="context.activeItem.type == 'view';">
														<hr />
														<div class="input-group">
															<input class="form-control" id="ipt-url" type="text" ng-model="context.activeItem.url" ng-disabled="context.group.disabled"/>
															<div class="input-group-btn">
																<button class="btn btn-primary" id="search" ng-click="context.select_link()" ng-disabled="context.group.disabled"><i class="fa fa-external-link"></i> 系统链接</button>
															</div>
														</div>
														<span class="help-block">指定点击此菜单时要跳转的链接（注：链接需加http://）</span>
														<span class="help-block"><strong>注意: 由于接口限制. 如果你没有网页oAuth接口权限, 这里输入链接直接进入微站个人中心时将会有缺陷(有可能获得不到当前访问用户的身份信息. 如果没有oAuth接口权限, 建议你使用图文回复的形式来访问个人中心)</strong></span>
													</div>
													<div ng-show="context.activeItem.type == 'media_id' || context.activeItem.type == 'view_limited';">
														<hr />
														<div class="input-group">
															<input class="form-control" id="ipt-url" type="text" ng-model="context.activeItem.media_id" ng-disabled="context.group.disabled"/>
															<div class="input-group-btn">
																<button class="btn btn-primary" id="media_id" ng-click="context.select_mediaid()" ng-disabled="context.group.disabled"><i class="fa fa-external-link"></i> 选择素材</button>
															</div>
														</div>
														<span class="help-block">公众平台的素材id</span>
													</div>
													<div ng-show="context.activeItem.type != 'view' && context.activeItem.type != 'media_id' && context.activeItem.type != 'view_limited'" style="position:relative;">
														<hr />
														<div class="input-group">
															<input class="form-control" id="ipt-forward" type="text" ng-model="context.activeItem.key" ng-disabled="context.group.disabled"/>
															<div class="input-group-btn">
																<button class="btn btn-primary" id="search" ng-click="context.search()" ng-disabled="context.group.disabled"><i class="fa fa-search"></i> 搜索</button>
															</div>
														</div>
														<div id="key-result" style="width:100%;position:absolute;top:55px;left:0px;display:none;z-index:10000">
															<ul class="dropdown-menu" style="display:block;width:88%;"></ul>
														</div>
														<span class="help-block">指定点击此菜单时要执行的操作, 你可以在这里输入关键字, 那么点击这个菜单时就就相当于发送这个内容至微赞系统</span>
														<span class="help-block"><strong>这个过程是程序模拟的, 比如这里添加关键字: 优惠券, 那么点击这个菜单是, 微赞系统相当于接受了粉丝用户的消息, 内容为"优惠券"</strong></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
			<div class="shop-preview col-xs-12 col-sm-9 col-lg-10">
			<div class="text-center alert alert-warning" style="background:#faebcc">
									<span class="btn btn-primary" id="btn-submit" ng-click="context.submit();">上架</span>
							</div>
		</div>
		</div>
</div>

<script type="text/javascript">
require(['angular', 'underscore', 'jquery.ui', 'jquery.caret', 'wechatDistrict'], function(angular, _, $, $, dis){
	$(".tpl-district-container").each(function(){
		var elms = {};
		elms.province = $(this).find(".tpl-province")[0];
		elms.city = $(this).find(".tpl-city")[0];
		var vals = {};
		vals.province = $(elms.province).attr("data-value");
		vals.city = $(elms.city).attr("data-value");
		dis.render(elms, vals, {withTitle: true});
	});
	angular.module('app', []).controller('conditionMenuDesigner', function($scope, $http){
		$scope.context = {};
		$scope.context.group = null;
				if(!$scope.context.group) {
			$scope.context.group = {
				title: '标题',
				type: "1",
				button: [{
					name: '菜单名称',
					type: 'url',
					url: '',
					key: '',
					media_id : '',
					sub_button: []
				}],
				matchrule: {
					sex: 0,
					client_platform_type: 0,
					group_id: -1,
					country: '',
					province: '',
					city: ''
				}
			};
			if($scope.context.group.type == 1) {
				$scope.context.group.title = '系统默认菜单';
			} else if($scope.context.group.type == 2) {
				$scope.context.group.title = '个性化菜单';
			}
		}
		$scope.context.activeIndex = 0;
		$scope.context.activeBut = $scope.context.group['button'][$scope.context.activeIndex];
		$scope.context.activeItem = $scope.context.activeBut;
		$scope.context.activeType = 1; //标识一级菜单
		//删除默认菜单
		$scope.context.remove = function(){
			if(!confirm('删除默认菜单会清空所有菜单记录，确定吗？')) {
				return false;
			}
			location.href = "./index.php?c=platform&a=menu&do=remove&id=0";
			return false;
		};

		$scope.context.submit = function(){
			var group = $scope.context.group;
			group.button = _.sortBy(group.button, function(h){
				var elm = $(':hidden[data-role="parent"][data-hash="' + h.$$hashKey + '"]');
				return elm.parent().index();
			});
			angular.forEach(group.button, function(j){
				j.sub_button = _.sortBy(j.sub_button, function(h){
					var e = $(':hidden[data-role="sub"][data-hash="' + h.$$hashKey + '"]');
					return e.parent().index();
				});
			});

			if(!$.trim(group.title)) {
				util.message('没有设置标题', '', 'error');
				return false;
			}
						if(group.button.length < 1) {
				util.message('没有设置菜单', '', 'error');
				return false;
			}
			var error = {name: '', action: ''};
			angular.forEach(group.button, function(val, index){
				if($.trim(val.name) == '') {
					this.name += '第' + (index + 1) + '个一级菜单未设置菜单名称<br>';
				}
				if(val.sub_button.length > 0) {
					angular.forEach(val.sub_button, function(v, index1){
						if($.trim(v.name) == '') {
							this.name += '第' + (index + 1) + '个一级菜单中的第' + (index1 + 1) + '个二级菜单未设置菜单名称<br>';
						}
						if((v.type == 'view' && $.trim(v.url) == '') || ((v.type != 'view' && v.type != 'media_id' && v.type != 'view_limited') && $.trim(v.key) == '') || ((v.type == 'media_id' || v.type == 'view_limited') && !$.trim(v.media_id))) {
							this.action += '菜单【' + val.name + '】的子菜单【' + v.name + '】未设置操作选项. <br />';
						}
					}, error);
				} else {
					if((val.type == 'view' && $.trim(val.url) == '') || ((val.type != 'view' && val.type != 'media_id' && val.type != 'view_limited') && $.trim(val.key) == '') || ((val.type == 'media_id' || val.type == 'view_limited') && !$.trim(val.media_id))) {
						this.action += '菜单【' + val.name + '】不存在子菜单并且未设置操作选项. <br />';
					}
				}
			}, error);

			if(error.name) {
				util.message(error.title, '', 'error');
				return;
			}
			if(error.action) {
				util.message(error.action, '', 'error');
				return;
			}
			$('#btn-submit').attr('disabled', true);
			$http.post(location.href, {group: group, method: 'save'}).success(function(dat){
				if(dat.message.errno != 0) {
					$('#btn-submit').attr('disabled', false);
					util.message(dat.message.message, '', 'error');
				} else {
					util.message('创建菜单成功. ', "./index.php?c=platform&a=menu&do=display&", 'success');
				}
			});
		}

		$scope.context.triggerActiveBut = function(but){
			var index = $.inArray(but, $scope.context.group.button);
			if(index == -1) return false;
			$scope.context.activeIndex = index;
			$scope.context.activeBut = $scope.context.group['button'][$scope.context.activeIndex];
			$scope.context.activeItem = $scope.context.activeBut;
			$scope.context.activeType = 1;
		};

		$scope.context.editBut = function(subbut, but){
			$scope.context.triggerActiveBut(but);
			if(!subbut) {
				$scope.context.activeItem = but;
				$scope.context.activeType = 1;
			} else {
				$scope.context.activeItem = subbut;
				$scope.context.activeType = 2;
			}
		};

		$scope.context.addBut = function(){
			if($scope.context.group['button'].length >= 3) {
				return;
			}
			$scope.context.group['button'].push({
				name: '菜单名称',
				type: 'view',
				url: '',
				key: '',
				media_id : '',
				sub_button: []
			});
			var but = $scope.context.group['button'][$scope.context.group.button.length - 1];
			$scope.context.triggerActiveBut(but);
			$('.designer-x').sortable({
				items: '.js-sortable',
				axis: 'x'
			});
		}

		$scope.context.removeBut = function(but, type){
			if(type == 1) {
				if(!confirm('将同时删除所有子菜单,是否继续')) {
					return false;
				}
				$scope.context.group.button = _.without($scope.context.group.button, but);
				$scope.context.triggerActiveBut($scope.context.group['button'][0]);
			} else {
				$scope.context.activeBut.sub_button = _.without($scope.context.activeBut.sub_button, but);
				$scope.context.triggerActiveBut($scope.context.activeBut);
			}
		};

		$scope.context.addSubBut = function(but){
			if($scope.context.group.disabled == 1) {
				return false;
			}
			$scope.context.triggerActiveBut(but);
			if($scope.context.activeBut.sub_button.length >= 5) {
				return;
			}
			$scope.context.activeBut.sub_button.push({
				name: '子菜单名称',
				type: 'url',
				url: '',
				key: '',
				media_id : ''
			});
			$('.designer-y').sortable({
				items: 'dd',
				axis: 'y',
				cancel: '.js-not-sortable'
			});
			$scope.context.activeItem = $scope.context.activeBut.sub_button[$scope.context.activeBut.sub_button.length - 1];
			$scope.context.activeType = 2;
		}

		/*选择Emoji表情*/
		$scope.context.selectEmoji = function() {
			util.emojiBrowser(function(emoji){
				var text = '::' + emoji.find("span").text() + '::';
				$('#title').setCaret();
				$('#title').insertAtCaret(text);
				$scope.context.activeItem.name = $('#title').val();
				$scope.$digest();
			});
		};

		//点击选择【系统连接】事件
		$scope.context.select_link = function(){
			var ipt = $(this).parent().prev();
			util.linkBrowser(function(href){
				var site_url = "http://www.weizan1.com/";
				if(href.substring(0, 4) == 'tel:') {
					util.message('自定义菜单不能设置为一键拨号');
					return;
				} else if(href.indexOf("http://") == -1 && href.indexOf("https://") == -1) {
					href = href.replace('./index.php?', '/index.php?');
					href = site_url + 'app' + href;
				}
				$scope.context.activeItem.url = href;
				$scope.$digest();
			});
		};

		$scope.context.search = function(){
			var search_value = $('#ipt-forward').val();
			$.post("./index.php?c=platform&a=menu&do=search_key&", {'key_word' : search_value}, function(data){
				var data = $.parseJSON(data);
				var total = data.length;
				var html = '';
				if(total > 0) {
					for(var i = 0; i < total; i++) {
						html += '<li><a href="javascript:;">' + data[i] + '</a></li>';
					}
				} else {
					html += '<li><a href="javascript:;" id="no-result">没有找到您输入的关键字</a></li>';
				}
				$('#key-result ul').html(html);
				$('#key-result ul li a[id!="no-result"]').click(function(){
					$('#ipt-forward').val($(this).html());
					$scope.context.activeItem.key = $(this).html();
					$('#key-result').hide();
				});
				$('#key-result').show();
			});
		};

		$scope.context.select_mediaid = function(){
			var option = {
				'ignore' : {
					'wxcard' : true
				}
			};
			if($scope.context.activeItem.type == 'view_limited') {
				option.ignore = {
					'image' : true,
					'voice' : true,
					'video' : true,
					'wxcard' : true
				};
			}
			util.material(function(material){
				$scope.context.activeItem.media_id = material.media_id;
				$scope.$digest();
			}, option);
		};
	});
	angular.bootstrap($('#conditionMenuDesigner')[0], ['app']);
	$(function(){
		$('.designer-y').sortable({
			items: 'dd',
			axis: 'y',
			cancel: '.js-not-sortable'
		});

		$('.designer-x').sortable({
			items: '.js-sortable',
			axis: 'x'
		});
	});
});
</script>
			</div>
		</div>
	</div>
	<script>
		function subscribe(){
			$.post("./index.php?c=utility&a=subscribe&", function(){
				setTimeout(subscribe, 5000);
			});
		}
		function sync() {
			$.post("./index.php?c=utility&a=sync&", function(){
				setTimeout(sync, 60000);
			});
		}
		$(function(){
			subscribe();
			sync();
		});
					function checknotice() {
				$.post("./index.php?c=utility&a=notice&", {}, function(data){
					var data = $.parseJSON(data);
					$('#notice-container').html(data.notices);
					$('#notice-total').html(data.total);
					if(data.total > 0) {
						$('#notice-total').css('background', '#ff9900');
					} else {
						$('#notice-total').css('background', '');
					}
					setTimeout(checknotice, 60000);
				});
			}
			checknotice();
					</script>
	<script type="text/javascript">
		require(['bootstrap']);
		$('.js-clip').each(function(){
			util.clip(this, $(this).attr('data-url'));
		});
	</script>
	<div class="container-fluid footer" role="footer">
		<div class="page-header"></div>
		<span class="pull-left">
			<p>powered by 012wz.com</p>
		</span>
		<span class="pull-right">
			<p><a href="http://chat.idear7.cn">关于微赞</a>&nbsp; &nbsp; <a href="http://chat.idear7.cn">微赞帮助</a></p>
		</span>
	</div>
	</body>
</html>
