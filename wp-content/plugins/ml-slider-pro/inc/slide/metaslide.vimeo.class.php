<?php
/**
 * Vimeo Slide
 */
class MetaVimeoSlide extends MetaSlide {

    /**
     * Register slide type
     */
    public function __construct() {
        add_filter('metaslider_get_vimeo_slide', array($this, 'get_slide'), 10, 2);
        add_action('metaslider_save_vimeo_slide', array($this, 'save_slide'), 5, 3);
        add_action('media_upload_vimeo', array($this, 'vimeo_tab'));
        add_action('wp_ajax_create_vimeo_slide', array($this, 'ajax_create_slide'));
    }

    /**
     * Create a new slide and echo the admin HTML
     */
    public function ajax_create_slide() {
        $slider_id = intval($_POST['slider_id']);
        $fields['menu_order'] = 9999;
        $fields['video_id'] = $_POST['video_id'];
        $this->create_slide($slider_id, $fields);
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result
    }

    /**
     * Media Manager tab
     */
    public function vimeo_tab() {
        return $this->get_iframe();
    }

    /**
     * Get the thumbnail URL from the vimeo API
     */
    private function get_thumb_url($id) {
        $thumb = new WP_Http();
        $thumb = $thumb->request("http://vimeo.com/api/v2/video/$id.php");

        if (!is_wp_error($thumb) && isset($thumb['body'])) {
            $body = unserialize($thumb['body']);

            if (isset($body[0]['thumbnail_medium'])) {
                return $body[0]['thumbnail_medium'];
            }
        }

        return false;
    }

    /**
     * Create a new vimeo slide
     */
    public function create_slide($slider_id, $fields) {
        $this->set_slider($slider_id);

        $postinfo = array(
            'post_title'=> "Meta Slider - Vimeo - {$fields['video_id']}",
            'post_mime_type' => 'video/x-flv',
            'post_status' => 'inherit',
            'guid' => "http://www.vimeo.com/{$fields['video_id']}",
            'menu_order' => $fields['menu_order'],
            'post_name' => $fields['video_id']
        );

        $thumb_url = $this->get_thumb_url($fields['video_id']);
        $vimeo_thumb = false;

        if ($thumb_url) {
            $vimeo_thumb = new WP_Http();
            $vimeo_thumb = $vimeo_thumb->request($thumb_url);            
        }
        
        if (!$vimeo_thumb || is_wp_error($vimeo_thumb) || $vimeo_thumb['response']['code'] != 200) {
            $slide_id = wp_insert_attachment($postinfo);
        } else {
            $attachment = wp_upload_bits( "vimeo_{$fields['video_id']}.jpg", null, $vimeo_thumb['body'], date("Y-m", strtotime( $vimeo_thumb['headers']['last-modified'] ) ) );
            $filename = $attachment['file'];
            $slide_id = wp_insert_attachment($postinfo, $filename);
            $attach_data = wp_generate_attachment_metadata($slide_id, $filename);
            wp_update_attachment_metadata($slide_id, $attach_data);
        }

        // store the type as a meta field against the attachment
        $this->add_or_update_or_delete_meta($slide_id, 'type', 'vimeo');
        $this->set_slide($slide_id);
        $this->tag_slide_to_slider();

        return $slide_id;
    }

