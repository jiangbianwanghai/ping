@extends('layouts.app')

@section('title', '监控详情')

@section('content')
<div class="container">
<div class="row">
    <div class="col-sm-9" style="line-height: 45px;">可在右侧下拉列表中切换任务</div>
    <div class="col-sm-3">
        <select class="select2" data-placeholder="在不同任务间切换">
        <option>{{ $title }}</option>
        </select>
    </div>
</div>
<div class="mb20"></div>
<div class="row">
    <div class="col-sm-6 col-md-3">
      <div class="panel panel-success panel-stat">
        <div class="panel-heading">
          <div class="stat">
            <div class="row">
              <div class="col-xs-4">
                <img src="//192.168.8.91/bracket/images/is-user.png" alt="" />
              </div>
              <div class="col-xs-8">
                <small class="stat-label">监控时长</small>
                <h1 id="jksc">...</h1>
              </div>
            </div><!-- row -->
            <div class="mb15"></div>
            <small class="stat-label">起始于</small>
            <h4 id="jksc-a">...</h4>
          </div><!-- stat -->
        </div><!-- panel-heading -->
      </div><!-- panel -->
    </div><!-- col-sm-6 -->
    <div class="col-sm-6 col-md-3">
      <div class="panel panel-danger panel-stat">
        <div class="panel-heading">
          <div class="stat">
            <div class="row">
              <div class="col-xs-4">
                <img src="//192.168.8.91/bracket/images/is-document.png" alt="" />
              </div>
              <div class="col-xs-8">
                <small class="stat-label">一周内可用率</small>
                <h1 id="kyl">...</h1>
              </div>
            </div><!-- row -->
            <div class="mb15"></div>
            <small class="stat-label">整个监控周期可用率</small>
            <h4 id="kyl_all">...</h4>
          </div><!-- stat -->
        </div><!-- panel-heading -->
      </div><!-- panel -->
    </div><!-- col-sm-6 -->
    <div class="col-sm-6 col-md-3">
      <div class="panel panel-primary panel-stat">
        <div class="panel-heading">
          <div class="stat">
            <div class="row">
              <div class="col-xs-4">
                <img src="//192.168.8.91/bracket/images/is-document.png" alt="" />
              </div>
              <div class="col-xs-8">
                <small class="stat-label">最短响应时间</small>
                <h1 id="min">...</h1>
              </div>
            </div><!-- row -->
            <div class="mb15"></div>
            <small class="stat-label">发生在</small>
            <h4 id="min_time">...</h4>
          </div><!-- stat -->
        </div><!-- panel-heading -->
      </div><!-- panel -->
    </div><!-- col-sm-6 -->
    <div class="col-sm-6 col-md-3">
      <div class="panel panel-dark panel-stat">
        <div class="panel-heading">
          <div class="stat">
            <div class="row">
              <div class="col-xs-4">
                <img src="//192.168.8.91/bracket/images/is-money.png" alt="" />
              </div>
              <div class="col-xs-8">
                <small class="stat-label">最长响应时间</small>
                <h1 id="max">...</h1>
              </div>
            </div><!-- row -->
            <div class="mb15"></div>
            <small class="stat-label">发生在</small>
            <h4 id="max_time">...</h4>
          </div><!-- stat -->
        </div><!-- panel-heading -->
      </div><!-- panel -->
    </div><!-- col-sm-6 -->
</div><!-- row -->

<!-- Nav tabs -->
<ul class="nav nav-tabs">
  <li class="active"><a href="#home" data-toggle="tab"><strong>响应时间 <span class="fa fa-exclamation-circle tooltips" data-toggle="tooltip" data-original-title="响应时间的单位为毫秒"></span></strong></a></li>
  <li><a href="#kyltb" data-toggle="tab" id="kyltab"><strong>可用率 <span class="fa fa-exclamation-circle tooltips" data-toggle="tooltip" data-original-title="可用率的定义是状态码不在200-400之间的为不可用（不包括200）。"></span></strong></a></li>
  <li><a href="#ztmtb" data-toggle="tab" id="ztmtab"><strong>HTTP状态码 <span class="fa fa-exclamation-circle tooltips" data-toggle="tooltip" data-original-title="此功能在2016年10月10日开发完成，因此之前的数据为空"></span></strong></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content mb30">
  <div class="tab-pane active" id="home">
    <div class="row">
      <div class="col-sm-12">
        <div id="container" style="height: 400px;">图表正在加载中.....</div>
      </div><!-- col-sm-12 -->
    </div><!-- row -->
  </div>
  <div class="tab-pane" id="kyltb">
    <div class="row">
      <div class="col-sm-12">
        <div id="container-2" style="height: 400px;">图表正在加载中.....</div>
      </div><!-- col-sm-12 -->
    </div><!-- row -->
  </div>
  <div class="tab-pane" id="ztmtb">
    <div class="row">
      <div class="col-sm-12">
        <div id="container-3" style="height: 400px;">图表正在加载中.....</div>
      </div><!-- col-sm-12 -->
    </div><!-- row -->
  </div>
  <div class="mb20"></div>
  <div id="list-table" style="display: none">
  <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>时间</th>
          <th>响应时间(单位：毫秒)</th>
          <th>是否可用</th>
          <th>状态码</th>
        </tr>
      </thead>
    <tbody></tbody>
  </table>
  </div>
