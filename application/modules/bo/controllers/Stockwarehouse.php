<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Stockwarehouse extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'STWH';
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
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock_warehouse' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		$data['page_title'] = "Stock Warehouse";
		$data['class'] = $this->class;
		$in = $this->m_master->sum_stock(array('In'));		
			
		$reject = $this->m_master->sum_stock(array('Reject'));
		
		$out =  $this->m_master->sum_stock(array('Out'));
		
		$remove = $this->m_master->sum_stock(array('Remove'));
		
		$data['available'] = $in;
		$data['reject'] = $reject;
		$data['total'] = count($this->m_master->get_table_filter($this->table,array('deleted'=>1)));
		$data['out'] = $out;
		$data['remove'] = $remove;
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
				3 => 'id_product',
				4 => 'id_product',
				5 => 'id_product',
				6 => 'id_product',
				7 => 'id_product',				
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
		}
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			//pembulatan kebawah
			$siklus = floor($this->m_master->get_total_move(array('product_code'),array('product_code'=>$row->product_code,'move'=>'Out')));				
			$max = $this->m_master->get_stock_max($row->product_code);
			$out = $this->label($max['out']);
			$in  = $this->label($max['in']);
			$reject = $this->label($max['reject']);
			$remove = $this->label($max['remove']);
			$sikluss = $this->label($siklus);
			$output['data'][]=array($nomor_urut,
					$row->product_code,					
					$row->supplier,					
					$in,
					$out,
					$reject,
					$remove,
					$sikluss,					
					'<a href="'.base_url('bo/stockmove/form/'.$row->product_code).'" title="'.$this->config->config['detail'].'" class="btn btn-warning btn-circle"><i class="icon-search"></i></a>'
					
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function label($stock){
		if ($stock > 0){
			return '<span class="badge badge-info">'.$stock.'</span>';
		}else {
			return '<span class="badge badge-important">'.$stock.'</span>';
		}
	}
	function export($to){
		$this->db->order_by('product_code','asc');		
		$sql = $this->db->select('product_code,supplier')->from($this->table)->get();				
		$filename = 'stock_warehouse('.date('d-m-Y',strtotime($this->datenow)).')';
		$title = "STOCK WAREHOUSE";
		$column_header = array(
				'no' => 'No',
				'product_code' => 'Item Code',
				'supplier'=>'Supplier',
				'in'=>'Stock Available',
				'out'=>'Stock Out',
				'reject'=>'Reject Stock',
				'remove'=>'Remove Reject',
				'siklus'=>'Siklus',				
		);
		$this->m_master->generate_export_stock_wh($to,$filename,$sql,$title,$column_header);
	}
}
