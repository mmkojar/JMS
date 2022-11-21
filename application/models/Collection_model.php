<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Collection_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function get($id=FALSE,$cid=FALSE,$zid=FALSE) {

        $this->db->select("collection_entries.*,zone_master.zone_name,users.username as collector_name");
        $this->db->join('zone_master',"zone_master.id = collection_entries.zone","left");
		$this->db->join('users',"users.id = collection_entries.collector_id","left");
		$this->db->from('collection_entries');
		if($id) {
			$this->db->where('collection_entries.id',$id);
		}
		else if($cid) {
			$this->db->where('collection_entries.collector_id',$cid);
		}
        else if($zid) {
            $this->db->where('collection_entries.zone',$zid);
        }
		$query=$this->db->get();
		return $query->result();
	}

    public function get_joda_count_by_zone($id) {
		$this->db->select('users.*');
        $this->db->join('users_groups','users_groups.user_id = users.id','left');
		$this->db->from('users');
		$this->db->where('users.zone_id',$id);
        $this->db->where('users_groups.group_id',3);
		return $this->db->count_all_results();
	}
    
	public function insert($data) {
				
		$this->db->insert('collection_entries', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update($id,$data) {
		$this->db->where('collection_entries.id',$id);
		$update = $this->db->update('collection_entries',$data);		
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}	
	
}
