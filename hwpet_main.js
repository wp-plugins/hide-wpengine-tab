/*
 * hwpet_main.js
 * hide-wpengine-tab
 * Created by Aaron Vanderzwan on 2012-12-14.
 * http://www.aaronvanderzwan.com/
 */

jQuery(function(){
	// If staging is locked and we are on the wpengine page
	if( HWPET_settings.lock == 'on' && jQuery('body').hasClass('toplevel_page_wpengine-common') ){
		jQuery('[name="snapshot"]').on( 'click', function(e){
			
			// Build our message to the user
			var message = 'Staging was locked on ' + HWPET_settings.lock_date + ' by ' + HWPET_settings.locked_by;
			if( HWPET_settings.lock_message.length > 0 ) message += "\n\n" + urldecode(HWPET_settings.lock_message);
			
			// Let our user know our message
			alert( message );
			
			// Prevent default action
			e.preventDefault();
		});
	}
});

function urldecode(url) {
	return decodeURIComponent(url.replace(/\+/g, ' '));
}