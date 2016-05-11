<?php
/** 
 * Main controller to work with Flickr API
 * 
 * @author Andriy Leshchuk <andriy.leshchuk@gmail.com>
 */

class FlickrController extends \BaseController {
	
	/**
     * @var available methods to use
     */
	public $testMethods = array(
		'- Please select a method here -',
		'flickr.test.echo',
		'flickr.test.login',
		'flickr.test.null',
		'flickr.urls.lookupUser',
		'flickr.panda.getPhotos',
	);
	
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
	 * Display main dashboard.
	 *
	 * @return Response
	 */
	public function index()
	{
		$api = FlickrApi::getInstance();
		//get passed parameters
		$input = Input::all();
//		dd($input);
		$params = array(
			'method' => (isset($input['method']) && !empty($this->testMethods[$input['method']])) ? $this->testMethods[$input['method']] : 'flickr.test.echo',
			'format' => isset($input['response-format']) ? $input['response-format'] : 'rest' 
		);
		//TODO: handle custom parameters(+ methods)
//		$sResult = $api->requestMethod('flickr.panda.getPhotos', 
//											array('panda_name' => 'ling ling')
//										);
		
		$sResult = $api->requestMethod($params);
		
		return View::make('index', array('testMethods' => $this->testMethods, 'sResult' => $sResult));
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
