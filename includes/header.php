<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		&lt;<?php echo SITE_NAME; ?>&gt; :: <?php echo SITE_TAGLINE; ?>
	</title>
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker3.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda-themeless.min.css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:100,300">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/styles.css?090815">
	<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
	
	<meta property="og:title" content="<?php echo SITE_TAGLINE; ?>" />
	<meta property="og:site_name" content="<?php echo SITE_NAME; ?>"/>
	<meta property="og:description" content="I'll cut right to it. I built this app for my own selfish reasons. Every time I share a password, account number, or personal information with someone, I cringe the moment I push the send button. I know my message might be stored on some server forever, or exposed for the world to see if our accounts are ever compromised. Call me paranoid, but I wanted something a little more secure. That's where smokescrn comes in." />
	<meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/ssshh_fb.png"/>
	<meta property="og:image:secure_url" content="<?php echo SITE_URL; ?>/assets/images/ssshh_fb.png"/>
	
</head>

<body class="<?php if($homepage){echo 'home';}?>">

	<?php if(defined('GOOGLE_ANALYTICS_KEY')) { ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?php echo GOOGLE_ANALYTICS_KEY?>', 'auto');
	  ga('send', 'pageview');

	</script>	
	<?php } ?>
	
	<div class="container">
		<div class="row">
			<div id="main" class="col-md-12">
				<div class="row">
					<div id="brand" class="col-md-8">
						<h1 id="logo"><a class="link-black" href="<?php echo SITE_URL; ?>"><span style="font-weight:300;">&lt;smokescrn&gt;</span></a></h1>
						<h4 id="tagline" style="font-weight:300;"><?php echo SITE_TAGLINE; ?></h4>
					</div>
				</div>