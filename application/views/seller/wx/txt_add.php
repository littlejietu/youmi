
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
			
	<?php $this->load->view('seller/wx/inc/reply_js');?>
<div class="clearfix ng-cloak" id="js-reply-form" ng-controller="replyForm">
	<form id="reply-form" class="form-horizontal form" action="<?php echo SELLER_SITE_URL.'/wxreply/txt_save';?>" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<?php $this->load->view('seller/wx/inc/reply_inc');?>
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
