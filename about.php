<?php require_once 'common.php'; ?>
<?php require_once 'includes/header.php'; ?>

<div class="page-header" style="margin-top:20px">
	<h2>About</h2>
</div>

<p><strong>So what's this thing for?</strong></p>
<p>
I'll cut right to it.  I built this app for my own selfish reasons.  Every time I need to share a password, account number, or other personal information with someone, I cringe the moment I push the send button.  I know my message might be stored on some server for decades, or exposed for the world to see if the accounts are ever compromised.  Call me paranoid, but I want something a little more secure. That's where <?php echo SITE_NAME; ?> comes in.
</p>
<p><strong>Ok, how does it work?</strong></p>
<p>
When you enter your information into <?php echo SITE_NAME; ?> we use the password you choose to encrypt the message to the highest standards available.  It's so secure that if you forget your password we have no way of recovering the information because we only store the encrypted data.  On top of that, once the message is viewed by the recipient it is permanently wiped off the face of the internet.  
</p>
<p><strong>Cool, but I still don't trust you.</strong></p>
<p>
I don't blame you.  I don't trust other websites that offer similar services.  Most of them want you to sign up for an account, provide personal information, or don't allow you to view their application code.  But the truth is, even if we could view your message in plain text (which we can't), there's not much we could do with a password or account number unless we knew all of the supporting details associated with it. In other words, what good is a password if you don't know who or what it belongs to?
</p>
<p>
If that doesn't make you feel any better, the code running this website is open source and can be found on our <a href="http://github.com/hursey013/smokescrn" target="_blank">Github</a> page &mdash; roll your own installation, you won't hurt our feelings.
</p>
<p><strong>I'm a nerd.  Technical details, please.</strong></p>
<p>
<?php echo SITE_NAME; ?> is a simple PHP application built upon several open source libraries and APIs.  An encryption key is generated using the provided password and is hashed using many rounds of SHA-256 (pbkdf2).  This key is used in conjunction with the excellent <a href="https://github.com/defuse/php-encryption" target="_blank">defuse/php-encryption</a> library to encrypt the content of the message with AES-128 in CBC mode.  We also use <a href="https://orchestrate.io/" target="_blank">Orchestrate.io</a> to store the data and <a href="https://sendgrid.com/" target="_blank">SendGrid</a> for email notifications.
</p>

<?php require_once 'includes/footer.php'; ?>