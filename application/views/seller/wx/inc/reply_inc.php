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