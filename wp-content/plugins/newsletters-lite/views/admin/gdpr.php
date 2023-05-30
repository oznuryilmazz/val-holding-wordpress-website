<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters newsletters-gdpr">
	<h1>Newsletters: GDPR Compliance Requirements</h1>
	
	<h2>What is GDPR?</h2>
	<p>The <a href="https://www.eugdpr.org/the-regulation.html" target="_blank">GDPR website</a> states "​The aim of the GDPR is to protect all EU citizens from privacy and data breaches in an increasingly data-driven world."</p>
	<p>Protecting private data is something we are passionate about at Tribulant and a cause we can get behind 100%.</p>
	<p>GDPR applies to all companies processing personal data of people in the EU, regardless of the company’s location. This means that even if you're outside Europe, you need to take action.</p>
	<p>The good news is that GDPR compliance for the Newsletter plugin is 100% free and does not require any additional plugins. Just a few simple tweaks to your existing forms and you're set.</p>
	
	<h2>The Main GDPR Requirements</h2>
	<ul>
		<li><strong>1. Request Consent:</strong> GDPR requires that users give explicit consent before submitting personal data.</li>
		<li><strong>2. Right to Access:</strong> Provide a way for users to request access to, and view the data you have collected from them.</li>
		<li><strong>3. Right to be Forgotten:</strong> Give users a way to withdraw consent and delete personal data collected from them.</li>
	</ul>
	
	<h2>How to Comply</h2>
	<h3>1. Request Consent</h3>
	<p>Requesting consent is as easy as adding a checkbox custom field to your subscribe forms. Go to <strong>Newsletters > Custom Fields</strong> and add a new, required checkbox custom field with a label/option that says something like "<strong>I give consent to Company Name to collect and use my details via this form</strong>". As a result, the subscribe form will only submit once the checkbox is checked and your subscriber has given consent. Make sure you add this required checkbox custom field to all your forms.</p>
	
	<h3>2. Right to Access</h3>
	<p>Your subscribers already have access to their subscriptions and profile on the Manage Subscriptions page. Make sure you put a <code>[newsletters_manage]</code> shortcode in your newsletter template or content so subscribers can have access. Also make your Manage Subscriptions page prominent on your website.</p>
	
	<h3>3. Right to be Forgotten</h3>
	<p>We added a "<strong>Delete Account</strong>" button to the Manage Subscriptions page for subscribers to delete their subscriber account completely. This is turned on by default as you install this update but it can be turned on/off under <strong>Newsletters > Configuration > Subscribers > Subscriber Management</strong> as needed.</p>
	
	<h2>Re-Obtain Consent/Confirmation</h2>
	<p>If you previously gained consent from your subscribers in a way that complies with the GDPR, you don't need to re-obtain consent from them.</p>
	<p>You can however re-obtain consent with the following steps:</p>
	<ol>
		<li>Create a new mailing list under <strong>Newsletters > Mailing Lists</strong>.</li>
		<li>Create a newsletter under <strong>Newsletters > Create Newsletter</strong>.</li>
		<li>In the newsletter, put a <code>[newsletters_subscribe_link list=X]</code> shortcode for the new list.</li>
		<li>Your subscribers can now confirm and give consent to your new mailing list.</li>
		<li>Use your new mailing list with confirmed subscribers to send newsletters to.</li>
	</ol>
</div>