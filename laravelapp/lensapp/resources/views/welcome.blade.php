@extends('shopify-app::layouts.default')

@section('content')
    <!-- You are: (shop domain name) -->
    <!--<p>You are: {{ Auth::user()->name }}</p>-->
   
<?php $app_url = config('app.url'); ?>
<?php  //$a = $shopDomain ?? Auth::user()->name; 
//echo $app_url;
?>
<?php $date = new DateTime();
$time = $date->getTimestamp(); ?>


<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="<?php echo $app_url; ?>/resources/css/custom.css" rel="stylesheet" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="<?php echo $app_url; ?>/resources/js/custom.js"></script>

 
 



<!--NavBar Start -->
<div class="cgt-desc">
<ul class="tabs">
    <li class="custom-wel"><a href="#tab-1" class="menu-items-pg">Welcome</a></li>
    <li class="lenstab"><a href="#tab-2" class="menu-items-pg">Lens Collection</a></li>
    <li><a href="#tab-3" class="menu-items-pg">Orders</a></li>
    <li><a href="#tab-4" class="menu-items-pg">Settings</a></li>
    <li><a href="#tab-5" class="menu-items-pg">Contact Us</a></li>
    <li><a href="#tab-6" class="menu-items-pg">FAQ</a></li>
   
  </ul>
 
 

 
<div class="cgt-content">
    <div id="tab-1">
@include('lensfiles.dashboard')
  </div>
 
    <div id="tab-2">
       @include('lensfiles.collectionListings')

  </div>
  <div id="tab-3">
@include('lensfiles.orders')
  </div>
  <div id="tab-4">
 @include('lensfiles.settings')
  </div>
  <div id="tab-5">
 @include('lensfiles.contactus')
  </div>
  <div id="tab-6">
    @include('lensfiles.faq')
  </div>
  </div>  
 </div>

<!--EndNavbar -->

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var TitleBar = actions.TitleBar;
        var Button = actions.Button;
        var Redirect = actions.Redirect;
        var titleBarOptions = {
            title: 'Select Lenses App',
        };
        var myTitleBar = TitleBar.create(app, titleBarOptions);
    </script>
   
    <script>
    /* navbar scripts start */
     var Shopname="{{ $shopDomain ?? Auth::user()->name }}";
     
     $(document).ready(function() {
         
         
       
     $('ul.tabs').each(function(){
      var active, content, links = $(this).find('a');
      active = links.first().addClass('active');
      content = $(active.attr('href'));
      links.not(':first').each(function () {
        $($(this).attr('href')).hide();
      });
      $(this).find('a').click(function(e){
       
        active.removeClass('active');
        content.hide();
        active = $(this);
        content = $($(this).attr('href'));
        active.addClass('active');
        content.show();
         /***function for refresh lenslisting page after inactive start ***/
if ($("li.lenstab a").hasClass("active")){
    // do nothing
}else{
   
    var mmform = document.querySelector('#createCollectionForm');
        mmform.reset();
         $('.lens-get-started').removeClass('hideData');
        
      //edit coll
      if ($('.edit-collectionPage').hasClass('hideData')){
          //do nothing
      }else{
          $('.edit-collectionPage').addClass('hideData');
      }
      
      // EditAssignProducts
      if ($('.EditAssignProducts').hasClass('hideData')){
          //do nothing
      }else{
          $('.EditAssignProducts').addClass('hideData');
      }
//create collection to back
if ($(".create-collectionPage").hasClass("hideData")){
    // do nothing
}else{
 $('.create-collectionPage').addClass('hideData');
 getCollections(Shopname);
}

//add lens to back
if ($(".AddLensesForm").hasClass("hideData")){
    // do nothing
}else{
 $('.AddLensesForm').addClass('hideData');
 getCollections(Shopname);
}

//assign product to back
if ($(".AssignProducts").hasClass("hideData")){
    // do nothing
}else{
 $('.AssignProducts').addClass('hideData');
 getCollections(Shopname);
}
    
}


/*** function for refresh lenslisting page after inactive end ***/
        
        return false;
        
      });
    });
  });
 
   /* navbar scripts end */
 
 
  /***create shop folder in pull dir code ***/
function addAsset(Shopname){
$.ajax({
type: 'GET',
url: '/lensapp/addassetfile?shops='+Shopname,
success: function(response){
    console.log(response);
    
    
}
});
}

