<?php if (!defined('BASEPATH'))	exit ('No direct script access allowed');
class Group extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'GROUP';
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	
	function index(){	
		$this->m_master->get_login();
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/group/v_index_group' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		
		$data['page_title'] = "Group Akses";
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'id_group',//default order sort
				1 => 'nama_group',
				2 => 'kd_group'
		);
		return $column_array;
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
		/*Menghitung total record didalam database*/
		$total = count($this->m_master->get_table_filter('tb_group'));
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
		$query = $this->m_master->get_table_filter('tb_group','',$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);	
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		 yang mengandung keyword tertentu
		 */
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_table_filter('tb_group'));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
	
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			$actions = ($row->kd_group != 'SA') ? '<button title="'.$this->config->config['delete'].'" type="button" id="delete" class="btn btn-danger btn-circle" onclick="ajaxDelete(\''.base_url('bo/'.$this->class.'/delete').'\',\''.$row->id_group.'-'.$row->kd_group.'\',\'tes\')"><i class="icon-trash"></i></button>
												   <a href="'.base_url('bo/'.$this->class.'/form/'.$row->id_group).'" title="'.$this->config->config['edit'].'" class="btn btn-info btn-circle"><i class="icon-edit"></i></a>
												   <a href="'.base_url('bo/'.$this->class.'/form/'.$row->id_group.'/1').'" title="'.$this->config->config['akses'].'" class="btn btn-success btn-circle"><i class="icon-sitemap"></i></a>' : '';
			//show in html
			$output['data'][]=array($nomor_urut,
									$row->nama_group,
									$row->kd_group,
									$actions
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function form($id='',$detail=''){
		$this->m_master->get_login();		
		$view = 'bo/group/v_crud_group';
		if ($id !=''){
			//akses edit
			$action = 'edit';			
			$data['page_title'] = "Ubah Group";
			$sql = $this->m_master->get_table_filter('tb_group',array('id_group'=>$id));
			foreach ($sql as $row)
				foreach ($row as $key=>$val){
					$data[$key] = $val;
			}
			if ($detail != ''){
				//show view detail
				$action = 'view';
				$data['page_title'] = "Akses Group";
				$data['nama_group'] = $row->nama_group;
				$data['get_parent_modul'] = $this->m_master->get_modul_group(array('B.kd_group'=>$row->kd_group,'B.id_modul_parent'=>0));
				$view = 'bo/group/v_akses_group';
			}
		}else{
			//akses tambah
			$action = 'add';
			$data['page_title'] = "Tambah Group";
		}
		$priv = $this->m_master->get_priv($this->acces_code,$action);
		$main_page = (empty($priv)) ? $view : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		
		$data['class'] = $this->class;		
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function proses(){
		$post = $this->input->post();	
		$this->db->trans_start();	
		$id= $post['id'];//for update		
		$put['nama_group'] = $post['namagroup'];
		$put['kd_group'] = $post['kdgroup'];
		if ($this->cek_kdgroup($post['kdgroup']) == true){
			$this->session->set_flashdata('danger','kode group sudah terdaftar.');
			redirect('bo/'.$this->class);
		}else{
			if ($id != ''){
				//edit proses
				$res = $this->m_master->updatedata('tb_group',$put,array('id_group'=>$id));
				//edit tb_akses
				$res = $this->m_master->updatedata('tb_akses',array('kd_group'=>$post['kdgroup']),array('kd_group'=>$post['kd']));
				if ($res > 0){
					$this->session->set_flashdata('success','Ubah group berhasil.');
					redirect('bo/'.$this->class);
				}
			}else{
				//input proses
				$res = $this->m_master->insertdata('tb_group',$put);
				//input akses
				$sql = $this->m_master->get_table_column(array('id_modul','id_modul_parent'),'tb_modul');
				foreach ($sql as $row){
					$in = array('kd_group'=>$post['kdgroup'],
							'id_modul'=>$row->id_modul,
							'id_modul_parent'=>$row->id_modul_parent,
							'add_by'=>$this->addby,
							'add_update'=>$this->addby,
							'date_add'=>$this->datenow,
							'date_update'=>$this->datenow,
					);
					$merge = array_merge($in);
					$res = $this->m_master->insertdata('tb_akses',$merge);
				}
				if ($this->db->trans_status() === false){
					$this->db->trans_rollback();
				}else{
					$this->db->trans_complete();
					if ($res > 0){
						$this->session->set_flashdata('success','Tambah group berhasil.');
						redirect('bo/'.$this->class);
					}
				}
				
					
			}
		}	
		
	}
	function delete(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');		
		if (empty($priv)){
			$val = explode('-',$this->input->post('value'));
			$id_group = $val[0];
			$kd_group = $val[1];
			$res = $this->m_master->deletedata('tb_group',array('id_group'=>$id_group));
			$res = $this->m_master->deletedata('tb_akses',array('kd_group'=>$kd_group));
			if ($res){
				echo json_encode(array('error'=>0,'msg'=>'Hapus group berhasil'));
			}
		}else{
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
		
	}	
	function check_modul(){
		$priv = $this->m_master->get_priv($this->acces_code,'edit');
		if (empty($priv)){
			$val = explode('-',$this->input->post('value',true));
			$check = $this->input->post('check');
			$id_akses = $val[0];
			$priv = $val[1];
			$where = array('id_akses'=>$id_akses);
			if ($priv == 'active'){
				$set['active'] = $check;
				$msg = ($check == 1) ? 'Modul akses berhasil diaktifkan' : 'Modul Akses berhasil dimatikan';
			}else if ($priv == 'view'){
				$set['view'] = $check;
				$msg = ($check == 1) ? 'Modul akses menampilkan data berhasil diaktifkan' : 'Modul akses menampilkan data berhasil dimatikan';
			}
			else if ($priv == 'add'){
				$set['add'] = $check;
				$msg = ($check == 1) ? 'Modul akses tambah baru berhasil diaktifkan' : 'Modul Akses tambah baru berhasil dimatikan';
			}
			else if ($priv == 'edit'){
				$set['edit'] = $check;
				$msg = ($check == 1) ? 'Modul akses ubah data berhasil diaktifkan' : 'Modul akses ubah data berhasil dimatikan';
			}
			else if ($priv == 'delete'){
				$set['delete'] = $check;
				$msg = ($check == 1) ? 'Modul akses hapus data berhasil diaktifkan' : 'Modul akses hapus data berhasil dimatikan';
			}
			$res = $this->m_master->updatedata('tb_akses',$set,$where);
			echo json_encode(array('error'=>0,'msg'=>$msg));
		}else{
			
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
		
	}
	function cek_kdgroup($value=''){
		$val = ($value != '') ? $value : $this->input->post('value');
		$cek = $this->m_master->get_table_column('*','tb_group',array('kd_group'=>$val));		
		if (count($cek) > 0){
			echo 'Kode Group Sudah terdaftar, masukan kode group yang berbeda';
			return true;
		}
	
	}
	function check_all(){
		$priv = $this->m_master->get_priv($this->acces_code,'edit');
		if (empty($priv)){
			$val = explode('#',$this->input->post('value',true));
			$check = $this->input->post('check');
			$kd_group = $val[0];
			$priv = $val[1];
			$where = array('kd_group'=>$kd_group);
			$set[$priv] = $check;
			$msg = ($check == 1) ? 'Modul akses '.$priv.' berhasil diaktifkan semua' : 'Modul Akses '.$priv.' berhasil dimatikan semua';
			$res = $this->m_master->updatedata('tb_akses',$set,$where);
			echo json_encode(array('error'=>0,'msg'=>$msg));
		}else{
	
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
	
	}
	
}
