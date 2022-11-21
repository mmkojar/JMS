<?php $this->load->view('templates/header'); ?>

<div class="col-12">
   <div class="card">
    <?php $this->load->view('templates/header_title'); ?>
      <div class="card-body">        
         <?php echo form_open(uri_string());?>
           <div class="row">
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="description">Description</label>
                   <input type="text" class="form-control" name="description" id="description" value="<?php echo $expense->description ? $expense->description : '' ?>"  required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="amount">Amount</label>
                   <input type="text" class="form-control" name="amount" id="amount" value="<?php echo $expense->amount ? $expense->amount : '' ?>"  required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Paid By</label>
                    <input type="text" class="form-control" name="paid_by" id="paid_by" value="<?php echo $expense->paid_by ? $expense->paid_by : '' ?>" readonly>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Receiver Name</label>
                    <input type="text" class="form-control" name="receiver_name" id="receiver_name" value="<?php echo $expense->receiver_name ? $expense->receiver_name : '' ?>"  required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="date" id="date" value="<?php echo $expense->date ? $expense->date : '' ?>" required>
                 </div>
              </div> 
           </div>
           <?php echo form_hidden($csrf); ?>
           <?php echo form_submit('submit', 'Update',array('class'=>'btn btn-success'));?>
         <?php echo form_close();?>                                                      
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>