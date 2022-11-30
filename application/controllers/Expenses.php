<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Expenses_model');
		$this->load->model('Masters_model');
		$this->load->model('Payment_model');
		$this->load->model('ion_auth_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator to view this page.');
			redirect('/');
		}

		/*if(!$this->ion_auth->is_admin()) {
		
			$this->session->set_flashdata('error','You Are Not Allowed To View That Page');
			redirect('students');
		}*/		
	}

	// For Payments & Receipts
	public function index() {

		$this->data['title'] = 'Income & Expenses';
		$this->data['expenses'] = $this->Expenses_model->get_expense();
		$this->_render_page('pages/expenses' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}

	public function add() {

		$this->form_validation->set_rules('description','Required','required');
		$this->form_validation->set_rules('receiver_id','Required','required');
		$this->form_validation->set_rules('amount','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			$inputamt =  $this->input->post('amount');
			$year = $this->input->post('year');

			$additional_data = [
				'receiver_id' => $this->input->post('receiver_id'),
				'year' => $year,
				'description' => $this->input->post('description'),
				'amount' => $inputamt,
				'paid_by' => $this->input->post('paid_by'),
				'date' => $this->input->post('date'),
				'created_at' => date('Y-m-d'),
			];
			
			$pay = $this->Expenses_model->insert($additional_data);
			if ($pay) {
				$getcollec = $this->db->query('SELECT * FROM admin_collection WHERE year='.$year);
				$getamt = $getcollec->result()[0];

				$this->db->where('admin_collection.year',$year);
				$done = $this->db->update('admin_collection', ['amt_use_in_expense' => $inputamt + $getamt->amt_use_in_expense]);
			}

			$this->session->set_flashdata('success', 'Expense Added Successfully');
			redirect("expenses", 'refresh');
		}
		else
		{
			$this->data['title'] = 'Add Expense';
			// For Admin and Members Who Collect The Amount	
			$this->data['receivers'] = $this->Masters_model->get('expenses_master');
			$this->data['years'] = $this->Payment_model->get_joda_fee();
			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('pages/expenses' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}
	}

	public function update($id) {
		
		$this->form_validation->set_rules('description','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			$additional_data = [
				'description' => $this->input->post('description'),
				'amount' => $this->input->post('amount'),
				'receiver_name' => $this->input->post('receiver_name'),
				'paid_by' => $this->input->post('paid_by'),
				'date' => $this->input->post('date'),
				'created_at' => date('Y-m-d'),
			];
			
			$this->Expenses_model->update($id,$additional_data);

			$this->session->set_flashdata('success', 'Expense Added Successfully');
			redirect("expenses", 'refresh');
		}
		else
		{
			$this->data['title'] = 'Update Expense';
			$this->data['expense'] = $this->Expenses_model->get_expense($id);
			// For Admin and Members Who Collect The Amount	
			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('pages/expenses' . DIRECTORY_SEPARATOR . 'edit', $this->data);
		}
	}

	public function delete($table,$id) {

		$this->Expenses_model->delete($table,$id);

		print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
	}

	public function get_total_expense_amount($year) {
		$pyear = $year-1;
		$gettotal = $this->db->query('SELECT * FROM admin_collection WHERE year='.$year.' || year='.$pyear );
		$data = $gettotal->result();
		print_r(json_encode($data));
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
