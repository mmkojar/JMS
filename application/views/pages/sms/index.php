<?php $this->load->view('templates/header') ?>

            <div class="col-12">
	            <div class="card">
	            	<div class="card-header">
	                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
	                    <!-- <a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('sms/add') ?>">Add SMS</a> -->
	                  </div>
	                <div class="card-body">
	                    <div class="table-responsive">
	                    	<?php $sr_no=1; ?>
	                        <table id="config_table" class="table table-striped table-bordered">
	                            <thead>
	                                <tr>
	                                	<th>Sr.No</th>
	                                    <th>Title</th>
	                                    <th>Content</th>
	                                    <th>Status</th>
	                                    <th>Date</th>
	                                    <th>Action</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                                <?php foreach ($smstmp as $res):?>
										<tr>
											<td><?php echo $sr_no; ?></td>
								            <td><?php echo $res['title']; ?></td>
								            <td><?php echo $res['content']; ?></td>
								            <td><?php echo ($res['status'] == '1' ? '<span class="btn btn-success btn-sm">Active</span>' : '<span class="btn btn-danger btn-sm">Inactive</span>') ?></td>
											<td><?php echo date('M j Y', strtotime($res['created_at'])); ?></td>
											<td><a class="btn btn-success btn-sm" href="<?php echo base_url('sms/edit/'.$res['id']) ?>"><i class="mdi mdi-pencil"></i></a>
											<!-- <a class="btn btn-warning btn-sm" href="<s?php //echo base_url('sms/send') ?>"><i class="mdi mdi-rocket"></i></a> -->
											</td>
										</tr>
										<?php $sr_no++?>
									<?php endforeach;?>
	                            </tbody>
	                        </table>
	                    </div>

	                </div>
	            </div>
            </div>
            
    
<?php $this->load->view('templates/footer') ?>