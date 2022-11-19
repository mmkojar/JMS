<?php $this->load->view('templates/header'); ?>
<style>   
   .error{
      color: red;
   }
</style>
<div class="col-12">
   <div class="card">
      <?php $this->load->view('templates/header_title'); ?>
      <div class="card-body">               
         <?php echo form_open(uri_string());?>
           <div class="row">
              <div class="col-md-12">
                <div class="p-3 mb-2 text-white bg-danger d-none" id="show_pending_amt"></div>
              </div>            
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Select User</label><br>
                    <select name="user" id="pay_users" class="select2 form-control custom-select" style="width: 100%;" required>
                       <option value="">Select</option>
                       <optgroup>
                          <?php foreach($users as $user): ?>
                              <option value="<?php echo $user->id ?>"><?php echo str_replace('_',' ',ucwords($user->username)) ?></option>
                          <?php endforeach ?>
                       </optgroup>
                    </select>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label for="zone">Select Zone</label>
                    <?php if($this->ion_auth->is_admin()) { ?>
                      <select name="zone" class="form-control" id="zone_from_user_admin" style="width: 100%;" required>
                      </select>
                    <?php } else { ?>
                        <select name="zone" class="form-control" style="width: 100%;" required>
                            <?php foreach($zones as $zone): ?>
                                <option value="<?php echo $zone->id ?>"><?php echo $zone->zone_name ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php } ?>
                    
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Receipt No.</label>
                    <input type="text" class="form-control" name="receipt_no" id="receipt_no" required>
                    <?php echo form_error('receipt_no','<p class="error">', '</p>'); ?>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Receipt Date</label>
                    <input type="date" class="form-control" name="receipt_date" id="receipt_date" required>
                 </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Select Year</label>
                  <select name="year" id="payment_year" class="form-control" style="width: 100%;" required>
                     <!-- <option value="">Select</option>
                      <optgroup>
                        <?php //foreach($get_payment_year as $row): ?>
                          <option value="<?php //echo $row->financial_year ?>"><?php //echo $row->financial_year ?></option>
                        <?php //endforeach ?>
                      </optgroup> -->
                  </select>
                </div>
              </div>
              <div class="col-md-6 mb-2">
                <div class="form-group">
                  <label>Select Amount</label>
                  <!-- <select name="receipt_amt" id="amount_from_year" class="form-control"></select> -->
                  <input type="text" name="receipt_amt" id="receipt_amt" class="form-control">
                </div>
                <input type="checkbox" name="adjustment_check" id="adjustment_check">
                <span>Click here For Adjustment Amount</span>
                <div class="form-group mt-2" id="show_on_check">
                  <input type="text" name="adjustment_amt" id="adjustment_amt" class="form-control" placeholder="Enter Adjustment Amount" value="0">
                </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Receiver / Collector Name</label>
                    <input type="text" class="form-control" name="receiver_name" id="receiver_name" value="<?php echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->surname ?>" readonly>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Remark</label>
                    <input type="text" class="form-control" name="remark" id="remark" required>
                 </div>
              </div>            
               <div class="col-md-12 mt-3">
                  <h4>For SMS</h4>
               </div>
               <!-- <div class="col-md-6">                  
                  <div class="form-group">
                     <label>Receipt No.</label>
                     <input type="text" class="form-control" name="preceipt_no" required>                    
                  </div>
               </div> -->
               <div class="col-md-6">  
                 <div class="form-group">
                    <label>Mobile No.</label>
                    <input type="text" class="form-control" name="pmobile_no" id="pmobile_no">                    
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
   <script>
         $(document).on('change', '#pay_users', function() {
            $("#preloader").show();
            $("#payment_year").val('');
            $("#show_pending_amt").addClass('d-none');
            $("#show_pending_amt").removeClass('d-block');
            var selected_id = $('#pay_users option:selected').val();

            $.ajax({
               url: "<?php echo base_url('payment/zone_from_user') ?>/" + selected_id,
               method: 'GET',
               dataType: "json",
               success: function(res) {
                  $("#preloader").hide();
                  console.log(res);
                  var html = '';
                  html += '<option value="' + res.zone_id + '">' + res.zone_name + '</option>';
                  $("#zone_from_user_admin").html(html);
                  $("#pmobile_no").val(res.phone);
                  if(res.zone_id) {
                     $.ajax({
                        url: "<?php echo base_url('books/get_receipt_no') ?>/"+res.zone_id,
                        method: 'GET',
                        dataType:'json',
                        success: function(zres) {
                           console.log(zres);
                           if(zres.data) {
                              var rno = zres.data ? zres.data.current_page : '';
                              $("#receipt_no").val(rno);
                           }
                           else {
                              alert('Please Check Book Receipt');
                           }
                        }
                     });
                  }
                  if (selected_id != '') {
                     $.ajax({
                        url: "<?php echo base_url('payment/get_pending_year') ?>/" +
                              selected_id,
                        method: 'GET',
                        success: function(res1) {
                              var res1 = res1.reverse();
                              var phtml = '';
                              if (res1.length > 0) {
                                 for (var i in res1) {
                                    phtml += '<option value="' + res1[i] +
                                          '">' + res1[i] + '</option>';
                                 }
                              } else {
                                 phtml +=
                                    '<option value="">No Pending Year</option>';
                              }
                              $("#payment_year").html(phtml);
                        }
                     });
                  }
               }
            });
         });

         // Get Pendign Amount From Year In Payment Addition
         $(document).on('change', '#payment_year', function() {
            var year = $(this).val();

            var selected_id = $('#pay_users option:selected').val();

            if (selected_id != '') {
               $.ajax({
                     url: "<?php echo base_url('payment/get_pending_payments_api') ?>/" +
                        selected_id + '/' + year,
                     method: 'GET',
                     dataType: "json",
                     success: function(res1) {
                        $("#show_pending_amt").text(res1.msg);
                        $("#show_pending_amt").addClass('d-block');
                     }
               });
            }
            /*$.ajax({                
               url:"<?php //echo base_url('payment/get_amount_from_year_api') ?>/"+year,
               method: 'GET',
               dataType: "json",
               success: function(res) {
                     var html = '';
                     html += '<option value="'+res.amount+'">'+res.amount+'</option>';                
                     $("#amount_from_year").html(html);

                     if(selected_id != '') {
                        $.ajax({                
                           url:"<?php //echo base_url('payment/get_pending_payments_api') ?>/"+selected_id+'/'+year,
                           method: 'GET',
                           dataType: "json",
                           success: function(res1) {
                                 $("#show_pending_amt").text(res1.msg);
                                 $("#show_pending_amt").addClass('d-block');
                           }
                        });
                     }
               
               }
            });      */
         });
   </script>