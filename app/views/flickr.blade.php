@extends('layout')

@section('content')
	<nav><a href="/" >Go to main page</a> | <a href="{{ URL::full() }}">Reload current page</a></nav>

    <h2>Flickr view content.</h2>
	<p>Select a method and call it using passed GET params</p>
	<div>
		<select>
			<option value="0">Please select a method here</option>
			@foreach ($testMethods as $method)
				<option value="{{ $method }}">{{ $method }}</option>
			@endforeach
		</select>
		<a href="#">Submit query</a>
	</div>
	<div>
		<p>Response:</p>
		<textarea style="width: 924px; height: 271px;">{{{ $sResult }}}</textarea>
	</div>
		
@stop
