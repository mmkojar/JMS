<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Members extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Members_model');
		$this->load->model('Payment_model');
		$this->load->model('Masters_model');
		$this->load->model('ion_auth_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group("supervisor")) 
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator or Collector to view this page.');
			redirect('/');
		}	
	}
	
	public function index() {

		$this->data['title'] = 'Members List';
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		/*if($this->ion_auth->is_admin()) {
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}*/
		$this->data['zones'] = $this->Payment_model->get_zones()->result();		
		$this->_render_page('pages' . DIRECTORY_SEPARATOR . 'members', $this->data);
	}

	public function add()
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
		$this->form_validation->set_rules('zone', 'zone', 'trim|required');
		$this->form_validation->set_rules('joining_date', 'Joining Date', 'required');

		if ($identity_column !== 'email')
		{
			$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim');
		}
		else
		{
			$this->form_validation->set_rules('email', 'Email', 'trim|is_unique[' . $tables['users'] . '.email]');
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
			$group = [];
			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'father_name' => $this->input->post('father_name'),
				'phone' => $this->input->post('phone'),
				'zone_id' => $this->input->post('zone'),
				'joining_date' => $this->input->post('joining_date'),
				'status' => 'active'
			];
		}
		if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $additional_data, $get_last_name,$group))
		{
			// check to see if we are creating the user
			// redirect them back to the admin page 
			$this->session->set_flashdata('success', $this->ion_auth->messages());
			redirect("members", 'refresh');
		}
		else
		{
			$this->data['title'] = 'Create New Member';

			$this->data['zones'] = $this->Payment_model->get_zones()->result();
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
				'value' => $this->form_validation->set_value('email')
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
			$this->data['joining_date'] = [
				'name' => 'joining_date',
				'id' => 'joining_date',
				'class' => 'form-control',
				'type' => 'date',
				'value' => $this->form_validation->set_value('joining_date'),		
				'required' => true,		
			];
			$this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_members', $this->data);
		}
	}


	public function members_api() {
		
		if($this->ion_auth->is_admin()) {
			$list = $this->Members_model->get_datatables('');
		}
		else {
			$list = $this->Members_model->get_datatables($this->ion_auth->user()->row()->zone_id);
		}
		
		$i = 1;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $requested) {
			$no++;
			$row = array();
			$row[] = $no;
			// $row[] = str_replace('_',' ',ucwords($requested->username));
			$row[] = $requested->first_name.' '.$requested->father_name.' '.$requested->surname;
			$row[] = $requested->zone_name ? $requested->zone_name : '-';
			$row[] = $requested->phone ? $requested->phone : '-';
			$row[] = $requested->email ? $requested->email : '-';
			$row[] = $requested->joining_date ? $requested->joining_date : '-';
			$row[] = $requested->expiry_date ? $requested->expiry_date : '-';
			$row[] = $requested->divorce_date ? $requested->divorce_date : '-';
			$row[] = $requested->transfer_date ? $requested->transfer_date : '-';
			$row[] = $requested->status ? $requested->status : '-';
			$row[] = '<a class="btn btn-success text-white btn-sm edit_user" id="'.$requested->id.'"><i class="mdi mdi-pencil"></i></a>';
			// <a class="btn btn-danger btn-sm delete_items text-white" url="auth/delete" tname="users" id="'.$requested->id.'"><i class="mdi mdi-delete"></i></a>
			$data[] = $row;
		}
		
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Members_model->count_all('users'),
			"recordsFiltered" => $this->ion_auth->is_admin() ? $this->Members_model->count_filtered('') : $this->Members_model->count_filtered($this->ion_auth->user()->row()->zone_id),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}



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
