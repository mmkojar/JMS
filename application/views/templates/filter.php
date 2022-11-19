<?php 

$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
$page = end($link_array);

?>

<div class="row">
	<?php if($page !== 'members' && $page !== 'collection'): ?>
		<div class="col-md-3">
		    <div class="form-group">
		        <label for="year_search">Select Year</label>		        
		        <select id="year_search" class="form-control">		        	
		            <?php foreach($get_payment_year as $row): ?>
		              <option value="<?php echo $row->financial_year ?>"><?php echo $row->financial_year ?></option>
		            <?php endforeach ?>
		        </select>
		    </div>
		</div>
	<?php endif ?>
	<?php if($page !== 'area' && $page !== 'surname' && $page !== 'collection'): ?>
		<div class="col-md-3">
		    <div class="form-group">
		        <label for="surname_search">Select Surname</label>
		        <select id="surname_search" class="form-control">
		        	<option value="">Select</option>
		            <?php foreach($surnames as $row): ?>
		              <option value="<?php echo $row->id ?>"><?php echo $row->surname ?></option>
		            <?php endforeach ?>
		        </select>
		    </div>
		</div>
		<?php if($this->ion_auth->is_admin()): ?>
			<div class="col-md-3">
				<div class="form-group">
					<label for="zone_search">Select Zone</label>
					<select id="zone_search" class="form-control">
						<option value="">Select</option>
						<?php foreach($zones as $row): ?>
						<option value="<?php echo $row->id ?>"><?php echo $row->zone_name ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		<?php endif ?>
		<div class="col-md-3">
		    <div class="form-group mt-md-4">
		        <input type="submit" class="btn btn-info" id="multiple_search" value="Submit">
		    </div>
		</div>
	<?php endif ?>
	<?php if($page == 'collection'): ?>
		<div class="col-md-3">
			<div class="form-group">
				<label for="zone_search">Select Zone</label>
				<select id="zone_search" class="form-control">
					<option value="">Select</option>
					<?php foreach($zones as $row): ?>
					<option value="<?php echo $row->id ?>"><?php echo $row->zone_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	<?php endif ?>
</div>