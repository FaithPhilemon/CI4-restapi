<?php namespace App\Controllers;

use \App\Libraries\Oauth;
use \OAuth2\Request;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class User extends BaseController
{
	use ResponseTrait;

	public function login(){
		$oauth = new Oauth();
		$request = new Request();
		$respond = $oauth->server->handleTokenRequest($request->createFromGlobals($request));
		$code = $respond->getStatusCode();
		$body = $respond->getResponseBody();

		return $this->respond(json_decode($body), $code);
	}

	public function register(){
		helper('form');

		$data = [];

		if($this->request->getMethod() != 'post')
			return $this->fail('Only post request is allowed');

		$rules = [
			'first_name' => 'required|min_length[5]|max_length[20]',
			'last_name' => 'required|min_length[5]|max_length[20]',
			'email' => 'required|valid_email|is_unique[users.email]',
			'password' => 'required|min_length[6]',
			'password_confirm' => 'matches[password]'
		];

		if(!$this->validate($rules)){
			return $this->fail($this->validator->getErrors());
		}else{
			$model = new UserModel();

			$data = [
				'first_name' => $this->request->getVar('first_name'),
				'last_name' => $this->request->getVar('last_name'),
				'email' => $this->request->getVar('email'),
				'password' => $this->request->getVar('password')
			];

			$user_id = $model->insert($data);
			$data['id'] = $user_id;
			unset($data['password']);
			return $this->respondCreated($data);
		}
	}

}
