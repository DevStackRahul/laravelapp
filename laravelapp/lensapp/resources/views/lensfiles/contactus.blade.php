<div class="customContainer welcome-row contact-row">
    <div class="row landing-page">
    <h1>Get In <span style="color: #345FF1;">Touch</span></h1>
    <h4>We are here for your help!</h4>
	
	<div class="contact-col">
			<div class="col-7">
	<form class="contact" id="contactusForm" method="GET">
		<div class="col-12">
		<div class="col-6">
    <label for="fname">First Name *</label>
    <input type="text" id="fname" name="firstname" required>
	</div>
<div class="col-6">
    <label for="lname">Last Name *</label>
    <input type="text" id="lname" name="lastname" required>
	</div>
	</div>
	
			<div class="col-12">
		<div class="col-6">
	  <label for="lname">Email *</label>
     <input type="email" id="email" name="email" required>
		</div>
<div class="col-6">
    <label for="lname">Subject *</label>
    <input type="text" id="subject" name="subject" required>
		</div>
	</div>

	<div class="side-space">
    <label for="subject">Message *</label>
    <textarea id="message" name="message" required></textarea>
			</div>

	<div class="side-space">
    <input id="contactSubmit" type="submit" value="Submit" class="submit-btn">
  </form>
  		</div>
<div class="info-msg-form">
    <div class="sent-row hideData">
<img src="<?php echo $app_url; ?>/resources/images/confirm_icon.png" alt="Contact Form">
<h2>Thank You!</h2>
<p>Your submission has been sent.</p>
</div>

    <div class="error-row hideData">
<img src="<?php echo $app_url; ?>/resources/images/error-message.png" alt="Contact Form">
<h2>Oops! Something went Wrong.</h2>
<p>Form submission failed.</p>
</div>
</div>
		</div>
		
		<div class="col-3">
		    <img src="<?php echo $app_url;?>/resources/images/contact-form.png" alt="Contact Form">
		<p class="line">Why Choose Us</p>
		<h1>We Deal With The Best Lens Services</h1>
		<h5>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae ornare lorem, cras at aliquet nisi.</h5>
    </div>
		</div>
</div>
</div>