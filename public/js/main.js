"use strict";
$( document ).ready( function( $ ) {

	$( 'select[name=method]' ).on( 'change', function() {
		var selectedElement = $(this).find("option:selected");
		var methodName = selectedElement.text();
		var postData = { method_name: methodName };
		//show spinner to indicate operation in progress
		$('#method-details').before('<i class="fa fa-spinner fa-spin" style="font-size:24px;color:#6699ff"></i>');
		//reset prevous results.
		$('textarea[name=response_result]').val('');
		
		$.getJSON("/getMethodDetails", postData).done(function( response ) {
			if (!response.success) {
				$('#method-details').html(response.msg);
			} else {
				$('#method-details .selected-method').html(methodName);
				var requiredParamsStr, optionalParamStr;
				requiredParamsStr = optionalParamStr = 'No parameters';
				if (response.data.required.length) {
					requiredParamsStr = '<code>'+response.data.required.join('</code>, <code>')+'</code>';
				}
				if (response.data.optional.length) {
					optionalParamStr = '<code>'+response.data.optional.join('</code>, <code>')+'</code>';
				}
				$('#method-details .required-parameters').html('<p><b>Required parameters</b>: '+requiredParamsStr+'</p>');
				$('#method-details .optional-parameters').html('<p><b>Optional parameters</b>: '+optionalParamStr+'</p>');
				$('#method-details .short-info').html('<p><b>General info</b>: '+response.data.short_info+'</p>');
				$('#method-details a.doc-url').prop('href', response.data.doc_url);
			}
			$('#method-details').collapse('show');
			//remove spiner
			$('.fa-spinner').remove();
		});
	} );

} );
