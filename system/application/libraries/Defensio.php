<?php 
/**
 * Class Defensio
 * @package Core
 * @subpackage Library
 */

/**
 * Checks defensio to see if a message is possible spam.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Defensio {

    /**
     * API key to Defensio
     * @var string
     */	
	protected $key = 'd22ba53cb84a0555b2d0f3836cfece5c';
	
	/**
	 * base url for our website
	 * @var string
	 */
	protected $baseUrl = 'http://joind.in';
	
	/**
	 * The recieved XML response from Defensio
	 * @var SimpleXMLElement
	 */
	protected $responseXml = null;
	
	/**
	 * Checks a comment against the Defensio API
	 * @param string $name the comment authors name
	 * @param string $comment the comment body
	 * @return boolean
	 */
	function check($name, $comment)
	{
		$response = '';
		$apiLocation = '/app/1.2/audit-comment/' . $this->key . '.xml';
		$parameters = array(
			'user-ip' => $_SERVER['REMOTE_ADDR'],
			'owner-url' => $this->baseUrl,
			'article-date' => date('Y/m/d'),
			'comment-author' => $name,
			'comment-type' => 'comment',
			'comment-content' => $comment,
		);
		
		if(isset($_SERVER['HTTP_REFERER'])) {
			$parameters['referrer'] = $_SERVER['HTTP_REFERER'];
		}
		
		$requestBody='';
		foreach($parameters as $key => $value){
			$requestBody .= $key . '=' . urlencode($value) . '&';
		}
		
		$request = "POST " . $apiLocation . " HTTP/1.0\r\n";
		$request .= "Host: api.defensio.com\r\n";
		$request .= "Content-type: application/x-www-form-urlencoded\r\n";
		$request .= "Content-length: " . strlen($requestBody) . "\r\n";
		$request .= "Connection: close\r\n";
		$request .= "\r\n";
		$request .= $requestBody;

		$fp = fsockopen('api.defensio.com', 80, $errno, $errstr);
		if($fp){
			fwrite($fp, $request);
			while(!feof($fp)) { 
			    $response .= fread($fp, 1024); 
			}
			fclose($fp);
		}
		
		if($response){
			$responseBody = explode("\r\n\r\n", $response);
			$this->responseXml = simplexml_load_string($responseBody[1]);
			
			return ($this->responseXml->spam == 'false');
		}else{ 
		    return false; 
		}
	}
	
	/**
	 * Returns the recieved XML response from Defensio.
	 * @return SimpleXMLElement
	 */
	public function getResponse()
	{
	    return $this->responseXml;
	}
	
}
