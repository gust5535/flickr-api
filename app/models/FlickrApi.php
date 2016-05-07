<?php
/** 
 * Flickr API class realization of oAuth
 * 
 *	The OAuth flow has 3 steps:
 *   - Get a Request Token
 *   - Get the User's Authorization
 *   - Exchange the Request Token for an Access Token
 * 
 * @author Andriy Leshchuk <andriy.leshchuk@gmail.com>
 */

class FlickrApi
{
    private $sig_method = "HMAC-SHA1";//Currently, Flickr only supports HMAC-SHA1 signature encryption.
    
    private $mt; //microtime holder
    private $mt_random;
    private $nonce;
    private $timestamp;
	//configuration data
	private $_cfg = array(
			'api_key'				 => '1830259902a360553220d33f97ad84bc',
			'api_secret'			 => '93f637d8c1ae4eb7', //the Consumer Secret
			'request_token_endpoint' => 'https://www.flickr.com/services/oauth/request_token',
			'authorization_endpoint' => 'https://www.flickr.com/services/oauth/authorize',
			'access_token_endpoint'  => 'https://www.flickr.com/services/oauth/access_token',
			'method_endpoint'		 => 'https://api.flickr.com/services/rest'
		);
    /**
	 * Initiate default values.
	 * 
	 * @param array $params Extra parameters which might be added for initialization.
	 * 
	 * @return void
	 */
	public function __construct($params = array())
	{
		$this->mt        = microtime();
        $this->mt_random = mt_rand();
        $this->nonce     = md5( $this->mt . $this->mt_random );
        $this->timestamp = gmdate ( 'U' ); // Must be UTC time format
		
		if(!empty($params)){
			foreach($params as $k => $v){
				$this->_cfg[$k] = $v;
			}
		}
	}
	
    public function getApiKey()
    {
        return $this->_cfg['api_key'];
    }
	
    public function getRequestTokenEndpoint()
    {
        return $this->_cfg['request_token_endpoint'];
    }

    public function getAuthorizationEndpoint()
    {
        return $this->_cfg['authorization_endpoint'];
    }

    public function getAccessTokenEndpoint()
    {
        return $this->_cfg['access_token_endpoint'];
    }
	
    public function getMethodEndpoint()
    {
        return $this->_cfg['method_endpoint'];
    }

	/**
	 * Generate Signature
	 * 
	 * * First, you must create a base string from your request. 
	 * The base string is constructed by concatenating the HTTP verb, the request URL, 
	 * and all request parameters sorted by name, using lexicograhpical byte value ordering, separated by an '&'.
	 * 
	 * @param string $requestURL The end point of the request
	 * @param array $oauthData query data based on which signature will be generated
	 * @param string $HTTPverb GET|POST
	 * @param string $tokenSecret a secret key returned by Flickr oAuth. For the requestToken it is an empty string.
	 * 
	 * @return string base64encoded signature
	 */
	private function _getSignature($requestURL, $oauthData, $HTTPverb = 'GET', $tokenSecret = '')
	{
		//all request parameters sorted by name, using lexicograhpical byte value ordering
		ksort($oauthData, SORT_STRING);
		$requestParameters = http_build_query($oauthData);
		
		$baseString = $HTTPverb . '&' . urlencode( $requestURL ) . "&" . urlencode( $requestParameters );
		//Use the base string as the text 
		//	and the key is the concatenated values of the Consumer Secret and Token Secret, separated by an '&'.
        $hashkey = $this->_cfg['api_secret'] . "&" . $tokenSecret;
        return base64_encode(hash_hmac('sha1', $baseString, $hashkey, true ));
	}
	
	/**
	 * Getting a Request Token
	 * 
	 * * The first step to obtaining authorization for a user is to get a Request Token using your Consumer Key. 
	 * This is a temporary token that will be used to authenticate the user to your application. 
	 * This token, along with a token secret, will later be exchanged for an Access Token.
	 * 
	 * @return array values from the response string
	 */
	public function requestToken()
	{
		$oauthResponse = array('oauth_callback_confirmed' => false);
        
		$oauthData = array(
            'oauth_callback'			=> Config::get('app.url'),
            'oauth_consumer_key'		=> $this->_cfg['api_key'],
			'oauth_nonce'				=> $this->nonce,
            'oauth_signature_method'	=> $this->sig_method,
            'oauth_timestamp'			=> $this->timestamp,
            'oauth_version'				=> '1.0'
		);
		//add signature
        $oauthData['oauth_signature'] = $this->_getSignature($this->getRequestTokenEndpoint(), $oauthData);
        //build url for request
		$url = $this->getRequestTokenEndpoint().'?'.http_build_query($oauthData);
        try {
			$sResult = file_get_contents( $url );
			//success example: oauth_callback_confirmed=true&oauth_token=72157667256081040-d6d22957ab6b3868&oauth_token_secret=730b9f3c83125790
			parse_str($sResult, $oauthResponse);
		} catch (Exception $e) {
			$oauthResponse['message'] = 'Request to Flickr returned an error. Please verify passed parameters. Request URL: '.$url;
		}
		return $oauthResponse;
    }
	
