<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Stockreject extends  CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'STRJ';		
		
		
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	function index(){
		$this->m_master->get_login();
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock_reject' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		$data['page_title'] = "Stock Reject";
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'A.product_code',//default order sort
				1 => 'A.product_code',				
				2 => 'A.supplier',
				3 => 'B.date_add',
				4 => 'A.product_code',
				5 => 'B.move',				
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
		$where_in = array('Reject','Remove');		
		$total = count($this->m_master->get_stock_reject($where_in));
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
		}else if ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('B.date_add',$request['date_from']);
		}else if ($request['date_from'] != "" && $request['date_to'] != ""){			
			$this->db->where('DATE_FORMAT(B.date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(B.date_add,"%Y-%m-%d") <=',$request['date_to']);
		}
		/*Pencarian ke database*/
		$query = $this->m_master->get_stock_reject($where_in,$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		yang mengandung keyword tertentu
		*/
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_stock_reject($where_in));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}else if ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('B.date_add',$request['date_from']);
			$total = count($this->m_master->get_stock_reject($where_in));			
			$output['recordsFiltered'] = $total;
		}else if ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(B.date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(B.date_add,"%Y-%m-%d") <=',$request['date_to']);
			$total = count($this->m_master->get_stock_reject($where_in));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
	
	
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			//show in html
			$siklus = $this->m_master->get_total_move(array('product_code'),array('product_code'=>$row->product_code,'move'=>'Out'));
			$sikluss = ($siklus < 1) ? '<span class="badge badge-warning">'.$siklus.'</span>' : '<span class="badge badge-info">'.$siklus.'</span>';
			$move = ($row->move == 'Reject') ? '<span class="label label-warning">'.$row->move.'</span>' : '<span class="label label-important">'.$row->move.'</span>';
			$output['data'][]=array($nomor_urut,
					$row->product_code,					
					$row->supplier,
					$row->date_add,
					$sikluss,
					$move,					
	
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function export($to){		
		$sql = $this->m_master->export_reject();
		$filename = 'stock_reject('.date('d-m-Y',strtotime($this->datenow)).')';
		$title = "STOCK REJECT";
		$column_header = array(
				'no' => 'No',
				'product_code' => 'Item Code',				
				'supplier' =>'supplier',
				'date_add'=>'Scan Date',
				'siklus'=>'Siklus',
				'move'=>'Status'
		);
		$this->m_master->generate_export_reject($to,$filename,$sql,$title,$column_header);
	}
}