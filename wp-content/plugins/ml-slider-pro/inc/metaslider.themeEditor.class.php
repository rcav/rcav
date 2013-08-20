<?php
/**
 * Theme Editor
 */
class MetaSliderThemeEditor {

    private $theme = array();
    private $theme_slug = "";
    private $preview_slider_id = 0;

    /**
     * Constructor
     */
    public function __construct() {
        add_filter('metaslider_css', array($this, 'get_theme_css'), 15, 3);
        add_filter('metaslider_css_classes', array($this, 'get_theme_css_classes'), 10, 3);
        add_action('admin_menu', array($this, 'register_theme_editor_menu'), 9556);
        add_filter('metaslider_get_available_themes', array($this, 'get_theme_select_options'), 10, 2);
    }

    /**
     * Append the shadow effect type onto the list of classes applied to a slideshow
     */
    public function get_theme_css_classes($classes, $id, $settings) {
        $theme = isset($settings['theme']) ? $settings['theme'] : 'default';

        // if we're using the theme editor, always use the currently selected theme
        if (is_admin() && isset($_REQUEST['theme_slug'])) {
            $theme = $_REQUEST['theme_slug'];
        }

        // bail out if we're not using a custom theme
        if (substr($theme, 0, strlen('_theme')) !== '_theme') {
            return $classes;
        }

        if ($this->load_theme($theme) && isset($this->theme['shadow'])) {
            return $classes .= " " . $this->theme['shadow'];
        }
        
        return $classes;
    }

    /**
     * Append custom themes to the list of theme options on the slideshow edit page.
     */
    public function get_theme_select_options($themes, $selected_theme) {
        $custom_themes = $this->get_themes();

        if (!is_array($custom_themes)) {
            return $themes;
        }

        foreach ($custom_themes as $slug => $theme) {
            $themes .= "<option value='{$slug}' class='option nivo flex responsive coin'";
            if ($slug == $selected_theme) {
                $themes .= " selected=selected";
            }
            $themes .= ">{$theme['title']}</option>";
        }

        return $themes;
    }

    /**
     * Add the theme editor menu option to WordPress
     */
    public function register_theme_editor_menu() {
        $page = add_submenu_page( 'metaslider', 'Theme Editor', 'Theme Editor', 'manage_options', 'metaslider-theme-editor', array($this,'process_admin_page') ); 
    
        // ensure our JavaScript is only loaded on the Meta Slider admin page
        add_action('admin_print_scripts-' . $page, array($this, 'register_theme_editor_scripts'));
        add_action('admin_print_styles-' . $page, array($this, 'register_theme_editor_styles'));
    }

    /**
     * Admin styles
     */
    public function register_theme_editor_styles() {
        wp_enqueue_style('metaslider-admin-styles', METASLIDER_ASSETS_URL . 'metaslider/admin.css', false, METASLIDER_VERSION);
        wp_enqueue_style('metasliderpro-admin-styles', METASLIDERPRO_ASSETS_URL . 'metaslider/admin.css', false, METASLIDERPRO_VERSION);
        wp_enqueue_style('metasliderpro-spectrum-style', METASLIDERPRO_ASSETS_URL . 'spectrum/spectrum.css', false, METASLIDERPRO_VERSION);
    }

    /**
     * Admin scripts
     */
    public function register_theme_editor_scripts() {
        wp_enqueue_script('metasliderpro-yui', 'http://yui.yahooapis.com/3.10.0/build/yui/yui-min.js');
        wp_enqueue_script('metasliderpro-spectrum', METASLIDERPRO_ASSETS_URL . 'spectrum/spectrum.js',array(),METASLIDERPRO_VERSION);
        wp_enqueue_script('metasliderpro-themeEditor-script', METASLIDERPRO_ASSETS_URL . 'metaslider/themeEditor.js', array('jquery', 'metasliderpro-spectrum'),METASLIDERPRO_VERSION);
    }

    /**
     * Create a new theme
     */
    public function create_theme() {
        $slug = '_theme_' . time(); 

        // load existing themes
        $themes = get_option('metaslider-themes');

        // create a new blank theme
        $themes[$slug] = $this->get_theme_defaults();

        // save
        update_option('metaslider-themes', $themes);

        // load it up
        $this->load_theme($slug);
        $_REQUEST['theme_slug'] = $slug;
    }

    /**
     * Return an array of all created themes
     */
    public function get_themes() {
        $themes = get_option('metaslider-themes');
        return $themes;
    }

