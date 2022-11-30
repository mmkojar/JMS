<?php $this->load->view('templates/header'); ?>
<style>
	.error {
		color: red;
	}

</style>
<div class="col-12">
	<div class="card">
		<?php $this->load->view('templates/header_title'); ?>
		<div class="card-body">
			<?php echo form_open(uri_string('collection/addadmin'));?>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Year</label>
						<select name="year" id="year" class="form-control" style="width: 100%;" required>
							<option value="">Select</option>
							<?php foreach($years as $row): ?>
								<option value="<?php echo $row->financial_year ?>"><?php echo $row->financial_year ?>
								</option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="zone">Select Zone</label>
						<select name="zone" id="zone" class="form-control" style="width: 100%;" required>
							<option value="">Select</option>
							<?php foreach($zones as $zone): ?>
							<option value="<?php echo $zone->id ?>"><?php echo $zone->zone_name ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Collector</label><br>
						<select name="collector_id" id="collector_id" class="form-control" style="width: 100%;"
							required>
						</select>
					</div>
				</div>
				<div class="col-md-6 mb-2">
					<div class="form-group">
						<label>Amount Received</label><br>
                        <span class="error" id="pendingamterr"></span>                       
						<input type="number" name="amt_received" id="amt_received" class="form-control" required>
						<input type="hidden" id="pending_amt">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Date</label>
						<input type="date" class="form-control" name="date" id="date" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Remark</label>
						<input type="text" class="form-control" name="remark" id="remark" required>
					</div>
				</div>
			</div>
			<?php echo form_hidden($csrf); ?>
			<?php echo form_submit('submit', 'Submit',array('class'=>'btn btn-success','id'=>'admin_coll_btn'));?>
			<?php echo form_close();?>
		</div>
	</div>
</div>
<?php $this->load->view('templates/footer') ?>
<script>
	$(document).ready(function () {

        $("#year").on('change', function () {
            $("#zone").val('');
        })
		$("#zone").on('change', function () {
			$("#preloader").show();
			var selected_id = $(this).val();
            var selected_text = $(this).find(":selected").text()
			if (selected_id) {
				$.ajax({
					url: "<?php echo base_url('collection/get_users_from_zone') ?>/" + selected_id,
					method: 'GET',
					dataType: 'json',
					success: function (res) {
						var html = '';
						if (res.length > 0) {
							for (var i in res) {
								html += '<option value="' + res[i].id +
									'">' + res[i].username + '</option>';
							}
						} else {
							html += '<option value="">No Collector</option>';
						}
						$("#preloader").hide();
						$("#collector_id").html(html);
					}
				});
                // GET PENDING AMOUNT
                $.ajax({
					url: "<?php echo base_url('collection/get_pending_amount_from_collector') ?>/" + selected_id + '/' + $("#year").val(),
					method: 'GET',
					dataType: 'json',
					success: function (resp) {
						console.log(resp);
                        if(resp.length > 0) {
                            for (var i in resp) {					
                                resp[i].amt_collected = Number(resp[i].amt_collected);
                                resp[i].amt_received_by_admin = Number(resp[i].amt_received_by_admin);
                            }
                            const result =  resp.reduce((a2, c2) => {
                                let filteredP = a2.filter(el => el.zone === c2.zone && el.year === c2.year)
                                if (filteredP.length > 0) {
                                    a2[a2.indexOf(filteredP[0])].amt_collected += +c2.amt_collected;
                                } else {
                                    a2.push(c2);
                                }
                                return a2;
                            }, []);
                            console.log(result);
                            const amtpending = resp[0].amt_collected - resp[0].amt_received_by_admin;
                            $("#pending_amt").val(amtpending);
                            $("#pendingamterr").text(amtpending + ' Rs left from '+selected_text+' (મદીના મંઝીલથી '+ amtpending +' Rs બાકી)')
                            if(amtpending == '0') {
                                $("#admin_coll_btn").hide();
                            }
                            // else if($("#amt_received").val() > amtpending) {
                            //     $("#pendingamterr").text('Receive Amount is greater than collected amount (પ્રાપ્ત રકમ એકત્રિત રકમ કરતાં વધુ છે)')
                            //     $("#admin_coll_btn").hide();
                            // }
                            else {
                                $("#admin_coll_btn").show();
                            }
                            // $("#admin_coll_btn").show();
                        }
                        else {
                            $("#admin_coll_btn").hide();                           
                            $("#pendingamterr").text('No Amount added of '+selected_text+' from collector (કલેક્ટર તરફથી '+selected_text+' કોઈ રકમ ઉમેરવામાં આવી નથી )');
                        }
					}
				});
			} else {
				$("#preloader").hide();
				$("#collector_id").html('<option value="">Select Zone</option>');
			}
            $("#amt_received").on('keyup', function () {
                if(Number($(this).val()) > Number($("#pending_amt").val())) {
                    $("#pendingamterr").text('Receive Amount '+$(this).val()+' is greater than collected amount '+$("#pending_amt").val()+' (પ્રાપ્ત રકમ '+$(this).val()+' એકત્રિત કરેલી રકમ '+$("#pending_amt").val()+' કરતાં વધુ છે)')
                    $("#admin_coll_btn").hide();
                }
                else {
                    $("#admin_coll_btn").show();
                    $("#pendingamterr").text('Receive Amount '+$("#pending_amt").val());
                }
            })

		});
	})

</script>
