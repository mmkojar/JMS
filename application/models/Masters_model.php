<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masters_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}	

	public function get($table,$id = FALSE) {

		if($id) {
			$this->db->select($table.".*");
			$this->db->from($table);
			$this->db->where($table.'.id',$id);
			$query = $this->db->get();
			return $query->row();
		}
		if($table == 'surname_master') {
			$this->db->where('surname_master.status','active');
			$this->db->order_by('surname_master.surname','ASC');
		}
		$this->db->select($table.".*");
		$this->db->from($table);
		$query=$this->db->get();
		return $query->result();
	}

	public function get_master_surname() {
		$this->db->select("surname_master.*");
		$this->db->from('surname_master');
		$this->db->order_by('surname_master.surname','ASC');
		$query=$this->db->get();
		return $query->result();
	}

	public function insert($table,$data) {
				
		$this->db->insert($table, $data);
		$id = $this->db->insert_id();
		if($id) {
			if($table == 'fee_master') {
				$invoice_data = [
					'fy_'.$data['financial_year'].'_amt_to_collect' => $data['amount'],
					'fy_'.$data['financial_year'].'_balance_count_amt' => 1,
					'fy_'.$data['financial_year'].'_balance_amt' => $data['amount'],
				];
				$this->db->where('invoices.status','active');
				$done = $this->db->update('invoices', $invoice_data);
				/*if($done) {
					$default_amt = $this->db->query('ALTER TABLE invoices ALTER fy_'.$data['financial_year'].'_amt_to_collect SET DEFAULT '.$data['amount'].' ;');
					if($default_amt) {
						$defaultcount =  $this->db->query('ALTER TABLE invoices ALTER fy_'.$data['financial_year'].'_balance_count_amt SET DEFAULT 1.00 ;');
						if($defaultcount) {
							$this->db->query('ALTER TABLE invoices ALTER fy_'.$data['financial_year'].'_balance_amt SET DEFAULT '.$data['amount'].' ;');
						}
					}
				}*/
			}
		}
	}

	public function update($table,$id,$data) {
		$this->db->where($table.'.id',$id);
		$update = $this->db->update($table,$data);		
		if($table == 'fee_master') {
			$invoice_data = [
				'fy_'.$data['financial_year'].'_amt_to_collect' => $data['amount'],
				'fy_'.$data['financial_year'].'_balance_count_amt' => 1,
				'fy_'.$data['financial_year'].'_balance_amt' => $data['amount'],
			];
			$this->db->where('invoices.status','active');
			$done = $this->db->update('invoices', $invoice_data);
		}
	}

	public function check_for_fee_update($data) {
		$this->db->select('fy_'.$data.'_receipt_no');
		$this->db->from('invoices');
		$this->db->where('fy_'.$data.'_receipt_no IS NOT NULL', null, false);
		return $this->db->get()->result();			
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}

	public function check_for_duplication($table,$tblname,$param) {
		$this->db->select($table.".*");
		$this->db->from($table);
		$this->db->where($table.'.'.$tblname,$param);
		$query = $this->db->get();
		return $query->result();
	}


}
