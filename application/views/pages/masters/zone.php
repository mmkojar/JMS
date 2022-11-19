<?php $this->load->view('templates/header') ?>

          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                    <a class="btn btn-info round-btn float-right mb-0" href="" id="show-zone-modal" data-toggle="modal">Add Zone</a>
                  </div>
                  <div class="card-body">
                     <div class="table-responsive">
                        <?php $sr_no=1; ?>
                        <table id="config_table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th>Sr.No</th>
                                 <th>Zone Name</th>
                                 <th>Status</th>
                                 <th>Date</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($zones as $zone):?>
                              <tr>
                                 <td><?php echo $sr_no; ?></td>
                                 <td><?php echo $zone->zone_name; ?></td>
                                 <td><?php echo ($zone->status == 'active' ? '<span class="btn btn-success btn-sm">Active</span>' : '<span class="btn btn-danger btn-sm">Inactive</span>'); ?></td>
                                 <td><?php echo date('M j Y', strtotime($zone->date)); ?></td>
                                 <td>
                                    <a class="btn btn-success btn-sm text-white edit_zone" id="<?php echo $zone->id ?>"><i class="mdi mdi-pencil"></i></a> 
                                    <a class="btn btn-danger btn-sm text-white delete_items" url="masters/delete_itmes" tname="zone_master" id="<?php echo $zone->id ?>"><i class="mdi mdi-delete"></i></a> 
                                 </td>                    
                              </tr>
                              <?php $sr_no++?>
                              <?php endforeach;?>
                           </tbody>
                        </table>
                     </div>
                  </div>
              </div>
              <div class="modal fade" id="zone-modal">
                  <div class="modal-dialog" role="document ">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Add Zone</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                              </button>
                          </div>
                          <form action="<?php echo base_url('masters/zone_crud') ?>" accept="" role="form" method="post" id="add_zone_form">
                                <div class="modal-body">
                                    <div class="box-body">
                                      <div class="form-group">
                                        <label>Zone Name</label>
                                        <input type="text" id="zone_name" name="zone_name" class="form-control" placeholder="Enter..." required>
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
                                  <input type="hidden" name="hidden_zone_id" id="hidden_zone_id">
                                  <input type="hidden" name="table" value="zone_master">
                                  <input type="submit" class="btn btn-success btn-round pull-left" id="insert" value="Submit">
                                </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
          
<?php $this->load->view('templates/footer') ?>
