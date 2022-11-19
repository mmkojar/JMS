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
                   <input type="text" class="form-control" name="title" id="title" value="<?php echo $sms->title ? $sms->title : '' ?>" required>
                 </div>
              </div>
              <div class="col-md-12">
                 <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="5"><?php echo $sms->content ? $sms->content : '' ?></textarea>
                 </div>
                 <p class="text-danger">Available Variables: [USR_NAME] [AMT] [DATE] [YEAR] [RCP_NO] [COLL_NAME]</p>
                 <!-- <p class="text-danger">Add &lt;br&gt; for line break <p> -->
              </div>
              <div class="col-md-12">
                 <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">Select</option>
                        <option value="1" <?php echo ($sms->status == '1' ? 'selected' : '') ?>>Active</option>
                        <option value="0" <?php echo ($sms->status == '0' ? 'selected' : '') ?>>Inactive</option>
                    </select>
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

<script src="https://cdn.tiny.cloud/1/1a5w4ka884d2keahn36xltwaif9zp4pckvbu3ht2utanno1i/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>tinymce.init({selector:'textarea',content_style: "body { line-height: 1; }", });</script>