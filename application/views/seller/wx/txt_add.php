
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>基本文字回复</title>
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
				<li><a href="./index.php?c=platform&a=reply&m=basic">管理基本文字回复</a></li>
				<li class="active"><a href="./index.php?c=platform&a=reply&do=post&m=basic"><i class="fa fa-plus"></i> 添加基本文字回复</a></li>
			</ul>
			<style type="text/css">
				.help-block em{display:inline-block;width:10em;font-weight:bold;font-style:normal;}
			</style>
<script>
require(['angular.sanitize', 'bootstrap', 'underscore', 'util'], function(angular, $, _, util){
	angular.module('app', ['ngSanitize']).controller('replyForm', function($scope, $http){
		$scope.reply = {
			advSetting: false,
			advTrigger: false,
			entry: null 
		};
		$scope.trigger = {};
		$scope.trigger.descriptions = {};
		$scope.trigger.descriptions.contains = '用户进行交谈时，对话中包含上述关键字就执行这条规则。';
		$scope.trigger.descriptions.regexp = '用户进行交谈时，对话内容符合述关键字中定义的模式才会执行这条规则。<br/><strong>注意：如果你不明白正则表达式的工作方式，请不要使用正则匹配</strong> <br/><strong>注意：正则匹配使用MySQL的匹配引擎，请使用MySQL的正则语法</strong> <br /><strong>示例: </strong><br/><em>^微信</em>匹配以“微信”开头的语句<br /><em>微信$</em>匹配以“微信”结尾的语句<br /><em>^微信$</em>匹配等同“微信”的语句<br /><em>微信</em>匹配包含“微信”的语句<br /><em>[0-9\.\-]</em>匹配所有的数字，句号和减号<br /><em>^[a-zA-Z_]$</em>所有的字母和下划线<br /><em>^[[:alpha:]]{3}$</em>所有的3个字母的单词<br /><em>^a{4}$</em>aaaa<br /><em>^a{2,4}$</em>aa，aaa或aaaa<br /><em>^a{2,}$</em>匹配多于两个a的字符串';
		$scope.trigger.descriptions.trustee = '如果没有比这条回复优先级更高的回复被触发，那么直接使用这条回复。<br/><strong>注意：如果你不明白这个机制的工作方式，请不要使用直接接管</strong>';
		$scope.trigger.labels = {};
		$scope.trigger.labels.contains = '包含关键字';
		$scope.trigger.labels.regexp = '正则表达式模式';
		$scope.trigger.labels.trustee = '直接接管操作';
		$scope.trigger.active = 'contains';
		$scope.trigger.items = {};
		$scope.trigger.items.default = '';
		$scope.trigger.items.contains = [];
		$scope.trigger.items.regexp = [];
		$scope.trigger.items.trustee = [];
		if($scope.reply.entry) {
			$scope.reply.entry.istop = $scope.reply.entry.displayorder >= 255 ? 1 : 0;
			//$scope.reply.advSetting = $scope.reply.entry.displayorder!=0 || !$scope.reply.entry.status;
			if($scope.reply.entry.keywords) {
				angular.forEach($scope.reply.entry.keywords, function(v, k){
					if(v.type == '1') {
						this.default += (v.content + ',');
					}
					if(v.type == '2') {
						this.contains.push({content: v.content, label: '请输入' + $scope.trigger.labels.contains, saved: true});
					}
					if(v.type == '3') {
						this.regexp.push({content: v.content, label: '请输入' + $scope.trigger.labels.regexp, saved: true});
					}
					if(v.type == '4') {
						this.trustee.push({});
					}
				}, $scope.trigger.items);
				if($scope.trigger.items.default.length > 1) {
					$scope.trigger.items.default = $scope.trigger.items.default.slice(0, $scope.trigger.items.default.length - 1);
				}
				if($scope.trigger.items.contains.length > 0 || $scope.trigger.items.regexp.length > 0 || $scope.trigger.items.trustee.length > 0) {
					$scope.reply.advTrigger = true;
				}
				if($scope.trigger.items.contains.length > 0) {
					$('a[data-toggle="tab"]').eq(0).tab('show');
					$scope.trigger.active = 'contains';
				} else if($scope.trigger.items.regexp.length > 0) {
					$('a[data-toggle="tab"]').eq(1).tab('show');
					$scope.trigger.active = 'regexp';
				} else if($scope.trigger.items.trustee.length > 0) {
					$('a[data-toggle="tab"]').eq(2).tab('show');
					$scope.trigger.active = 'trustee';
				}
			}
		}
		$scope.trigger.addItem = function(){
			var type = $scope.trigger.active;
			if(type != 'trustee') {
				$scope.trigger.items[type].push({content: '', label: '请输入' + $scope.trigger.labels[type], saved: false});
			} else {
				if($scope.trigger.items.trustee.length == 0) {
					$scope.trigger.items.trustee.push({type:4, content:''});
				}
			}
		};
		
		$scope.trigger.saveItem = function(item){
			item.saved = !item.saved;
		};
		$scope.trigger.removeItem = function(item) {
			var type = $scope.trigger.active;
			$scope.trigger.items[type] = _.without($scope.trigger.items[type], item);
			$scope.$digest();
		};
		$scope.trigger.test = function(item) {
		}
		if($.isFunction(window.initReplyController)) {
			window.initReplyController($scope, $http);
		}
		$('#reply-form').submit(function(){
			if($.trim($(':text[name="name"]').val()) == '') {
				util.message('必须输入回复规则名称');
				return false;
			}
			var val = [];
			$scope.$digest();
			if($scope.trigger.items.default != '') {
				var kws = $scope.trigger.items.default.replace('，', ',').split(',');
				kws = _.union(kws);
				angular.forEach(kws, function(v){
					if(v != '') {
						val.push({type: 1, content: v});
					}
				}, val);
			}
			angular.forEach($scope.trigger.items, function(v, name){
				var flag = true;
				if(name != 'default' && v.length > 0) {
					if(name == 'contains' || name == 'regexp'){
						angular.forEach(v, function(value){
							if(value.content.trim() != '') {
								this.push({
									content: value.content,
									type: name == 'contains' ? 2 : 3
								});
							}
						}, val);
					}
					if(name == 'trustee'){
						angular.forEach(v, function(value){
							this.push({type:4, content:''});
						}, val);
					}
				}
			}, val);
			if(val.length == 0 && $scope.trigger.active != 'trustee') {
				util.message('请输入有效的触发关键字.');
				return false;
			}
			$scope.$digest();
			val = angular.toJson(val);
			$(':hidden[name=keywords]').val(val);
			if($.isFunction(window.validateReplyForm)) {
				return window.validateReplyForm(this, $, _, util, $scope, $http);
			}
			return true;
		});
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$scope.trigger.active = e.target.hash.replace(/#/, '');
			$scope.$digest();
		})
		util.emotion($("#keyword"), $("#keywordinput")[0], function(txt, elm, target){
			$scope.trigger.items.default = $(target).val();
			$scope.$digest();
		});
	}).filter('nl2br', function($sce){
		return function(text) {
			return text ? $sce.trustAsHtml(text.replace(/\n/g, '<br/>')) : '';
		};
	}).directive('ngInvoker', function($parse){
		return function (scope, element, attr) {
			scope.$eval(attr.ngInvoker);
		};
	}).directive('ngMyEditor', function(){
		var editor = {
			'scope' : {
				'value' : '=ngMyValue'
			},
			'template' : '<textarea id="editor" style="height:600px;width:100%;"></textarea>',
			'link' : function ($scope, element, attr) {
				if(!element.data('editor')) {
					editor = UE.getEditor('editor', ueditoroption);
					element.data('editor', editor);
					editor.addListener('contentChange', function() {
						$scope.value = editor.getContent().replace(/\&quot\;/g, '"');
						$scope.$root.$$phase || $scope.$apply('value');
					});
					$(element).parents('form').submit(function() {
						if (editor.queryCommandState('source')) {
							editor.execCommand('source');
						}
					});
					editor.addListener('ready', function(){
						if (editor && editor.getContent() != $scope.value) {
							editor.setContent($scope.value);
						}
						$scope.$watch('value', function (value) {
							if (editor && editor.getContent() != value) {
								editor.setContent(value ? value : '');
							}
						});
					});
				}
			}
		};
		return editor;
	});
	angular.bootstrap($('#js-reply-form')[0], ['app']);


	// 检测规则是否已经存在
	window.checkKeyWord = function(key) {
		var keyword = key.val().trim();
		if (keyword == '') {
			return false;
		}
		var type = key.attr('data-type');
		var wordIndex = key.index('.keyword');
		var isLeagl = true;
		$('.keyword').each(function(index) {
			var currentWord = $(this).val().trim();
			if (keyword == currentWord && wordIndex != index) {
				isLeagl = false;
				return false;
			}
		});
		if (isLeagl === false) {
			key.next().text('');
			util.message('该关键字已重复存在于当前规则中.');
			return false;
		}

		$.post(location.href, {keyword:keyword}, function(resp){
			if(resp != 'success') {
				var rid = $('input[name="rid"]').val();
				var rules = JSON.parse(resp);
				var url = "./index.php?c=platform&a=reply&do=post&m=basic";
				var ruleurl = '';
				for (rule in rules) {
					if (rid != rules[rule].id) {
						ruleurl += "<a href='" + url + "&rid=" + rules[rule].id + "' target='_blank'><strong class='text-danger'>" + rules[rule].name + "</strong></a>&nbsp;";
					}
				}
				if (ruleurl != '') {
					key.next().html('该关键字已存在于 ' + ruleurl + ' 规则中.');
				}
			} else {
				key.next().text('');
			}
		});
	}

	$('.keyword').each(function() {
		$(this).attr('data-type', 'keyword');
	});
});

