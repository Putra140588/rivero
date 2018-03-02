<div id="page-wrapper">
       <div class="cleaner_h50"></div>
        <div class="row">
                <div class="col-lg-12">  
                 <?php $this->load->view('bo/v_alert_notif');?>                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                             <h4><?php echo $page_title?> <label><?php echo $nama_group?></label></h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr><th>#</th>                                        	
                                        	<th>Nama Modul</th>  
                                        	<th>Active <input type="checkbox" name="active" value="<?php echo $kd_group.'#active'?>" onchange="ajaxcheck('bo/group/check_all',this.value,this)"></th>
                                        	<th>View <input type="checkbox" name="view" value="<?php echo $kd_group.'#view'?>" onchange="ajaxcheck('bo/group/check_all',this.value,this)"></th>
                                        	<th>Add <input type="checkbox"  name="add" value="<?php echo $kd_group.'#add'?>" onchange="ajaxcheck('bo/group/check_all',this.value,this)"></th>
                                        	<th>Edit <input type="checkbox"  name="edit" value="<?php echo $kd_group.'#edit'?>" onchange="ajaxcheck('bo/group/check_all',this.value,this)"></th>   
                                        	<th>Delete <input type="checkbox"  name="delete" value="<?php echo $kd_group.'#delete'?>" onchange="ajaxcheck('bo/group/check_all',this.value,this)"></th>                                  	                                      	                                                                           	                                      	                                        	                                                                            	
                                        </tr>                                      	              	
                                    </thead> 
                                    <tbody>
                                    	<?php $no=1;
                                    	foreach ($get_parent_modul->result() as $row){
                                    		$active = ($row->active == 1) ? 'checked' : '';
                                    		$view = ($row->view == 1) ? 'checked' : '';
                                    		$add = ($row->add == 1) ? 'checked' : '';
                                    		$edit = ($row->edit == 1) ? 'checked' : '';
                                    		$delete = ($row->delete == 1) ? 'checked' : '';                                    		
                                    		echo '<tr><td>'.$no++.'</td>                                      					 
                    								  <td>'.$row->nama_modul.'</td>                                    				  
                        							  <td class="center"><input class="active" type="checkbox" '.$active.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-active\',this)"></td>';
                        						if ($row->link == ''){
                        							echo '<td colspan=4></td>';
                                    			}else{                                    					
                                    				echo '<td class="center"><input class="view" type="checkbox" '.$view.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-view\',this)"onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-view\',this)"></td>
                                        			 	  <td class="center"><input class="add" type="checkbox" '.$add.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-add\',this)"></td>
                                        				  <td class="center"><input class="edit" type="checkbox" '.$edit.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-edit\',this)"></td>
                                        				  <td class="center"><input class="delete" type="checkbox" '.$delete.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$row->id_akses.'-delete\',this)"></td>';                                        				  
                                    				}
                        							  
						                        	  
                                    		$get_sub_modul = $this->m_master->get_modul_group(array('B.kd_group'=>$row->kd_group,'B.id_modul_parent'=>$row->id_modul));
                                    			foreach ($get_sub_modul->result() as $val){
                                    				$active = ($val->active == 1) ? 'checked' : '';                                    				
                                    				$view = ($val->view == 1) ? 'checked' : '';
                                    				$add = ($val->add == 1) ? 'checked' : '';
                                    				$edit = ($val->edit == 1) ? 'checked' : '';
                                    				$delete = ($val->delete == 1) ? 'checked' : '';
                        							echo '<tr>
                        								  <td></td>                                    					  
                        								  <td><div style="margin-left:20px"><i class="icon-chevron-right"></i> '.$val->nama_modul.'</div></td>
                                        				  <td class="center"><input class="active" type="checkbox" '.$active.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$val->id_akses.'-active\',this)"></td>
                                        			 	  <td class="center"><input class="view" type="checkbox" '.$view.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$val->id_akses.'-view\',this)"></td>
                                        				  <td class="center"><input class="add" type="checkbox" '.$add.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$val->id_akses.'-add\',this)"></td>
                                        				  <td class="center"><input class="edit" type="checkbox" '.$edit.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$val->id_akses.'-edit\',this)"></td>
                                        				  <td class="center"><input class="delete" type="checkbox" '.$delete.' onchange="ajaxcheck(\'bo/'.$class.'/check_modul\',\''.$val->id_akses.'-delete\',this)"></td>
                        							  </tr>';
                                    			}
                        					echo '</tr>';
                                    	}?>
                                    </tbody>                                   
                                </table>
                            </div>
                            <button type="button" class="btn btn-warning" onclick="window.history.back()">Kembali</button>
                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
              
     </div>
</div>
