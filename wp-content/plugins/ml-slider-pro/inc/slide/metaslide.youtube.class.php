<?php
/**
 *
 */
class MetaYouTubeSlide extends MetaSlide {

    /**
     * Register slide type
     */
    public function __construct() {
        add_filter('metaslider_get_youtube_slide', array($this, 'get_slide'), 10, 2);
        add_action('metaslider_save_youtube_slide', array($this, 'save_slide'), 5, 3);
        add_action('media_upload_youtube', array($this, 'youtube_tab'));
        add_action('wp_ajax_create_youtube_slide', array($this, 'ajax_create_slide'));
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
     * Media Manager Tab
     */
    public function youtube_tab() {
        return $this->get_iframe();
    }

    /**
     * Create a new YouTube slide
     */
    private function create_slide($slider_id, $fields) {
        $this->set_slider($slider_id);

        $postinfo = array(
            'post_title'=> "Meta Slider - YouTube - {$fields['video_id']}",
            'post_mime_type' => 'video/x-flv',
            'post_status' => 'inherit',
            'guid' => "http://www.youtube.com/watch?v={$fields['video_id']}",
            'menu_order' => $fields['menu_order'],
            'post_name' => $fields['video_id']
        );

        $youtube_thumb = new WP_Http();
        $youtube_thumb = $youtube_thumb->request("http://img.youtube.com/vi/{$fields['video_id']}/0.jpg");

        if (!is_wp_error($youtube_thumb) && isset($youtube_thumb['response']['code']) && $youtube_thumb['response']['code'] == 200) {
            $attachment = wp_upload_bits( "youtube_{$fields['video_id']}.jpg", null, $youtube_thumb['body'], date("Y-m", strtotime( $youtube_thumb['headers']['last-modified'] ) ) );
            $filename = $attachment['file'];
            $slide_id = wp_insert_attachment($postinfo, $filename);
            $attach_data = wp_generate_attachment_metadata($slide_id, $filename);
            wp_update_attachment_metadata($slide_id,  $attach_data);
        } else {
            $slide_id = wp_insert_attachment($postinfo);
        }

        // store the type as a meta field against the attachment
        $this->add_or_update_or_delete_meta($slide_id, 'type', 'youtube');
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
        $url_parts = explode("=", $url);
        $video_id = $url_parts[1];

        $row  = "<tr class='slide flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";
        $row .= "            <a class='delete-slide confirm' href='?page=metaslider&id={$this->slider->ID}&deleteSlide={$this->slide->ID}'>x</a>";
        $row .= "            <span class='slide-details'>" . __("YouTube", 'metasliderpro') . "</span>";
        $row .= "            <span class='youtube'></span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <iframe type='text/html' width='100%' height='150' src='http://www.youtube.com/embed/{$video_id}?HD=1;rel=0;showinfo=0;autohide=1' frameborder='0'></iframe>";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='youtube' />";
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
        add_filter('metaslider_responsive_slider_javascript', array($this, 'get_youtube_javascript'), 10, 2);

        add_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'), 10, 2);
        add_filter('metaslider_flex_slider_javascript', array($this, 'get_youtube_javascript'), 10, 2);

        wp_enqueue_script('metasliderpro-youtube-api', METASLIDERPRO_ASSETS_URL . 'tubeplayer/jQuery.tubeplayer.min.js', array('jquery'));

        $url = $this->slide->guid;
        $url_parts = explode("=", $url);
        $video_id = $url_parts[1];

        if ($this->settings['type'] == 'responsive' || $this->settings['type'] == 'flex') {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;

            $html  = "<div style='position: relative; padding-bottom: {$ratio}%; height: 0;' rel='{$video_id}' class='youtube'></div>";

            return $html;          
        }
    }

    /**
     * Pause youtube videos when the slide is changed
     */
    public function get_responsive_slider_parameters($options, $slider_id) {
        // disable hoverpause - there is a bug with flex slider that means it 
        // resumes the slideshow even when it has just been told to pause
        if (isset($options["pause"])) {
            unset($options["pause"]);
        }

        $options["auto"] = "false";
        $options["before"][] = "jQuery('#metaslider_{$slider_id} .youtube').each(function(index) {jQuery(this).tubeplayer('pause');});";
        
        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_responsive_slider_parameters', array($this, 'get_responsive_slider_parameters'));

        return $options;
    }

    /**
     * Pause youtube videos when the slide is changed
     */
    public function get_flex_slider_parameters($options, $slider_id) {
        // disable hoverpause - there is a bug with flex slider that means it 
        // resumes the slideshow even when it has just been told to pause
        if (isset($options["pauseOnHover"])) {
            unset($options["pauseOnHover"]);
        }

        $options["useCSS"] = "false";
        $options["before"][] = "jQuery('#metaslider_{$slider_id} .youtube').each(function(index) {jQuery(this).tubeplayer('pause');});";
        
        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'));

        return $options;
    }

    /**
     * Return the javascript which creates the YouTube videos in the slideshow
     */
    public function get_youtube_javascript($javascript, $slider_id) {
        $html  = "\njQuery('#metaslider_{$this->slider->ID} .youtube').each(function() {";
        $html .= "\n   var video_id = $(this).attr('rel');";
        $html .= "\n   $(this).tubeplayer({";
        $html .= "\n        width: {$this->settings['width']},";
        $html .= "\n        height: {$this->settings['height']},";
        $html .= "\n        allowFullScreen: 'true',";
        $html .= "\n        initialVideo: video_id,";
        $html .= "\n        preferredQuality: 'hd720',";
        $html .= "\n        onPlayerPlaying: function(id){ jQuery('#metaslider_{$this->slider->ID}').flexslider('pause'); },";
        $html .= "\n        onPlay: function(id){ jQuery('#metaslider_{$this->slider->ID}').flexslider('pause'); },";
        $html .= "\n    });";
        $html .= "\n});";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_javascript', array($this, 'get_youtube_javascript'));
        remove_filter('metaslider_responsive_slider_javascript', array($this, 'get_youtube_javascript'));

        return $javascript . $html;
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
        wp_enqueue_style('metasliderpro-youtube-styles', METASLIDERPRO_ASSETS_URL . 'metaslider/youtube/youtube.css');
        wp_enqueue_script('metasliderpro-youtube-script', METASLIDERPRO_ASSETS_URL . 'metaslider/youtube/youtube.js', array('jquery'));

        echo "<div class='metaslider'>
                <div class='youtube'>
                    <div class='media-embed'>
                        <label class='embed-url'>
                            <input type='text' placeholder='http://www.youtube.com/watch?v=J---aiyznGQ' class='youtube_url'>
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