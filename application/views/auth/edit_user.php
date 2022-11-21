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
            <div class="col-md-6">
              <div class="form-group">
               <?php echo lang('edit_user_fname_label', 'first_name');?>
               <?php echo form_input($first_name);?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                 <label for="father_name">Father Name</label>
                 <?php echo form_input($father_name);?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">          
               <label for="last_name">Select SurName</label>
               <select name="last_name" id="last_name" class="form-control">
                  <option value="">Select</option>
                  <?php foreach($surnames as $surname): ?>
                  <option value="<?php echo $surname->id ?>" <?php echo $surname->id == $user->last_name ? 'selected' : '' ?>><?php echo $surname->surname ?></option>
                  <?php endforeach ?>
               </select>
              </div>
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
               <?php echo lang('edit_user_phone_label', 'phone');?> 
               <?php echo form_input($phone);?>
              </div>
            </div>            
            <div class="col-md-6" id="hide_on_admin">
               <div class="form-group">
                  <label for="zone">Select Zone</label>
                  <select name="zone" id="zone" class="form-control">
                     <option value="">Select</option>
                     <?php foreach($zones as $zone): ?>
                     <?php if($this->ion_auth->is_admin()): ?>
                     <option value="<?php echo $zone->id ?>" <?php echo $zone->id == $user->zone_id ? 'selected' : '' ?>><?php echo $zone->zone_name ?></option>
                     <?php else: ?>
                     <?php if($zone->id == $this->ion_auth->user()->row()->zone_id): ?>
                     <option value="<?php echo $zone->id ?>" <?php echo $zone->id == $user->zone_id ? 'selected' : '' ?>><?php echo $zone->zone_name ?></option>
                     <?php endif; ?>
                     <?php endif; ?>                                    
                     <?php endforeach ?>
                  </select>
                  <?php echo form_error('zone','<p class="error">', '</p>'); ?>
               </div>
            </div>
            <div class="col-md-6">
              <div class="form-group"> 
               <?php echo lang('edit_user_password_label', 'password');?> 
               <?php echo form_input($password);?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group"> 
               <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?>
               <?php echo form_input($password_confirm);?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
               <?php if ($this->ion_auth->is_admin()): ?>
                 <label>Select Group</label><br>
                 <?php foreach ($groups as $group):?>
                  <?php if($group['id'] !== '3'): ?>
                   <label class="checkbox ml-2">
                   <?php
                      $gID=$group['id'];
                      $checked = null;
                      $item = null;
                      foreach($currentGroups as $grp) {
                          if ($gID == $grp->id) {
                              $checked= ' checked="checked"';
                          break;
                          }
                      }
                      ?>
                   <input type="radio" class="user_types" name="groups" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
                   <?php echo htmlspecialchars($group['description'],ENT_QUOTES,'UTF-8');?>
                   </label>
                   <?php endif ?>
                 <?php endforeach?>
               <?php endif ?>
              </div>
            </div>
            <div class="col-md-12">
              <input type="hidden" id="check_admin_noti_id" value="<?php echo $user->group_id ?>">
               <?php echo form_hidden('id', $user->id);?>
               <?php echo form_hidden($csrf); ?>
               <?php echo form_submit('submit', 'Update',array('class'=>'btn btn-success'));?>
            </div>
         </div>
         <?php echo form_close();?> 
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>

<script type="text/javascript">
  
  $(document).ready(function() {
     var type = $("#check_admin_noti_id").val();
              
      if(type == 1 || type == 4) {
        $("#zone").attr('required',false);
        $("#zone").val('');
        $("#hide_on_admin").hide();
      }
      else {
        $("#zone").attr('required',true);              
        $("#hide_on_admin").show();
      }

      $(".user_types").on('click',() => {

        if($("input[type='radio'].user_types").is(':checked')) {
          var ctype = $("input[type='radio'].user_types:checked").val();
            
          if(ctype == 1 || ctype == 4) {
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