<?php $this->load->view('templates/header') ?>
                       
            <div class="col-12">
	            <div class="card">
	            	<div class="card-header">
	                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
	                    <a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('members/add') ?>">Add Members</a>
	                  </div>
	                <div class="card-body">
	                	<?php $this->load->view('templates/filter'); ?>
	                    <div class="table-responsive">
		                    <table id="display_users" class="table table-striped table-bordered" style="width:100%">
		                        <thead>
		                            <tr>
		                            	<th>Sr.No</th>
		                                <th>User Name</th>
		                                <th>Zone Name</th>
		                                <th>Phone</th>
		                                <th>Email</th>
		                                <th>Joining Date</th>
		                                <th>Expiry Date</th>
										<th>Divorce Date</th>
		                                <th>Transfer Date</th>
		                                <th>Stats</th>
		                                <th>Action</th>		                                
		                            </tr>
		                        </thead>
		                    </table>
						</div>
	                </div>
	            </div>
	            <div class="modal fade" id="edit-user-modal">
	                <div class="modal-dialog" role="document">
	                    <div class="modal-content">
	                        <div class="modal-header">
	                            <h5 class="modal-title" id="exampleModalLabel">Edit Member</h5>
	                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                                <span aria-hidden="true">×</span>
	                            </button>
	                        </div>
	                        <form action="" accept="" role="form" method="post" id="update_user_form">
	                              <div class="modal-body">
	                                  <div class="box-body">
	                                  	<div class="form-group">
	                                      <label>First Name</label>
	                                      <input type="text" id="first_name" name="first_name" class="form-control">
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Father Name</label>
	                                      <input type="text" id="father_name" name="father_name" class="form-control">
	                                    </div>
										<div class="form-group">
	                                      <label>Last Name</label>
	                                      <select name="last_name" id="last_name" class="form-control">
											  	<option id="selected_surname"></option>
    					                       	<option value="">Select</option>
    					                        <?php foreach($surnames as $row): ?>
    					                          	<option value="<?php echo $row->id ?>"><?php echo $row->surname ?></option>
    					                        <?php endforeach ?>
    					                    </select>
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Email</label>
	                                      <input type="text" id="email" name="email" class="form-control">
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Phone No.</label>
	                                      <input type="text" id="phone" name="phone" class="form-control">
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Current Zone</label>
	                                      <p id="current_zone"></p>
	                                      <label>New Zone</label>
	                                      <select name="zone_id" id="zone_id" class="form-control">
    					                       	<option value="">Select</option>
    					                        <?php foreach($zones as $zone): ?>
    					                          	<option value="<?php echo $zone->id ?>"><?php echo $zone->zone_name ?></option>
    					                        <?php endforeach ?>
    					                    </select>
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Transfer Date</label>
	                                      <input type="date" id="transfer_date" name="transfer_date" class="form-control">
	                                    </div>
	                                    <div class="form-group">
	                                      <label>Change Status</label>
	                                      <select name="status" id="status" class="form-control check_user_status">
	                                      </select>
	                                    </div>
	                                    <div class="form-group show_on_inactive" style="display: none">
	                                      <label>Expiry Date</label>
	                                      <input type="date" id="expiry_date" name="expiry_date" class="form-control">
	                                    </div>
										<div class="form-group show_on_divorce" style="display: none">
	                                      <label>Divorce Date</label>
	                                      <input type="date" id="divorce_date" name="divorce_date" class="form-control">
	                                    </div>
	                                  </div>
	                              </div>
	                              <div class="modal-footer">	                              	
	                                <!-- <input type="hidden" name="father_name" id="father_name"> -->
	                                <input type="hidden" name="surname" id="surname">
	                                <input type="hidden" name="hidden_user_id" id="hidden_user_id">
	                                <input type="hidden" name="old_zone_id" id="old_zone_id">
	                                <input type="hidden" name="current_zone_id" id="current_zone_id">
	                                <input type="submit" class="btn btn-success btn-round pull-left" id="insert" value="Submit">
	                              </div>
	                        </form>
	                    </div>
	                </div>
	            </div>
            </div>
            
    
