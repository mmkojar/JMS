<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Reports_model');
		$this->load->model('Masters_model');
		$this->load->model('Payment_model');
		$this->load->model('Collection_model');
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

	public function area_reports() {

		$this->data['title'] = 'All Area Wise Reports';
		
		$this->data['zones'] = $this->Payment_model->get_zones()->result();
		
		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();

		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'area', $this->data);
	}

	public function surname_reports() {

		$this->data['title'] = 'All Surname Wise Reports';
		
		$this->data['surnames'] = $this->Masters_model->get('surname_master');

		
		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();

		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'surname', $this->data);
	}

	public function outstanding_reports() {

		$this->data['title'] = 'All Outstanding Reports';
		
		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		if($this->ion_auth->is_admin()) {			
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}

		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'outstanding', $this->data);
	}

	public function user_outstanding_reports() {

		$this->data['title'] = 'Member Wise Reports';
				
		if($this->ion_auth->is_admin()) {
			$this->data['get_users'] = $this->Payment_model->get_Active_user(1)->result();
		}
		else {
			$this->data['get_users'] = $this->Payment_model->get_Active_user(1,$this->ion_auth->user()->row()->zone_id)->result();
		}

		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'user_outstanding', $this->data);
	}

	public function receipts_reports() {

		$this->data['title'] = 'All Receipts Reports';
		
		$this->data['get_payment_year'] = $this->Payment_model->get_joda_fee();
		$this->data['surnames'] = $this->Masters_model->get('surname_master');
		if($this->ion_auth->is_admin()) {			
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}

		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'receipts', $this->data);
	}

	public function death_reports() {

		$this->data['title'] = 'All Death Users';
		
		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'death', $this->data);
	}

	public function divorce_reports() {

		$this->data['title'] = 'All Divorce Users';
		
		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'divorce', $this->data);
	}

	public function zone_transfer() {

		$this->data['title'] = 'Users Transfer From Zone A To Zone B';
		
		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'zone_transfer', $this->data);
	}
	
	public function collection() {

		$this->data['title'] = 'Colection Report';
		if($this->ion_auth->is_admin()) {			
			$this->data['zones'] = $this->Payment_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Payment_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}
		$this->_render_page('pages/reports' . DIRECTORY_SEPARATOR . 'collection', $this->data);
	}

	//--Apiṣ--//
	public function reports_api($year=FALSE) {

		header("Content-Type: application/json; charset=UTF-8");
		
		if($this->ion_auth->is_admin()) {
			$data = $this->Reports_model->get_reports('',$year);
		}
		else {
			$data = $this->Reports_model->get_reports($this->ion_auth->user()->row()->zone_id,$year);
		}
		print_r(json_encode($data));
			
	}

	public function area_surname_wise_reports_api($id,$param) {

		header("Content-Type: application/json; charset=UTF-8");
				
		if($param == 'a') {
			if($this->ion_auth->is_admin()) {
				$data = $this->Reports_model->get_area_surname_wise_reports($id,$param);
			}
			else {
				$data = $this->Reports_model->get_area_surname_wise_reports($this->ion_auth->user()->row()->zone_id,$param);			
			}
		}
		else {
			$data = $this->Reports_model->get_area_surname_wise_reports($id,$param);
		}
		
				
		print_r(json_encode(['data'=>$data]));
		
	}

	public function outstanding_api($year=FALSE) {

		/*header("Content-Type: application/json; charset=UTF-8");
		

		if($this->ion_auth->is_admin()) {
			$data =  $this->Reports_model->get_outstanding_reports($year);
		}
		else {
			$data = $this->Reports_model->get_outstanding_reports($year,$this->ion_auth->user()->row()->zone_id);
		}		
		foreach ($data as $value) {
			$value->username = str_replace('_',' ',ucwords($value->username));
		}
		print_r(json_encode($data));*/

		if($this->ion_auth->is_admin()) {
			$list = $this->Reports_model->get_datatables($year,'',1);
		}
		else {
			$list = $this->Reports_model->get_datatables($year,$this->ion_auth->user()->row()->zone_id,1);
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
			$row[] = $requested->amt_to_collect ? number_format($requested->amt_to_collect,2, '.', '') : '0.00';
			$row[] = $requested->receipt_amt ? number_format($requested->receipt_amt,2, '.', '') : '0.00';
			$row[] = $requested->adjustment_amt ? number_format($requested->adjustment_amt,2, '.', '') : '0.00';
			$row[] = $requested->balance_amt ? number_format($requested->balance_amt,2, '.', '') : '0.00';
			$row[] = $requested->balance_count_amt ? number_format($requested->balance_count_amt,2, '.', '') : '0.00';
			$row[] = $year;
			$row[] = $requested->remark ? $requested->remark : '-';
			$row[] = $requested->istatus;
			$data[] = $row;
		}
		
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Reports_model->count_all('invoices'),
			"recordsFiltered" => $this->ion_auth->is_admin() ? $this->Reports_model->count_filtered($year,'',1) : $this->Reports_model->count_filtered($year,$this->ion_auth->user()->row()->zone_id,1),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}

	public function death_divorce_api($status) {

		header("Content-Type: application/json; charset=UTF-8");
		if($this->ion_auth->is_admin()) {		
			$data = $this->Reports_model->get_death_divorce_users('',$status);
		}
		else {
			$data = $this->Reports_model->get_death_divorce_users($this->ion_auth->user()->row()->zone_id,$status);
		}
		print_r(json_encode($data));
			
	}

	public function user_zone_transfer_api() {

		header("Content-Type: application/json; charset=UTF-8");
		
		if($this->ion_auth->is_admin()) {		
			$data = $this->Reports_model->get_user_zone_transfer_api();
		}
		else {
			$data = $this->Reports_model->get_user_zone_transfer_api($this->ion_auth->user()->row()->zone_id);
		}
	
		print_r(json_encode($data));
	}

	public function all_user_outstanding_api($id) {

		header("Content-Type: application/json; charset=UTF-8");
		
		$data = $this->Reports_model->get_ouststanding_users($id);
		
		print_r(json_encode($data));
	}
	
	public function collection_reports_api($zid=FALSE) {
		
		header("Content-Type: application/json; charset=UTF-8");
		if($zid) {
			$data = $this->Collection_model->get('','',$zid);
		}
		else {
			$data = $this->Collection_model->get();
		}
		
		print_r(json_encode($data));
	}

	public function collection_list_wise_api() {
		header("Content-Type: application/json; charset=UTF-8");
		if($this->ion_auth->is_admin()) {
			$data = $this->Collection_model->get();
			foreach ($data as $value) {
				$value->type = 'admin';
				$value->collector_name = str_replace('_',' ',ucwords($value->collector_name));
			}
		}
		else{
			$data =  $this->Collection_model->get('',$this->ion_auth->user()->row()->id,'');
			foreach ($data as $value) {
				$value->type = 'collector';
				$value->collector_name = str_replace('_',' ',ucwords($value->collector_name));
			}
		}

		print_r(json_encode($data));
	}

	/*public function receipts_api($year=FALSE) {

		header("Content-Type: application/json; charset=UTF-8");
		
		$data = $this->Payment_model->get_receipts($year);
		print_r(json_encode($data));		
	}*/

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
