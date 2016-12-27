$(function(){

  //提交按钮触发
  $("#material-add").on("click", function() {

    $.ajax({
      type: 'post',
      url: '/post',
      data:{
        'method': $('input[name="method"]').val(),
        'url': $('input[name="url"]').val(),
        'alias': $('input[name="alias"]').val(),
        'cookie': $('textarea[name="cookie"]').val(),
      },
      dataType: "json",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      success: function(data){
        if (data.errors) {
          var alert_block = '';
          for(var p in data.errors.validator){
            alert_block += data.errors.validator[p];
          }
          $.gritter.add({
            text: alert_block,
            class_name: 'growl-danger gritter-center',
            sticky: false,
            time: '2000'
          });
        } else {
          $('input[name="method"]').val('get');
          $('input[name="url"]').val('');
          $('input[name="alias"]').val('');
          $('textarea[name="cookie"]').val('');
          $.gritter.add({
            text: '<p align="center">提交成功 ( id:' + data.id + ' ) <a href="/">查看监控列表</a></p>',
            class_name: 'growl-success gritter-center',
            sticky: false,
            time: '2000'
          });
        }
      },
      error: function(xhr, type){
        $.gritter.add({
          text: 'Ajax error!',
          class_name: 'growl-danger gritter-center',
          sticky: false,
          time: '2000'
        });
      }
    });
    return false;
  });

  $("#postoken").on("click", function() {
    $.ajax({
      type: 'post',
      url: '/auth/postoken',
      data:{
        'email': $('input[name="email"]').val()
      },
      dataType: "json",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      success: function(data){
        if (data.errors) {
          var alert_block = '';
          for(var p in data.errors.validator){
            alert_block += data.errors.validator[p];
          }
          $.gritter.add({
            text: alert_block,
            class_name: 'growl-danger gritter-center',
            sticky: false,
            time: '2000'
          });
        } else {
          $.gritter.add({
            text: '<p align="center">邮件发送成功，请登录邮箱点击授权链接~</p>',
            class_name: 'growl-success gritter-center',
            sticky: false,
            time: '2000'
          });
        }
      },
      error: function(xhr, type){
        $.gritter.add({
          text: 'Ajax error!',
          class_name: 'growl-danger gritter-center',
          sticky: false,
          time: '2000'
        });
      }
    });
    return false;
  });

  $("#profile-btn").on("click", function() {
    $.ajax({
      type: 'post',
      url: '/profile/update',
      data:{
        'qwid': $('input[name="qwid"]').val()
      },
      dataType: "json",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      success: function(data){
        if (data.errors) {
          var alert_block = '';
          for(var p in data.errors.validator){
            alert_block += data.errors.validator[p];
          }
          $.gritter.add({
            text: alert_block,
            class_name: 'growl-danger',
            sticky: false,
            time: '2000'
          });
        } else {
          $.gritter.add({
            text: '<p align="center">更新成功</p>',
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
    return false;
  });

  //编辑按钮触发
  $("#material-edit").on("click", function() {

    $.ajax({
      type: 'post',
      url: '/update',
      data:{
        'method': $('input[name="method"]').val(),
        'alias': $('input[name="alias"]').val(),
        'cookie': $('textarea[name="cookie"]').val(),
        'uuid': $('input[name="uuid"]').val(),
      },
      dataType: "json",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      success: function(data){
        if (data.errors) {
          var alert_block = '';
          for(var p in data.errors.validator){
            alert_block += data.errors.validator[p];
          }
          $.gritter.add({
            text: alert_block,
            class_name: 'growl-danger gritter-center',
            sticky: false,
            time: '2000'
          });
        } else {
          $.gritter.add({
            text: '<p align="center">修改成功 <a href="/detail/'+ $('input[name="uuid"]').val() +'">点击查看</a></p>',
            class_name: 'growl-success gritter-center',
            sticky: false,
            time: '2000'
          });
        }
      },
      error: function(xhr, type){
        $.gritter.add({
          text: 'Ajax error!',
          class_name: 'growl-danger gritter-center',
          sticky: false,
          time: '2000'
        });
      }
    });
    return false;
  });

  $('#get-sel').click(function(){
    method_post(this, 'get');
  });
  $('#post-sel').click(function(){
    method_post(this, 'post');
  });

  $('#more').on("click", function() {
    var css = $(this).find('i').attr('class');
    if (css == 'fa fa-angle-double-down') {
      $(this).find('i').removeClass('fa-angle-double-down');
      $(this).find('i').addClass('fa fa-angle-double-up');
      $('#cookie-wrap').show();
    } else {
      $(this).find('i').removeClass('fa-angle-double-up');
      $(this).find('i').addClass('fa fa-angle-double-down');
      $('#cookie-wrap').hide();
    }

  });

});

//请求方式切换效果
function method_post(obj, method) {
  $(obj).removeClass('btn-default');
  $(obj).siblings().removeClass('btn-primary');
  $(obj).siblings().addClass('btn-default');
  $(obj).addClass('btn-primary');
  $('#method').val(method);
}
