<?php $this->load->view('templates/header') ?>

          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                    <a class="btn btn-info round-btn float-right mb-0" href="" id="show-modal" data-toggle="modal">Add</a>
                  </div>
                  <div class="card-body">                    
                     <div class="table-responsive">
                        <?php $sr_no=1; ?>
                        <table id="config_table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th>Sr.No</th>
                                 <th>Name</th>
                                 <th>Status</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($expenses as $row):?>
                              <tr>
                                 <td><?php echo $sr_no; ?></td>
                                 <td><?php echo $row->name; ?></td>
                                 <td><?php echo ($row->status == 'active' ? '<span class="btn btn-success btn-sm">Active</span>' : '<span class="btn btn-danger btn-sm">Inactive</span>'); ?></td>                                 
                                 <td>
                                    <a class="btn btn-success btn-sm text-white edit_row" id="<?php echo $row->id ?>"><i class="mdi mdi-pencil"></i></a> 
                                    <a class="btn btn-danger btn-sm text-white delete_items" url="masters/delete_itmes" tname="expenses_master" id="<?php echo $row->id ?>"><i class="mdi mdi-delete"></i></a> 
                                 </td>                    
                              </tr>
                              <?php $sr_no++?>
                              <?php endforeach;?>
                           </tbody>
                        </table>
                     </div>
                  </div>
              </div>
              <div class="modal fade" id="expense-modal">
                  <div class="modal-dialog" role="document ">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Add</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                              </button>
                          </div>
                          <form action="<?php echo base_url('masters/expense_crud') ?>" accept="" role="form" method="post" id="add_form">
                                <div class="modal-body">
                                    <div class="box-body">
                                      <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter..." required>
                                      </div>
                                      <div class="form-group" id="show_status">
                                        <label>Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                      </div>
                                    </div>                      
                                </div>
                                <div class="modal-footer">
                                  <input type="hidden" name="hidden_expense_id" id="hidden_expense_id">
                                  <input type="hidden" name="table" value="expenses_master">
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
        $("#show-modal").on('click', function() {
            $("#expense-modal").modal('show');
            $("#expense-modal #show_status").hide();
            $("#expense-modal #name").val('');
            $("#expense-modal #status").val('');
            $("#expense-modal #insert").val('Submit');
            $("#hidden_expense_id").val('');
        });

        $(document).on('click', '.edit_row', function() {
            var id = $(this).attr("id");
            $("#expense-modal #show_status").show();
            $.ajax({
                url: "<?php echo base_url('masters/get_data') ?>/expenses_master/" + id,
                method: 'GET',
                dataType: "json",
                success: function(res) {
                    $("#expense-modal").modal('show');
                    $("#hidden_expense_id").val(id);
                    $("#expense-modal #name").val(res.name);
                    $("#expense-modal #status").val(res.status);
                    $("#expense-modal #insert").val('Update');
                }
            });
        });
    })
</script>