</div>
<div class="row" align="center"><span style="cursor:pointer" class="mb20 pages" page="1" uuid="{{ $uuid }}">点击载入数据表格</span></div>
</div>
@endsection

@section('js')
<script src="//192.168.8.91/bracket/js/select2.min.js"></script>
<script type="text/javascript">
$(function () {
    $(".select2").bind("change",function(){
        var uuid = $(this).val();
        window.location.href = '/detail/'+uuid;
    });
    $(".select2").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });

    //载入摘要信息
    $.getJSON("{{ url('detail/sumary/'.$uuid) }}?callback=?", function (data) {
        $('#jksc').text(data.jksc);
        $('#jksc-a').text(data.jksc_a);
        $('#kyl').text(data.kyl);
        $('#kyl_all').text(data.kyl_all);
        $('#min').text(data.min);
        $('#min_time').text(data.min_time);
        $('#max').text(data.max);
        $('#max_time').text(data.max_time);
    });

    Highcharts.setOptions({
        global: { useUTC: false  },
        lang : {
            shortMonths : ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            weekdays : ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六']
        },
    });

    //载入相应时间图表
    $.getJSON("{{ url('detail/xysj/'.$uuid) }}?callback=?", function (data) {
        $('#container').highcharts('StockChart', {
            chart: {
                zoomType: 'x'
            },
            title : {
                text : '{{ $title }} 的响应时间'
            },
            tooltip: {
                xDateFormat: '%Y-%m-%d %a %H:%M',
                shared: true
            },
            subtitle: {
                text: '{{ $url }}'
            },
            exporting:{
　　　　　　　　　　 enabled:false
            },
            credits:{
                enabled:false
            },
            rangeSelector: {
                inputEnabled:false,
                buttons: [{
                    type: 'day',
                    count: 1,
                    text: '1天'
                }, {
                    type: 'week',
                    count: 1,
                    text: '1周'
                }, {
                    type: 'month',
                    count: 1,
                    text: '1月'
                }, {
                    type: 'month',
                    count: 6,
                    text: '半年'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1年'
                }, {
                    type: 'all',
                    text: '全部'
                }],
                selected: 0
            },
            yAxis: {
                title: {
                    text: '响应时间（毫秒）'
                }
            },
            series : [{
                name : '响应时间',
                data : data,
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });

    //载入可用率图表
    $('#kyltab').click(function(){
        if (!$(this).hasClass('loaded')) {
            $.getJSON("{{ url('detail/kyl/'.$uuid) }}?callback=?", function (data) {
                $('#container-2').highcharts('StockChart', {
                    chart: {
                        zoomType: 'x'
                    },
                    title : {
                        text : '{{ $title }} 可用率'
                    },
                    tooltip: {
                        xDateFormat: '%Y-%m-%d %a %H:%M',
                        shared: true
                    },
                    subtitle: {
                        text: '{{ $url }}'
                    },
                    exporting:{
        　　　　　　　　　　 enabled:false
                    },
                    credits:{
                        enabled:false
                    },
                    rangeSelector: {
                        inputEnabled:false,
                        buttons: [{
                            type: 'day',
                            count: 1,
                            text: '1天'
                        }, {
                            type: 'week',
                            count: 1,
                            text: '1周'
                        }, {
                            type: 'month',
                            count: 1,
                            text: '1月'
                        }, {
                            type: 'month',
                            count: 6,
                            text: '半年'
                        }, {
                            type: 'year',
                            count: 1,
                            text: '1年'
                        }, {
                            type: 'all',
                            text: '全部'
                        }],
                        selected: 0
                    },
                    yAxis: {
                        title: {
                            text: '可用率'
                        }
                    },
                    series : [{
                        name : '是否可用',
                        data : data,
                        step: true,
                        tooltip: {
                            valueDecimals: 2
                        }
                    }]
                });
            });
            $(this).addClass('loaded');
        }
    });

    //载入状态码图表
    $('#ztmtab').click(function(){
        if (!$(this).hasClass('loaded')) {
            $.getJSON("{{ url('detail/ztm/'.$uuid) }}?callback=?", function (data) {
                $('#container-3').highcharts('StockChart', {
                    chart: {
                        zoomType: 'x'
                    },
                    title : {
                        text : '{{ $title }} 可用率'
                    },
                    tooltip: {
                        xDateFormat: '%Y-%m-%d %a %H:%M',
                        shared: true
                    },
                    subtitle: {
                        text: '{{ $url }}'
                    },
                    exporting:{
        　　　　　　　　　　 enabled:false
                    },
                    credits:{
                        enabled:false
                    },
                    rangeSelector: {
                        inputEnabled:false,
                        buttons: [{
                            type: 'day',
                            count: 1,
                            text: '1天'
                        }, {
                            type: 'week',
                            count: 1,
                            text: '1周'
                        }, {
                            type: 'month',
                            count: 1,
                            text: '1月'
                        }, {
                            type: 'month',
                            count: 6,
                            text: '半年'
                        }, {
                            type: 'year',
                            count: 1,
                            text: '1年'
                        }, {
                            type: 'all',
                            text: '全部'
                        }],
                        selected: 0
                    },
                    yAxis: {
                        title: {
                            text: '可用率'
                        }
                    },
                    series : [{
                        name : '是否可用',
                        data : data,
                        step: true,
                        tooltip: {
                            valueDecimals: 2
                        }
                    }]
                });
            });
            $(this).addClass('loaded');
        }
    });

    //载入别名列表
    $.ajax({
        url: "{{ url('board/api/0') }}",
        dataType: "json",
        success: function(data){
          if (data) {
            for(var p in data){
              $('select').append('<option value="'+ data[p]['uuid'] +'">'+ data[p]['alias'] +'</option>');
            }
          } else {
            $('.select2').remove();
          }
        }
    });

    //分页载入列表
  $(".pages").on("click", function() {
    $('#list-table').css('display','block');
    $(".pages").text('载入更多...');
    var uuid = $(this).attr('uuid');
    var page = parseInt($(this).attr('page'));
    offset = (page-1)*20;
    page = page + 1;
    $(this).attr('page', page);
    getLists(uuid, offset);
  });
});

function getLists(uuid, offset) {
  $.ajax({
    url: "{{ url('detail/table') }}/"+uuid+"/"+offset,
    dataType: "json",
    beforeSend: function(){
      $('tbody').append('<tr id="loading"><td colspan="5" align="center"><img src="//192.168.8.91/bracket/images/loaders/loader19.gif" alt=""></td></tr>');
    },
    success: function(data){
      $('#loading').remove();
      if (data) {
        var i = 0;
        for(var p in data){
          i++;
          var newDate = new Date();
          newDate.setTime(data[p]['created_at'] * 1000);
          $('tbody').append('<tr><td>'+data[p]['id']+'</td><td>'+newDate.format('yyyy-MM-dd h:m:s')+'</td><td>'+data[p]['request_time']+'</td><td>'+ data[p]['status'] +'</td><td>'+data[p]['http_code']+'</td></tr>');
        }
        if (i >= 20 ) {
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
Date.prototype.format = function(format) {
       var date = {
              "M+": this.getMonth() + 1,
              "d+": this.getDate(),
              "h+": this.getHours(),
              "m+": this.getMinutes(),
              "s+": this.getSeconds(),
              "q+": Math.floor((this.getMonth() + 3) / 3),
              "S+": this.getMilliseconds()
       };
       if (/(y+)/i.test(format)) {
              format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
       }
       for (var k in date) {
              if (new RegExp("(" + k + ")").test(format)) {
                     format = format.replace(RegExp.$1, RegExp.$1.length == 1
                            ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
              }
       }
       return format;
}
</script>
<script src="http://cdn.hcharts.cn/highstock/highstock.js"></script>
<script src="http://cdn.hcharts.cn/highstock/modules/exporting.js"></script>
@endsection
