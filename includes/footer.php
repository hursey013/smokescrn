
				<hr />

				<p class="small text-center"><a href="<?php echo SITE_URL; ?>/static/about.php">About</a> | <a href="<?php echo SITE_URL; ?>/static/privacy.php">Privacy</a> | <a href="https://github.com/hursey013/smokescrn">Github</a></p>

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

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/spin.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.jquery.min.js"></script>
	<script src="<?php echo SITE_URL; ?>/assets/js/validator.js"></script>
	<script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>

</body>

</html>