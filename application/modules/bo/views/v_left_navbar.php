 <!-- navbar side -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <!-- sidebar-collapse -->
            <div class="sidebar-collapse">
                <!-- side-menu -->
                <ul class="nav" id="side-menu">
                    <li>
                        <!-- user image section-->
                        <div class="user-section">
                            <div class="user-section-inner">
                                <img src="<?php echo base_url()?>assets/bo/images/user.jpg" alt="">
                            </div>
                            <div class="user-info">
                                <div><?php echo $this->session->userdata('nama_depan')?> <strong><?php echo $this->session->userdata('nama_belakang')?></strong></div>
                                <div class="user-text-online">
                                    <span class="user-circle-online btn btn-success btn-circle "></span>&nbsp;Online
                                </div>
                            </div>
                        </div>
                        <!--end user image section-->
                    </li>
                    <li class="sidebar-search">
                        <!-- search section-->
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        <!--end search section-->
                    </li>
                    <li class="">
                        <a href="<?php echo base_url()?>"><i class="fa fa-dashboard fa-fw"></i> DASHBOARD</a>
                    </li>
                   <?php $parent= $this->m_master->get_akses_modul(array('B.id_modul_parent'=>0,'level'=>0));
                    foreach ($parent->result() as $row){
                    	$sub = $this->m_master->get_akses_modul(array('B.id_modul_parent'=>$row->id_modul,'level'=>1));                  	         	
                    	if ($sub->num_rows() > 0){
                    		//jika modul mempunyai child sub
                    	echo '<li class="">
		                        <a href="javascript:void(0)"><i class="'.$row->icon.'"></i> '.$row->nama_modul.' <span class="fa arrow"></span></a>
                        		<ul class="nav nav-second-level">';                       				
	                    			foreach ($sub->result() as $val){
	                    				echo '<li><a href="'.base_url($val->link).'">'.$val->nama_modul.'</a></li>';
	                    			}
		                 echo '</ul>
                             </li>';
                    	}else{
                    		echo '<li class="">
		                        	<a href="'.base_url($row->link).'"><i class="'.$row->icon.'"></i> '.$row->nama_modul.'</a>                        		
                            	 </li>';
                    	}
                    }?>                   
                                
                </ul>
                <!-- end side-menu -->
            </div>
            <!-- end sidebar-collapse -->
        </nav>
        <!-- end navbar side -->
       