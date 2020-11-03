<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . 'libraries/JWT.php';
use \Firebase\JWT\JWT;

class My_Rest_Controller extends REST_Controller
{
    public function __construct()
	{
		parent::__construct();
		$this->_ci =&get_instance();
		header('Access-Control-Allow-Origin: *');
    }
    
    public function validate_token($access_token)
	{
		if (empty($access_token)) {
			$response_data = array(
				'status_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' => 'Access token missing',
				'token' => null
			);
            return $this->response($response_data);
		}
		else{
			try {
				$token = JWT::decode($access_token, $this->_ci->config->item('jwt_key'), array('HS256'));
				
				if ((time() - strtotime($token->iat)) > ($this->_ci->config->item('expire_time') * 3600)) {
					$response_data = array(
						'status_code' => REST_Controller::HTTP_UNAUTHORIZED,
						'message' => 'Access token expired',
						'token' => $access_token
					);
					return $this->response($response_data);
				} else {
					return $token;
				}
			} catch (Exception $e) {
				$response_data = array(
					'status_code' => REST_Controller::HTTP_BAD_REQUEST,
					'message' => 'Access token invalid',
					'error' => $e
				);
				return $this->response($response_data);
			}
		}
	}
}