<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class User extends CI_Controller{
	public function __construct(){
		parent::__construct();	
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'USER';
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	function index(){
		$this->m_master->get_login();
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/user/v_index_user' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
	
		$data['page_title'] = "Data User";
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'A.date_add',//default order sort
				1 => 'A.nama_depan',				
				2 => 'A.nama_belakang',
				3 => 'A.email',
				4 => 'B.nama_group',				
		);
		return $column_array;
	}
	function form($id='',$detail=''){
		$this->m_master->get_login();
		$view = 'bo/user/v_crud_user';
		if ($id !=''){
			//akses edit
			$action = 'edit';
			$data['page_title'] = "Ubah User";
			$sql = $this->m_master->get_user(array('id_user'=>$id));
			foreach ($sql as $row)
				foreach ($row as $key=>$val){
				$data[$key] = $val;
			}
			if ($detail != ''){
				//show view detail
				$action = 'view';
				$data['page_title'] = "Detail user";
				$view = 'bo/user/v_detail_user';
			}
		}else{
			//akses tambah
			$action = 'add';
			$data['page_title'] = "Tambah user";
		}
		$priv = $this->m_master->get_priv($this->acces_code,$action);
		$main_page = (empty($priv)) ? $view : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
	
		$data['class'] = $this->class;		
		$data['group'] = $this->m_master->get_akses_all();
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function get_records(){
		/*Mempersiapkan array tempat kita akan menampung semua data
		 yang nantinya akan server kirimkan ke client*/
		$output=array();
		/*data request dari client*/
		$request = $this->m_master->request_datatable();
	
		/*Token yang dikrimkan client, akan dikirim balik ke client*/
		$output['draw'] = $request['draw'];
	
		/*
		 $output['recordsTotal'] adalah total data sebelum difilter
		$output['recordsFiltered'] adalah total data ketika difilter
		Biasanya kedua duanya bernilai sama pada saat load default(Tanpa filter), maka kita assignment
		keduaduanya dengan nilai dari $total
		*/
		/*Menghitung total desa didalam database*/
		$total = count($this->m_master->get_user());
		$output['recordsTotal']= $output['recordsFiltered'] = $total;
	
		/*disini nantinya akan memuat data yang akan kita tampilkan
		 pada table client*/
		$output['data'] = array();
	
	
		/*
		 * jika keyword tidak kosong, maka menjalankan fungsi search
		* untuk ditampilkan di datable
		* */
		if($request['keyword'] !=""){
			/*menjalankan fungsi filter or_like*/
			$this->m_master->search_like($request['keyword'],$this->column());
		}
		/*Pencarian ke database*/
		$query = $this->m_master->get_user('',$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		yang mengandung keyword tertentu
		*/
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_user());
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
	
	
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			$actions = ($row->kd_group != 'SA') ? '<button title="'.$this->config->config['delete'].'" type="button" id="delete" class="btn btn-danger btn-circle" onclick="ajaxDelete(\''.base_url('bo/'.$this->class.'/delete').'\',\''.$row->id_user.'\',\'tes\')"><i class="icon-trash"></i></button>
									 			  <a href="'.base_url('bo/'.$this->class.'/form/'.$row->id_user).'" title="'.$this->config->config['edit'].'" class="btn btn-info btn-circle"><i class="icon-edit"></i></a>' : '';
			//show in html
			$output['data'][]=array($nomor_urut,					
					$row->nama_depan,
					$row->nama_belakang,
					$row->email,
					$row->nama_group,					
					$actions
									
			);
			$nomor_urut++;
		}
		echo json_encode($output);	
	}
	function proses(){
		$post = $this->input->post();
		$id= $post['id'];//for update		
		$put['nama_depan'] = $post['namadepan'];
		$put['nama_belakang'] = $post['namabelakang'];		
		$put['email'] = $post['email'];		
		$put['kd_group'] = $post['group'];		
		if ($id != ''){
			//edit proses
			if ($post['password'] != ''){
				$put['password'] = hash_password($post['password']);
			}
			$res = $this->m_master->updatedata('tb_user',$put,array('id_user'=>$id));
			if ($res > 0){
				$this->session->set_flashdata('success','Ubah data user berhasil.');
				redirect('bo/'.$this->class);
			}
		}else{
			//input proses
			$put['password'] = hash_password($post['password']);
			$put['add_by'] = $this->addby;
			$put['date_add'] = $this->datenow;
			$res = $this->m_master->insertdata('tb_user',$put);
			if ($res > 0){
				$this->session->set_flashdata('success','Tambah data user berhasil.');
				redirect('bo/'.$this->class);
			}
		}
	}
	function cek_email(){
		$val = $this->input->post('value');
		$cek = $this->m_master->get_user(array('email'=>$val));
		if (count($cek) > 0){
			echo 'Email Sudah terdaftar, masukan email yang berbeda';
		}
	
	}
	function lengt_pass(){
		$val = $this->input->post('value');
		if (strlen($val) < 8){
			echo 'Panjang password minimal harus 8 karakter';
		}
	}
	function delete(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');
		if (empty($priv)){
			$val = $this->input->post('value');
			$res = $this->m_master->updatedata('tb_user',array('deleted'=>0),array('id_user'=>$val));
			if ($res){
				echo json_encode(array('error'=>0,'msg'=>'Hapus data User berhasil'));
			}
		}else{
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
	
	}
}