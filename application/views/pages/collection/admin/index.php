<?php $this->load->view('templates/header'); ?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
			<a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('collection/addadmin') ?>">Add
				Collection</a>
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
						<table id="display_ccollection" class="table table-striped table-bordered" style="width:100%">
						</table>
					</div>
				</div>
				<div class="tab-pane" id="single" role="tabpanel">
					<div class="table-responsive p-0">
						<table id="display_dccollection" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>Sr.No</th>
									<th>Year</th>
									<th>Zone</th>
									<th>Collector Name</th>
									<th>Amount Received</th>
									<th>Date</th>
									<th>Remark</th>
									<!-- <th>Action</th> -->
								</tr>
							</thead>
							<?php $sr_no = 1; ?>
							<?php foreach($acollect as $row): ?>
							<tr>
								<td><?php echo $sr_no ?></td>
								<td><?php echo $row->year ?></td>
								<td><?php echo $row->zone_name ?></td>
								<td><?php echo $row->collector_name; ?></td>
								<td><?php echo $row->amt_received ? $row->amt_received : '-' ?></td>
								<td><?php echo date('M j Y', strtotime($row->date)); ?></td>
								<td><?php echo $row->remark ?></td>
								<!-- <td>
									<?php //echo anchor("collection/edit/".$row->id, '<i class="mdi mdi-pencil"></i>',array('class'=>'btn btn-success btn-sm')) ;?>
									<a class="btn btn-danger btn-sm delete_items text-white" url="collection/delete"
										tname="admin_collection" id="<?php// echo $row->id ?>"><i
											class="mdi mdi-delete"></i></a>
								</td> -->
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
        $('#display_dccollection').DataTable({
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
			url:  "<?php echo base_url('collection/get_admin_total_collection') ?>",
			method: 'GET',
			dataType: "json",
			beforeSend: function () {
				$("#preloader").show();
			},
			success: function (res) {
				var total_response = [];
				for (var i in res) {					
                    res[i].amt_received = Number(res[i].amt_received);
					total_response[i] = res[i];
				}
				const result =  total_response.reduce((a2, c2) => {
					let filteredP = a2.filter(el => el.zone === c2.zone && el.year === c2.year)
					if (filteredP.length > 0) {
						a2[a2.indexOf(filteredP[0])].amt_received += +c2.amt_received;
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
								<th>Amount Received</th>
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
								<td>${result[i].amt_received}</td>
							</tr>
							`;
					sr_no++;
				}
				html += `
                </tbody>`;
				$("#display_ccollection").html(html);
				$('#display_ccollection').DataTable({
					responsive: true,
                    // rowGroup: {
                    //     dataSrc: [1]
                    // },
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
					}],
					"drawCallback": function ( settings ) {
						var api = this.api();
						var rows = api.rows( {page:'current'} ).nodes();
						var last=null;
						var subTotal = new Array();
						var groupID = -1;
						var aData = new Array();
						var index = 0;
						
						api.column(1, {page:'current'} ).data().each( function ( group, i ) {
							
						// console.log(group+">>>"+i);
						
						var vals = api.row(api.row($(rows).eq(i)).index()).data();
						var amt_received = vals[4] ? parseFloat(vals[4]) : 0;
						
						if (typeof aData[group] == 'undefined') {
							aData[group] = new Array();
							aData[group].rows = [];
							aData[group].amt_received = [];
						}
					
							aData[group].rows.push(i); 
								aData[group].amt_received.push(amt_received); 
							
						} );
				
						var idx= 0;			
						for(var office in aData){				
							idx =  Math.max.apply(Math,aData[office].rows);
				
							var sum = 0; 
							$.each(aData[office].amt_received,function(k,v){
									sum = sum + v;
							});
							console.log(aData[office].amt_received);
							$(rows).eq( idx ).after(
								'<tr style="background-color: #e0e0e0;"><td colspan="3"><b>'+office+'</b></td>'+
								'<td><b>'+sum+'</b></td></tr>'
							);								
						};

					}
				});
				$("#preloader").hide();
			}
		});
	});
</script>