<?php $this->load->view('templates/header') ?>

<div class="col-12">
	<div class="card">
		<?php $this->load->view('templates/header_title'); ?>
		<div class="card-body">
			<?php if($this->ion_auth->is_admin()): ?>
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#all" role="tab"><span
							class="hidden-sm-up"></span> <span class="hidden-xs-down">All</span></a> </li>
				<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#date_wise" role="tab"><span
							class="hidden-sm-up"></span> <span class="hidden-xs-down">Date Wise</span></a> </li>
			</ul>
			<div class="tab-content tabcontent-border mt-4">
				<div class="tab-pane active" id="all" role="tabpanel">
					<div class="p-0">
						<?php $this->load->view('templates/filter'); ?>
						<div class="table-responsive">
							<table id="display_collection_reports" class="table table-striped table-bordered"
								style="width:100%">

							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="date_wise" role="tabpanel">
					<div class="p-0">
						<div class="table-responsive">
							<table id="display_list_wise_collection" class="table table-striped table-bordered"
								style="width:100%">

							</table>
						</div>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="table-responsive">
				<table id="display_list_wise_collection" class="table table-striped table-bordered" style="width:100%">
				</table>
			</div>
			<?php endif ?>
		</div>
	</div>
</div>


<?php $this->load->view('templates/footer') ?>
<script type="text/javascript">
	$(document).ready(function () {

		$("#zone_search").on('change', function () {

			var y = $(this).val();
			if (y !== '') {
				$('#display_collection_reports').DataTable().destroy();
				collection_reports(y);
			} else {
				$('#display_collection_reports').DataTable().destroy();
				collection_reports();
			}
		});

		// All Collection Reports
		collection_reports();

		function collection_reports(y = "") {
			var url = y ? `<?php echo base_url('reports/collection_reports_api') ?>/${y}` :
				`<?php echo base_url('reports/collection_reports_api') ?>`;
			$.ajax({
				url: url,
				method: 'GET',
				dataType: "json",
				beforeSend: function () {
					$("#preloader").show();
				},
				success: function (res) {
					var yearly_response = [];
					for (var i in res) {
						res[i].receipt_amt = Number(res[i].receipt_amt);
						yearly_response[i] = res[i];
					}
					var result = yearly_response.reduce((a2, c2) => {
						let filteredP = a2.filter(el => (el.year === c2.year && el.zone === c2
							.zone))
						if (filteredP.length > 0) {
							a2[a2.indexOf(filteredP[0])].receipt_amt += +c2.receipt_amt;
						} else {
							a2.push(c2);
						}
						return a2;
					}, []);
					const total_amount_collect = result.map(results => Number(results
							.total_zone_amount))
						.reduce((total, litres) => total + litres, 0);

					const total_receive_amount = result.map(results => Number(results.receipt_amt))
						.reduce((total, litres) => total + litres, 0);
					var html = "";
					var sr_no = 1;
					html += `
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Year</th>
                                    <th>Zone</th>
                                    <th>Amount To Collect</th>
                                    <th>Amount Received</th>
                                    <th>Balance Amount</th>                                        
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
                                <td>${result[i].total_zone_amount}</td>
                                <td>${result[i].receipt_amt}</td>
                                <td>${result[i].total_zone_amount - result[i].receipt_amt}</td>
                            </tr>
                        `;
						sr_no++;
					}
					html += `</tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td></td>
                                <td></td>
                                <td>Total: </td>
                                <td>${total_amount_collect}</td>
                                <td>${total_receive_amount}</td>
                                <td>${total_amount_collect - total_receive_amount}</td>
                            </tr>
                        </tfoot>
                    `;
					$("#display_collection_reports").html(html);
					$('#display_collection_reports').DataTable({
						dom: 'lBfrtip',
						buttons: [{
							extend: 'collection',
							text: '<span></span> Export',
							buttons: [
								'excel',
							]
						}],
					});
					$("#preloader").hide();
				}
			});
		}

		// List Wise Report
		list_wise_collection();

		function list_wise_collection() {
			$.ajax({
				url: `<?php echo base_url('reports/collection_list_wise_api') ?>`,
				method: 'GET',
				dataType: "json",
				beforeSend: function () {
					$("#preloader").show();
				},
				success: function (res) {
					console.log(res);
					if (res.length > 0) {
						var html = "";
						var sr_no = 1;
						html += `
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Year</th>
                                    ${res[0].type == "admin" ? `<th>Zone</th>` : ``}
                                    ${res[0].type == "admin" ? `<th>Collector Name</th>` : ``}
                                    ${res[0].type == "admin" ? `<th>Amount Received</th>` : `<th>Amount Paid To Admin</th>`}
                                    ${res[0].type == "admin" ? `<th>Receipt Date</th>` : `<th>Paid Date</th>`}
                                    ${res[0].type == "admin" ? `<th>Receiver Name</th>` : `<th>Received By</th>`}
                                    <th>Remark</th>
                                </tr>
                            </thead>
                        <tbody>
                    `;
						for (var i in res) {
							html += `
                            <tr>
                                <td>${sr_no}</td>
                                <td>${res[i].year}</td>
                                ${res[0].type == "admin" ? `<td>${res[i].zone_name}</td>` :  ``}
                                ${res[0].type == "admin" ? `<td>${res[i].collector_name}</td>` :  ``}
                                <td>${res[i].receipt_amt}</td>
                                <td>${res[i].receipt_date}</td>
                                <td>${res[i].receiver_name}</td>
                                <td>${res[i].remark}</td>
                            </tr>
                        `;
							sr_no++;
						}
						html += ` </tbody>
								<tfoot>
                                    <tr class="font-weight-bold">
                                    <td></td>
                                    ${res[0].type == "admin" ? `<td></td>` : `<td>Total:</td>`}
                                    ${res[0].type == "admin" ? `<td></td>` :  ``}
                                    ${res[0].type == "admin" ? `<td>Total:</td>` :  ``}
                                    <td></td>
                                    <td></td> 
                                    <td> </td> 
                                    <td></td>
                                    </tr> 
                            </tfoot>`;

						$("#display_list_wise_collection").html(html);
						$('#display_list_wise_collection').DataTable({
							"footerCallback": function (row, data, start, end, display) {
								var column = res[0].type == "admin" ? '4' : '2';
								var api = this.api(),
									data;

								// Remove the formatting to get integer data for summation
								var intVal = function (i) {
									return typeof i === 'string' ?
										i.replace(/[\$,]/g, '') * 1 :
										typeof i === 'number' ?
										i : 0;
								};

								// Total over all pages
								total = api
									.column(column)
									.data()
									.reduce(function (a, b) {
										return intVal(a) + intVal(b);
									}, 0);

								// Total over this page
								pageTotal = api
									.column(column, {
										page: 'current'
									})
									.data()
									.reduce(function (a, b) {
										return intVal(a) + intVal(b);
									}, 0);

								// Update footer
								$(api.column(column).footer()).html(
									'₹' + pageTotal + ' ( ₹' + total + ' total)'
								);
							},
							dom: 'lBfrtip',
							buttons: [{
								extend: 'collection',
								text: '<span></span> Export',
								buttons: [
									'excel',
								]
							}],
						});
						$("#preloader").hide();
					}
				}
			});
		}
	});

</script>
