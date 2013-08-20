jQuery(document).ready(function() {
    // show the confirm dialogue
    jQuery(".confirm").live('click', function() {
        return confirm("Are you sure?");
    });
	/**
	 *
	 */
	var applyBulletStylingToPreview = function() {
	    var bullets = jQuery(".flex-control-nav a, .nivo-controlNav a, .rslides_tabs li a, .cs-buttons a");
		var start = jQuery("#colourpicker-fill-start").spectrum("get").toRgbString();
	    var end = jQuery("#colourpicker-fill-end").spectrum("get").toRgbString();
		var borderColour = jQuery("#colourpicker-border-colour").spectrum("get").toRgbString();

		bullets
			.css('padding', '0')
			.css('box-shadow', 'none')
			.css('text-indent', '-9999px')
		    .css('border-style', 'solid')
		    .css('border-color', borderColour)
		    .css('border-radius', jQuery('#theme_dot_border_radius').val() + 'px')
	        .css('border-width', jQuery('#theme_dot_border_width').val() + 'px')
		    .css('width', jQuery('#theme_dot_size').val() + 'px')
	        .css('height', jQuery('#theme_dot_size').val() + 'px')
	        .css('margin-left', jQuery('#theme_dot_spacing').val() + 'px')
	        .css('margin-right', jQuery('#theme_dot_spacing').val() + 'px')
		    .css('background', '-webkit-gradient(linear, 0% 0%, 0% 100%, from(' + start + '), to(' + end + '))')
		    .css('background', '-webkit-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-moz-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-ms-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-o-linear-gradient(top, ' + start + ', ' + end + ')');

	    var activeBullets = jQuery(".flex-control-nav li a.flex-active, .nivo-controlNav a.active, .rslides_tabs li.rslides_here a, .cs-buttons a.cs-active");
		var start = jQuery("#colourpicker-active-fill-start").spectrum("get").toRgbString();
	    var end = jQuery("#colourpicker-active-fill-end").spectrum("get").toRgbString();
		var activeBorderColour = jQuery("#colourpicker-active-border-colour").spectrum("get").toRgbString();

		activeBullets
		    .css('background', '-webkit-gradient(linear, 0% 0%, 0% 100%, from(' + start + '), to(' + end + '))')
		    .css('background', '-webkit-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-moz-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-ms-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('background', '-o-linear-gradient(top, ' + start + ', ' + end + ')')
		    .css('border-color', activeBorderColour);
	}

	/**
	 *
	 */
	var applyBulletPositioningToPreview = function() {
		jQuery('.metaslider').css('margin-bottom', '0px');

	    var bulletContainers = jQuery(".flex-control-nav, .nivo-controlNav, .rslides_tabs, .cs-buttons");

	    var style = "padding: 0; " +
				    "background: transparent; " +
				    "position: absolute; " +
				    "z-index: 99; " +
				    "margin-top: " + jQuery('#theme_nav_vertical_margin').val() + "px; " +
				    "margin-bottom: " + jQuery('#theme_nav_vertical_margin').val() + "px; " +
				    "margin-left: " + jQuery('#theme_nav_horizontal_margin').val() + "px; " +
				    "margin-right: " + jQuery('#theme_nav_horizontal_margin').val() + "px; ";

		var position = jQuery('#arrow_position option:selected').val();

		// slider bottom margin - apply if the buttons are underneath
		var dotSize = parseInt(jQuery('#theme_dot_size').val());
		var dotMargin = parseInt(jQuery('#theme_nav_vertical_margin').val());
		var margin = dotSize + (dotMargin * 2);
		
		if (position == 'default') {
			bulletContainers
				.css('cssText', style +
								"top: auto !important;" +
								"bottom: auto !important;" +
								"left: auto !important;" +
								"right: auto !important;" +
								"width: 100% !important;" +
								"text-align: center !important");

			jQuery('.metaslider').css('margin-bottom', margin + 'px');
		}

		if (position == 'topLeft') {
			bulletContainers
				.css('cssText', style +
								"width: auto !important;" +
								"bottom: auto !important;" +
								"top: 0 !important;" +
								"right: auto !important;" +
								"left: 0 !important;");
		}

		if (position == 'topCenter') {
			bulletContainers
				.css('cssText', style +
								"width: 100% !important;" +
								"bottom: auto !important;" +
								"top: 0 !important;" +
								"right: auto !important;" +
								"left: 0 !important;");
		}

		if (position == 'topRight') {
			bulletContainers
				.css('cssText', style +
								"width: auto !important;" +
								"bottom: auto !important;" +
								"top: 0 !important;" +
								"right: 0 !important;" +
								"left: auto !important;");
		}

		if (position == 'bottomLeft') {
			bulletContainers
				.css('cssText', style +
								"width: auto !important;" +
								"bottom: 0 !important;" +
								"top: auto !important;" +
								"right: auto !important;" +
								"left: 0 !important;");
		}

		if (position == 'bottomCenter') {
			bulletContainers
				.css('cssText', style +
								"width: 100% !important;" +
								"bottom: 0 !important;" +
								"top: auto !important;" +
								"right: auto !important;" +
								"left: 0 !important;");
		}

		if (position == 'bottomRight') {
			bulletContainers
				.css('cssText', style +
								"width: auto !important;" +
								"bottom: 0 !important;" +
								"top: auto !important;" +
								"right: 0 !important;" +
								"left: auto !important;");
		}
	}


	/**
	 *
	 */
	var applyCaptionStylingToPreview = function() {
	    var captions = jQuery('.caption-wrap, .nivo-caption, .cs-title');
		var position = jQuery('#caption_position option:selected').val();
		var caption_width = jQuery('#theme_caption_width').val();
		var border_radius = jQuery('#theme_outer_border_radius').val();

	    var style = "opacity: 1; " +
				    "background: " + jQuery('#colourpicker-caption-background-colour').val() + "; " +
				    "color: " + jQuery('#colourpicker-caption-text-colour').val() + "; " +
				    "z-index: 1000; " +
				    "margin-top: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
				    "margin-bottom: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
				    "margin-left: " + jQuery('#theme_caption_horizontal_margin').val() + "px; " +
				    "margin-right: " + jQuery('#theme_caption_horizontal_margin').val() + "px; ";

		if (position == 'underneath') {
			captions
				.css('cssText', style +
								"width: " + caption_width + "% !important;" +
								"bottom: auto !important;" +
								"top: auto !important;" +
								"right: auto !important;" +
								"left: auto !important;" +
								"clear: both !important;" +
								"position: relative !important;");
		}

		if (position == 'topLeft') {
			captions
				.css('cssText', style +
								"width: " + caption_width + "% !important;" +
								"bottom: auto !important;" +
								"top: 0 !important;" +
								"right: auto !important;" +
								"left: 0 !important;" +
								"clear: none !important;" +
								"position: absolute !important;");
		}

		if (position == 'topRight') {
			captions
				.css('cssText', style +
								"width: " + caption_width + "% !important;" +
								"bottom: auto !important;" +
								"top: 0 !important;" +
								"right: 0 !important;" +
								"left: auto !important;" +
								"clear: none !important;" +
								"position: absolute !important;");
		}

		if (position == 'bottomLeft') {
			captions
				.css('cssText', style +
								"width: " + caption_width + "% !important;" +
								"bottom: 0 !important;" +
								"top: auto !important;" +
								"right: auto !important;" +
								"left: 0 !important;" +
								"clear: none !important;" +
								"position: absolute !important;");
		}

		if (position == 'bottomRight') {
			captions
				.css('cssText', style +
								"width: " + caption_width + "% !important;" +
								"bottom: 0 !important;" +
								"top: auto !important;" +
								"right: 0 !important;" +
								"left: auto !important;" +
								"clear: none !important;" +
								"position: absolute !important;");
		}
	}

	/**
	 *
	 */
	function applyArrowStylingToPreview() {
	    // arrow preview
        var offset = jQuery('#arrow_style option:selected').data('offset');
        var height = jQuery('#arrow_style option:selected').data('height');
        var width = jQuery('#arrow_style option:selected').data('width');
        var url = jQuery('#arrow_colour option:selected').data('url');
        var opacity = jQuery('#arrow_opacity').val() / 100;

        // all arrows
        jQuery('.nivo-prevNav, .flex-prev, .cs-prev, .nivo-nextNav, .flex-next, .cs-next, .rslides_nav')
        	.css('height', height + 'px')
        	.css('padding', '0')
        	.css('text-indent', '-9999px')
        	.css('background-color', 'transparent')
        	.css('width', width + 'px')
        	.css('margin-top', '-' + (height / 2) + 'px')
        	.css('top', '50%')
        	.css('opacity', opacity)
        	.css('background-image', 'url(' + url + ')');

        // prev arrows
        jQuery('.nivo-prevNav, .flex-prev, .cs-prev, .rslides_nav.prev')
	        .css('background-position', '0 -' + offset + 'px')
	        .css('left', jQuery('#theme_arrow_indent').val() + 'px');

	    // next arrows
        jQuery('.nivo-nextNav, .flex-next, .cs-next, .rslides_nav.next')
        	.css('background-position', '100% -' + offset + 'px')
        	.css('right', jQuery('#theme_arrow_indent').val() + 'px');
	}

	/**
	 *
	 */
	function applySlideshowStylingToPreview() {

		// Create a new YUI instance and populate it with the required modules.
		YUI().use('stylesheet', function (Y) {
			Y.StyleSheet('MyApp').set(
			    ".metaslider .flexslider, .metaslider .flexslider .slides img, .metaslider .nivoSlider img,.metaslider .nivoSlider, .coin-slider .coin-slider", {
		        	borderRadius : jQuery('#theme_outer_border_radius').val() + 'px',
			    }
			);
		});

		jQuery(".metaslider").removeClass (function (index, css) {
		    return (css.match (/\beffect\S+/g) || []).join(' ');
		});

		var effect = jQuery('#shadow option:selected').val();
		jQuery('.wrap .metaslider').addClass(effect);

	}

	var applyAllStylingToPreview = function() {
		applyBulletStylingToPreview();
		applyBulletPositioningToPreview();
		applyArrowStylingToPreview();
		applySlideshowStylingToPreview();
		applyCaptionStylingToPreview();
	}

	/**
	 *
	 */
	jQuery('.colorpicker').spectrum({
		preferredFormat: "rgb",
		showInput: true,
		showAlpha: true
	});

	jQuery(".flex-control-nav, .nivo-controlNav, .rslides_tabs, .cs-buttons, .nivo-prevNav, .flex-prev, .cs-prev, .nivo-nextNav, .flex-next, .cs-next").live('click', function() {
		applyBulletStylingToPreview();
		applySlideshowStylingToPreview();
	});

	jQuery('input, select').change(function() {
		applyAllStylingToPreview();
	});

});
