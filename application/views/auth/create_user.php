 <?php $this->load->view('templates/header'); ?>

    <style type="text/css">
      .error{
        color: red;
      }
    </style>      
      <div class="col-12">
         <div class="card">
          <?php $this->load->view('templates/header_title'); ?>
            <div class="card-body">
                <?php echo form_open("auth/create_user");?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">                       
                         <label for="first_name">First Name</label>
                         <?php echo form_input($first_name);?>
                         <?php echo form_error('first_name','<p class="error">', '</p>'); ?>   
                        </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">                       
                       <label for="father_name">Father Name</label>
                        <?php echo form_input($father_name);?>
                       <?php echo form_error('father_name','<p class="error">', '</p>'); ?>   
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="last_name">Select SurName</label>
                          <select name="last_name" id="last_name" class="form-control">
                            <option value="">Select</option>
                            <?php foreach($surnames as $surname): ?>
                            <option value="<?php echo $surname->id ?>" <?php echo set_select('last_name',  $surname->id); ?>><?php echo $surname->surname ?></option>
                            <?php endforeach ?>
                          </select>
                          <?php echo form_error('last_name','<p class="error">', '</p>'); ?>
                      </div>
                    </div>
                      <div class="form-group">
                       <?php
                        if($identity_column!=='email') {
                            echo '<p>';
                            echo lang('create_user_identity_label', 'identity');
                            echo '<br />';
                            echo form_error('identity');
                            echo form_input($identity);
                            echo '</p>';
                        }
                        ?>
                      </div>
                    <div class="col-md-6">
                      <div class="form-group">                       
                         <label for="email">Login Username:</label>
                          <?php echo form_input($email);?>
                         <?php echo form_error('email','<p class="error">', '</p>'); ?>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">                        
                        <label for="phone">Phone</label>
                        <?php echo form_input($phone);?>
                       <?php echo form_error('phone','<p class="error">', '</p>'); ?>   
                      </div> 
                    </div>
                    <div class="col-md-6" id="hide_on_admin">
                       <div class="form-group">
                        <label for="zone">Select Zone</label>
                        <select name="zone" id="zone" class="form-control">
                          <option value="">Select</option>
                          <?php foreach($zones as $zone): ?>
                            <?php if($this->ion_auth->is_admin()): ?>                            
                              <option value="<?php echo $zone->id ?>" <?php echo set_select('zone',  $zone->id); ?>><?php echo $zone->zone_name ?></option>                            
                              <?php else: ?>
                                <?php if($zone->id == $this->ion_auth->user()->row()->zone_id): ?>
                                  <option value="<?php echo $zone->id ?>" <?php echo set_select('zone',  $zone->id); ?>><?php echo $zone->zone_name ?></option>
                                <?php endif; ?>
                            <?php endif; ?>
                          <?php endforeach ?>
                        </select>
                        <?php echo form_error('zone','<p class="error">', '</p>'); ?>
                        </div>  
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">                       
                       <label for="password">Password</label>
                        <?php echo form_input($password);?>
                       <?php echo form_error('password','<p class="error">', '</p>'); ?>   
                      </div>
                    </div>                        
                    <div class="col-md-6">
                      <div class="form-group">                      
                      <label for="password_confirm">Confirm Password</label>
                      <?php echo form_input($password_confirm);?>
                       <?php echo form_error('password','<p class="error">', '</p>'); ?>   
                      </div> 
                    </div>
                    <?php if ($this->ion_auth->is_admin()): ?>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Select Group</label><br>                          
                          <?php foreach($groups as $data): ?>
                            <?php if($data->id !== '3'): ?>
                            <label class="ml-2" for="types<?php echo $data->id ?>">
                              <input type="radio" class="user_types" id="types<?php echo $data->id ?>" name="group[]" value="<?php echo $data->id ?>" required>
                              <?php echo $data->description ?>                              
                            </label>
                          <?php endif; ?>
                          <?php endforeach ?>                      
                        </div>
                      </div>
                    <?php endif ?>            
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <button class="btn btn-success" id="submit_user">Submit</button>                   
                  </div>
                </div>
                <?php echo form_close();?>
            </div>
         </div>
      </div>

<?php $this->load->view('templates/footer') ?>
<script type="text/javascript">
    
    $(document).ready(function() {

      $(".user_types").on('click',() => {


          if($("input[type='radio'].user_types").is(':checked')) {
            var type = $("input[type='radio'].user_types:checked").val();
            
            if(type == 1 || type == 4) {
              $("#zone").attr('required',false);
              $("#zone").val('');
              $("#hide_on_admin").hide();
            }
            else {
              $("#zone").attr('required',true);              
              $("#hide_on_admin").show();
            }

        }
      })

    })

</script>