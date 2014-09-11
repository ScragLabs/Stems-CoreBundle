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

$(document).ready(function() {
	
	/**
	 * Close flash message
	 */
	$('body').on('click', '.flash .close', function(){
		$(this).parent().slideToggle();
	});
});