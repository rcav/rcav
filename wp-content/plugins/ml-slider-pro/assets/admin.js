jQuery(document).ready(function($) {

    // Enable the correct options for this slider type
    var checkSlideCompatibility = function(slider) {
        // slides - set red background on incompatible slides
        jQuery("#compatibilityWarning").remove();

        if (jQuery('.metaslider .slide:not(.' + slider + ')').length) {
            var message = ucFirst(slider) + " Slider is only compatible with Image and Post Feed slides.";
            var warningDiv = jQuery("<div id='compatibilityWarning' class='updated'><p><b>Warning:</b> " + message + "</p></div>");
            jQuery(".metaslider .left").prepend(warningDiv);
        };
    };

    var ucFirst = function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }


    // handle slide libary switching
    jQuery('.metaslider .select-slider').click(function() {
        checkSlideCompatibility(jQuery(this).attr('rel'));
    });

    checkSlideCompatibility(jQuery('.metaslider .select-slider:checked').attr('rel'));

});