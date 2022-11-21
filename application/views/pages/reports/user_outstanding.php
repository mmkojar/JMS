<?php $this->load->view('templates/header') ?>

<div class="col-12">
    <div class="card">
        <?php $this->load->view('templates/header_title'); ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_search">Select User</label>
                        <select name="user_search" id="user_search" class="select2 form-control custom-select"
                            style="width: 100%;">
                            <option value="">Select</option>
                            <optgroup>
                                <?php foreach($get_users as $user): ?>
                                <option value="<?php echo $user->id ?>">
                                    <?php echo str_replace('_',' ',ucwords($user->username)) ?></option>
                                <?php endforeach ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="display_user_reports" class="table table-striped table-bordered">
                </table>
            </div>

        </div>
    </div>
</div>


<?php $this->load->view('templates/footer') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    $('#user_search').on('change', function() {
        var user_id = $("#user_search").val();
        $('#display_user_reports').DataTable().destroy();
        load_user_reports(user_id);
    });
    var randno = Math.floor(Math.random() * 100) + 31;
    load_user_reports(randno);

    function load_user_reports(id) {
        $.ajax({
            url: "<?php echo base_url('reports/all_user_outstanding_api') ?>/" + id,
            method: 'GET',
            dataType: "json",
            beforeSend: function() {
                $("#preloader").show();
            },
            success: function(res) {
                var html = "";
                var sr_no = 1;
                /* html += `
                	<thead>
                		<tr>
                			<th>Sr.No</th>
                			<th>Name</th>
                			<th>Values</th>
                		</tr>
                	</thead>	 
                	<tbody>
                	`;
                $.each(res, (key,value)=> {
                	if(key == "username") {
                		var value = value.replaceAll("_"," ").toUpperCase();
                	}
                	var key = key.replaceAll('_'," ");
                	html += `
                		<tr>
                			<td>${sr_no}</td>
                			<td>${key}</td>
                			<td>${value}</td>
                		</tr>
                		`;
                	sr_no++;
                });
                html += `</tbody>`; */
                html += `
						<thead>
							<tr>
								<th>Sr.No</th>
								<th>Year</th>
								<th>Userame</th>
								<th>Receiver Name</th>
								<th>Amount To Collect</th>
								<th>Receipt No</th>
								<th>Receipt Date</th>
								<th>Receipt Amount</th>
								<th>Balance Amount</th>
								<th>Balance Count</th>
								<th>Adjustment Amount</th>
								<th>Remark</th>
							</tr>
						</thead>
						<tbody>
						`;
                for (var i in res.year) {
                    var yearget = Number(res.year[i]);
                    var recvrname = res['fy_' + yearget + '_receiver_name'];
                    var amtcollect = res['fy_' + yearget + '_amt_to_collect'];
                    var recptno = res['fy_' + yearget + '_receipt_no'];
                    var recptdate = res['fy_' + yearget + '_receipt_date'];
                    var recptamt = res['fy_' + yearget + '_receipt_amt'];
                    var balamt = res['fy_' + yearget + '_balance_amt'];
                    var balcntamt = res['fy_' + yearget + '_balance_count_amt'];
                    var adjamt = res['fy_' + yearget + '_adjustment_amt'];
                    var remark = res['fy_' + yearget + '_remark'];
                    html += `
							<tr class="${balcntamt == '1.00' ? 'bg-danger text-white' : ''}">
								<td>${sr_no}</td>
								<td>${yearget}</td>
								<td><b>UserName: ${res.username.replaceAll("_"," ").toUpperCase()} ---MobileNo ${res.phone} --- Zone: ${res.zone_name} --- User Status: ${res.user_status} --- Invoice Status: ${res.invoice_status}</b></td>
								<td>${recvrname}</td>
								<td>${amtcollect}</td>
								<td>${recptno}</td>
								<td>${recptdate}</td>
								<td>${recptamt}</td>
								<td>${balamt}</td>
								<td>${balcntamt}</td>
								<td>${adjamt}</td>
								<td>${remark}</td>
							</tr>
							`;
                    sr_no++;
                }
                html += `</tbody>`;
                $("#display_user_reports").html(html);
                $('#display_user_reports').DataTable({
                    responsive: true,
                    rowGroup: {
                        dataSrc: [2]
                    },
                    columnDefs: [{
                        targets: [2],
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

});
</script>