<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="/favicon.ico">
  <meta name="_token" content="{{ csrf_token() }}"/>
  <title>@yield('title') - 猫头鹰状态监控助手</title>
  <link href="//192.168.8.91/bracket/css/style.default.css" rel="stylesheet">
  <link href="//192.168.8.91/bracket/css/jquery.gritter.css" rel="stylesheet" />
  <link href="/css/navbar.css" rel="stylesheet">
</head>

<body>

<!-- Static navbar -->
<nav class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/"><img src="/logo.png" width="24" /> 猫头鹰 - 状态监控助手</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li class="{{ active_class(if_uri('/')) }}"><a href="{{ url('') }}"><i class="fa fa-home"></i> <span>首页</span></a></li>
        <li class="{{ active_class(if_uri('add')) }}"><a href="{{ url('add') }}"><i class="fa fa-plus"></i> <span>新增监控</span></a></li>
        <?php if (isset($monitor)) { echo '<li class="active"><a href="#"><i class="fa fa-bar-chart-o"></i> <span>监控记录</span></a></li>'; } ?>
        <?php if (isset($html)) { echo '<li class="active"><a href="#"><i class="fa fa-eye"></i> <span>快照</span></a></li>'; } ?>
        <li><a href="https://jiangbianwanghai.gitbooks.io/mty-monitor-user-guide/content/chapter1.html" target="_blank"><i class="fa fa-book"></i> <span>使用指南</span></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
      <!-- Authentication Links -->
      @if (Auth::guest())
      <li><a href="{{ url('/auth/email') }}" ><i class="fa fa-user"></i> <span>获取操作授权</span></a></li>
      @else
      <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            {{ Auth::user()->email }} <span class="caret"></span>
          </a>

          <ul class="dropdown-menu" role="menu">
            <li><a href="{{ url('/profile') }}"><i class="fa fa-btn fa-gear"></i> 设置</a></li>
            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i> 退出</a></li>
          </ul>
      </li>
      @endif
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>

@yield('content')


<script src="//192.168.8.91/bracket/js/jquery-1.11.1.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery-migrate-1.2.1.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery-ui-1.10.3.min.js"></script>
<script src="//192.168.8.91/bracket/js/bootstrap.min.js"></script>
<script src="//192.168.8.91/bracket/js/modernizr.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery.sparkline.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery.cookies.js"></script>
<script src="//192.168.8.91/bracket/js/toggles.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery.gritter.min.js"></script>
<script src="//192.168.8.91/bracket/js/jquery.validate.min.js"></script>
<script src="//192.168.8.91/bracket/js/custom.js"></script>
<script src="/js/custom.js?{{ time() }}"></script>
@yield('js')

</body>
</html>
