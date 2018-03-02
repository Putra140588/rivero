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
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover ss-tables">
                                    <thead>
                                        <tr><th>#</th>
                                        	<th>Akses Kode</th> 
                                        	<th>Nama Modul</th>                                      	
                                        	<th>Level</th>     
                                        	<th>Link</th>
                                        	<th>Icon</th>                              	                                      	                                        	                                     	                                    	
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
<script type="text/javascript">	
	$(document).ready(function(){		
		loaddatatable('<?php echo base_url('bo/'.$class.'/get_records')?>');
	});
	</script>   