<?php if (!defined('BASEPATH')) exit('No direct access allowed');
class Mplogin extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	function index(){			
		($this->session->userdata('login_admin') == true) ? redirect('bo') : '';
		//echo $this->input->post('email');die;
		$this->form_validation->set_rules('email','Email','required|callback_email_check');
		$this->form_validation->set_rules('password','Password','required|min_length[8]|callback_login_check');
		if ($this->form_validation->run() == false){
			$this->load->view("bo/v_login");
		}
		
	}
	function email_check($str){
		if (format_email($str) == false){			
			$this->form_validation->set_message('email_check','Format email tidak benar!');
			return false;
		}else{
			return true;
		}
	}
	function login_check($str){		
		$email = $this->input->post('email',true);		
		$password = $str;
		$cek_login = $this->m_master->cek_login(array('email'=>$email));
		if (count($cek_login) > 0){
			foreach ($cek_login as $row){
				$passhash = $row->password;
				if (password_verify($password, $passhash)){
					$sess_data['login_admin'] = true;
					$sess_data['id_user'] = $row->id_user;					
					$sess_data['nama_depan'] = $row->nama_depan;
					$sess_data['nama_belakang'] = $row->nama_belakang;
					$sess_data['kd_group'] = $row->kd_group;					
					$this->session->set_userdata($sess_data);
					redirect('bo');
				}else{
					$this->form_validation->set_message('login_check','Password tidak valid!');
					return false;
				}
			}
		}else{
			$this->form_validation->set_message('login_check','Email tidak terdaftar!');
			return false;
		}
	}
	function logout(){
		$this->session->sess_destroy();
		redirect('bo');
	}
}
