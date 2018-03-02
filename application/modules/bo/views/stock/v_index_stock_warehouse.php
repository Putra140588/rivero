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
                             <a class="btn btn-primary btn-sm" href="<?php echo base_url('bo/'.$class.'/export/csv')?>">Export CSV</a>
	                        <a class="btn btn-info btn-sm" href="<?php echo base_url('bo/'.$class.'/export/excel')?>">Export Excel</a>
	                        <a class="btn btn-warning btn-sm" href="<?php echo base_url('bo/'.$class.'/export/pdf')?>" target="_blank">Export PDF</a>  
                        </div>                  
                        <div class="panel-body">
                            <div class="table-responsive">                                                    
                                <table class="table table-striped table-bordered table-hover ss-tables">
                                    <thead>
                                        <tr><th>#</th>
                                        	<th>Item Code</th>                                        	
                                        	<th>Supplier</th>   
                                        	<th>Stock Available</th>  
                                        	<th>Stock Out</th>                                        	
                                        	<th>Reject Stock</th>
                                        	<th>Remove Reject</th>
                                        	<th>Siklus</th>                                  	                                      	                                            	  
                                        	<th class="no-sort">Actions</th>                             	                                        	                                     	                                       	
                                        </tr>                                      	              	
                                    </thead>                                    
                                </table>
                            </div>                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
                <div class="col-lg-6">
                	<div class="panel panel-info">      
	                     <div class="panel-heading">
	                     	Summary Stock
	                     </div>
	                     <div class="panel-body">
	                     	 <table class="table table-striped table-bordered table-hover">	  
	                     	 	<thead>
	                     		 	<tr><th>Description</th>
	                     		 		<th>Total</th>
	                     		 		<th>View</th>
	                     	 	</thead>                   	 	
	                     	 	<tbody>
	                     	 		<tr><td>Total Stock Available</td><td class="center"><?php echo $available?></td>
	                     	 			<td class="center">
		                     	 			<a class="btn btn-warning btn-circle" href="<?php echo base_url('bo/stock/index/In')?>"  title="Lihat detail">
											<i class="icon-search"></i>
											</a>
										</td>
	                     	 		</tr>
	                     	 		<tr><td>Total Stock Out</td><td class="center"><?php echo $out?></td>
	                     	 			<td class="center">
		                     	 			<a class="btn btn-warning btn-circle" href="<?php echo base_url('bo/stock/index/Out')?>"  title="Lihat detail">
											<i class="icon-search"></i>
											</a>
										</td>	                     	 		
	                     	 		</tr>
	                     	 		<tr><td>Total Stock Reject</td><td class="center"><?php echo $reject?></td>
	                     	 			<td class="center">
		                     	 			<a class="btn btn-warning btn-circle" href="<?php echo base_url('bo/stock/index/Reject')?>"  title="Lihat detail">
											<i class="icon-search"></i>
											</a>
										</td>
	                     	 		</tr>
	                     	 		<tr><td>Total Remove Reject</td><td class="center"><?php echo $remove?></td>
	                     	 			<td class="center">
		                     	 			<a class="btn btn-warning btn-circle" href="<?php echo base_url('bo/stock/index/Remove')?>"  title="Lihat detail">
											<i class="icon-search"></i>
											</a>
										</td>
	                     	 		</tr>
	                     	 		<tr><td>Total Main Assets</td><td class="center"><b><?php echo $total?></b></td>
	                     	 			<td class="center">
		                     	 			<a class="btn btn-warning btn-circle" href="<?php echo base_url('bo/produk')?>"  title="Lihat detail">
											<i class="icon-search"></i>
											</a>
										</td>
	                     	 		</tr>
	                     	 	</tbody>
	                     	 </table>
	                     </div>
                     </div>
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