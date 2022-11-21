<?php $this->load->view('templates/header') ?>

            <div class="col-12">
	            <div class="card">
	            	<div class="card-header">
	                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
	                    <a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('message/add') ?>">Add Message</a>
	                  </div>
	                <div class="card-body">
	                    <div class="table-responsive">
	                    	<?php $sr_no=1; ?>
	                        <table id="config_table" class="table table-striped table-bordered">
	                            <thead>
	                                <tr>
	                                	<th>Sr.No</th>
	                                    <th>Sender Name</th>
	                                    <th>Title</th>
	                                    <th>Message</th>
	                                    <th>Date</th>
	                                    <th>Action</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                                <?php foreach ($messages as $message):?>
										<tr>
											<td><?php echo $sr_no; ?></td>
								            <td><?php echo $message['sender_name']; ?></td>
								            <td><?php echo $message['title']; ?></td>
								            <td><?php echo $message['message_en'] ?></td>
											<td><?php echo date('M j Y', strtotime($message['date'])); ?></td>
											<td><a class="btn btn-success btn-sm" 
												href="<?php echo base_url('message/edit/'.$message['id']) ?>"><i class="mdi mdi-pencil"></i></a>
												<a class="btn btn-danger btn-sm delete_items text-white" id="<?php echo $message['id'] ?>" url="message/delete" tname="notification_message"><i class="mdi mdi-delete"></i></a>											
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