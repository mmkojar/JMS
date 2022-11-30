<?php $this->load->view('templates/header'); ?>

<div class="col-12">
   <div class="card">
    <?php $this->load->view('templates/header_title'); ?>
      <div class="card-body">        
         <?php echo form_open(uri_string());?>
           <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Select Year</label>
                     <select name="year" id="year" class="form-control" style="width: 100%;" required>
                        <option value="">Select</option>
                        <?php foreach($years as $row): ?>
                           <option value="<?php echo $row->financial_year ?>"><?php echo $row->financial_year ?>
                           </option>
                        <?php endforeach ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                 <div class="form-group">
                    <label>Receiver Name</label>
                    <select name="receiver_id" id="receiver_id" class="form-control" style="width: 100%;" required>
                        <option value="">Select</option>
                        <?php foreach($receivers as $row): ?>
                           <option value="<?php echo $row->id ?>"><?php echo $row->name ?>
                           </option>
                        <?php endforeach ?>
                     </select>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="amount">Amount</label>
                  <span class="msg" id="expenseamtmsg"></span>
                  <input type="number" class="form-control" name="amount" id="amount" required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Paid By</label>
                    <input type="text" class="form-control" name="paid_by" id="paid_by" value="<?php echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->surname ?>" readonly>
                 </div>
              </div>
              
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="date" id="date" required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="description">Description</label>
                   <input type="text" class="form-control" name="description" id="description" required>
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

<script type="text/javascript">
   $(document).ready(function () {

      $("#year").on('change', function () {
         $.ajax({
            url: "<?php echo base_url('expenses/get_total_expense_amount') ?>/" + $(this).val(),
            method: 'GET',
            dataType: 'json',
            success: function (resp) {
               console.log(resp);
               var total_response = [];
               for (var i in resp) {					
                  resp[i].amt_received = Number(resp[i].amt_received);
                  resp[i].amt_use_in_expense = Number(resp[i].amt_use_in_expense);                  
                  total_response[i] = resp[i];
               }
               const result =  total_response.reduce((a2, c2) => {
                  let filteredP = a2.filter(el => el.year === c2.year)
                  if (filteredP.length > 0) {
                     a2[a2.indexOf(filteredP[0])].amt_received += +c2.amt_received;
                     
                  } else {
                     a2.push(c2);
                  }
                  return a2;
               }, []);
               console.log(result);
               const amtleft = resp[0].amt_received - resp[0].amt_use_in_expense;
               console.log(amtleft);
               /* const sumamt = result.map(reslt => reslt.amt_use_in_expense)
                                    .reduce((total,count) => total + count,0);
               console.log(sumamt); */
               // const sumamt = resp
               //                .map(results => Number(results.amt_received))
               //                .reduce((total, count) => total + count,0);
            }
         });
      });
   })
</script>