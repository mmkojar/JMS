<?php $this->load->view('templates/header') ?>
            
            <div class="col-12">
	            <div class="card">
	            	<?php $this->load->view('templates/header_title'); ?>
	                <div class="card-body">	                	
	                	<?php $this->load->view('templates/filter'); ?>
	                    <div class="table-responsive">   
	                        <table id="display_outstanding_reports" class="table table-striped table-bordered" style="width:100%">
	                        	<thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Zone</th>
                                        <th>Amount To Collect</th>
                                        <th>Receipt Amount</th>
                                        <th>Adjustment Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Balance Count</th>
                                        <th>Year</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
	                        </table>
	                    </div>

	                </div>
	            </div>
            </div>
            
    
<?php $this->load->view('templates/footer') ?>
<script type="text/javascript">
	$(document).ready(function() {

		var d = new Date();
        var current_year = d.getFullYear();

        $("#year_search").on('change',function() {

        	$("#surname_search").val('');
			$("#zone_search").val('');

            var y = $(this).val();
            $('#display_outstanding_reports').DataTable().destroy();
           	load_outstandings(y);            
        });

        load_outstandings(current_year);
        function load_outstandings(current_year,filter_surname='',filter_zone='') {
        	$('#display_outstanding_reports').DataTable({
        		"searchable":    true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				// Load data for the table's content from an Ajax source
				 "ajax": {
					url: "<?php echo base_url();?>reports/outstanding_api/"+current_year,
					type: "POST",
					data : {
						filter_surname:filter_surname,
						filter_zone:filter_zone
					}
				},
			    "pagingType": "full_numbers",
			    lengthMenu: [[10,25,50,100, -1], [10,25,50,100, "All"]],
				columnDefs: [
					{ responsivePriority: 2, targets: 0 },
					{ responsivePriority: 2, targets: -1 }
				],
			    responsive: true,
			    language: {
					search: "_INPUT_",
					searchPlaceholder: "Search records",
			    },
				"dom": 'lBfrtip',
				"buttons": [
					{
						extend: 'collection',
						text: '<span></span> Export',
						buttons: [
							'excel',
						]
					}
				],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			      	if (aData[10] == "inactive") {
			        	$('td', nRow).addClass('bg-danger text-white');
			      	}
			    }
			});
			$("#datatables_filter label input").addClass("form-control input-sm");
			$("#datatables_length label select").addClass("form-control input-sm");
			$(".dt-buttons a").removeClass("dt-button buttons-collection");
			$(".dt-buttons a").addClass("btn btn-info btn-fill btn-wd");
			$(".dt-buttons").css("left","10px");
        }

		$('#multiple_search').on('click',function() {
			var surname_search = $("#surname_search").val();
			var zone_search = $("#zone_search").val();

			if(surname_search !== '' || zone_search !== '') {				
				$('#display_outstanding_reports').DataTable().destroy();
				load_outstandings($("#year_search").val(),surname_search,zone_search);
			}
			else {
				toastr.error('Select Filter');
				$('#display_outstanding_reports').DataTable().destroy();
				load_outstandings($("#year_search").val());
			}
		});
		
	});
</script>