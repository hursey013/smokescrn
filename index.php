<?php require_once 'common.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		<?php echo SITE_NAME; ?> :: <?php echo SITE_TAGLINE; ?>
	</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker3.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda-themeless.min.css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:100,300">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>

	<div class="container">
		<div class="row">
			<div id="main" class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
				<div class="row">
					<div class="col-md-8">
						<h1 id="logo" class="animated fadeIn"><a class="link-black" href="<?php echo SITE_URL; ?>"><span style="font-weight:300;">Smoke</span><span style="font-weight:100;">SCRN</span></a></h1>
						<h4 id="tagline" style="font-weight:300;"><?php echo SITE_TAGLINE; ?></h4>
					</div>
				</div>

				<!--RESULTS-->
				<div id="results" style="display:none;"></div>

				<div class="tab-content">

					<!--ENCRYPT-->
					<div role="tabpanel" class="tab-pane <?php if(!$referral){echo 'active';}?>" id="encrypt">
						<form id="form_encrypt" data-disable="false">
							<label for="message">Your secret message: <a href="#" class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" data-content="Messages are securely transferred, stored using AES-128 encryption, and permanently deleted once viewed."></a></label> 
							<div class="row">
								<div class="col-sm-6" id="expandContainer">
									<div class="form-group">
										<textarea class="form-control" id="message" name="message" rows="3" maxlength="1000" required></textarea>
										<span class="help-block"><span id="chars" style="font-weight:bold;">1000</span> characters remaining. <span class="hidden-xs">[<a href="#" id="expand">Expand</a>]</span></span>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="encrypt_password">Add a password to your message:
									<a href="#" class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" data-content="Your password is never stored on our server, so if you forget it...your message will be unretrievable!"></a>
								</label>
								<div class="form-inline row">
									<div class="form-group col-sm-6">
										<input type="password" class="form-control" id="encrypt_password" name="encrypt_password" placeholder="Password" required data-minlength="8">
										<span class="help-block">Minimum of 8 characters.</span>
									</div>
									<div class="form-group col-sm-6">
										<input type="password" class="form-control" id="encrypt_password_confirm" name="encrypt_password_confirm" placeholder="Confirm password" required data-match="#encrypt_password">
										<span class="help-block with-errors"></span>
									</div>
								</div>
							</div>

							<h5><strong>Additional options:</strong></h5>

							<div class="checkbox">
								<label>
									<input type="checkbox" name="show_email_recipient">Send the message link to someone
									<a href="#" class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" data-content="If you choose not to send an email, you will need to provide the link to the recipient in some other way."></a>
								</label>
							</div>

							<div class="form-group" id="show_email_recipient" style="display:none;">
								<div class="form-inline row">
									<div class="form-group col-sm-6">
										<input type="email" class="form-control" id="email_recipient" name="email_recipient" placeholder="recipient@provider.com">
										<span class="help-block with-errors"></span>
									</div>
									<div class="form-group col-sm-6">
										<input type="text" class="form-control" id="email_password_hint" name="email_password_hint" placeholder="Password hint (optional)" maxlength="200">
										<span class="help-block with-errors"></span>
									</div>
								</div>
							</div>

							<div class="checkbox">
								<label>
									<input type="checkbox" name="show_email_sender">Get notified when message is viewed
									<a href="#" class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" data-content="Your email address will be encrypted along with your message and will never be visible to anyone else."></a>
								</label>
							</div>

							<div class="form-group" id="show_email_sender" style="display:none;">
								<input type="email" class="form-control" id="email_sender" name="email_sender" placeholder="me@provider.com">
								<span class="help-block with-errors"></span>
							</div>

							<div class="checkbox">
								<label>
									<input type="checkbox" name="show_expiration_date">Change the automatic expiration date
									<a href="#" class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" data-content="By default, messages that have not been viewed are permanently deleted after 7 days (30 days max)."></a>
								</label>
							</div>

							<div class="form-group" id="show_expiration_date" style="display:none;">
								<div class="input-group date">
									<input type="text" class="form-control" id="expiration_date" name="expiration_date" required><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
								<span class="help-block with-errors"></span>
							</div>
							<button type="submit" class="btn btn-primary btn-lg btn-block ladda-button" data-style="zoom-out" data-size="l"><span class="ladda-label"><i class="fa fa-lock"></i> Encrypt Secret Message</span>
							</button>
						</form>
					</div>

					<!--DECRYPT-->
					<div role="tabpanel" class="tab-pane <?php if($referral){echo 'active';}?>" id="decrypt">
						<form id="form_decrypt" data-disable="false">
							<div class="form-group">
								<label for="id">Message ID</label>
								<input type="text" class="form-control" id="id" name="id" placeholder="<?php if($use_orchestrate){echo "0b519785ab20dde5";}else{echo "o8AZv0hGh";}?>" required maxlength="16" value="<?php if($referral){echo $_GET["id"];}?>" readonly>
							</div>
							<div class="form-group">
								<label for="decrypt_password">Password</label>
								<input type="password" class="form-control" id="decrypt_password" name="decrypt_password" placeholder="Password" required>
							</div>
							<button type="submit" class="btn btn-primary btn-lg btn-block ladda-button" data-style="zoom-out" data-size="l"><span class="ladda-label"><i class="fa fa-unlock"></i> Decrypt Secret Message</span>
							</button>
						</form>
					</div>
				</div>

				<hr />

				<!--COPYRIGHT-->
				<p class="small text-center">&copy; <?php echo date('Y'); ?> <a href="index.php"><?php echo SITE_NAME; ?></a>. All rights reserved. | <a href="https://github.com/hursey013/smokescrn">Github</a></p>

			</div>
		</div>
	</div>

	<script type="text/javascript">
		<?php
		 $vars = array(
			"URL" => SITE_URL,
			"SUCCESS_ENCRYPTION" => SUCCESS_ENCRYPTION,
			"SUCCESS_DECRYPTION" => SUCCESS_DECRYPTION,
			"INTERNAL_ERROR" => INTERNAL_ERROR
		); ?>
		var vars = <?php echo json_encode($vars); ?>;
	</script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/spin.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.jquery.min.js"></script>
	<script src="assets/js/validator.js"></script>
	<script src="assets/js/script.js"></script>

</body>

</html>