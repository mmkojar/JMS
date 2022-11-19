<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Sms_model');
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
			$this->session->set_flashdata('error', 'You must be an administrator or Notification User to view this page.');
			redirect('/');
		}
	}

	public function index() {
		
		$this->data['title'] = 'All SMS';
		$this->data['smstmp'] = $this->Sms_model->get_sms();
		
		$this->_render_page('pages/sms' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}

	public function add() {

		$this->form_validation->set_rules('title','Required','required');
		$this->form_validation->set_rules('content','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			if ($this->_valid_csrf_nonce() === FALSE)
			{
				$this->session->set_flashdata('success', $this->lang->line('error_csrf'));
				redirect('sms/add', 'refresh');
			}
			else {
				
                $additional_data = [
                    'title' => $this->input->post('title'),
                    'content' => str_replace(['<p>', '</p>'],'',htmlspecialchars_decode($this->input->post('content'))),
                    'status' => '1',
                    'created_at' => date('Y-m-d h:i:s A')
                ];
                
                $message = $this->Sms_model->insert($additional_data);

                $this->session->set_flashdata('success', 'Sms Added Successfully');
                redirect("sms", 'refresh');				
			}			
		}
		else
		{
			$this->data['title'] = 'Add Sms';
			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('pages/sms' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}		
	}	

	public function edit($id) {

		$this->form_validation->set_rules('title','Required','required');
		$this->form_validation->set_rules('content','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			if ($this->_valid_csrf_nonce() === FALSE)
			{
				$this->session->set_flashdata('success', $this->lang->line('error_csrf'));
				redirect('sms/add', 'refresh');
			}
			else {					
                $additional_data = [
                    'title' => $this->input->post('title'),
                    'content' => str_replace(['<p>', '</p>'],'',htmlspecialchars_decode($this->input->post('content'))),
                    'status' =>  $this->input->post('status'),
                    'updated_at' => date('Y-m-d h:i:s A')
                ];

                $pay = $this->Sms_model->update($id,$additional_data);
                
                $this->session->set_flashdata('success', 'Sms Updated Successfully');
                redirect("sms", 'refresh');							
			}
		}
		else
		{
			
			$this->data['sms'] = $this->Sms_model->get_sms($id);			
			$this->data['title'] = 'Edit Sms';
			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('pages/sms' . DIRECTORY_SEPARATOR . 'edit', $this->data);
		}
	}

	public function delete($table,$id) {

		$this->Sms_model->delete($table,$id);

		print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
	}	


	public function send() {
		
		$this->load->helper('sms');

		$tmpdata = [
			'USR_NAME'  => 'Mohammed Mustafa kojar',
			'AMT' => '1000.00',
			'DATE' => '2022-08-10',
			'YEAR'=> '2022',
			'RCP_NO'=> '2541',
			'COLL_NAME'=> 'Esak piyarji'
		];
		$smstemplate = $this->Sms_model->get_sms_temp($tmpdata);
		$sendsms = sendSMS($smstemplate,'9769337909');
		print_r($sendsms);
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
