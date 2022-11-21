<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}	

	public function get_user_detail_for_check($no) {

		$this->db->select('users.*');
		$this->db->from('users');			
		$this->db->where('users.phone' , $no);
		$query=$this->db->get();
		return $query->row();

	}
	
}
