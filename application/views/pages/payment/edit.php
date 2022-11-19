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
<div class="col-12 mb-3">
   <h4 class="float-left"><?php echo $title ?></h4>
</div>
<div class="col-12">
   <div class="card">
      <div class="card-body">        
         <?php echo form_open(uri_string());?>
           <div class="row">
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Select User</label><br>
                    <select name="user" class="select2 form-control custom-select" style="width: 100%;" required>
                       <option value="">Select</option>
                       <optgroup>
                          <?php foreach($users as $user): ?>
                          <?php if($user->id != 1): ?>
                          <option value="<?php echo $user->id ?>" <?php echo $user->id == $payment->user_id ? 'selected' : '' ?><?php echo $get_zone_id != $user->zone_id ? 'disabled' : '' ?>><?php echo $user->first_name ?></option>
                          <?php endif ?>
                          <?php endforeach ?>
                       </optgroup>
                    </select>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label for="zone">Select Zone</label>
                    <select name="zone" class="select2 form-control custom-select" style="width: 100%;" required>
                       <option value="">Select</option>
                       <optgroup>
                          <?php foreach($zones as $zone): ?>
                          <option value="<?php echo $zone->id ?>" <?php echo $zone->id == $payment->zone_id ? 'selected' : '' ?><?php echo $get_zone_id != $zone->id ? 'disabled' : '' ?>><?php echo $zone->zone_name ?></option>
                          <?php endforeach ?>
                       </optgroup>
                    </select>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Receiver Name</label>
                    <input type="text" class="form-control" name="receiver_name" id="receiver_name" 
                    value="<?php echo $payment->receiver_name ? $payment->receiver_name : '' ?>" required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" class="form-control" name="amount" id="amount" 
                    value="<?php echo $payment->amount ? $payment->amount : '' ?>" readonly>
                 </div>
              </div>
           </div>
           <?php echo form_hidden($csrf); ?>
           <input type="hidden" name="payment_id" value="">
           <?php echo form_submit('submit', 'Submit',array('class'=>'btn btn-success'));?>
         <?php echo form_close();?>                                                      
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>