<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
     <link rel="shortcut icon" href="<?php echo base_url()?>assets/bo/images/logo/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rivero water</title>
    <!-- Core CSS - Include with every page -->
    <link href="<?php echo base_url()?>assets/bo/css/jquery-ui.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/bootstrap.min.css" rel="stylesheet" />
     <link href="<?php echo base_url()?>assets/bo/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/pace-theme-big-counter.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/style.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/main-style.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/datatables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />

    <script src="<?php echo base_url()?>assets/bo/js/jquery-1.12.4.js"></script>
     <script src="<?php echo base_url()?>assets/bo/js/jquery-ui.min.js"></script>
    
    
</head>

<body>
    <!--  wrapper -->
    <div id="wrapper">
        <?php $this->load->view('bo/v_top_navbar')?>        
       	<?php 
       	$x = $this->m_load_config->activasi();
       	if (isset($x[0]->active) || !isset($x[0]->active)){
       		if (!isset($x[0]->active)){
       			$this->load->view('bo/v_left_navbar');       			
       		}
       		else if (isset($x[0]->active) && $x[0]->active == 1){
       			$this->load->view('bo/v_left_navbar');       			
       		}
       	}
       ?>       
   

