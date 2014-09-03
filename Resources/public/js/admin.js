/**
 * Create flash message
 */
function createFlashMessage(type, message) {

	// Remove any existing messages
	$('.flash').remove();

	// Create the new message
	var element = $('<div class="flash flash-'+type+'"><p>'+message+'</p><i class="fa close">&times;</i></div>');
	$('#flash-messages > .wrap').append(element);

	// Auto hide after 5 seconds
	setTimeout(function() {
		element.slideToggle();
	}, 5000);
}

/**
 * Callback to update a featured image
 */
function updateFeatureImage(data, originator) {
	$('.feature-image').html(data.html).removeClass('feature-image-empty');
}

/**
 * Slugify a string
 */
function slugify(string) {
	string = string.replace(/&/g, 'and');
	string = string.replace(/[^\w\s-]/g, '').trim().toLowerCase();
	string = string.replace(/[-\s]+/g, '-');
	return string;
}

$(document).ready(function() {
	
	/**
	 * Close flash message
	 */
	$('body').on('click', '.flash .close', function(){
		$(this).parent().slideToggle();
	});

	/**
	 * Get a popup
	 */
	$('body').on('click', '.rest-get-popup', function(e) {
		e.preventDefault();
		var originator = $(this);

		$.get(originator.attr('href')).done(function(data) {
			if (data.status == 'success') {
				var popup = $(data.html);
				$('body').append(popup);
				popup.children('.admin-popup').slideToggle();
			} else {
				if (data.flash) {
					createFlashMessage(data.status, data.message);
				}
			}
			if (data.callback) {
				window[data.callback](data, originator);
			}
		});
	});

	/**
	 * Close a popup
	 */
	$('body').on('click', '.close-popup', function(e) {
		e.preventDefault();
		var originator = $(this);

		originator.parent().parent().slideToggle(function() {
			$(this).parent().remove();
		});
	});

	/**
	 * Standardised popup post request
	 */
	$('body').on('click', '.rest-post-popup', function(e) {
		e.preventDefault();
		var originator = $(this);
		var form = $('.admin-popup form');
		var buttonText = originator.html();
		
		originator.html('<i class="fa fa-circle-o-notch fa-spin"></i>');

		$.post(form.attr('action'), form.serialize()).done(function(data) {
			originator.html(buttonText);

			if (data.flash) {
				createFlashMessage(data.status, data.message);
			}

			if (data.callback) {
				var callback = data.callback;
				eval(callback)(data, originator);
			}

			originator.parent().parent().slideToggle(function() {
				$(this).parent().remove();
			});
		});
	});

	/**
	 * Standardised popup post request with upload
	 */
	$('body').on('click', '.rest-upload-popup', function(e) {
		e.preventDefault();
		var originator = $(this);
		var form = $('.admin-popup form');
		var data = new FormData();
		var buttonText = originator.html();
		var buttonWidth = originator.css('width');

		originator.css('width', buttonWidth);
		originator.html('<i class="fa fa-circle-o-notch fa-spin"></i> Uploading');

		form.find('input').each(function(e) {
			if ($(this).attr('type') == 'file') {
				data.append($(this).attr('name'), $(this)[0].files[0]);
			} else {
				data.append($(this).attr('name'), $(this).val());
			}
		});

		form.find('select').each(function(e) {
			data.append($(this).attr('name'), $(this).val());
		});

		$.ajax({
			url: form.attr('action'),
			data: data,
			processData: false,
			contentType: false,
			type: 'POST'
		}).done(function(data) {
			originator.html(buttonText);

			if (data.flash) {
				createFlashMessage(data.status, data.message);
			}

			if (data.callback) {

				var callback = data.callback;
				eval(callback)(data, originator);
			}

			originator.parent().parent().slideToggle(function() {
				$(this).parent().remove();
			});
		}).fail(function(data) {
			originator.html(buttonText);
		});
	});

	/**
	 * Sortable elements init
	 */
	$('.sortable').sortable();

	/**
	 * Change edit tabs
	 */
	$('.edit-tabs').on('click', 'a', function(e){
		e.preventDefault();

		$('.edit-tabs > ul > li > a').removeClass('active');
		$(this).addClass('active');
		$('.edit-panel').css('height', '0px');
		$('#'+$(this).data('tab')).css('height', 'auto');
	});
});