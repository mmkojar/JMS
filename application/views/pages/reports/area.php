<?php $this->load->view('templates/header') ?>
            
            <div class="col-12">
	            <div class="card">
	            	<?php $this->load->view('templates/header_title'); ?>
	                <div class="card-body">

						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#year_wise" role="tab"><span
										class="hidden-sm-up"></span> <span class="hidden-xs-down">Year Wise</span></a> </li>
							<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#zone_wise" role="tab"><span
										class="hidden-sm-up"></span> <span class="hidden-xs-down">Zone Wise</span></a> </li>
						</ul>

						<div class="tab-content tabcontent-border mt-4">
							<div class="tab-pane active" id="year_wise" role="tabpanel">
								<div class="p-0">
									<?php $this->load->view('templates/filter'); ?>
									<div class="table-responsive">   
										<table id="display_area_wise_reports" class="table table-striped table-bordered">
										</table>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="zone_wise" role="tabpanel">
								<div class="p-0">
									<div class="col-md-3">
										<div class="form-group">
											<label for="area_wise_zone_search">Select Zone</label>
											<?php if($this->ion_auth->is_admin()): ?>
												<select id="area_wise_zone_search" class="form-control">
													<option value="">Select</option>
													<?php foreach($zones as $row): ?>
													<option value="<?php echo $row->id ?>"><?php echo $row->zone_name ?></option>
													<?php endforeach ?>
												</select>
												<?php else: ?>
													<select id="area_wise_zone_search" class="form-control">
														<option value="<?php $this->ion_auth->user()->row()->zone_id ?>">
															<?php echo $this->ion_auth->user()->row()->zone_name ?>
														</option>
													</select>
											<?php endif ?>
										</div>
									</div>
									<div class="table-responsive">   
										<table id="display_area_zone_wise_reports" class="table table-striped table-bordered" width="100%">
										</table>
									</div>
								</div>
							</div>
						</div>
	                						
	                </div>
	            </div>
            </div>
            
    
<?php $this->load->view('templates/footer') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {

	$("#area_wise_zone_search").on('change', function () {

		var id = $(this).val();		
		$('#display_area_zone_wise_reports').DataTable().destroy();	
        load_data(id);
	});

    load_data(1);
	function load_data(id) {
		
		if(id == "") {
			toastr.error('Select zone');
		}
		else {
			$.ajax({
				url:  "<?php echo base_url('reports/area_surname_wise_reports_api') ?>/"+id+'/'+'a',
				method: 'GET',
				dataType: "json",
				beforeSend: function () {
					$("#preloader").show();
				},
				success: function (res) {
					
					/* const result = Object.assign([], res.data).reduce((r, { zone_id, zone_name, ...o }) => {
						// console.log("reduce:",o);
						Object
						.entries(o)
						.forEach(([k, v]) => {
							r[k] = (r[k] || 0) + (+v || 0)
						});
						return r;
					}, {}); */
					
					// result.zone_name = res.data[0].zone_name;
					// result.year = res.data['year'];

					var prefix = 'fy',
					keys = ['receipt_amt', 'balance_amt', 'amt_to_collect' ,'balance_count_amt', 'adjustment_amt'],
					result = {};

					for (let i = 0; i in res.data; i++) {
						res.data.year.forEach(year => {
							const 
								totalCount = [prefix, year, 'total_count'].join('_'),
								k = [prefix, year, 'amt_to_collect'].join('_');

							result[totalCount] ??= 0;
							if (+res.data[i][k] > 0) result[totalCount]++;

							keys.forEach(key => {
								const k = [prefix, year, key].join('_');
								result[k] = (result[k] || 0) + (+res.data[i][k] || 0);
							});
						});
					}
					result.zone_name = res.data[0].zone_name;
					result.year = res.data['year'];

					var html = "";
					var sr_no = 1;
					html += `
							<thead>
								<tr>
									<th>Sr.No</th>
									<th>Zone Name</th>
									<th>Year</th>
									<th>Total Joda</th>
									<th>Remaining Joda Count</th>
									<th>Paid Joda Count</th>
									<th>Amount Paid</th>
									<th>Adjustment Amount</th>
									<th>Remaining Balance</th>
									<th>Total Amount</th>
								</tr>
							</thead>
							<tbody>
							`;
					for (var i in result.year) {
						var yearget = Number(result.year[i]);
						var recptamt = result['fy_' + yearget + '_receipt_amt'];
						var balamt = result['fy_' + yearget + '_balance_amt'];
						var balcntamt = result['fy_' + yearget + '_balance_count_amt'];
						var adjamt = result['fy_' + yearget + '_adjustment_amt'];
						var total_joda = result['fy_' + yearget + '_total_count'];

						html += `
								<tr>
									<td>${sr_no}</td>
									<td>${result.zone_name}</td>
									<td>${yearget}</td>
									<td>${total_joda}</td>
									<td>${balcntamt}</td>
									<td>${total_joda - balcntamt}</td>
									<td>${recptamt}</td>
									<td>${adjamt}</td>
									<td>${balamt}</td>
									<td>${recptamt + adjamt + balamt}</td>
								</tr>
								`;
						sr_no++;
					}
					html += `</tbody>`;
					$("#display_area_zone_wise_reports").html(html);
					$('#display_area_zone_wise_reports').DataTable({
						responsive: true,		
						rowGroup: {
							dataSrc: [1]
						},
						columnDefs: [{
							targets: [1],
							visible: false
						}],				
						dom: 'lBfrtip',
						buttons: [{
							extend: 'collection',
							text: '<span></span> Export',
							buttons: [
								'excel',
							]
						}]
					});
					$("#preloader").hide();
				}
			});
		}

	}

});

</script>
