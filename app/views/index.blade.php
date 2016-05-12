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
				<legend>Request Format</legend>
				{{ Form::radio('request-format', 'rest', true, array('id' => 'request-format-rest')) }}
				{{ Form::label('request-format-rest', 'REST') }}
				<br/>
				{{ Form::radio('request-format', 'xmlrpc', (Input::get('request-format')=='xmlrpc'), array('id' => 'request-format-xmlrpc', 'disabled' => 'disabled')) }}
				{{ Form::label('request-format-xmlrpc', 'XML-RPC *') }} 
				<br/>
				{{ Form::radio('request-format', 'soap', (Input::get('request-format')=='soap'), array('id' => 'request-format-soap', 'disabled' => 'disabled')) }}
				{{ Form::label('request-format-soap', 'SOAP *') }}
				<br/>
				<div>
					<p class="small text-muted">* - this format is not supported at the moment</p>
				</div>
			</fieldset>
			<fieldset>
				<legend>Response Format</legend>
				{{ Form::radio('response_format', 'rest', (Input::get('response_format', 'rest')=='rest'), array('id' => 'response-format-rest')) }}
				{{ Form::label('response-format-rest', 'REST') }}
				<br/>
				{{ Form::radio('response_format', 'xmlrpc', (Input::get('response_format')=='xmlrpc'), array('id' => 'response-format-xmlrpc')) }}
				{{ Form::label('response-format-xmlrpc', 'XML-RPC') }} 
				<br/>
				{{ Form::radio('response_format', 'soap', (Input::get('response_format')=='soap'), array('id' => 'response-format-soap')) }}
				{{ Form::label('response-format-soap', 'SOAP') }}
				<br/>
				{{ Form::radio('response_format', 'json', (Input::get('response_format')=='json'), array('id' => 'response-format-json')) }}
				{{ Form::label('response-format-json', 'JSON') }}
				<br/>
				{{ Form::radio('response_format', 'php_serial', (Input::get('response_format')=='php_serial'), array('id' => 'response-format-php')) }}
				{{ Form::label('response-format-php', 'PHP *') }}
				<br/>
				<div>
					<p class="small text-muted">* - the API response will be in PHP serialized format</p>
				</div>
			</fieldset>
		</div>
		<div id="method-details" class="bs-callout bs-callout-info collapse">
			<div>Here are details for the selected method - <b class="selected-method"></b>:</div>
			<div class="required-parameters"></div>
			<div class="optional-parameters"></div>
			<div class="short-info"></div>
			<div>{{ link_to('#', 'See details on the oficial page', array('class' => 'btn btn-info doc-url', 'target' => '_blank')) }}</div>
		</div>
		<div>
			<p>Additional parameters*:</p>
			{{ Form::text('additional_parameters', Input::get('additional_parameters'), array('style'=> 'width: 80%;')) }}
			<p class="small text-muted">* - here you could provide additional parameters in URI query format(e.g. <i>param_one=valueForParam1&amp;paramSecond=second value</i>)</p>
		</div>
		<div style="margin: 20px 0 10px;">
			{{ Form::submit('Submit query', array('class' => 'btn btn-primary', 'style' => 'margin-right: 20px;')) }}
			
			{{ link_to('/clearSession', 'Clear session', array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	<div>
		<p>Response:</p>
		{{ Form::textarea('response_result', $sResult, array('style'=> 'width: 80%; height: 271px;')) }}
	</div>
		
@stop
