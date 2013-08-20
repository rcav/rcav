<?php
/*
 * Plugin Name: Meta Slider - Pro Addon Pack
 * Plugin URI: http://www.metaslider.com
 * Description: Supercharge your slideshows!
 * Version: 1.2
 * Author: Matcha Labs
 * Author URI: http://www.matchalabs.com
 */

/**
 * Changelog:
 *
 * 1.2
 * - WYSIWYG Editor Added to HTML Overlay slides
 * - Plugin localized
 * - Fix: Post Feeds now only count posts with featured images set
 * 
 * 1.1.4
 * - Fix for YouTube and Vimeo slides when thumbnail download fails
 * 
 * 1.1.3
 * - Youtube debug removed
 * 
 * 1.1.2
 * - PHP Short tag fixed
 * - Theme editor CSS fixed
 * - "More Slide Types" menu item removed
 * - Alt text added to HTML Overlay slide type
 * - HTML Validation Fixes
 * 
 * 1.1.1 
 * - HTML Overlay bug fixed when slideshow has a single slide
 * 
 * 1.1
 * - Theme Editor added
 * - Vimeo thumbnail loader now uses build in WordPress functionality
 * 
 * 1.0.1
 * - Hide overflow on HTML Slides (to stop animations from 'leaking' into other slides)
 * 
 * 1.0
 * - Initial Version
 */
define('METASLIDERPRO_VERSION', '1.2');
define('METASLIDERPRO_BASE_URL', plugin_dir_url(__FILE__));
define('METASLIDERPRO_ASSETS_URL', METASLIDERPRO_BASE_URL . 'assets/');
define('METASLIDERPRO_BASE_DIR_LONG', dirname(__FILE__));
define('METASLIDERPRO_INC_DIR', METASLIDERPRO_BASE_DIR_LONG . '/inc/');

// handle automatic updates
require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater( 'http://wp-updates.com/api/1/plugin', 136, plugin_basename(__FILE__) );

// load ml-slider class
if (!file_exists(WP_PLUGIN_DIR . '/ml-slider/inc/slide/metaslide.class.php')) {
    return;
}
if (!class_exists('MetaSlide')) {
    require_once(WP_PLUGIN_DIR . '/ml-slider/inc/slide/metaslide.class.php');
}

// load ml-slider class
if (!file_exists(WP_PLUGIN_DIR . '/ml-slider/inc/metaslider.imagehelper.class.php')) {
    return;
}
if (!class_exists('MetaSliderImageHelper')) {
    require_once(WP_PLUGIN_DIR . '/ml-slider/inc/metaslider.imagehelper.class.php');
}

