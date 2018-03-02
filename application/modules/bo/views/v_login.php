<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="<?php echo base_url()?>assets/bo/images/logo/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rivero Water | Login</title>
    <!-- Core CSS - Include with every page -->
    <link href="<?php echo base_url()?>assets/bo/css/bootstrap.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/font-awesome.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/pace-theme-big-counter.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/style.css" rel="stylesheet" />
    <link href="<?php echo base_url()?>assets/bo/css/main-style.css" rel="stylesheet" />
</head>
<body class="body-Login-back">

    <div class="container">
       
        <div class="row">
            <div class="col-md-4 col-md-offset-4 text-center logo-margin ">
              <img src="<?php echo base_url()?>assets/bo/images/logo/logo.png" alt="Satu Scan"/>
             
            </div>
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">                  
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Enter Your Information </h3>
                    </div>
                    <div class="panel-body">
                    	<div class="danger"><?php echo validation_errors()?></div>
                        <?php echo form_open(base_url('bo/mplogin'))?>
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" type="text" value="<?php echo set_value('email')?>">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="<?php echo set_value('password')?>">
                                </div>
                               <!--  
                                <div class="checkbox">
                                    <a href="#">Lupa Password ?</a>
                                </div>    
                                -->                           
                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                       <?php echo form_close()?>
                    </div>
                    <div class="panel-footer text-center">
                        <h3 class="panel-bottom">2017 &copy; PT. Tirta Alam Semesta</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Core Scripts - Include with every page -->
    <script src="<?php echo base_url()?>assets/bo/js/jquery-1.10.2.js"></script>
    <script src="<?php echo base_url()?>assets/bo/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url()?>assets/bo/js/jquery.metisMenu.js"></script>
    <script src="<?php echo base_url()?>assets/bo/js/function.js"></script>

</body>

</html>
