<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Auth extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Payment_model');
		$this->load->model('Masters_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
	}

	/**
	 * Redirect if needed, otherwise display the user list
	 */
	public function index()
	{

		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator to view this page.');
			redirect('/');
		}
		else
		{
			$this->data['title'] = 'Users List';
			
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			// $this->data['login_ids'] = $this->ion_auth->user()->row();

			//list the users
			// $this->data['users'] = $this->ion_auth->users()->result();
			
			//USAGE NOTE - you can do more complicated queries like this
			//$this->data['users'] = $this->ion_auth->where('field', 'value')->users()->result();
			
			// foreach ($this->data['users'] as $k => $user)
			// {
			// 	$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			// }

			
			$this->db->select('users.*,zone_master.zone_name,users_groups.group_id,groups.description as group_name');
			$this->db->join('zone_master','zone_master.id = users.zone_id','left');
			$this->db->join('users_groups','users_groups.user_id = users.id','inner');
			$this->db->join('groups','groups.id = users_groups.group_id','inner');
			$this->db->where_in('users_groups.group_id',[1,2,4]);
			$this->db->order_by('users.first_name','ASC');
			$this->db->from('users');
			$query=$this->db->get();
			$this->data['users'] = $query->result();

			// $this->data['zones'] = $this->Payment_model->get_zones()->result();

			$this->_render_page('pages' . DIRECTORY_SEPARATOR . 'users', $this->data);

			/*if($this->data['login_ids']->id == 1) {

				$this->data['check_users_id'] = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
				
				$this->data['zones'] = $this->Payment_model->get_zones()->result();

				$this->_render_page('pages' . DIRECTORY_SEPARATOR . 'admin_user', $this->data);
			}
			else {
				$this->_render_page('pages' . DIRECTORY_SEPARATOR . 'user', $this->data);
			}*/		
		}
	}
	
	/**
	 * Log the user in
	 */
	public function login()
	{
		if ($this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('/', 'refresh');
		}

		$this->data['title'] = $this->lang->line('login_heading');

		// validate form input
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

		if ($this->form_validation->run() === TRUE)
		{
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool)$this->input->post('remember');

			if ($this->_valid_csrf_nonce() === FALSE)
			{				
				$this->session->set_flashdata('message', $this->lang->line('error_csrf'));
				redirect('auth/login', 'refresh');
			}
			else {
				if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
				{
					//if the login is successful
					//redirect them back to the home page
					// $this->session->set_flashdata('success', $this->ion_auth->messages());
					$this->session->set_flashdata('success', 'Logged In Successfully');
					if($this->ion_auth->is_admin() || $this->ion_auth->in_group("supervisor")) {						
						redirect('/', 'refresh');
					}
					else if($this->ion_auth->in_group("notify")) {
						redirect('/message', 'refresh');
					}
				}
				else
				{
					// if the login was un-successful
					// redirect them back to the login page
					$this->session->set_flashdata('message', $this->ion_auth->errors());
					redirect('auth/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
				}
			}
		}
		else
		{
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? '' : $this->session->flashdata('message');

			$this->data['identity'] = [
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'class' => 'form-control',
                'placeholder' => 'Username',
				'value' => $this->form_validation->set_value('identity'),
				// 'value' => 'admin@gmail.com'
			];

			$this->data['password'] = [
				'name' => 'password',
				'id' => 'password',
				'class' => 'form-control',
                'placeholder' => 'Password',
				'type' => 'password',
				// 'value' => '123'
			];

			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'login', $this->data);
		}
	}

	public function ger_user_api($id) {

		$data = $this->ion_auth->user($id)->row();
		print_r(json_encode($data));
	}
	
	public function update_user_api() {

		$id = $this->input->post('hidden_user_id');

		/*if($this->input->post('status') == 'inactive') {
			$data = [
				'expiry_date' => date('Y-m-d')
			];
			// $this->db->where('user_id',$id);
			// $this->db->delete('invoices');
		}
		else {
			$data = [
				'expiry_date' => ''
			];
		}*/

		$get_name_from_id = $this->Masters_model->get('surname_master',$this->input->post('last_name'));
		$get_last_name = $get_name_from_id->surname;
		
		$data = [
			'first_name' => $this->input->post('first_name'),
			'father_name' => $this->input->post('father_name'),
			'last_name' => $this->input->post('last_name'),
			'username' => strtolower($this->input->post('first_name').'_'.$this->input->post('father_name').'_'.$get_last_name),
			'email' => $this->input->post('email'),
			'phone' => $this->input->post('phone'),
			'zone_id' => $this->input->post('zone_id') ? $this->input->post('zone_id') : $this->input->post('current_zone_id'),
			'old_zone_id' => $this->input->post('zone_id') ? $this->input->post('current_zone_id') : $this->input->post('old_zone_id'),
			'transfer_date' => $this->input->post('transfer_date') ? $this->input->post('transfer_date') : '',
			'status' => $this->input->post('status'),
			'divorce_date' => $this->input->post('status') == 'divorce' ? $this->input->post('divorce_date') : '',
			'expiry_date' => $this->input->post('status') == 'inactive' ? $this->input->post('expiry_date') : '',
		];

		if($this->input->post('status') !== 'active') {
			$this->ion_auth->deactivate($id);
			$this->db->where('invoices.user_id',$id);
			$this->db->update('invoices',[
				'status' => 'inactive'
			]);
		}
		else {		    
			$this->ion_auth->activate($id);
			$this->db->where('invoices.user_id',$id);
			$this->db->update('invoices',[
				'status' => 'active'
			]);
		}

		if($this->input->post('zone_id') !== '') {
			$this->db->where('invoices.user_id',$id);
			$this->db->update('invoices',[
				'zone_id' => $this->input->post('zone_id')
			]);
		}

		if($this->input->post('last_name') !== '') {
			$this->db->where('invoices.user_id',$id);
			$this->db->update('invoices',[
				'surname' => $this->input->post('last_name')
			]);
		}

		$upd = $this->ion_auth->update($id, $data);

		if($upd) {
			print_r(json_encode(['status'=>'success','msg'=>'User Updated']));
		}
	}

	public function delete($table,$id) {

		$this->db->where('id',$id);
		$delete = $this->db->delete($table);

		if($delete) {
			$this->db->where('user_id',$id);
			$this->db->delete('invoices');
			
			print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
		}
		
	}

	/**
	 * Log the user out
	 */
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		$this->ion_auth->logout();

		// redirect them to the login page
		redirect('auth/login', 'refresh');
	}

	/**
	 * Change password
	 */
	public function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() === FALSE)
		{
			// display the form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = [
				'name' => 'old',
				'id' => 'old',
				'type' => 'password',
			];
			$this->data['new_password'] = [
				'name' => 'new',
				'id' => 'new',
				'type' => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			];
			$this->data['new_password_confirm'] = [
				'name' => 'new_confirm',
				'id' => 'new_confirm',
				'type' => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			];
			$this->data['user_id'] = [
				'name' => 'user_id',
				'id' => 'user_id',
				'type' => 'hidden',
				'value' => $user->id,
			];

			// render
			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'change_password', $this->data);
		}
		else
		{
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	/**
	 * Forgot password
	 */
	public function forgot_password()
	{
		$this->data['title'] = $this->lang->line('forgot_password_heading');
		
		// setting validation rules by checking whether identity is username or email
		if ($this->config->item('identity', 'ion_auth') != 'email')
		{
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		}
		else
		{
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}


		if ($this->form_validation->run() === FALSE)
		{
			$this->data['type'] = $this->config->item('identity', 'ion_auth');
			// setup the input
			$this->data['identity'] = [
				'name' => 'identity',
				'id' => 'identity',
			];

			if ($this->config->item('identity', 'ion_auth') != 'email')
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
			}
			else
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'forgot_password', $this->data);
		}
		else
		{
			$identity_column = $this->config->item('identity', 'ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if (empty($identity))
			{

				if ($this->config->item('identity', 'ion_auth') != 'email')
				{
					$this->ion_auth->set_error('forgot_password_identity_not_found');
				}
				else
				{
					$this->ion_auth->set_error('forgot_password_email_not_found');
				}

				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				// if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	/**
	 * Reset password - final step for forgotten password
	 *
	 * @param string|null $code The reset code
	 */
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$this->data['title'] = $this->lang->line('reset_password_heading');
		
		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			// if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() === FALSE)
			{
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = [
					'name' => 'new',
					'id' => 'new',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['new_password_confirm'] = [
					'name' => 'new_confirm',
					'id' => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['user_id'] = [
					'name' => 'user_id',
					'id' => 'user_id',
					'type' => 'hidden',
					'value' => $user->id,
				];
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;
				
				// render
				$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'reset_password', $this->data);
			}
			else
			{
				$identity = $user->{$this->config->item('identity', 'ion_auth')};

				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($identity);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						// if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("auth/login", 'refresh');
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	/**
	 * Activate the user
	 *
	 * @param int         $id   The user ID
	 * @param string|bool $code The activation code
	 */
	public function activate($id, $code = FALSE)
	{
		$activation = FALSE;

		if ($code !== FALSE)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			// redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			// redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	/**
	 * Deactivate the user
	 *
	 * @param int|string|null $id The user ID
	 */
	public function deactivate($id = NULL)
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the home page because they must be an administrator to view this
			show_error('You must be an administrator to view this page.');
		}

		$id = (int)$id;
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() === FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
		}
		else
		{
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in())
				{
					$this->ion_auth->deactivate($id);
				}
			}

			// redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	/**
	 * Create a new user
	 */
	public function create_user()
	{		
		$this->data['title'] = $this->lang->line('create_user_heading');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth', 'refresh');
		}

		$tables = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
		$this->form_validation->set_rules('father_name', 'Father Name', 'trim|required');

		if ($identity_column !== 'email')
		{
			$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required');
		}
		else
		{
			$this->form_validation->set_rules('email', 'Username', 'trim|required|is_unique[' . $tables['users'] . '.email]');
		}
		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'),
				 'trim|is_unique[' . $tables['users'] . '.phone]');

		//$this->form_validation->set_rules('zone', 'Zone', 'trim|required');
		
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() === TRUE)
		{
			
			$email = strtolower($this->input->post('email'));
			$identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password = $this->input->post('password');

			$get_name_from_id = $this->Masters_model->get('surname_master',$this->input->post('last_name'));
			$get_last_name = $get_name_from_id->surname;
			$group = $this->input->post('group');
			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'father_name' => $this->input->post('father_name'),
				'phone' => $this->input->post('phone'),
				'zone_id' => $this->input->post('zone'),
				'joining_date' => date('Y-m-d'),
				'status' => 'active'
			];
		}
		if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $additional_data, $get_last_name,$group))
		{
			// check to see if we are creating the user
			// redirect them back to the admin page 
			$this->session->set_flashdata('success', $this->ion_auth->messages());
			redirect("users", 'refresh');
		}
		else
		{
			$this->data['title'] = 'Create New User';
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
			$this->data['groups'] = $this->ion_auth->groups()->result();
			$this->data['surnames'] = $this->Masters_model->get('surname_master');

			$this->data['message'] = (validation_errors() ? '': ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = [			
				'name' => 'first_name',
				'id' => 'first_name',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('first_name'),
				'required' => true,
			];
			$this->data['last_name'] = [
				'name' => 'last_name',
				'id' => 'last_name',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('last_name'),
				'required' => true,
			];
			$this->data['father_name'] = [
				'name' => 'father_name',
				'id' => 'father_name',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('father_name'),
				'required' => true,
			];
			$this->data['identity'] = [
				'name' => 'identity',
				'id' => 'identity',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			];
			$this->data['email'] = [
				'name' => 'email',
				'id' => 'email',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
				'required' => true,
			];
			$this->data['phone'] = [
				'name' => 'phone',
				'id' => 'phone',
				'class' => 'form-control',
				'type' => 'text',
				'value' => $this->form_validation->set_value('phone'),				
			];
			$this->data['password'] = [
				'name' => 'password',
				'id' => 'password',
				'class' => 'form-control',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password'),
				'required' => true,
			];
			$this->data['password_confirm'] = [
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'class' => 'form-control',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
				'required' => true,
			];

			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_user', $this->data);
		}
	}

	/**
	* Redirect a user checking if is admin
	*/
	public function redirectUser(){
		if ($this->ion_auth->is_admin()){
			redirect('/users', 'refresh');
		}
		redirect('/users', 'refresh');
	}

	/**
	 * Edit a user
	 *
	 * @param int|string $id
	 */

	public function edit_user($id)
	{
		$this->data['title'] = $this->lang->line('edit_user_heading');

		if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id)))
		{
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();
		$tables = $this->config->item('tables', 'ion_auth');
		// $identity_column = $this->config->item('identity', 'ion_auth');
		// $this->data['identity_column'] = $identity_column;
	
		//USAGE NOTE - you can do more complicated queries like this
		//$groups = $this->ion_auth->where(['field' => 'value'])->groups()->result_array();
	
		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
		// $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
		// $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim');		
		// if ($identity_column !== 'email')
		// {
		// 	$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
		// 	$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
		// }
		// else
		// {
			$this->form_validation->set_rules('email', 'Username', 'trim|required');
		// }

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error($this->lang->line('error_csrf'));
			}

			// update the password if it was posted
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				if($this->input->post('last_name')) {
					$get_name_from_id = $this->Masters_model->get('surname_master',$this->input->post('last_name'));
					$get_last_name = $get_name_from_id->surname;
				}		
				else {
					$get_last_name = "";
				}		
				$data = [
					'username' => strtolower($this->input->post('first_name').'_'.$this->input->post('father_name').'_'.$get_last_name),
					'first_name' => $this->input->post('first_name'),
					'father_name' => $this->input->post('father_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email'),
					'phone' => $this->input->post('phone'),
					'zone_id' => $this->input->post('zone'),
				];

				// update the password if it was posted
				if ($this->input->post('password'))
				{
					$data['password'] = $this->input->post('password');
				}

				// Only allow updating groups if user is admin
				if ($this->ion_auth->is_admin())
				{
					// Update the groups user belongs to
					// $this->ion_auth->remove_from_group('', $id);
					
					$groupData = $this->input->post('groups');
				
					if (isset($groupData))
					{
						$this->ion_auth->update_group_id($id, $groupData);

					}
					/*if (isset($groupData) && !empty($groupData))
					{
						foreach ($groupData as $grp)
						{
							$this->ion_auth->add_to_group($grp, $id);
						}

					}*/
				}

				// check to see if we are updating the user
				if ($this->ion_auth->update($user->id, $data))
				{
					// redirect them back to the admin page if admin, or to the base url if non admin
					// $this->session->set_flashdata('message', $this->ion_auth->messages());
					$this->session->set_flashdata('success', 'Account Information Successfully Updated');
					// $this->redirectUser();
					redirect('users/edit/'.$id, 'refresh');

				}
				else
				{
					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('error', $this->ion_auth->errors());
					// $this->redirectUser();
					redirect('users/edit/'.$id, 'refresh');

				}

			}
		}

		$this->data['zones'] = $this->Payment_model->get_zones()->result();
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		// display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? '' : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = [
			'name'  => 'first_name',
			'id'    => 'first_name',
			'class' => 'form-control',
			'type'  => 'text',
			// 'readonly' => true,
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		];
		$this->data['father_name'] = [
			'name'  => 'father_name',
			'id'    => 'father_name',
			'class' => 'form-control',
			'type'  => 'text',
			// 'readonly' => true,
			'value' => $this->form_validation->set_value('father_name', $user->father_name),
		];
		$this->data['last_name'] = [
			'name'  => 'last_name',
			'id'    => 'last_name',
			'class' => 'form-control',
			'type'  => 'text',
			// 'readonly' => true,
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		];
		/*$this->data['identity'] = [
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
		];*/
		$this->data['email'] = [
			'name' => 'email',
			'id' => 'email',
			'class' => 'form-control',
			'type' => 'text',			
			'autocomplete' => 'off',
			'value' => $this->form_validation->set_value('email', $user->email),
		];
		$this->data['phone'] = [
			'name'  => 'phone',
			'id'    => 'phone',
			'class' => 'form-control',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone', $user->phone),
		];
		$this->data['phone'] = [
			'name'  => 'phone',
			'id'    => 'phone',
			'class' => 'form-control',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone', $user->phone),
		];
		$this->data['password'] = [
			'name' => 'password',
			'id'   => 'password',
			'class' => 'form-control',
			'type' => 'password',
			'autocomplete' => 'off'
		];
		$this->data['password_confirm'] = [
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'class' => 'form-control',
			'type' => 'password'
		];

		$this->_render_page('auth/edit_user', $this->data);
	}

	/**
	 * Create a new group
	 */
	public function create_group()
	{
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			redirect('auth', 'refresh');
		}

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');

		if ($this->form_validation->run() === TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id)
			{
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		}
		else
		{
			// display the create group form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = [
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			];
			$this->data['description'] = [
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description'),
			];

			$this->_render_page('auth/create_group', $this->data);
		}
	}

	/**
	 * Edit a group
	 *
	 * @param int|string $id
	 */
	public function edit_group($id)
	{
		// bail if no group id given
		if (!$id || empty($id))
		{
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'trim|required|alpha_dash');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], array(
					'description' => $_POST['group_description']
				));

				if ($group_update)
				{
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth", 'refresh');
			}
		}

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$this->data['group'] = $group;

		$this->data['group_name'] = [
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'value'   => $this->form_validation->set_value('group_name', $group->name),
		];
		if ($this->config->item('admin_group', 'ion_auth') === $group->name) {
			$this->data['group_name']['readonly'] = 'readonly';
		}
		
		$this->data['group_description'] = [
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		];

		$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
	}

	/**
	 * @return array A CSRF key-value pair
	 */
	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	/**
	 * @return bool Whether the posted CSRF token matches
	 */
	public function _valid_csrf_nonce(){
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
			return FALSE;
	}

	/**
	 * @param string     $view
	 * @param array|null $data
	 * @param bool       $returnhtml
	 *
	 * @return mixed
	 */
	public function _render_page($view, $data = NULL, $returnhtml = FALSE)//I think this makes more sense
	{

		$viewdata = (empty($data)) ? $this->data : $data;

		$view_html = $this->load->view($view, $viewdata, $returnhtml);

		// This will return html on 3rd argument being true
		if ($returnhtml)
		{
			return $view_html;
		}
	}

}