addAsset(Shopname);

  /*** End code create shop folder ***/
 
  /*** Start fetch collection code ***/
  function getCollections(Shopname){
   $.ajax({
type: 'GET',
url: '/lensapp/fetchCollections?shops='+Shopname,
success: function(response) {
//console.log(response);
var colHtml = response;
$('.CollectionCard__Section').html(colHtml);
getSetting(Shopname);
}
});
        }
 
   

  getCollections(Shopname);
  /*** End fetch collection code ***/
 
 
  /** editcollection code**/
  function editcollection(colid){
      var edit_colid = colid;
        console.log(Shopname);
        $('.lens-get-started').addClass('hideData');
        $('.edit-collectionPage').removeClass('hideData')
        
         $.ajax({
type: 'GET',
data: {'editcol' : edit_colid}, 
url: '/lensapp/editCollections?shops='+Shopname,
success: function(response) {
    $('.updatedatas').html(response);
    
    
    //select only one main cat
 $(".maincatcheckbox-fields").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
      // show sub cat for select main that has sub true
  var ISsubITEM =  $(this).attr('data-subcat');
  if(ISsubITEM == 'true'){
      $('.choose-subcats').removeClass('hideData');
  }else{
     $('.choose-subcats').addClass('hideData');
     $('.subcategory-field').prop('checked', false);
  }
     
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
 
});
    
}
});
        
     
     
  }
 
  $('.Editnavigation-Contents').on('click', function(){
     $('.lens-get-started').removeClass('hideData');
        $('.edit-collectionPage').addClass('hideData')
       
  });
 
  /**create collection popup  **/
  $('.createColbtn').on('click', function(){
      getPrescriptionTypes(Shopname);
     $('.lens-get-started').addClass('hideData');
        $('.create-collectionPage').removeClass('hideData')  
  });
 
 
 // form to back to dashboard of lenses function
$('.createNaviContent').on('click', function(e){
      e.preventDefault();
      var mmform = document.querySelector('#createCollectionForm');
        mmform.reset();
         $('.lens-get-started').removeClass('hideData');
        
      
//create collection to back
if ($(".create-collectionPage").hasClass("hideData")){
    // do nothing
}else{
 $('.create-collectionPage').addClass('hideData');
 getCollections(Shopname);
}

//add lens to back
if ($(".AddLensesForm").hasClass("hideData")){
    // do nothing
}else{
 $('.AddLensesForm').addClass('hideData');
 getCollections(Shopname);
}

//assign product to back
if ($(".AssignProducts").hasClass("hideData")){
    // do nothing
}else{
 $('.AssignProducts').addClass('hideData');
 getCollections(Shopname);
}

 // EditAssignProducts
      if ($('.EditAssignProducts').hasClass('hideData')){
          //do nothing
      }else{
          $('.EditAssignProducts').addClass('hideData');
      }
});

