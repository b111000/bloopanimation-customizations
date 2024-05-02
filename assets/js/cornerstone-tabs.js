(function($){
	'use strict';

    $(document).ready(function() {
        // Initialize and set active elements
        $.each( $('.x-tabs .x-tabs-list ul li:first-child'), function() {
            $(this).addClass('bloopanimation-active'); 
        });

        // Set current element, close dropdown
        $('body').on('click', '.e137608-e4 .x-tabs-list ul li:not(.bloopanimation-active)', function(e) {
            if ($(window).width() > 767) {
                return;
            }
            $(this).closest('.x-tabs-list').removeClass('bloopanimation-ul-open');

            $.each($(this).closest('.x-tabs-list').find('li'), function() {
                $(this).removeClass('bloopanimation-active');
            });
            $(this).addClass('bloopanimation-active');
        });

        // Open dropdown
        $('body').on('click', '.e137608-e4 .x-tabs-list ul li.bloopanimation-active', function(e) {
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