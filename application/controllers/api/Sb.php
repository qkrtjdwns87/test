<?
defined('BASEPATH') or exit ('No direct script access allowed');

	/**
	 * User
	 * 
	 *
	 * @author : gilbert
	 * @date    : 2016. 04
	 * @version:
	 */
	class Sb extends CI_Controller {

		protected $m_auth = '';

		public function __construct() 
		{
			parent::__construct ();
			
			$m_auth = "09382cc3b477fc6e62e1e7ab1fe565232f680054";
		}

		public function create($_email) 
		{	
			// The data to send to the API
			$postData = array(
			    'auth' => '09382cc3b477fc6e62e1e7ab1fe565232f680054',
			    'id' => $_email,
			    'nickname' => $_email,
			    'image_url' => 'http://api.circusflag.com/images/app/main/default_profile.png'
			);

			// Setup cURL
			$ch = curl_init('https://api.sendbird.com/user/create');
			curl_setopt_array($ch, array(
			    CURLOPT_POST => TRUE,
			    CURLOPT_RETURNTRANSFER => TRUE,
			    CURLOPT_HTTPHEADER => array(
			        'Content-Type: application/json'
			    ),
			    CURLOPT_POSTFIELDS => json_encode($postData)
			));

			// Send the request
			$response = curl_exec($ch);

			// Check for errors
			if($response === FALSE){
			    die(curl_error($ch));
			}

			// Decode the response
			// $responseData = json_decode($response, TRUE);

			// Print the date from the response
			// print_r( $responseData );

			echo $response;
		}
	}
?>