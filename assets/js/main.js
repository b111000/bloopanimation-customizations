(function($){
	'use strict';
	
	$(document).ready(function() {
		// Show or hide login form
		$( 'body' ).on( 'click', '#bloopanimation-login-link', function(e) {
			e.preventDefault();

            if ( $('.bloopanimation-login-section').hasClass('bloopanimation-reveal-section') ) {
                $('.bloopanimation-login-section').slideUp();
                $('.bloopanimation-login-section').removeClass('bloopanimation-reveal-section');
            }else {
                $('.bloopanimation-login-section').slideDown();
                $('.bloopanimation-login-section').addClass('bloopanimation-reveal-section');
            }
		});

		// Hide login form when clicking outside any of the elements
        $(document).on('click', function(event) {
            if ( !$(event.target).closest( '#bloopanimation-login-link, .bloopanimation-login-section').length ) {

                // Don't hide if doing ajax
                if ( $('.bloopanimation-login-section button').attr('disabled') ) {
                    return;
                }

                $('.bloopanimation-login-section').slideUp();
                $('.bloopanimation-login-section').removeClass('bloopanimation-reveal-section');
            }
        });

        // Login
        $( 'body' ).on( 'click', '.bloopanimation-login-section button', function(e) {
			e.preventDefault();

			// Remove previous error classes
            $('.bloopanimation-login-section input').removeClass('bloopanimation-validation-error');
            $('#bloopanimation-validation-msg ').text('Please fill all the fields highlighted in red.');
            $('#bloopanimation-validation-msg ').hide();

            // Check if any input field is empty
            var any_empty = false;
            $('.bloopanimation-login-section input').each(function() {
                if ($(this).val().trim() === '') {
                    any_empty = true;
                    $(this).addClass('bloopanimation-validation-error');
                }
            });

            // If any field is empty, prevent form submission
            if ( any_empty ) {
            	$('#bloopanimation-validation-msg ').show();
            	return;
            }

            $(this).find('.bloopanimation-spinner').css('display', 'inline-block');
            $(this).prop('disabled', true);

            // Login
			var data = {
                'action':     'bloopanimation_login',
                'username':   $('#bloopanimation-login-username').val().trim(),
                'password':   $('#bloopanimation-login-password').val().trim(),
                'ajax_nonce': bloopanimation_object.ajax_nonce,
            };
			$.ajax({ 
                type: 'POST', 
                url: bloopanimation_object.ajax_url, 
                data: data,
                success: function ( data ) {

                	if ( !data.success ) {
                        $('.bloopanimation-login-section button').find('.bloopanimation-spinner').hide();
                        $('.bloopanimation-login-section button').prop('disabled', false);

                		$('#bloopanimation-validation-msg ').text( data.data );
            			$('#bloopanimation-validation-msg ').show();
                	}else if( data.success ){
                		location.reload();
                	}

                },//success
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                },//Error
            });
		});
	});
})(jQuery)
