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
	/**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;
	
    /**
     * @var Currently, Flickr only supports HMAC-SHA1 signature encryption.
     */
    private $sig_method = "HMAC-SHA1";
	
    /**
     * @var microtime holder
     */
    private $mt;
    private $mt_random;
    private $nonce;
    private $timestamp;
	
	private $_oauth_token = '';
	private $_oauth_verifier = '';
	
    /**
     * @var configuration data
     */
	private $_cfg = array(
			'api_key'				 => '1830259902a360553220d33f97ad84bc',
			'api_secret'			 => '93f637d8c1ae4eb7', //the Consumer Secret
			'request_token_endpoint' => 'https://www.flickr.com/services/oauth/request_token',
			'authorization_endpoint' => 'https://www.flickr.com/services/oauth/authorize',
			'access_token_endpoint'  => 'https://www.flickr.com/services/oauth/access_token',
			'method_endpoint'		 => 'https://api.flickr.com/services/rest'
		);
	
    /**
     * Returns the *Singleton* instance of this class.
     *
	 * @param array $params Extra parameters which might be added for initialization.
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance($params = array())
    {
        if (null === static::$instance) {
            static::$instance = new static($params);
        }
        
        return static::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
	
	/**
	 * Populate required for API data.
	 * 
	 * @param array $params Extra parameters which might be added for initialization.
	 * @return void
	 */
	protected function _populateRequiredData($params = array())
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
    /**
	 * Initiate default values.
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
	 * 
	 * @param array $params Extra parameters which might be added for initialization.
	 * 
	 * @return void
	 */
	protected function __construct($params = array())
	{
		$this->_populateRequiredData($params);
		//if user is not authenticated in Flickr yet, provide him to authentication process
		if (!Session::has('accessTokenData')) {
			$this->_oauth_token = Input::get('oauth_token', '');
			$this->_oauth_verifier = Input::get('oauth_verifier', '');
			if (!$this->_oauth_token && !$this->_oauth_verifier) {
				$this->requestToken();
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
	 * @return redirect to Flickr to Get the User's Authorization
	 */
	public function requestToken()
	{
		$oauthResponse = array('oauth_callback_confirmed' => false);
        
		$oauthData = array(
            'oauth_callback'			=> URL::route('exchangeToken'),
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
		//do first step: Get a Request Token
		$sResult = file_get_contents( $url );
		//success example: oauth_callback_confirmed=true&oauth_token=72157667256081040-d6d22957ab6b3868&oauth_token_secret=730b9f3c83125790
		parse_str($sResult, $oauthResponse);
		
		if ($oauthResponse['oauth_callback_confirmed']) {
			//do second step: Get the User's Authorization
			$this->storeTokenSecret($oauthResponse['oauth_token_secret']);
			// Redirect browser to Flickr
			header("Location: ".$this->getAuthorizationEndpoint().'?oauth_token='.$oauthResponse['oauth_token']);
			//after redirects user endups at oauth_callback page.
			// e.g. http://[oauth_callback_page]/?oauth_token=72157626737672178-022bbd2f4c2f3432&oauth_verifier=5d1b96a26b494074
			exit();
		}
		
		return false;
    }
	
	/**
	 * Exchanging the Request Token for an Access Token
	 * 
	 * @return redirect to main page
	 */
	public function requestAccessToken()
	{
		$accessTokenData = '';
		$this->_oauth_token = Input::get('oauth_token', '');
		$this->_oauth_verifier = Input::get('oauth_verifier', '');
		if ($this->_oauth_token && $this->_oauth_verifier) {
			$oauthData = array(
				'oauth_consumer_key'		=> $this->_cfg['api_key'],
				'oauth_nonce'				=> $this->nonce,
				'oauth_signature_method'	=> $this->sig_method,
				'oauth_timestamp'			=> $this->timestamp,
				'oauth_version'				=> '1.0',
				'oauth_token'				=> $this->_oauth_token,
				'oauth_verifier'			=> $this->_oauth_verifier,
			);
			//add signature
			$oauthData['oauth_signature'] = $this->_getSignature($this->getAccessTokenEndpoint(), $oauthData, 'GET', $this->_getTokenSecret());
			//build url for request
			$url = $this->getAccessTokenEndpoint().'?'.http_build_query($oauthData);
			//do request
			$sResult = file_get_contents( $url );
			//success example: fullname=Andriy%20Leshchuk&oauth_token=72157667634900131-07f485c9fb58afe8&oauth_token_secret=d3d7365e63624203&user_nsid=140618292%40N02&username=ALeshchuk
			parse_str($sResult, $accessTokenData);
				
			if (!empty($accessTokenData)) {
				Session::put('accessTokenData', $accessTokenData);
			}
		}
		return Redirect::to('/');
    }
	
	/**
	 * Make request for any Method
	 * 
	 * @param array $params Extra parameters for a request
	 * 
	 * @return string Response
	 */
	public function requestMethod($params = array())
	{
		$accessTokenData = Session::get('accessTokenData');
		$oauthData = array(
            'oauth_consumer_key'	=> $this->_cfg['api_key'],
			'oauth_nonce'			=> $this->nonce,
            'oauth_signature_method' => $this->sig_method,
            'oauth_timestamp'		=> $this->timestamp,
            'oauth_version'			=> '1.0',
			'oauth_token'			=> $accessTokenData['oauth_token']
		);
		if (!empty($params)) {
			$oauthData = array_merge($oauthData, $params);
		}
		//add signature
        $oauthData['oauth_signature'] = $this->_getSignature($this->getMethodEndpoint(), $oauthData, 'GET', $accessTokenData['oauth_token_secret']);
		//build url for request
		$url = $this->getMethodEndpoint().'?'.http_build_query($oauthData);
		//make request and return content of response
		return file_get_contents( $url );
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
