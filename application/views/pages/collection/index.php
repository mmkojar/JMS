<?php $this->load->view('templates/header'); ?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
			<!-- <?php //if($this->ion_auth->in_group("supervisor")): ?> -->
				<a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('collection/add') ?>">Add
				Collection</a>
			<!-- <?php //endif ?> -->
		</div>
		<div class="card-body">
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#total" role="tab"><span
							class="hidden-sm-up"></span> <span class="hidden-xs-down">Total Entries</span></a> </li>
				<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#single" role="tab"><span
							class="hidden-sm-up"></span> <span class="hidden-xs-down">Date Wise Entry</span></a> </li>
			</ul>
			<div class="tab-content tabcontent-border mt-4">
				<div class="tab-pane active" id="total" role="tabpanel">
					<div class="table-responsive p-0">   
						<table id="display_acollection" class="table table-striped table-bordered" style="width:100%">
						</table>
					</div>
				</div>
				<div class="tab-pane" id="single" role="tabpanel">
					<div class="table-responsive p-0">
						<table id="display_dacollection" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>Sr.No</th>
									<th>Year</th>
									<th>Zone</th>
									<th>Collector Name</th>
									<th>Amount Collected</th>
									<th>Date</th>
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
								<td><?php echo $row->amt_collected ? $row->amt_collected : '-' ?></td>
								<td><?php echo date('M j Y', strtotime($row->date)); ?></td>
								<td><?php echo $row->remark ?></td>
								<td>
									<?php //echo anchor("collection/edit/".$row->id, '<i class="mdi mdi-pencil"></i>',array('class'=>'btn btn-success btn-sm')) ;?>
									<a class="btn btn-danger btn-sm delete_items text-white" url="collection/delete"
										tname="collector_collection" id="<?php echo $row->id ?>"><i
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
	</div>
</div>


<?php $this->load->view('templates/footer') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript">

	$(document).ready(function() {
		$('#display_dacollection').DataTable({
            responsive: true,
            rowGroup: {
                dataSrc: [1]
            },
            columnDefs: [{
                targets: [1],
                visible: false
            }],
            order: [[1, 'desc']],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'collection',
                text: '<span></span> Export',
                buttons: [
                    'excel',
                ]
            }]
        });
		$.ajax({
			url:  "<?php echo base_url('collection/get_collectors_total_collection') ?>",
			method: 'GET',
			dataType: "json",
			beforeSend: function () {
				$("#preloader").show();
			},
			success: function (res) {
				var total_response = [];
				// console.log(res);
				for (var i in res) {					
					res[i].amt_collected = Number(res[i].amt_collected);
					res[i].amt_received_by_admin = Number(res[i].amt_received_by_admin);
					total_response[i] = res[i];
				}			
				const result =  total_response.reduce((a2, c2) => {
					let filteredP = a2.filter(el => el.zone === c2.zone && el.year === c2.year)
					if (filteredP.length > 0) {
						a2[a2.indexOf(filteredP[0])].amt_collected += +c2.amt_collected;
					} else {
						a2.push(c2);
					}
					return a2;
				}, []);

				var html = "";
				var sr_no = 1;
				html += `
						<thead>
							<tr>
								<th>Sr.No</th>
								<th>Year</th>
								<th>Zone</th>
								<th>Collector Name</th>
								<th>Amount Collected</th>
								<th>Amount Collected By Admin</th>
								<th>Amount Left</th>
							</tr>
						</thead>
						<tbody>
						`;
				for (var i in result) {

					html += `
							<tr>
								<td>${sr_no}</td>
								<td>${result[i].year}</td>
								<td>${result[i].zone_name}</td>
								<td>${result[i].collector_name}</td>
								<td>${result[i].amt_collected}</td>
								<td>${result[i].amt_received_by_admin}</td>
								<td>${result[i].amt_collected - result[i].amt_received_by_admin}</td>
							</tr>
							`;
					sr_no++;
				}
				html += `</tbody>`;
				$("#display_acollection").html(html);
				$('#display_acollection').DataTable({
					responsive: true,
					rowGroup: {
						dataSrc: [1]
					},
					columnDefs: [{
						targets: [1],
						visible: false
					}],
					order: [[1, 'desc']],
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
	});
</script>