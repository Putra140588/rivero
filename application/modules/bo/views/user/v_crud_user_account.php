<div id="page-wrapper">     
 <div class="cleaner_h50"></div>
     <div class="row">
     	<div class="col-lg-12">
     		<div class="panel panel-info">
     			<div class="panel-heading">
     				<h4><?php echo $page_title?></h4>
     			</div>
     			<div class="panel-body">
     			<?php $this->load->view('bo/v_alert_notif');?>
     			<form id="frmkaryawan" method="post" action="<?php echo base_url('bo/'.$class.'/user_update')?>">     			
     			<?php $select='';?>
     				<div class="row">     				
     					<div class="col-lg-6">
     						<div class="form-group">
	     						<label> Email <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''?>"  required>	     							
	     						</div>
	     					</div>	   
	     					<div class="form-group">
	     						<label> Password Lama <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="password" name="oldpass" placeholder="Password Lama" required>	     							
	     						</div>
	     					</div>	
	     					<div class="form-group">
	     						<label> Password Baru <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="password" name="newpass" placeholder="Password Baru" required>	     							
	     						</div>
	     					</div>		     					
     					</div>     							
     				</div>
     				<span class="required">*</span> Wajib diisi.
     				<div class="cleaner_h10"></div>
     				<button type="submit" class="btn btn-primary">Simpan</button>
     				<button type="button" class="btn btn-warning" onclick="window.history.back()">Kembali</button>
     				</form>			
     			</div>
     		</div>
     	</div>
     </div>
</div>
       