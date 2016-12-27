@extends('layouts.app')

@section('title', '首页')

@section('content')
<div class="container">
  <div class="panel panel-default panel-alt">
    <div class="panel-heading">
        <div class="panel-btns">
            <a href="" class="panel-close">&times;</a>
            <a href="" class="minimize">&minus;</a>
        </div><!-- panel-btns -->
        <h5 class="panel-title">监控任务列表</h5>
    </div><!-- panel-heading -->
    <div class="panel-body panel-table">
        <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th width="40px" nowrap style="text-align: center">#</th>
              <th width="200px" nowrap style="word-wrap:break-word;">别名</th>
              <th width="470px" nowrap style="word-wrap:break-word;">监控地址</th>
              <th width="50px" nowrap style="text-align: center">Method</th>
              <th width="80px" nowrap style="text-align: center">可用率 <span class="fa fa-exclamation-circle tooltips" data-toggle="tooltip" data-original-title="当前显示的可用率是本周的，旁边的剪头是代表与上周可用率对比的结果，红色代表涨，绿色代表跌与股市的颜色一致，呵呵~"></span></th>
              <th width="50px" nowrap style="text-align: center">快照</th>
              <th width="100px" nowrap style="text-align: center">相关操作</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div><!-- table-responsive -->
    </div><!-- panel-body -->
  </div><!-- panel -->
  <div class="row" align="center"><span style="display: none;cursor:pointer" class="mb20 pages" page="2">加载更多...</span></div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(function () {

  //默认载入列表
  getLists(0);

  //分页载入列表
  $(".pages").on("click", function() {
    var page = parseInt($(this).attr('page'));
    offset = (page-1)*10;
    page = page + 1;
    $(this).attr('page', page);
    getLists(offset);
  });

  //点击触发删除监控任务
  $('body').delegate('.delet','click',function(){
    var uuid = $(this).attr('uuid');
    $.ajax({
      url: "{{ url('api/del_job') }}/"+uuid,
      dataType: "json",
      success: function(data){
        if (data.errors) {
          $.gritter.add({
            text: '<p align="center">'+data.errors+'</p>',
            class_name: 'growl-danger',
            sticky: false,
            time: '2000'
          });
          $('#td-'+uuid).popover('hide');
        } else {
          $.gritter.add({
            text: '<p align="center">'+data.message+'</p>',
            class_name: 'growl-success',
            sticky: false,
            time: '4000'
          });
          $('#td-'+uuid).popover('destroy');
          $('#tr-'+uuid).remove();
        }
      }
    });
  });

  //点击触发编辑监控任务
  $('body').delegate('.edit-job','click',function(){
    var uuid = $(this).attr('uuid');
    window.location.href = '/job/edit/' + uuid;
  });

  //点击触发抓取快照
  $('body').delegate('.snapshot_get','click',function(){
    var uuid = $(this).attr('uuid');
    $.ajax({
      url: "{{ url('snapshot') }}/"+uuid,
      dataType: "json",
      beforeSend: function(){
        $('#snapshot-'+uuid).html('<img src="//192.168.8.91/bracket/images/loaders/loader3.gif" alt="">');
        $('#snapshot-'+uuid).removeClass('snapshot_get');
      },
      success: function(data){
        if (data.errors) {
          $.gritter.add({
            text: data.errors,
            class_name: 'growl-danger',
            sticky: false,
            time: '2000'
          });
          $('#snapshot-'+uuid).html('<i class="fa fa-eye-slash">');
          $('#snapshot-'+uuid).addClass('snapshot_get');
        } else {
          $.gritter.add({
            text: '<p align="center">快照获取成功</p>',
            class_name: 'growl-success',
            sticky: false,
            time: '2000'
          });
          $('#snapshot-'+uuid).removeClass('btn-default');
          $('#snapshot-'+uuid).removeClass('snapshot_get');
          $('#snapshot-'+uuid).addClass('btn-white');
          $('#snapshot-'+uuid).html('<i class="fa fa-eye">');
          $('#snapshot-'+uuid).attr('href','/cache/'+uuid);
        }
      }
    });
  });

  //点击触发
  $('body').delegate('.working','click',function(){
    var uuid = $(this).attr('uuid');
    if ($(this).hasClass('play')) {
      var act = 'play';
      var act_f = 'stop';
      var msg = '开启成功';
    } else {
      var act = 'stop';
      var act_f = 'play';
      var msg = '关闭成功';
    }
    $.ajax({
      url: "{{ url('working') }}/"+uuid+'/'+act,
      dataType: "json",
      beforeSend: function(){
        $('#working-'+uuid).removeClass('working');
      },
      success: function(data){
        if (data.errors) {
          $.gritter.add({
            text: data.errors,
            class_name: 'growl-danger',
            sticky: false,
            time: '2000'
          });
          $('#working-'+uuid).html('<i class="fa fa-play">');
          $('#working-'+uuid).addClass('working');
        } else {
          $.gritter.add({
            text: '<p align="center">'+msg+'</p>',
            class_name: 'growl-success',
            sticky: false,
            time: '2000'
          });
          $('#working-'+uuid).html('<i class="fa fa-'+act_f+'">');
          $('#working-'+uuid).addClass('working');
          $('#working-'+uuid).addClass(act_f);
          $('#working-'+uuid).removeClass(act);
        }
      }
    });
  });

  // Star
  $('body').delegate('.star','click',function(){
    var uuid = $(this).attr('uuid');
    if(!$(this).hasClass('star-checked')) {
      $.ajax({
        type: "GET",
        dataType: "JSON",
        url: "/star/add/"+uuid,
        success: function(data){
          if (data.errors) {
            $.gritter.add({
              text: '<p align="center">'+data.errors+'</p>',
              class_name: 'growl-danger',
              sticky: false,
              time: '2000'
            });
          } else {
            $('#star-'+uuid).addClass('star-checked');
            $.gritter.add({
              text: '<p align="center">关注成功</p>',
              class_name: 'growl-success',
              sticky: false,
              time: '2000'
            });
          }
        },
        error: function(xhr, type){
        $.gritter.add({
          text: 'Ajax error!',
          class_name: 'growl-danger',
          sticky: false,
          time: '2000'
        });
      }
      });
    } else {
      $.ajax({
        type: "GET",
        dataType: "JSON",
        url: "/star/remove/"+uuid,
        success: function(data){
          if (data.errors) {
            $.gritter.add({
              text: '<p align="center">'+data.errors+'</p>',
              class_name: 'growl-danger',
              sticky: false,
              time: '2000'
            });
          } else {
            $('#star-'+uuid).removeClass('star-checked');
            $.gritter.add({
              text: '<p align="center">取消成功</p>',
              class_name: 'growl-success',
              sticky: false,
              time: '2000'
            });
          }
        },
        error: function(xhr, type){
        $.gritter.add({
          text: 'Ajax error!',
          class_name: 'growl-danger',
          sticky: false,
          time: '2000'
        });
      }
      });
    }
    return false;
  });

});

