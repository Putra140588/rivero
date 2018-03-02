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
     			<input type="hidden" name="id" value="<?php echo isset($id_group) ? $id_group : ''?>">
     			<input type="hidden" name="kd" value="<?php echo isset($kd_group) ? $kd_group : ''?>">
     			<?php $select='';?>
     				<div class="row">     				
     					<div class="col-lg-6">
	     					<div class="form-group">
	     						<label> Nama Group <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="namagroup" placeholder="Nama Group" value="<?php echo isset($nama_group) ? $nama_group : ''?>"  required>
	     							
	     						</div>
	     					</div>	
	     					<div class="form-group">
	     						<label> Kode Group <span class="required">*</span></label>
	     						<div class="controls">	     						
	     							<input class="form-control" type="text" name="kdgroup" placeholder="Kode Group" value="<?php echo isset($kd_group) ? $kd_group : ''?>"  onchange="ajaxcall('<?php echo base_url('bo/'.$class.'/cek_kdgroup')?>',this.value,'group')" required>
	     							<span id="group" class="required"></span>
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
       