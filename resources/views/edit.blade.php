@extends('layouts.app')

@section('title', '修改监控任务')

@section('content')
<div class="container">
<form id="basicForm" action="form-validation.html" class="form-horizontal">
  <div class="panel panel-default">
      <div class="panel-heading">
        <div class="panel-btns">
          <a href="" class="panel-close">&times;</a>
          <a href="" class="minimize">&minus;</a>
        </div>
        <h4 class="panel-title">修改监控任务</h4>
        <p>为了保证数据的准确性，监控地址一旦创建则不能修改。</p>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="col-sm-2 control-label">监控地址 <span class="asterisk">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="url" class="form-control" placeholder="请输入监控地址" disabled="" value="{{ $row->url }}" required />
            <span class="help-block"><i class="fa fa-exclamation-circle"></i> 例：http://www.baidu.com or https://www.baidu.com/s?wd=highcharts&rsv_spt=1 两种格式均符合要求</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">别名</label>
          <div class="col-sm-10">
            <input type="text" name="alias" class="form-control" placeholder="请输入别名" value="{{ $row->alias }}" />
            <span class="help-block"><i class="fa fa-exclamation-circle"></i> 添加别名有助于区分不同的监控地址</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">请求方式 <span class="asterisk">*</span></label>
          <div class="col-sm-10">
            <div class="btn-group">
              <button type="button" id="get-sel" class="btn btn-<?php echo $row->method == 'get' ? 'primary' : 'default'; ?> btn-sm">GET</button>
              <button type="button" id="post-sel" class="btn btn-<?php echo $row->method == 'post' ? 'primary' : 'default'; ?> btn-sm">POST</button>
            </div>
            <span class="help-block"><i class="fa fa-info-circle"></i> 默认使用GET方法，当然你也可以自行选择。</span>
          </div>
        </div>
        <input type="hidden" name="method" id="method" value="{{ $row->method }}">
        <input type="hidden" name="uuid" id="uuid" value="{{ $row->uuid }}">
        <div class="form-group">
          <div class="col-sm-2"></div>
          <div class="col-sm-10"><a href="javascript:void(0);" id="more" style="text-decoration:none;">设置更多参数 <i class="fa fa-angle-double-down"></i></a></div>
        </div>

        <div class="form-group" style="display: none" id="cookie-wrap">
          <label class="col-sm-2 control-label">Cookie</label>
          <div class="col-sm-10">
            <textarea rows="5" class="form-control" name="cookie" placeholder="请按照规则输入Cookies">{{ $row->cookie }}</textarea>
            <span class="help-block"><i class="fa fa-exclamation-circle"></i> 格式：key1=val1;key2=val2，比如：token=d906b69209d9de92789fcd65a1a5d210; pvid=954970634; flv=10.0</span>
          </div>
        </div>
      </div><!-- panel-body -->
      <div class="panel-footer">
        <div class="row">
          <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary" id="material-edit">提交</button>
          </div>
        </div>
      </div>

  </div><!-- panel -->
</form>
</div>
@endsection
