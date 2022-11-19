<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function get_invoices_by_id($year,$id=FALSE,$zone_id=FALSE)
	{		
		$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
			fy_'.$year.'_receipt_no as receipt_no,
			fy_'.$year.'_receipt_date as receipt_date,
			fy_'.$year.'_receipt_amt as receipt_amt,
			fy_'.$year.'_balance_amt as balance_amt,
			fy_'.$year.'_balance_count_amt as balance_count_amt,
			fy_'.$year.'_remark as remark,
			users.username,users.status as user_status,zone_master.zone_name,invoices.id,invoices.zone_id');
		$this->db->join('users',"users.id = invoices.user_id","inner");
		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->from('invoices');
		// $this->db->where('fy_'.$year.'_amt_to_collect !=',0);
		if($id) {
			$this->db->where('invoices.id',$id);
			$query=$this->db->get();
			return $query->row();
		}
		if($zone_id) {
			$this->db->where('invoices.zone_id',$zone_id);
			$query=$this->db->get();
			return $query->result();
		}
		$query=$this->db->get();
		return $query->result();
	}
	
	public function get_receipts_by_id($year,$id=FALSE,$zone_id=FALSE)
	{
		$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
			invoices.fy_'.$year.'_receiver_name as receiver_name,
			invoices.fy_'.$year.'_receipt_no as receipt_no,
			invoices.fy_'.$year.'_receipt_date as receipt_date,
			invoices.fy_'.$year.'_receipt_amt as receipt_amt,
			invoices.fy_'.$year.'_adjustment_amt as adjustment_amt,
			invoices.fy_'.$year.'_balance_amt as balance_amt,
			invoices.fy_'.$year.'_balance_count_amt as balance_count_amt,
			fy_'.$year.'_remark as remark,
			users.username,users.status as user_status,zone_master.zone_name,invoices.id,invoices.zone_id');
		$this->db->join('users',"users.id = invoices.user_id","inner");
		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->where('fy_'.$year.'_balance_count_amt',0);
		$this->db->where('fy_'.$year.'_amt_to_collect !=',0);
		$this->db->where('fy_'.$year.'_receipt_amt !=',0);

		if($id) {
			$this->db->where('invoices.id',$id);
			$this->db->from('invoices');
			$query=$this->db->get();
			return $query->row();
		}
		else if($zone_id){
			$this->db->where('invoices.zone_id',$zone_id);
			$this->db->from('invoices');
			$query=$this->db->get();
			return $query->result();
		}
		else {						
			$this->db->from('invoices');
			$query=$this->db->get();
			return $query->result();
		}
	}
	
	public function update($id,$uid,$zid,$data) {
		if($id == 1) {
			$this->db->where('invoices.user_id',$uid);
		}
		else {
			$this->db->where('invoices.id',$uid);
		}
		$this->db->where('invoices.zone_id',$zid);
		$update = $this->db->update('invoices',$data);
		return TRUE;
	}
	
	public function get_zone_by_users($id) {
		$this->db->select("users.phone,zone_master.zone_name,zone_master.id as zone_id");
		$this->db->join('zone_master',"zone_master.id = users.zone_id","inner");
		$this->db->from('users');
		$this->db->where('users.id',$id);
		$query = $this->db->get();
		return $query->row();
	}	


	public function check_for_duplicate_payment($id,$year) {
		$this->db->select("invoices.*");
		$this->db->from('invoices');
		$this->db->where('invoices.user_id',$id);
		$this->db->where('invoices.fy_'.$year.'_balance_count_amt',0);	
		$query = $this->db->get();
		return $query;
	}

	public function get_pending_payments($id,$year) {

		$last_year = $year-1;

		$this->db->select("user_id,fy_".$last_year."_balance_amt as balance_amt");
		$this->db->from('invoices');
		$this->db->where('invoices.user_id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_joda_fee($year = FALSE) {
		if($year) {
			$this->db->select("fee_master.*");
			$this->db->from('fee_master');
			$this->db->where('fee_master.financial_year',$year);
			$query = $this->db->get();
			return $query->row();
		}
		$this->db->select('fee_master.*');
		$this->db->from('fee_master');
		$this->db->order_by('fee_master.financial_year','DESC');
		$query=$this->db->get();
		return $query->result();
	}

	public function get_zones($id=FALSE) {	
		$this->db->select('zone_master.*');
		$this->db->from('zone_master');
		$this->db->where('status','active');
		$this->db->order_by('zone_master.zone_name','ASC');
		if($id) {			
			$this->db->where('id',$id);
		}
		$query=$this->db->get();
		return $query;
	}

	public function get_Active_user($no=FALSE,$id=FALSE) {
		
		$this->db->select("users.*");
		$this->db->join('users_groups','users_groups.user_id = users.id','inner');
		$this->db->join('groups','groups.id = users_groups.group_id','inner');			
		$this->db->from('users'); 
		if($no == "") {
			$this->db->where('users.active',1);
			$this->db->where('users_groups.group_id',3);
		}
		else if($no == '1') {
			$this->db->where('users_groups.group_id',3);
		}
		else if($no == '2') {
			$this->db->where('users_groups.group_id',2);
		}
		$this->db->order_by('users.first_name','ASC');
		if($id) {
			$this->db->where('users.zone_id',$id);					
		}		
		$query = $this->db->get();
		return $query;
	}

	public function get_unpaid_users($id=FALSE) {
		
		header('Content-Type:Application/json');

		$getyears = $this->get_joda_fee();
		$year = [];
		foreach ($getyears as $y) {

			array_push($year,$y->financial_year);
		}

		foreach ($year as $key => $value) {
			$this->db->select('
				invoices.fy_'.$value.'_balance_count_amt'
			);
		}
		
		$this->db->from('invoices');
		$this->db->where('invoices.user_id',$id);		
		$query=$this->db->get();
		return $query->row();
		
	}

	public function _get_datatables_query($year,$zid=FALSE,$id=FALSE)
	{
		if($id == 1) {

			$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
				invoices.fy_'.$year.'_receiver_name as receiver_name,
				invoices.fy_'.$year.'_receipt_no as receipt_no,
				invoices.fy_'.$year.'_receipt_date as receipt_date,
				invoices.fy_'.$year.'_receipt_amt as receipt_amt,
				invoices.fy_'.$year.'_adjustment_amt as adjustment_amt,
				invoices.fy_'.$year.'_balance_amt as balance_amt,
				invoices.fy_'.$year.'_balance_count_amt as balance_count_amt,
				fy_'.$year.'_remark as remark,
				invoices.status as istatus,
				users.username,users.phone,users.first_name,users.father_name,users.status as user_status,
				zone_master.zone_name,invoices.id,
				invoices.zone_id,invoices.surname,surname_master.surname');
			$this->db->join('users',"users.id = invoices.user_id","inner");
			$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
			$this->db->join('surname_master','surname_master.id = users.last_name','left');
			$this->db->from('invoices');
			$this->db->where('fy_'.$year.'_balance_count_amt',0);
			$this->db->where('fy_'.$year.'_amt_to_collect !=',0);
			$this->db->where('fy_'.$year.'_receipt_amt !=',0);
			$this->db->order_by('users.first_name','ASC');
			if($zid) {
				$this->db->where('invoices.zone_id',$zid);
			}

			$column = array(
				'0' => 'invoices.id',
				'1' => 'first_name'.'father_name'.'surname',
				'2' => 'phone',			
				'3' => 'zone_name',			
				'4' => 'fy_'.$year.'_receipt_no',
				'5' => 'fy_'.$year.'_receipt_date',
				'6' => 'fy_'.$year.'_receipt_amt',
				'7' => 'fy_'.$year.'_adjustment_amt',
				'8' => 'fy_'.$year.'_balance_amt',
				'9' => 'fy_'.$year.'_receiver_name',
				'10' => 'invoices.zone_id',
				'11' => 'fy_'.$year.'_remark as remark',
				'12' => 'user_status',
				'13' => 'istatus'

			);
		}

		if($id == 2) {
			$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
				fy_'.$year.'_receipt_no as receipt_no,
				fy_'.$year.'_receipt_date as receipt_date,
				fy_'.$year.'_receipt_amt as receipt_amt,
				fy_'.$year.'_balance_amt as balance_amt,
				fy_'.$year.'_balance_count_amt as balance_count_amt,
				fy_'.$year.'_remark as remark,
				invoices.status as istatus,
				users.username,users.first_name,users.father_name,users.status as user_status,
				zone_master.zone_name,invoices.id,
				invoices.zone_id,invoices.surname,surname_master.surname');
			$this->db->join('users',"users.id = invoices.user_id","inner");
			$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
			$this->db->join('surname_master','surname_master.id = users.last_name','left');
			$this->db->from('invoices');
			// $this->db->where('fy_'.$year.'_amt_to_collect !=',0);
			$this->db->order_by('users.first_name','ASC');
			if($zid) {
				$this->db->where('invoices.zone_id',$zid);
			}

			$column = array(
				'0' => 'invoices.id',
				'1' => 'first_name'.'father_name'.'surname',
				'2' => 'zone_name',
				'3' => 'fy_'.$year.'_amt_to_collect',
				'4' => 'fy_'.$year.'_receipt_no',
				'5' => 'fy_'.$year.'_receipt_date',
				'6' => 'fy_'.$year.'_receipt_amt',
				'7' => 'fy_'.$year.'_balance_amt',
				'8' => 'invoices.zone_id',
				'9' => 'fy_'.$year.'_remark as remark',
				// '10' => 'user_status',
				'10' => 'istatus'
			);		
		}

		$search_column = array(
			'0' => 'username',
			'1' => 'invoices.status'
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
        	$this->db->where('invoices.surname',$_POST['filter_surname']);
        }
        else if(isset($_POST['filter_zone']) && $_POST['filter_zone'] !== '' && $_POST['filter_surname'] == '') {
        	$this->db->where('invoices.zone_id',$_POST['filter_zone']);
        }
        else if(isset($_POST['filter_surname'],$_POST['filter_zone']) && $_POST['filter_surname'] !== '' && $_POST['filter_zone'] !== '') {
        	$this->db->where('invoices.surname',$_POST['filter_surname']);
        	$this->db->where('invoices.zone_id',$_POST['filter_zone']);
        }

        if(isset($_POST['order'])) {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
	}

	function get_datatables($year,$zid=FALSE,$id=FALSE)
	{
		$this->_get_datatables_query($year,$zid,$id);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($year,$zid=FALSE,$id=FALSE)
	{
		$this->_get_datatables_query($year,$zid,$id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($table)
	{
		$this->db->from($table);
		return $this->db->count_all_results();
	}
}