    /**
     * Return true if the user has created themes
     */
    private function themes_available() {
        $themes = $this->get_themes();
        return !empty($themes);
    }

    /**
     * Save changes to an existing theme
     */
    public function save_theme($slug, $theme) {
        $themes = get_option('metaslider-themes');
        $themes[$slug] = $theme;
        update_option('metaslider-themes', $themes);
    }

    /**
     * Save changes to an existing theme
     */
    public function delete_theme($slug) {
        $themes = get_option('metaslider-themes');

        if (isset($themes[$slug])) {
            unset($themes[$slug]);
            update_option('metaslider-themes', $themes);
            $this->load_default_theme();
            return true; 
        }
        
        return false;
    }

    /**
     * Load an existing theme, identified by the slug
     */
    public function load_theme($slug) {
        $themes = get_option('metaslider-themes');

        if (isset($themes[$slug])) {
            $this->theme = $themes[$slug];
            $this->theme_slug = $slug;
            return true;
        }

        return false;
    }

    /**
     * Load an existing theme, identified by the slug
     */
    public function load_default_theme() {
        $themes = get_option('metaslider-themes');

        if (is_array($themes) && count($themes)) {
            foreach ($themes as $theme => $vals) {
                $this->theme = $vals;
                $this->theme_slug = $theme;
                $_REQUEST['theme_slug'] = $theme;
                return true; // theme loaded
            }
        }

        return false; // no themes found
    }

    /**
     * Contains all the settings for a default (new) theme
     */
    private function get_theme_defaults() {
        $defaults = array(
            'title' => 'New Theme',
            'dot_fill_colour_start' => 'rgba(0,0,0,0.5)',
            'dot_fill_colour_end' => 'rgba(0,0,0,0.5)',
            'dot_border_colour' => 'rgba(0,0,0,1)',
            'dot_border_width' => 0,
            'dot_border_radius' => 10,
            'dot_size' => 12,
            'dot_spacing' => 4,
            'active_dot_fill_colour_start' => 'rgba(0,0,0,1)',
            'active_dot_fill_colour_end' => 'rgba(0,0,0,1)',
            'active_dot_border_colour' => 'rgba(0,0,0,1)',
            'nav_position' => 'default',
            'nav_vertical_margin' => 10,
            'nav_horizontal_margin' => 0,
            'arrow_type' => 1,
            'arrow_colour' => 'black',
            'arrow_indent' => 5,
            'arrow_opacity' => 70,
            'outer_border_radius' => 0,
            'shadow' => 'default',
            'caption_position' => 'bottomLeft',
            'caption_width' => 100,
            'caption_vertical_margin' => 0,
            'caption_horizontal_margin' => 0,
            'caption_background_colour' => 'rgba(0,0,0,0.7)',
            'caption_text_colour' => 'rgba(255,255,255,1)'
        );

        return $defaults;
    }

    /**
     * Get the theme setting. Fall back to returning the default theme setting.
     */
    private function get_setting($name) {
        // try and get a saved setting
        if (isset($this->theme[$name])) {
            return $this->theme[$name];
        }

        // fall back to default values
        $defaults = $this->get_theme_defaults();
        if (isset($defaults[$name])) {
            return $defaults[$name];
        }
        
        return false;
    }

    /**
     * Return all available sliders
     */
    private function get_sliders_for_preview() {
        $sliders = false;
        
        // list the tabs
        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'posts_per_page' => -1
        );
        
        $the_query = new WP_Query($args);
        
        while ($the_query->have_posts()) {
            $the_query->the_post();
            
            $sliders[] = array(
                'title' => get_the_title(),
                'id' => $the_query->post->ID
            );
        }
        
