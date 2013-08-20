<?php
/**
 * HTML Overlay Slide - HTML placed over an image.
 * 
 * Renamed on the public side to "Layer Slide"
 */
class MetaHtmlOverlaySlide extends MetaSlide {

    /**
     * Register slide type
     */
    public function __construct() {
        add_filter('metaslider_get_html_overlay_slide', array($this, 'get_slide'), 10, 2);
        add_action('metaslider_save_html_overlay_slide', array($this, 'save_slide'), 5, 3);
        add_action('wp_ajax_create_html_overlay_slide', array($this, 'ajax_create_slide'));
    }

    /**
     * 
     */
    public function ajax_create_slide() {
        $slide_id = intval($_POST['slide_id']);
        $slider_id = intval($_POST['slider_id']);

        $this->set_slider($slider_id);

        // duplicate the attachment - get the source slide
        $attachment = get_post($slide_id, ARRAY_A);
        unset($attachment['ID']);
        unset($attachment['post_parent']);
        $attachment['post_mime_type'] = 'text/html';
        $attachment['post_title'] = 'Meta Slider - HTML Overlay - ' . $attachment['post_title'];

        // insert a new attachment
        $new_slide_id = wp_insert_post($attachment);

        // copy over the custom fields
        $custom_fields = get_post_custom( $slide_id );

        foreach( $custom_fields as $key => $value ) {
            if( $key != '_wp_attachment_metadata' ) {
                update_post_meta( $new_slide_id, $key, $value[0] );
            }
        }

        // update metadata (regen thumbs also)
        $data = wp_get_attachment_metadata($slide_id);

        wp_update_attachment_metadata($new_slide_id, $data);

        // store the file type
        $this->add_or_update_or_delete_meta($new_slide_id, 'type', 'html_overlay');

        // set current slide to our newly duplicated slide
        $this->set_slide($new_slide_id);

        // tag the new slide to the slider
        $this->tag_slide_to_slider();

        // finally, return the admin table row HTML
        echo $this->get_admin_slide();
        die();
    }

