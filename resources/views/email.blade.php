@extends('layouts.app')

@section('title', '获取授权链接')

@section('content')
<div class="container">
<form class="form-signin" id="basicForm" action="form-validation.html">
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="panel-btns">
        <a href="" class="panel-close">&times;</a>
        <a href="" class="minimize">&minus;</a>
      </div>
      <h4 class="panel-title">获取授权链接</h4>
      <p>拿到授权后你可以进行 <b>关注任务</b> 等操作</p>
    </div>
    <div class="panel-body">
    <div class="form-group">
      <label for="email" class="sr-only">邮箱地址</label>
      <input type="text" id="email" name="email" class="form-control" placeholder="邮箱地址" required autofocus>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" id="remember" name="remember" value="1" checked="checked" disabled="true"> Cookie保存一年
        </label>
      </div>
    </div><!-- panel-body -->
    <div class="panel-footer">
      <div class="row" align="right">
          <button class="btn btn-primary" id="postoken">提交</button>
      </div>
    </div>
  </div><!-- panel -->
</form>
</div>
@endsection