require_once(METASLIDERPRO_INC_DIR . 'slide/metaslide.htmloverlay.class.php');
require_once(METASLIDERPRO_INC_DIR . 'slide/metaslide.youtube.class.php');
require_once(METASLIDERPRO_INC_DIR . 'slide/metaslide.vimeo.class.php');
require_once(METASLIDERPRO_INC_DIR . 'slide/metaslide.postfeed.class.php');
require_once(METASLIDERPRO_INC_DIR . 'metaslider.themeEditor.class.php');

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaSliderPro {
    /**
     * Constructor
     */
    public function __construct() {
        add_filter('metaslider_menu_title', array($this, 'menu_title'));

        add_action('init', array($this, 'load_plugin_textdomain'));

        add_action('metaslider_register_admin_scripts', array($this, 'register_admin_scripts'), 10, 1);
        add_action('metaslider_register_admin_styles', array($this, 'register_admin_styles'), 10, 1);

        // Add Slide Tabs
        add_filter('media_upload_tabs', array($this,'custom_media_upload_tab_name'), 999, 1);
        add_filter('media_view_strings', array($this, 'custom_media_uploader_tabs'), 10, 1);

        add_action('admin_notices', array($this, 'metaslider_missing'));

        add_filter('metaslider_css', array($this, 'get_public_css'), 11, 3);

        $themeEditor = new MetaSliderThemeEditor();

        $this->register_slide_types();
    }

    /**
     * Initialise translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('metasliderpro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Show a warning if meta slider (free) is not installed or active
     */
    public function metaslider_missing(){
        if (!is_plugin_active('ml-slider/ml-slider.php')) {
            echo '<div id="message" class="updated"><p><b>Warning</b> Meta Slider Pro <i>Addon Pack</i> is not active. Please install and activate Meta Slider (free) from your Plugins menu or deactivate Meta Slider Pro to remove this message.</p></div>';
        }
    }

    /**
     * Register the pro slide types
     */
    private function register_slide_types() {
        $postFeed = new MetaPostFeedSlide();
        $vimeo = new MetaVimeoSlide();
        $youtube = new MetaYouTubeSlide();
        $htmlOverlay = new MetaHtmlOverlaySlide();
    }

    /**
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts() {
        wp_enqueue_script('metasliderpro-html-overlay-script', METASLIDERPRO_ASSETS_URL . 'metaslider/html_overlay/html_overlay.js', array('jquery', 'media-views', 'metaslider-admin-script'),METASLIDERPRO_VERSION);
        wp_enqueue_script('metasliderpro-layer-editor-script', METASLIDERPRO_ASSETS_URL . 'metaslider/html_overlay/layer_editor.js', array('jquery', 'media-views', 'metaslider-admin-script'),METASLIDERPRO_VERSION);
        wp_enqueue_script('metasliderpro-codemirror-lib', METASLIDERPRO_ASSETS_URL . 'codemirror/lib/codemirror.js', array(), METASLIDERPRO_VERSION);
        wp_enqueue_script('metasliderpro-codemirror-xml', METASLIDERPRO_ASSETS_URL . 'codemirror/mode/xml/xml.js', array(), METASLIDERPRO_VERSION);
        wp_enqueue_script('metasliderpro-admin-script', METASLIDERPRO_ASSETS_URL . 'metaslider/admin.js', array('jquery', 'metaslider-admin-script'),METASLIDERPRO_VERSION);
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('metasliderpro-spectrum', METASLIDERPRO_ASSETS_URL . 'spectrum/spectrum.js',array(),METASLIDERPRO_VERSION);
        wp_enqueue_script('ckeditor', METASLIDERPRO_ASSETS_URL . 'ckeditor/ckeditor.js', array('jquery'),METASLIDERPRO_VERSION);

        // localise the JS
        wp_localize_script( 'metasliderpro-layer-editor-script', 'metasliderpro', array( 
            'newLayer' => __("New Layer", 'metasliderpro'),
            'addLayer' => __("Add Layer", 'metasliderpro'),
            'saveChanges' => __("Save changes?", 'metasliderpro'),
            'animation' => __("Animation", 'metasliderpro'),
            'styling' => __("Styling", 'metasliderpro'),
            'px' => __("px", 'metasliderpro'),
            'animation' => __("Animation", 'metasliderpro'),
            'wait' => __("Wait", 'metasliderpro'),
            'thenWait' => __("then wait", 'metasliderpro'),
            'secondsAnd' => __("seconds and", 'metasliderpro'),
            'padding' => __("Padding", 'metasliderpro'),
            'background' => __("Background", 'metasliderpro'),
            'areYouSure' => __("Are you sure?", 'metasliderpro'),
            'snapToGrid' => __("Snap to grid", 'metasliderpro'),
        ));
    }

    /**
     * Registers and enqueues admin CSS
     */
    public function register_admin_styles() {
        wp_enqueue_style('metasliderpro-spectrum-style', METASLIDERPRO_ASSETS_URL . 'spectrum/spectrum.css', false, METASLIDERPRO_VERSION);
        wp_enqueue_style('metasliderpro-codemirror-style', METASLIDERPRO_ASSETS_URL . 'codemirror/lib/codemirror.css', false, METASLIDERPRO_VERSION);
        wp_enqueue_style('metasliderpro-codemirror-theme-style', METASLIDERPRO_ASSETS_URL . 'codemirror/theme/monokai.css', false, METASLIDERPRO_VERSION);
        wp_enqueue_style('metasliderpro-admin-styles', METASLIDERPRO_ASSETS_URL . 'metaslider/admin.css', false, METASLIDERPRO_VERSION);
    }

    /**
     * Registers and enqueues public CSS
     */
    public function get_public_css($css, $settings, $id) {
        return $css .= "\n        @import url('" . METASLIDERPRO_ASSETS_URL . "metaslider/public.css?ver=" . METASLIDERPRO_VERSION . "');";
    }

    /**
     * Creates a new media manager tab
     * 
     * @var array registered media manager tabs
     */
    public function custom_media_uploader_tabs( $strings ) {
        $strings['insertHtmlOverlay'] = __('Layer Slide', 'metasliderpro');
        return $strings;
    }

    /**
     * Add "Pro" to the menu title
     * 
     * @var string Meta Slider menu name
     * @return string title
     */
    public function menu_title($title) {
        return $title . " Pro";
    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     * 
     * @var array existing media manager tabs
     */
    public function custom_media_upload_tab_name( $tabs ) {
        // restrict our tab changes to the meta slider plugin page
        if ((isset($_GET['page']) && $_GET['page'] == 'metaslider') || 
            (isset($_GET['tab']) && in_array($_GET['tab'], array('youtube', 'vimeo', 'post_feed')))) {

            $newtabs = array( 
                'youtube' => __("YouTube", 'metasliderpro'),
                'vimeo' => __("Vimeo", 'metasliderpro'),
                'post_feed' => __("Post Feed", 'metasliderpro'),
            );

            if (isset($tabs['nextgen'])) unset($tabs['nextgen']);
            if (isset($tabs['metaslider_pro'])) unset($tabs['metaslider_pro']);

            return array_merge( $tabs, $newtabs );
        }

        return $tabs;
    }
}

$metasliderpro = new MetaSliderPro();
?>