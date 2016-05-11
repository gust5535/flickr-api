@extends('layout')

@section('content')

    <h2>The Error page</h2>
	<p>This is a custom error page, if you see it something went wrong. Please try again.</p>
	
	<nav>
		{{ link_to('/', 'Click here to try again', array('class' => 'btn btn-primary')) }}
	</nav>
	
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2"><p>The error message: </p></div>
		<div class="col-md-10">
			{{{ $message ? $message : 'No details.' }}}
		</div>
	</div>
@stop
