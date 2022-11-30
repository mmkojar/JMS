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
			<?php echo form_open(uri_string());?>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Year</label>
						<select name="year" id="year" class="form-control" style="width: 100%;" required>
							<option value="">Select</option>
							<?php foreach($years as $row): ?>
							<option value="<?php echo $row->financial_year ?>"><?php echo $row->financial_year ?>
							</option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="zone">Select Zone</label>
						<select name="zone" id="zone" class="form-control" style="width: 100%;" required>
							<option value="">Select</option>
							<?php foreach($zones as $zone): ?>
							<option value="<?php echo $zone->id ?>"><?php echo $zone->zone_name ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Collector</label><br>
						<select name="collector_id" id="collector_id" class="form-control" style="width: 100%;"
							required>
							<!-- <option value="">Select</option>
							<?php //foreach($users as $user): ?>
							<option value="<?php //echo $user->id ?>">
								<?php //echo str_replace('_',' ',ucwords($user->username)) ?></option>
							<?php //endforeach ?> -->
						</select>
					</div>
				</div>
				<div class="col-md-6 mb-2">
					<div class="form-group">
						<label>Amount Collected</label>
						<input type="number" name="amt_collected" id="amt_collected" class="form-control" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Date</label>
						<input type="date" class="form-control" name="date" id="date" required>
					</div>
				</div>
				<!-- <div class="col-md-6">
					<div class="form-group">
						<label>Receiver Name</label>
						<input type="text" class="form-control" name="receiver_name" id="receiver_name"
							value="<s?php //echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->surname ?>"
							readonly>
					</div>
				</div> -->
				<div class="col-md-6">
					<div class="form-group">
						<label>Remark</label>
						<input type="text" class="form-control" name="remark" id="remark" required>
					</div>
				</div>
			</div>
			<?php echo form_hidden($csrf); ?>
			<?php echo form_submit('submit', 'Submit',array('class'=>'btn btn-success'));?>
			<?php echo form_close();?>
		</div>
	</div>
</div>
<?php $this->load->view('templates/footer') ?>
<script>
	$(document).ready(function () {

		$("#zone").on('change', function () {
			$("#preloader").show();
			var selected_id = $(this).val();
			if (selected_id) {
				$.ajax({
					url: "<?php echo base_url('collection/get_users_from_zone') ?>/" + selected_id,
					method: 'GET',
					dataType: 'json',
					success: function (res) {
						console.log(res);
						var html = '';
						if (res.length > 0) {
							for (var i in res) {
								html += '<option value="' + res[i].id +
									'">' + res[i].username + '</option>';
							}
						} else {
							html += '<option value="">No Collector</option>';
						}
						$("#preloader").hide();
						$("#collector_id").html(html);
					}
				});
			} else {
				$("#preloader").hide();
				$("#collector_id").html('<option value="">Select Zone</option>');
			}

		});
	})

</script>
