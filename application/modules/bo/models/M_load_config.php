<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class M_load_config extends CI_Model{
	public function __construct(){
		parent::__construct();		
		$_SESSION['date_now'] = date('Y-m-d H:i:s');
		$date_end = strtotime('2018-02-28');
		$date_now = strtotime(date('Y-m-d'));
		if ($date_end < $date_now){
			if (count($this->activasi()) < 1){
				$post['active']=0;
				$res = $this->db->insert('tb_active',$post);
			}			
		}
		
	}	
	function activasi(){		
		$sql = $this->db->select('*')->from('tb_active')->get()->result();
		return $sql;
	}
	
}
