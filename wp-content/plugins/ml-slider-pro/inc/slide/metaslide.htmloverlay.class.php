<?php
/**
 * HTML Overlay Slide - HTML placed over an image
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
     * Create a new HTML Overlay slide
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
     * Return the admin slide HTML
     */
    protected function get_admin_slide() {
        $thumb = $this->get_thumb();
        $alt = get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true);
        $html = get_post_meta($this->slide->ID, 'ml-slider_html', true);

        $row  = "<tr class='slide flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";
        $row .= "            <a class='delete-slide confirm' href='?page=metaslider&id={$this->slider->ID}&deleteSlide={$this->slide->ID}'>x</a>";
        $row .= "            <span class='slide-details'>HTML Overlay</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <textarea class='wysiwyg' id='editor{$this->slide->ID}' name='attachment[{$this->slide->ID}][html]'>{$html}</textarea>";
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
            $slide .= "    <div style='position: relative;'>";
            $slide .= "        <div class='msHtmlOverlay'>" . $html . "</div>";
            $slide .= "        <img height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$url}' alt='{$alt}' />";
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
        $options["before"][] = "jQuery('#metaslider_{$slider_id} .animated').each(function(index) {
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
        $options["before"][] = "jQuery('#metaslider_{$slider_id} li:not(\".flex-active-slide\") .animated').each(function(index) {
                                    var el = $(this);
                                    var cloned = el.clone();
                                    el.before(cloned);
                                    $(this).remove();
                                });";

        return $options;
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