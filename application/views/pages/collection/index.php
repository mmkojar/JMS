<?php $this->load->view('templates/header'); ?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
			<a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('collection/add') ?>">Add
				Collection</a>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="config_table" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Sr.No</th>
							<th>Year</th>
							<th>Zone</th>
							<th>Collector Name</th>
							<th>Receipt Date</th>
							<th>Amount Received</th>
							<th>Receiver Name</th>
							<th>Remark</th>
							<th>Action</th>
						</tr>
					</thead>
					<?php $sr_no = 1; ?>
					<?php foreach($collections as $row): ?>
					<tr>
						<td><?php echo $sr_no ?></td>
						<td><?php echo $row->year ?></td>
						<td><?php echo $row->zone_name ?></td>
						<td><?php echo $row->collector_name; ?></td>
						<td><?php echo $row->receipt_date ? $row->receipt_date : '-' ?></td>
						<td><?php echo $row->receipt_amt ? $row->receipt_amt : '-' ?></td>
						<td><?php echo $row->receiver_name ? $row->receiver_name : '-' ?></td>
						<td><?php echo $row->remark ?></td>
						<td>
							<?php echo anchor("collection/edit/".$row->id, '<i class="mdi mdi-pencil"></i>',array('class'=>'btn btn-success btn-sm')) ;?>
							<a class="btn btn-danger btn-sm delete_items text-white" url="collection/delete"
								tname="collection_entries" id="<?php echo $row->id ?>"><i
									class="mdi mdi-delete"></i></a>
						</td>
					</tr>
					<?php $sr_no++ ; ?>
					<?php endforeach ?>
				</table>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view('templates/footer') ?>
