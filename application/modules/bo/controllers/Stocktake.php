<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class Stocktake extends  CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->datenow = $_SESSION['date_now'];
		$this->addby = $this->session->userdata('nama_depan');
		$this->class = strtolower(__CLASS__);
		$this->acces_code = 'STKE';
		$this->table = 'tb_stock_take';
		
	}
	var $datenow;
	var $addby;
	var $class;
	var $acces_code;
	var $table;
	function index(){
		$this->m_master->get_login();	
		$priv = $this->m_master->get_priv($this->acces_code,'view');
		$main_page = (empty($priv)) ? 'bo/stock/v_index_stock_take' : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];
		
		$data['page_title'] = "Stock Take";
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
			
		$total = count($this->m_master->get_table_filter($this->table));
		$output['recordsTotal']= $output['recordsFiltered'] = $total;		
		$output['data'] = array();
	
		/*
		 * jika keyword tidak kosong, maka menjalankan fungsi search
		* untuk ditampilkan di datable
		* */
		if($request['keyword'] !=""){
			/*menjalankan fungsi filter or_like*/
			$this->m_master->search_like($request['keyword'],$this->column());
		}else if ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
		}else if ($request['date_from'] != "" && $request['date_to'] != ""){			
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
		}else if ($request['date_from'] != "" && $request['date_to'] == ""){
			$this->db->like('date_add',$request['date_from']);
			$total = count($this->m_master->get_table_filter($this->table));			
			$output['recordsFiltered'] = $total;
		}else if ($request['date_from'] != "" && $request['date_to'] != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$request['date_from']);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$request['date_to']);
			$total = count($this->m_master->get_table_filter($this->table));
			/*total record yg difilter*/
			$output['recordsFiltered'] = $total;
		}
		$nomor_urut=$request['start']+1;
		foreach ($query as $row) {
			//show in html
			$label = array('In'=>'success','Out'=>'default','Reject'=>'warning','Remove'=>'important');
			$move = '<span class="label label-'.$label[$row->move].'">'.$row->move.'</span>';
			$output['data'][]=array($nomor_urut,
					$row->product_code,				
					$move,					
					$row->date_add,		
					'<a title="'.$this->config->config['edit'].'" type="button" id="edit" class="btn btn-warning btn-circle" href="'.base_url('bo/'.$this->class.'/form/'.$row->id_stock_take).'"><i class="icon-edit"></i></a>
					<button title="'.$this->config->config['delete'].'" type="button" id="delete" class="btn btn-danger btn-circle" onclick="ajaxDelete(\''.base_url('bo/'.$this->class.'/delete').'\',\''.$row->id_stock_take.'\',\'tes\')"><i class="icon-trash"></i></button>'								 
			);
			$nomor_urut++;
		}
		echo json_encode($output);
	
	}
	function form($id=''){
		$this->m_master->get_login();
		$view = 'bo/stock/v_crud_stock_take';
		if ($id !=''){
			//akses edit
			$action = 'edit';
			$data['page_title'] = "Ubah Stock Take";
			$sql = $this->m_master->get_table_filter($this->table,array('id_stock_take'=>$id));
			foreach ($sql as $row)
				foreach ($row as $key=>$val){
				$data[$key] = $val;
			}
		}else{
			//akses tambah
			$action = 'add';
			$data['page_title'] = "Tambah Stock Take";
		}
		$priv = $this->m_master->get_priv($this->acces_code,$action);
		$main_page = (empty($priv)) ? $view : 'bo/'.$priv['error'];
		$data['notif'] = (empty($priv)) ? '' : $priv['notif'];		
		$data['class'] = $this->class;			
		$this->load->view('bo/v_header',$data);
		$this->load->view($main_page);
		$this->load->view('bo/v_footer');
	}
	function import(){
		/*
		 * importing file txt || csv
		 */
		$file = $_FILES['stocktake']['tmp_name'];
		$expl = explode('.',$_FILES['stocktake']['name']);
		$end_param = end($expl);
		if ($end_param == 'TXT' or $end_param == 'csv'){
			if ($file) {
		    $handle = fopen($file,"r");          //  Open the file and read     
		    $no=0;          
		    while($fileimport = fgetcsv($handle, 10000, ";")) {//To get Array from CSV, " "(delimiter) 
		    	$columnCount = count($fileimport);
		    	
		    	if ($columnCount == 3){
		    		$input['product_code'] = $fileimport[0];
		    		$input['move'] = $fileimport[1];
		    		$input['date_add'] = date('Y-m-d H:i:s',strtotime($fileimport[2]));
		    		$res = $this->m_master->insertdata('tb_stock_take',$input);
		    		($res) ?  $no++ : $no++;
		    	}else{
		    		$res = 0;
		    	}                  		        
		        
			   }
			   if ($res > 0){
			    	$this->session->set_flashdata('success','Tambah stock take berhasil sebanyak '.$no.' Items');
			    	redirect('bo/'.$this->class);
			    }else{
			    	$this->session->set_flashdata('danger','Import tidak berhasil, format data file tidak sesuai.');
			    	redirect('bo/'.$this->class);
			    }
			   
			}  
		}else{
			$this->session->set_flashdata('danger','Tidak dapat import data, type file bukan TXT atau CSV.');
			redirect('bo/'.$this->class);
		}
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
	function export($to){
		$this->db->order_by('product_code','asc');
		$this->db->order_by('date_add','asc');//tanngal paling awal
		$sql = $this->db->select('product_code,move,date_add')->from($this->table)->get();			
		$filename = 'stock_take('.date('d-m-Y',strtotime($this->datenow)).')';
		$title = "STOCK TAKE";
		$column_header = array(
				'no' => 'No',
				'product_code' => 'Item Code',
				'move'=>'Move',
				'date_add'=>'Date Add'
		);
		$this->m_master->generate_export($to,$filename,$sql,$title,$column_header);
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
	function proses(){
		$post = $this->input->post();
		$id= $post['id_stock_take'];//for update		
		$put['move'] = $post['move'];		
		if ($id != ''){
			//edit proses
			$res = $this->m_master->updatedata($this->table,$put,array('id_stock_take'=>$id));
			if ($res > 0){
				$this->session->set_flashdata('success','Ubah stock take berhasil.');
				redirect('bo/'.$this->class);
			}
		}else{
			//input proses
			$put['product_code'] = $post['itemnumber'];			
			$put['date_add'] = $this->datenow;
			$res = $this->m_master->insertdata($this->table,$put);
			if ($res > 0){
				$this->session->set_flashdata('success','Tambah stock take berhasil.');
				redirect('bo/'.$this->class);
			}
		}
	}
}
