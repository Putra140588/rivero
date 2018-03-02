<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Stockdouble extends CI_Controller{
	/*
	 * stock double menampilkan kode barang yang sama dengan tanggal yang sama
	 * stock double harus dihapus agar siklus sesuai data real
	 */
	function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'STDB';
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
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock_double' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		$data['page_title'] = "Stock Double";
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
				3 => 'date_add',
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
		$where = array('');
		foreach ($this->m_master->get_stock_double() as $row=>$key){
			$where[]= (!empty($key->product_code)) ? $key->product_code : '';
		}		
		
		/*
		 $output['recordsTotal'] adalah total data sebelum difilter
		$output['recordsFiltered'] adalah total data ketika difilter
		Biasanya kedua duanya bernilai sama pada saat load default(Tanpa filter), maka kita assignment
		keduaduanya dengan nilai dari $total
		*/
		
		$total = count($this->m_master->get_table_stock_double($this->table,$where));
		
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
		$query = $this->m_master->get_table_stock_double($this->table,$where,$this->column()[$request['column']],$request['sorting'],$request['length'],$request['start']);
		
	
		/*Ketika dalam mode pencarian, berarti kita harus
		 'recordsTotal' dan 'recordsFiltered' sesuai dengan jumlah baris
		yang mengandung keyword tertentu
		*/
		if($request['keyword'] !=""){
			$this->m_master->search_like($request['keyword'],$this->column());
			$total = count($this->m_master->get_table_stock_double($this->table,$where));
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
	function delete_all(){
		$priv = $this->m_master->get_priv($this->acces_code,'delete');
		if (empty($priv)){
			/*
			 * mendapatkan list item yang sama lebih dari 1 pada tanggal yang sama
			 */
			$where = array('');
			foreach ($this->m_master->get_stock_double() as $row=>$key){
				$where[]= $key->product_code;					
			}
			$res = $this->m_master->delete_all_double($this->table,$where);
			if ($res){
				$this->session->set_flashdata('success','Hapus seluruh data double berhasil');
				redirect('bo/'.$this->class);
			}
		}else{
			$this->session->set_flashdata('danger',$priv['notif']);
			redirect('bo/'.$this->class);
		}
	}
	
}
