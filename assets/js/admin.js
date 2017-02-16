jQuery(document).ready(function ($) {

    jQuery( document ).on( 'click', '[data-sfwp-add-list]', function(e) {

        var currentContainer = $(this).parent('[data-sfwp-list-container]');
        var nextContainer = currentContainer.next('[data-sfwp-list-container]');

        nextContainer.addClass('sfwp-lists__item--active');
    });

    jQuery( document ).on( 'click', '[data-sfwp-remove-list]', function(e) {

        var currentContainer = $(this).parent('[data-sfwp-list-container]');
        var currentInput = currentContainer.find('input');

        currentInput.val('');
        currentContainer.removeClass('sfwp-lists__item--active');
    });

});