</script>
<div class="clearfix ng-cloak" id="js-reply-form" ng-controller="replyForm">
	<form id="reply-form" class="form-horizontal form" action="<?php echo SELLER_SITE_URL.'/wxreply/txt_save';?>" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">添加回复规则 <span class="text-muted">删除，修改规则、关键字以及回复后，请提交以保存操作。</span></div>
					<ul class="list-group">
						<li class="list-group-item">
							<div class="form-group">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">回复规则名称</label>
								<div class="col-sm-6 col-md-8 col-xs-12">
									<input type="text" class="form-control" placeholder="请输入回复规则的名称" name="name" value="" />
								</div>
								<div class="col-sm-3 col-md-2">
									<div class="checkbox">
										<label>
											<input type="checkbox" ng-model="reply.advSetting" /> 高级设置
										</label>
									</div>
								</div>
							</div>
							<div class="form-group" ng-show="reply.advSetting">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
								<div class="col-sm-9">
									<label class="radio-inline">
										<input type="radio" name="status" value="1"  checked="checked" /> 启用
									</label>
									<label class="radio-inline">
										<input type="radio" name="status" value="2"  /> 禁用
									</label>
									<span class="help-block">您可以临时禁用这条回复.</span>
								</div>
							</div>
							<div class="form-group" ng-show="reply.advSetting">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">置顶回复</label>
								<div class="col-sm-9">
									<label class="radio-inline">
										<input type="radio" name="is_top" ng-model="reply.entry.istop" ng-value="1" value="1"  /> 置顶
									</label>
									<label class="radio-inline">
										<input type="radio" name="is_top" ng-model="reply.entry.istop" ng-value="0" value="0"  checked="checked" /> 普通
									</label>
									<span class="help-block">“置顶”时无论在什么情况下均能触发且使终保持最优先级</span>
								</div>
							</div>
							<div class="form-group" ng-show="reply.advSetting && !reply.entry.istop">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">优先级</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" placeholder="输入这条回复规则优先级" name="sort" value="">
									<span class="help-block">规则优先级，越大则越靠前，最大不得超过254</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">触发关键字</label>
								<div class="col-sm-6 col-md-8 col-xs-12">
									<input type="hidden" name="rid" value="0" />
									<input type="text" class="form-control keyword" placeholder="请输入触发关键字" ng-model="trigger.items.default" id="keywordinput" onblur="checkKeyWord($(this));" />
									<span class="help-block"></span>
									<input type="hidden" name="keywords"/>
									<span class="help-block">
										当用户的对话内容符合以上的关键字定义时，会触发这个回复定义。多个关键字请使用逗号隔开。 <br />
										
									</span>
								</div>
								<div class="col-sm-3 col-md-2">
									<div class="checkbox">
										<label>
											<!-- <input type="checkbox" ng-model="reply.advTrigger" /> 高级触发 -->
										</label>
									</div>
								</div>
							</div>
							<div class="form-group" ng-show="reply.advTrigger">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label">高级触发列表</label>
								<div class="col-sm-9">
									<div class="panel panel-default tab-content">
										<div class="panel-heading">
											<ul class="nav nav-pills">
												<li class="active"><a href="#contains" data-toggle="tab">包含关键字</a></li>
												<li><a href="#regexp" data-toggle="tab">正则表达式模式匹配</a></li>
												<li><a href="#trustee" data-toggle="tab">直接接管</a></li>
											</ul>
										</div>
										<ul class="tab-pane list-group active" id="contains">
											<li class="list-group-item row" ng-repeat="entry in trigger.items.contains">
												<div class="col-xs-12 col-sm-8">
													<input type="text" class="form-control keyword" ng-hide="entry.saved" placeholder="{{entry.label}}" ng-model="entry.content" onblur="checkKeyWord($(this));" />
													<span class="help-block"></span>
													<p class="form-control-static" ng-show="entry.saved" ng-bind="entry.content"></p>
												</div>
												<div class="col-sm-4">
													<div class="btn-group">
														<a href="javascript:;" class="btn btn-default" ng-click="trigger.saveItem(entry);">{{entry.saved ? '编辑' : '保存'}}</a>
														<a href="javascript:;" class="btn btn-default" ng-click="trigger.removeItem(entry);">删除</a>
													</div>
												</div>
											</li>
										</ul>
										<ul class="tab-pane list-group" id="regexp">
											<li class="list-group-item row" ng-repeat="entry in trigger.items.regexp">
												<div class="col-xs-12 col-sm-8">
													<input type="text" class="form-control keyword" ng-hide="entry.saved" placeholder="{{entry.label}}" ng-model="entry.content" onblur="checkKeyWord($(this));" />
													<span class="help-block"></span>
													<p class="form-control-static" ng-show="entry.saved" ng-bind="entry.content"></p>
												</div>
												<div class="col-sm-4">
													<div class="btn-group">
														<a href="javascript:;" class="btn btn-default" ng-click="trigger.saveItem(entry);">{{entry.saved ? '编辑' : '保存'}}</a>
														<a href="javascript:;" class="btn btn-default" ng-click="trigger.removeItem(entry);">删除</a>
													</div>
												</div>
											</li>
										</ul>
										<ul class="tab-pane list-group" id="trustee">
											<li class="list-group-item row" ng-repeat="entry in trigger.items.trustee">
												<div class="col-xs-12 col-sm-8">
													<p class="form-control-static">符合优先级条件时, 这条回复将直接生效</p>
												</div>
												<div class="col-sm-4">
													<a href="javascript:;" class="btn btn-default" ng-click="trigger.removeItem(entry);">取消接管</a>
												</div>
											</li>
										</ul>
										<div class="panel-footer">
											<a href="javascript:;" class="btn btn-default" ng-click="trigger.addItem();" ng-bind="'添加' + trigger.labels[trigger.active]">添加</a>
											<span class="help-block" ng-bind-html="trigger.descriptions[trigger.active]"></span>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div> 
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-12">
				<div class="panel panel-default">
	<div class="panel-heading">
		回复内容
	</div>
	<ul class="list-group">
		<li class="row list-group-item" ng-repeat="item in context.items">
			<div class="block">
				<div class="col-xs-12 col-sm-12">
					<textarea class="form-control content" ng-hide="item.saved" placeholder="添加要回复的内容" ng-model="item.content" rows="4" onkeyup="if (this.value.split('\n').length>4) this.rows=this.value.split('\n').length;"></textarea>
					<p class="form-control-static" ng-show="item.saved" ng-bind-html="item.content | nl2br"></p>
				</div>
				<div class="col-xs-12 col-sm-12 help-block">您还可以使用表情和链接。<a class="emotion-triggers" href="javascript:;" ng-init="initEmotion(this);"><i class="fa fa-github-alt"></i> 表情</a> <a class="emoji-triggers" href="javascript:;" ng-click="selectEmoji(this)" title="添加表情"><i class="fa fa-github-alt"></i> Emoji</a></div>
			</div>
			<div class="col-sm-12 text-right">
				<div class="btn-group">
					<a href="javascript:;" class="btn btn-default" ng-click="context.saveItem(item);">{{item.saved ? '编辑' : '保存'}}</a>
					<a href="javascript:;" class="btn btn-default" ng-click="context.removeItem(item);">删除</a>
				</div>
			</div>
		</li>
	</ul>
	<!-- <div class="form-group">
		<div class="col-sm-9">
			<textarea type="text" name="welcome" class="form-control" id="welcomeinput" autocomplete="off" /></textarea>
			<div class="help-block">设置用户添加公众帐号好友时，发送的欢迎信息。<a href="javascript:;" id="welcome"><i class="fa fa-github-alt"></i> 表情</a></div>
		</div>
	</div> -->
	<div class="panel-footer">
		<a href="javascript:;" class="btn btn-default" ng-click="context.addItem();">添加回复条目</a>
		<span class="help-block">添加多条回复内容时, 随机回复其中一条</span>
	</div>
	<input type="hidden" name="replies" />
