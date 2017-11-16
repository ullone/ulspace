<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ulspace-sources</title>

	<!-- <link href="{{ asset('/css/home.css') }}" rel="stylesheet"> -->
	<link href="/css/home.css" rel="stylesheet">

	<link href="/css/bootstrap.min.css" rel="stylesheet">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<header>
		<div class="collapse bg-dark" id="navbarHeader">
	</div>
		<div class="navbar navbar-dark bg-dark">
			<div class="container" style="padding-left: 0;padding-right: 0;">
				<div class="lft">
					<a href="#" class="navbar-brand">电影</a>
					<a href="#" class="navbar-brand">音乐</a>
					<a href="#" class="navbar-brand">相册</a>
					<a href="#" class="navbar-brand">小说</a>
					<a href="#" class="navbar-brand">记事本</a>
					<a href="#" class="navbar-brand">关于</a>
			  </div>
				<div class="rgt">
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
				</div>
			</div>
		</div>
	</header>

	@yield('content')

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="/js/app.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>
