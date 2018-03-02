<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Mpuser extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);		
		$this->id = $this->session->userdata('id_user');
		$this->table = 'tb_user';
	}
	var $datenow;
	var $addby;
	var $class;
	var $id;
	var $table;
	function index(){
		$this->m_master->get_login();			
		$data['page_title'] = "User Account";
		$data['class'] = $this->class;
		$sql = $this->m_master->get_table_filter($this->table,array('id_user'=>$this->id));
		foreach ($sql as $row)
			foreach ($row as $key=>$val){
				$data[$key] = $val;
		}
		$this->load->view('bo/v_header',$data);
		$this->load->view('bo/user/v_crud_user_account');
		$this->load->view('bo/v_footer');
	}
	function user_update(){
		$email = $this->input->post('email',true);
		$oldpassword =  $this->input->post('oldpass',true);
		$newpassword = $this->input->post('newpass',true);
		if (strlen($newpassword) >= 8){
			$cek_login = $this->m_master->cek_login(array('id_user'=>$this->id));		
				foreach ($cek_login as $row){
					$passhash = $row->password;
					if (password_verify($oldpassword, $passhash)){
						//buat hash baru
						$hasspass = hash_password($newpassword);
						$res = $this->m_master->updatedata('tb_user',array('email'=>$email,'password'=>$hasspass),array('id_user'=>$this->id));
						if ($res){
							$this->session->set_flashdata('success','Ubah user account berhasil.');
							redirect('bo/'.$this->class);
						}
					}else{
						$this->session->set_flashdata('danger','Password lama tidak valid.');
						redirect('bo/'.$this->class);
					}
				}
		}else{
			$this->session->set_flashdata('danger','Panjang password baru tidak boleh kurang 8 digit.');
			redirect('bo/'.$this->class);
		}
		
	}
}
