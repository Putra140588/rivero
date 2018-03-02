<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');
class M_master extends CI_Model{
	
	function insertdata($table,$post){
		$res = $this->db->insert($table,$post);
		return $res;
	}	
	function updatedata($table,$data,$where){
		$res = $this->db->update($table,$data,$where);
		return $res;
	}
	function deletedata($table,$where){
		$res = $this->db->delete($table,$where);
		return $res;
	}
	function cek_login($post){
		$sql = $this->db->select('*')
						->from	('tb_user')						
						->where	($post)
						->where	('deleted',1)
						->where	('active',1)
						->get()->result();
		return $sql;
	}
	function get_login(){
		if ($this->session->userdata('login_admin') == false){
			redirect('bo/mplogin');
		}
	}
	function get_akses_modul($where){		
		$kd_group = $this->session->userdata('kd_group');
		$sql = $this->db->select('A.*,B.*')
					->from  ('tb_akses as A')
					->join	('tb_modul as B','A.id_modul = B.id_modul','left')
					->where	('A.kd_group',$kd_group)
					->where	('A.active',1)
					->where ($where)
					//->where	('B.id_modul_parent',0)
					//->where	('B.level',0)
					->order_by('B.sort','asc')
					->get();
		return $sql;
	}
	function get_table($table){
		return $this->db->get($table);
	}
	function get_table_column($column,$table,$where=''){
			$this->db->select($column);
			$this->db->from($table);
			($where != '') ? $this->db->where($where) : '';
		return $this->db->get()->result();
	}
	function get_table_filter($table,$where='',$column='',$sort='',$length='',$start=''){		
		$this->db->select('*');
		$this->db->from($table);
		(!empty($where)) ? $this->db->where($where) : '';
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		if ($table == 'tb_stock_take'){
			//only sort tb_stock_take					
			$this->db->order_by('date_add','asc');
		}
		$sql = $this->db->get()->result();		
		return $sql;
	}
	function get_akses_all(){
		//tidak menampilkan superadmin
		return $this->db->where('kd_group <> ','SA')->get('tb_group');
	}	
	function get_karyawan($where='',$column='',$sort='',$length='',$start=''){
			 $this->db->select('A.*,B.nama_jabatan,C.nama_bagian,D.nama_group');
			 $this->db->from	('tb_karyawan as A');
			 $this->db->join	('tb_jabatan as B','A.id_jabatan = B.id_jabatan','left');
			 $this->db->join	('tb_bagian as C','A.id_bagian = C.id_bagian','left');	
			 $this->db->join	('tb_group D','A.kd_group = D.kd_group','left');
			 $this->db->where	('A.deleted',1);
			 /*where digunakan untuk get by field*/
			 ($where !='') ? $this->db->where($where) : '';			 
			 $this->db->limit($length,$start);
			 $this->db->order_by($column,$sort);			
			 $sql =  $this->db->get()->result();
		return $sql;
	}
	/*
	 * mengakftifkan mungsi search by keyword
	 */
	function search_like($keyword,$column){		
		//melakukan pengulangan column
		$this->db->group_start();
		foreach ($column as $val=>$row){
			$this->db->or_like($row,$keyword);
		}
		$this->db->group_end();
	}
	function search_like_($keyword,$column){
		//melakukan pengulangan column		
		foreach ($column as $val=>$row){
			$this->db->like($row,$keyword);
		}
		
	}
	/*Menangkap semua data yang dikirimkan oleh client*/
	function request_datatable(){
		/*Offset yang akan digunakan untuk memberitahu database
		 dari baris mana data yang harus ditampilkan untuk masing masing page
		 */
		$start = $_REQUEST['start'];	
		/*Keyword yang diketikan oleh user pada field pencarian*/
		$keyword = $_REQUEST['search']["value"];
		/*Sebagai token yang yang dikrimkan oleh client, dan nantinya akan
		 server kirimkan balik. Gunanya untuk memastikan bahwa user mengklik paging
		 sesuai dengan urutan yang sebenarnya */
		$draw = $_REQUEST['draw'];	
		/*asc/desc yg direquest dari client*/
		$sorting = $_REQUEST['order'][0]['dir'];	
		/*index column yg direquest dari client*/
		$column = $_REQUEST['order'][0]['column'];	
		/*Jumlah baris yang akan ditampilkan pada setiap page*/
		$length = $_REQUEST['length'];
		
		//tanggal from index 0 get by id
		$date_from = $_REQUEST['columns'][0]['search']['value'];
		//tanggal to index 1 get by id
		$date_to = $_REQUEST['columns'][1]['search']['value'];
		$output = array('start'=>$start,'keyword'=>$keyword,
					   'draw'=>$draw,'sorting'=>$sorting,
					   'column'=>$column,'length'=>$length,
					   'date_from'=>$date_from,'date_to'=>$date_to
				
		);
		return $output;
	}
	
	
	
	
	function get_moduls($where='',$column='',$sort='',$length='',$start=''){
		$this->db->select('A.id_modul,A.nama_modul,A.level,A.akses_code,B.nama_modul as nama_parent');
		$this->db->from	('tb_modul as A');
		$this->db->join	('tb_modul as B','A.id_modul = B.id_modul_parent','left');
		/*where digunakan untuk get by field*/
		($where !='') ? $this->db->where($where) : '';
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		$sql =  $this->db->get()->result();
		return $sql;		
	}
	
	
	function get_stock_move($where='',$column='',$sort='',$length='',$start=''){
		$this->db->select('*');
		$this->db->from	('tb_stock_take');		
		/*where digunakan untuk get by field*/
		($where !='') ? $this->db->where($where) : '';		
		$this->db->group_by('product_code');		
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		$sql =  $this->db->get()->result();
		return $sql;
		
	}
	function export_product($field){
		$this->db->select  ($field);
		$this->db->from    ('tb_product');
		$this->db->order_by('product_code','asc');
		$sql =  $this->db->get();
		return $sql;
	}
	
