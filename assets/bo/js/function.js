/**
 * 
 */

function loaddatatable(){	
	//dilakukan hanya sekali Load, untuk mengirim dan mengembalikan data menggunakan draw
	var dataTable = $(".ss-tables").DataTable({
		"columnDefs" : [{
			"targets": 'no-sort',
	          "orderable": false,//desabled column sort
		}],
		"oLanguage" : {
			"sSearch": "Filter Data",
			"oPaginate" : {
				"sPrevious" : "Sebelumnya",
				"sNext" : "Berikutnya",
			},				
			"sEmptyTable" : "Tidak ada data tersedia",
			"sInfo" : "(Halaman _PAGE_ - _PAGES_) Ditampilkan (_START_ s/d _END_) dari Total _TOTAL_ records ",
			"sInfoEmpty": "Ditampilkan (_END_ to _END_) dari Total _TOTAL_ records (Halaman _PAGE_ to _PAGES_)",	
			"sInfoFiltered": "(Filter dari total _MAX_ records)",
			"sLengthMenu": "Tampil _MENU_ data perhalaman",
			"sProcessing": "Prosess data...",
			"sLoadingRecords": "Mohon menunggu - loading...",
			"sZeroRecords": "Tidak ada data ditampilkan"
		},
		 "iDisplayLength": 10,
		"lengthMenu": [ 10, 25, 50, 75, 100,200,500,1000,1500,3000,5000,10000 ],
		"order": [[ 0, "desc" ]],//set default order sortir		
		ordering: true,//order sortir is true
		processing: true,
		serverSide: true,		
		ajax: {
		  url: document.getElementById("url-datatable").value,
		  type:'POST',
		}
	});
	
	// Date range filter	
	   $( ".date-filter" ).datepicker({	
		   dateFormat: "yy-mm-dd",
		   autoSize: false,
		   showOn: "button",		   
		   buttonImage: urlprotocol() + 'assets/bo/images/calendar.gif',
		   buttonImageOnly: false,	
		   buttonText: "Select date",			        
	   }).on('keyup click change', function () {
	    	var id = $(this).attr('id');//mengambil id ketika input text tanggal dimasukan
	    	var value = $(this).val();//mengambil value ketika input text tanggal dimasukan
	    	//mengirim dan mengembalikan data yang diinput dari client ke server side menggunakan draw
	    	dataTable.columns(id).search(value).draw();
	   });		
}
$(document).ready(function(){
	//menambahkan class selected jika table row diklik
	$('.ss-tables tbody').on( 'click', 'tr', function () {
	      $(this).toggleClass('selected');
	  } );	
	
	//autoload notif alert option
	 var x = $('.success').html();	
	 var y = $('.warning').html();
	 var z = $('.danger').html();
     if(x !=''){      	
     	 show_alert('.success','success',x);
     }
     if(y !=''){ 
      	
     	 show_alert('.warning','warning',y);
     }
     if(z !=''){       	
     	 show_alert('.danger','danger',z);
     }
     
     //auto load page
     $(window).load(function() {					
			$(".loader").fadeOut("slow");
		});
	
		
});
function modalShow(urlx,val,div){		
	$.ajax({
		url : urlx,
		type : 'post',
		data : 'val='+val,
		cache	: false,
		success	: function(param){
			$('#'+div).html(param);			
		},error : function(param){
			alert(param);
		}
	})
}



//format price direct input keyup
function formatPrice(num)
{
	   num = num.toString().replace(/\$|\,/g,'');
	   if(isNaN(num))
	   num = "0";
	   sign = (num == (num = Math.abs(num)));
	   num = Math.floor(num*100+0.50000000001);
	   cents = num%100;
	   num = Math.floor(num/100).toString();
	   if(cents<10)
	   cents = "0" + cents;
	   for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	   num = num.substring(0,num.length-(4*i+3))+','+ num.substring(num.length-(4*i+3));	  
	   //return (((sign)?'':'-') + '<?php //echo $this->model_superadmin->generate_isocode_bo()?> ' + num + ',' + cents);
	   return (((sign)?'':'-') + 'Rp. '+ num +',00');
}
//url current protocol
function urlprotocol(){
	return document.location.protocol + "//" + document.location.hostname + "/";	 
}
//in use now only input decimal value no char
function decimals(evt,id)
{
	try{
        var charCode = (evt.which) ? evt.which : event.keyCode;
  
        if(charCode==46){
            var txt=document.getElementById(id).value;
            if(!(txt.indexOf(".") > -1)){
	
                return true;
            }
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57) )
            return false;

        return true;
	}catch(w){
		alert(w);
	}
};