    /**
     *
     */
    protected function get_admin_slide() {
        $thumb = $this->get_thumb();
        $alt = get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true);
        $html = get_post_meta($this->slide->ID, 'ml-slider_html', true);

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'], 
            $this->settings['height'], 
            isset($this->settings['smartCrop']) ? $this->settings['smartCrop'] : 'false'
        );
        
        $url = $imageHelper->get_image_url();

        $row  = "<tr class='slide flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";
        $row .= "            <a class='delete-slide confirm' href='?page=metaslider&id={$this->slider->ID}&deleteSlide={$this->slide->ID}'>x</a>";
        $row .= "            <span class='slide-details'>" . __("Layer Slide", 'metasliderpro') . "</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <textarea class='wysiwyg' style='display: none;' id='editor{$this->slide->ID}' name='attachment[{$this->slide->ID}][html]'>{$html}</textarea>";
        $row .= "        <button class='openLayerEditor button' data-thumb='{$url}' data-width='{$this->settings['width']}' data-height='{$this->settings['height']}' data-editor_id='editor{$this->slide->ID}'>Launch Layer Editor</button>";
        $row .= "        <p class='rawEdit' rel='editor{$this->slide->ID}'>Edit source (advanced)</p>";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='html_overlay' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Public slide html
     * 
     * @return string html
     */
    protected function get_public_slide() {
        add_filter('metaslider_responsive_slider_javascript', array($this, 'get_responsive_javascript'), 10, 2);
        add_filter('metaslider_flex_slider_javascript', array($this, 'get_responsive_javascript'), 10, 2);
        add_filter('metaslider_responsive_slider_parameters', array($this, 'get_responsive_slider_parameters'), 10, 2);
        add_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'), 10, 2);
        add_filter('metaslider_css', array($this, 'get_animate_css'), 11, 3);

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'], 
            $this->settings['height'], 
            isset($this->settings['smartCrop']) ? $this->settings['smartCrop'] : 'false'
        );
        
        $url = $imageHelper->get_image_url();

        if (is_wp_error($url)) {
            return ""; // bail out here. todo: look at a way of notifying the admin
        }

        $html = get_post_meta($this->slide->ID, 'ml-slider_html', true);
        $alt = get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true);
        
        $slide = "";

        if ($this->settings['type'] == 'responsive' || $this->settings['type'] == 'flex') {
            $slide .= "    <div style='position: relative; height: 100%; width: 100%; padding: 0; margin: 0; float: left;'>";
            $slide .= "        <div class='msHtmlOverlay'>" . do_shortcode($html) . "</div>";
            $slide .= "        <img class='msDefaultImage' height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$url}' alt='{$alt}' />";
            $slide .= "    </div>";
        }

        return $slide;
    }

    /**
     * Import the CSS Animation CSS file
     */
    public function get_animate_css($css, $settings, $id) {
        if ($this->slider->ID == $id) {
            return $css .= "\n        @import url('" . METASLIDERPRO_ASSETS_URL . "animate/animate.css?ver=" . METASLIDERPRO_VERSION . "');";
        }

        return $css;
    }

    /**
     * Reset CSS3 Animations when navigating between slides. 
     */
    public function get_responsive_slider_parameters($options, $slider_id) {
        $options["before"][] = "                 jQuery('#metaslider_{$slider_id} .animated').each(function(index) {
                                    var el = $(this);
                                    var cloned = el.clone();
                                    el.before(cloned);
                                    $(this).remove();
                                });";

        return $options;
    }

    /**
     * Reset CSS3 Animations when navigating between slides. 
     */
    public function get_flex_slider_parameters($options, $slider_id) {
        $options["before"][] = "    jQuery('#metaslider_{$slider_id} li:not(\".flex-active-slide\") .animated').each(function(index) {
                        var el = $(this);
                        var cloned = el.clone();
                        el.before(cloned);
                        $(this).remove();
                    });";

        return $options;
    }

    /**
     * Return the javascript which creates the YouTube videos in the slideshow
     */
    public function get_responsive_javascript($javascript, $slider_id) {
        $html = "
                    function metaslider_scaleLayers_{$slider_id}() {

                        var orig_width = jQuery('#metaslider_{$slider_id} .msDefaultImage').attr('width');
                        var new_width  = jQuery('#metaslider_{$slider_id}').width();

                        if (parseFloat(new_width) >= parseFloat(orig_width)) {
                            return;
                        }

                        jQuery('#metaslider_{$slider_id} .msHtmlOverlay').each(function() {
                            var multiplier = parseFloat(new_width) / parseFloat(orig_width);
                            var percentage = multiplier * 100;

                            jQuery('.layer', jQuery(this)).each(function() {
                                var layer_width  = parseFloat(jQuery(this).attr('data-width'));
                                var layer_height = parseFloat(jQuery(this).attr('data-height'));
                                var layer_top    = parseFloat(jQuery(this).attr('data-top'));
                                var layer_left   = parseFloat(jQuery(this).attr('data-left'));

                                jQuery(this).css('width',       Math.round(layer_width  * multiplier) + 'px');
                                jQuery(this).css('height',      Math.round(layer_height * multiplier) + 'px');
                                jQuery(this).css('top',         Math.round(layer_top    * multiplier) + 'px');
                                jQuery(this).css('left',        Math.round(layer_left   * multiplier) + 'px');
                                jQuery(this).css('font-size',   Math.round(percentage) + '%');
                                jQuery(this).css('line-height', Math.round(percentage) + '%');

                                var content_padding = parseFloat($('.content', $(this)).attr('data-padding'));
                                jQuery('.content', $(this)).css('padding', Math.round(content_padding * multiplier) + 'px');
                            });
                        });
                    }

                    jQuery(window).resize(function(){
                        metaslider_scaleLayers_{$slider_id}();
                    });

                    metaslider_scaleLayers_{$slider_id}();
        ";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_javascript', array($this, 'get_responsive_javascript'));
        remove_filter('metaslider_responsive_slider_javascript', array($this, 'get_responsive_javascript'));

        return $javascript . $html;
    }


    /**
     * Save
     */
    protected function save($fields) {
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

        $this->add_or_update_or_delete_meta($this->slide->ID, 'html', $fields['html']);
    }
}
?>