<?php
/** 
 * Main controller to work with Flickr API
 * 
 * @author Andriy Leshchuk <andriy.leshchuk@gmail.com>
 */

class FlickrController extends \BaseController {

	/**
	 * Display main dashboard.
	 *
	 * @return Response
	 */
	public function index()
	{
		$api = FlickrApi::getInstance();
		$testMethods = $api->getMethodsNames('- Please select a method here -');
		//get passed parameters
		$input = Input::all();
		$apiMethodId = Input::get('method');
		$params = array(
			'method' => !empty($testMethods[$apiMethodId]) ? $testMethods[$apiMethodId] : 'flickr.test.echo',
			'format' => isset($input['response_format']) ? $input['response_format'] : 'rest' 
		);
		if (!empty($input['additional_parameters'])) {
			$additionalParams = array();
			parse_str($input['additional_parameters'], $additionalParams);
			$params = array_merge($params, $additionalParams);
		}
		//do request data by a Method.
		$sResult = $api->requestMethod($params);
		
		return View::make('index', array('testMethods' => $testMethods, 'sResult' => $sResult));
	}
	
	/**
	 * Get description and details for the method by AJAX request.
	 * Use POST method_name parameter to specify Method.
	 * 
	 * @return string JSON represntation
	 */
	public function getAjaxMethodDetails()
	{
		$returnData = array(
			'success' => false,
			'msg' => 'Unauthorized attempt to access method.'
		);
		if (Request::ajax()) {
			$methodName = Input::get('method_name');
			$api = FlickrApi::getInstance();
			$returnData = array(
				'success' => true,
				'data' => $api->getMethodDetails($methodName)
			);
		}
		
		return Response::json( $returnData );
	}
	
	/**
	 * Action is used for a callback point.
	 * Perform a third step of oAuth: Exchange the Request Token for an Access Token
	 * 
	 * @return redirect to main page
	 */
	public function exchangeToken()
	{
		$api = FlickrApi::getInstance();
		//do third step: Exchange the Request Token for an Access Token
		return $api->requestAccessToken();
	}
	
	/**
	 * Custom action to show errors if any
	 * @return render view
	 */
	public function showError()
	{
		$message = '';
		return View::make('error', array('message' => $message));
	}


	/**
	 * Destroy Session data and redirect to home page.
	 * 
	 * @return redirect to the API entrance point
	 */
	public function destroy()
	{
		Session::flush();
		return Redirect::to('/');
	}
}