	function generate_export($to,$filename,$sql,$title,$column){
		if ($to == 'csv'){
			$this->load->dbutil(); // call db utility library
			$this->load->helper('download'); // call download helper
			$file_name = $filename.'.csv';
			$delimiter = ";";
			$newline = "\r\n";//baris baru
			$enclosure = '';//tanda kutip
			//remove firts line (headername)
			$convert = ltrim(strstr($this->dbutil->csv_from_result($sql,$delimiter,$newline,$enclosure), $newline));
			force_download($file_name, $convert);
		}else if ($to == 'excel'){
			$this->load->helper('to_excel');
			$file_name = $filename;
			to_excel_custom($sql,$file_name,$column);
		}else if ($to == 'pdf'){
			error_reporting(1);
			$parameters = array (
					'paper'=>'A4',
					'orientation'=>'portrait',
					'type'=>'',
					'options'=>'',
			);
			$this->load->library('Pdf', $parameters);
			//path font set
			$this->pdf->selectFont(APPPATH.'/third_party/pdf-php/fonts/FreeSerif.afm');
			$this->pdf->ezImage(base_url('assets/bo/images/logo/logo.png'), 0, 100, 'none', 'left');
			$this->pdf->ezText($title, 14, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-10);
			$this->pdf->ezText(short_date($this->datenow), 12, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-15);
			$no = 1;
			foreach ($sql->result_array() as $key=>$value){
				$data[$key] = $value;
				$data[$key]['no'] = $no++;
	
			}
			$this->pdf->ezTable($data, $column);
			$file_name = $filename.'.pdf';
			$this->pdf->ezStream(array('Content-Disposition'=>$file_name));
		}else{
			echo 'Error export';
			return false;
		}
	}
	function generate_export_move($to,$filename,$sql,$title,$column){
		if ($to == 'csv'){
			$this->load->dbutil(); // call db utility library
			$this->load->helper('download'); // call download helper
			$file_name = $filename.'.csv';
			$delimiter = ";";
			$newline = "\r\n";//baris baru
			$enclosure = '';//tanda kutip
			//remove firts line (headername)
			$convert = $this->dbutil->csv_from_result_move($sql,$delimiter,$newline,$enclosure);
			force_download($file_name, $convert);
		}else if ($to == 'excel'){
			$this->load->helper('to_excel');
			$file_name = $filename;
			to_excel_move($sql,$file_name);
		}else if ($to == 'pdf'){
			error_reporting(1);
			$parameters = array (
					'paper'=>'A4',
					'orientation'=>'portrait',
					'type'=>'',
					'options'=>'',
			);
			$this->load->library('Pdf', $parameters);
			//path font set
			$this->pdf->selectFont(APPPATH.'/third_party/pdf-php/fonts/FreeSerif.afm');
			$this->pdf->ezImage(base_url('assets/bo/images/logo/logo.png'), 0, 100, 'none', 'left');
			$this->pdf->ezText($title, 14, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-10);
			$this->pdf->ezText(short_date($this->datenow), 12, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-15);
			$no = 1;
			foreach ($sql->result_array() as $key=>$value){					
				$data[$key] = $value;//product code			
				$data[$key]['no'] = $no++;
				$data[$key]['siklus'] = $this->m_master->get_total_move(array('product_code'),array('product_code'=>$value['product_code'],'move'=>'Out'));			
			}			
			$this->pdf->ezTable($data, $column);
			$file_name = $filename.'.pdf';
			$this->pdf->ezStream(array('Content-Disposition'=>$file_name));
		}else{
			echo 'Error export';
			return false;
		}
	}
	function generate_export_reject($to,$filename,$sql,$title,$column){
		if ($to == 'excel'){
			$this->load->helper('to_excel');
			$file_name = $filename;
			to_excel_reject($sql,$file_name,$column);
		}else if ($to == 'pdf'){
			error_reporting(1);
			$parameters = array (
					'paper'=>'A4',
					'orientation'=>'portrait',
					'type'=>'',
					'options'=>'',
			);
			$this->load->library('Pdf', $parameters);
			//path font set
			$this->pdf->selectFont(APPPATH.'/third_party/pdf-php/fonts/FreeSerif.afm');
			$this->pdf->ezImage(base_url('assets/bo/images/logo/logo.png'), 0, 100, 'none', 'left');
			$this->pdf->ezText($title, 14, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-10);
			$this->pdf->ezText(short_date($this->datenow), 12, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-15);
			$no = 1;
			foreach ($sql->result_array() as $key=>$value){
				$data[$key] = $value;
				$data[$key]['no'] = $no++;
				$data[$key]['siklus'] = $this->m_master->get_total_move(array('product_code'),array('product_code'=>$value['product_code'],'move'=>'Out'));			
			}		
			$this->pdf->ezTable($data, $column);
			$file_name = $filename.'.pdf';
			$this->pdf->ezStream(array('Content-Disposition'=>$file_name));
		}else{
			echo 'Error export';
			return false;
		}
	}
	function generate_export_stock($to,$filename,$sql,$title,$column){		
		if ($to == 'csv'){
			$this->load->dbutil(); // call db utility library			
			$file_name = $filename.'.csv';			
			$delimiter = ";";
			$newline = "\r\n";//baris baru
			$enclosure = '';//tanda kutip
			//remove firts line (headername)
			$convert = $this->dbutil->csv_from_result_stock($sql,$delimiter,$newline,$enclosure);
			force_download($file_name, $convert);
		}else if ($to == 'excel'){
			$this->load->helper('to_excel');
			$file_name = $filename;
			to_excel_stock($sql,$file_name);
		}else if ($to == 'pdf'){
			error_reporting(1);
			$parameters = array (
					'paper'=>'A4',
					'orientation'=>'portrait',
					'type'=>'',
					'options'=>'',
			);
			$this->load->library('Pdf', $parameters);
			//path font set
			$this->pdf->selectFont(APPPATH.'/third_party/pdf-php/fonts/FreeSerif.afm');
			$this->pdf->ezImage(base_url('assets/bo/images/logo/logo.png'), 0, 100, 'none', 'left');
			$this->pdf->ezText($title, 14, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-10);
			$this->pdf->ezText(short_date($this->datenow), 12, array('justification'=> 'centre'));
			$this->pdf->ezSetDy(-15);
			$no = 1;
			foreach ($sql->result_array() as $key=>$value){
				$data[$key] = $value;//product code
				$data[$key]['no'] = $no++;
				$data[$key]['siklus'] = $this->m_master->get_total_move(array('product_code'),array('product_code'=>$value['product_code'],'move'=>'Out'));
			}
			
			$this->pdf->ezTable($data, $column);
			$file_name = $filename.'.pdf';
			$this->pdf->ezStream(array('Content-Disposition'=>$file_name));
		}else{
			echo 'Error export';
			return false;
		}
	}
	function get_stock_double(){
		/*
		 * mendapatkan list item yang sama lebih dari 1 pada tanggal yang sama
		 */
		$this->db->select('product_code,COUNT(*) as total');
		$this->db->from ('tb_stock_take');		
		$this->db->group_by('YEAR(date_add),MONTH(date_add),DAY(date_add)');
		$this->db->group_by ('product_code,move');
		$this->db->having('count(total) > 1');			
		return $this->db->get()->result();		

	}
	/*
	 * menammpilkan item yang double dari tanggal yang paling terakhir
	 */
	function get_table_stock_double($table,$where=array(),$column='',$sort='',$length='',$start=''){
		/*
		 * mendapatkan id_stock_take yang lebih kecil/min
		 * dengan product code yang hanya double pada tanggal yang sama
		 */		
		$id = array(0);
		$sq = $this->db->DISTINCT()
					  ->select('min(id_stock_take) as id_stock_take')
					  ->from($table)
					  ->where_in('product_code',$where)
					  ->group_by ('product_code,move,YEAR(date_add),MONTH(date_add),DAY(date_add)')					  
					  ->get()->result();
		foreach ($sq as $i){
			$id[] = $i->id_stock_take;	
			
		}
		//print_r($id);die;
		/*
		 * menampilkan item yang hanya double dan tidak menampilkan id_stock_take yang lebih kecil
		 */
		$this->db->select('id_stock_take,product_code,move,date_add');
		$this->db->from($table);
		$this->db->where_in('product_code',$where);
		$this->db->where_not_in('id_stock_take',$id);//tidak menampilkan id_stock_take yang terbilang		
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		$sql = $this->db->get()->result();				
		return $sql;
	}
	function delete_all_double($table,$where=array()){
		/*
		 * mendapatkan id_stock_take yang lebih kecil/minimal
		* dengan product code yang hanya double pada tanggal yang sama
		*/
		$sq = $this->db->DISTINCT()
					  ->select('min(id_stock_take) as id_stock_take')
					  ->from($table)
					  ->where_in('product_code',$where)
					  ->group_by ('product_code,move,YEAR(date_add),MONTH(date_add),DAY(date_add)')					  
					  ->get()->result();
		foreach ($sq as $i){
			$id[] = $i->id_stock_take;
				
		}
		$this->db->where_in('product_code',$where);
		$this->db->where_not_in('id_stock_take',$id);//tidak menghapus id_stock_take yang terbilang
		$res = $this->db->delete($table);
		return $res;
	}
	function get_priv($ac,$action){
		$notif='';
		$alias_array  = array('view'=>'Menampilkan halaman','add'=>'Tambah baru',
				'edit'=>'Ubah data','delete'=>'Hapus data','active'=>'Akses modul');
		$kd_group = $this->session->userdata('kd_group');
		$this->db->select ('A.active,A.add,A.edit,A.delete,A.view,B.nama_modul,B.kd_modul');
		$this->db->from   ('tb_akses as A');
		$this->db->join	  ('tb_modul as B','A.id_modul = B.id_modul','left');
		$this->db->where  ('A.kd_group',$kd_group);
		$this->db->where  ('B.kd_modul',$ac);
		$sql = $this->db->get()->result();
		foreach ($sql as $row)
			if ($row->$action != 1){
			$data['notif'] = 'Anda tidak punya hak untuk '.$alias_array[$action].' '.$row->nama_modul;
			$data['error'] = 'v_access_denied';
			return $data;
		}
	}
	function get_modul_group($where){
		$sql = $this->db->select('A.nama_modul,A.link,A.kd_modul,
								  B.id_akses,B.kd_group,B.id_modul,B.active,B.add,B.edit,B.delete,B.view')
									  ->from	('tb_modul as A')
									  ->join	('tb_akses as B','A.id_modul = B.id_modul','left')
									  ->where	($where)
									  ->get();
		return $sql;
	}
	function get_user($where='',$column='',$sort='',$length='',$start=''){
		$this->db->select('A.*,B.nama_group');
		$this->db->from('tb_user as A');
		$this->db->join('tb_group as B','A.kd_group = B.kd_group','left');
		$this->db->where('A.deleted',1);
		/*where digunakan untuk get by field*/
		($where !='') ? $this->db->where($where) : '';
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		$sql =  $this->db->get()->result();
		return $sql;
	}
	
	function export_reject(){
		$this->db->select('A.product_code,A.date_produksi,A.supplier,B.move,B.date_add');
		$this->db->from('tb_product as A');
		$this->db->join('tb_stock_take as B','A.product_code = B.product_code','inner');
		$this->db->where('A.deleted',1);
		$this->db->where('B.move','Reject');		
		$this->db->order_by('B.date_add','asc');
		$sql =  $this->db->get();
		return $sql;
	}
	/*
	 * mendapatkan total move dan dikurang 1 karena pada saat scan pertama kali tidak dihitung siklus
	 */
	function get_total_move($field,$where){
		/*
		 * mendapatkan total siklus base on Move == Out
		 */
		//tidak menjumlahkan jika ada data out yang double pada tanggal yang sama
		$this->db->group_by('product_code,move,YEAR(date_add),MONTH(date_add),DAY(date_add)');			
		$total = count($this->m_master->get_table_column($field,'tb_stock_take',$where));
		$siklus = $total-1;
		/*
		 * jika skilus minus maka akan 0
		 * ditampilkan jika belum melakukan proses scan barang keluar
		 * jika baru 1 kali scan out, maka akan dihitung 0, jika scan > 1 maka akan dihitung siklus
		 */
		return ($siklus < 0) ? 0 : $siklus;
	}
	
	function sum_stock($where=""){		
		$id = $this->m_master->max_id();	
		$sql = $this->db->select('count(move) as total,product_code')
						->from('tb_stock_take')
						->where_in('id_stock_take',$id)
						->where($where)
						->order_by('date_add','desc')
						->get()->result();
		return $sql[0]->total;
	}
	function get_stock_max($code){
		/*
		 * mendapatkan status stock paling akhir
		 */
		$this->db->select('move');
		$this->db->from('tb_stock_take');
		$this->db->where('product_code',$code);			
		$this->db->order_by('date_add','desc');
		$this->db->limit(1);
		$sql = $this->db->get()->result();
		if (count($sql) > 0){
		foreach ($sql as $row){
			$data['out'] = 0;
			$data['in'] = 0;
			$data['reject'] = 0;
			$data['remove'] = 0;
			if ($row->move == 'In'){				
				$data['in'] = 1;				
			}elseif ($row->move == 'Out'){
				$data['out'] = 1;				
			}elseif ($row->move == 'Reject'){				
				$data['reject'] = 1;
			}elseif ($row->move == 'Remove'){				
				$data['remove'] = 1;
			}			
		  }
		}else{
				$data['out'] = 'N/A';
				$data['in'] = 'N/A';
				$data['reject'] = 'N/A';
				$data['remove'] = 'N/A';
		}
		return $data;
	}
	function max_id(){
		//mendapatkan id_stock_take paling maksimum sesuai item_code
		$this->db->select('max(id_stock_take) as id');
		$this->db->from('tb_stock_take');
		$this->db->group_by('product_code');
		$sql = $this->db->get()->result();
		$id = array(0);
		if (count($sql) > 1){
			foreach ($sql as $row){
				$id[] = $row->id;
			}
		}
		return $id;
	}
	function get_stock_reject($where='',$column='',$sort='',$length='',$start=''){
		$id = $this->m_master->max_id();
		$this->db->select('A.product_code,A.supplier,B.move,B.date_add');
		$this->db->from('tb_product as A');
		$this->db->join('tb_stock_take as B','A.product_code = B.product_code','inner');
		$this->db->where('A.deleted',1);
		$this->db->where_in('B.move',array('Reject','Remove'));
		$this->db->where_in('id_stock_take',$id);
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);
		$sql =  $this->db->get()->result();
		return $sql;	
	}
	function get_stock($where='',$columns=""){
		$request = $this->m_master->request_datatable();				
		$column = $columns[$request['column']];				
		$sort = $request['sorting'];		
		$length = $request['length'];
		$start = $request['start'];
		$keyword = $request['keyword'];
		$date_from = $request['date_from'];
		$date_to = $request['date_to'];
		
		//get max id_stock_take array
		$id = $this->m_master->max_id();			
		$this->db->select('*');
		$this->db->from('tb_stock_take');				
		($where !='') ? $this->db->where($where) : '';	
		$this->db->where_in('id_stock_take',$id);
		
		//jika melakukan filter keyword
		if ($keyword != "" && $columns != ""){			
			$this->m_master->search_like($keyword,$columns);
		}elseif ($date_from != "" && $date_to == ""){			
			$this->db->like('date_add',$date_from);
		}elseif ($date_from != "" && $date_to != ""){
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") >=',$date_from);
			$this->db->where('DATE_FORMAT(date_add,"%Y-%m-%d") <=',$date_to);
		}		
		$this->db->limit($length,$start);
		$this->db->order_by($column,$sort);			
		$sql =  $this->db->get()->result();
		return $sql;		
	}
	function get_stock_exp($where=""){
		//get max id_stock_take array
		$id = $this->m_master->max_id();
		$this->db->select('*');
		$this->db->from('tb_stock_take');
		($where !='') ? $this->db->where($where) : '';
		$this->db->where_in('id_stock_take',$id);
		$this->db->order_by('date_add','desc');
		$sql =  $this->db->get();
		return $sql;
	}
}
