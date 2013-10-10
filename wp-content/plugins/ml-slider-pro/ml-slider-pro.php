<?php
/*
 * Plugin Name: Meta Slider - Pro Addon Pack
 * Plugin URI: http://www.metaslider.com
 * Description: Supercharge your slideshows!
 * Version: 2.0
 * Author: Matcha Labs
 * Author URI: http://www.matchalabs.com
 */

/**
 * Changelog:
 * 
 * 2.0
 * - New Feature: Thumbnail navigation for Flex & Nivo Slider
 * - Improvement: Pro functionality refactored into 'modules'
 * - Improvement: Theme editor CSS output tidied up
 * - Fix: YouTube thumbnail date 
 * - Fix: YouTube videos on HTTPS
 * 
 * 1.2.2
 * - Fix: Vimeo slideshows not pausing correctly
 *
 * 1.2.1
 * - Fix: Vertical slides with HTML Overlay not working
 * - Fix: YouTube & Vimeo slides not saving on some installations
 * - Change: Post Feed limit changed to 'number' input type
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
define('METASLIDERPRO_VERSION', '2.0');
define('METASLIDERPRO_BASE_URL', plugin_dir_url(__FILE__));
define('METASLIDERPRO_ASSETS_URL', METASLIDERPRO_BASE_URL . 'assets/');
define('METASLIDERPRO_BASE_DIR_LONG', dirname(__FILE__));
define('METASLIDERPRO_INC_DIR', METASLIDERPRO_BASE_DIR_LONG . '/modules/');

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

// load image helper class
if (!file_exists(WP_PLUGIN_DIR . '/ml-slider/inc/metaslider.imagehelper.class.php')) {
    return;
}
if (!class_exists('MetaSliderImageHelper')) {
    require_once(WP_PLUGIN_DIR . '/ml-slider/inc/metaslider.imagehelper.class.php');
}

require_once(METASLIDERPRO_INC_DIR . 'youtube/slide.php');
require_once(METASLIDERPRO_INC_DIR . 'vimeo/slide.php');
require_once(METASLIDERPRO_INC_DIR . 'layer/slide.php');
require_once(METASLIDERPRO_INC_DIR . 'post_feed/slide.php');
require_once(METASLIDERPRO_INC_DIR . 'theme_editor/theme_editor.php');
require_once(METASLIDERPRO_INC_DIR . 'thumbnails/thumbnails.php');

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
        add_action('admin_notices', array($this, 'metaslider_missing'));
        add_filter('metaslider_css', array($this, 'get_public_css'), 11, 3);
        add_filter('media_upload_tabs', array($this,'custom_media_upload_tab_name'), 999, 1);

        $themeEditor = new MetaSliderThemeEditor();
        $thumbnails = new MetaSliderThumbnails();
        $postFeed = new MetaPostFeedSlide();
        $vimeo = new MetaVimeoSlide();
        $youtube = new MetaYouTubeSlide();
        $htmlOverlay = new MetaLayerSlide();
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
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts() {
        wp_enqueue_script('metasliderpro-admin-script', METASLIDERPRO_ASSETS_URL . 'admin.js', array('jquery', 'metaslider-admin-script'),METASLIDERPRO_VERSION);
    }

    /**
     * Registers and enqueues admin CSS
     */
    public function register_admin_styles() {
        wp_enqueue_style('metasliderpro-admin-styles', METASLIDERPRO_ASSETS_URL . 'admin.css', false, METASLIDERPRO_VERSION);
    }

    /**
     * Registers and enqueues public CSS
     * 
     * @param string $css
     * @param array $settings
     * @param int $id
     * @return string
     */
    public function get_public_css($css, $settings, $id) {
        return $css .= "\n        @import url('" . METASLIDERPRO_ASSETS_URL . "public.css?ver=" . METASLIDERPRO_VERSION . "');";
    }

    /**
     * Add "Pro" to the menu title
     * 
     * @param string Meta Slider menu name
     * @return string title
     */
    public function menu_title($title) {
        return $title . " Pro";
    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     * 
     * @param array existing media manager tabs
     */
    public function custom_media_upload_tab_name( $tabs ) {
        // restrict our tab changes to the meta slider plugin page
        if (isset($_GET['page']) && $_GET['page'] == 'metaslider') {
            if(isset($tabs['nextgen'])) unset($tabs['nextgen']);
            if(isset($tabs['metaslider_pro'])) unset($tabs['metaslider_pro']);
        }

        return $tabs;
    }
}

$metasliderpro = new MetaSliderPro();
?>