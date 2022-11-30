<?php $this->load->view('templates/header'); ?>
<style>
	.error {
		color: red;
	}

</style>
<div class="col-12">
	<div class="card">
		<?php $this->load->view('templates/header_title'); ?>
		<div class="card-body">
			<?php echo form_open('collection/add');?>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Year</label>
						<select name="year" id="year" class="form-control" style="width: 100%;" required>
							<option value="">Select</option>
							<?php foreach($years as $row): ?>
							<option <?php echo $row->financial_year == $collection->year ? 'selected' : '' ?>
								value="<?php echo $row->financial_year ?>"><?php echo $row->financial_year ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="zone">Select Zone</label>
						<select name="zone" id="zone" class="form-control" style="width: 100%;" required>
							<?php foreach($zones as $zone): ?>
							<option <?php echo $zone->id == $collection->zone ? 'selected' : '' ?>
								value="<?php echo $zone->id ?>"><?php echo $zone->zone_name ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Select User</label><br>
						<select name="collector_id" id="collector_id" class="form-control" style="width: 100%;"
							required>
							<option value="">Select</option>
							<?php foreach($users as $user): ?>
							<option <?php echo $user->id == $collection->collector_id ? 'selected' : '' ?> value="<?php echo $user->id ?>" readonly>
								<?php echo str_replace('_',' ',ucwords($user->username)) ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Receipt Date</label>
						<input type="date" class="form-control" name="date" id="date"
							value="<?php echo $collection->date ?>" required>
					</div>
				</div>
				<div class="col-md-6 mb-2">
					<div class="form-group">
						<label>Receipt Amount</label>
						<input type="text" name="amt_collected" id="amt_collected" class="form-control"
							value="<?php echo $collection->amt_collected ?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Remark</label>
						<input type="text" class="form-control" name="remark" id="remark"
							value="<?php echo $collection->remark ?>" required>
					</div>
				</div>
			</div>
			<?php echo form_hidden($csrf); ?>
			<input type="hidden" name="hidden_collection_id" value="<?php echo $collection->id ?>">
			<?php echo form_submit('submit', 'Update',array('class'=>'btn btn-success'));?>
			<?php echo form_close();?>
		</div>
	</div>
</div>
<?php $this->load->view('templates/footer') ?>
