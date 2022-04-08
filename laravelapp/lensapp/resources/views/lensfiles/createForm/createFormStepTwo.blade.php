<!-- Add lenses form -->
<div class="Custom-CollectionContainer AddLensesForm hideData">
    <div class="edit-inner-container">
          <div class="Editnavigation-header">
              
              <div class="createNaviContent Editnavigation-Contents">
                 <span class="arrow-nav"><i class="fa fa-long-arrow-left"></i> </span><span class="EditBreadcrumbs__text">Back To Lens Collections</span>
              </div>
              
               <div class="edit-PageTitle">
                  <span class="edit-Titletxt"><span class="form-numbers">Step 2</span> <h2 class="create-titletxt">Add <span style="color: #345FF1;">Lenses</span></h2></span>
              </div>
              
          </div>
     </div>
   
<form class="" id="createLenseForm" method="GET">
      <div class="mylensform-headers"><p class="tt">Add Lense to the Single Vision</p></div>
      <div class="formAreahere lens-containers">
          <div class="lenserr error-msgs"></div>
         
         <div class="lens-formStart">
            
         
          <div class="add_lenses_FormArea"><div class="add_lenses_innerArea">
              <div class="hidden-inputFields"></div>
               <div class="row">
                   
               <div class="col-7 gp-lens-title">
               <span class="lensgroup-name"></span><span> Group 1</span></div>
               <div class="col-12 lens-title-col"><div class="display-pre-Heading">
               <label class="display_titles" for="pre-titles">Display Title
               <span class="display-title-inputFields"><input type="text" name="lens_title1" class="txt-inputs" placeholder="Regular clear lens">
               </span>
               </label></div></div>
              </div>
           
            <!--- repeater 1 -->
            <div class="repeater">
            <div class="sortableContainer-table lens-table"><div class="table_of_lens_add"> <div><table class="table table-borderless">
                <thead>
                <tr class="lens-table-header">
                <th><strong>Lens Name</strong></th><th><strong>Price</strong></th><th><strong>Rx Type</strong></th><th></th>
                </tr>
                </thead>
                 <tbody data-repeater-list="clearlens-group" class="lens-body">
                 <tr data-repeater-item="">
                <input type="hidden" name="id" id="clearlens-id">
                <td class="lens-name"><input type="text" name="lens_names"></td>
              
                <td class="lens-price"><input type="text" name="lensprice" placeholder="100"></td>
                <td class="lens-Rx-type"><input disabled type="text" name="rxtype" placeholder="single-vision" value="single-vision"></td>
                <td class="lens-deleteIcon"><button type="button" data-repeater-delete=""><i class="fa fa-trash-o"></i></button></td>
                </tr></tbody><input data-repeater-create="" class="add-btn" type="button" value="+Add"></table>
               
                </div></div>
                </div>
                </div>
                <!-- end repeater 1-->
               
                </div></div>
               
               <div class="add_lenses_FormArea"><div class="add_lenses_innerArea">
               <div class="row">
               <div class="col-7 gp-lens-title">
               <span class="lensgroup-name"></span><span> Group 2</span></div>
               <div class="col-12 lens-title-col"><div class="display-pre-Heading">
               <label class="display_titles" for="pre-titles">Display Title
               <span class="display-title-inputFields"><input type="text" name="lens_title2" class="txt-inputs" placeholder="Anti Blue Light Lens">
               </span>
               </label></div></div>
               </div>
           
            <!-- repeater 2-->
            <div class="repeater">
            <div class="sortableContainer-table lens-table"><div class="table_of_lens_add"><div><table class="table table-borderless">
                <thead>
                <tr class="lens-table-header">
               <th><strong>Lens Name</strong></th><th><strong>Price</strong></th><th><strong>Rx Type</strong></th><th></th>
                </tr>
                </thead>
                 <tbody data-repeater-list="antilens-group" class="lens-body">
                 <tr data-repeater-item="">
                <input type="hidden" name="id" id="anti-lensid">
                <td class="lens-name"><input type="text" name="antilens_names"></td>
                
                <td class="lens-price"><input type="text" name="antilensprice" placeholder="100"></td>
                <td class="lens-Rx-type"><input disabled type="text" value="single-vision" name="antirxtype" placeholder="single-vision"></td>
                <td class="lens-deleteIcon"><button type="button" data-repeater-delete=""><i class="fa fa-trash-o"></i></button></td>
                </tr></tbody><input data-repeater-create="" class="add-btn" type="button" value="+Add"></table>
               
                </div></div>
                </div>
                </div>
                <!-- end repeater 2-->
               
                </div></div>
               
               
                </div>
         
           <div class="row form-btns">
     
    <div class="collectionSave col-12 form-save primary-button-container"><button type="submit" id="lensaved-btn" class="collSaveButton lensformsavedbtns Polaris-Button Polaris-Button--primary">
      <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save &amp; Continue</span></span></button>
      </div>
      </div>
      </div>
         
 </form>
 </div>
 <!-- Add Lense Form End-->
         
         