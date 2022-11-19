<?php 
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- Favicon icon -->
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>/assets/images/favicon.png">
	<title>JMS - Admin</title>
	<!-- Custom CSS -->

	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap"> -->
	<?php if($page !== 'index.php' && $page !== 'add'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap4.css">
	<!--<link rel="stylesheet" type="text/css" href="<?php //echo base_url(); ?>assets/css/responsive.dataTables.min.css"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/buttons.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/toastr.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/custom.css">
	<?php endif ?>
	<?php if($page == 'user_outstanding' ||  base_url('payment/add')) : ?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/select2.min.css">
	<?php endif ?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.min.css">
</head>

<body>
	<div id="preloader">
		<div class="lds-ripple">
			<div class="lds-pos"></div>
			<div class="lds-pos"></div>
		</div>
	</div>

	<div id="main-wrapper">
		<header class="topbar" data-navbarbg="skin5">
			<nav class="navbar top-navbar navbar-expand-md navbar-dark">
				<div class="navbar-header" data-logobg="skin5">
					<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
						<i class="mdi mdi-menu"></i></a>

					<a class="navbar-brand" href="<?php echo base_url(); ?>">
						<b class="logo-icon p-l-10">
							<img src="<?php echo base_url(); ?>/assets/images/logo-icon.png" alt="homepage"
								class="light-logo" />
						</b>
						<span class="logo-text">
							<h4 class="pt-2">JMS</h4>
						</span>

					</a>

					<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
						data-toggle="collapse" data-target="#navbarSupportedContent"
						aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
							class="ti-more"></i></a>
				</div>

				<div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">

					<ul class="navbar-nav float-left">
						<li class="nav-item d-none d-md-block"><a
								class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
								data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
					</ul>
					<h5 class="text-center text-white m-auto">
						<?php echo $this->ion_auth->user()->row()->email.' ('.$this->ion_auth->user()->row()->group_name.') '.$this->ion_auth->user()->row()->zone_name ?>
					</h5>
				</div>

			</nav>
		</header>

		<?php $this->load->view('templates/sidebar') ?>

		<div class="page-wrapper">
			<div class="container-fluid">

				<div id="flash_messages">
					<?php if($this->ion_auth->logged_in()) : ?>
					<?php if($this->session->flashdata('message')) { ?>
					<p class="alert alert-success">
						<?php echo $this->session->flashdata('message'); unset($_SESSION['message']) ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</p>
					<?php } ?>
					<?php if($this->session->flashdata('success')) { ?>
					<p class="alert alert-success">
						<?php echo $this->session->flashdata('success'); unset($_SESSION['success']) ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</p>
					<?php } ?>
					<?php if($this->session->flashdata('error')) { ?>
					<p class="alert alert-danger">
						<?php echo $this->session->flashdata('error'); unset($_SESSION['error']) ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</p>
					<?php } ?>
					<?php if($this->session->flashdata('warning')) { ?>
					<p class="alert alert-warning">
						<?php echo $this->session->flashdata('warning'); unset($_SESSION['warning']) ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</p>
					<?php } ?>
					<?php endif; ?>
				</div>

				<div class="row">