</div>
<script>
	window.initReplyController = function($scope) {
		$scope.context = {};
		$scope.context.items = null;
		if(!$.isArray($scope.context.items)) {
			$scope.context.items = [];
		}
		if($scope.context.items.length == 0) {
			$scope.context.items.push({content: ''});
		}
		$scope.context.addItem = function(){
			$scope.context.items.push({
				content: ''
			});
		};
		$scope.context.saveItem = function(item){
			item.saved = !item.saved;
		};
		$scope.context.removeItem = function(item) {
			require(['underscore'], function(_){
				$scope.context.items = _.without($scope.context.items, item);
				$scope.$digest();
			});
		};
		$scope.initEmotion = function(obj) {
			require(['util'], function(util){
				var elm = $('.emotion-triggers').eq(obj.$index);
				util.emotion(elm[0], elm.parent().parent().find('.content')[0], function(txt, elm, target){
					obj.item.content = $(target).val();
					$scope.$digest();
				});
			});
		};
        /*选择Emoji表情*/
		$scope.selectEmoji = function(obj) {
            require(['util'], function(util){
				var elm = $('.emoji-triggers').eq(obj.$index);
				var textbox = elm.parent().parent().find('.content')[0];
    			util.emojiBrowser(function(emoji){
    				var unshift = '[U+' + emoji.find("span").text() + ']';
    				var newstart = textbox.selectionStart + unshift.length;
    				var insertval = textbox.value.substr(0,textbox.selectionStart) + unshift + textbox.value.substring(textbox.selectionEnd);
    				obj.item.content = insertval;
    				$scope.$digest();
    			});
            });
		};
	};
	window.validateReplyForm = function(form, $, _, util, $scope) {
		var val = [];
		$scope.$digest();
		angular.forEach($scope.context.items, function(v, k){
			if($.trim(v.content) != '') {
				this.push(v);
			}
		}, val);
		if(val.length == 0) {
			util.message('请输入有效的回复内容.');
			return false;
		}
		$scope.$digest();
		val = angular.toJson(val);
		$(':hidden[name=replies]').val(val);
		return true;
	};
</script>			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1" />
			</div>
		</div>
	</form>
</div>
			</div>
		</div>
	</div>
	<script>
	/*
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
		*/
					</script>
	<script type="text/javascript">
		require(['bootstrap']);
	</script>

	</body>
</html>
