<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth']);
		$this->lang->load('ion_auth');		
	}

	public function get_sms($id = FALSE) {

		if($id) {
			$this->db->select("sms_tempate.*");
			$this->db->from('sms_tempate');
			$this->db->where('sms_tempate.id',$id);
			$query = $this->db->get();
			return $query->row();
		}
		$this->db->select("sms_tempate.*");
		$this->db->from('sms_tempate');
		$query=$this->db->get();
		return $query->result_array();
	}

    public function get_sms_temp($tmpdata) {
        $this->db->select("sms_tempate.*");
		$this->db->where('status',1);
		$this->db->from('sms_tempate');
		$query=$this->db->get();
		$template =  $query->row_array();
		
		$tmpdata = array(
			'USR_NAME'  => $tmpdata['USR_NAME'],
			'AMT' => $tmpdata['AMT'],
			'DATE' => $tmpdata['DATE'],
			'YEAR'=> $tmpdata['YEAR'],
			'RCP_NO'=> $tmpdata['RCP_NO'],
			'COLL_NAME'=> $tmpdata['COLL_NAME']
		);
		$pattern = '[%s]';
		foreach($tmpdata as $key=>$val){
			$varMap[sprintf($pattern,$key)] = $val;
		}

		$smstemplate = strtr($template['content'],$varMap);

		return $smstemplate;
    }

	public function insert($data) {
				
		$this->db->insert('sms_tempate', $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function update($id,$data) {
		$this->db->where('sms_tempate.id',$id);
		$update = $this->db->update('sms_tempate',$data);		
	}

	public function delete($table,$id) {
		$this->db->where('id',$id);
		$this->db->delete($table);
		return true;		
	}	
	
}
