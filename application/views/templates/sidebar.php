<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" data-sidebarbg="skin5">
	<!-- Sidebar scroll-->
	<div class="scroll-sidebar">
		<!-- Sidebar navigation-->
		<nav class="sidebar-nav">
			<ul id="sidebarnav" class="p-t-30">
				<?php if($this->ion_auth->logged_in()): ?>

				<?php if($this->ion_auth->in_group("admin") || $this->ion_auth->in_group("supervisor")): ?>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url() ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>
				<?php endif; ?>
				<?php if($this->ion_auth->is_admin()): ?>
				<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
						href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
							class="hide-menu">Masters </span></a>
					<ul aria-expanded="false" class="collapse  first-level">
						<li class="sidebar-item"><a href="<?php echo base_url('masters/zone') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu"> Zone
								</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('masters/fee') ?>" class="sidebar-link"><i
									class="mdi mdi-alert-octagon"></i><span class="hide-menu"> Joda Fee </span></a></li>

						<li class="sidebar-item"><a href="<?php echo base_url('masters/surnames') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Surnames </span></a></li>
					</ul>
				</li>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('books') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Books</span></a></li>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('users') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Users</span></a></li>
				<?php endif; ?>

				<?php if($this->ion_auth->in_group("admin") || $this->ion_auth->in_group("supervisor")): ?>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('members') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Members</span></a></li>

				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('invoices') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Invoices</span></a></li>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('payment') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Payments Entry</span></a></li>
				<?php endif; ?>

				<?php if($this->ion_auth->is_admin()): ?>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('collection') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Collections Entry</span></a></li>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('expenses') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Expenses Entry</span></a></li>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url('sms') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">SMS Template</span></a></li>
				<?php endif; ?>
				<?php if($this->ion_auth->in_group("admin") || $this->ion_auth->in_group("notify")): ?>
				<!-- <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php //echo base_url('message') ?>" aria-expanded="false"><i
							class="mdi mdi-view-dashboard"></i><span class="hide-menu">Notification Messages</span></a>
				</li> -->
				<?php endif; ?>
				<?php if($this->ion_auth->in_group("admin") || $this->ion_auth->in_group("supervisor")): ?>

				<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
						href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
							class="hide-menu">Reports </span></a>
					<ul aria-expanded="false" class="collapse  first-level">
						<li class="sidebar-item"><a href="<?php echo base_url('reports/user_outstanding') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Member Wise Report</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/outstanding') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Outstanding Report</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/receipts') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Receipts Reports</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/area') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu"> Area
									Wise Income</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/surname') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Surname Wise Income</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/collection') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Collection Reports</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/death') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu"> Death
									Member</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/divorce') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">
									Divorce Member</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url('reports/zone_transfer') ?>"
								class="sidebar-link"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu"> Zone
									Transfer Member</span></a></li>
					</ul>
				</li>
				<?php endif; ?>
				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url("users/edit/".$this->ion_auth->user()->row()->id) ?>"
						aria-expanded="false"><i class="mdi mdi-account"></i><span class="hide-menu">My
							Profile</span></a></li>

				<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
						href="<?php echo base_url("auth/logout") ?>" aria-expanded="false"><i
							class="mdi mdi-power"></i><span class="hide-menu">Logout</span></a></li>
				<?php endif; ?>
			</ul>
		</nav>
		<!-- End Sidebar navigation -->
	</div>
	<!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
