<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function get_messages($id = FALSE) {

		if($id) {
			$this->db->select("notification_message.*");
			$this->db->from('notification_message');
			$this->db->where('notification_message.id',$id);
			$query = $this->db->get();
			return $query->row();
		}
		$this->db->select("notification_message.*");
		$this->db->from('notification_message');
		$query=$this->db->get();
		return $query->result_array();
	}

	public function insert($data) {
				
		$this->db->insert('notification_message', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update($id,$data) {
		$this->db->where('notification_message.id',$id);
		$update = $this->db->update('notification_message',$data);		
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}	
	
}
