<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Api extends CI_Controller {

	public $data = [];
    public $db2 = '';
    
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->model('Api_model');
		$this->load->model('Message_model');
		$this->load->helper('sms');
		$this->lang->load('auth');
		// $this->db2 = $this->load->database('database2', TRUE);
	}
	
	
	public function debug($query) {
		echo '<pre>';
		print_r($query);
		echo '</pre>';
		die();
	}
	
	public function clean_text($string)
	{
	 $string = trim($string);
	 $string = stripslashes($string);
	 $string = strip_tags($string);
	 $string = htmlspecialchars($string);
	 return $string;
	}
	
	public function sendmsg() {
	    
	    $inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE);
		
	    $mobile = $input['mobile'];
	    $msg = $this->clean_text($input['msg']);
	    
	    if(($mobile == "") && ($msg == "")) {
	        print_r(json_encode(['status'=>'error','msg'=>'Please Enter Mobile No. & msg both']));
	        die();
	    }
	    else {
	        $a = sendSMS($msg,$mobile);
	        $this->debug($a);
	    }
	}
    
	/*public function system_code($code=FALSE) {
        
        if($code == '') {
    		$query = $this->db2->query("SELECT * FROM stop_system");
    	    $result = $query->row();
    
    	    if($result->status == '1') {
    	    	print_r(json_encode(['status'=>'on','msg'=>'System is Active']));
    	    }
    	    else {
    	    	print_r(json_encode(['status'=>'off','msg'=>'System is InActive']));
    	    }
        }
        else {
            $this->db2->update('stop_system',[
    			'status' => $code
    		]);
    		print_r(json_encode(['msg'=>'System Updated']));
        }
	}*/
	
	// Register Api
	public function register()
	{
		if($_SERVER["REQUEST_METHOD"] !== 'POST') {

			$return['errCode'] = '1';
			$return['errMsg'] = $_SERVER["REQUEST_METHOD"].' Method Is Not Allowed Use POST';
			print_r(json_encode($return));
			die();
		}
		$headers = getallheaders();

		if(array_key_exists('Encryptedd-Token',$headers)) {
			
			if(in_array('Access-Token', $headers)) {

				if(isset($headers['Encryptedd-Token']) && !empty($headers['Encryptedd-Token'])) {

					if(!empty(isset($_POST) && $_POST)) 
					{
						$tables = $this->config->item('tables', 'ion_auth');
						$identity_column = $this->config->item('identity', 'ion_auth');
						$this->data['identity_column'] = $identity_column;
						
						$email = strtolower($this->input->post('email'));
						$identity = ($identity_column === 'email') ? $email : $email;
						$password = $this->input->post('password');
						
						$this->check_for_user_detail($this->input->post('phone'));
							
						$additional_data = [
							'first_name' => $this->clean_text($this->input->post('first_name')),
							'father_name' => $this->clean_text($this->input->post('father_name')),
							'last_name' => $this->clean_text($this->input->post('last_name')),
							'phone' => $this->input->post('phone'),
							'alternate_phone' => $this->input->post('alternate_phone'),
							'profession' => $this->input->post('profession'),
							'gender' => $this->input->post('gender'),
							'marital_status' => $this->input->post('marital_status'),
							'blood_group' => $this->input->post('blood_group'),
							'dob' => $this->input->post('dob'),
							'joining_date' => date('Y-m-d'),
							'zone' => $this->input->post('zone'),
							'area' => $this->input->post('area'),
							'status' => 'active'
						];

						if ($this->ion_auth->register($identity, $password, $email, $additional_data))
						{
							$return['errCode'] = '-1';
							$return['errMsg'] = $this->ion_auth->messages();
							print_r(json_encode($return));
						}
						else {
							$return['errCode'] = '1';
							$return['errMsg'] = $this->ion_auth->errors();
							print_r(json_encode($return));
							die();
						}
					}
					else{
						$return['errCode'] = '1';
						$return['errMsg'] = 'Please Enter Proper details';
						print_r(json_encode($return));
						die();
					}

				}
				else{
					$return['errCode'] = '1';
					$return['errMsg'] = 'Invalid Token';
					print_r(json_encode($return));
					die();
					
				}

			}
			else{
				$return['errCode'] = '1';
				$return['errMsg'] = 'Invalid Token Value';
				print_r(json_encode($return));
				die();
				
			}
		}
		else{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Token Key';
			print_r(json_encode($return));
			die();
		}
	}

	public function login() 
	{

		$headers = getallheaders();

		if($_SERVER["REQUEST_METHOD"] !== 'POST') {

			$return['errCode'] = '1';
			$return['errMsg'] = $_SERVER["REQUEST_METHOD"].' Method Is Not Allowed Use POST';
			print_r(json_encode($return));
			die();
		}
		if(array_key_exists('Encryptedd-Token',$headers)) {
			
			if(in_array('Access-Token', $headers)) {

				if(isset($headers['Encryptedd-Token']) && !empty($headers['Encryptedd-Token'])) {

					if(isset($_POST) && !empty($_POST)) 
					{
						if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password')))
						{
							$return['errCode'] = '-1';
							$return['errMsg'] = $this->ion_auth->messages();
							print_r(json_encode($return));
						}
						else
						{							
							$return['errCode'] = '1';
							$return['errMsg'] = $this->ion_auth->errors();
							print_r(json_encode($return));
							die();
						}
					}
					else{
						$return['errCode'] = '1';
						$return['errMsg'] = 'Please Enter Proper details';
						print_r(json_encode($return));
						die();
					}

				}
				else{
					$return['errCode'] = '1';
					$return['errMsg'] = 'Invalid Token';
					print_r(json_encode($return));
					die();
					
				}

			}
			else{
				$return['errCode'] = '1';
				$return['errMsg'] = 'Invalid Token Value';
				print_r(json_encode($return));
				die();
				
			}
		}
		else{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Token Key';
			print_r(json_encode($return));
			die();
		}
	}

	public function update($id = FALSE) {

		if (!$this->ion_auth->logged_in() || $this->ion_auth->user()->row()->id !== $id)
		{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid login';
			print_r(json_encode($return));
			die();
		}

		if($_SERVER["REQUEST_METHOD"] !== 'POST') {
			$return['errCode'] = '1';
			$return['errMsg'] = $_SERVER["REQUEST_METHOD"].' Method Is Not Allowed Use POST';
			print_r(json_encode($return));
			die();
		}		

		if(!isset($id) || empty($id)) {

			$return['errCode'] = '1';
			$return['errMsg'] = 'Please Enter User Id to Update';
			print_r(json_encode($return));
			die();
		}

		$headers = getallheaders();

		if(array_key_exists('Encryptedd-Token',$headers)) {
			
			if(in_array('Access-Token', $headers)) {

				if(isset($headers['Encryptedd-Token']) && !empty($headers['Encryptedd-Token'])) {

					if(isset($_POST) && !empty($_POST))
					{
						$user = $this->ion_auth->user($id)->row();

						$additional_data = [
							'first_name' => $this->clean_text($this->input->post('first_name')),
							'last_name' => $this->clean_text($this->input->post('last_name')),
							'father_name' => $this->clean_text($this->input->post('father_name')),
							'phone' => $this->input->post('phone'),
							'zone_id' => $this->input->post('zone'),
							'profession' => $this->input->post('profession'),
							'gender' => $this->input->post('gender'),
							'marital_status' => $this->input->post('marital_status'),
							'blood_group' => $this->input->post('blood_group'),
							'dob' => $this->input->post('dob'),
						];		

						if ($this->input->post('password'))
						{
							$additional_data['password'] = $this->input->post('password');
						}

						if ($this->ion_auth->update($user->id, $additional_data))
						{
							$return['errCode'] = '-1';
							$return['errMsg'] = $this->ion_auth->messages();
							print_r(json_encode($return));
						}
						else {
							$return['errCode'] = '1';
							$return['errMsg'] = $this->ion_auth->errors();
							print_r(json_encode($return));
							die();
						}
					}
					else{
						$return['errCode'] = '1';
						$return['errMsg'] = 'Please Enter Proper details';
						print_r(json_encode($return));
						die();
					}

				}
				else{
					$return['errCode'] = '1';
					$return['errMsg'] = 'Invalid Token';
					print_r(json_encode($return));
					die();
					
				}

			}
			else{
				$return['errCode'] = '1';
				$return['errMsg'] = 'Invalid Token Value';
				print_r(json_encode($return));
				die();
				
			}
		}
		else{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Token Key';
			print_r(json_encode($return));
			die();
		}

	}

	public function get_user($id = FALSE) {

		if (!$this->ion_auth->logged_in())
		{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Login';
			print_r(json_encode($return));
			die();
		}

		if($_SERVER["REQUEST_METHOD"] !== 'GET') {

			$return['errCode'] = '1';
			$return['errMsg'] = $_SERVER["REQUEST_METHOD"].' Method Is Not Allowed Use GET';
			print_r(json_encode($return));
			die();
		}	

		if(!isset($id) || empty($id)) {

			$return['errCode'] = '1';
			$return['errMsg'] = 'Please Enter User Id';
			print_r(json_encode($return));
			die();
		}

		$headers = getallheaders();

		if(array_key_exists('Encryptedd-Token',$headers)) {
			
			if(in_array('Access-Token', $headers)) {

				if(isset($headers['Encryptedd-Token']) && !empty($headers['Encryptedd-Token'])) {

					$user = $this->ion_auth->user($id)->row();

					if($user) {
						$return['errCode'] = '-1';
						$return['errMsg'] = 'User Get Successfull';
						$return['data'] = $user;
						print_r(json_encode($return));
					}
					else {
						$return['errCode'] = '1';
						$return['errMsg'] = 'No User Found';
						print_r(json_encode($return));
						die();
					}		
				}
				else{
					$return['errCode'] = '1';
					$return['errMsg'] = 'Invalid Token';
					print_r(json_encode($return));
					die();
					
				}

			}
			else{
				$return['errCode'] = '1';
				$return['errMsg'] = 'Invalid Token Value';
				print_r(json_encode($return));
				die();
				
			}
		}
		else{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Token Key';
			print_r(json_encode($return));
			die();
		}	
	}

	public function logout()
	{	
		// log the user out
		$this->ion_auth->logout();
		
		$return['errCode'] = '-1';
		$return['errMsg'] = 'Logged Out Successfully';
		print_r(json_encode($return));
	}

	public function check_for_user_detail($no) {

		$get_user = $this->Api_model->get_user_detail_for_check($no);
		if ($get_user)
		{
			$return['errCode'] = '-1';
			$return['errMsg'] = 'Phone No. Already Exists';
			print_r(json_encode($return));
			die;
		}
	}

	public function get_message($id = FALSE) {

		if (!$this->ion_auth->logged_in())
		{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Login';
			print_r(json_encode($return));
			die();
		}

		if($_SERVER["REQUEST_METHOD"] !== 'GET') {

			$return['errCode'] = '1';
			$return['errMsg'] = $_SERVER["REQUEST_METHOD"].' Method Is Not Allowed Use GET';
			print_r(json_encode($return));
			die();
		}

		$headers = getallheaders();

		if(array_key_exists('Encryptedd-Token',$headers)) {
			
			if(in_array('Access-Token', $headers)) {

				if(isset($headers['Encryptedd-Token']) && !empty($headers['Encryptedd-Token'])) {

					$messages = $this->Message_model->get_messages($id);

					if($messages) {
						$return['errCode'] = '-1';
						$return['errMsg'] = 'Messages Get Successfull';
						$return['data'] = $messages;
						print_r(json_encode($return));
					}
					else {
						$return['errCode'] = '1';
						$return['errMsg'] = 'No User Found';
						print_r(json_encode($return));
						die();
					}			
				}
				else{
					$return['errCode'] = '1';
					$return['errMsg'] = 'Invalid Token';
					print_r(json_encode($return));
					die();
					
				}

			}
			else{
				$return['errCode'] = '1';
				$return['errMsg'] = 'Invalid Token Value';
				print_r(json_encode($return));
				die();
				
			}
		}
		else{
			$return['errCode'] = '1';
			$return['errMsg'] = 'Invalid Token Key';
			print_r(json_encode($return));
			die();
		}		
	}
}
