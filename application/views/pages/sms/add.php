<?php $this->load->view('templates/header'); ?>

<div class="col-12">
   <div class="card">
    <?php $this->load->view('templates/header_title'); ?>
      <div class="card-body">        
         <?php echo form_open(uri_string());?>
           <div class="row">             
              <div class="col-md-12">
                 <div class="form-group">
                  <label for="title">Title</label>
                   <input type="text" class="form-control" name="title" id="title" required>
                 </div>
              </div>
              <div class="col-md-12">
                 <div class="form-group">
                    <label>Message</label>
                    <textarea name="content" class="form-control" rows="5"></textarea>
                 </div>
              </div>
           </div>
           <?php echo form_hidden($csrf); ?>
           <?php echo form_submit('submit', 'Submit',array('class'=>'btn btn-success'));?>
         <?php echo form_close();?>                                                      
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>