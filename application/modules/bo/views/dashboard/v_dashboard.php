<!--  page-wrapper -->
        <div id="page-wrapper">
            <div class="row">
                <!-- Page Header -->
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                      <div class="alert alert-info"><h3>Selamat Datang <?php echo $this->session->userdata('nama_depan')?></h3></div>
                	
                	 <?php $this->load->view('bo/v_alert_notif');
	                	$x = $this->m_load_config->activasi();
	                	if (isset($x[0]->active) || !isset($x[0]->active)){	                	 	
	                	 	if (!isset($x[0]->active)){
								$this->load->view('v_form_activasi');
								echo '<H2>TRIAL</H2>';
							}
	                	 	else if (isset($x[0]->active) && $x[0]->active == 0){
								$this->load->view('v_form_activasi');
	                	 		echo '<div class="alert alert-danger"><H2>APPLICATION BLOCKED INPUT SERIAL NUMBER FOR ENABLE</H2></div>';
	                	 	}
						}											
					?>
                </div>
                <!--End Page Header -->             
            </div>

          

        </div>
        <!-- end page-wrapper -->