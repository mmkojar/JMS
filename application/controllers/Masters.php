<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masters extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Masters_model');
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
	}

	// For Zone
	public function zone() {

		$this->data['title'] = 'Total Zones';

		$this->data['zones'] = $this->Masters_model->get('zone_master');
		
		$this->_render_page('pages/masters' . DIRECTORY_SEPARATOR . 'zone', $this->data);
	}

	public function zone_crud() {

		$this->form_validation->set_rules('zone_name','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			
			$id = $this->input->post('hidden_zone_id');

			$check_duplication = $this->Masters_model->check_for_duplication('zone_master','zone_name',$this->input->post('zone_name'));
			
			if($id) {
				/* $stop_updatation = $this->Masters_model->check_for_duplication('users','zone_id',$id);

				if(count($stop_updatation) > 0) {
					$this->session->set_flashdata('error', 'This Zone is Already Used You Cannot Update Now');
					redirect("masters/zone", 'refresh');
				}
				else { */
					$additional_data = [
						'zone_name' => $this->input->post('zone_name'),
						'status' => $this->input->post('status'),
					];
					
					$this->Masters_model->update('zone_master',$id,$additional_data);
					$this->session->set_flashdata('success', 'Zone Updated Successfully');
				// }
			}
			else {
				if(count($check_duplication) > 0) {
					$this->session->set_flashdata('error', 'No Duplication Allowed');
					redirect("masters/zone", 'refresh');
				}
				else {
					$additional_data = [
						'zone_name' => $this->input->post('zone_name'),
						'status' => 'active',
						'date' => date('Y-m-d h:m:s A'),
					];
					
					$this->Masters_model->insert('zone_master',$additional_data);
					$this->session->set_flashdata('success', 'Zone Added Successfully');
				}
				
			}
			

			redirect("masters/zone", 'refresh');						
		}		
	}

	// For Fee 	
	public function fee() {

		$this->data['title'] = 'Financial Years';

		$this->data['fees'] = $this->Masters_model->get('fee_master');
		
		$this->_render_page('pages/masters' . DIRECTORY_SEPARATOR . 'joda_fee', $this->data);
	}

	public function fee_crud() {

		$this->form_validation->set_rules('financial_year','Required','required');

		if ($this->form_validation->run() === TRUE)
		{			
			
			$id = $this->input->post('hidden_fee_id');		

			$check_duplication = $this->Masters_model->check_for_duplication('fee_master','financial_year',$this->input->post('financial_year'));

			
				if($id) {
					$check_update = $this->Masters_model->check_for_fee_update($this->input->post('financial_year'));

					if(count($check_update) > 0) {
						$this->session->set_flashdata('error', 'This Amount is Already Used in Transactions You Cannot Update Now');
					}
					else {
						$additional_data = [
							'financial_year' => $this->input->post('financial_year'),
							'period' => $this->input->post('period'),
							'amount' => $this->input->post('amount'),
						];
						
						$this->Masters_model->update('fee_master',$id,$additional_data);
						$this->session->set_flashdata('success', 'Data Updated Successfully');
					}
					
				}
				else {

					if(count($check_duplication) > 0) {
						$this->session->set_flashdata('error', 'No Duplication Allowed');
						redirect("masters/fee", 'refresh');
					}
					else {
						$additional_data = [
							'financial_year' => $this->input->post('financial_year'),
							'period' => $this->input->post('period'),
							'amount' => $this->input->post('amount'),
							'date' => date('Y-m-d h:m:s A'),
						];
						
						$this->Masters_model->insert('fee_master',$additional_data);
						$this->session->set_flashdata('success', 'Data Added Successfully');
					}

				}		
								

			redirect("masters/fee", 'refresh');						
		}		
	}

	// For Surname
	public function surnames() {

		$this->data['title'] = 'Surname Details';

		$this->data['surnames'] = $this->Masters_model->get_master_surname();
		
		$this->_render_page('pages/masters' . DIRECTORY_SEPARATOR . 'surname', $this->data);
	}

	public function surname_crud() {

		$this->form_validation->set_rules('surname','Required','required');

		if ($this->form_validation->run() === TRUE)
		{

			$check_duplication = $this->Masters_model->check_for_duplication('surname_master','surname',$this->input->post('surname'));
			
			$id = $this->input->post('hidden_surname_id');

			if($id) {
				/* $stop_updatation = $this->Masters_model->check_for_duplication('users','last_name',$id);

				if(count($stop_updatation) > 0) {
					$this->session->set_flashdata('error', 'This Name is Already Used You Cannot Update Now');
					redirect("masters/surnames", 'refresh');
				}
				else { */
					$additional_data = [
						'surname' => strtolower($this->input->post('surname')),
						'status' => $this->input->post('status')
					];
					
					$this->Masters_model->update('surname_master',$id,$additional_data);
					$this->session->set_flashdata('success', 'Data Updated Successfully');
				// }
				
			}
			else {
				if(count($check_duplication) > 0) {
					$this->session->set_flashdata('error', 'No Duplication Allowed');
					redirect("masters/surnames", 'refresh');
				}
				else {
					$additional_data = [
						'surname' => strtolower($this->input->post('surname')),		
						'status' => 'active',
						'date' => date('Y-m-d h:m:s A'),
					];
					
					$this->Masters_model->insert('surname_master',$additional_data);
					$this->session->set_flashdata('success', 'Data Added Successfully');
				}
			}							

			redirect("masters/surnames", 'refresh');
			
		}		
	}

	// For Surname
	public function expenses() {

		$this->data['title'] = 'Expense Details';

		$this->data['expenses'] = $this->Masters_model->get('expenses_master');
		
		$this->_render_page('pages/masters' . DIRECTORY_SEPARATOR . 'expenses', $this->data);
	}

	public function expense_crud() {

		$this->form_validation->set_rules('name','Required','required');

		if ($this->form_validation->run() === TRUE)
		{
		
			$id = $this->input->post('hidden_expense_id');			

			if($id) {								
				$additional_data = [
					'name' => strtolower($this->input->post('name')),
					'status' => $this->input->post('status')
				];
				$this->Masters_model->update('expenses_master',$id,$additional_data);
				$this->session->set_flashdata('success', 'Data Updated Successfully');							
			}
			else {
				$additional_data = [
					'name' => strtolower($this->input->post('name')),
					'status' => 'active'
				];				
				$this->Masters_model->insert('expenses_master',$additional_data);
				$this->session->set_flashdata('success', 'Data Added Successfully');
				
			}							

			redirect("masters/expenses", 'refresh');
			
		}		
	}

	public function delete_itmes($table,$id) {
		
		if($table == 'surname_master') {
			$stop_updatation = $this->Masters_model->check_for_duplication('users','last_name',$id);
		}
		if($table == 'zone_master') {
			$stop_updatation = $this->Masters_model->check_for_duplication('users','zone_id',$id);
		}
		if($table == 'fee_master') {
			$stop_updatation = $this->Masters_model->check_for_duplication('fee_master','id',$id);
		}

		if($table == 'expenses_master') {
			$del = $this->Masters_model->delete($table,$id);

			if($del) {
				print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
			}
			else {
				print_r(json_encode(['status'=>'1','msg'=>'Error']));			
				die;
			}
		}
		else {
			if(count($stop_updatation) > 0) {
				print_r(json_encode(['status'=>'1','msg'=>'You Cannot Delete Once It Used']));
				die;
			}
			else {
				$del = $this->Masters_model->delete($table,$id);
	
				if($del) {
					print_r(json_encode(['status'=>'-1','msg'=>'Successfully Deleted']));
				}
				else {
					print_r(json_encode(['status'=>'1','msg'=>'Error']));			
					die;
				}
			}
		}
				
		

		
	}

	// Get Data Api

	public function get_data($table,$id) {

		$data = $this->Masters_model->get($table,$id);
		print_r(json_encode($data));
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
