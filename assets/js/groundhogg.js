(function($){
	'use strict';
	
	$(document).ready(function(){

	    // Event listener for input changes
	    $('[name="user_first_name"], [name="user_last_name"], [name="user_email"]').on('change', function() {
	        
	        var first_name = $('[name="user_first_name"]').val().trim();
	        var last_name  = $('[name="user_last_name"]').val().trim();
	        var email      = $('[name="user_email"]').val().trim();
	        var product_id = $('[name="mepr_product_id"]').val().trim();
	        
	        // Regular expression for email validation
	        var email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	        // Check if first name, last name (optional), and email are not empty and email is valid
	        if( first_name !== '' && email !== '' && email_regex.test( email ) ) {

	            // Process Contact
	            var data = {
	                'action':     'bloopanimation_groundhogg_process_contact',
	                'first_name': first_name,
	                'last_name':  last_name,
	                'email':      email,
	                'product_id': product_id,
	                'ajax_nonce': bloopanimation_object.ajax_nonce,
	            };
				$.ajax({ 
	                type: 'POST', 
	                url: bloopanimation_object.ajax_url, 
	                data: data,
	                success: function ( data ) {
	                },//success
	                error: function(XMLHttpRequest, textStatus, errorThrown) { 
	                },//Error
	            });

	        }

	    });
	});

})(jQuery)
