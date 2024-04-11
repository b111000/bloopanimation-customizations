(function($){
	'use strict';

    $(document).ready(function() {
        /**
         * This section handles the functionality of the dropdown menu specifically for mobile devices.
         * It ensures that when an item in the dropdown is clicked, it moves to the top of the list and closes the dropdown.
         * Additionally, it opens the dropdown when the first item is clicked, and closes it when clicking outside the dropdown.
         */
        
        // Set current element, close dropdown
        $('body').on('click', '.e137608-e4 .x-tabs-list ul li:not(:first-child)', function(e) {
            if ($(window).width() > 767) {
                return;
            }
            $(this).prependTo('.e137608-e4 .x-tabs-list ul');
            $(this).closest('.x-tabs-list').removeClass('bloopanimation-ul-open');
        });

        // Open dropdown
        $('body').on('click', '.e137608-e4 .x-tabs-list ul li:first-child', function(e) {
            if ($(window).width() > 767) {
                return;
            }
            $(this).closest('.x-tabs-list').toggleClass('bloopanimation-ul-open');
        });

        // Hide dropdown when clicking outside any of the elements
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.e137608-e4 .x-tabs-list').length) {
                $('.e137608-e4 .x-tabs-list').removeClass('bloopanimation-ul-open');
            }
        });
    });

})(jQuery)