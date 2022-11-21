<?php $this->load->view('templates/header') ?>
            
            <style type="text/css">
                .db-class h4 {
                    color: #fff;
                    font-weight: bold;
                }
                .db-class h3 {
                    color: #27a9e3;
                    font-weight: bold;
                }
                .box {
                    background-color: #343a40;
                    text-align: center;
                }
            </style>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                        <?php if($this->ion_auth->is_admin()): ?>
                            <a href="<?php echo base_url() ?>/backupDB.php" class="btn btn-dark float-right">Back Up DB</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if($this->ion_auth->is_admin()): ?>
                                <div class="col-md-6 col-lg-3">
                                    <a href="<?php echo base_url('users') ?>">
                                        <div class="card card-hover db-class">
                                            <div class="box">
                                                <h4>Total Users</h4>
                                                <h3><?php echo count($total_users); ?></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6 col-lg-3">
                                <a href="<?php echo base_url('members') ?>">
                                    <div class="card card-hover db-class">
                                        <div class="box">
                                            <h4>Total Members</h4>
                                            <h3><?php echo count($members); ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Column -->
                             <div class="col-md-6 col-lg-3">
                                <a href="<?php echo base_url('members') ?>">
                                    <div class="card card-hover db-class">
                                        <div class="box">
                                            <h4>Active Members</h4>
                                            <h3><?php echo count($active_members); ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Column -->            
                            <div class="col-md-6 col-lg-3">
                                <a href="<?php echo base_url('reports/death') ?>">
                                    <div class="card card-hover db-class">
                                        <div class="box">
                                            <h4>Inactive Members</h4>
                                            <h3><?php echo count($inactive_members); ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Column -->
                            <div class="col-md-6 col-lg-3">
                                <a href="<?php echo base_url('reports/divorce') ?>">
                                    <div class="card card-hover db-class">
                                        <div class="box">
                                            <h4>Divorced Members</h4>
                                            <h3><?php echo count($divorced_members); ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="<?php echo base_url('reports/zone_transfer') ?>">
                                    <div class="card card-hover db-class">
                                        <div class="box">
                                            <h4>Zone Transfer Members</h4>
                                            <h3><?php echo count($zone_transfer); ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>             
                        </div>
                    </div>
                </div>
            </div>
    
<?php $this->load->view('templates/footer') ?>