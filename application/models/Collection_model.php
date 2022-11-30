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

        $this->db->select("collector_collection.*,zone_master.zone_name,users.username as collector_name");
        $this->db->join('zone_master',"zone_master.id = collector_collection.zone","left");
		$this->db->join('users',"users.id = collector_collection.collector_id","left");
		$this->db->from('collector_collection');
		if($id) {
			$this->db->where('collector_collection.id',$id);
		}
		else if($cid) {
			$this->db->where('collector_collection.collector_id',$cid);
		}
        else if($zid) {
            $this->db->where('collector_collection.zone',$zid);
        }
		$query=$this->db->get();
		return $query->result();
	}

	public function getadmin($id=FALSE,$year=FALSE) {

        $this->db->select("admin_collection.*,zone_master.zone_name,users.username as collector_name");
        $this->db->join('zone_master',"zone_master.id = admin_collection.zone","left");
		$this->db->join('users',"users.id = admin_collection.collector_id","left");
		$this->db->from('admin_collection');
		if($id) {
			$this->db->where('admin_collection.id',$id);
		}
		else if($year) {
            $this->db->where('collector_collection.year',$year);
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
    
	public function insert($table,$data) {
				
		$this->db->insert($table, $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update($id,$data) {
		$this->db->where('collector_collection.id',$id);
		$update = $this->db->update('collector_collection',$data);		
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}	
	
}