// end function of back to dashboards from forms


  /* enable input fields */

    $('.CatPreType-checkbox .choice-check-inner input[type="checkbox"]').on('click', function(){
        var myval = $(this).attr('data-title');
            $('.msglens-type-selection').addClass('hideData');
            $('.fileds-areaactive').removeClass('hideData');
            $('.lens-group-heading').text(myval);
            $('.group-name').text(myval);
        });
        //else{
        //     $('.fileds-areaactive').addClass('hideData');
        //    $('.msglens-type-selection').removeClass('hideData');
       // }
       
       
  /* fetch and append categorys in the create collection form  */
  function getPrescriptionTypes(Shopname){
   $.ajax({
type: 'GET',
url: '/lensapp/fetchPrescriptionTypes?shops='+Shopname,
success: function(response) {
// console.log(response);
var preTypeHtml = response;
   $('.CatPreType-checkbox').html(preTypeHtml);

//select only one main cat
 $(".maincatcheckbox-fields").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
      // show sub cat for select main that has sub true
  var ISsubITEM =  $(this).attr('data-subcat');
  if(ISsubITEM == 'true'){
      $('.choose-subcats').removeClass('hideData');
  }else{
     $('.choose-subcats').addClass('hideData');
     $('.subcategory-field').prop('checked', false);
  }
     
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
 
});

}
});
}
       
        // save newly create collection with prescription type
       
   
        $('body').on('submit', '#createCollectionForm', function(e){
            e.preventDefault();
           
            $('#Savecollect-btn').addClass('disable-btn');
        $('#Savecollect-btn').attr('disabled');
            var formData = $('#createCollectionForm').serialize();
            $.ajax({
url: '/lensapp/saveDefaultCollections?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {

   $('#Savecollect-btn').removeClass('disable-btn');
                   $('#Savecollect-btn').removeAttr('disabled');
                     
                      if(response.indexOf('Sent') > -1) {
                      var vall = '<div class="error-txt"><span class="errval">Please choose a Prescription Type!</span></div>';
                      $('.prescription-Det').addClass('form-errorApears');
                      $('.colerrs').html(vall);
                      
                     $('html, body').animate({
        scrollTop: $("#prescripdiv").offset().top
    }, 1);
                      }else{
        var preTypeHtml = response;
                    $('.mylensform-headers').html(preTypeHtml);
                    var lenstype = $('.addLensform-headers').attr('data-lenstype');
                    var saved_type_id = $('.addLensform-headers').attr('data-category-id');
                                var saved_collectionID = $('.addLensform-headers').attr('data-collection-id');
                                var hiddeninputs = '<input type="hidden" name="collectionID" value="'+saved_collectionID+'"><input type="hidden" name="categoryID" value="'+saved_type_id+'"">';
                    $('.hidden-inputFields').html(hiddeninputs);
                    $('.lensgroup-name').text(lenstype);
   $('.create-collectionPage').addClass('hideData');
   $('.AddLensesForm').removeClass('hideData');
 
   
                      }
}

});
        });
       
        // delete collection
        function deletecollection(col_id){
            $.ajax({
url: '/lensapp/deleteCollections?shops='+Shopname,
type: 'GET',
data: {'delcol' : col_id},
success: function(response) {
console.log(response);

},
complete: function() {
getCollections(Shopname);
}
});
        }
       
       
        /* submit lens form data */
         $('body').on('submit', '#createLenseForm', function(e){
            e.preventDefault();
           
                  $('#lensaved-btn').addClass('disable-btn');
        $('#lensaved-btn').attr('disabled');
            var formData = $('#createLenseForm').serialize();
           
            $.ajax({
url: '/lensapp/saveLenes?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {
$('#lensaved-btn').removeClass('disable-btn');
        $('#lensaved-btn').removeAttr('disabled');
        if(response.indexOf('Sent') > -1) {
                     $('.add_lenses_FormArea').addClass('form-errorApears');
                      var vall = '<div class="error-txt"><span class="errval">Please Add a lens!</span></div>';
                      $('.lenserr').html(vall);
                      }else{
                          showProductBox(Shopname);
    //console.log(response);
      $('.AssignProducts').removeClass('hideData');
   $('.AddLensesForm').addClass('hideData');
 
   var preTypeHtml = response;
                    $('.myassign-headers').html(preTypeHtml);
                   
                    var saved_type_id = $('.assign-headers').attr('data-category-id');
                                var saved_collectionID = $('.assign-headers').attr('data-collection-id');
   
   var hiddeninputs = '<input type="hidden" name="collectionID" value="'+saved_collectionID+'"><input type="hidden" name="categoryID" value="'+saved_type_id+'"">';
                    $('.passignHidden').html(hiddeninputs);
   
                      }

}

});
});


/** edit prodcut assign fetch list**/

function EditshowProductBox(Shopname){
     var saved_collectionID = $('.editassignhead').attr('data-collection-id');
    //$('body').addClass('customOverLays');
   // $('.show-StoreListOfAllProduct').removeClass('hideData');
    
    /*$.ajax({
url: '/lensapp/Editfetchallproducts?shops='+Shopname,
type: 'GET',
data: {'editcol_id', saved_collectionID},
success: function(response) {
 var alldata = response;
 $('.editmainpopup-Bodyst').html(alldata);
 */
 /* first show 10 products in tb */
 /*
 var totalproducts = $('.my-tb-count').attr('data-totals');
 if(totalproducts <= 10){
    // $('.load-btnss').addClass('hideData');
 }else{
   $('.load-btnss').removeClass('hideData');  
 }
 */
}

/** end code **/


