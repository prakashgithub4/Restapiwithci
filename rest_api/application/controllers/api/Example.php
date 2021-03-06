<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Example extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();


		$this->load->model('user');
	}
	public function index()
	{
		if ($this->input->server('REQUEST_METHOD') == 'GET'){
			
			$this->db->select('*');
			$query = $this->db->get('users');
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: application/json; charset=UTF-8");
			header("HTTP/1.1 200 Ok");
			$data = [
				"msg" => 'Data fetch successfully',
				'data' => $query->result_array(),
				'status' => 200
			];
		}
		else
		{
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: application/json; charset=UTF-8");
			header("HTTP/1.1 405 Method Not Allowed");
		   
			$data = [
				"msg" => '',
				"error"=>"method not allowed",
				'data' => [],
				'status' => 405
			];
           
		}
	
		echo json_encode($data);
	}
	public function store()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
		$error = [];
		$flag = true;
		$data['first_name'] = $this->input->post('fname');
		if (empty($data['first_name'])) {

			$error['fname'] = "fname is required";
			$flag = false;
		}
		$data['last_name'] = $this->input->post('lname');

		if (empty($data['last_name'])) {

			$error['lname'] = "lname is required";
			$flag = false;
		}

		$data['email'] = $this->input->post('email');

		if (empty($data['email'])) {

			$error['email'] = "email is required";
			$flag = false;
		}

		$data['phone'] = $this->input->post('phone');

		if (empty($data['phone'])) {

			$error['phone'] = "phone is required";
			$flag = false;
		}
		$data['image'] = $this->do_upload('image', './assets/');
		if (empty($data['image'])) {

			$error['image'] = "phone is required";
			$flag = false;
		}
		if ($flag == false) {
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: application/json; charset=UTF-8");
			header("HTTP/1.1 400 Bad Request");
			$data = ["message" => "error", "data" => [], 'error' => $error];
			echo json_encode($data);
		} else {
			$data['status'] = 1;
			$user_info = $this->db->insert('users', $data);
			//header('Content-Type: application/json');
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: application/json; charset=UTF-8");
			header("HTTP/1.1 200 OK");

			
			$data = [
				"message" => "data Save successfully",
				"status" => $user_info
			];
		
		}
	}
	else{
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		header("HTTP/1.1 405 Method not Allowed");

		$data = [
			"message" => "method not Allowed",
			"status" =>405
		];
		
	}	
	echo json_encode($data);

	}

	public function do_upload($image, $path)
	{

		$config['upload_path']          = $path;
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 1024;
		$config['max_width']            = 1024;
		$config['max_height']           = 768;
		$new_name = time();
		$config['file_name'] = $new_name;


		$this->load->library('upload', $config);

		if ($this->upload->do_upload($image)) {
			$data = array($this->upload->data());
			$file_name = $data[0]['file_name'];
			$location = $path . $file_name;
			$base64 = $this->convertimagetobase64($location);
			return  $base64;
		}
	}

	/* base64 convertion*/

	public function convertimagetobase64($pathdata)
	{
		$path = $pathdata;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
}
