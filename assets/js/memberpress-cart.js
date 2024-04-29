(function($){
	'use strict';
	
	$(document).ready(function() {
        /**
         * Set product url in header cart
         */
        var in_cart_product_url = Cookies.get( 'bloopanimation-product-in-the-cart-url' );
        if ( in_cart_product_url !== undefined && in_cart_product_url !== null && in_cart_product_url !== '' ) {
            $('.bloopanimation-header-cart a').attr( 'href', in_cart_product_url );
            $('.bloopanimation-header-cart').removeClass('bloopanimation-hide');
        }

        /**
         * Set product url in a cookie so that we can track the product visited
         */ 
        if ( $('[name="mepr_product_id"]').length == 0 ) {
            return;
        }
        if( $('[name="mepr_product_id"]').val().trim() == '' ){
            return;
        }
        var product_id   = $('[name="mepr_product_id"]').val().trim();
        var product_url  = window.location.href;
        var expire_after = 365;
        Cookies.set( 'bloopanimation-product-in-the-cart-url', product_url, { expires: expire_after } );
	});
})(jQuery)
