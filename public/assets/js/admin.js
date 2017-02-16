jQuery(document).ready(function(a) {
    jQuery(document).on("click", "[data-sfwp-add-list]", function(b) {
        var c = a(this).parent("[data-sfwp-list-container]"), d = c.next("[data-sfwp-list-container]");
        d.addClass("sfwp-lists__item--active");
    }), jQuery(document).on("click", "[data-sfwp-remove-list]", function(b) {
        var c = a(this).parent("[data-sfwp-list-container]"), d = c.find("input");
        d.val(""), c.removeClass("sfwp-lists__item--active");
    });
});