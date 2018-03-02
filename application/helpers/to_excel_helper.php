<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Excel library for Code Igniter applications
* Author: Derek Allard, Dark Horse Consulting, www.darkhorse.to, April 2006
*/

//default excel
function to_excel($query, $filename='exceloutput')
{
    $headers = ''; // just creating the var for field headers to append to below
    $data = ''; // just creating the var for field data to append to below

    $obj =& get_instance();

    // yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
    // $fields = $query->field_data();
    $fields = $query->list_fields();

    if ($query->num_rows() == 0) {
        echo '<p>The table appears to have no data.</p>';
    } else {
        foreach ($fields as $field) {
            // yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
            // $headers .= $field->name . "\t";
            $headers .= $field . "\t";
        }

        foreach ($query->result() as $row) {
            $line = '';
            foreach($row as $value) {
                if ((!isset($value)) OR ($value == "")) {
                    $value = "\t";
                } else {
                    $value = str_replace('"', '""', $value);
                    $value = '"' . $value . '"' . "\t";
                }
                $line .= $value;
            }
            $data .= trim($line)."\r\n";
        }

        $data = str_replace("\r","",$data);
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename.xls");
        echo "$headers\n$data"; //with header column
       //echo $data;//only row records
    }
}

//generate data siklus
function to_excel_move($query, $filename='exceloutput',$id)
{
	$headers = ''; // just creating the var for field headers to append to below
	$data = ''; // just creating the var for field data to append to below

	$obj =& get_instance();
	// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
	// $fields = $query->field_data();
	$fields = (!empty($id)) ? array(0=>'No',1=>'Item Code',2=>'Move',3=>'Date Add') : array(0=>'No',1=>'Item Code',2=>'Siklus');

	if ($query->num_rows() == 0) {
		echo '<p>The table appears to have no data.</p>';
	}else {
		foreach ($fields as $field) {
			// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
			// $headers .= $field->name . "\t";
			$headers .= $field . "\t";
		}
		$no=1;
		foreach ($query->result() as $row) {
			$line = '';
			$item_code = $row->product_code;
			$CI =& get_instance();
			$datax['no'] = $no++;
			$datax['product_code'] = $item_code;
			if (!empty($id)){
				$datax['move'] = $row->move;
				$datax['date_add'] = $row->date_add;
			}else{
				$datax['siklus'] = floor($CI->m_master->get_total_move(array('product_code'),array('product_code'=>$item_code,'move'=>'Out')));
			}					
			foreach($datax as $value) {				
				if ((!isset($value)) OR ($value == "")) {
					$value = "\t";
				} else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t";
				}
				$line .= $value;
			}			
			$data .= trim($line)."\r\n";
		}		
		
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename.xls");
		echo "$headers\n$data"; //with header column
		//echo $data;//only row records
	}
}