<?php $this->load->view('templates/footer') ?>
<script type="text/javascript">
	$(document).ready(function() {

	    load_users();
	    function load_users(filter_surname='',filter_zone='') {
	    	$('#display_users').DataTable({
	    		"searchable":    true,
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				// Load data for the table's content from an Ajax source
				 "ajax": {
					url: "<?php echo base_url();?>members/members_api/",
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
			      	if (aData[9] == "inactive") {
			        	$('td', nRow).addClass('bg-danger text-white');
			      	}
					else if (aData[9] == "divorce") {
						$('td', nRow).addClass('bg-warning text-dark font-weight-bold');
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
				$('#display_users').DataTable().destroy();
				load_users(surname_search,zone_search);
			}
			else {
				toastr.error('Select Filter');
				$('#display_users').DataTable().destroy();
				load_users();
			}
		});      

		$(document).on('click', '.edit_user', function() {
            var id = $(this).attr("id");

            $.ajax({                
                url:"<?php echo base_url('auth/ger_user_api') ?>/"+id,
                method: 'GET',
                dataType: "json",
                success: function(res) {
                	if(res.status == 'inactive') {
                		$(".show_on_inactive").show();
                		$("#edit-user-modal #expiry_date").val(res.expiry_date);
                	}
					else if(res.status == 'divorce') {
                		$(".show_on_divorce").show();
                		$("#edit-user-modal #divorce_date").val(res.divorce_date);
                	}
					else {
                	    $(".show_on_inactive").hide();
						$(".show_on_divorce").hide();
                	    $("#edit-user-modal #expiry_date").val('');
                		$("#edit-user-modal #divorce_date").val('');
                	}
                    var html = `
                        <option ${res.status == 'active' ? 'selected' : ''} value="active">Active</option>
                        <option ${res.status == 'inactive' ? 'selected' : ''} value="inactive">InActive (Member Death/Wife Death)</option>
                        <option ${res.status == 'divorce' ? 'selected' : ''} value="divorce">Divorce</option>
                    `;
                    $("#edit-user-modal").modal('show');
                    $("#hidden_user_id").val(id);					
                    $("#edit-user-modal #first_name").val(res.first_name);
                    $("#edit-user-modal #father_name").val(res.father_name);
                    $("#edit-user-modal #surname").val(res.surname);
					$("#selected_surname").val(res.last_name);
					$("#selected_surname").text(res.surname);
                    $("#edit-user-modal #email").val(res.email);
                    $("#edit-user-modal #phone").val(res.phone);
                    $("#edit-user-modal #current_zone_id").val(res.zone_id);
                    $("#edit-user-modal #old_zone_id").val(res.old_zone_id);
                    $("#edit-user-modal #status").html(html);
                    $("#edit-user-modal #current_zone").text(res.zone_name);
                }
            });
        });

        $(document).on('submit', '#update_user_form', function(e) {

            e.preventDefault();
            var dataSerialize = new FormData($('#update_user_form')[0]);

            if($("#zone_id").val() !== '' && $("#transfer_date").val() == '') {
                toastr.error('Transfer Date is Required')
            }
            else if($("#status").val() == 'inactive' && $("#expiry_date").val() == '') {
                toastr.error('Exipry Date is Required')
            }
            else {
                $.ajax({
                    url:"<?php echo base_url('auth/update_user_api') ?>",
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
                        	$("#zone_id").val('');
                        	$("#preloader").hide();
                            $("#edit-user-modal").modal('hide');
                            toastr.success(res.msg);
                            $('#display_users').DataTable().destroy();
                            load_users();                                
                        }                        
                    }
                });
            }

            
        });

        $(document).on('change', '.check_user_status', function() {
            if($(this).val() == 'inactive') {
                $(".show_on_inactive").show();
				$(".show_on_divorce").hide();
            }
			else if($(this).val() == 'divorce') {
				$(".show_on_inactive").hide();
				$(".show_on_divorce").show();
			}
            else {
                $(".show_on_inactive").hide();
				$(".show_on_divorce").hide();
            }
        })
	});
</script>