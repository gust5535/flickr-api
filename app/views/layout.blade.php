<!DOCTYPE html>
 <html class="no-js"> 
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Flickr API</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Bootstrap -->
		{{ HTML::style('packages/bootstrap/css/bootstrap.min.css') }}

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		{{ HTML::style('css/main.css') }}
    </head>
    <body>
		<div class="container">
			<h1>Flickr API based on Laravel framework</h1>
			<p>{{-- Hello world! This is HTML5 Boilerplate.--}}</p>
			<p>The Flickr API is available for non-commercial use by outside developers. Commercial use is possible by prior arrangement.</p>

		
			@yield('content')
		</div>
		
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		{{ HTML::script('packages/bootstrap/js/bootstrap.min.js') }}
		{{ HTML::script('js/main.js') }}

        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
    </body>
</html>