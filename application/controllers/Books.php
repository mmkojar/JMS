<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Books extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Books_model');
		$this->load->model('Payment_model');
		$this->load->model('ion_auth_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin()) 
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator to view this page.');
			redirect('/');
		}
	}

	// For Books
	public function index() {

		$this->data['title'] = 'All Books';

		$this->data['books'] = $this->Books_model->get();

		/*if($this->ion_auth->is_admin()) {
			$this->data['zones'] = $this->Books_model->get_zones()->result();
		}
		else {
			$this->data['zones'] = $this->Books_model->get_zones($this->ion_auth->user()->row()->zone_id)->result();
		}*/
		
		$this->_render_page('pages/books' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}

	public function add() {

        $this->data['title'] = "Add Books";

        $zones = $this->Payment_model->get_zones()->result();
    	$output = '';
    	foreach($zones as $row) {
    		 $output .= '<optgroup><option value="'.$row->id.'">'.trim($row->zone_name).'</option></optgroup>';
    	}
    	$this->data['zones'] = $output;

        $this->data['csrf'] = $this->_get_csrf_nonce();

        $this->_render_page('pages/books/' . DIRECTORY_SEPARATOR . 'add', $this->data);
    }

	public function insert() {

		unset($_POST['submit']);

    	if(!empty($_POST["zone_id"])) {
    		for($i = 0; $i < count($_POST["zone_id"]); $i++)
	    	{
	    		if(!empty($_POST["zone_id"][$i])) {

		    		$additional_data = [
		    			'zone_id' => $_POST["zone_id"][$i],
		    			'book_no' => $_POST["book_no"][$i],
		    			'page_range' => $_POST["page_range"][$i],
		    			'current_page' => $_POST["current_page"][$i],
		    			'last_page' => $_POST["last_page"][$i],
		    			'status' => 'inactive',
		    			'created_at' => date('Y-m-d h:i:s'),
		    		];
										
		    		$this->Books_model->insert($additional_data);					
				}
	    	}
    	}

    	print_r(json_encode(['status'=>'success','msg'=>'Record Added Successfully']));
	}

	public function updateStatus($id,$st,$z) {
				
		$getActiveZone = $this->Books_model->get_active_books_by_zone($z);

		if($st == '1') {
			if($getActiveZone) {
				$this->session->set_flashdata('error',' At a time Only One Book Can be Active For a Particular Zone <br>(એક સમયે ફક્ત એક જ પુસ્તક ચોક્કસ ઝોન માટે સક્રિય હોઈ શકે છે)');
				redirect('Books','refresh');
			}
		}
		
		$status = ['status' => $st];
		$this->Books_model->update_status($id,$status);
		$this->session->set_flashdata('success','Status Updated');
		redirect('Books','refresh');
	}

	public function delete($id) {

		$del = $this->Books_model->delete($id);
		
		if($del) {
			print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
		}
		else {
			print_r(json_encode(['status'=>'1','msg'=>'Error']));			
			die;
		}
	}

	public function get_receipt_no($zone) {

		$getZone = $this->Books_model->get_active_books_by_zone($zone);
		
		print_r(json_encode(['status'=>'success','data'=>$getZone]));
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
