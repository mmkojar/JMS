<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function get_expense($id = FALSE) {

		if($id) {
			$this->db->select("expenses.*");
			$this->db->from('expenses');
			$this->db->where('expenses.id',$id);
			$query = $this->db->get();
			return $query->row();
		}
		$this->db->select("expenses.*");
		$this->db->from('expenses');
		$query=$this->db->get();
		return $query->result_array();
	}

	public function insert($data) {
				
		$this->db->insert('expenses', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update($id,$data) {
		$this->db->where('expenses.id',$id);
		$update = $this->db->update('expenses',$data);		
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}	
	
}
