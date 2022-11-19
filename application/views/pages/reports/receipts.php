<?php $this->load->view('templates/header') ?>
            
            <div class="col-12">
	            <div class="card">
	            	<?php $this->load->view('templates/header_title'); ?>
	                <div class="card-body">	                	
	                	<?php $this->load->view('templates/filter'); ?>
	                    <div class="table-responsive">   
	                        <table id="display_receipts_reports" class="table table-striped table-bordered" style="width:100%">
	                        	<thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Zone</th>
                                        <th>Receipt No.</th>
                                        <th>Receipt Date</th>
                                        <th>Receipt Amount</th>
                                        <th>Adjustment Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Receiver Name</th>
                                        <th>Year</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
	                        </table>
	                    </div>

	                       <!-- Modal -->
	                    <div class="modal fade" id="receipt-modal">
		                  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		                      <div class="modal-content">
			                        <div class="modal-header">
			                              <h5 class="modal-title" id="exampleModalLabel">Joda Receipt</h5>
			                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                                  <span aria-hidden="true">×</span>
			                              </button>
			                        </div>
		                          	<div class="modal-body">
	                                    <div class="card card-body mb-0">
	                                    		<div class="col-md-12">
						                            <h3 class="float-left"><b>INVOICE</b> <span id="invoice_no"></span></h3>
						                            <p class="float-right"><b>Invoice Date :</b> <i class="fa fa-calendar"></i> <span id="invoice_date"></span></p>
						                        </div>
					                            <hr>
				                                <div class="col-md-12">
				                                    <div class="table-responsive" style="clear: both;">
				                                        <table class="table table-striped table-bordered table-hover">
				                                            <thead>
				                                                <tr>
				                                                    <th class="text-center">Name</th>
				                                                    <th class="text-center">Zone</th>
				                                                    <th class="text-center">Amount</th>
				                                                    <th class="text-center">Receiver Name</th>
				                                                    <th class="text-center">Year</th>
				                                                </tr>
				                                            </thead>
				                                            <tbody id="display_single_receipt">          
				                                            </tbody>
				                                        </table>
				                                    </div>
					                            </div>
				                        </div>
			                        </div>
	                                </div>
	                                <div class="modal-footer">
	                                  
	                                </div>
		                      </div>
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
            $('#display_receipts_reports').DataTable().destroy();
           	load_receipts(y);
        });

        load_receipts(current_year);
        function load_receipts(current_year,filter_surname='',filter_zone='') {
        	$('#display_receipts_reports').DataTable({
        		"searchable":    true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				// Load data for the table's content from an Ajax source
				 "ajax": {
					url: "<?php echo base_url();?>payment/payments_api/"+current_year,
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
			      	if (aData[12] == "inactive") {
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
				$('#display_receipts_reports').DataTable().destroy();
				load_receipts($("#year_search").val(),surname_search,zone_search);
			}
			else {
				toastr.error('Select Filter');
				$('#display_receipts_reports').DataTable().destroy();
				load_receipts($("#year_search").val());
			}
		});

		$(document).on('click', '.view_receipt', function() {

            var id = $(this).attr('id');
            var year = $(this).attr('year');

            $.ajax({                
                url:"<?php echo base_url('payment/payments_api_by_id') ?>/"+year+'/'+id,
                method: 'GET',
                dataType: "json",
                beforeSend: function() {
                  $("#preloader").show();
                },
                success: function(res) {
                    $("#receipt-modal").modal('show');
                    $("#receipt-modal #invoice_no").text("#"+res.receipt_no);
                    $("#receipt-modal #invoice_date").text(res.receipt_date);
                    var html = `<tr>
                                    <td class="text-center">${res.username}</td>
                                    <td class="text-center">${res.zone_name}</td>
                                    <td class="text-center">${res.receipt_amt}</td>
                                    <td class="text-center">${res.receiver_name ? res.receiver_name : '-'}</td>
                                    <td class="text-center">${year}</td>
                                </tr>`;
                    $("#display_single_receipt").html(html);
                    $("#preloader").hide();
                }
            });
        })

	});
</script>