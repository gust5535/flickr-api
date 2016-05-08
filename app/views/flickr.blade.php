@extends('layout')

@section('content')
	<nav><a href="/" >Go to main page</a> | <a href="{{ URL::full() }}">Reload current page</a></nav>

    <h2>Flickr view content.</h2>
	<p>Select a method and call it using passed GET params</p>
	<form action="/" method="GET">
		<div class="clearfix">
			<fieldset>
				<legend>Method</legend>
				<select>
					<option value="0">Please select a method here</option>
					@foreach ($testMethods as $method)
						<option value="{{ $method }}">{{ $method }}</option>
					@endforeach
				</select>
			</fieldset>
			<fieldset>
				<legend>Request Formats</legend>
				<input type="radio" name="request-format" value="rest" id="request-format-rest" checked="checked" /> <label for="request-format-rest">REST</label> <br/>
				<input type="radio" name="request-format" value="xml-rpc" id="request-format-xml" /> <label for="request-format-xml">XML-RPC</label> <br/>
				<input type="radio" name="request-format" value="soap" id="request-format-soap" /> <label for="request-format-soap">SOAP</label> <br/>
			</fieldset>
			<fieldset>
				<legend>Response Formats</legend>
				<input type="radio" name="response-format" value="rest" id="response-format-rest" checked="checked" /> <label for="response-format-rest">REST</label> <br/>
				<input type="radio" name="response-format" value="xml-rpc" id="response-format-xml" /> <label for="response-format-xml">XML-RPC</label> <br/>
				<input type="radio" name="response-format" value="soap" id="response-format-soap" /> <label for="response-format-soap">SOAP</label> <br/>
				<input type="radio" name="response-format" value="json" id="response-format-json" /> <label for="response-format-json">JSON</label> <br/>
				<input type="radio" name="response-format" value="php" id="response-format-php" /> <label for="response-format-php">PHP</label> <br/>
			</fieldset>
		</div>
		<div>
			<button type="submit" class="btn btn-default">Submit query</button>
		</div>
	</form>
	<div>
		<p>Response:</p>
		<textarea style="width: 924px; height: 271px;">{{{ $sResult }}}</textarea>
	</div>
		
@stop
