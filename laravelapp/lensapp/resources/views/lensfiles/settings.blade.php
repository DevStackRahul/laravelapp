<!-- Settings form-->
<div class="Custom-CollectionContainer Settings-Page">
     <div class="edit-inner-container">
          <div class="Editnavigation-header">
              
              <div class="edit-PageTitle">
                  <h2 class="create-titletxt">Lens <span style="color: #345FF1;">Settings</span></h2></span>
              </div>
          </div>
     </div> 
     
     <form method="GET" id="addSettings">
     <div class="formAreahere settingmainCat-forms">
           
          <div class="form-errors"></div>  
          <div class="form-success"></div>
        
        <div class="formset-data"></div>
        
                  
     <!--   <div class="coldetail-container">
            <div class="collection-MainFieldarea">
            <div class="colscard-sub-heading">
            <p class="colscard-heading">Pupillary Distance (PD)</p>
            </div>
            <div class="colscard-sub-heading">
                <p class="colsubs-heading">Do you require customers to enter their PD for prescription orders?</p>
            </div>
            
          <div class="choiceinput-fields formSettings">
        
        <div class="singleradioChoice1 customCreates_Choices">
            <div class="frist-checkHeading">
               <p>When customer enters their prescription manually</p>
            </div>
            
        <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input checked type="checkbox" name="pdValueManual" class="radiomainSetting-fields pdValueManual" value="true" >
              </span></span>
              <span class="pre-names">PD is required.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueManual" class="pdValueManual radiomainSetting-fields" value="false" >
              </span></span>
              <span class="pre-names">PD is optional.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueManual" class="pdValueManual radiomainSetting-fields" value="false" >
              </span></span>
              <span class="pre-names">Do not show PD dropdown.</span>
              </label>
              
              </div>
              
                      <div class="singleradioChoice2 customCreates_Choices">
            <div class="frist-checkHeading">
               <p>When customer uploads their prescription</p>
            </div>
            
        <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueUpload" class="pdValueUpload radiomainSetting-fields" value="false" >
              </span></span>
              <span class="pre-names">PD is required.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input checked type="checkbox" name="pdValueUpload" class="radiomainSetting-fields pdValueUpload" value="true" >
              </span></span>
              <span class="pre-names">PD is optional.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueUpload" class="pdValueUpload radiomainSetting-fields" value="false" >
              </span></span>
              <span class="pre-names">Do not show PD dropdown.</span>
              </label>
              
              </div>
              
              
             <div class="customCreates_Choices">
            <div class="frist-checkHeading">
               <p>When customer chooses Reading prescription type</p>
            </div>
            
        <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueReading" class="radiomainSetting-fields pdValueReading" value="false" >
              </span></span>
              <span class="pre-names">PD is required.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input checked type="checkbox" name="pdValueReading" class="radiomainSetting-fields pdValueReading" value="true" >
              </span></span>
              <span class="pre-names">PD is optional.</span>
              </label>
              
              <label class="radio-sett prescriptionSettings" for="prescription-type-checkbox">
         <span class="radiochoice-check-outer"><span class="radiochoice-check-inner">
        <input type="checkbox" name="pdValueReading" class="pdValueReading radiomainSetting-fields" value="false" >
              </span></span>
              <span class="pre-names">Do not show PD dropdown.</span>
              </label>
              
              </div>
              
              
              
          </div>
          
          
          
            </div>
        </div>
        -->
                 
     
          

            
        
          
         <div class="row form-btns">
      
    <div class="collectionSave col-12 form-save primary-button-container"><button type="submit" id="Savesetting-btn" class="collSaveButton formsavedbtns Polaris-Button Polaris-Button--primary">
      <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save Changes</span></span></button>
      </div>
      </div>      
     </div>
     </form>
     
     
</div>
@section('scripts')
    @parent
<script>
   $(".customCreates_Choices .mainSetting-fields").on('click', function() {
     //alert('dfsdf');
   if($(this).is(":checked")){
     // show checked
     $(this).attr('value', 'true');
  } else {
    $(this).attr('value', 'false');
    $(this).removeAttr('checked');
  }
 
});
 
/*//select only one main cat pdValueManual



 $(".pdValueReading").on('click', function() {
     //alert('dfsdf');
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
     // show checked
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
 
});


 $(".pdValueUpload").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
     // show checked
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
 
});*/
</script>
 @endsection