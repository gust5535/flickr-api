@extends('layout')

@section('content')
	<nav>{{ link_to('/', 'Go to main page', array('class' => 'btn btn-link')) }}</nav>
	
    <h2>Flickr view content.</h2>
	<p>Select a method and call it using passed GET params</p>
	{{ Form::open(array('method' => 'POST')) }}
		<div class="clearfix">
			<fieldset>
				<legend>Method</legend>
				{{ Form::select('method', $testMethods, Input::get('method')) }}
				 
			</fieldset>
			<fieldset>
				<legend>Request Formats</legend>
				{{ Form::radio('request-format', 'rest', true, array('id' => 'request-format-rest')) }}
				{{ Form::label('request-format-rest', 'REST') }}
				<br/>
				{{ Form::radio('request-format', 'xmlrpc', (Input::get('request-format')=='xmlrpc'), array('id' => 'request-format-xmlrpc', 'disabled' => 'disabled')) }}
				{{ Form::label('request-format-xmlrpc', 'XML-RPC') }} 
				<br/>
				{{ Form::radio('request-format', 'soap', (Input::get('request-format')=='soap'), array('id' => 'request-format-soap', 'disabled' => 'disabled')) }}
				{{ Form::label('request-format-soap', 'SOAP') }}
				<br/>
			</fieldset>
			<fieldset style="width: 450px;">
				<legend>Response Formats</legend>
				{{ Form::radio('response-format', 'rest', (Input::get('response-format', 'rest')=='rest'), array('id' => 'response-format-rest')) }}
				{{ Form::label('response-format-rest', 'REST') }}
				<br/>
				{{ Form::radio('response-format', 'xmlrpc', (Input::get('response-format')=='xmlrpc'), array('id' => 'response-format-xmlrpc')) }}
				{{ Form::label('response-format-xmlrpc', 'XML-RPC') }} 
				<br/>
				{{ Form::radio('response-format', 'soap', (Input::get('response-format')=='soap'), array('id' => 'response-format-soap')) }}
				{{ Form::label('response-format-soap', 'SOAP') }}
				<br/>
				{{ Form::radio('response-format', 'json', (Input::get('response-format')=='json'), array('id' => 'response-format-json')) }}
				{{ Form::label('response-format-json', 'JSON') }}
				<br/>
				{{ Form::radio('response-format', 'php_serial', (Input::get('response-format')=='php_serial'), array('id' => 'response-format-php')) }}
				{{ Form::label('response-format-php', 'PHP') }} <i>the API response will be in PHP serialized format</i>
				<br/>
			</fieldset>
		</div>
		<div>
			{{ Form::submit('Submit query', array('class' => 'btn btn-default', 'style' => 'margin-right: 20px;')) }}
			
			{{ link_to('/clearSession', 'Clear session', array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	<div>
		<p>Response:</p>
		{{ Form::textarea('response-result', $sResult, array('style'=> 'width: 924px; height: 271px;')) }}
	</div>
		
@stop