        return $sliders;
    }

    /**
     * Work out which slider to use for the preview
     */
    private function get_preview_slider_id() {
        if (isset($_REQUEST['preview_slider_id'])) {
            return $_REQUEST['preview_slider_id'];
        }

        $all_sliders = $this->get_sliders_for_preview();

        if (is_array($all_sliders) && isset($all_sliders[1]['id'])) {
            return $all_sliders[1]['id'];
        }

        return false;
    }

    /**
     * Handle deleting/saving themes etc
     */
    private function process() {

        if (isset($_REQUEST['save_theme'])) {
            $slug = $_REQUEST['theme_slug'];
            $theme = $_REQUEST['theme'];
            $this->save_theme($slug, $theme);
        }

        if (isset($_REQUEST['delete_theme'])) {
            $slug = $_REQUEST['theme_slug'];
            $this->delete_theme($slug);
        }

        if (isset($_REQUEST['theme_slug'])) {
            $this->load_theme($_REQUEST['theme_slug']);
        } else {
            $this->load_default_theme();
        }

        if (isset($_REQUEST['add'])) {
            $this->create_theme();
        }

        $this->preview_slider_id = $this->get_preview_slider_id();
    }

    /**
     * Render the admin page
     */
    public function process_admin_page() {
        $this->process();

        $arrow_style = $this->get_arrow_options();
        $arrow_colour = $this->get_arrow_colours();

        ?>

        <div class='metaslider metaslider_themeEditor'>

            <h2 class="nav-tab-wrapper">
                <?php
                    if ($this->themes_available()) {
                        foreach ($this->get_themes() as $slug => $theme) {
                            if ($this->theme_slug == $slug) {
                                echo "<div class='nav-tab nav-tab-active'>{$theme['title']}</div>";
                            } else {
                                echo "<a href='?page=metaslider-theme-editor&theme_slug={$slug}' class='nav-tab'>{$theme['title']}</a>";
                            }   
                        }                          
                    }
                ?>
                
                <a href="?page=metaslider-theme-editor&add=true" id="create_new_tab" class="nav-tab">+</a>
            </h2>


            <?php if (!$this->theme_slug) {
                // bail out if we have no themes
                return;
            } ?>

            <div class='left'>
                <form accept-charset='UTF-8' action='?page=metaslider-theme-editor' method='post'>
                    <input type='hidden' name='theme_slug' value='<?php echo $this->theme_slug ?>' />
                    <input type='hidden' name='preview_slider_id' value='<?php echo $this->preview_slider_id ?>' />
                    <table class='widefat settings'>
                        <thead>
                            <tr>
                                <th width='40%'><?php _e("Theme Settings", 'metasliderpro') ?></th>
                                <th><input type='submit' name='save_theme' value='Save' class='alignright button button-primary' /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php _e("Theme Name", 'metasliderpro') ?></td>
                                <td><input type='text' name='theme[title]' value='<?php echo $this->get_setting('title') ?>' />
                            <tr>
                                <td colspan='2' class='highlight'><?php _e("Slideshow", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Outer Border Radius", 'metasliderpro') ?></td>
                                <td>
                                    <input type='number' min='0' max='50' id='theme_outer_border_radius' name='theme[outer_border_radius]' value='<?php echo $this->get_setting('outer_border_radius'); ?>' /><?php _e("px", 'metasliderpro') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("CSS3 Shadow", 'metasliderpro') ?></td>
                                <td>
                                    <select id='shadow' name='theme[shadow]'>
                                        <option value='none' <?php if ($this->get_setting('shadow') == 'none') echo 'selected=selected'?>>None</option>
                                        <option value='effect0' <?php if ($this->get_setting('shadow') == 'effect0') echo 'selected=selected'?>>Default</option>
                                        <option value='effect1' <?php if ($this->get_setting('shadow') == 'effect1') echo 'selected=selected'?>>Bottom</option>
                                        <option value='effect2' <?php if ($this->get_setting('shadow') == 'effect2') echo 'selected=selected'?>>Page Curl</option>
                                        <option value='effect3' <?php if ($this->get_setting('shadow') == 'effect3') echo 'selected=selected'?>>Bottom Curve</option>
                                        <option value='effect4' <?php if ($this->get_setting('shadow') == 'effect4') echo 'selected=selected'?>>Top and Bottom Curve</option>
                                        <option value='effect5' <?php if ($this->get_setting('shadow') == 'effect5') echo 'selected=selected'?>>Sides</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'><?php _e("Caption", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Position", 'metasliderpro') ?></td>
                                <td>
                                    <select id='caption_position' name='theme[caption_position]'>
                                        <option value='bottomLeft' <?php if ($this->get_setting('caption_position') == 'bottomLeft') echo 'selected=selected'?>><?php _e("Bottom Left", 'metasliderpro') ?></option>
                                        <option value='bottomRight' <?php if ($this->get_setting('caption_position') == 'bottomRight') echo 'selected=selected'?>><?php _e("Bottom Right", 'metasliderpro') ?></option>
                                        <option value='topLeft' <?php if ($this->get_setting('caption_position') == 'topLeft') echo 'selected=selected'?>><?php _e("Top Left", 'metasliderpro') ?></option>
                                        <option value='topRight' <?php if ($this->get_setting('caption_position') == 'topRight') echo 'selected=selected'?>><?php _e("Top Right", 'metasliderpro') ?></option>
                                        <option value='underneath' <?php if ($this->get_setting('caption_position') == 'underneath') echo 'selected=selected'?>><?php _e("Underneath", 'metasliderpro') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Width", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='100' id='theme_caption_width' name='theme[caption_width]' value='<?php echo $this->get_setting('caption_width'); ?>' />%</td>
                            </tr>
                            <tr>
                                <td><?php _e("Text Color", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-caption-text-colour' name='theme[caption_text_colour]' value='<?php echo $this->get_setting('caption_text_colour'); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Background Color", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-caption-background-colour' name='theme[caption_background_colour]' value='<?php echo $this->get_setting('caption_background_colour') ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Vertical Margin", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='500' id='theme_caption_vertical_margin' name='theme[caption_vertical_margin]' value='<?php echo $this->get_setting('caption_vertical_margin'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Horizontal Margin", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='500' id='theme_caption_horizontal_margin' name='theme[caption_horizontal_margin]' value='<?php echo $this->get_setting('caption_horizontal_margin'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'><?php _e("Arrows", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Style", 'metasliderpro') ?></td>
                                <td>
                                    <select id='arrow_style' name='theme[arrow_type]'><?php echo $arrow_style ?></select>
                                    <select id='arrow_colour' name='theme[arrow_colour]'><?php echo $arrow_colour ?></select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Opacity", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='100' step='1' id='theme_arrow_opacity' name='theme[arrow_opacity]' value='<?php echo $this->get_setting('arrow_opacity'); ?>' />%</td>
                            </tr>
                            <tr>
                                <td><?php _e("Distance from edge", 'metasliderpro') ?></td>
                                <td><input type='number' min='-50' max='50' id='theme_arrow_indent' name='theme[arrow_indent]' value='<?php echo $this->get_setting('arrow_indent'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'><?php _e("Navigation", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Position", 'metasliderpro') ?></td>
                                <td>
                                    <select id='arrow_position' name='theme[nav_position]'>
                                        <option value='default' <?php if ($this->get_setting('nav_position') == 'default') echo 'selected=selected'?>><?php _e("Default", 'metasliderpro') ?></option>
                                        <option value='topLeft' <?php if ($this->get_setting('nav_position') == 'topLeft') echo 'selected=selected'?>><?php _e("Top Left", 'metasliderpro') ?></option>
                                        <option value='topCenter' <?php if ($this->get_setting('nav_position') == 'topCenter') echo 'selected=selected'?>><?php _e("Top Center", 'metasliderpro') ?></option>
                                        <option value='topRight' <?php if ($this->get_setting('nav_position') == 'topRight') echo 'selected=selected'?>><?php _e("Top Right", 'metasliderpro') ?></option>
                                        <option value='bottomLeft' <?php if ($this->get_setting('nav_position') == 'bottomLeft') echo 'selected=selected'?>><?php _e("Bottom Left", 'metasliderpro') ?></option>
                                        <option value='bottomCenter' <?php if ($this->get_setting('nav_position') == 'bottomCenter') echo 'selected=selected'?>><?php _e("Bottom Center", 'metasliderpro') ?></option>
                                        <option value='bottomRight' <?php if ($this->get_setting('nav_position') == 'bottomRight') echo 'selected=selected'?>><?php _e("Bottom Right", 'metasliderpro') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Vertical Margin", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='500' id='theme_nav_vertical_margin' name='theme[nav_vertical_margin]' value='<?php echo $this->get_setting('nav_vertical_margin'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Horizontal Margin", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='500' id='theme_nav_horizontal_margin' name='theme[nav_horizontal_margin]' value='<?php echo $this->get_setting('nav_horizontal_margin'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'><?php _e("Bullets", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Fill Color", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-fill-start' name='theme[dot_fill_colour_start]' value='<?php echo $this->get_setting('dot_fill_colour_start'); ?>' />
                                    <input type='text' class='colorpicker' id='colourpicker-fill-end' name='theme[dot_fill_colour_end]' value='<?php echo $this->get_setting('dot_fill_colour_end'); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Fill Color (Active)", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-active-fill-start' name='theme[active_dot_fill_colour_start]' value='<?php echo $this->get_setting('active_dot_fill_colour_start'); ?>' />
                                    <input type='text' class='colorpicker' id='colourpicker-active-fill-end' name='theme[active_dot_fill_colour_end]' value='<?php echo $this->get_setting('active_dot_fill_colour_end'); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Border Color", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-border-colour' name='theme[dot_border_colour]' value='<?php echo $this->get_setting('dot_border_colour'); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Border Color (Active)", 'metasliderpro') ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-active-border-colour' name='theme[active_dot_border_colour]' value='<?php echo $this->get_setting('active_dot_border_colour'); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Size", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='50' id='theme_dot_size' name='theme[dot_size]' value='<?php echo $this->get_setting('dot_size'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Spacing", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='50' id='theme_dot_spacing' name='theme[dot_spacing]' value='<?php echo $this->get_setting('dot_spacing'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Border Width", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='50' id='theme_dot_border_width' name='theme[dot_border_width]' value='<?php echo $this->get_setting('dot_border_width'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Border Radius", 'metasliderpro') ?></td>
                                <td><input type='number' min='0' max='50' id='theme_dot_border_radius' name='theme[dot_border_radius]' value='<?php echo $this->get_setting('dot_border_radius'); ?>' /><?php _e("px", 'metasliderpro') ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type='submit' class='confirm' name='delete_theme' value='Delete Theme' />
                </form>
            </div>

            <div class='right'>
                <div class='wrap'><h3 style='margin: 0 0 10px 0;'><?php _e("Live Preview", 'metasliderpro') ?></h3>
                    <?php
                        if ($sliders = $this->get_sliders_for_preview()) {
                            echo "<form style='margin-bottom: 10px;' accept-charset='UTF-8' action='?page=metaslider-theme-editor' method='post'>";
                            echo "<input type='hidden' name='theme_slug' value='{$this->theme_slug}' />";                            
                            echo "<select name='preview_slider_id'>";
                            foreach ($sliders as $slider) {
                                $selected = $slider['id'] == $this->preview_slider_id ? 'selected=selected' : '';
                                echo "<option value='{$slider['id']}' {$selected}>{$slider['title']}</option>";
                            }
                            echo "</select>";
                            echo "<input type='submit' value='Switch Preview Slider' /></form>";
                        }
                    ?>

                    <?php echo do_shortcode("[metaslider id=" . $this->preview_slider_id . "]") ?>

                    <p style='margin-top: 30px'>
                        <i><?php _e("This is a preview only. To apply the theme to a slideshow, edit the slideshow and select this theme from the theme dropdown menu.", 'metasliderpro') ?></i>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * The different arrow types are stored as a sprite, this function
     * returns an array containing the details required to position the 
     * sprite correctly
     */
    public function get_arrows() {
        $arrows[1]  = array('height' => 51, 'width' => 23, 'offset' => 0);
        $arrows[2]  = array('height' => 39, 'width' => 22, 'offset' => 54);
        $arrows[3]  = array('height' => 37, 'width' => 20, 'offset' => 95);
        $arrows[4]  = array('height' => 30, 'width' => 21, 'offset' => 135);
        $arrows[5]  = array('height' => 27, 'width' => 26, 'offset' => 167);
        $arrows[6]  = array('height' => 31, 'width' => 31, 'offset' => 194);
        $arrows[7]  = array('height' => 25, 'width' => 25, 'offset' => 226);
        $arrows[8]  = array('height' => 29, 'width' => 28, 'offset' => 251);
        $arrows[9]  = array('height' => 40, 'width' => 21, 'offset' => 282);
        $arrows[10] = array('height' => 31, 'width' => 21, 'offset' => 325);
        $arrows[11] = array('height' => 23, 'width' => 17, 'offset' => 362);
        $arrows[12] = array('height' => 17, 'width' => 12, 'offset' => 391);
        $arrows[13] = array('height' => 18, 'width' => 22, 'offset' => 411);
        $arrows[14] = array('height' => 25, 'width' => 21, 'offset' => 429);
        $arrows[15] = array('height' => 34, 'width' => 34, 'offset' => 459);
        $arrows[16] = array('height' => 34, 'width' => 34, 'offset' => 498);

        return $arrows;
    }


    /**
     * Return the CSS for the theme.
     */
    public function get_theme_css($css, $settings, $id) {
        $theme_slug = $settings['theme'];

        // theme_slug is used for the preview in the back end. This causes the theme to load
        // even if the preview slideshow isn't set to use this theme
        if (isset($_REQUEST['theme_slug'])) {
            $theme_slug = $_REQUEST['theme_slug'];
        }

        if (substr($theme_slug, 0, strlen('_theme')) !== '_theme') {
            return $css; // we're not using a custom theme
        }

        if (!$this->load_theme($theme_slug)) {
            return $css; // the theme doesn't exist (deleted maybe)
        }
        
        $arrows = $this->get_arrows();
        $selected_arrow_type = $this->theme['arrow_type'];
        $arrow = $arrows[$selected_arrow_type];

        $css .= "\n\n        #metaslider_container_{$id} .flexslider, 
        #metaslider_container_{$id} .flexslider img, 
        #metaslider_container_{$id} .nivoSlider, 
        #metaslider_container_{$id} .nivoSlider img,
        #metaslider_container_{$id} .rslides,
        #metaslider_container_{$id} .rslides img {
            border-radius: {$this->theme['outer_border_radius']}px;
            -webkit-border-radius: {$this->theme['outer_border_radius']}px;
            -moz-border-radius: {$this->theme['outer_border_radius']}px;
        }

        #metaslider_container_{$id} .rslides_tabs li {
            line-height: {$this->theme['dot_size']}px;
        }

        #metaslider_container_{$id} .theme-default .nivoSlider {
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
        }

        #metaslider_container_{$id} .flex-control-nav a, 
        #metaslider_container_{$id} .nivo-controlNav a, 
        #metaslider_container_{$id} .rslides_tabs li a, 
        #metaslider_container_{$id} .cs-buttons a {
            padding: 0;
            box-shadow: none;
            text-indent: -9999px;
            border-style: solid;
            display: inline-block;
            border-color: {$this->theme['dot_border_colour']};
            border-radius: {$this->theme['dot_border_radius']}px;
            border-width: {$this->theme['dot_border_width']}px;
            border: {$this->theme['dot_border_width']}px solid {$this->theme['dot_border_colour']};
            line-height: {$this->theme['dot_size']}px;
            width: {$this->theme['dot_size']}px;
            height: {$this->theme['dot_size']}px;
            margin-left: {$this->theme['dot_spacing']}px;
            margin-right: {$this->theme['dot_spacing']}px;
            background: {$this->theme['dot_fill_colour_start']};
            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->theme['dot_fill_colour_start']}), to({$this->theme['dot_fill_colour_end']}));
            background: -webkit-linear-gradient(top, {$this->theme['dot_fill_colour_start']}, {$this->theme['dot_fill_colour_end']});
            background: -moz-linear-gradient(top, {$this->theme['dot_fill_colour_start']}, {$this->theme['dot_fill_colour_end']});
            background: -ms-linear-gradient(top, {$this->theme['dot_fill_colour_start']}, {$this->theme['dot_fill_colour_end']});
            background: -o-linear-gradient(top, {$this->theme['dot_fill_colour_start']}, {$this->theme['dot_fill_colour_end']});
            background: linear-gradient(top, {$this->theme['dot_fill_colour_start']}, {$this->theme['dot_fill_colour_end']});

        }

        #metaslider_container_{$id} .flex-control-nav li a.flex-active, 
        #metaslider_container_{$id} .nivo-controlNav a.active, 
        #metaslider_container_{$id} .rslides_tabs li.rslides_here a, 
        #metaslider_container_{$id} .cs-buttons a.cs-active {
            border: {$this->theme['dot_border_width']}px solid {$this->theme['active_dot_border_colour']};
            background: {$this->theme['active_dot_fill_colour_start']};
            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->theme['active_dot_fill_colour_start']}), to({$this->theme['active_dot_fill_colour_end']}));
            background: -webkit-linear-gradient(top, {$this->theme['active_dot_fill_colour_start']}, {$this->theme['active_dot_fill_colour_end']});
            background: -moz-linear-gradient(top, {$this->theme['active_dot_fill_colour_start']}, {$this->theme['active_dot_fill_colour_end']});
            background: -ms-linear-gradient(top, {$this->theme['active_dot_fill_colour_start']}, {$this->theme['active_dot_fill_colour_end']});
            background: -o-linear-gradient(top, {$this->theme['active_dot_fill_colour_start']}, {$this->theme['active_dot_fill_colour_end']});
            background: linear-gradient(top, {$this->theme['active_dot_fill_colour_start']}, {$this->theme['active_dot_fill_colour_end']});
        }

        #metaslider_container_{$id} .flex-control-nav,
        #metaslider_container_{$id} .nivo-controlNav,
        #metaslider_container_{$id} .rslides_tabs,
        #metaslider_container_{$id} .cs-buttons {
            line-height: {$this->theme['dot_size']}px;
            padding: 0 !important;
            background: transparent !important;
            z-index: 99;
            margin-top: {$this->theme['nav_vertical_margin']}px;
            margin-bottom: {$this->theme['nav_vertical_margin']}px;
            margin-left: {$this->theme['nav_horizontal_margin']}px;
            margin-right: {$this->theme['nav_horizontal_margin']}px;
            {$this->get_nav_position_css($this->theme['nav_position'])}
        }

        #metaslider_container_{$id} .flexslider .caption-wrap, 
        #metaslider_container_{$id} .rslides .caption-wrap, 
        #metaslider_container_{$id} .nivo-caption, 
        #metaslider_container_{$id} .cs-title {
            opacity: 1;
            margin-top: {$this->theme['caption_vertical_margin']}px;
            margin-bottom: {$this->theme['caption_vertical_margin']}px;
            margin-left: {$this->theme['caption_horizontal_margin']}px;
            margin-right: {$this->theme['caption_horizontal_margin']}px;
            color: {$this->theme['caption_text_colour']};
            background: {$this->theme['caption_background_colour']};
            {$this->get_caption_position_css($settings)}
        }

        #metaslider_container_{$id} .nivo-prevNav, 
        #metaslider_container_{$id} .flex-prev, 
        #metaslider_container_{$id} .cs-prev, 
        #metaslider_container_{$id} .nivo-nextNav, 
        #metaslider_container_{$id} .flex-next, 
        #metaslider_container_{$id} .cs-next, 
        #metaslider_container_{$id} .rslides_nav {
            height: {$arrow['height']}px;
            width: {$arrow['width']}px;
            padding: 0 !important;
            text-indent: -9999px;
            background-color: transparent;
            margin-top: -" . ($arrow['height'] / 2) . "px !important;
            top: 50% !important;
            background-image: url(". METASLIDERPRO_ASSETS_URL . "metaslider/arrows/{$this->theme['arrow_colour']}.png);
        }

        #metaslider_container_{$id} .flex-direction-nav {
            opacity: " . $this->theme['arrow_opacity'] / 100 . ";
        }

        #metaslider_container_{$id} .nivo-prevNav, 
        #metaslider_container_{$id} .cs-prev, 
        #metaslider_container_{$id} .rslides_nav.prev {
            background-position: 0 -{$arrow['offset']}px;
            left: {$this->theme['arrow_indent']}px !important;
        }

        #metaslider_container_{$id} .nivo-nextNav, 
        #metaslider_container_{$id} .cs-next, 
        #metaslider_container_{$id} .rslides_nav.next {
            background-position: 100% -{$arrow['offset']}px;
            right: {$this->theme['arrow_indent']}px !important;
        }

        #metaslider_container_{$id} .flex-direction-nav .flex-prev {
            background-position: 0 -{$arrow['offset']}px;
        }

        #metaslider_container_{$id} .flex-direction-nav .flex-next {
            background-position: 100% -{$arrow['offset']}px;
        }

        #metaslider_container_{$id} .flexslider:hover .flex-direction-nav .flex-prev {
            left: {$this->theme['arrow_indent']}px;
        }

        #metaslider_container_{$id} .flexslider:hover .flex-direction-nav .flex-next {
            right: {$this->theme['arrow_indent']}px;
        }

        #metaslider_container_{$id} {
            {$this->get_container_margin($settings)}
        }

        #metaslider_container_{$id} .flexslider {
            {$this->get_flexslider_margin($settings)}
        }";

        return $css;
    }

    /**
     * Work out the correct margin value to apply to the bottom of the slideshow
     */
    public function get_container_margin($settings) {
        $position = $this->theme['nav_position'];

        if ($position == 'default') {
            $margin = ($this->theme['nav_vertical_margin'] * 2) + $this->theme['dot_size'];
            return "margin-bottom: {$margin}px !important;";
        } else {
            return "margin-bottom: 10px; !important";
        }
    }

    /**
     * Work out the correct margin value to apply to the bottom of the slideshow
     */
    public function get_flexslider_margin($settings) {
        $position = $this->theme['nav_position'];

        if ($position != 'default') {
            return "margin: 0;";
        }
    }

    /**
     * Return CSS rules for the navigation positioning
     */
    public function get_nav_position_css($position) {
        $navPosition['width'] = 'auto';
        $navPosition['top'] = 'auto';
        $navPosition['right'] = 'auto';
        $navPosition['bottom'] = 'auto';
        $navPosition['left'] = 'auto';
        $navPosition['position'] = 'absolute';

        if ($position == 'topCenter' || $position == 'bottomCenter') {
            $navPosition['width'] = '100%';
        }

        if ($position == 'topRight' || $position == 'topCenter') {
            $navPosition['top'] = 0;
            $navPosition['right'] = 0;
        }

        if ($position == 'topLeft') {
            $navPosition['top'] = 0;
            $navPosition['left'] = 0;
        }

        if ($position == 'bottomLeft' || $position == 'bottomCenter') {
            $navPosition['bottom'] = 0;
            $navPosition['left'] = 0;
        }

        if ($position == 'bottomRight') {
            $navPosition['bottom'] = 0;
            $navPosition['right'] = 0;
        }

        if ($position == 'default') {
            $navPosition['width'] = '100%';
            $navPosition['top'] = 'auto';
            $navPosition['right'] = 'auto';
            $navPosition['bottom'] = 'auto';
            $navPosition['left'] = 'auto';
        }

        foreach ($navPosition as $key => $value) {
            $rules[] = $key . ": " . $value . " !important;";
        }
        return implode("\n            ", $rules);
    }

    /**
     * Return CSS rules for the caption positioning
     */
    public function get_caption_position_css($settings) {
        $position = $this->theme['caption_position'];
        $width = $this->theme['caption_width'];

        $captionPosition['width'] = $width . "%";
        $captionPosition['top'] = 'auto';
        $captionPosition['right'] = 'auto';
        $captionPosition['bottom'] = 'auto';
        $captionPosition['left'] = 'auto';
        $captionPosition['clear'] = 'none';
        $captionPosition['position'] = 'absolute';

        if ($position == 'topCenter' || $position == 'bottomCenter') {
            $captionPosition['width'] = '100%';
        }

        if ($position == 'topRight') {
            $captionPosition['top'] = 0;
            $captionPosition['right'] = 0;
        }

        if ($position == 'topLeft') {
            $captionPosition['top'] = 0;
            $captionPosition['left'] = 0;
        }

        if ($position == 'bottomLeft') {
            $captionPosition['bottom'] = 0;
            $captionPosition['left'] = 0;
        }

        if ($position == 'bottomRight') {
            $captionPosition['bottom'] = 0;
            $captionPosition['right'] = 0;
        }

        if ($position == 'underneath') {
            $captionPosition['width'] = '100%';
            $captionPosition['top'] = 'auto';
            $captionPosition['right'] = 'auto';
            $captionPosition['bottom'] = 'auto';
            $captionPosition['left'] = 'auto';
            $captionPosition['clear'] = 'both';
            $captionPosition['position'] = 'relative';
        }

        foreach ($captionPosition as $key => $value) {
            $rules[] = $key . ": " . $value . " !important;";
        }
        return implode("\n            ", $rules);
    }

    /**
     * Return an HTML select list of the available arrow options
     */
    public function get_arrow_options() {
        $arrow_select_options = "";
        $selected_arrow_type = $this->get_setting('arrow_type');
        $arrows = $this->get_arrows();

        foreach($arrows as $id => $vals) {
            $arrow_select_options .= "<option value='{$id}' data-height='{$vals['height']}' data-width='{$vals['width']}' data-offset='{$vals['offset']}'";

            if ($id == $selected_arrow_type) {
                $arrow_select_options .= " selected=selected";
            }

            $arrow_select_options .= ">" . __("Type", 'metasliderpro') . " {$id}</option>";
        }

        return $arrow_select_options;
    }

    /**
     * Return an HTML select list of the available arrow colours
     */
    public function get_arrow_colours() {
        $selected_arrow_colour = $this->get_setting('arrow_colour');

        $colours = array(
            'black', 'blue', 'green', 'grey', 'navy', 'purple', 'red', 'white', 'yellow'
        );

        $arrow_colour_options = "";

        foreach ($colours as $colour) {
            $arrow_colour_options .= "<option value='{$colour}' data-url='" . METASLIDERPRO_ASSETS_URL . "metaslider/arrows/" . $colour . ".png'";

            if ($colour == $selected_arrow_colour) {
                $arrow_colour_options .= " selected=selected";
            }

            $arrow_colour_options .= ">" . ucfirst($colour) . "</option>";
        }

        return $arrow_colour_options;
    }
}

?>