<?php
/**
 * Thumbnail Navigation addon
 */
class MetaSliderThumbnails {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter('metaslider_nivo_slider_parameters', array($this, 'nivo_enable_thumbnails'), 10, 3);
        add_filter('metaslider_flex_slider_parameters', array($this, 'flex_enable_thumbnails'), 10, 3);

        add_filter('metaslider_image_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);
        add_filter('metaslider_post_feed_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);
        add_filter('metaslider_vimeo_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);
        add_filter('metaslider_youtube_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);
        add_filter('metaslider_layer_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);

        add_filter('metaslider_navigation_options', array($this, 'navigation_options'), 10, 2);
    }

    /**
     * Add the 'thumbnails' radio selector to the list of available navigation types.
     * 
     * @param string $navigation_row - the HTML for the existing settings row
     * @param object $slider
     * @return string
     */
    public function navigation_options($navigation_row, $slider) {
        $falseChecked = $slider->get_setting('navigation') == 'false' ? 'checked' : '';
        $trueChecked = $slider->get_setting('navigation') == 'true' ? 'checked' : '';
        $thumbsChecked = $slider->get_setting('navigation') == 'thumbs' ? 'checked' : '';

        $navigation_row = 
        "<tr>
            <td class='tipsy-tooltip' title='" . __("Show slide navigation row", 'metaslider') . "'>
                " . __("Navigation", 'metaslider')  . "
            </td>
            <td style='padding: 0 8px 8px 8px;'>
                <input type='radio' name='settings[navigation]' class='option flex nivo coin responsive' value='false' {$falseChecked} />" . __("Hidden", 'metaslider') . "</option><br />
                <input type='radio' name='settings[navigation]' class='option flex nivo coin responsive' value='true' {$trueChecked} />" . __("Dots", 'metaslider') . "</option><br />
                <input type='radio' name='settings[navigation]' class='option flex nivo' value='thumbs' {$thumbsChecked} />" . __("Thumbnails", 'metaslider') . "</option>
            </td>
        </tr>
        <tr>
            <td class='tipsy-tooltip' title='" . __("Show slide navigation row", 'metasliderpro') . "'>
                " . __("Thumbnail Size (px)", 'metasliderpro') . "
            </td>
            <td>
                ". __("Width", 'metasliderpro') . ": <input type='number' max='999' min='10' size='3' class='width tipsytop' title='" . __("Width", 'metasliderpro') ."' name='settings[thumb_width]' value='" . $slider->get_setting('thumb_width') . "' />
                ". __("Height", 'metasliderpro') . ": <input type='number' max='999' min='10' size='3' class='height tipsytop' title='" . __("Height", 'metasliderpro') . "' name='settings[thumb_height]' value='" . $slider->get_setting('thumb_height') . "' />
            </td>
        </tr>";

        return $navigation_row;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     * 
     * @param array $options - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     * 
     * @return array modified javascript parameters
     */
    public function nivo_enable_thumbnails($options, $slider_id, $settings) {
        if ($settings['navigation'] == 'thumbs') {
            unset($options['controlNav']);
            $options['controlNavThumbs'] = 'true';
        }

        return $options;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Flex Slider
     * 
     * @param array $options - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     * 
     * @return array modified javascript parameters
     */
    public function flex_enable_thumbnails($options, $slider_id, $settings) {
        if ($settings['navigation'] == 'thumbs') {
            $options['controlNav'] = "'thumbnails'";
        }

        return $options;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     * 
     * @param array $slide - slide data
     * @param integer $slide_id - slide ID
     * @param array $settings - slideshow settings
     * 
     * @return array modified slide data
     */
    public function generate_thumbnail_for_slide($slide, $slide_id, $settings) {
        if (($settings['type'] == 'nivo' || $settings['type'] == 'flex') && $settings['navigation'] == 'thumbs') {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide['id'], 
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $slide['data-thumb'] = $imageHelper->get_image_url();

            return $slide;
        }

        return $slide;
    }
}

?>