<?php $this->load->view('templates/header') ?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
			<a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('expenses/add') ?>">Add
				Expense</a>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<?php $sr_no=1; ?>
				<table id="config_table" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Sr.No</th>
							<th>Description</th>
							<th>Amount</th>
							<th>Paid By</th>
							<th>Receiver Name</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($expenses as $data):?>
						<tr>
							<td><?php echo $sr_no; ?></td>
							<td><?php echo $data['description']; ?></td>
							<td><?php echo $data['amount']; ?></td>
							<td><?php echo $data['paid_by'] ?></td>
							<td><?php echo $data['receiver_name'] ?></td>
							<td><?php echo date('M j Y', strtotime($data['date'])); ?></td>
							<td><a class="btn btn-success btn-sm"
									href="<?php echo base_url('expenses/update/'.$data['id']) ?>"><i
										class="mdi mdi-pencil"></i></a>
								<a class="btn btn-danger btn-sm delete_items text-white" id="<?php echo $data['id'] ?>"
									url="expenses/delete" tname="expenses"><i class="mdi mdi-delete"></i></a>
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