    /**
     * Admin slide html
     * 
     * @return string html
     */
    protected function get_admin_slide() {
        $thumb = "";

        // only show a thumbnail if we managed to download one when the slide
        // was created
        $file_path = get_attached_file($this->slide->ID);
        if (strlen($file_path)) {
            $thumb = $this->get_thumb();
        }

        $url = $this->slide->guid;
        sscanf(parse_url($url, PHP_URL_PATH), '/%d', $video_id);

        $row  = "<tr class='slide flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";
        $row .= "            <a class='delete-slide confirm' href='?page=metaslider&id={$this->slider->ID}&deleteSlide={$this->slide->ID}'>x</a>";
        $row .= "            <span class='slide-details'>" . __("Vimeo", 'metasliderpro') . "</span>";
        $row .= "            <span class='vimeo'></span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <iframe type='text/html' width='100%' height='150' src='http://player.vimeo.com/video/{$video_id}?title=0&byline=0&portrait=0' frameborder='0'></iframe>";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='vimeo' />";
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

        wp_enqueue_script('metasliderpro-vimeo-api', METASLIDERPRO_ASSETS_URL . 'froogaloop/jQuery.froogaloop.min.js', array('jquery'));

        $settings = get_post_meta($this->slider->ID, 'ml-slider_settings', true);
        $url = $this->slide->guid;
        sscanf(parse_url($url, PHP_URL_PATH), '/%d', $video_id); // get the video ID

        if ($settings['type'] == 'responsive' || $settings['type'] == 'flex') {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;

            $html  = "\n<div style='position: relative; padding-bottom: {$ratio}%; height: 0;' class='vimeo'>";
            $html .= "\n    <iframe class='vimeo' id='vimeo_{$this->slide->ID}' width='{$settings['width']}' height='{$settings['height']}' src='http://player.vimeo.com/video/{$video_id}?title=0&byline=0&portrait=0&api=1&player_id=vimeo_{$this->slide->ID}' frameborder='0'></iframe>";
            $html .= "\n</div>";
            $html .= "\n<script type='text/javascript'>";
            $html .= "\njQuery(document).ready(function() {";
            $html .= "\n        var player_{$this->slide->ID} = document.getElementById('vimeo_{$this->slide->ID}');";
            $html .= "\n        Froogaloop(player_{$this->slide->ID}).addEvent('ready', ready);";
            $html .= "\n        function addEvent(element, eventName, callback) {";
            $html .= "\n            if (element.addEventListener) {";
            $html .= "\n                element.addEventListener(eventName, callback, false)";
            $html .= "\n            } else {";
            $html .= "\n                element.attachEvent(eventName, callback, false);";
            $html .= "\n            }";
            $html .= "\n        }";
            $html .= "\n        function ready(player_id) {";
            $html .= "\n            var froogaloop = Froogaloop(player_id);";
            $html .= "\n            froogaloop.addEvent('play', function(data) { jQuery('#metaslider_{$this->slider->ID}').flexslider('pause'); });";
            $html .= "\n        }";
            $html .= "\n});";
            $html .= "\n</script>";

            return $html;          
        }
    }

    /**
     * Modify the flex slider parameters when a vimeo slide has been added
     */
    public function get_flex_slider_parameters($options, $slider_id) {
        // disable hoverpause - there is a bug with flex slider that means it 
        // resumes the slideshow even when it has just been told to pause
        if (isset($options["pause"])) {
            unset($options["pause"]);
        }

        $options['useCSS'] = 'false';
        $options["before"][] = "jQuery('#metaslider_{$slider_id} iframe.vimeo').each(function(index) {Froogaloop(this).api('pause');});";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'));

        return $options;
    }

    /**
     * Modify the reponsive slider parameters when a vimeo slide has been added
     */
    public function get_responsive_slider_parameters($options, $slider_id) {
        // disable hoverpause - there is a bug with flex slider that means it 
        // resumes the slideshow even when it has just been told to pause
        if (isset($options["pauseOnHover"])) {
            unset($options["pauseOnHover"]);
        }

        $options["auto"] = "false";
        $options["before"][] = "jQuery('#metaslider_{$slider_id} iframe.vimeo').each(function(index) {Froogaloop(this).api('pause');});";
        
        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_responsive_slider_parameters', array($this, 'get_responsive_slider_parameters'));

        return $options;
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe() {
        return wp_iframe( array($this, 'iframe'));
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe() {
        wp_enqueue_style('media-views');
        wp_enqueue_style('metasliderpro-vimeo-styles', METASLIDERPRO_ASSETS_URL . 'metaslider/vimeo/vimeo.css');
        wp_enqueue_script('metasliderpro-vimeo-script', METASLIDERPRO_ASSETS_URL . 'metaslider/vimeo/vimeo.js', array('jquery'));

        echo "<div class='metaslider'>
                <div class='vimeo'>
                    <div class='media-embed'>
                        <label class='embed-url'>
                            <input type='text' placeholder='http://vimeo.com/36820781' class='vimeo_url'>
                            <span class='spinner'></span>
                        </label>
                        <div class='embed-link-settings'></div>
                    </div>
                </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large' disabled='disabled'>Add to slider</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Save
     */
    protected function save($fields) {
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));
    }
}
?>