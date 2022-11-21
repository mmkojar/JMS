<?php $this->load->view('templates/header'); ?>                     

    <div class="col-12">
        <div class="card">
        	<?php $this->load->view('templates/header_title'); ?>
            <div class="card-body">            	
            	<?php $this->load->view('templates/filter'); ?>
            	<div class="table-responsive">
                    <table id="display_invoices" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                            	<th>Sr.No</th>
                                <th>Name</th>
                                <th>Zone</th>
                                <th>Amount To Collect</th>
                                <th>Receipt No</th>
                                <th>Receipt Date</th>
                                <th>Receipt Amount</th>
                                <th>Balance Amount</th>
                                <th>Year</th>
                                <th>Remark</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
				</div>
				<!-- Edit Modal -->
                <div class="modal fade" id="edit-invoice-modal">
					<div class="modal-dialog" role="document">
					  	<div class="modal-content">
					      	<div class="modal-header">
					          	<h5 class="modal-title" id="exampleModalLabel">Edit Invoice</h5>
					          	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					              	<span aria-hidden="true">×</span>
					          	</button>
					      	</div>
					      	<form action="" accept="" role="form" method="post" id="update_invoice_form">
					            <div class="modal-body">
									<div class="text-danger">
										<span>Note:</span>
										<p>1) To make outstanding amount as zero of any members enter zero value in this three fields</p>
										<span>નોંધ: </span>
										<p>કોઈપણ સભ્યોની શૂન્ય તરીકે બાકી રકમ બનાવવા માટે આ ત્રણ ક્ષેત્રોમાં શૂન્ય મૂલ્ય દાખલ કરો </p>
									</div>
					            	<div class="form-group">
										<label>Amount To Collect</label>
										<input type="text" class="form-control" name="amt_to_collect" id="amt_to_collect">
									</div>
									<!-- <div class="form-group">
										<label>Receipt Amount</label>
										<input type="text" class="form-control" name="receipt_amt" id="receipt_amt">
									</div> -->
									<div class="form-group">
										<label>Balance Amount</label>
										<input type="text" class="form-control" name="balance_amt" id="balance_amt">
									</div>												
									<div class="form-group">
										<label>Balance Count</label>
										<input type="text" class="form-control" name="balance_count_amt" id="balance_count_amt">
									</div>
									<!-- <div class="form-group">
										<label>Remark</label>
										<input type="text" class="form-control" name="remark" id="remark">
									</div> -->
					            </div>
					            <div class="modal-footer">
					              <input type="hidden" name="hidden_id" id="hidden_id">
					              <input type="hidden" name="year" id="year">
					              <input type="hidden" name="zone_id" id="zone_id">
					              <input type="submit" class="btn btn-success btn-round pull-left" id="insert" value="Submit">
					            </div>
					      	</form>
					  	</div>
					</div>
              	</div>
            </div>
        </div>
    </div>
    

<?php $this->load->view('templates/footer'); ?>
<script type="text/javascript">
	$(document).ready(function() {

		var d = new Date();
        var current_year = d.getFullYear();

        $("#year_search").on('change',function() {

        	$("#surname_search").val('');
			$("#zone_search").val('');

            var y = $(this).val();
            $('#display_invoices').DataTable().destroy();
           	load_invoices(y);            
        });

        load_invoices(current_year);
        function load_invoices(current_year,filter_surname='',filter_zone='') {
        	$('#display_invoices').DataTable({
        		"searchable":    true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				// Load data for the table's content from an Ajax source
				 "ajax": {
					url: "<?php echo base_url();?>payment/invoices_api/"+current_year,
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
			      	if (aData[11] == "inactive") {
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
				$('#display_invoices').DataTable().destroy();
				load_invoices($("#year_search").val(),surname_search,zone_search);
			}
			else {
				toastr.error('Select Filter');
				$('#display_invoices').DataTable().destroy();
				load_invoices($("#year_search").val());
			}
		});

        $(document).on('click', '.edit_invoices', function() {
            
            var id = $(this).attr('id');
            var year = $(this).attr('year');

            $.ajax({
                url:"<?php echo base_url('payment/invoices_api_by_id') ?>/"+year+'/'+id,
                method: 'GET',
                dataType: "json",
                beforeSend: function() {
                  $("#preloader").show();
                },
                success: function(res) {
                    console.log(res);
                    // res.receipt_date = moment(res.receipt_date).format("DD-MMM-YYYY");
                    $("#edit-invoice-modal").modal('show');
                    $("#edit-invoice-modal #amt_to_collect").val(res.amt_to_collect);
                    // $("#edit-invoice-modal #receipt_amt").val(res.receipt_amt);
                    $("#edit-invoice-modal #balance_amt").val(res.balance_amt);
                    $("#edit-invoice-modal #balance_count_amt").val(res.balance_count_amt);
                    // $("#edit-invoice-modal #remark").val(res.remark);
                    $("#edit-invoice-modal #hidden_id").val(id);
                    $("#edit-invoice-modal #year").val(year);
                    $("#edit-invoice-modal #zone_id").val(res.zone_id);
                    $("#preloader").hide();
                }
            });
        })

        $(document).on('submit', '#update_invoice_form', function(e) {

            e.preventDefault();                    
            var dataSerialize = new FormData($('#update_invoice_form')[0]);
            var year = $("#edit-invoice-modal #year").val();
            
            $.ajax({
                url:"<?php echo base_url('payment/update') ?>/2",
                method: 'POST',
                data : dataSerialize,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                  $("#preloader").show();
                },
                success: function(res) {
                    if(res.status == "success") {
                        $("#edit-invoice-modal").modal('hide');
                        toastr.success(res.msg);
                        $('#display_invoices').DataTable().destroy();
                        load_invoices($("#year_search").val());
                        $("#preloader").hide();
                    }                        
                }
            });
        });
	});
</script>