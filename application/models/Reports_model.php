<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->load->model('Payment_model');
		$this->lang->load('ion_auth');		
	}

	public function get_reports($zid=FALSE,$year=FALSE) {
	
		$this->db->select('invoices.fy_'.$year.'_balance_amt as balance_amt,
		invoices.fy_'.$year.'_balance_count_amt as balance_count_amt,
		invoices.fy_'.$year.'_receipt_amt as receipt_amt,
		invoices.fy_'.$year.'_adjustment_amt as adjustment_amt,
		invoices.zone_id,
		zone_master.zone_name,surname_master.surname');
		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->join('surname_master',"surname_master.id = invoices.surname","inner");
		$this->db->where('fy_'.$year.'_amt_to_collect !=',0);
		// $this->db->where('invoices.status','active');
		$this->db->order_by('zone_master.zone_name','ASC');
		$this->db->order_by('surname_master.surname','ASC');
		if($zid) {
			$this->db->where('invoices.zone_id',$zid);
		}
		$this->db->from('invoices');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_area_surname_wise_reports($id,$param) {

		$getyears = $this->Payment_model->get_joda_fee();
		$year = [];
		foreach ($getyears as $y) {
			array_push($year,$y->financial_year);
		}

		foreach ($year as $key => $value) {
			if($param == 'a') {
				$this->db->select('
					invoices.zone_id,
					zone_master.zone_name,
					fy_'.$value.'_amt_to_collect,
					fy_'.$value.'_receipt_amt,
					fy_'.$value.'_balance_amt,
					fy_'.$value.'_balance_count_amt,
					fy_'.$value.'_adjustment_amt'
				);
			}
			else {
				$this->db->select('
					invoices.surname as sid,
					surname_master.surname as sname,
					fy_'.$value.'_amt_to_collect,
					fy_'.$value.'_receipt_amt,
					fy_'.$value.'_balance_amt,
					fy_'.$value.'_balance_count_amt,
					fy_'.$value.'_adjustment_amt'
				);
			}
		}

		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->join('surname_master',"surname_master.id = invoices.surname","inner");
		$this->db->from('invoices');
		if($param == 'a') {
			$this->db->where('invoices.zone_id',$id);
		} else {
			$this->db->where('invoices.surname',$id);
		}
		$query=$this->db->get();
		$result =  $query->result_array();
		if(is_array($result) || is_object($result)) {
			foreach ($year as $key => $value) {				
				$this->db->where('fy_'.$value.'_amt_to_collect !=',0);
			}
			$result['year'] = $year;
		}
		return $result;

	}

	/*public function get_outstanding_reports($year,$zone_id=FALSE)
	{
		$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
			fy_'.$year.'_receipt_amt as receipt_amt,
			fy_'.$year.'_balance_amt as balance_amt,
			fy_'.$year.'_balance_count_amt as balance_count_amt,
			fy_'.$year.'_receipt_amt,
			fy_'.$year.'_remark as remark,
			users.username,users.status as user_status,zone_master.zone_name');
		$this->db->join('users',"users.id = invoices.user_id","inner");
		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->where('fy_'.$year.'_balance_count_amt',1);
		$this->db->where('fy_'.$year.'_amt_to_collect !=',0);
		$this->db->from('invoices');
		if($zone_id){
			$this->db->where('invoices.zone_id',$zone_id);
			$query=$this->db->get();
			return $query->result();
		}
		$query=$this->db->get();
		return $query->result();
	}*/

	public function get_death_divorce_users($zid=FALSE,$status=FALSE) {
		$this->db->select('users.*,zone_master.zone_name,surname_master.surname');
		$this->db->from('users');
		$this->db->join('zone_master',"zone_master.id = users.zone_id","inner");
		$this->db->join('surname_master',"surname_master.id = users.last_name","inner");
		$this->db->where('users.status',$status);
		if($zid) {
			$this->db->where('users.zone_id',$zid);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_user_zone_transfer_api($zid=FALSE) {
		$this->db->select('users.*,Z1.zone_name as z1name,Z2.zone_name as z2name,surname_master.surname');
		$this->db->from('users');
		$this->db->join('zone_master as Z1',"Z1.id = users.zone_id","inner");
		$this->db->join('zone_master as Z2',"Z2.id = users.old_zone_id","inner");
		$this->db->join('surname_master',"surname_master.id = users.last_name","inner");
		$this->db->where('users.old_zone_id !=','');
		if($zid) {
			$this->db->where('users.zone_id',$zid);
			$this->db->or_where('users.old_zone_id',$zid);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_ouststanding_users($id=FALSE) {
		
		$getyears = $this->Payment_model->get_joda_fee();
		$year = [];
		foreach ($getyears as $y) {
			array_push($year,$y->financial_year);
		}

		foreach ($year as $key => $value) {
			$this->db->select('
				users.username,
				users.phone,
				users.status as user_status,
				invoices.status as invoice_status,
				zone_master.zone_name,
				fy_'.$value.'_receiver_name,
				fy_'.$value.'_amt_to_collect,
				fy_'.$value.'_receipt_no,
				fy_'.$value.'_receipt_date,
				fy_'.$value.'_receipt_amt,
				fy_'.$value.'_balance_amt,
				fy_'.$value.'_balance_count_amt,
				fy_'.$value.'_adjustment_amt,
				fy_'.$value.'_remark'
			);
		}
		
		$this->db->join('users',"users.id = invoices.user_id","inner");
		$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
		$this->db->from('invoices');
		$this->db->where('invoices.user_id',$id);		
		$query=$this->db->get();
		$result =  $query->row();
		if(is_array($result) || is_object($result)) {
			foreach ($year as $key => $value) {
				$a = 'fy_'.$value.'_receiver_name';
				$b = 'fy_'.$value.'_amt_to_collect';
				$c = 'fy_'.$value.'_receipt_no';
				$d = 'fy_'.$value.'_receipt_date';
				$e = 'fy_'.$value.'_balance_amt';
				$f = 'fy_'.$value.'_balance_count_amt';
				$g = 'fy_'.$value.'_adjustment_amt';
				$h = 'fy_'.$value.'_receipt_amt';
				$i = 'fy_'.$value.'_remark';
				$result->$a = $result->$a ? $result->$a : '-';
				$result->$b = $result->$b ? number_format($result->$b,2, '.', '') : '0.00';
				$result->$c = $result->$c ? $result->$c : '-';
				$result->$d = $result->$d ? $result->$d : '-';
				$result->$e = $result->$e ? number_format($result->$e,2, '.', '') : '0.00';
				$result->$f = $result->$f ? number_format($result->$f,2, '.', '') : '0.00';
				$result->$g = $result->$g ? number_format($result->$g,2, '.', '') : '0.00';
				$result->$h = $result->$h ? number_format($result->$h,2, '.', '') : '0.00';
				$result->$i = $result->$i ? $result->$i : '-';				
			}
			$result->year = $year;
		}
		return $result;
	}

	public function _get_datatables_query($year,$zid=FALSE,$id=FALSE)
	{
		if($id == 1) {

			$this->db->select('fy_'.$year.'_amt_to_collect as amt_to_collect,
				fy_'.$year.'_receipt_amt as receipt_amt,
				fy_'.$year.'_balance_amt as balance_amt,
				fy_'.$year.'_adjustment_amt as adjustment_amt,
				fy_'.$year.'_balance_count_amt as balance_count_amt,
				fy_'.$year.'_remark as remark,
				invoices.status as istatus,
				users.username,users.phone,users.first_name,users.father_name,users.status as user_status,
				zone_master.zone_name,invoices.id,
				invoices.zone_id,invoices.surname,surname_master.surname');
			$this->db->join('users',"users.id = invoices.user_id","inner");
			$this->db->join('zone_master',"zone_master.id = invoices.zone_id","inner");
			$this->db->join('surname_master','surname_master.id = users.last_name','left');
			// $this->db->where('fy_'.$year.'_balance_count_amt',1);
			$this->db->where('fy_'.$year.'_balance_amt !=',0);
			$this->db->where('fy_'.$year.'_amt_to_collect !=',0);
			$this->db->order_by('users.first_name','ASC');
			$this->db->from('invoices');
			if($zid) {
				$this->db->where('invoices.zone_id',$zid);
			}

			$column = array(
				'0' => 'invoices.id',
				'1' => 'first_name'.'father_name'.'surname',
				'2' => 'phone',
				'3' => 'zone_name',			
				'4' => 'fy_'.$year.'_amt_to_collect',
				'5' => 'fy_'.$year.'_receipt_amt',
				'6' => 'fy_'.$year.'_adjustment_amt',
				'7' => 'fy_'.$year.'_balance_amt',				
				'8' => 'fy_'.$year.'_balance_count_amt',
				'9' => 'invoices.zone_id',
				'10' => 'fy_'.$year.'_remark as remark',
				'11' => 'istatus'
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
