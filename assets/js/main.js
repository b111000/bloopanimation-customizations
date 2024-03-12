(function($){
	'use strict';
	
	$(document).ready(function() {
		// Show or hide login form
		$( 'body' ).on( 'click', '#bloopanimation-login-link', function(e) {
			e.preventDefault();
			$('.bloopanimation-login-section').slideDown();
		});

		// Hide login form when clicking outside any of the elements
        $(document).on('click', function(event) {
            if ( !$(event.target).closest('#bloopanimation-login-link').length ) {
                $( '.bloopanimation-login-section' ).hide();
            }
        });

        // Login
        $( 'body' ).on( 'click', '.bloopanimation-login-section button', function(e) {
			e.preventDefault();
			e.stopPropagation();
		});
	});
})(jQuery)