//custom field
function to_excel_custom($query, $filename='exceloutput',$column)
{
	$headers = ''; // just creating the var for field headers to append to below
	$data = ''; // just creating the var for field data to append to below

	$obj =& get_instance();

	// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
	// $fields = $query->field_data();
	//$fields = $query->list_fields();
	$fields = $column;
	if ($query->num_rows() == 0) {
		echo '<p>The table appears to have no data.</p>';
	}else {
		foreach ($fields as $field) {
			// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
			// $headers .= $field->name . "\t";
			$headers .= $field . "\t";
		}
		$no=1;
		foreach ($query->result() as $row) {
			$line = '';
			$num['no'] = $no++;
			$array = json_decode(json_encode($row),true);//mengconvert stdclasObject menjadi array
			$datax = array_merge($num,$array);//merger array
			foreach($datax as $value) {
				if ((!isset($value)) OR ($value == "")) {
					$value = "\t";
				}else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t";
				}
				$line .= $value;
			}
			$data .= trim($line)."\r\n";
		}

		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename.xls");
		echo "$headers\n$data"; //with header column
		//echo $data;//only row records
	}	
	
}
function to_excel_reject($query, $filename='exceloutput',$column)
{
	$headers = ''; // just creating the var for field headers to append to below
	$data = ''; // just creating the var for field data to append to below

	$obj =& get_instance();
	// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
	// $fields = $query->field_data();
	$fields = $column;

	if ($query->num_rows() == 0) {
		echo '<p>The table appears to have no data.</p>';
	}else {
		foreach ($fields as $field) {
			// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
			// $headers .= $field->name . "\t";
			$headers .= $field . "\t";
		}
		$no=1;
		foreach ($query->result() as $row) {
			$line = '';
			$item_code = $row->product_code;
			$CI =& get_instance();
			$datax['no'] = $no++;
			$datax['product_code'] = $item_code;
			$datax['date_produksi'] = $row->date_produksi;
			$datax['supplier'] = $row->supplier;
			$datax['date_add'] = $row->date_add;
			$datax['siklus'] = (string)floor($CI->m_master->get_total_move(array('product_code'),array('product_code'=>$item_code,'move'=>'Out')));
			$datax['move'] = (string)$row->move;
			foreach($datax as $value) {
				if ((!isset($value)) OR ($value == "")) {
					$value = "\t";
				} else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t";
				}
				$line .= $value;
			}
			$data .= trim($line)."\r\n";
		}

		$data = str_replace("\r","",$data);

		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename.xls");
		echo "$headers\n$data"; //with header column
		//echo $data;//only row records
	}
}
function to_excel_stock($query, $filename='exceloutput')
{
	$headers = ''; // just creating the var for field headers to append to below
	$data = ''; // just creating the var for field data to append to below

	$obj =& get_instance();
	// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
	// $fields = $query->field_data();
	$fields = array(0=>'No',1=>'Item Code',2=>'Move',3=>'Date Add',4=>'Siklus');

	if ($query->num_rows() == 0) {
		echo '<p>The table appears to have no data.</p>';
	}else {
		foreach ($fields as $field) {
			// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
			// $headers .= $field->name . "\t";
			$headers .= $field . "\t";
		}
		$no=1;
		foreach ($query->result() as $row) {
			$line = '';
			$item_code = $row->product_code;
			$CI =& get_instance();
			$datax['no'] = $no++;
			$datax['product_code'] = $item_code;
			$datax['move'] = (string)$row->move;
			$datax['date_add'] = long_date_time($row->date_add);
			$datax['siklus'] = (string)floor($CI->m_master->get_total_move(array('product_code'),array('product_code'=>$item_code,'move'=>'Out')));
			foreach($datax as $value) {
				if ((!isset($value)) OR ($value == "")) {
					$value = "\t";
				} else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t";
				}
				$line .= $value;
			}
			$data .= trim($line)."\r\n";
		}

		$data = str_replace("\r","",$data);

		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename.xls");
		echo "$headers\n$data"; //with header column
		//echo $data;//only row records
	}
}
function to_excel_stock_wh($query, $filename='exceloutput')
{
	$headers = ''; // just creating the var for field headers to append to below
	$data = ''; // just creating the var for field data to append to below

	$obj =& get_instance();
	// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
	// $fields = $query->field_data();
	$fields = array(0=>'No',1=>'Item Code',2=>'Supplier',3=>'Stock Available',4=>'Stock Out',5=>'Reject Stock',6=>'Remove Reject',7=>'Siklus');

	if ($query->num_rows() == 0) {
		echo '<p>The table appears to have no data.</p>';
	}else {
		foreach ($fields as $field) {
			// yang di-comment aslinya, diubah agar bekerja di CI 2.1.4
			// $headers .= $field->name . "\t";
			$headers .= $field . "\t";
		}
		$no=1;
		foreach ($query->result() as $row) {
			$line = '';
			$item_code = $row->product_code;
			$CI =& get_instance();
			$siklus = floor($CI->m_master->get_total_move(array('product_code'),array('product_code'=>$item_code,'move'=>'Out')));
			$max = $CI->m_master->get_stock_max($item_code);
			$datax['no'] = $no++;
			$datax['product_code'] = $item_code;
			$datax['supplier'] = $row->supplier;
			$datax['in'] = (string)$max['in'];
			$datax['out'] = (string)$max['out'];
			$datax['reject'] = (string)$max['reject'];
			$datax['remove'] = (string)$max['remove'];
			$datax['siklus'] = (string)$siklus;
			foreach($datax as $value) {
				if ((!isset($value)) OR ($value == "")) {
					$value = "\t";
				} else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t";
				}
				$line .= $value;
			}
			$data .= trim($line)."\r\n";
		}

		$data = str_replace("\r","",$data);

		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename.xls");
		echo "$headers\n$data"; //with header column
		//echo $data;//only row records
	}
}
?>