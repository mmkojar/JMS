            </div>

        </div>

    </div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
</div>
<!-- All Jquery -->
<!-- ============================================================== -->
<?php
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
?>
<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script> -->
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/custom.min.js"></script>
<?php if($page == 'user_outstanding' || base_url('payment/add')) : ?>
    <script src="<?php echo base_url(); ?>assets/js/select2.min.js"></script>
    <script>$(".select2").select2();</script>
<?php endif ?>
<?php if($page == 'payment' || base_url('payment/add')) : ?>
    <script>
        $("#show_on_check").hide();
        $("#adjustment_check").click(function() {
            if ($(this).is(':checked')) {
                $("#show_on_check").show();
                $("#adjustment_check").attr('required', true);
            } else {
                $("#show_on_check").hide();
                $("#adjustment_check").attr('required', false);
            }
        })
    </script>
<?php endif ?>
<?php if($page !== 'index.php' && $page !== 'add'): ?>
    
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/jszip.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/buttons.html5.min.js"></script>
    <!--<script type="text/javascript" src="<?php //echo base_url(); ?>/assets/js/dataTables.responsive.min.js"></script>
    
    <script type="text/javascript" src="<?php //echo base_url(); ?>/assets/js/vfs_fonts.js"></script>
     -->
    <script src="<?php echo base_url(); ?>/assets/js/toastr.min.js"></script>
    <script type="text/javascript">
        toastr.options.timeOut = 1500;
        toastr.options.positionClass = 'toast-top-center';
    </script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            
            const pathname = window.location.href;
            const lastSegment = pathname.substring(pathname.lastIndexOf('/') + 1);
            // $(".table-responsive .table").addClass('display responsive nowrap');
            setTimeout(function() {
                $('#flash_messages').hide();
            }, 2500);
            $('#config_table').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'collection',
                    text: '<span></span> Export',
                    buttons: [
                        'excel',
                        'pdf',
                    ],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }]
            });

            if (lastSegment == 'zone') {
                // For Zone Modal
                $("#show-zone-modal").on('click', function() {
                    $("#zone-modal").modal('show');
                    $("#zone-modal #show_status").hide();
                    $("#zone-modal #zone_name").val('');
                    $("#zone-modal #status").val('');
                    $("#zone-modal #insert").val('Submit');
                    $("#hidden_zone_id").val('');
                });

                $(document).on('click', '.edit_zone', function() {
                    var id = $(this).attr("id");
                    $("#zone-modal #show_status").show();
                    $.ajax({
                        url: "<?php echo base_url('masters/get_data') ?>/zone_master/" + id,
                        method: 'GET',
                        dataType: "json",
                        success: function(res) {
                            $("#zone-modal").modal('show');
                            $("#hidden_zone_id").val(id);
                            $("#zone-modal #zone_name").val(res.zone_name);
                            $("#zone-modal #status").val(res.status);
                            $("#zone-modal #insert").val('Update');
                        }
                    });
                });
            }

            if (lastSegment == 'fee') {
                // For Fee Modal
                $("#show-fee-modal").on('click', function() {
                    $("#fee-modal").modal('show');
                    $("#fee-modal #financial_year").val('');
                    $("#fee-modal #period").val('');
                    $("#fee-modal #amount").val('');
                    $("#fee-modal #insert").val('Submit');
                    $("#hidden_fee_id").val('');
                });

                $(document).on('click', '.edit_fee', function() {
                    var id = $(this).attr("id");
                    $.ajax({
                        url: "<?php echo base_url('masters/get_data') ?>/fee_master/" + id,
                        method: 'GET',
                        dataType: "json",
                        success: function(res) {
                            $("#fee-modal").modal('show');
                            $("#hidden_fee_id").val(id);
                            $("#fee-modal #financial_year").val(res.financial_year);
                            $("#fee-modal #period").val(res.period);
                            $("#fee-modal #amount").val(res.amount);
                            $("#fee-modal #insert").val('Update');
                        }
                    });
                });
            }

            if (lastSegment == 'surnames') {
                // For Surname Modal
                $("#show-surname-modal").on('click', function() {
                    $("#surname-modal").modal('show');
                    $("#surname-modal #show_status").hide();
                    $("#surname-modal #surname").val('');
                    $("#surname-modal #status").val('');
                    $("#surname-modal #insert").val('Submit');
                    $("#hidden_surname_id").val('');
                });

                $(document).on('click', '.edit_surname', function() {
                    var id = $(this).attr("id");
                    $("#surname-modal #show_status").show();
                    $.ajax({
                        url: "<?php echo base_url('masters/get_data') ?>/surname_master/" + id,
                        method: 'GET',
                        dataType: "json",
                        success: function(res) {
                            $("#surname-modal").modal('show');
                            $("#hidden_surname_id").val(id);
                            $("#surname-modal #surname").val(res.surname);
                            $("#surname-modal #status").val(res.status);
                            $("#surname-modal #insert").val('Update');
                        }
                    });
                });
            }            

            var d = new Date();
            var current_year = d.getFullYear();

            // ------Get Area Wise Income Reports Starts------
            if (lastSegment == 'area') {
                get_area_surname_report('a', current_year);
            }
            // ------Get Area Wise Income Reports Ends------

            function get_area_surname_report(id, y) {
                $.ajax({
                    url: "<?php echo base_url('reports/reports_api') ?>/" + y,
                    method: 'GET',
                    dataType: "json",
                    beforeSend: function() {
                        $("#preloader").show();
                    },
                    success: function(res) {
                        var yearly_response = [];
                        // console.log(res);
                        for (var i in res) {
                            /*if(res[i].balance_count_amt === 1) {
                                res[i].balance_count_amt = Number(res[i].balance_count_amt);
                                res[i].balance_amt = Number(res[i].balance_amt);
                            }
                            else {
                                res[i].balance_count_amt = Number(res[i].balance_count_amt);
                                res[i].balance_amt = 0;
                            }*/
                            res[i].balance_amt = Number(res[i].balance_amt);
                            res[i].balance_count_amt = Number(res[i].balance_count_amt);
                            res[i].receipt_amt = Number(res[i].receipt_amt);
                            res[i].adjustment_amt = Number(res[i].adjustment_amt);
                            res[i].year = y;
                            yearly_response[i] = res[i];
                        }
                        if (id == 'a') {
                            yearly_response.reduce((a2, c2) => {
                                let filteredP = a2.filter(el => el.zone_id === c2.zone_id)
                                if (filteredP.length > 0) {
                                    a2[a2.indexOf(filteredP[0])].balance_amt += +c2.balance_amt;
                                    a2[a2.indexOf(filteredP[0])].balance_count_amt += +c2
                                        .balance_count_amt;
                                    a2[a2.indexOf(filteredP[0])].receipt_amt += +c2.receipt_amt;
                                    a2[a2.indexOf(filteredP[0])].adjustment_amt += +c2
                                        .adjustment_amt;
                                } else {
                                    a2.push(c2);
                                }
                                return a2;
                            }, []);

                            var final_filtered_response = [...yearly_response.reduce((mp, o) => {
                                if (!mp.has(o.zone_id)) mp.set(o.zone_id, Object.assign({
                                    count: 0
                                }, o));
                                mp.get(o.zone_id).count++;
                                return mp;
                            }, new Map).values()];
                        }

                        if (id == 's') {
                            yearly_response.reduce((a2, c2) => {
                                let filteredP = a2.filter(el => el.surname === c2.surname)
                                if (filteredP.length > 0) {
                                    a2[a2.indexOf(filteredP[0])].balance_amt += +c2.balance_amt;
                                    a2[a2.indexOf(filteredP[0])].balance_count_amt += +c2
                                        .balance_count_amt;
                                    a2[a2.indexOf(filteredP[0])].receipt_amt += +c2.receipt_amt;
                                    a2[a2.indexOf(filteredP[0])].adjustment_amt += +c2
                                        .adjustment_amt;
                                } else {
                                    a2.push(c2);
                                }
                                return a2;
                            }, []);

                            var final_filtered_response = [...yearly_response.reduce((mp, o) => {
                                if (!mp.has(o.surname)) mp.set(o.surname, Object.assign({
                                    count: 0
                                }, o));
                                mp.get(o.surname).count++;
                                return mp;
                            }, new Map).values()];
                        }

                        console.log(final_filtered_response);

                        const total_joda = final_filtered_response
                            .map(results => Number(results.count))
                            .reduce((total, litres) => total + litres, 0);

                        const joda_count = final_filtered_response.map(results => Number(results
                                .balance_count_amt))
                            .reduce((total, litres) => total + litres, 0);

                        const joda_balance = final_filtered_response.map(results => Number(results
                                .balance_amt))
                            .reduce((total, litres) => total + litres, 0);

                        const joda_adjust = final_filtered_response.map(results => Number(results
                                .adjustment_amt))
                            .reduce((total, litres) => total + litres, 0);

                        const paid_balance = final_filtered_response.map(results => Number(results
                                .receipt_amt))
                            .reduce((total, litres) => total + litres, 0);

                        var html = "";
                        var srn_no = 1;
                        html += `
                                        <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    ${id == 'a' ? '<th>Area</th>' : '<th>Surname</th>'}
                                                    <th>Total Joda</th>
                                                    <th>Remaining Joda Count</th>
                                                    <th>Paid Joda Count</th>
                                                    <th>Amount Paid</th>                                            
                                                    <th>Adjustment Amount</th>
                                                    <th>Remaining Balance</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        `;
                        for (var i in final_filtered_response) {
                            html += `
                                            <tr>
                                                <td>${srn_no}</td>
                                                ${id == 'a' ? `<td>${final_filtered_response[i].zone_name}</td>` : `<td>${final_filtered_response[i].surname}</td>`}
                                                <td>${final_filtered_response[i].count}</td>                                
                                                <td>${final_filtered_response[i].balance_count_amt}</td>
                                                <td>${final_filtered_response[i].count - final_filtered_response[i].balance_count_amt}</td>
                                                <td>${final_filtered_response[i].receipt_amt}</td>
                                                <td>${final_filtered_response[i].adjustment_amt}</td>
                                                <td>${final_filtered_response[i].balance_amt}</td>
                                                <td>${final_filtered_response[i].receipt_amt + final_filtered_response[i].balance_amt + final_filtered_response[i].adjustment_amt}</td>
                                            </tr>
                                            `;
                            srn_no++;
                        }
                        html += `
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td></td>
                                                    <td>Total: </td>
                                                    <td>${total_joda}</td>
                                                    <td>${joda_count}</td>
                                                    <td>${total_joda - joda_count}</td>
                                                    <td>${paid_balance}</td>
                                                    <td>${joda_adjust}</td> 
                                                    <td>${joda_balance}</td>
                                                    <td>${joda_balance + paid_balance + joda_adjust}</td>
                                                </tr>
                                            </tfoot>`;

                        if (id == 'a') {
                            $("#display_area_wise_reports").html(html);
                            $('#display_area_wise_reports').DataTable({
                                dom: 'lBfrtip',
                                buttons: [{
                                    extend: 'collection',
                                    text: '<span></span> Export',
                                    buttons: [
                                        'excel',
                                        'pdf',
                                    ]
                                }],
                            });
                        }
                        if (id == 's') {
                            $("#display_surname_wise_reports").html(html);
                            $('#display_surname_wise_reports').DataTable({
                                dom: 'lBfrtip',
                                buttons: [{
                                    extend: 'collection',
                                    text: '<span></span> Export',
                                    buttons: [
                                        'excel',
                                        'pdf',
                                    ]
                                }],
                            });
                            // getPagination('#display_surname_wise_reports');
                        }
                        $("#preloader").hide();
                    }
                });
            }

            // ------Get Surname Wise Income Reports Starts------
            if (lastSegment == 'surname') {
                get_area_surname_report('s', current_year)
            }

            // ------Get Surname Wise Income Reports Ends------        

            // ------Death And Divorce Reports Start------

            if (lastSegment == 'death' || lastSegment == 'divorce') {


                lastSegment == 'death' ? status = 'inactive' : lastSegment == 'divorce' ? status = 'divorce' : '';

                get_user_reports_list();

                function get_user_reports_list() {

                    $.ajax({
                        url: "<?php echo base_url('reports/death_divorce_api') ?>/" + status,
                        method: 'GET',
                        dataType: "json",
                        beforeSend: function() {
                            $("#preloader").show();
                        },
                        success: function(res) {
                            var html = "";
                            var sr_no = 1;
                            // var check_user_array = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15'];
                            html += `
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>First Name</th>
                                                <th>Father Name</th>
                                                <th>Last Name</th>
                                                <th>Phone No.</th>
                                                <th>Email</th>
                                                <th>Zone</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                ${lastSegment == 'death' ? `<th>Expiry Date</th>` : ``}
                                            </tr>
                                        </thead>
                                    <tbody>`;
                            for (var i in res) {
                                html += `
                                            <tr> 
                                                <td>${sr_no}</td>
                                                <td>${res[i].first_name}</td>
                                                <td>${res[i].father_name}</td>
                                                <td>${res[i].surname ? res[i].surname : res[i].last_name}</td>
                                                <td>${res[i].phone ? res[i].phone : '-'}</td>
                                                <td>${res[i].email ? res[i].email : '-'}</td>
                                                <td>${res[i].zone_name ? res[i].zone_name : '-'}</td>
                                                <td>${res[i].status}</td>
                                                <td>${res[i].created_on}</td>
                                                ${lastSegment == 'death' ? `<td>${res[i].expiry_date}</td>` : ``}
                                            </tr>`;
                                sr_no++;
                            }
                            html += `
                                        </tbody>`;
                            $("#display_death_divorce_reports").html(html);
                            $('#display_death_divorce_reports').DataTable({
                                dom: 'lBfrtip',
                                buttons: [{
                                    extend: 'collection',
                                    text: '<span></span> Export',
                                    buttons: [
                                        'excel',
                                        'pdf',
                                    ]
                                }],
                            });
                            $("#preloader").hide();
                            // getPagination('#invoices');

                        }
                    });
                }
            }

            // ------Death And Divorce Reports Ends------

            // ------Zone Transfer Reports Start------

            if (lastSegment == 'zone_transfer') {

                get_user_zone_transfer_report_list();

                function get_user_zone_transfer_report_list() {

                    $.ajax({
                        url: "<?php echo base_url('reports/user_zone_transfer_api') ?>/" + status,
                        method: 'GET',
                        dataType: "json",
                        beforeSend: function() {
                            $("#preloader").show();
                        },
                        success: function(res) {
                            var html = "";
                            var sr_no = 1;
                            html += `
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>First Name</th>
                                                <th>Father Name</th>
                                                <th>Last Name</th>
                                                <th>Mobile No.</th>
                                                <th>Old Zone</th>
                                                <th>New Zone</th>
                                                <th>Status</th>
                                                <th>Transfer Date</th>
                                            </tr>
                                        </thead>
                                    <tbody>`;
                            for (var i in res) {
                                html += `
                                            <tr> 
                                                <td>${sr_no}</td>
                                                <td>${res[i].first_name}</td>
                                                <td>${res[i].father_name}</td>
                                                <td>${res[i].surname ? res[i].surname : res[i].last_name}</td>
                                                <td>${res[i].phone}</td>
                                                <td>${res[i].z2name}</td>
                                                <td>${res[i].z1name}</td>
                                                <td>${res[i].status}</td>
                                                <td>${res[i].transfer_date}</td>
                                            </tr>`;
                                sr_no++;
                            }
                            html += `
                                        </tbody>`;
                            $("#display_zone_transfer_reports").html(html);
                            $('#display_zone_transfer_reports').DataTable({
                                dom: 'lBfrtip',
                                buttons: [{
                                    extend: 'collection',
                                    text: '<span></span> Export',
                                    buttons: [
                                        'excel',
                                        'pdf',
                                    ]
                                }],
                            });
                            $("#preloader").hide();
                            // getPagination('#invoices');

                        }
                    });
                }
            }

            // ------Zone Transfer Reports Ends------

            // commmon Year Search
            $("#year_search").on('change', function() {

                var y = $(this).val();
                if (lastSegment == 'surname') {
                    $('#display_surname_wise_reports').DataTable().destroy();
                    get_area_surname_report('s', y);
                }
                if (lastSegment == 'area') {
                    $('#display_area_wise_reports').DataTable().destroy();
                    get_area_surname_report('a', y);
                }
            });

            // Delete Items
            $(document).on('click', '.delete_items', function() {
                var id = $(this).attr('id');
                var table = $(this).attr('tname');
                var url = $(this).attr('url');

                if (confirm('Are you Sure!')) {
                    $.ajax({
                        url: `<?php echo base_url() ?>${url}/${table}/${id}`,
                        method: 'GET',
                        dataType: "json",
                        beforeSend: function() {
                            $("#preloader").show();
                        },
                        success: function(res) {
                            $("#preloader").hide();
                            if (res.status == -1) {
                                alert(res.msg);
                                window.location.href = lastSegment;
                            } else {
                                alert(res.msg);
                            }
                        }
                    });
                } else {
                    $("#preloader").hide();
                }
            })


        });
    </script>
<?php endif ?>

<!-- <script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/js/perfect-scrollbar.jquery.min.js"></script> -->

</body>

</html>