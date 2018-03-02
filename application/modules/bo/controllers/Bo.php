<?php if (!defined('BASEPATH')) exit('No direct access allowed');
class Bo extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
	}
	function index(){
		$this->m_master->get_login();			
		$this->load->view('bo/v_header');
		$this->load->view('bo/dashboard/v_dashboard');
		$this->load->view('bo/v_footer');
	}
	function destroy(){
		$this->session->sess_destroy();
	}
	function backup_db(){
		$this->load->dbutil();
		$setting = array('format'=>'zip','filename'=>'rivero.sql');
		$config =& $this->dbutil->backup($setting);
		$name_db = 'backup_rivero_db_'.date('d-m-Y-H-i-s').'.zip';
		$save = '/backup'.$name_db;
		write_file($save, $config);
		force_download($name_db,$config);
	}
	function proses(){
		$sn = $this->input->post('serialnumber');		
		if ($sn == SERIAL_KEY){
			$post['serial_key'] = $sn;
			$post['active']=1;
			$post['active_date'] = date('Y-m-d H:i:s');
			$x = $this->m_load_config->activasi();
			if (count($x) > 0){
				$res = $this->db->update('tb_active',$post,array('id_active'=>$x[0]->id_active));
			}else{
				$res = $this->db->insert('tb_active',$post);
			}			
			if ($res){
				$this->session->set_flashdata('success','System successfull activasi');
				redirect('bo');
			}
		}else{
			$this->session->set_flashdata('danger','Serial number invalid');
			redirect('bo');
		}
			
	}
}
