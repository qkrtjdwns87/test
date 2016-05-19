<?
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Items extends REST_Controller {

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();

        $this->load->library(array('session', 'common'));
        $this->load->helper(array('url'));
        $this->load->model('item_model');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['item_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['item_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['item_delete']['limit'] = 50; // 50 requests per hour per user/key
    }
    
    public function item_get()
    {
    	
    }

    public function item_post()
    {
    	$method = $this->input->post('method', TRUE);
    	$page = $this->input->post('page', TRUE);
    	$listCount = $this->input->post('listcount', TRUE);
    	   	
    	if ($method === 'list')
    	{
    		$sendData = array(
    			'pageMethod' => $method,
    			'listCount' => $listCount,
    			'currentPage' => $page
    		);    		
    		
    		$result = $this->item_model->getItemDataList($sendData, FALSE);
    		$this->response($result, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code    		
    	}
    	else
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'No method was specification(method=>'.$method.')'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    }

    public function item_put()
    {
    	exit('No access allowed');
    }
    
    public function item_delete()
    {
    	exit('No access allowed');
    }
}
