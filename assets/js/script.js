$(function() {
	
	// Bootstrap datepicker	
	$('.input-group.date').datepicker({
		format: 'mm/dd/yyyy',
		autoclose: true,
		startDate: '+1d',
		endDate: '+30d'
	});
	var plus7days = new Date();
	plus7days.setDate(plus7days.getDate() + 7 );
	$(".input-group.date").datepicker("setDate", plus7days);
	$(".input-group.date").datepicker('update');
	
	// Bootstrap popover
	$(function () {
	  $('[data-toggle="popover"]').popover();
	});

	// Message character counter
	var maxLength = 5000;
	$('textarea').keyup(function() {
		var length = $(this).val().length;
		length = maxLength-length;
		$('#chars').text(length);
	});

	// Expand textarea
	$('#expand').click(function(){
		$("#expandContainer").toggleClass("col-sm-6 col-sm-12");
		var text = $(this).text();
		$(this).text(text == "Expand" ? "Collapse" : "Expand");
	});	

	// Show hidden form fields when checkbox is selected
	$('input[type="checkbox"]').click(function(){
		var item = $(this).attr('name');
		$('#'+item).toggle();
	});

	// Connect to encrypt.php and return response
	$('#form_encrypt').validator().on('submit', function(e) {
		
		if (!e.isDefaultPrevented()) {
			
			// Initiate Ladda loading animation
			var l = $("button", this).ladda();
			l.ladda('start');
			
			var formData = $(this).serialize();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "encrypt.php",
				data: formData,
				success: function(data) {
					if (data.errors) {
						$("#results").show().removeClass().empty().addClass("alert alert-danger fade in text-center").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$(".tab-content").remove();
						$("#results").show().removeClass().empty().addClass("alert alert-success fade in text-center").html("<strong>Message encrypted!</strong> " + vars.SUCCESS_ENCRYPTION);
						$("#results").after('<div class="panel panel-default"><div class="panel-body text-center"><p>To access your message, use the following link:</p><p class="lead"><mark><a class="link-black" href="' +  vars.URL + '/' +data.msg + '" target="_blank">' +  vars.URL + '/' + data.msg + '</a></mark></p><p class="text-warning"><span class="glyphicon glyphicon-warning-sign"></span> This link will not be provided again, so keep it in a safe location!</p></div></div><a href="' +  vars.URL + '/index.php" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-home"></span> Return to homepage</a>');
					}
				},
				error: function(xhr, status, error) {
					$("#results").show().removeClass().empty().addClass("alert alert-danger fade in text-center").html('<strong>Hold on there...</strong> ' + vars.INTERNAL_ERROR);
				},
				complete: function() {
					$.ladda( 'stopAll' );
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			});
		}
		return false;
	});

	// Connect to decrypt.php and return response
	$('#form_decrypt').validator().on('submit', function(e) {
		
		if (!e.isDefaultPrevented()) {
			
			// Initiate Ladda loading animation
			var l = $("button", this).ladda();
			l.ladda('start');			
			
			var formData = $(this).serialize();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "decrypt.php",
				data: formData,
				success: function(data) {
					if (data.errors) {
						$("#results").show().removeClass().empty().addClass("alert alert-danger fade in text-center").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$(".tab-content").remove();
						$("#results").show().removeClass().empty().addClass("alert alert-warning fade in text-center").html('<span class="glyphicon glyphicon-warning-sign"></span> ' + vars.SUCCESS_DECRYPTION);
						$("#results").after('<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Secret message:</h3></div><div class="panel-body"><pre>' + data.msg + '</pre></div></div><a href="' +  vars.URL + '/index.php" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-home"></span> Return to homepage</a>');
					}
				},
				error: function(xhr, status, error) {
					$("#results").show().removeClass().empty().addClass("alert alert-danger fade in text-center").html('<strong>Hold on there...</strong> ' + vars.INTERNAL_ERROR);
				},
				complete: function() {
					$.ladda( 'stopAll' );
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			});
		}
		return false;
	});

});