/** assign product form**/
function showProductBox(Shopname){
    
   // $('body').addClass('customOverLays');
   // $('.show-StoreListOfAllProduct').removeClass('hideData');
    $.ajax({
url: '/lensapp/fetchallproducts?shops='+Shopname,
type: 'GET',
success: function(response) {
 var alldata = response;
 $('.mainpopup-Bodyst').html(alldata);
 
 /* first show 10 products in tb */
 
 var totalproducts = $('.my-tb-count').attr('data-totals');
 if(totalproducts <= 10){
    // $('.load-btnss').addClass('hideData');
 }else{
   $('.load-btnss').removeClass('hideData');  
 }
 $('#more-entries5').click(function(){
	var a = 10;
	var totalLength = $('.finalist_tables tbody tr').length;
	var b = $('.load-more-data').attr('mainclick');
	$('.load-more-data').attr('mainclick', parseInt(b)+1);
	var c = b * a;
	$('.finalist_tables tbody tr').each(function(){
		var count = $(this).attr('data-count');
		if(count <= c){
			if(count == totalLength){
				$('#more-entries5').addClass('hideData');
				$('#more-entries-minus').removeClass('hideData');
			}
			$(this).removeClass('hideData');
		}else{
			$(this).addClass('hideData');
		}
	});
});

$('#more-entries-minus').click(function(){
	var a = 10;
	var totalLength = $('.finalist_tables tbody tr').length;
	var b = $('.load-more-data').attr('mainclick');
	$('.load-more-data').attr('mainClick', parseInt(b)-1);
	b = $(this).attr('mainclick');
	var c = b * a;
	$('.finalist_tables tbody tr').each(function(){
		var count = $(this).attr('data-count');
		if(count <= c){
			if(count == totalLength){
				$('#more-entries5').addClass('hideData');
				$('#more-entries-minus').removeClass('hideData');
			}else if(c == a){
				$('#more-entries5').removeClass('hideData');
				$('#more-entries-minus').addClass('hideData');
			}
			$(this).removeClass('hideData');
		}else{
			$(this).addClass('hideData');
		}
	});
});


}
});
}
/** end form**/
   
   /***submit assign product form data ***/
 
$('body').on('submit', '#assignProductForm', function(e){
            e.preventDefault();
            var formData = $('#assignProductForm').serialize();
            $.ajax({
url: '/lensapp/addProductTocollection?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {
                      
                      if(response.indexOf('Sent') > -1) {
                      var vall = '<div class="error-txt"><span class="errval">Please add atlest one product!</span></div>';
                      $('.mainpopup-Bodyst').addClass('form-errorApears');
                      $('.assignerror').html(vall);
                      }else{
                         console.log(response);
                         $('.AssignProducts').addClass('hideData');
                          var mmform = document.querySelector('#assignProductForm');
                  mmform.reset();
         $('.lens-get-started').removeClass('hideData');
   getCollections(Shopname);
                      }
}
});
});
     
     /** end submission **/  
       
        /***  contact form Send Email  ***/
$('body').on('submit', '#contactusForm', function(e){
e.preventDefault();
  
var formData = $('#contactusForm').serialize();
console.log(formData);
$.ajax({
url: '/lensapp/sendEmail?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {
 
   if(response.indexOf('Sent') > -1) {
   
    $('.sent-row').removeClass('hideData');
     
   }else{
       $('.error-row').removeClass('hideData');
   }
   

}
});

});


/***submit edit form Step 1***/
$('body').on('submit', '#editCollectionForm', function(e){
            e.preventDefault();
            var formData = $('#editCollectionForm').serialize();
            $.ajax({
url: '/lensapp/updateCollectionsOne?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {
                      
                      if(response.indexOf('Sent') > -1) {
                      var vall = '<div class="error-txt"><span class="errval">Please choose a Prescription Type!</span></div>';
                      $('.prescription-Det').addClass('form-errorApears');
                      $('.colerrs').html(vall);
                      
                     $('html, body').animate({
        scrollTop: $("#prescripdiv").offset().top
    }, 1);
                      }else{
        var preTypeHtml = response;
                    $('.editmyassign').html(preTypeHtml);
                    
                    
                                var saved_collectionID = $('.editassignhead').attr('data-collection-id');
                                var hiddeninputs = '<input type="hidden" name="collectionID" value="'+saved_collectionID+'">';
                    $('.editpassignHidden').html(hiddeninputs);
                   // $('.lensgroup-name').text(lenstype);
                   EditshowProductBox(Shopname);
   $('.edit-collectionPage').addClass('hideData');
   $('.lens-get-started').removeClass('hideData');
   
 
   
                      }
}
});
});

/***end submit edit form step 1***/

/*** fetchsettings**/
function getSetting(Shopname){
    
    
    $.ajax({
url: '/lensapp/fetchsettings?shops='+Shopname,
type: 'GET',
success: function(response) {
    $('.formset-data').html(response);
    
  
}
});
    
    
    
}

/** Setting Form  addSettings **/
$('body').on('submit', '#addSettings', function(e){
e.preventDefault();
  
var formData = $('#addSettings').serialize();
console.log(formData);
$.ajax({
url: '/lensapp/lenssettings?shops='+Shopname,
type: 'GET',
data: formData,
success: function(response) {
 
 var valls = '<div class="success-txt"><span class="successval">Data Saved Successfully!</span></div>';
       
       $('.form-success').html(valls);
   getSetting(Shopname);

}
});

});
       
     
  </script>
@endsection
