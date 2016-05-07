<?php

class FlickrController extends \BaseController {
	
	public $testMethods = array(
		'flickr.test.echo',
		'flickr.test.login',
		'flickr.test.null',
		'flickr.urls.lookupUser',
		'flickr.panda.getPhotos',
	);
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sResult = '';
		$testUri = 'flickr.test.login';
		$api = new FlickrApi();
		
		$oauth_token = Input::get('oauth_token', '');
		$oauth_verifier = Input::get('oauth_verifier', '');
		
		if (!$oauth_token || !$oauth_verifier) {
			Session::forget('accessTokenData');
			
			$rt = $api->requestToken();
			if ($rt['oauth_callback_confirmed']) {
				//Getting the User Authorization
				$api->storeTokenSecret($rt['oauth_token_secret']);
				/* Redirect browser */
				//go to https://www.flickr.com/services/oauth/authorize?oauth_token=72157626737672178-022bbd2f4c2f3432
				header("Location: ".$api->getAuthorizationEndpoint().'?oauth_token='.$rt['oauth_token']);
				//after redirects user endups at oauth_callback page.
				// e.g. http://oauth_callback page/?oauth_token=72157626737672178-022bbd2f4c2f3432&oauth_verifier=5d1b96a26b494074
				exit();
			}
		} else {
			//get  Access Token
			if (Session::has('accessTokenData')) {
				$accessTokenData = Session::get('accessTokenData');
				# call a method
//				$testUri = $api->requestMethod('flickr.test.login', $accessTokenData['oauth_token'], $accessTokenData['oauth_token_secret'], array('foo' => 'BAR'));
//				$testUri = $api->requestMethod('flickr.urls.lookupUser', 
//												$accessTokenData['oauth_token'], 
//												$accessTokenData['oauth_token_secret'], 
//												array('url' => 'https://www.flickr.com/photos/paxx/')
//						);
				
				
				$sResult = $api->requestMethod('flickr.panda.getPhotos', 
											$accessTokenData['oauth_token'], 
											$accessTokenData['oauth_token_secret'], 
											array('panda_name' => 'ling ling')
					);
				
				//$sResult = $api->testResponse($testUri);
			} else {
				$accessTokenData = $api->requestAccessToken($oauth_token, $oauth_verifier);
				Session::put('accessTokenData', $accessTokenData);
			}

		}
		
		return View::make('flickr', array('testMethods' => $this->testMethods, 'sResult' => $sResult));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
