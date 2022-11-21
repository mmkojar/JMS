<?php $this->load->view('templates/header'); ?>
<style>
   .btn-success {
   color: #fff;
   background-color: #26A69A;
   border-color: transparent;
   border-radius:0rem;
   }
   .btn:hover {
   cursor: pointer;
   background-color: #26a69a;
   transition: 1s;
   border-color: transparent;
   }
   .btn:focus,.btn:active {
   outline: none;
   border:none;
   }
</style>
<div class="col-12">
   <div class="card">
    <?php $this->load->view('templates/header_title'); ?>
      <div class="card-body">        
         <?php echo form_open(uri_string());?>
           <div class="row">
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Select User</label><br>
                    <!-- <select name="sender_name" class="form-control" required>
                       <option value="">Select</option>
                        <?php //foreach($sender_names as $key => $value): ?>
                         <option value="<?php //echo ($key) ?>" <?php //echo $key == $message->sender_name ? 'selected' : '' ?>><?php //echo $value ?></option>
                        <?php//endforeach ?>
                    </select> -->
                    <input type="text" class="form-control" name="sender_name" id="sender_name" value="<?php echo $message->sender_name ? $message->sender_name : '' ?>" readonly>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="title">Title</label>
                   <input type="text" class="form-control" name="title" id="title" value="<?php echo $message->title ? $message->title : '' ?>" required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Message</label>
                    <input type="text" class="form-control" name="message" id="message" value="<?php echo $message->message_en ? $message->message_en : '' ?>" required>
                 </div>
              </div>
           </div>
           <?php echo form_hidden($csrf); ?>
           <input type="hidden" name="message_id" value="">
           <?php echo form_submit('submit', 'Update',array('class'=>'btn btn-success'));?>
         <?php echo form_close();?>                                                      
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>