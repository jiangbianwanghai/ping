@extends('layouts.app')

@section('title', '编辑个人资料')

@section('content')
<div class="container">
<form class="form-signin" id="basicForm" action="form-validation.html" class="form-horizontal">
  <div class="panel panel-default">
      <div class="panel-heading">
        <div class="panel-btns">
          <a href="" class="panel-close">&times;</a>
          <a href="" class="minimize">&minus;</a>
        </div>
        <h4 class="panel-title">编辑个人资料</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="col-sm-2 control-label">邮箱</label>
          <div class="col-sm-10">
            <input type="text" name="url" class="form-control" value="{{ $user->email }}" disabled="" />
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">企微ID</label>
          <div class="col-sm-10">
            <input type="text" name="qwid" class="form-control" value="{{ $user->qwid }}" placeholder="请输入企业微信帐号" />
            <span class="help-block"><i class="fa fa-exclamation-circle"></i> 报警提醒会发送到你的企业微信中</span>
          </div>
        </div>
      </div><!-- panel-body -->
      <div class="panel-footer">
        <div class="row">
          <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary" id="profile-btn">提交</button>
          </div>
        </div>
      </div>

  </div><!-- panel -->
</form>
</div>
@endsection
