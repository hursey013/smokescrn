$(function() {

	// Bootstrap Datepicker	
	$('.input-group.date').datepicker({
		format: 'mm/dd/yyyy',
		autoclose: true,
		startDate: '+1d'
	});
	
	var plus7days = new Date();
	plus7days.setDate(plus7days.getDate() + 7 );
	$(".input-group.date").datepicker("setDate", plus7days);
	$(".input-group.date").datepicker('update');
	
	// ZeroClipboard
	ZeroClipboard.config({
		swfPath: '//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf',
		forceHandCursor: true,
		trustedDomains: [window.location.host, "cdnjs.cloudflare.com"]
	});	
	
	// Clear form and alert messages when switching tabs
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		$("#results").removeClass().empty();
		$("form").trigger("reset");
	});

	$('input[type="checkbox"]').click(function(){
		var item = $(this).attr('name');
		$('#'+item).toggle();
	});
	
	$(document).ajaxSend(function(event, request, settings) {
	  $('#loading-indicator').show();
	});

	$(document).ajaxComplete(function(event, request, settings) {
	  $('#loading-indicator').hide();
	});	

	// Connect to encrypt.php and return response
	$('#form_encrypt').validator().on('submit', function(e) {
		$("html, body").animate({ scrollTop: 0 }, "slow");
		if (!e.isDefaultPrevented()) {
			var formData = $(this).serialize();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "encrypt.php",
				data: formData,
				success: function(data) {
					if (data.errors) {
						$("#results").removeClass().empty().addClass("alert alert-danger fade in").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$("form").trigger("reset");
						$(".input-group.date").datepicker("setDate", plus7days);
						$(".input-group.date").datepicker('update');
						$("[id^=show_]").hide();
						$("#results").removeClass().empty().addClass("alert alert-success fade in").html("<strong>Nice work!</strong> Your message, <span id='clip_button' data-clipboard-text='" + window.location.href + "?id=" + data.msg +"' title='Copy link to clipboard'><strong>" + data.msg + "</strong> <span class='glyphicon glyphicon-copy'></span></span> has been created. This unique ID will not be provided again, so keep it in a safe location!");
						var client = new ZeroClipboard( $('#clip_button') );	
					}
				},
				error: function(xhr, status, error) {
					$("#results").removeClass().empty().addClass("alert alert-danger fade in").html('<strong>Hold on there...</strong> An internal error has occured.');
				}
			});
			e.preventDefault();
		}
	});

	// Connect to decrypt.php and return response
	$('#form_decrypt').validator().on('submit', function(e) {
		$("html, body").animate({ scrollTop: 0 }, "slow");
		if (!e.isDefaultPrevented()) {
			var formData = $(this).serialize();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "decrypt.php",
				data: formData,
				success: function(data) {
					if (data.errors) {
						$("#results").removeClass().empty().addClass("alert alert-danger fade in").html("<strong>Hold on there...</strong> " + data.msg);
					} else {
						$("form").trigger("reset");
						$(".input-group.date").datepicker("setDate", plus7days);
						$(".input-group.date").datepicker('update');
						$(".nav, .tab-content").remove();
						$("#results").removeClass().empty().html("<pre>" + data.msg + "</pre>");
					}
				},
				error: function(xhr, status, error) {
					$("#results").removeClass().empty().addClass("alert alert-danger fade in").html('<strong>Hold on there...</strong> An internal error has occured.');
				}
			});
			e.preventDefault();
		}
	});

});