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
     			<form id="frmkaryawan" method="post" action="<?php echo base_url('bo/'.$class.'/proses')?>">
     			<input type="hidden" name="id" value="<?php echo isset($id_user) ? $id_user : ''?>">
     			<?php $select='';?>
     				<div class="row">     				
     					<div class="col-lg-6">	    					
	     					
	     					<div class="form-group">
	     						<label>Nama Depan <span class="required">*</span></label>
	     						<div class="controls">
	     							<input class="form-control" type="text" name="namadepan" placeholder="Nama Depan" value="<?php echo isset($nama_depan) ? $nama_depan : ''?>" required>
	     						</div>
	     					</div>
	     					<div class="form-group">
	     						<label>Nama Belakang </label>
	     						<div class="controls">
	     							<input class="form-control" type="text" name="namabelakang" placeholder="Nama Belakang" value="<?php echo isset($nama_belakang) ? $nama_belakang : ''?>">
	     						</div>
	     					</div>
	     					<div class="form-group">
	     						<label>Email <span class="required">*</span></label>
	     						<input class="form-control" type="text" name="email" placeholder="Email" onchange="ajaxcall('<?php echo base_url('bo/'.$class.'/cek_email')?>',this.value,'email')" required value="<?php echo isset($email) ? $email : ''?>">
	     						<span id="email" class="required"></span>
	     					</div>
	     					
     					</div>
     					<div class="col-lg-6">
     						<div class="form-group">
	     						<label>Password <span class="required">*</span></label>
	     						<input class="form-control" type="password" name="password" placeholder="Password" onchange="ajaxcall('<?php echo base_url('bo/'.$class.'/lengt_pass')?>',this.value,'pass')">
	     						( Minimal 8 Karater )<br>
	     						<span id="pass" class="required"></span>
	     					</div>     						
     						<div class="form-group">
     							<label>Group <span class="required">*</span></label>
     							<select class="form-control" name="group" required>
	     							<option value="" selected>Pilih Group</option>	
	     							<?php foreach ($group->result() as $row){
	     								if ($id_user != ''){
	     									$select = ($kd_group == $row->kd_group) ? 'selected' : '';
	     								}
	     								echo '<option value="'.$row->kd_group.'" '.$select.'>'.$row->nama_group.'</option>';
	     							}?>
	     						</select>
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
       