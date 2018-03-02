<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Stockmove extends  CI_Controller{
	/*
	 * stock move adalah perjumlahan siklus jika barang kode yg sama keluar dan masuk dihitung 1 siklus
	 * jika barang hanya keluar saja dan belum masuk maka belum mendapatkan siklus
	 * jika ada kode barang yg sama dan pada tanggal yg sama maka siklus akan tetap terhitung
	 * jika ada kode yang sama harus dihapus distok double agar siklus sesuai data real
	 */
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'STMV';
		$this->table = 'tb_stock_take';
		
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	var $table;
	var $product_code;
	function index(){
		$this->m_master->get_login();	
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock_move' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		$data['page_title'] = "Stock Move";
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'product_code',//default order sort
				1 => 'product_code',
				2 => 'move',
				3 => 'date_add'						
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
		$total = count($this->m_master->get_stock_move());
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
		$query = $this->m_master->get_stock_move('',$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		yang mengandung keyword tertentu
		*/
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_stock_move());
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
		
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {	
			$where = array('product_code'=>$row->product_code,'move'=>'Out');		
			$siklus = floor($this->m_master->get_total_move(array('product_code'),$where));
			$sikluss = ($siklus < 1) ? '<span class="badge badge-warning">'.$siklus.'</span>' : '<span class="badge badge-info">'.$siklus.'</span>';
			//show in html
			$output['data'][]=array($nomor_urut,
					$row->product_code,								
					$sikluss,											
					'<a href="'.base_url('bo/'.$this->class.'/form/'.$row->product_code).'" title="'.$this->config->config['detail'].'" class="btn btn-warning btn-circle"><i class="icon-search"></i></a>'
																 
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function form($id){
		$this->m_master->get_login();	
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/stock/v_detail_stock_move' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		
		$data['page_title'] = "Stock Move Detail";
		$data['class'] = $this->class;
		$data['id'] = $id;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	
	function delete(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');
		if (empty($priv)){
			$val = $this->input->post('value');
			$res = $this->m_master->deletedata($this->table,array('id_stock_take'=>$val));
			if ($res){
				echo json_encode(array('error'=>0,'msg'=>'Hapus item Code '.$val.' berhasil'));
			}
		}else{
			echo json_encode(array('error'=>1,'msg'=>$priv['notif']));
		}
	}
	function get_records_detail($id){
		
		/*Mempersiapkan array tempat kita akan menampung semua data
		 yang nantinya akan server kirimkan ke client*/
		$output=array();
		/*data request dari client*/
		$request = $this->m_master->request_datatable();
	
		/*Token yang dikrimkan client, akan dikirim balik ke client*/
		$output['draw'] = $request['draw'];
		$where =array('product_code'=>$id);	
		/*
		 $output['recordsTotal'] adalah total data sebelum difilter
		$output['recordsFiltered'] adalah total data ketika difilter
		Biasanya kedua duanya bernilai sama pada saat load default(Tanpa filter), maka kita assignment
		keduaduanya dengan nilai dari $total
		*/
		/*Menghitung total desa didalam database*/
		$total = count($this->m_master->get_table_filter($this->table,$where));
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
		$this->db->order_by('date_add','desc');
		$query = $this->m_master->get_table_filter($this->table,$where,$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	
	
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_table_filter($this->table,$where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
			$total = count($this->m_master->get_table_filter($this->table,$where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$request['date_to']);
			$total = count($this->m_master->get_table_filter($this->table,$where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
	
	
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			//show in html
			$output['data'][]=array($nomor_urut,
					$row->product_code,				
					$row->move,					
					$row->date_add,					
					'<button title="'.$this->config->config['delete'].'" type="button" id="delete" class="btn btn-danger btn-circle" onclick="ajaxDelete(\''.base_url('bo/'.$this->class.'/delete').'\',\''.$row->id_stock_take.'\',\'tes\')"><i class="icon-trash"></i></button>'
									
									 
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	}
	function export($to,$id=""){						
		if (!empty($id)){
			$filename = 'stock_move_detail('.date('d-m-Y',strtotime($this->datenow)).')';
			$title = "STOCK MOVE DETAIL";
			$column_header = array(
					'no' => 'No',
					'product_code' => 'Item Code',
					'move'=>'Move',
					'date_add'=>'Date Add'
			);
			$this->db->where('product_code',$id);
			$this->db->order_by('date_add','desc');
		}else{
			$this->db->order_by('product_code','asc');
			$this->db->group_by('product_code');
			$filename = 'stock_move('.date('d-m-Y',strtotime($this->datenow)).')';
			$title = "STOCK MOVE";
			$column_header = array(
					'no' => 'No',
					'product_code' => 'Item Code',
					'siklus'=>'Siklus'
			);
		}		
		$sql = $this->db->select('*')->from($this->table)->get();
		$this->m_master->generate_export_move($to,$filename,$sql,$title,$column_header,$id);
	}
	
}
