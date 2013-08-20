<?php
/**
 * Post Feed Slide
 */
class MetaPostFeedSlide extends MetaSlide {

    /**
     * Register slide type
     */
    public function __construct() {
        add_filter('metaslider_get_post_feed_slide', array($this, 'get_slide'), 10, 2);
        add_action('metaslider_save_post_feed_slide', array($this, 'save_slide'), 5, 3);        
        add_action('media_upload_post_feed', array($this, 'get_iframe'));
        add_action('wp_ajax_create_post_feed_slide', array($this, 'ajax_create_slide'));
    }

    /**
     *  
     */
    public function ajax_create_slide() {
        $slider_id = intval($_POST['slider_id']);
        $this->create_slide($slider_id, $fields);
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result
    }

    /**
     *
     */
    public function set_slide($id) {
        parent::set_slide($id);
        $this->slide_settings = get_post_meta($id, 'ml-slider_settings', true);
    }

    /**
     * Admin slide html
     * 
     * @return string html
     */
    protected function get_admin_slide() {
        $row  = "<tr class='slide post_feed flex responsive coin nivo'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb post_feed'>";
        $row .= "            <a class='delete-slide confirm' href='?page=metaslider&id={$this->slider->ID}&deleteSlide={$this->slide->ID}'>x</a>";
        $row .= "            <span class='slide-details'>Post Feed</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <div class='settings'>";
        $row .= "        <fieldset class='col-left'><legend class='tooltiptop' title='Select which post types to extract'>Post Types</legend>{$this->get_post_type_options()}</fieldset>";
        $row .= "        <fieldset class='col-middle'><legend class='tooltiptop' title='Posts must be tagged to at least one of checked categories'>Tag Restrictions</legend>{$this->get_tag_options()}</fieldset>";
        $row .= "        <fieldset class='col-right'><legend>Settings</legend>";
        $row .= "            <div class='row'>";
        $row .= "                <label>Slide Caption</label>";
        $row .= "                {$this->get_caption_options()}";
        $row .= "            </div>";
        $row .= "            <div class='row'>";
        $row .= "                <label>Slide Link</label>";
        $row .= "                {$this->get_link_to_options()}";
        $row .= "            </div>";
        $row .= "            <div class='row'>";
        $row .= "                <label>Order By</label>";
        $row .= "                {$this->get_order_by_options()}{$this->get_order_direction_options()}";
        $row .= "            </div>";
        $row .= "            <div class='row'>";
        $row .= "                <label>Limit</label>";
        $row .= "                {$this->get_limit_options()}";
        $row .= "            </div>";
        $row .= "        </fieldset>";
        $row .= "        </div>";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='post_feed' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Returns a nested list of taxonomies
     * 
     * @return string html
     */
    private function get_tag_options() {
        ob_start();

        echo "<div class='scroll'><ul>";

        $taxonomies = get_taxonomies(array('public' => true),'objects'); 

        foreach ($taxonomies as $taxonomy) {
            echo "<li class='header'>{$taxonomy->label}</li>";

            wp_terms_checklist(0, array(
                'taxonomy'  => $taxonomy->name,
                'selected_cats' => $this->get_selected_tags($taxonomy->name),
                'walker' => new Walker_MetaSlider_Checklist($this->slide->ID),
                'checked_ontop' => false,
                'popular_cats' => false
            ));
        }

        echo "</ul></div>";

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Generate the order by drop down list
     * 
     * @return string drop down list HTML
     */
    private function get_order_by_options() {
        $selected_option = isset($this->slide_settings['order_by']) ? $this->slide_settings['order_by'] : 'date';

        $options = array(
            'Publish Date' => 'date',
            'Post ID' => 'ID',
            'Author' => 'author',
            'Post Title' => 'title',
            'Post Slug' => 'name',
            'Modified Date' => 'modified',
            'Random' => 'rand'
        );

        $html = "<select name='attachment[{$this->slide->ID}][settings][order_by]'>";

        foreach ($options as $title => $value) {
            $selected = $value == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "</select>";

        return $html;
    }

    /**
     * Generate the limit drop down list
     * 
     * @return string drop down list HTML
     */
    private function get_limit_options() {
        $selected_option = isset($this->slide_settings['number_of_posts']) ? $this->slide_settings['number_of_posts'] : 3;

        $options = array(
            '1 Post' => 1,
            '2 Posts' => 2,
            '3 Posts' => 3,
            '4 Posts' => 4,
            '5 Posts' => 5,
            '6 Posts' => 6,
            '7 Posts' => 7,
            '8 Posts' => 8,
            '9 Posts' => 9,
            '10 Posts' => 10
        );

        $html = "<select name='attachment[{$this->slide->ID}][settings][number_of_posts]'>";

        foreach ($options as $title => $value) {
            $selected = $value == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "</select>";

        return $html;
    }

    /**
     * Return a list of all custom fields registered in the database.
     *
     * @return array
     */
    private function get_custom_fields() {
        global $wpdb;

        $limit = (int) apply_filters('postmeta_form_limit', 30);

        $keys = $wpdb->get_col("
            SELECT meta_key
            FROM $wpdb->postmeta
            GROUP BY meta_key
            HAVING meta_key NOT LIKE '\_%'
            ORDER BY meta_key
            LIMIT $limit");

        if ($keys) {
            natcasesort($keys);

            return $keys;
        }

        return array();
    }

    /**
     * Generate a drop down list with custom field options
     * 
     * @return string drop down list HTML
     */
    private function get_dropdown_select_with_custom_fields($options, $default, $name) {
        $selected_option = isset($this->slide_settings[$name]) ? $this->slide_settings[$name] : $default;

        $html = "<select name='attachment[{$this->slide->ID}][settings][{$name}]'>";
        foreach ($options as $title => $value) {
            $selected = $value == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "<optgroup label='Custom Fields'>";
        foreach ($this->get_custom_fields() as $key) {
            $selected = $key == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$key}' {$selected}>{$key}</option>";
        }
        $html .= "</optgroup>";
        $html .= "</select>";

        return $html;
    }

    /**
     * Generate the 'link to' down list
     * 
     * @return string drop down list HTML
     */
    private function get_link_to_options() {
        $options = array(
            'Disabled' => 'disabled',
            'Post' => 'slug',
        );

        return $this->get_dropdown_select_with_custom_fields($options, 'slug', 'link_to');
    }

    /**
     * Generate the 'caption' drop down list
     * 
     * @return string drop down list HTML
     */
    private function get_caption_options() {
        $options = array(
            'Disabled' => 'disabled',
            'Post Title' => 'title',
            'Post Excerpt' => 'excerpt',
            'Post Content' => 'content'
        );

        return $this->get_dropdown_select_with_custom_fields($options, 'title', 'caption');
    }

    /**
     * Generate the sort direction drop down list
     * 
     * @return string drop down list HTML
     */
    private function get_order_direction_options() {
        $selected_direction = isset($this->slide_settings['order']) ? $this->slide_settings['order'] : 'DESC';

        $options = array(
            'DESC' => 'DESC',
            'ASC' => 'ASC'
        );

        $html = "<select name='attachment[{$this->slide->ID}][settings][order]'>";

        foreach ($options as $title => $value) {
            $selected = $value == $selected_direction ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "</select>";

        return $html;
    }

    /**
     * Generate post type selection list HTML
     * 
     * @return string html
     */
    private function get_post_type_options() {
        $all_post_types = get_post_types(array('public' => 'true'), 'objects'); 
        $selected_post_types =$this->get_selected_post_types();
        $exclude = array('page', 'attachment'); // names

        $options = "";

        foreach ($all_post_types as $post_type ) {
            if (!in_array($post_type->name, $exclude)) {
                $checked = in_array($post_type->name, $selected_post_types) ? "checked='checked'" : "";
                $options .= "<li><label><input type='checkbox' name='attachment[{$this->slide->ID}][settings][post_types][]' value='{$post_type->name}' {$checked} /> {$post_type->label}</label></li>";
            }
        }

        return "<div class='scroll'><ul>" . $options . "</ul></div>";
    }

    /**
     * Get the selected order direction
     * 
     * @return string ASC or DESC
     */
    private function get_order() {
        return isset($this->slide_settings['order']) ? $this->slide_settings['order'] : 'ASC';
    }

    /**
     * Get the selected order field
     * 
     * @return string field name
     */
    private function get_order_by() {
        return isset($this->slide_settings['order_by']) ? $this->slide_settings['order_by'] : 'date';
    }

    /**
     * Get the selected limit
     * 
     * @return int number of posts to display
     */
    private function get_number_of_posts() {
        return isset($this->slide_settings['number_of_posts']) ? $this->slide_settings['number_of_posts'] : 5;
    }

    /**
     * Get the selected tags
     * 
     * @return array selected tag IDs
     */
    private function get_selected_tags($taxonomy_name) {
        $selected = array();

        if (isset($this->slide_settings['tags']) && count($this->slide_settings['tags'])) {
            foreach ($this->slide_settings['tags'] as $tax => $tags) {
                if ($tax == $taxonomy_name) {
                    foreach ($tags as $tag) {
                        $selected[] = (int)$tag;
                    }                    
                }
            }
        }

        return $selected;
    }

    /**
     * Get selected post types
     * 
     * @return array selected post types
     */
    private function get_selected_post_types() {
        if (isset($this->slide_settings['post_types']) && count($this->slide_settings['post_types'])) {
            foreach($this->slide_settings['post_types'] as $key => $value) {
                $post_types[] = $value;
            }            
        } else {
            $post_types[] = 'post';
        }


        return $post_types;
    }

    /**
     * Public slide html
     * 
     * @return string html
     */
    protected function get_public_slide() {
        $slider_settings = get_post_meta($this->slider->ID, 'ml-slider_settings', true);

        $args['post_type'] = $this->get_selected_post_types();
        $args['posts_per_page'] = $this->get_number_of_posts();
        $args['order_by'] = $this->get_order_by();
        $args['order'] = $this->get_order();
        $args['meta_key'] = '_thumbnail_id';
        $args['tax_query'] = array('relation' => 'OR');

        // add taxonomy limits
        if (isset($this->slide_settings['tags']) && count($this->slide_settings['tags'])) {
            foreach ($this->slide_settings['tags'] as $tax => $tags) {
                $selected = array(); // reset the array

                foreach ($tags as $tag) {
                    $selected[] = (int)$tag; // list all checked categories for this taxonomy
                }

                if (count($selected)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $tax,
                        'field' => 'id',
                        'terms' => $selected
                    ); 
                }
            }
        }

        $the_query = new WP_Query( $args );

        $slides = array();

        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $id = get_post_thumbnail_id($the_query->post->ID);
            $thumb = false;
            
            if ($id > 0) {
                // initialise the image helper
                $imageHelper = new MetaSliderImageHelper(
                    $id,
                    $slider_settings['width'], 
                    $slider_settings['height'], 
                    isset($slider_settings['smartCrop']) ? $slider_settings['smartCrop'] : 'false'
                );
                $thumb = $imageHelper->get_image_url();
            }

            // go on to the next slide if we encounter an error
            if (is_wp_error($thumb) || !$thumb) {
                continue;
            }

            $selected_url_type = isset($this->slide_settings['link_to']) ? $this->slide_settings['link_to'] : 'slug';

            switch ($selected_url_type) {
                case "slug":
                    $url = get_permalink();
                    break;
                case "disabled":
                    $url = "";
                    break;
                default:
                    $url = get_post_meta($the_query->post->ID, $selected_url_type, true);
            }

            $selected_caption_type = isset($this->slide_settings['caption']) ? $this->slide_settings['caption'] : 'title';

            switch ($selected_caption_type) {
                case "title":
                    $caption = get_the_title();
                    break;
                case "disabled":
                    $caption = "";
                    break;
                case "excerpt":
                    $caption = get_the_excerpt();
                    break;
                case "content":
                    $caption = get_the_content();
                    break;
                default:
                    $caption = get_post_meta($the_query->post->ID, $selected_caption_type, true);
            }

            $slide = array(
                'thumb' => $thumb,
                'url' => $url,
                'alt' => get_post_meta(get_post_thumbnail_id($the_query->post->ID), '_wp_attachment_image_alt', true),
                'target' => '_self', 
                'caption' => html_entity_decode($caption, ENT_NOQUOTES, 'UTF-8'),
            );

            switch($slider_settings['type']) {
                case "coin":
                    $slides[] = $this->get_coin_slider_markup($slide);
                    break;
                case "flex":
                    $slides[] = $this->get_flex_slider_markup($slide);
                    break;
                case "nivo":
                    $slides[] = $this->get_nivo_slider_markup($slide);
                    break;
                case "responsive":
                    $slides[] = $this->get_responsive_slides_markup($slide);
                    break;
            }
        }

        return $slides;
    }

    /**
     * Get nivo slider markup for slide
     * 
     * @return string html
     */
    private function get_nivo_slider_markup($slide) {
        $html = "<img height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$slide['thumb']}' title='{$slide['caption']}' alt='{$slide['alt']}' />";

        if (strlen($slide['url'])) {
            $html = "<a href='{$slide['url']}' target='{$slide['target']}'>" . $html . "</a>";
        }

        return $html;
    }

    /**
     * Get flex slider markup for slide
     * 
     * @return string html
     */
    private function get_flex_slider_markup($slide) {
        $html = "<img height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$slide['thumb']}' alt='{$slide['alt']}' />";

        if (strlen($slide['url'])) {
            $html = "<a href='{$slide['url']}' target='{$slide['target']}'>" . $html . "</a>";
        }

        if (strlen($slide['caption'])) {
            $html .= "<div class='caption-wrap'><div class='caption'>" . $slide['caption'] . "</div></div>";
        }

        return $html;
    }

    /**
     * Get coin slider markup for slide
     * 
     * @return string html
     */
    private function get_coin_slider_markup($slide) {
        $url = strlen($slide['url']) ? $slide['url'] : "javascript:void(0)"; // coinslider always wants a URL

        $html  = "<a href='{$url}'>";
        $html .= "<img height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$slide['thumb']}' alt='{$slide['alt']}' />"; // target doesn't work with coin
        $html .= "<span>{$slide['caption']}</span>";
        $html .= "</a>";
        return $html;
    }

    /**
     * Get responsive slides markup for slide
     * 
     * @return string html
     */
    private function get_responsive_slides_markup($slide) {
        $html = "<img height='{$this->settings['height']}' width='{$this->settings['width']}' src='{$slide['thumb']}' alt='{$slide['alt']}' />";

        if (strlen($slide['url'])) {
            $html = "<a href='{$slide['url']}' target='{$slide['target']}'>" . $html . "</a>";
        }

        if (strlen($slide['caption'])) {
            $html .= "<div class='caption-wrap'><div class='caption'>" . $slide['caption'] . "</div></div>";
        }

        return $html;
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe() {
        return wp_iframe(array($this, 'iframe'));
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe() {
        wp_enqueue_style('media-views');
        wp_enqueue_style('metasliderpro-youtube-styles', METASLIDERPRO_ASSETS_URL . 'metaslider/post_feed/post_feed.css');
        wp_enqueue_script('metasliderpro-youtube-script', METASLIDERPRO_ASSETS_URL . 'metaslider/post_feed/post_feed.js', array('jquery'));

        echo "<div class='metaslider'>
                    <div class='media-embed'>
                        <div class='embed-link-settings'>Click 'Add to slider' to create a new post feed slide.</div>
                    </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large'>Add to slider</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Create a new post_feed slide
     * 
     * @return int ID of the created slide
     */
    public function create_slide($slider_id, $fields) {
        $this->set_slider($slider_id);

        // Attachment options
        $attachment = array(
            'post_title'=> "Meta Slider - Post Feed",
            'post_mime_type' => 'text/html',
            'menu_order' => $fields['menu_order']
        );

        $slide_id = wp_insert_attachment($attachment);

        // store the type as a meta field against the attachment
        $this->add_or_update_or_delete_meta($slide_id, 'type', 'post_feed');

        $this->set_slide($slide_id);

        $this->tag_slide_to_slider();

        return $slide_id;
    }

    /**
     * Save
     */
    protected function save($fields) {
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

        $this->add_or_update_or_delete_meta($this->slide->ID, 'settings', $fields['settings']);
    }
}

/**
 * Walker to output an unordered list of category checkbox <input> elements.
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 */
class Walker_MetaSlider_Checklist extends Walker {
    var $tree_type = 'category';
    var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
    var $slide_id;

    function __construct($slide_id) {
        $this->slide_id = $slide_id;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent<ul class='children'>\n";
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    function start_el( &$output, $category, $depth, $args, $id = 0 ) {
        extract($args);
        if ( empty($taxonomy) )
            $taxonomy = 'category';

        $name = "attachment[{$this->slide_id}][settings][tags][$taxonomy]";
        $output .= "\n<li>" . '<label><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
    }

    function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }
}


?>