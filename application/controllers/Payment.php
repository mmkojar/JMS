<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language','sms']);
		$this->load->model('Payment_model');
		$this->load->model('Masters_model');
		$this->load->model('Books_model');
		$this->load->model('Sms_model');
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

	// For Payments & Receipts
	public function index() {

		$this->data['title'] = 'All Payments';

		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		if($this->ion_auth->is_admin()) {			
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}
		
		$this->_render_page('pages/payment' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}

	public function payments_api($year)
	{
		if($this->ion_auth->is_admin()) {
			$list = $this->Payment_model->get_datatables($year,'',1);
		}
		else {
			$list = $this->Payment_model->get_datatables($year,$this->ion_auth->user()->row()->zone_id,1);
		}
		
		$i = 1;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $requested) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $requested->first_name.' '.$requested->father_name.' '.$requested->surname;
			$row[] = $requested->phone;
			$row[] = $requested->zone_name;			
			$row[] = $requested->receipt_no ? $requested->receipt_no : '-';
			$row[] = $requested->receipt_date ? $requested->receipt_date : '-';
			$row[] = $requested->receipt_amt ? number_format($requested->receipt_amt,2, '.', '') : '0.00';
			$row[] = $requested->adjustment_amt ? number_format($requested->adjustment_amt,2, '.', '') : '0.00';
			$row[] = $requested->balance_amt ? number_format($requested->balance_amt,2, '.', '') : '0.00';
			$row[] = $requested->receiver_name ? $requested->receiver_name : '-';
			$row[] = $year;
			$row[] = $requested->remark ? $requested->remark : '-';
			if($requested->istatus == 'active') {
				$row[] = '<a class="btn btn-info text-white btn-sm view_receipt" year="'.$year.'" id="'.$requested->id.'"><i class="mdi mdi-eye"></i></a><a class="btn btn-success text-white btn-sm edit_payment" year="'.$year.'" id="'.$requested->id.'"><i class="mdi mdi-pencil"></i></a>';
			}
			else {
				$row[] = '';
			}			
			$row[] = $requested->istatus;
			$data[] = $row;
		}
		
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Payment_model->count_all('invoices'),
			"recordsFiltered" => $this->ion_auth->is_admin() ? $this->Payment_model->count_filtered($year,'',1) : $this->Payment_model->count_filtered($year,$this->ion_auth->user()->row()->zone_id,1),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}

	public function payments_api_by_id($year,$id=FALSE) {

		header("Content-Type: application/json; charset=UTF-8");
		
		if($this->ion_auth->is_admin()) {
			$data =  $this->Payment_model->get_receipts_by_id($year,$id);
		}
		else {
			$data = $this->Payment_model->get_receipts_by_id($year,$id,$this->ion_auth->user()->row()->zone_id);
		}
		if($id) {
			$data->username = str_replace('_',' ',ucwords($data->username));
		}
		else {
			foreach ($data as $value) {
				$value->username = str_replace('_',' ',ucwords($value->username));
			}	
		}

		print_r(json_encode($data));
	}

	// For Invoices

	public function invoices () {

		$this->data['title'] = "Total Invoices";
		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		if($this->ion_auth->is_admin()) {			
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}
		$this->_render_page('pages' . DIRECTORY_SEPARATOR . 'invoices', $this->data);
	}


	public function invoices_api($year)
	{
		if($this->ion_auth->is_admin()) {
			$list = $this->Payment_model->get_datatables($year,'',2);
		}
		else {
			$list = $this->Payment_model->get_datatables($year,$this->ion_auth->user()->row()->zone_id,2);
		}
		
		$i = 1;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $requested) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $requested->first_name.' '.$requested->father_name.' '.$requested->surname;
			$row[] = $requested->zone_name;
			$row[] = $requested->amt_to_collect ? number_format($requested->amt_to_collect,2, '.', '') : '0.00';
			$row[] = $requested->receipt_no ? $requested->receipt_no : '-';
			$row[] = $requested->receipt_date ? $requested->receipt_date : '-';
			$row[] = $requested->receipt_amt ? number_format($requested->receipt_amt,2, '.', '') : '0.00';
			$row[] = $requested->balance_amt ? number_format($requested->balance_amt,2, '.', '') : '0.00';
			$row[] = $year;
			$row[] = $requested->remark ? $requested->remark : '-';
			$row[] = '<a class="btn btn-success text-white btn-sm edit_invoices" year="'.$year.'" id="'.$requested->id.'"><i class="mdi mdi-pencil"></i></a>';
			/* if($requested->istatus == 'active') {
				$row[] = '<a class="btn btn-success btn-sm edit_invoices" year="'.$year.'" id="'.$requested->id.'"><i class="mdi mdi-pencil"></i></a>';
			}
			else {
				$row[] = '';
			}	 */		
			$row[] = $requested->istatus;
			$data[] = $row;
		}
		
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Payment_model->count_all('invoices'),
				"recordsFiltered" => $this->ion_auth->is_admin() ? $this->Payment_model->count_filtered($year,'',2) : $this->Payment_model->count_filtered($year,$this->ion_auth->user()->row()->zone_id,2),
				"data" => $data,
			);
		
		//output to json format
		echo json_encode($output);
	}

	public function invoices_api_by_id($year,$id=FALSE) {

		header("Content-Type: application/json; charset=UTF-8");
		
		if($this->ion_auth->is_admin()) {
			$data =  $this->Payment_model->get_invoices_by_id($year,$id);
		}
		else {
			$data = $this->Payment_model->get_invoices_by_id($year,$id,$this->ion_auth->user()->row()->zone_id);
		}
		if($id) {
			$data->username = str_replace('_',' ',ucwords($data->username));
		}
		else {
			foreach ($data as $value) {
				$value->username = str_replace('_',' ',ucwords($value->username));
			}	
		}
			
		print_r(json_encode($data));
	}

	public function add() {

		$checkY = $this->input->post('year');

		$this->form_validation->set_rules('receiver_name','Required','required');
		$this->form_validation->set_rules('receipt_no','Invoice No','required|is_unique[invoices.fy_'.$checkY.'_receipt_no]');

		if ($this->form_validation->run() === TRUE)
		{
			$year = $this->input->post('year');
			$uid = $this->input->post('user');
			$zid = $this->input->post('zone');

			$check_pay = $this->Payment_model->check_for_duplicate_payment($uid,$year);
			$get_amount_by_year = $this->Payment_model->get_joda_fee($year)->amount;
			
			if($this->input->post('adjustment_amt') == "" || $this->input->post('adjustment_amt') == 0) {
				$balance_amount = $get_amount_by_year - $this->input->post('receipt_amt');
				$adjustment_amount = 0;
			}
			else {
				$adjustment_amount = $this->input->post('adjustment_amt');
				$balance_amount = $get_amount_by_year - ($this->input->post('receipt_amt') + $this->input->post('adjustment_amt'));
			}

			if($check_pay->num_rows() == 0)
			{
				$additional_data = [
					'fy_'.$year.'_receipt_no' => $this->input->post('receipt_no'),
					'fy_'.$year.'_receipt_date' => $this->input->post('receipt_date'),
					'fy_'.$year.'_receipt_amt' => $this->input->post('receipt_amt'),					
					'fy_'.$year.'_balance_amt' => $balance_amount,
					'fy_'.$year.'_adjustment_amt' => $adjustment_amount,
					// 'fy_'.$year.'_balance_amt' => 0.00,
					'fy_'.$year.'_balance_count_amt' => 0.00,
					'fy_'.$year.'_receiver_name' => $this->input->post('receiver_name'),
					'fy_'.$year.'_remark' => $this->input->post('remark'),
					'updated_at' => date('Y-m-d'),
				];
				
				$pay = $this->Payment_model->update(1,$uid,$zid,$additional_data);
				if($pay) {
					
					$rno = $this->input->post('receipt_no');									
					$getbook = $this->Books_model->get_active_books_by_zone($zid);
					if($rno !== $getbook->last_page) {
						$current_page = $rno + 1;
					}
					else {
						$current_page = $rno;
					}
					$data = [
						'current_page' => $current_page
					];
					$upd = $this->Books_model->update_book_by_zone($zid,$data);
					if($upd) {
						if($rno == $getbook->last_page) {
							
							$this->db->where('books_issue.id',$getbook->id);
							$this->db->where('books_issue.zone_id',$zid);
							$done = $this->db->update('books_issue', ['status'=>'completed']);
						}
					}

					// Send Sms Code
					/*$query = $this->db->query(
						'SELECT users.*,surname_master.surname,surname_master.id 
						FROM `users` 
						LEFT JOIN `surname_master` ON
						surname_master.id = users.last_name 
						WHERE users.id='.$uid
					);
					$res = $query->row_array();
					
					$mbno = $res['phone'] ? $res['phone'] : $_POST['pmobile_no'];
					
					$tmpdata = [
						'USR_NAME'  => $res['first_name'].' '.$res['surname'],
						'AMT' => $this->input->post('receipt_amt'),
						'DATE' => date('j-M-Y', strtotime($this->input->post('receipt_date'))),
						'YEAR'=> $year,
						'RCP_NO'=> $this->input->post('receipt_no'),
						'COLL_NAME'=> $this->input->post('receiver_name')
					];
					$smstemplate = $this->Sms_model->get_sms_temp($tmpdata);
					$sendsms = sendSMS($smstemplate,$mbno);*/					
				
					$this->session->set_flashdata('success', 'Payment Added Successfully');
					redirect("payment/add", 'refresh');
				}
			}
			else {
				$this->session->set_flashdata('error', 'Payment Already Added For this User for selected Year');
				redirect("payment/add", 'refresh');
			}
		}
		else
		{
			$this->data['title'] = 'Add Payment';
			// For Admin and Members Who Collect The Amount
			// $this->data['check_users_id'] = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
			$this->data['get_user_data'] = $this->ion_auth->user()->row();
			$this->data['csrf'] = $this->_get_csrf_nonce();
			
			if($this->ion_auth->is_admin()) {
				$this->data['users'] = $this->Payment_model->get_Active_user()->result();
				// $this->data['zones'] = $this->Payment_model->get_zones()->result();
			}
			else {
				$this->data['users'] = $this->Payment_model->get_Active_user('',$this->ion_auth->user()->row()->zone_id)->result();
				$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
			}
			
			// $this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();

			$this->_render_page('pages/payment' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}
	}

	public function update($id) {
		
		$uid = $this->input->post('hidden_id');
		$year = $this->input->post('year');
		$zid = $this->input->post('zone_id');

		if($id == 1) {

			$get_amount_by_year = $this->Payment_model->get_joda_fee($year)->amount;
			if($this->input->post('adjustment_amt') == "" || $this->input->post('adjustment_amt') == 0) {
				$balance_amount = $get_amount_by_year - $this->input->post('receipt_amt');
				$adjustment_amount = 0;
			}
			else {
				$adjustment_amount = $this->input->post('adjustment_amt');
				$balance_amount = $get_amount_by_year - ($this->input->post('receipt_amt') + $this->input->post('adjustment_amt'));
			}

			$additional_data = [
				'fy_'.$year.'_receipt_no' => $this->input->post('receipt_no'),
				'fy_'.$year.'_receipt_date' => $this->input->post('receipt_date'),
				'fy_'.$year.'_receipt_amt' => $this->input->post('receipt_amt'),
				'fy_'.$year.'_balance_amt' => $balance_amount,
				'fy_'.$year.'_adjustment_amt' => $adjustment_amount,
				'fy_'.$year.'_receiver_name' => $this->input->post('receiver_name'),
				'fy_'.$year.'_remark' => $this->input->post('remark'),
				'updated_at' => date('Y-m-d'),
			];
		}
		else {
			$additional_data = [
				'fy_'.$year.'_amt_to_collect' => $this->input->post('amt_to_collect'),
				// 'fy_'.$year.'_receipt_amt' => $this->input->post('receipt_amt'),
				'fy_'.$year.'_balance_amt' => $this->input->post('balance_amt'),
				'fy_'.$year.'_balance_count_amt' => $this->input->post('balance_count_amt'),
				// 'fy_'.$year.'_remark' => $this->input->post('remark'),
				'updated_at' => date('Y-m-d'),
			];
		}
		
		$update = $this->Payment_model->update(2,$uid,$zid,$additional_data);

		if($update) {
			print_r(json_encode(['status'=>'success','msg'=>'Data Updated Successfully']));
		}	
	}

	public function zone_from_user($id) {
		$zone = $this->Payment_model->get_zone_by_users($id);
		print_r(json_encode($zone));
	}

	public function get_amount_from_year_api($year) {
		$year = $this->Payment_model->get_joda_fee($year);
		print_r(json_encode($year));
	}

	public function get_pending_payments_api($id,$year) {
		$pamount = $this->Payment_model->get_pending_payments($id,$year);
		$pamount->year = $year-1;
		if($pamount->balance_amt != 0) {
			$return['msg'] = $pamount->balance_amt. ' Rs Pending of year '.$pamount->year.' For Selected User';
			$return['data'] = $pamount;
		}
		else if($pamount->balance_amt == 0) {
			$return['msg'] = 'No Pending Amount of year '.$pamount->year.' For Selected User';		
			$return['data'] = $pamount;
		}
		else if($pamount->balance_amt == null) {
			$return['msg'] = 'No Data Found';	
			$return['data'] = $pamount;
		}
		print_r(json_encode($return));
	}

	public function get_pending_year($id=FALSE) {
		$data = $this->Payment_model->get_unpaid_users($id);
		$new_year = [];
		if(is_array($data) || is_object($data)) {
			foreach ($data as $key => $value) {
				if($value == '1') {
					$newkeys = (explode("_",$key));
					array_push($new_year,$newkeys[1]);
				}
			}
		}
		print_r(json_encode($new_year));
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