	/**
	 * Exchanging the Request Token for an Access Token
	 * 
	 * @param string $oauthToken from RequestToken
	 * @param string $oauthVerifier  from RequestToken
	 * 
	 * @return string oAuth response
	 */
	public function requestAccessToken($oauthToken, $oauthVerifier)
	{
		$oauthResponse = array('oauth_callback_confirmed' => false);
		$oauthData = array(
            'oauth_consumer_key'		=> $this->_cfg['api_key'],
			'oauth_nonce'				=> $this->nonce,
            'oauth_signature_method'	=> $this->sig_method,
            'oauth_timestamp'			=> $this->timestamp,
            'oauth_version'				=> '1.0',
			'oauth_token'				=> $oauthToken,
			'oauth_verifier'			=> $oauthVerifier,
		);
		//add signature
        $oauthData['oauth_signature'] = $this->_getSignature($this->getAccessTokenEndpoint(), $oauthData, 'GET', $this->_getTokenSecret());
        //build url for request
		$url = $this->getAccessTokenEndpoint().'?'.http_build_query($oauthData);
//		echo $url;die;
//        try {
			$sResult = file_get_contents( $url );
			//success example: fullname=Andriy%20Leshchuk&oauth_token=72157667634900131-07f485c9fb58afe8&oauth_token_secret=d3d7365e63624203&user_nsid=140618292%40N02&username=ALeshchuk
			parse_str($sResult, $oauthResponse);
			$oauthResponse['oauth_callback_confirmed'] = true;
//		} catch (Exception $e) {
//			dd($e);
//			$oauthResponse['message'] = 'Request to Flickr returned an error.';
//			if Auth error , redirect to / page
//		}
		return $oauthResponse;
    }
	
	/**
	 * Make request for any Method
	 * 
	 * @param string $methodName
	 * @param string $oauthToken oauth_token from Access Token
	 * @param string $tokenSecret oauth_token_secret from Access Token
	 * @param array $params Extra parameters for a request
	 * 
	 * @return string Response
	 */
	public function requestMethod($methodName, $oauthToken, $tokenSecret, $params = array())
	{
		$oauthData = array(
			//'nojsoncallback'		=> 1,
			//'format'				=> $format,
            'oauth_consumer_key'	=> $this->_cfg['api_key'],
			'oauth_nonce'			=> $this->nonce,
            'oauth_signature_method' => $this->sig_method,
            'oauth_timestamp'		=> $this->timestamp,
            'oauth_version'			=> '1.0',
			'oauth_token'			=> $oauthToken,
			'method'				=> $methodName
		);
		if (!empty($params)) {
			$oauthData = array_merge($oauthData, $params);
		}
		//add signature
        $oauthData['oauth_signature'] = $this->_getSignature($this->getMethodEndpoint(), $oauthData, 'GET', $tokenSecret);
		//build url for request
		$url = $this->getMethodEndpoint().'?'.http_build_query($oauthData);
		
		return file_get_contents( $url );
	}
	
	public function testResponse($url)
	{
		echo 'The URL: <br>'.$url.'<br><br>';
		$sResult = file_get_contents( $url );
		echo 'Response: <textarea style="width: 924px; height: 271px;">';
		echo $sResult;
		echo '</textarea>';
		die;
	}
	
	/**
	 * Store oauth_token_secret from the Request Token
	 * 
	 * @param $tokenSecret oauth_token_secret value
	 * @return void
	 */
	public function storeTokenSecret($tokenSecret)
	{
		//TODO: hide file from public access
		$bytes_written = File::put('flickr_token_secret.tmp', $tokenSecret);
		if ($bytes_written === false)
		{
			die("Error writing to file");
		}
	}
	
	/**
	 * Get oauth_token_secret from the Request Token
	 * 
	 * @return string value of the oauth_token_secret
	 */
	private function _getTokenSecret()
	{
		$pathToFile = 'flickr_token_secret.tmp';
		if (File::exists($pathToFile)) {
			return File::get($pathToFile);
		}
		return '';
	}
	
	public function getErrorCode(){}
	public function getErrorMessage(){}
}
