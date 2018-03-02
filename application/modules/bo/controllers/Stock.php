<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Stock extends  CI_Controller{
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
		$this->acces_code = 'STWH';
		$this->table = 'tb_stock_take';
		
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	var $table;
	var $product_code;
	function index($code=""){	
		$this->m_master->get_login();	
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];		
		$data['code'] = $code;
		$data['page_title'] = $this->title($code)['title'];
		$data['filename'] = $this->title($code)['filename'];
		$data['class'] = $this->class;
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function column(){
		//indeks nilai array ke nama column table
		$column_array = array(
				0 => 'date_add',//default order sort
				1 => 'product_code',
				2 => 'move',	
				3 => 'date_add'					
		);
		return $column_array;
	}
	function get_records($code){
		/*Mempersiapkan array tempat kita akan menampung semua data
		 yang nantinya akan server kirimkan ke client*/
		$output=array();
		$request = $this->m_master->request_datatable();		
		/*Token yang dikrimkan client, akan dikirim balik ke client*/
		$output['draw'] = $_REQUEST['draw'];
		$where = array($code);				
		/*Menghitung total desa didalam database*/
		$total = count($this->m_master->get_stock($where));
		$output['recordsTotal']= $output['recordsFiltered'] = $total;	
		/*disini nantinya akan memuat data yang akan kita tampilkan
		 pada table client*/
		$output['data'] = array();	
		//jika melakukan filter keyword
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
		$query = $this->m_master->get_stock($where,$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
	 	if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_stock($where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
			$total = count($this->m_master->get_stock($where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}elseif ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$request['date_to']);
			$total = count($this->m_master->get_stock($where));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
		$nomor_urut= $_REQUEST['start']+1;
		foreach ($query as $row) {			
			$siklus = floor($this->m_master->get_total_move(array('product_code'),array('product_code'=>$row->product_code,'move'=>'Out')));
			$sikluss = ($siklus < 1) ? '<span class="badge badge-warning">'.$siklus.'</span>' : '<span class="badge badge-info">'.$siklus.'</span>';
			//show in html
			$output['data'][]=array($nomor_urut,
					$row->product_code,		
					$row->move,
					long_date_time($row->date_add),						
					$sikluss								
																				 
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}	
	function export($to,$code){
		$where = array($code);
		$sql = $this->m_master->get_stock_exp($where);			
		$filename = $this->title($code)['filename'].'_'.date('d-m-Y-H-i-s');
		$title = $this->title($code)['title'];
		$column_header = array(
				'no' => 'No',
				'product_code' => 'Item Code',	
				'move'=>'Move',
				'date_add'=>'Date Add',		
				'siklus'=>'Siklus'		
		);
		$this->m_master->generate_export_stock($to,$filename,$sql,$title,$column_header);
	}
	function title($code){
		if ($code =='In'){
			$data['title'] = 'Data Stock Available';
			$data['filename'] = 'Stock_available';
		}elseif($code == 'Out'){
			$data['title'] = 'Data Stock Out';
			$data['filename'] = 'Stock_Out';
		}elseif ($code == 'Reject'){
			$data['title'] = 'Data Stock Reject';
			$data['filename'] = 'Stock_Reject';
		}elseif ($code == 'Remove'){
			$data['title'] = 'Data Stock Remove Reject';
			$data['filename'] = 'Stock_Remove_Reject';
		}
		return $data;
	}
}
