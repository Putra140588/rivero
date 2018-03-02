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
                         	<a class="btn btn-primary btn-sm" href="<?php echo base_url('bo/'.$class.'/export/csv/'.$code)?>">Export CSV</a>
	                        <a class="btn btn-info btn-sm" href="<?php echo base_url('bo/'.$class.'/export/excel/'.$code)?>">Export Excel</a>
	                        <a class="btn btn-warning btn-sm" href="<?php echo base_url('bo/'.$class.'/export/pdf/'.$code)?>" target="_blank">Export PDF</a> 
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                            <?php $this->load->view('bo/v_date_range')?>
                                <table class="table table-striped table-bordered table-hover ss-tables">
                                    <thead>
                                        <tr><th>#</th>
                                        	<th>Item Code</th>   
                                        	<th>Move</th>    
                                        	<th>Date Add</th>                                	                                        	   
                                        	<th class="no-sort">Siklus</th>                                        	                           	                                      	                                     	                                        	                              	                                        	                                     	                                       	
                                        </tr>                                      	              	
                                    </thead>                                    
                                </table>
                            </div>  
                            <div class="cleaner_h20"></div>
                            <button class="btn btn-warning btn-small" onclick="javascript:history.back()">Kembali</button>                          
                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
              
     </div>
</div>    
<input type="hidden" id="url-datatable" value="<?php echo base_url('bo/'.$class.'/get_records/'.$code)?>">
<script type="text/javascript">	
	$(document).ready(function(){		
		loaddatatable();		
	});
</script>      