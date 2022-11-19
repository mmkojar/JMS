<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('ion_auth', TRUE);
		$this->load->helper('cookie');
		$this->lang->load('ion_auth');		
	}

	public function users_query($zid=FALSE) {
		$this->db->select("users.*,users_groups.group_id");
		$this->db->join('users_groups',"users_groups.user_id = users.id","inner");
		$this->db->join('zone_master','zone_master.id = users.zone_id','left');
		if($zid) {
			$this->db->where('users.zone_id',$zid);
		}
		return $this->db->from('users');
	}

	public function users_result() {
		$query=$this->db->get();
		return $query->result_array();
	}

	public function total_users($zid=FALSE) {
		
		$this->users_query($zid);
		$this->db->where_in('users_groups.group_id',[1,2,4]);
		return $this->users_result();
	}
	
	public function members($zid=FALSE) {
		
		$this->users_query($zid);
		$this->db->where('users_groups.group_id',3);
		return $this->users_result();
	}

	public function active_members($zid=FALSE) {

		$this->users_query($zid);
		$this->db->where('users.status','active');
		$this->db->where('users_groups.group_id',3);
		return $this->users_result();
	}

	public function inactive_members($zid=FALSE) {

		$this->users_query($zid);
		$this->db->where('users.status','inactive');
		$this->db->where('users_groups.group_id',3);
		return $this->users_result();
	}

	public function divorced_members($zid=FALSE) {

		$this->users_query($zid);
		$this->db->where('users.status','divorce');
		$this->db->where('users_groups.group_id',3);
		return $this->users_result();
	}

	public function zone_transfer_users($zid=FALSE) {

		$this->db->select("users.*,users_groups.group_id");
		$this->db->join('users_groups',"users_groups.user_id = users.id","inner");
		$this->db->join('zone_master','zone_master.id = users.zone_id','inner');		
		$this->db->where('users.old_zone_id !=','');
		$this->db->where('users_groups.group_id',3);
		if($zid) {
			$this->db->where('users.zone_id',$zid);
			$this->db->or_where('users.old_zone_id',$zid);
		}
		$this->db->from('users');
		$query=$this->db->get();
		return $query->result_array();
	}
}
