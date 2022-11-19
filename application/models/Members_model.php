<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Members_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function _get_datatables_query($zid=FALSE)
	{
		$this->db->select('users.*,zone_master.zone_name,users_groups.group_id,surname_master.surname');
		$this->db->join('zone_master','zone_master.id = users.zone_id','left');
		$this->db->join('surname_master','surname_master.id = users.last_name','left');
		$this->db->join('users_groups','users_groups.user_id = users.id','inner');
		$this->db->from('users');
		if($zid) {
			$this->db->where('users.zone_id',$zid);
		}
		$this->db->order_by('users.first_name','ASC');
		$this->db->where('users_groups.group_id',3);
		$column = array(
			'0' => 'id',
			'1' => 'first_name'.'father_name'.'surname',
			'2' => 'zone_name',
			'3' => 'phone',
			'4' => 'email',
			'5' => 'joining_date',
			'6' => 'expiry_date',
			'7' => 'divorce_date',
			'8' => 'transfer_date',
			'9' => 'status',
			'10' => 'created_on',
		);

		$search_column = array(
			'0' => 'username',
			'1' => 'phone',
			'2' => 'email',
			'3' => 'joining_date',
			'4' => 'expiry_date',
			'5' => 'transfer_date',
			'6' => 'users.status',
		);
		
		$i = 0;
        // loop searchable columns 
        foreach($search_column as $item){
            // if datatable send POST for search
            if($_POST['search']['value']) {
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, str_replace(' ', '_', $_POST['search']['value']));
                }else{
                    $this->db->or_like($item, str_replace(' ', '_', $_POST['search']['value']));
                }
                
                // last loop
                if(count($search_column) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
        
        if(isset($_POST['filter_surname']) && $_POST['filter_surname'] !== '' && $_POST['filter_zone'] == '')  {
        	$this->db->where('users.last_name',$_POST['filter_surname']);
        }
        else if(isset($_POST['filter_zone']) && $_POST['filter_zone'] !== '' && $_POST['filter_surname'] == '') {
        	$this->db->where('users.zone_id',$_POST['filter_zone']);
        }
        else if(isset($_POST['filter_surname'],$_POST['filter_zone']) && $_POST['filter_surname'] !== '' && $_POST['filter_zone'] !== '') {
        	$this->db->where('users.last_name',$_POST['filter_surname']);
        	$this->db->where('users.zone_id',$_POST['filter_zone']);
        }

        if(isset($_POST['order'])) {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
	}

	function get_datatables($zid=FALSE)
	{
		$this->_get_datatables_query($zid);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($zid=FALSE)
	{
		$this->_get_datatables_query($zid);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($table)
	{
		$this->db->from($table);
		return $this->db->count_all_results();
	}
}
