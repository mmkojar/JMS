<?php $this->load->view('templates/header') ?>
            
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                  <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                  <a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('users/add') ?>">Add Users</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="config_table" class="table table-striped table-bordered" style="width:100%">	    
                          <thead>
                              <tr>
                                  <th>Sr.No</th>
                                  <th>User Name</th>
                                  <th>Phone No.</th>
                                  <th>Login Username</th>
                                  <th>Zone</th>
                                  <th>Status</th>
                                  <th>User Type</th>
                                  <th>Date</th>
                                  <th>Edit</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php $sr_no = 1; ?>
                              <?php foreach($users as $row): ?>
                                  <tr class="<?php echo ($row->status == 'inactive' ? 'bg-danger text-white' : $row->status == 'divorce') ? 'bg-warning text-dark' : '' ?>">
                                          <td><?php echo $sr_no ?></td>
                                          <td><?php echo str_replace('_',' ',ucwords($row->username)); ?></td>
                                          <td><?php echo $row->phone ? $row->phone : '-' ?></td>
                                          <td><?php echo $row->email ? $row->email : '-' ?></td>
                                          <td><?php echo $row->zone_name ? $row->zone_name : '-' ?></td>
                                          <td><?php echo $row->status ?></td>
                                          <td><?php echo $row->group_name ?></td>
                                          <td><?php echo $row->created_on ?></td>
                                          <!-- <td>
                                              <a class="btn btn-success btn-sm edit_user" id="<?php echo $row->id ?>"><i class="mdi mdi-pencil"></i></a>
                                          </td> -->
                                          <?php if($row->group_id !== '1' || $this->ion_auth->user()->row()->id == $row->id): ?>
                                            <td><?php echo anchor("users/edit/".$row->id, '<i class="mdi mdi-pencil"></i>',array('class'=>'btn btn-success btn-sm')) ;?>
                                              <!-- <a class="btn btn-danger btn-sm delete_items text-white" url="auth/delete" tname="users" id="<?php echo $row->id ?>"><i class="mdi mdi-delete"></i></a>  -->
                                            </td>
                                            <?php else: ?>
                                              <td></td>
                                          <?php endif; ?>
                                      </tr>
                                  <?php $sr_no++ ; ?>
                             <?php endforeach ?>
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>            
        </div>
            
    
<?php $this->load->view('templates/footer') ?>