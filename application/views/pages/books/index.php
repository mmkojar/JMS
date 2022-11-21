<?php $this->load->view('templates/header') ?>

          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                    <h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
                    <a class="btn btn-info round-btn float-right mb-0" href="<?php echo base_url('books/add') ?>">Add Books</a>
                  </div>
                  <div class="card-body">
                     <div class="table-responsive">
                        <?php $sr_no=1; ?>
                        <table id="config_table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th>Sr.No</th>
                                 <th>Zone Name</th>
                                 <th>Book No</th>
                                 <th>Page Range</th>
                                 <th>Current Page</th>
                                 <th>Last Page</th>
                                 <th>Status</th>
                                 <th>Date</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($books as $row):?>                        
                              <tr>
                                 <td><?php echo $sr_no; ?></td>
                                 <td><?php echo $row->zone_name; ?></td>
                                 <td><?php echo $row->book_no; ?></td>
                                 <td><?php echo $row->page_range; ?></td>
                                 <td><?php echo $row->current_page; ?></td>
                                 <td><?php echo $row->last_page; ?></td>
                                 <td><?php echo ($row->status == 'active' ? anchor('books/updateStatus/'.$row->id.'/inactive/'.$row->zone_id,'Active','class="btn btn-sm btn-info"') : ($row->status == 'inactive' ? anchor('books/updateStatus/'.$row->id.'/active/'.$row->zone_id,'Inactive','class="btn btn-sm btn-danger"') : '<span class="btn btn-sm btn-success">Completed</span>')); ?></td>
                                 <td><?php echo date('M j Y', strtotime($row->created_at)); ?></td>
                                 <td>
                                    <a class="btn btn-danger btn-sm delete_items text-white" url="books/delete" tname="books_issue" id="<?php echo $row->id ?>"><i class="mdi mdi-delete"></i></a>
                                 </td>
                              </tr>
                              <?php $sr_no++?>
                              <?php endforeach;?>
                           </tbody>
                        </table>
                     </div>
                  </div>
              </div>
          </div>
          
<?php $this->load->view('templates/footer') ?>
