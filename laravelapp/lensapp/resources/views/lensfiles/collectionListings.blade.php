<!-- collection listings -->
<!--below div class 'lens-get-started' is in use with jquery -->
<div class="lens-get-started">
    <div class="lens-desc-content">
    <div class="Polaris-Banner__Heading" id="Banner52Heading"><p class="Polaris-Heading"><i class="fa fa-check-circle"></i> Pro Tip: Create a clear lens collection for eyeglasses and a dark lens collection for sunglasses.</p></div>
    <div class="Polaris-Banner__Content" id="Banner52Content">
        <p class="text-left">In the "Eyeglasses" lens collection, you can add Clear, Blue-Light-Blocking and Photochromic (Light-Responsive) lenses. "Sunglasses," on the other hand, can include Non-Polarized Sunglasses Lenses and Polarized Sunglasses Lenses.</p>
    <p class="text-left">Most leading online eyewear stores keep separate checkout flows for eyeglasses and sunglasses, and we recommend following their example.</p>
    </div>
    </div>
    
    <div class="row custom-lensColarea create-collection">
        <div class="col-sm-8 custom-col1-lens">
            <h1 class="collection-heading">Lens <span style="color: #345FF1;">Collections</span></h1>
            
        </div>
        <div class="col-sm-4 primary-button-container">
            
            <!--below btn class 'createColbtn' is in use with jquery -->
            <button type="button" class="createColbtn Polaris-Button Polaris-Button--primary">
            <span class="Polaris-Button__Content">
                <span class="Polaris-Button__Text">Create Lens Collection</span>
                </span>
            </button>
            </div>
    </div>
    
    <!--- collection list here-->
    <div class="Collection_Polaris-Card">
        <!--below div class 'CollectionCard__Section' is in use with jquery -->
        <div class="CollectionCard__Section">
          
        </div></div>
        
</div>
    
<!--Include Files of the create and edit Collection forms -->
 @include('lensfiles.createForm.createFormStepOne')
 @include('lensfiles.createForm.createFormStepTwo')
 @include('lensfiles.createForm.createFormStepThree')
 
 @include('lensfiles.editForms.editCollectionForm')
 @include('lensfiles.editForms.editAssignProductForm')
<!--End File includation -->