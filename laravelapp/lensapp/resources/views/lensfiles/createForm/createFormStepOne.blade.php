<!-- Create Collection Form-->
<div class="Custom-CollectionContainer create-collectionPage hideData">
      
       <div class="edit-inner-container">
          <div class="Editnavigation-header">
              <div class="createNaviContent Editnavigation-Contents">
                <span class="arrow-nav"><i class="fa fa-long-arrow-left"></i> </span><span class="EditBreadcrumbs__text">Back To Lens Collections</span>
              </div>
              
              <div class="edit-PageTitle">
                  <span class="edit-Titletxt"><span class="form-numbers">Step 1</span> <h2 class="create-titletxt">Create <span style="color: #345FF1;">Collection</span></h2></span>
              </div>
          </div>
     </div>  
     
    
     <form id="createCollectionForm" method="GET">
      
      <div class="formAreahere newcreate-forms">
        
       
        
        <div class="coldetail-container">
            <div class="collection-MainFieldarea">
            <div class="colscard-sub-heading">
            <p class="colscard-heading">Collection Title</p>
            </div>
            <div class="colscard-sub-heading">
                <p class="colsubs-heading">This title is for internal use and not visible to customers.</p>
            </div>
            <div class="Form-fields">
            <input id="collec-titles" placeholder="Eg: Select Lenses" class="form-txtfileds" name="collection_title" required>
            </div>
            </div>
        </div>
        
        
            
   

    
  <div id="prescripdiv" class="coldetail-container detailContainers prescription-Det">
    
     <div class="Second-FieldsecHeading">Choose Prescription Types</div>
     <p class="colsubs-heading">Select the types of prescription lenses you will add to this lens collection</p>
       <div class="CatPreType-checkbox"></div>
       
        <div class="colerrs error-msgs"></div>
</div>
       
<div class="coldetail-container detailContainers">
        <div class="display-settings-prescripType">
         <div class="field-smalldescription">
           <h5 class="field-descriptionHeading">Prescription Types Display Settings</h5>
           <p class="field-descripContent">You can add a display title or description for the selected prescription type, which are what customers see.</p>
             </div>
       
           <div style="margin-bottom: 10px;" class="display-pre-Heading">
               <span class="display-title-inputFields"><input type="text" name="category_title" class="txt-inputs" placeholder="Display Title">
               </span>
               </div>
               
           <div class="display-pre-Heading">
               <span class="display-title-inputFields">
                <textarea type="text" name="category_desc" class="txt-inputs" style="height: 84px;" placeholder="Description"></textarea>
                 </span>
            </div>
        
    </div>
   
</div>
 
  <div class="row form-btns">
      
    <div class="collectionSave col-12 form-save primary-button-container"><button type="submit" id="Savecollect-btn" class="collSaveButton formsavedbtns Polaris-Button Polaris-Button--primary">
      <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save & Continue</span></span></button>
      </div>
      </div>
      
      
</div>
</form>
</div>

<!--end create collection -->