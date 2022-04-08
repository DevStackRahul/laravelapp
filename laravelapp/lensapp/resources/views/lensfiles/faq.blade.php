<div class="customContainer welcome-row optical-row">
    <div class="row landing-page">
    <h1>Frequently Asked <span style="color: #345FF1;">Questions</span></h1>
    <h4>Ask anything with us!</h4>

 <div class="accordion_container">
     <div class="accordion_head">1. Question Aksed One<span class="plusminus">+</span>
    </div>
     <div class="accordion_body">
         Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec erat a augue ultricies porta. Suspendisse potenti. Mauris ac luctus risus. Vestibulum vel nunc sed nibh lobortis mollis. Nullam a est tincidunt, feugiat sem id, rutrum erat. Suspendisse potenti. Suspendisse et mauris risus. In enim enim, porta sed enim ac, aliquam aliquet tellus. Nulla elit purus, hendrerit pharetra nulla id, maximus scelerisque nunc.
         </div>
                
         <div class="accordion_head">2. Question Aksed Two<span class="plusminus">+</span>
    </div>
     <div class="accordion_body" style="display: none;">
       Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec erat a augue ultricies porta. Suspendisse potenti. Mauris ac luctus risus. Vestibulum vel nunc sed nibh lobortis mollis. Nullam a est tincidunt, feugiat sem id, rutrum erat. Suspendisse potenti. Suspendisse et mauris risus. In enim enim, porta sed enim ac, aliquam aliquet tellus. Nulla elit purus, hendrerit pharetra nulla id, maximus scelerisque nunc.
         </div>
           
         <div class="accordion_head">3. Question Aksed Three<span class="plusminus">+</span>
    </div>
     <div class="accordion_body" style="display: none;">
       Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec erat a augue ultricies porta. Suspendisse potenti. Mauris ac luctus risus. Vestibulum vel nunc sed nibh lobortis mollis. Nullam a est tincidunt, feugiat sem id, rutrum erat. Suspendisse potenti. Suspendisse et mauris risus. In enim enim, porta sed enim ac, aliquam aliquet tellus. Nulla elit purus, hendrerit pharetra nulla id, maximus scelerisque nunc.
         </div>
		 
	<div class="accordion_head">4. Question Aksed Four<span class="plusminus">+</span>
    </div>
     <div class="accordion_body" style="display: none;">
       Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec erat a augue ultricies porta. Suspendisse potenti. Mauris ac luctus risus. Vestibulum vel nunc sed nibh lobortis mollis. Nullam a est tincidunt, feugiat sem id, rutrum erat. Suspendisse potenti. Suspendisse et mauris risus. In enim enim, porta sed enim ac, aliquam aliquet tellus. Nulla elit purus, hendrerit pharetra nulla id, maximus scelerisque nunc.
         </div>
		 
	<div class="accordion_head">5. Question Aksed Five<span class="plusminus">+</span>
    </div>
     <div class="accordion_body" style="display: none;">
       Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec erat a augue ultricies porta. Suspendisse potenti. Mauris ac luctus risus. Vestibulum vel nunc sed nibh lobortis mollis. Nullam a est tincidunt, feugiat sem id, rutrum erat. Suspendisse potenti. Suspendisse et mauris risus. In enim enim, porta sed enim ac, aliquam aliquet tellus. Nulla elit purus, hendrerit pharetra nulla id, maximus scelerisque nunc.
         </div>
     </div>
				
				
				
	@section('scripts')
    @parent
	
  <script>
$(document).ready(function () {
    //toggle the component with class accordion_body
    $(".accordion_head").click(function () {
        if ($('.accordion_body').is(':visible')) {
            $(".accordion_body").slideUp(300);
            $(".plusminus").text('+');
        }
        if ($(this).next(".accordion_body").is(':visible')) {
            $(this).next(".accordion_body").slideUp(300);
            $(this).children(".plusminus").text('+');
        } else {
            $(this).next(".accordion_body").slideDown(300);
            $(this).children(".plusminus").text('-');
        }
    });
});
</script>
  
  @endsection