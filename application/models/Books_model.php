<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Books_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}	

	public function get($id = FALSE) {

		$this->db->select("books_issue.*,zone_master.zone_name");
		$this->db->join('zone_master','zone_master.id = books_issue.zone_id','left');
		$this->db->from('books_issue');
		if($id) {
			$this->db->where('books_issue.id',$id);
			$query = $this->db->get();
			return $query->row();
		}	
		else {
			$query=$this->db->get();
			return $query->result();
		}
	}

	public function get_active_books_by_zone($zone) {
		$this->db->select("books_issue.*");
		$this->db->from('books_issue');
		$this->db->where('books_issue.zone_id',$zone);
		$this->db->where('books_issue.status','active');
		$query = $this->db->get();
		return $query->row();
	}

	public function insert($data) {

		$this->db->insert('books_issue', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update_book_by_zone($zid,$data) {

		$this->db->where('books_issue.status','active');
		$this->db->where('books_issue.zone_id',$zid);
		$done = $this->db->update('books_issue', $data);
		return $done;
	}

	public function update_status($id,$status) {

		$this->db->trans_begin();

		$this->db->update("books_issue", $status, array('id' => $id));

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->session->set_flashdata('error','Update Failed');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->session->set_flashdata('success','Update Successful');
		return TRUE;
	}

	public function delete($id) {
		$this->db->where('id',$id);
		$this->db->delete('books_issue');
		return true;		
	}

}