function addform(url,form,div)
{		
	//get url protocol current 	
	loading(true);
	
	$.ajax ({
		url		: url,
		type	: 'post',
		data	: form,
		cache	: false,
		success	: function (param)
		{			
			var json = eval('('+param+')');
			//sukkses
			if (json.error == 0 && json.type == 'save')
			{
				window.location.href = json.redirect;
				
			}			
			else if (json.error == 0 && json.type == 'project')
			{
				alert(json.msg);				
				window.location.href = urlprotocol() + json.type;
			}
			else if (json.error == 0 && json.type == 'alert')
			{
				show_alert('.success','success',json.msg);
				$("#"+div).html(json.content);
				$('select option[value=""]').prop("selected", "selected");
				$('input[type=text]').val('');
				$('textarea').val('');
				
			}
			else if (json.error == 0 && json.type == "cari")
			{
				//alert(json.msg);
				$("#"+div).html(json.content).fadeIn(50);
			}
			else if (json.error == 1 && json.type == "error")
			{
				//alert(json.msg);
				show_alert('.danger','error',json.msg);
			}
			
			loading(false);
				
		},
		error	: function (param)
		{
				alert('error show cart');
		}
	});
}
function modalConfirm(url,val,div){
	bootbox.confirm(div, function(result) {
		if(result) {
			//actions
			loading(true);
			var datax = {'value':val};
			$.ajax({
				url 	: url,
				type	: 'post',
				data	: datax,
				cache	: false,
				success	: function (param){
					var json = eval('('+param+')');
					if (json.error == 0){
						window.location.reload();
					}else{
						bootbox.alert(json.msg);
					}
					
					loading(false);
				},error : function(param){
					alert('error'+param);
				}
			})
		}
	});
}
function confirms(url,val,div)
{
	if (confirm('Anda yakin hapus data ?')){
		ajaxcall(url,val,div);
	}
}
function ajaxcallmulti(url,val,div)
{
	loading(true);	
	var spl = div.split('-');
	var div0 = spl[0];
	var div1 = spl[1];	
	var div2 = spl[2];	
	var datax = {'value':val};
	$.ajax({
		url 	: url,
		type	: 'post',
		data	: datax,
		cache	: false,
		success	: function (param){
			var json = eval('('+param+')');
			if (json.error == 0){
				$("#"+div0).html('');
				$("#"+div1).html('');
				$("#"+div2).html('');
				$("#"+div0).append(json.element0);
				$("#"+div1).append(json.element1);
				$("#"+div2).append(json.element2);
			}			
			loading(false);
		},error : function(param){
			alert('error'+param);
		}
	})
}
function cheklabel(url,val,div){
	if (val.checked){
		val = 1;
	}else{
		val=0;
	}
	ajaxcall(url,val,div);
}
function ajaxcall(url,val,div)
{			
	loading(true);		
	var datax = {'value':val};
	$.ajax({
		url 	: url,
		type	: 'post',
		data	: datax,
		cache	: false,
		success	: function (param){
			$("#"+div).html('');
			$("#"+div).append(param);
			loading(false);
		},error : function(param){
			alert('error'+param);
		}
	})
}
function ajaxDelete(url,val,div)
{				
	var datax = {'value':val,'div':div};
	if (confirm('Anda yakin ingin menghapus data ?')){
		loading(true);	
		$.ajax({
			url 	: url,
			type	: 'post',
			data	: datax,
			cache	: false,
			success	: function (param){
				var json = eval('('+param+')');
				if (json.error == 0){
					$(".selected").fadeOut('slow');					
					show_alert('.success','success',json.msg);
					//$("#"+div).remove();
				}else{
					show_alert('.danger','danger',json.msg);
				}			
				loading(false);
			},error : function(param){
				alert('error'+param);
			}
		})
	}
	
}
function ajaxcheck(url,val,obj){	
	loading(true);	
	var cb = document.getElementsByTagName('input');
	var spl = val.split('#');
	var access = spl[1];
	if (obj.checked){
		var check = 1;
		/*jika melakukan check all sesuai access*/
		if (url == 'bo/group/check_all'){					
			for (var i=0; i < cb.length; i++)
			{
				if (cb[i].className == access)
				{
					cb[i].checked = true;
				}
			}
		}
	}else{
		var check = 0;
		/*jika melakukan check all sesuai access*/
		if (url == 'bo/group/check_all'){
			for (var i=0; i < cb.length; i++)
			{
				if (cb[i].className == access)
				{
					cb[i].checked = false;
				}
			} 
		}
	}	
	var datax = {'value':val,'check':check};
	$.ajax({
		url 	: urlprotocol()+url,
		type	: 'post',
		data	: datax,
		cache	: false,
		success	: function (param){
			var json = eval('('+param+')');
			if (json.error == 0){
				show_alert('.success','success',json.msg);
			}else if (json.error == 1){
				show_alert('.danger','danger',json.msg);
			}
			loading(false);
		},error : function(param){
			alert('error'+param);
		}
	})
}
//alert show
function show_alert(div,status,msg){	
	$(div).removeClass("alert alert-danger alert-dismissable");
	$(div).removeClass("alert alert-success alert-dismissable");
	$(div).removeClass("alert alert-warning alert-dismissable");
	$(div).addClass("alert alert-"+status+" alert-dismissable");
	if(status == 'warning')
		var span = ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong class="label label-warning">WARNING!</strong><br>';
	else if (status == 'success')
		var span = ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong class="label label-success">CONGRATULATIONS!</strong><br>';
	else if (status == 'danger')
		var span = ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong class="label label-important">ERROR!</strong><br>';
    //$(div).html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+"<b>"+msg+"</b>").show(500);
    $(div).html(span+msg).slideDown();//slideDown(500);
// 	$(div).removeClass('hide');
    //$(div).slideUp();
	setTimeout(function(){ $(div).slideUp(500); },10000);
}




//for ajax actions
function loading(is_show){	
	//get url protocol current 

    if(is_show == true){
        $("#loading").html("<img src=\""+urlprotocol()+"assets/bo/images/loader.gif\" />").fadeIn('slow');
		}
    if(is_show == false){
        $("#loading").html("<img src=\""+urlprotocol()+"assets/bo/images/loader.gif\" />").fadeOut('slow');
		
	}
}