function getLists(offset) {
  $.ajax({
    url: "{{ url('board/api') }}/"+offset,
    dataType: "json",
    beforeSend: function(){
      $('tbody').append('<tr id="loading"><td colspan="7" align="center"><img src="//192.168.8.91/bracket/images/loaders/loader19.gif" alt=""></td></tr>');
    },
    success: function(data){
      $('#loading').remove();
      if (data) {
        var i = 0;
        for(var p in data){
          i++;
          var snapshot = '-slash';
          var snapshot_bt = 'default';
          var snapshot_url = 'javascript:void(0);';
          var snapshot_title = '获取快照';
          var snapshot_get = ' snapshot_get';
          var working = 'play';
          var star = '';
          if (data[p]['workding'] == 1) {
            working = 'stop';
          }
          if (data[p]['star'] == 1) {
            star = ' star-checked';
          }
          if (data[p]['snapshot_flag'] == 1) {
            snapshot = '';
            snapshot_bt = 'white';
            snapshot_url = '/cache/'+data[p]['uuid'];
            snapshot_title = '查看快照';
            snapshot_get = '';
          }
          if (data[p]['candle']) {
            var now = parseFloat(JSON.parse(data[p]['candle']).now);
            var last = parseFloat(JSON.parse(data[p]['candle']).last);
            if ( now > last ) {
              var flag = '<span style="color:#ff0000">↑</span>';
            } else if ( now == last ) {
              var flag = '';
            } else {
              var flag = '<span style="color:#00ff00">↓</span>';
            }
            var kyl = JSON.parse(data[p]['candle']).now + '% '+flag;
          } else {
            var kyl = '';
          }
          $('tbody').append('<tr id="tr-'+ data[p]['uuid'] +'"><td align="center" class="table-board"><a href="javascript:void(0);" class="star'+star+'" id="star-'+ data[p]['uuid'] +'" style="line-height:2.5em" title="关注" uuid="'+ data[p]['uuid'] +'"><i class="glyphicon glyphicon-heart"></i></a></td><td style="line-height:2.5em"><a href="/detail/'+ data[p]['uuid'] +'" title="'+ data[p]['alias'] +'">'+ data[p]['alias'] +'</a></td><td style="line-height:2.5em"><a href="'+ data[p]['url'] +'" target="_blank" title="'+ data[p]['url'] +'">'+ data[p]['url'] +'</a></td><td align="center" style="text-transform:uppercase; line-height:2.5em">'+ data[p]['method'] +'</td><td align="center"  style="line-height:2.5em">'+ kyl +'</td><td align="center"><a id="snapshot-'+ data[p]['uuid'] +'" title="'+snapshot_title+'" href="'+snapshot_url+'" class="btn btn-sm btn-'+snapshot_bt+snapshot_get+'" type="button" uuid="'+ data[p]['uuid'] +'"><i class="fa fa-eye'+snapshot+'"></a></td><td class="table-action"><div class="btn-group" style="margin-bottom: 0px;"><button id="working-'+ data[p]['uuid'] +'" class="btn btn-sm btn-white working '+working+'" type="button" uuid="'+ data[p]['uuid'] +'"><i class="fa fa-'+working+'"></i></button><button class="btn btn-sm btn-white edit-job" type="button" uuid="'+ data[p]['uuid'] +'"><i class="fa fa-pencil"></i></button><button id="td-'+ data[p]['uuid'] +'" class="btn btn-sm btn-white" type="button" data-container="body" data-toggle="popover" data-html="true" data-placement="left" data-content="<p><i class=\'fa fa-exclamation-circle\'></i> 你确定要删除该监控任务吗？如果删除，相应的监控记录也会随之删除。</p><button class=\'btn btn-primary btn-xs delet\' uuid=\''+ data[p]['uuid'] +'\'>我确定</button>"><i class="fa fa-trash-o"></i></button></div></td></tr>');
        }
        if (i >= 10 ) {
          $(".pages").css('display','block');
        } else {
          $('.pages').parent().html('没有更多数据了');
        }
        $("[data-toggle='popover']").popover();
      } else {
        $('.pages').parent().html('没有更多数据了');
      }
    }
  });
}
</script>
@endsection
