<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>基本混合图文回复</title>
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


<div class="container-fluid" style="margin-top: 36px;margin-bottom: 88px;min-height: 700px;max-width: 1400px;">
	<div class="row">

		<div class="col-xs-12 col-sm-9 col-lg-10">
										<ul class="nav nav-tabs">
	<li><a href="./index.php?c=platform&a=reply&m=news">管理基本混合图文回复</a></li>
	<li class="active"><a href="./index.php?c=platform&a=reply&do=post&m=news"><i class="fa fa-plus"></i> 添加基本混合图文回复</a></li>
		</ul>
<?php $this->load->view('seller/wx/inc/reply_js');?>
<div class="clearfix ng-cloak" id="js-reply-form" ng-controller="replyForm">
	<form id="reply-form" class="form-horizontal form" action="<?php echo SELLER_SITE_URL.'/reply/txt_save';?>" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<div class="col-sm-12">
				<?php $this->load->view('seller/wx/inc/reply_inc');?>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-12">
				<script type="text/javascript" src="/resource/components/ueditor/ueditor.config.js"></script>
				<script type="text/javascript" src="/resource/components/ueditor/ueditor.all.min.js"></script>
				<script type="text/javascript" src="/resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>
	<script type="text/javascript">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'insertvideo',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': 200,
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multi : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			
	</script><div class="alert alert-info" style="margin-top:-20px">
	<i class="fa fa-info-circle"></i>
	图文可添加多组回复，如果添加了多组回复，系统将随机选择一条回复给粉丝
