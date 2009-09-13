<?php
/**
 * Class Akismet
 * @package Core
 * @subpackage Library
 */

/**
 * Checks a comment agains the Akismet api to see if it's spam.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Akismet {
	
	/**
	 * The API key
	 * @var string
	 */
	protected $key = 'b8bf76a6e0d8';
	
	/** 
	 * The base url for our website
	 * @var string
	 */
	protected $baseUrl = 'http://joind.in';

    
    /**
     * Checks comment data against the API
     * @param string $path the path to the api inside the akismet domain
     * @param array $data the comment data
     */
	function check($path, $data){
		$requestBody = '';
		$response = '';
		$host = $this->key.'.rest.akismet.com';
		$data['key'] = $this->key;
		$data['blog'] = $this->baseUrl;
		$data['user_ip'] = $_SERVER['REMOTE_ADDR'];
		
		foreach($data as $key => $value){ 
		    $requestBody .= $key . '=' . urlencode($value) . '&'; 
		}
		
		$request = "POST ".$path." HTTP/1.0\r\n";
		$request .= "Host: ".$host."\r\n";
		$request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$request .= 'Content-length: '.strlen($requestBody)."\r\n";
		$request .= "User-Agent: Joind.in/1.0\r\n";
		$request .= "\r\n";
		$request .= $requestBody;
		
		$fp = fsockopen($host, 80, $errno, $errstr, 10);
		if($fp){
			fwrite($fp, $request);
			while(!feof($fp)) { 
			    $response .= fgets($fp, 1024); 
			}
			fclose($fp);
			$responseBody = explode("\r\n\r\n", $response);
			return (boolean) $responseBody[1];
		} 
		else { 
		    return false; 
		}
	}
	
}
