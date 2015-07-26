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

	var maxLength = 2500;
	$('textarea').keyup(function() {
		var length = $(this).val().length;
		length = maxLength-length;
		$('#chars').text(length);
	});
	
	// ZeroClipboard
	ZeroClipboard.config({
		swfPath: '//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf',
		forceHandCursor: true,
		trustedDomains: [window.location.host, "cdnjs.cloudflare.com"]
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
						$("#results").removeClass().empty().addClass("alert alert-danger fade in text-center").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$(".tab-content").remove();
						$("#results").removeClass().empty().addClass("alert alert-success fade in text-center").html("<strong>Message encrypted!</strong> " + vars.SUCCESS_ENCRYPTION);
						$("#results").after('<div class="panel panel-default"><div class="panel-body text-center">To access your message, use the following link: <p class="lead"><mark><a class="link-black" href="' +  vars.URL + '/?id=' +data.msg + '" target="_blank">' +  vars.URL + '/?id=' +data.msg + '</a></mark></p><p class="text-warning"><span class="glyphicon glyphicon-warning-sign"></span> This link will not be provided again, so keep it in a safe location!</p></div></div><button id="clip_button" class="btn btn-success btn-lg btn-block" data-clipboard-text="' + vars.URL + '/?id=' +data.msg + '"><span class="glyphicon glyphicon-copy"></span> Copy message link to clipboard</button>');
						var client = new ZeroClipboard( $('#clip_button') );	
					}
				},
				error: function(xhr, status, error) {
					$("#results").removeClass().empty().addClass("alert alert-danger fade in text-center").html('<strong>Hold on there...</strong> ' + vars.INTERNAL_ERROR);
				},
				complete: function() {
					l.ladda('stop');
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			});
			e.preventDefault();
		}
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
						$("#results").removeClass().empty().addClass("alert alert-danger fade in text-center").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$(".tab-content").remove();
						$("#results").removeClass().empty().html("<pre>" + data.msg + "</pre>");
						$("#results").after('<p class="text-warning text-center"><span class="glyphicon glyphicon-warning-sign"></span> ' + vars.SUCCESS_DECRYPTION + '</p>');
					}
				},
				error: function(xhr, status, error) {
					$("#results").removeClass().empty().addClass("alert alert-danger fade in text-center").html('<strong>Hold on there...</strong> ' + vars.INTERNAL_ERROR);
				},
				complete: function() {
					l.ladda('stop');
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			});
			e.preventDefault();
		}
	});

});