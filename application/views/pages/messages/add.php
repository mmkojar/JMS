<?php $this->load->view('templates/header'); ?>
<script type="text/javascript" src="http://vaani.neechalkaran.com/Scripts/google_jsapi.js"></script>
<script type="text/javascript">
  google.load("elements", "1", {packages: "transliteration"});
</script> 
<script>
function OnLoad() {                
     var options = {
          sourceLanguage:
              google.elements.transliteration.LanguageCode.ENGLISH,
          destinationLanguage:
              [google.elements.transliteration.LanguageCode.GUJARATI],
          shortcutKey: 'ctrl+g',
          transliterationEnabled: true
      };

    var control = new google.elements.transliteration.TransliterationControl(options);
   control.makeTransliteratable(["message_gj"]);

    document.getElementById("message_en").addEventListener("keyup", translate);
    function translate() {
          document.getElementById("message_gj").value = document.getElementById("message_en").value;
    }
} //end onLoad function

google.setOnLoadCallback(OnLoad);
</script> 
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
                         <option value="<?php //echo ($key) ?>"><?php //echo $value ?></option>
                        <?php //endforeach ?>
                    </select> -->
                    <input type="text" class="form-control" name="sender_name" id="sender_name" value="<?php echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->surname ?>" readonly>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                  <label for="title">Title</label>
                   <input type="text" class="form-control" name="title" id="title" required>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label>Message</label>
                    <input type="text" class="form-control" name="message_en" id="message_en" required>
                 </div>
              </div>
           </div>
           <?php echo form_hidden($csrf); ?>
           <input type="hidden" class="form-control" name="message_gj" id="message_gj">
           <?php echo form_submit('submit', 'Submit',array('class'=>'btn btn-success'));?>
         <?php echo form_close();?>                                                      
      </div>
   </div>
</div>
<?php $this->load->view('templates/footer') ?>