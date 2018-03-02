<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Produk extends  CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'PROD';
		$this->table = 'tb_product';
		
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	var $table;
	function index(){
		$this->m_master->get_login();
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/produk/v_index_produk' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		$data['page_title'] = "Main Assets";
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'id_product',//default order sort
				1 => 'product_code',				
				2 => 'supplier',
				3 => 'date_add',
				4 => 'add_by',				
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
		/*Menghitung total desa didalam database*/
		$total = count($this->m_master->get_table_filter($this->table));
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
		}elseif ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
		}elseif ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$request['date_to']);
		}
		/*Pencarian ke database*/
		$query = $this->m_master->get_table_filter($this->table,'',$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		yang mengandung keyword tertentu
		*/
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_table_filter($this->table));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
			$total = count($this->m_master->get_table_filter($this->table));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$request['date_to']);
			$total = count($this->m_master->get_table_filter($this->table));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
	
	
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			//show in html
			$output['data'][]=array($nomor_urut,
					$row->product_code,						
					$row->supplier,							
					$row->date_add,
					$row->add_by,
					'<button title="'.$this->config->config['delete'].'" type="button" id="delete" class="btn btn-danger btn-circle" onclick="ajaxDelete(\''.base_url('bo/'.$this->class.'/delete').'\',\''.$row->id_product.'\',\'tes\')"><i class="icon-trash"></i></button>
									 <a href="'.base_url('bo/'.$this->class.'/form/'.$row->id_product).'" title="'.$this->config->config['edit'].'" class="btn btn-info btn-circle"><i class="icon-edit"></i></a>'
									 
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function form($id='',$detail=''){
		$this->m_master->get_login();
		$view = 'bo/produk/v_crud_produk';
		if ($id !=''){
			//akses edit
			$action = 'edit';
			$data['page_title'] = "Ubah Produk";
			$sql = $this->m_master->get_table_filter($this->table,array('id_product'=>$id));
			foreach ($sql as $row)
				foreach ($row as $key=>$val){
				$data[$key] = $val;
			}
			if ($detail != ''){
				//show view detail
				$action = 'view';
				$data['page_title'] = "Detail produk";
				$view = 'bo/produk/v_detail_produk';
			}
		}else{
			//akses tambah
			$action = 'add';
			$data['page_title'] = "Tambah produk";
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
		$id= $post['id'];//for update	
		$put['product_code'] = $post['itemnumber'];			
		$put['supplier'] = $post['supplier'];	
		$put['date_update'] = $this->datenow;
		$put['add_update'] = $this->addby;		
		if ($id != ''){
			//edit proses			
			$res = $this->m_master->updatedata($this->table,$put,array('id_product'=>$id));
			if ($res > 0){
				$this->session->set_flashdata('success','Ubah produk berhasil.');
				redirect('bo/'.$this->class);
			}
		}else{
			//input proses			
			$put['add_by'] = $this->addby;
			$put['date_add'] = $this->datenow;
			$res = $this->m_master->insertdata($this->table,$put);
			if ($res > 0){
				$this->session->set_flashdata('success','Tambah produk berhasil.');
				redirect('bo/'.$this->class);
			}
		}
	}
	function delete(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');
		if (empty($priv)){
			$val = $this->input->post('value');
			$res = $this->m_master->deletedata($this->table,array('id_product'=>$val));
			if ($res){
				echo json_encode(array('error'=>0,'msg'=>'Hapus product ID '.$val.' berhasil'));
			}
		}else{
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
	}
	function export($to){
		if ($to == 'csv'){
			$field = array('product_code');
		}else{
			$field = array('product_code','supplier','date_add','add_by');
		}
		$sql = $this->m_master->export_product($field);
		$filename = 'main_assets('.date('d-m-Y',strtotime($this->datenow)).')';
		$title = "MAIN ASSETS";
		$column_header = array(
				'no' => 'No',
				'product_code' => 'Item Code',					
				'supplier' => 'Supplier',
				'date_add'	=> 'Date Add',
				'add_by' => 'Add By'		
		);
		$this->m_master->generate_export($to,$filename,$sql,$title,$column_header);
	}
	function import(){
		/*
		 * importing file txt || csv
		*/
		$file = $_FILES['filemaster']['tmp_name'];
		$expl = explode('.',$_FILES['filemaster']['name']);
		$end_param = end($expl);
		if ($end_param == 'TXT' or $end_param == 'csv' or $end_param == 'txt' or $end_param == 'CSV'){
			if ($file) {
				$handle = fopen($file,"r");          //  Open the file and read
				$no=0;
				while($fileimport = fgetcsv($handle, 10000, ",")) {//To get Array from CSV, " "(delimiter)
					$columnCount = count($fileimport);
					if ($columnCount == 2){												
						$input['product_code'] = $fileimport[0];								
						$input['supplier'] = $fileimport[1];				
						$input['date_add'] = $this->datenow;
						$input['add_by'] = $this->addby;
						$input['date_update'] = $this->datenow;
						$input['add_update'] = $this->addby;
						$res = $this->m_master->insertdata($this->table,$input);
						($res) ?  $no++ : $no++;
					}else{
						$res = 0;
					}
				}
				if ($res > 0){
					$this->session->set_flashdata('success','Tambah stock take berhasil sebanyak '.$no.' Items');
					redirect('bo/'.$this->class);
				}else{
					$this->session->set_flashdata('danger','Import tidak berhasil, Jumlah colum file harus 2.');
					redirect('bo/'.$this->class);
				}
	
			}
		}else{
			$this->session->set_flashdata('danger','Tidak dapat import data, type file bukan TXT atau CSV.');
			redirect('bo/'.$this->class);
		}
	}
	function delete_all(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');
		if (empty($priv)){
			$res = $this->db->truncate($this->table);
			if ($res){
				$this->session->set_flashdata('success','Hapus seluruh data berhasil');
				redirect('bo/'.$this->class);
			}
		}else{
			$this->session->set_flashdata('danger',$priv['notif']);
			redirect('bo/'.$this->class);
		}
	}
}
