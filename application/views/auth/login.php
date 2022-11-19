<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/images/favicon.png') ?>">
    <title>JMS - Login</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">    
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            font-family: 'Varela Round', sans-serif !important;
        }
        .form-control:focus,button:focus {
            box-shadow: none;
            outline: none;
        }
        .alert,.alert-dismissible .close {
            padding: 0.5rem 1.25rem;
        }
        .error{
            color:red;
        }
        a,a:hover{
          color: #000;
          text-decoration: none;
        }
    </style>
</head>

<body>
  
     <div class="container">
       <div class="row justify-content-center align-items-center" style="height: 100vh">
          <div class="col-lg-5 col-md-8">
             <div class="card o-hidden border-0 shadow-lg my-5">                
                <div class="card-body p-0">
                   <div class="py-5 px-3">
                      <div class="text-center">
                         <h1 class="h4 text-gray-900 mb-3">Welcome!</h1>
                      </div>
                      <hr>
                      <?php if($message != ""): ?>
                       <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $message;?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <?php endif; ?>
                      <?php echo form_open("auth/login", array('class'=>'form-horizontal'));?>
                        <div class="form-group">
                           <?php echo form_input($identity);?>
                           <?php echo form_error('identity','<p class="error">', '</p>'); ?>
                        </div>
                        <div class="form-group">                         
                           <?php echo form_input($password);?>                           
                          <?php echo form_error('password','<p class="error">', '</p>'); ?>
                        </div>
                        <?php echo form_hidden($csrf); ?>
                        <button class="btn btn-dark btn-block">Login</button>
                        <!-- <p class="text-center mt-2"><a href="<?php echo base_url('auth/create_user') ?>">Not A Member! Register</a></p> -->
                      <?php echo form_close();?>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>

</body>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>    
</html>