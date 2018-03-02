<div id="page-wrapper">
       <div class="row">
          <div class="col-lg-12">
              <h2 class="page-header"><?php echo $page_title?></h2> 
              <?php $this->load->view('bo/v_alert_notif');?>             
        </div>
        <div class="row">
                <div class="col-lg-12">                    
                    <div class="panel panel-info">
                        <div class="panel-heading">                        	
                             <a class="btn btn-success btn-sm" href="<?php echo base_url('bo/'.$class.'/form')?>" >Tambah</a>
                              <a class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus seluruh data')" href="<?php echo base_url('bo/'.$class.'/delete_all')?>" >Hapus Semua</a>
                              <span class="pull-right">
                             	<a class="btn btn-primary btn-sm" href="<?php echo base_url('bo/'.$class.'/export/csv')?>">Export CSV</a>
	                             <a class="btn btn-info btn-sm" href="<?php echo base_url('bo/'.$class.'/export/excel')?>">Export Excel</a>
	                             <a class="btn btn-warning btn-sm" href="<?php echo base_url('bo/'.$class.'/export/pdf')?>" target="_blank">Export PDF</a>                        
                              </span>
                        </div>
                        <div class="panel-body">                        	
                            <div class="table-responsive">
                            <div class="cleaner_h5"></div>
                             <?php $this->load->view('bo/v_date_range')?>                           		
                                <table class="table table-striped table-bordered table-hover ss-tables">
                                    <thead>
                                        <tr><th>#</th>
                                        	<th>Item Code</th>   
                                        	<th>Move</th>                                     	                                      	     
                                        	<th>Date Add</th>                                        	                              	                                        	                                     	
                                        	<th class="no-sort">Actions</th>
                                        </tr>                                      	              	
                                    </thead>                                    
                                </table>
                            </div>                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
              
     </div>
</div>    
<input type="hidden" id="url-datatable" value="<?php echo base_url('bo/'.$class.'/get_records')?>">
<script type="text/javascript">	
	$(document).ready(function(){		
		loaddatatable();		
	});
</script>    