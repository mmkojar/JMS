<?php $this->load->view('templates/header'); ?>

            <div class="col-12">
	            <div class="card">	            	
	            	<div class="card-header">
	            		<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
	            		<a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('payment/add') ?>">Add Payment</a>
	            	</div>
	                <div class="card-body">
					    <?php $this->load->view('templates/filter'); ?>
	                    <div class="table-responsive">
	                        <table id="payments_table" class="table table-striped table-bordered" style="width:100%">
	                        	<thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Name</th>
                                        <th>Phone No.</th>
                                        <th>Zone</th>
                                        <th>Receipt No.</th>
                                        <th>Receipt Date</th>
                                        <th>Receipt Amount</th>
                                        <th>Adjustment Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Receiver Name</th>
                                        <th>Year</th>
                                        <th>Remark</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
	                        </table>
	                    </div>

	                    <!-- Receipt Modal -->
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
		                <!-- Edit Modal -->
		                <div class="modal fade" id="edit-payment-modal">
		                  <div class="modal-dialog" role="document">
		                      <div class="modal-content">
		                          <div class="modal-header">
		                              <h5 class="modal-title" id="exampleModalLabel">Edit Payment</h5>
		                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                                  <span aria-hidden="true">×</span>
		                              </button>
		                          </div>
		                          <form action="" accept="" role="form" method="post" id="update_payment_form">
		                                <div class="modal-body">
											<div class="form-group">
												<label>Receipt No.</label>
												<input type="text" class="form-control" name="receipt_no" id="receipt_no">
											</div>
											<div class="form-group">
												<label>Receipt Date</label>
												<input type="text" class="form-control" name="receipt_date" id="receipt_date">
											</div>
											<div class="form-group">
												<label>Receipt Amount</label>
												<input type="text" class="form-control" name="receipt_amt" id="receipt_amt">
											</div>
											<!-- <div class="form-group">
												<label>Balance Amount</label>
												<input type="text" class="form-control" name="balance_amt" id="balance_amt">
											</div> -->
											<input type="checkbox" name="adjustment_check" id="adjustment_check">
							                <span>Click here For Adjustment Amount</span><br>
							                <div class="form-group mt-2" id="show_on_check">
							                  <input type="text" name="adjustment_amt" id="adjustment_amt" class="form-control" placeholder="Enter Adjustment Amount">
							                </div>
											<div class="form-group mt-2">
												<label>Receiver Name</label>
												<input type="text" class="form-control" name="receiver_name" id="receiver_name">
											</div>
											<div class="form-group">
												<label>Remark</label>
												<input type="text" class="form-control" name="remark" id="remark">
											</div>
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
            
    
<?php $this->load->view('templates/footer') ?>
<script type="text/javascript">
	$(document).ready(function() {

		var d = new Date();
        var current_year = d.getFullYear();

        $("#year_search").on('change',function() {

        	$("#surname_search").val('');
			$("#zone_search").val('');

            var y = $(this).val();
            $('#payments_table').DataTable().destroy();
           	load_receipts(y);
        });

        load_receipts(current_year);
        function load_receipts(current_year,filter_surname='',filter_zone='') {
        	$('#payments_table').DataTable({
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
			      	if (aData[13] == "inactive") {
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
				$('#payments_table').DataTable().destroy();
				load_receipts($("#year_search").val(),surname_search,zone_search);
			}
			else {
				toastr.error('Select Filter');
				$('#payments_table').DataTable().destroy();
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

        $(document).on('click', '.edit_payment', function() {
            
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
                	if(res.adjustment_amt !== '' || res.adjustment_amt == 0) {
			        	$("#adjustment_check").attr('checked',true);
			        	$("#show_on_check").show();
			        }
			        else {
			        	$("#show_on_check").hide();
			        	$("#adjustment_check").attr('checked',false);
			        }
                    // res.receipt_date = moment(res.receipt_date).format("DD-MMM-YYYY");
                    $("#edit-payment-modal").modal('show');
                    $("#edit-payment-modal #receipt_no").val(res.receipt_no);
                    $("#edit-payment-modal #receipt_date").val(res.receipt_date);
                    $("#edit-payment-modal #receipt_amt").val(res.receipt_amt);
                    $("#edit-payment-modal #adjustment_amt").val(res.adjustment_amt);
                    // $("#edit-payment-modal #balance_amt").val(res.balance_amt);
                    $("#edit-payment-modal #receiver_name").val(res.receiver_name);
                    $("#edit-payment-modal #remark").val(res.remark);
                    $("#edit-payment-modal #hidden_id").val(id);
                    $("#edit-payment-modal #year").val(year);
                    $("#edit-payment-modal #zone_id").val(res.zone_id);
                    $("#preloader").hide();
                }
            });
        })

        $(document).on('submit', '#update_payment_form', function(e) {

            e.preventDefault();                    
            var dataSerialize = new FormData($('#update_payment_form')[0]);
            var year = $("#edit-payment-modal #year").val();

            $.ajax({
                url:"<?php echo base_url('payment/update') ?>/1",
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
                        $("#edit-payment-modal").modal('hide');
                        toastr.success(res.msg);
                        $('#payments_table').DataTable().destroy();
                        load_receipts($("#year_search").val());     
                        $("#preloader").hide();                           
                    }                        
                }
            });
        });

	});
</script>