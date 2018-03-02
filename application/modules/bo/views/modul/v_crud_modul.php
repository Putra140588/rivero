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
     			<input type="hidden" name="id" value="<?php echo isset($id_modul) ? $id_modul : ''?>">
     			<?php $select='';?>
     				<div class="row">     				
     					<div class="col-lg-6">
	     					<div class="form-group">
	     						<label> Nama Modul <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="namamodul" placeholder="Nama Modul" value="<?php echo isset($nama_modul) ? $nama_modul : ''?>"  required>
	     						</div>
	     					</div>
	     					<div class="form-group">
	     						<label> Akses Kode <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="aksescode" placeholder="Akses Kode" value="<?php echo isset($akses_code) ? $akses_code : ''?>"  required>
	     						</div>
	     					</div>	    
	     					<div class="form-group">
	     						<label>Modul Parent <span class="required">*</span></label>
	     						<div class="controls">
	     							<select name="parent" class="form-control" required>
	     								<option value="" selected disabled>Pilih Modul Parent</option>	
	     								<option value="0">Parent</option>     								    								
	     								<?php foreach ($modul->result() as $row){
		     								if ($id_modul != ''){
		     									$select = ($id_modul_parent == $row->id_modul) ? 'selected' : ''; 
		     								}
	     								echo '<option value="'.$row->id_modul.'" '.$select.'>'.$row->nama_modul.'</option>';
	     								}?>	     									 
	     							</select>
	     						</div>
	     					</div> 	
	     					<div class="form-group">
	     						<label>Level <span class="required">*</span></label>
	     						<div class="controls">
	     							<select name="level" class="form-control" required>
	     								<option value="" selected disabled>Pilih Level</option>	
	     								<option value="0">0</option>
	     								<option value="1">1</option> 									 
	     							</select>
	     						</div>
	     					</div> 
	     					<div class="form-group">
	     						<label> Link</label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="link" placeholder="Link">
	     						</div>
	     					</div>	
	     					<div class="form-group">
	     						<label> Icon</label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="icon" placeholder="Icon">
	     						</div>
	     					</div>	
	     					<div class="form-group">
	     						<label>Sort <span class="required">*</span></label>
	     						<div class="controls">
	     							<select name="sort" class="form-control" required>
	     								<option value="" selected disabled>Pilih Sort</option>	
	     								<?php for ($i=1; $i <= 20; $i++){?>
	     									<option value="<?php echo $i?>"><?php echo $i?></option> 	
	     								<?php }?>									 
	     							</select>
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
       