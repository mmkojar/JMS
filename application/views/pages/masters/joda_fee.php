<?php $this->load->view('templates/header') ?>
          
          <div class="col-12">
              <div class="card">
                   <div class="card-header">
                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                    <a class="btn btn-info round-btn float-right mb-0" href="" id="show-fee-modal" data-toggle="modal">Add Fee</a>
                  </div>
                  <div class="card-body">                      
                     <div class="table-responsive">
                        <?php $sr_no=1; ?>
                        <table id="config_table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th>Sr.No</th>
                                 <th>Financial Year</th>
                                 <th>Period</th>
                                 <th>Amount</th>
                                 <th>Date</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($fees as $fee):?>
                              <tr>
                                 <td><?php echo $sr_no; ?></td>
                                 <td><?php echo $fee->financial_year; ?></td>
                                 <td><?php echo $fee->period; ?></td>
                                 <td><?php echo $fee->amount; ?></td>
                                 <td><?php echo date('M j Y', strtotime($fee->date)); ?></td>
                                 <td>
                                    <a class="btn btn-success btn-sm text-white edit_fee" id="<?php echo $fee->id ?>"><i class="mdi mdi-pencil"></i></a> 
                                    <a class="btn btn-danger btn-sm text-white delete_items" url="masters/delete_itmes" tname="fee_master" id="<?php echo $fee->id ?>"><i class="mdi mdi-delete"></i></a> 
                                 </td>                    
                              </tr>
                              <?php $sr_no++?>
                              <?php endforeach;?>
                           </tbody>
                        </table>
                     </div>
                  </div>
              </div>
              <div class="modal fade" id="fee-modal">
                  <div class="modal-dialog" role="document ">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Add Joda Fee</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                              </button>
                          </div>
                          <form action="<?php echo base_url('masters/fee_crud') ?>" accept="" role="form" method="post" id="add_zone_form">
                                <div class="modal-body">
                                    <div class="box-body">
                                      <div class="form-group">
                                        <label>Financial Year</label>
                                        <input type="text" id="financial_year" name="financial_year" class="form-control" placeholder="Enter..." required>
                                      </div>
                                      <div class="form-group">
                                        <label>Period</label>
                                        <input type="text" id="period" name="period" class="form-control" placeholder="eg. Jan 2020 - Dec 2020" required>
                                      </div>
                                      <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" id="amount" name="amount" class="form-control" placeholder="Enter..." required>
                                      </div>
                                    </div>                      
                                </div>
                                <div class="modal-footer">
                                  <input type="hidden" name="hidden_fee_id" id="hidden_fee_id">
                                  <input type="submit" class="btn btn-success btn-round pull-left" id="insert" value="Submit">
                                </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
          
<?php $this->load->view('templates/footer') ?>