</div>
<input type="hidden" name="replies" value="">
<div class="panel panel-default clearfix">
	<div class="panel-heading">回复内容</div>
	<div class="panel-body">
		<div class="row clearfix reply">
			<div class="col-xs-6 col-sm-3 col-md-3">
				<div class="panel-group" ng-repeat="items in context.groups">
					<div class="panel panel-default" ng-repeat="item in items">
						<div class="panel-body" ng-if="$index == 0">
							<div class="img">
								<i class="default">封面图片</i>
								<img src="" ng-src="{{item.thumb}}">
								<span class="text-left">{{item.title}}</span>
								<div class="mask">
									<!-- <a href="javascript:;" ng-click="context.exportFromCms(item, items)"><i class="fa fa-book"></i>导入文章</a> -->
									<a href="javascript:;" ng-click="context.editItem(item, items)"><i class="fa fa-edit"></i>编辑</a>
									<a href="javascript:;" ng-click="context.removeItem(item, items)"><i class="fa fa-times"></i>删除</a>
								</div>
							</div>
						</div>
						<div class="panel-body" ng-if="$index != 0">
							<div class="text">
								<h4>{{item.title}}</h4>
							</div>
							<div class="img">
								<img src="" ng-src="{{item.thumb}}">
								<i class="default">缩略图</i>
							</div>
							<div class="mask">
								<!-- <a href="javascript:;" ng-click="context.exportFromCms(item, items)"><i class="fa fa-book"></i> 导入文章</a> -->
								<a href="javascript:;" ng-click="context.editItem(item, items)"><i class="fa fa-edit"></i> 编辑</a>
								<a href="javascript:;" ng-click="context.removeItem(item, items)"><i class="fa fa-times"></i> 删除</a>
							</div>
						</div>
					</div>
					<div class="panel panel-default" ng-show="items.length < 8">
						<div class="panel-body" style="padding-right:15px">
							<div class="add" ng-click="items.length >= 8 ? '' : context.addItem(items);"><span><i class="fa fa-plus"></i> 添加</span></div>
						</div>
					</div>
					<div class="no">{{$index + 1}}</div>
					<div class="del" ng-click="context.removeGroup(items);"><i class="fa fa-times"></i></div>
				</div>
				<div class="btn btn-primary" ng-click="context.addGroup()" style="margin-bottom: 20px">添加一组回复</div>

			</div>
			<div class="col-xs-6 col-sm-9 col-md-9 aside" id="edit-container">
				<div style="margin-bottom: 20px"></div>
				<div class="card">
					<div class="arrow-left"></div>
					<div class="inner">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">标题</label>
									<div class="col-sm-9 col-xs-12">
										<input type="text" class="form-control" placeholder="添加图文消息的标题" ng-model="context.activeItem.title"/>
										<input type="hidden" ng-model="context.activeItem.id" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">作者</label>
									<div class="col-sm-9 col-xs-12">
										<input type="text" class="form-control" placeholder="添加图文消息的作者" ng-model="context.activeItem.author"/>
									</div>
								</div>
								<div class="form-group" ng-show="!context.activeItem.isFirst">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
									<div class="col-sm-9 col-xs-12">
										<input type="text" class="form-control" placeholder="添加排序" ng-model="context.activeItem.displayorder"/>
										<span class="help-block">排序只能在提交后显示。按照从大到小的顺序对图文排序</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">封面</label>
									<div class="col-sm-9 col-xs-12">
										<div class="col-xs-3 img" ng-if="context.activeItem.thumb == ''">
											<span ng-click="context.changeItem(context.activeItem)"><i class="fa fa-plus-circle green"></i>&nbsp;添加图片</span>
										</div>
										<div class="col-xs-3 img" ng-if="context.activeItem.thumb != ''">
											<h3 ng-click="context.changeItem(context.activeItem)">重新上传</h3>
											<img ng-src="{{ context.activeItem.thumb }}">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
									<div class="col-sm-9 col-xs-12">
										<label>
											封面（大图片建议尺寸：360像素 * 200像素）
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
									<div class="col-sm-9 col-xs-12">
										<label class="checkbox-inline">
											<input type="checkbox" value="1" ng-model="context.activeItem.incontent" ng-checked="context.activeItem.incontent"/> 封面图片显示在正文中
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
									<div class="col-sm-9 col-xs-12">
										<textarea class="form-control" placeholder="添加图文消息的简短描述" ng-model="context.activeItem.description"></textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-4 col-md-offset-3 col-lg-offset-2 col-xs-12 col-sm-8 col-md-9 col-lg-10">
										<label class="checkbox-inline">
											<input type="checkbox" value="1" ng-model="context.activeItem.detail" ng-checked="context.activeItem.detail" ng-init="context.activeItem.detail = context.activeItem.content!=''"/> 是否编辑图文详情
										</label>
										<span class="help-block">编辑详情可以展示这条图文的详细内容, 可以选择不编辑详情, 那么这条图文将直接链接至下方的原文地址中.</span>
									</div>
								</div>
								<div class="form-group" ng-show="context.activeItem.detail">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">详情</label>
									<div class="col-sm-9 col-xs-12">
										<div ng-my-editor ng-my-value="context.activeItem.content"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label">来源</label>
									<div class="col-sm-9 col-xs-12">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="图文消息的来源地址" ng-model="context.activeItem.url"/>
											<span class="input-group-btn">
												<button class="btn btn-default" type="button" ng-click="context.selectLink()"><i class="fa fa-external-link"></i> 系统链接</button>
											</span>
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
<script>
	window.initReplyController = function($scope, $http) {
		$scope.context = {};
		$scope.context.groups = [];
		if(!$.isArray($scope.context.groups)) {
			$scope.context.groups = [];
		}
		if($scope.context.groups.length == 0) {
			$scope.context.groups.push(
				[{
					id: '',
					parent_id: -1,
					title: '',
					author: '',
					thumb: '',
					displayorder: '0',
					incontent: true,
					description: '',
					detail: true,
					content: '',
					url: ''
				}]
			);
		}

		//当前编辑的回复项目的索引
		$scope.context.activeGroupIndex = 0;
		$scope.context.activeIndex = 0;
		//当前编辑的回复项目
		$scope.context.activeItem = $scope.context.groups[$scope.context.activeGroupIndex][$scope.context.activeIndex];
		$scope.context.activeItem.incontent = $scope.context.activeItem.incontent == 1;

		$scope.context.addGroup = function(){
			$scope.context.groups.push(
				[{
					id: '',
					parent_id: -1,
					title: '',
					author: '',
					thumb: '',
					displayorder: '0',
					incontent: true,
					description: '',
					detail: true,
					content: '',
					url: ''
				}]
			);

			$scope.context.activeGroupIndex = $scope.context.groups.length - 1;
			$scope.context.triggerActiveItem(0);
		};

		$scope.context.removeGroup = function(items){
			if($scope.context.groups.length == 1) {
				util.message('至少有一组回复内容');
				return false;
			}
			$scope.context.groups = _.without($scope.context.groups, items);
			$scope.context.activeGroupIndex = 0;
			$scope.context.triggerActiveItem(0);
			$scope.$digest();
		}

		$scope.context.editItem = function(item, items){
			$scope.context.triggerActiveGroup(items);
			var index = $.inArray(item, $scope.context.groups[$scope.context.activeGroupIndex]);
			if(index == -1) return false;
			$scope.context.triggerActiveItem(index);
		};

		$scope.context.triggerActiveGroup = function(items) {
			var index = $.inArray(items, $scope.context.groups);
			if(index == -1) return false;
			$scope.context.activeGroupIndex = index;
		}

		$scope.context.triggerActiveItem = function(index) {
			var gindex = $scope.context.activeGroupIndex;
			var top = 0;
			for(i = 0; i < gindex; i++) {
				if($scope.context.groups[i].length == 8) {
					top = top + 7*105 + 210;
				} else {
					top = top + 210 + $scope.context.groups[i].length * 105;
				}
			}
			top += index * 105 + 80;

			$('#edit-container').css('marginTop', top);
			$("html,body").animate({scrollTop:top + 500},500);
			$scope.context.activeIndex = index;
			$scope.context.activeItem = $scope.context.groups[$scope.context.activeGroupIndex][$scope.context.activeIndex];
			$scope.context.activeItem.incontent = $scope.context.activeItem.incontent == 1;
			$scope.context.activeItem.detail = $scope.context.activeItem.content != '';
			$scope.context.activeItem.isFirst = false;
			if(!index) {
				$scope.context.activeItem.isFirst = true;
			}
		};

		$scope.context.changeItem = function(item) {
			require(['fileUploader'], function(uploader){
				uploader.init(function(imgs){
					var index = $.inArray(item, $scope.context.groups[$scope.context.activeGroupIndex]);
					if(index > -1){
						$scope.context.groups[$scope.context.activeGroupIndex][index].thumb = imgs.url;
						$scope.$apply()
					}
				}, {'direct' : true, 'multiple' : false});
			});
		};

		$scope.context.selectLink = function(){
			util.linkBrowser(function(href){
				$scope.context.activeItem.url = href;
				$scope.$digest();
			});
		};

		$scope.context.addItem = function(items){
			$scope.context.triggerActiveGroup(items);

			$scope.context.groups[$scope.context.activeGroupIndex].push({
				id: '',
				parent_id: -1,
				title: '',
				author: '',
				thumb: '',
				displayorder: '0',
				incontent: true,
				description: '',
				detail: true,
				content: '',
				url: ''
			});
			var index = $scope.context.groups[$scope.context.activeGroupIndex].length - 1;
			$scope.context.triggerActiveItem(index);
		};

		$scope.context.removeItem = function(item, items){
			$scope.context.triggerActiveGroup(items);
			require(['underscore'], function(_){
				$scope.context.groups[$scope.context.activeGroupIndex] = _.without($scope.context.groups[$scope.context.activeGroupIndex], item);
				$scope.context.triggerActiveItem(0);
				$scope.$digest();
			});
		};

		//导入文章
		$scope.context.exportFromCms = function(item, items) {
			$scope.context.triggerActiveGroup(items);
			var index = $.inArray(item, $scope.context.groups[$scope.context.activeGroupIndex]);
			if(index == -1) return false
			$scope.context.triggerActiveItem(index);
			$scope.context.searchCms();
		}

		$scope.context.searchCms = function(page) {
			var html = {};
			html['header'] = '<ul role="tablist" class="nav nav-pills" style="font-size:14px; margin-top:-20px;">'+
					'	<li role="presentation" class="active" id="li_goodslist"><a data-toggle="tab" role="tab" aria-controls="articlelist" href="#articlelist">文章列表</a></li>'+
					'</ul>';
			html['content'] =
				'<div class="tab-content">'+
					'<div id="articlelist" class="tab-pane active" role="tabpanel">' +
					'	<table class="table table-hover">' +
					'		<thead class="navbar-inner">' +
					'			<tr>' +
					'				<th style="width:40%;">标题</th>' +
					'				<th style="width:30%">创建时间</th>' +
					'				<th style="width:30%; text-align:right">' +
					'					<div class="input-group input-group-sm">' +
					'						<input type="text" class="form-control">' +
					'						<span class="input-group-btn">' +
					'							<button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>' +
					'						</span>' +
					'					</div>' +
					'				</th>' +
					'			</tr>' +
					'		</thead>' +
					'		<tbody></tbody>'+
					'	</table>'+
					'	<div id="pager" style="text-align:center;"></div>'+
					'</div>'+
				'</div>';
			html['footer'] = '';
			html['articleitem'] =
					'<%_.each(list, function(item) {%> \n' +
					'<tr>\n' +
					'	<td><a href="#" data-cover-attachment-url="<%=item.attachment%>" title="<%=item.title%>"><%=item.title%></a></td>\n' +
					'	<td><%=item.createtime%></td>\n' +
					'	<td class="text-right">\n' +
					'		<button class="btn btn-default js-btn-select" js-id="<%=item.id%>">选取</button>\n' +
					'	</td>\n' +
					'</tr>\n' +
					'<%});%>\n';
			if (!$('#link-search-cms')[0]) {
				$scope.modalobj = util.dialog(html['header'], html['content'], html['footer'] ,{'containerName' : 'link-search-cms'});
				$scope.modalobj.find('.modal-body').css({'height':'680px','overflow-y':'auto' });
				$scope.modalobj.modal('show');
				$scope.modalobj.on('hidden.bs.modal', function(){$scope.modalobj.remove();});
				$('#link-search-cms').data('modal', $scope.modalobj);
			} else {
				$scope.modalobj = $('#link-search-cms').data('modal');
			}
			page = page || 1;
			$http.get('./index.php?c=utility&a=link&do=articlelist' + '&page=' + page).success(function(result, status, headers, config){
				if (result.message.list) {
					$scope.modalobj.find('#articlelist').data('articles', result.message.list);
					$scope.modalobj.find('#articlelist tbody').html(_.template(html['articleitem'])(result.message));
					$scope.modalobj.find('#pager').html(result.message.pager);
					$scope.modalobj.find('#pager .pagination li[class!=\'active\'] a').click(function(){
						$scope.context.searchCms($(this).attr('page'));
						return false;
					});
					$scope.modalobj.find('.js-btn-select').click(function(){
						$scope.context.addCms($(this).attr('js-id'));
						$scope.$apply();
						$scope.modalobj.modal('hide');
					});
				}
			});
		};

		$scope.context.addCms = function(id) {
			var article =$scope.modalobj.find('#articlelist').data('articles')[id];
			$scope.context.activeItem.title = article.title;
			$scope.context.activeItem.thumb = article.thumb_url;
			$scope.context.activeItem.author = article.author;
			$scope.context.activeItem.incontent = article.incontent == 1;
			$scope.context.activeItem.description = article.description;
			$scope.context.activeItem.content = article.content;
			$scope.context.activeItem.url = article.linkurl;
			$scope.context.activeItem.detail = article.content != '';
		};
	};

	window.validateReplyForm = function(form, $, _, util, $scope) {
		if($scope.context.groups.length == 0) {
			util.message('没有回复内容', '', 'error');
			return false;
		}
		var error = {empty: false, message: ''};
		angular.forEach($scope.context.groups, function(v, k){
			var item = $scope.context.groups[k];
			angular.forEach(item, function(v1){
				if($.trim(v1.title) == '') {
					this.empty = true;
				}
				if($.trim(v1.thumb) == '') {
					this.message = '标题为 "' + v1.title + '" 的回复条目没有上传封面图片<br>';
				}
			}, error);
		}, error);
		if(error.empty) {
			util.message('存在没有设置 "标题" 的回复条目');
			return false;
		}
		if(error.message) {
			util.message(error.message, '', 'error');
			return false;
		}
		var val = angular.toJson($scope.context.groups);
		$(':hidden[name=replies]').val(val);
		return true;
	};
</script>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="8a4abc8c" />
			</div>
		</div>
	</form>
</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		require(['bootstrap']);
		$('.js-clip').each(function(){
			util.clip(this, $(this).attr('data-url'));
		});
	</script>

	</body>
</html>
