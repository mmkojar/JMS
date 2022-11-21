<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->load->model('Dashboard_model');
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
			redirect('/message');
		}
	}

	public function index() {
		
		if($this->ion_auth->is_admin()) {			
			$this->data['total_users'] = $this->Dashboard_model->total_users();
			$this->data['members'] = $this->Dashboard_model->members();
			$this->data['active_members'] = $this->Dashboard_model->active_members();
			$this->data['inactive_members'] = $this->Dashboard_model->inactive_members();
			$this->data['divorced_members'] = $this->Dashboard_model->divorced_members();
			$this->data['zone_transfer'] = $this->Dashboard_model->zone_transfer_users();
		}
		else {
			$this->data['total_users'] = $this->Dashboard_model->total_users($this->ion_auth->user()->row()->zone_id);
			$this->data['members'] = $this->Dashboard_model->members($this->ion_auth->user()->row()->zone_id);
			$this->data['active_members'] = $this->Dashboard_model->active_members($this->ion_auth->user()->row()->zone_id);
			$this->data['inactive_members'] = $this->Dashboard_model->inactive_members($this->ion_auth->user()->row()->zone_id);
			$this->data['divorced_members'] = $this->Dashboard_model->divorced_members($this->ion_auth->user()->row()->zone_id);
			$this->data['zone_transfer'] = $this->Dashboard_model->zone_transfer_users($this->ion_auth->user()->row()->zone_id);
		}

		$this->data['title'] = 'Dashboard';

		$this->_render_page('Dashboard',$this->data);
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
