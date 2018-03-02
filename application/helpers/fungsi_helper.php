<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('replace_p')){
	function replace_p($data){
		$find = array('<p>','</p>');
		return str_replace($find,'',$data);
	}
}
function replace_freetext($text)
{
	$replace_address = str_ireplace(array("\r","\n",'\r','\n','\\',"<p>","</p>"),'', $text);
	return $replace_address;
}
function replace_desc($text)
{
	$replace_desc = str_ireplace(array("\r","\n",'\r','\n','\\'),'', $text);
	return $replace_desc;
}
function site_title()
{
	$find = array('<p>','</p>');
	return str_replace($find,'',$_SESSION['site_title']);
}
if (!function_exists('format_email')){
	function format_email($subject){
		if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $subject)){
			return false;
		}else{return true;}
	}
}
function hash_password($password)
{
	//buat password_hash
	$options = ['cost' => 10,
				'salt' => mcrypt_create_iv(33, MCRYPT_DEV_URANDOM),];
	$hash = password_hash($password, PASSWORD_BCRYPT, $options);
	return $hash;
}
function email_send($emailparam)
{	
	$ci =& get_instance();
	$ci->load->library('email');
	$subjek      =  $emailparam['subjek'];
	$email_from	 =  $emailparam['email_from'];
	$name_from	 =  $emailparam['name_from'];
	$email_to	 =  $emailparam['email_to'];	
	$email_bcc   =  $emailparam['email_bcc'];
	$content      = $emailparam['content'];
	//konfigurasi pengiriman
	$ci->email->from($email_from,$name_from);
	$ci->email->to($email_to);
	$ci->email->bcc($email_bcc);	
	$ci->email->subject($subjek);		
	$ci->email->message($content);	
	if ($ci->email->send()){		
		return true;
	}else{	
	show_error($ci->email->print_debugger());die;
	return false;}
		
}
function error_page()
{	
	$ci =& get_instance();
	$name = '404 Page not found';
	$data['title']		 = replace_p($name.$_SESSION['site_title']);
	$data['description'] = $name;
	$data['keywords']    = $name;
	$ci->load->view('bm/v_top_panel',$data);		
	$ci->load->view('bm/error/v_404');		
	$ci->load->view('bm/v_footer');
}

function request_server($data,$request)
{
	
	$ci =& get_instance();
	$server_url = $data['request_http'];
	$ci->xmlrpc->server($server_url, 80);
	$ci->xmlrpc->method($data['method']);	
	$ci->xmlrpc->request($request);
	if ( ! $ci->xmlrpc->send_request())
	{
		return $ci->xmlrpc->display_error();
	}
	else
	{			
		$response = $ci->xmlrpc->display_response();
		return $response;
			
	}
}
function xml_generate($data,$sql){
	$xml = new SimpleXMLElement($data['parent']);
	$no=0;
	foreach($sql as $item) {
		$sale = $xml->addChild($data['child'].$no++);
		foreach ($item as $key=>$val)
			$sale->addChild($key,htmlspecialchars($val));
	}
	$xml->asXML($data['path']);
	
}
function short_date($date){
	return date_format(date_create($date),'d/m/Y');
}
function short_date_time($date){
	return date_format(date_create($date),'d/m/Y H:i:s');
}
function long_date($date){
	
	return date_format(date_create($date), 'd M Y');
}
function long_date_time($date){

	return date_format(date_create($date), 'd M Y - H:i:s');
}
