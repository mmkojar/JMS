<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Collection extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Collection_model');
		$this->load->model('Payment_model');
		$this->load->model('ion_auth_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group("supervisor")) 
		{			
			$this->session->set_flashdata('error', 'You must be an administrator or Collector to view this page.');
			redirect('/');
		}
	}

	// Collectors Collection
	public function index() {
		
		$this->data['title'] = 'Collector Collections';
		
		if($this->ion_auth->is_admin()) {
			$this->data['collections'] = $this->Collection_model->get();
		}
		else{
			$this->data['collections'] = $this->Collection_model->get('','',$this->ion_auth->user()->row()->zone_id);
		}
		
		$this->_render_page('pages/collection' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}
	
	public function add() {

		$checkY = $this->input->post('year');

		$this->form_validation->set_rules('date','Required','required');
		$this->form_validation->set_rules('amt_collected','Required','required');

		if ($this->form_validation->run() === TRUE)
		{
			$id = $this->input->post('hidden_collection_id');

			/* $get_count = $this->Collection_model->get_joda_count_by_zone($this->input->post('zone'));
			$get_year = $this->Payment_model->get_joda_fee($this->input->post('year')); */

			$additional_data = [
				'year' => $this->input->post('year'),
				'zone' => $this->input->post('zone'),
				'collector_id' => $this->input->post('collector_id'),					
				'amt_collected' => $this->input->post('amt_collected'),
				'amt_received_by_admin' => 0,
				// 'receiver_name' => $this->input->post('receiver_name'),
				'date' => $this->input->post('date'),
				'remark' => $this->input->post('remark'),
				// 'total_zone_amount' => $get_count*$get_year->amount,
				'created_at' => date('Y-m-d h:i:s'),
			];

			if($id) {								
				$pay = $this->Collection_model->update($id,$additional_data);

				$this->session->set_flashdata('success', 'Collection Updated Successfully');
				redirect("collection", 'refresh');
			}
			else {											
				$pay = $this->Collection_model->insert('collector_collection',$additional_data);

				$this->session->set_flashdata('success', 'Collection Added Successfully');
				redirect("collection", 'refresh');
			}
		}
		else
		{
			$this->data['title'] = 'Add Collection';
            
			$this->data['csrf'] = $this->_get_csrf_nonce();
			
			$this->data['years'] = $this->Payment_model->get_joda_fee();
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
			// $this->data['users'] = $this->Payment_model->get_Active_user(2,'')->result();						

			$this->_render_page('pages/collection' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}
	}

	public function get_collectors_total_collection() {

		if($this->ion_auth->is_admin()) {
			$data = $this->Collection_model->get();
		}
		else{
			$data = $this->Collection_model->get('','',$this->ion_auth->user()->row()->zone_id);
		}
		print_r(json_encode($data));
	}

	public function get_users_from_zone($id) {
		$data = $this->Payment_model->get_Active_user(2,$id)->result();
		foreach ($data as $value) {
			$value->username = str_replace('_',' ',ucwords($value->username));
		}
		print_r(json_encode($data));
	}

	public function edit($id) {
		
		$this->data['title'] = 'Edit Collection';
		$this->data['collection'] = $this->Collection_model->get($id)[0];
		$this->data['years'] = $this->Payment_model->get_joda_fee();
		$this->data['zones'] = $this->Payment_model->get_zones()->result();
		$this->data['users'] = $this->Payment_model->get_Active_user(2,'')->result();
		$this->data['csrf'] = $this->_get_csrf_nonce();

		$this->_render_page('pages/collection' . DIRECTORY_SEPARATOR . 'edit', $this->data);
	}

	public function delete($table,$id) {

		$this->Collection_model->delete($table,$id);

		print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
	}

	// Admin Collection
	public function admin() {
		
		if (!$this->ion_auth->is_admin()) 
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator to view this page.');
			redirect('/');
		}
		$this->data['title'] = 'Admin Collections';
		
		$this->data['acollect'] = $this->Collection_model->getadmin();
		
		$this->_render_page('pages/collection/admin' . DIRECTORY_SEPARATOR . 'index', $this->data);
		
	}
	
	public function addadmin() {

		if (!$this->ion_auth->is_admin()) 
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator to view this page.');
			redirect('/');
		}
		
		$this->form_validation->set_rules('date','Required','required');
		$this->form_validation->set_rules('amt_received','Required','required');

		if ($this->form_validation->run() === TRUE)
		{
			$zid = $this->input->post('zone');
			$amtrec = $this->input->post('amt_received');
			$year = $this->input->post('year');
			
			$additional_data = [
				'year' => $year,
				'zone' => $zid,
				'collector_id' => $this->input->post('collector_id'),					
				'amt_received' => $amtrec,
				'amt_use_in_expense' => 0,
				'date' => $this->input->post('date'),
				'remark' => $this->input->post('remark'),
				'created_at' => date('Y-m-d h:i:s'),
			];

			// if($id) {								
			// 	$pay = $this->Collection_model->update($id,$additional_data);

			// 	$this->session->set_flashdata('success', 'Collection Updated Successfully');
			// 	redirect("collection", 'refresh');
			// }
			// else {											
				$pay = $this->Collection_model->insert('admin_collection',$additional_data);
				if ($pay) {
					$getcollec = $this->db->query('SELECT * FROM collector_collection WHERE zone='.$zid.' && year='.$year);
					$getamt = $getcollec->result()[0];

					$this->db->where('collector_collection.year',$year);
					$this->db->where('collector_collection.zone',$zid);
					$done = $this->db->update('collector_collection', ['amt_received_by_admin' => $amtrec + $getamt->amt_received_by_admin]);
				}

				$this->session->set_flashdata('success', 'Collection Added Successfully');
				redirect("collection/admin", 'refresh');
			// }
		}
		else
		{
			$this->data['title'] = 'Add Collection';
            
			$this->data['csrf'] = $this->_get_csrf_nonce();
			
			$this->data['years'] = $this->Payment_model->get_joda_fee();
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
			// $this->data['users'] = $this->Payment_model->get_Active_user(2,'')->result();						

			$this->_render_page('pages/collection/admin' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}
	}

	public function get_pending_amount_from_collector($zid,$year) {
		$getpamt = $this->db->query('SELECT * FROM collector_collection WHERE zone='.$zid.' && year='.$year);
		$data = $getpamt->result();
		print_r(json_encode($data));
	}

	public function get_admin_total_collection() {
		$data = $this->Collection_model->getadmin();
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
