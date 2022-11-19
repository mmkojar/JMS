<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Message_model');
		$this->load->model('ion_auth_model');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group("notify")) 
		{
			// redirect them to the home page because they must be an administrator to view this
			$this->session->set_flashdata('error', 'You must be an administrator or Notification User to view this page.');
			redirect('/');
		}
	}

	public function debug($query) {
		echo '<pre>';
		print_r($query);
		echo '</pre>';
		die();
	}

	public function index() {
		
		// if(!$this->ion_auth->is_admin()) {
		
		// 	$this->session->set_flashdata('error','You Are Not Allowed To View That Page');
		// 	redirect('students');
		// }		
		
		$this->data['title'] = 'All Messages';
		$this->data['messages'] = $this->Message_model->get_messages();	
		
		$this->_render_page('pages/messages' . DIRECTORY_SEPARATOR . 'index', $this->data);
	}

	public function add() {

		$this->form_validation->set_rules('sender_name','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			if ($this->_valid_csrf_nonce() === FALSE)
			{
				$this->session->set_flashdata('success', $this->lang->line('error_csrf'));
				redirect('payment/add', 'refresh');
			}
			else {

				/*$check_pay = $this->Message_model->check_for_duplicate_payment($this->input->post('user'),date('Y'));
				if($check_pay->num_rows() == 0)
				{*/						
					$additional_data = [
						'sender_name' => $this->input->post('sender_name'),
						'title' => $this->input->post('title'),
						'message_en' => $this->input->post('message_en'),
						'message_gj' => $this->input->post('message_gj'),
						'date' => date('Y-m-d h:i:s A')
					];
					
					$message = $this->Message_model->insert($additional_data);

					/*$androidIds = "fmzZYTTS_kk:APA91bHybT4h7C60Ve5Ry8iZyYEm5lKLzZM45m_r5B17bzjGCj5qUrlLXt6pdu2Z-EDVSaU4hxhAOx2ouJBWl1t1K7rRJfV0mdSXpPgw-8G7Ttzwl3NhSHgbVMaTQdom6RW61pr0sIXr";

					if($message) {
						$this->load->helper('notification');
						sendFCMAndroid($this->input->post('message_en'),$this->input->post('title'),$androidIds,'Message');
					}*/

					$this->session->set_flashdata('success', 'Message Added Successfully');
					redirect("message", 'refresh');
				/*}
				else {
					$this->session->set_flashdata('error', 'Payment Already Added For this User for Current Year');
					redirect("payment/add", 'refresh');
				}*/
			}
			
		}
		else
		{
			$this->data['title'] = 'Add Message';
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['sender_names'] = ['@Mk' => 'Mohammed Kojar', '@EP' => 'Esak Piyarji'];			

			$this->_render_page('pages/messages' . DIRECTORY_SEPARATOR . 'add', $this->data);
		}		
	}	

	public function edit($id) {

		$this->form_validation->set_rules('sender_name','Required','required');

		if ($this->form_validation->run() === TRUE)
		{
			
			if ($this->_valid_csrf_nonce() === FALSE)
			{
				$this->session->set_flashdata('success', $this->lang->line('error_csrf'));
				redirect('message/add', 'refresh');
			}
			else {

				// $check_pay = $this->Message_model->check_for_duplicate_payment($this->input->post('user'),date('Y'));
				// if($check_pay->num_rows() == 0)
				// {		
					$additional_data = [
						'sender_name' => $this->input->post('sender_name'),
						'title' => $this->input->post('title'),
						'message_en' => $this->input->post('message_en'),
						'message_gj' => $this->input->post('message_gj'),
						'date' => date('Y-m-d h:i:s A')
					];

					$pay = $this->Message_model->update($id,$additional_data);
					
					$this->session->set_flashdata('success', 'Message Updated Successfully');
					redirect("message", 'refresh');	
				// }
				// else {
				// 	$this->session->set_flashdata('error', 'Payment Already Added For Selected User for Current Year');
				// 	redirect("message/edit/".$id, 'refresh');
				// }		
			}
		}
		else
		{
			
			$this->data['message'] = $this->Message_model->get_messages($id);			
			$this->data['sender_names'] = ['@Mk' => 'Mohammed Kojar', '@EP' => 'Esak Piyarji'];
			$this->data['title'] = 'Edit Mesage';
			$this->data['csrf'] = $this->_get_csrf_nonce();

			$this->_render_page('pages/messages' . DIRECTORY_SEPARATOR . 'edit', $this->data);
		}
	}

	public function delete($table,$id) {

		$this->Message_model->delete($table,$id);

		print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
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
