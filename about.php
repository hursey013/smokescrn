<?php require_once 'common.php'; ?>
<?php require_once 'includes/header.php'; ?>

<div class="page-header" style="margin-top:20px">
	<h2>About</h2>
</div>

<h4>So what's this thing for?</h4>
<p style="margin-bottom:40px;">
	I'll cut right to it.  I built this app for my own selfish reasons.  I can't count the number of times I've needed to share a password, account number, or some other sort of sensitive information with someone, and each and every time I cringe when I push the button to text or email it.  I know my message might be stored on some server I don't own for decades, or exposed if our accounts are ever compromised.  Call me paranoid, but I want something a little more secure.  That's where SmokeSCRN comes in.
</p>
<h4>Ok, how does it work?</h4>
<p style="margin-bottom:40px;">
	When you enter your information into SmokeSCRN we use your password to encrypt the message to the highest standards available.  It's so secure that if you forget your password we have no way of ever recovering the information because we only store the encrypted data.  On top of that, and this is key, once the message is viewed by the recipient it is permanently wiped off the face of the internet.  
</p>
<h4>Cool, but I still don't trust you</h4>
<p style="margin-bottom:40px;">
	I don't blame you.  I don't trust other websites that offer similar services.  Most of them want you to sign up for an account, provide personal information, or don't want you to view their "proprietary" application code.  But the truth is, even if we could view your message in plain text (which we can't), there's not much we could do with a password or account number unless we knew all of the supporting details associated with it. In other words, what good is a password if you don't know what site it belongs to? If that doesn't make you feel any better, our exact code running this website is open source and can be found on our <a href="http://github.com/hursey013/smokescrn" target="_blank">Github</a> page &mdash; roll your own installation, no feelings hurt.
</p>
<h4>I'm a nerd.  Skip the fluffy stuff, give me the technical details</h4>
<p style="margin-bottom:40px;">
	SmokeSCRN is built upon several open source libraries and APIs.  An encryption key is generated using the provided password and is hashed using 100,000 rounds of SHA-256 (pbkdf2).  This key is used in conjunction with the <a href="https://github.com/defuse/php-encryption" target="_blank">defuse/php-encryption</a> library to actually encrypt the content of the message (AES-128).  We also use <a href="https://orchestrate.io/" target="_blank">Orchestrate.io</a> to store the data and <a href="https://sendgrid.com/" target="_blank">SendGrid</a> for email notifications.
</p>

<?php require_once 'includes/footer.php'; ?>