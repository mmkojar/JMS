<?php $this->load->view('templates/header'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>            
        </div>
        <div class="card-body">
            <form action="" method="POST" id="saveForm">
                <div class="row">
                    <div class="col-md-12">                
                        <div class="table-responsive">
                            <span id="errors"></span>
                            <table id="multi_form" class="table table-striped table-bordered table-responsive-md" cellspacing="0" width="100%">
                                <thead>
                                    <tr>    
                                        <th>Zone Name</th>
                                        <th>Book No</th>
                                        <th>Page Range</th>
                                        <th>Current Page</th>
                                        <th>Last Page</th>
                                        <th width="50px" id="remove_rows">
                                            <div class="add_row text-center"><i class="btn btn-sm btn-info mdi mdi-plus-circle"></i></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="append_rows">
                                </tbody>
                            </table>     
                        </div>
                    </div>
                </div>
                <?php echo form_hidden($csrf); ?>
                <button type="submit" name="submit" id="saveBtn" class="btn btn-success">Save</button>        
            <?php echo form_close();?>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer') ?>

<script type="text/javascript">

    $(document).ready(function() {
        var count = 0;
        function addRows() {
            count += 1;
            var html = '';
            html += '<tr>';
            html += '<td><select class="form-control zone_id select2" data-sub_item='+count+' name="zone_id[]" id="zone_id_' + count + '"><option value="" selected>--select--</option><?php print_r($zones) ?></select></td>';
            html += '<td><input type="text" id="book_no_' + count + '" data-sub_item='+count+' name="book_no[]" class="form-control book_no" placeholder=""/>';            
            html += '<td><input type="text" id="page_range_' + count + '" data-sub_item='+count+' name="page_range[]" class="form-control page_range" placeholder=""/>';
            html += '<td><input type="text" id="current_page_' + count + '" data-sub_item='+count+' name="current_page[]" class="form-control current_page" placeholder=""/>';
            html += '<td><input type="text" id="last_page_' + count + '" data-sub_item='+count+' name="last_page[]" class="form-control last_page" placeholder=""/>';
            html += '<td><div class="delete_row text-center"><i class="btn btn-sm btn-danger mdi mdi-minus-circle"></i></div></td></tr>';
            $('#append_rows').append(html);
        }

        addRows();

        $(".select2").select2();

        $(document).on('click', '.add_row', function(e){ 
		
            addRows();
            if($("#append_rows").find('tr').length == 0){
                $("#saveBtn").hide();
                
            }
            else{
                $("#saveBtn").show();
            }
            $(".select2").select2();
        });

        $(document).on('click', '.delete_row', function(){
            $(this).closest('tr').remove();
            if($("#append_rows").find('tr').length == 0){
                $("#saveBtn").hide();
                count = 0;
            }
            else{
                $("#saveBtn").show();
            } 
        });
        
        $("#saveBtn").on('click', (e) => {

            e.preventDefault();
            var errors = '';
            $('.zone_id,.book_no,.page_range,.current_page,.last_page').each(function(){
                var sub_item = $(this).data('sub_item');
                if($(this).val() == '')
                {
                    errors += 'Fill All Values at row '+sub_item+'<br/>';              
                    return false;
                }
                else {
                    errors +='';
                    return true;
                }
            });
            if(errors == '') {
                $.ajax({
                    url:'<?php echo base_url('Books/insert'); ?>',
                    method:'POST',
                    data: $("#saveForm").serialize(),
                    beforeSend:function() {
                        $("#saveBtn").text('Loading...');
                        $("#saveBtn").attr('disabled',true);
                    },
                    success:function(res) {
                        var res = JSON.parse(res);
                        alert(res.msg);
                        $("#saveBtn").text('Save');
                        $("#saveBtn").attr('disabled',false);
                        window.location.reload();
                    },
                    error:function(err) {
                        $("#saveBtn").attr('disabled',false);
                        alert(err);
                    }
                })
            }
            else {
                $('#errors').html('<div class="alert alert-danger">'+errors+'</div>');
            }
        })

    })

</script>