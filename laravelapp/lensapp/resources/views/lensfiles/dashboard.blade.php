<?php $app_url = config('app.url'); ?>
<div class="customContainer welcome-row">
    <div class="row landing-page">
    <h1>Welcome to <span style="color: #345FF1;">LensAdvizor!</span></h1>
    <h4>We help online eyewear stores sell prescription lenses.</h4>
		
        <p style="margin-top: 20px;">In summary, here's how to use LensAdvizor in 3 easy steps:</p>
        <ul class="step-lists">
            <li>1. Create a lens collection.</li>
            <li>2. Add lenses to it.</li>
            <li>3. Assign your products to it.</li>
            </ul>
			
            <p style="margin-bottom: 15px;">When you assign products to a lens collection, we replace their add to cart button with a "Select Lenses and Purchase" button that opens the LensAdvizor lens selection pop-up.</p>
			
			<div class="two-col">
			<div class="col-6">
			<h6>Watch this video to see how to set up LensAdvizor:</h6>
        <iframe width="100%" height="315" src="https://www.youtube.com/embed/9VsEAEX6C9Q" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" title="LensAdvizor Introduction"></iframe>
		</div>
		<div class="col-6">
    <img src="<?php echo $app_url;?>/resources/images/checkup.jpg" alt="Empty_state" width="100%">
    </div>
		</div>
			
			<p style="margin-top: 10px; margin-bottom: 5px; color: #345FF1; font-size: 20px;"><strong>Contact us if you need help!</strong></p>
			<p>After you assign products to a lens collection, if the "Select Lenses and Purchase" button doesn't appear for you, please contact us at <a href="mailto:example@gmail.com">example@gmail.com</a>. Your custom theme may be preventing our app from finding your add to cart button, and we're happy to jump on a call with you to help you with set-up.</p>
			
			<div class="get-started-button primary-button-container" style="margin-top: 30px;">
                <!--<button id="started-app" type="button" class="Polaris-Button Polaris-Button--primary">
                <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Get Started</span></span>
                </button>--></div>
				</div></div>