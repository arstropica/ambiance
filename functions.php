<?php
    /**
    * Ambiance Theme
    Functions.php
    * 
    * PHP versions 4 and 5
    * 
    * @category   Theme Functions 
    * @package    WordPress
    * @author     ArsTropica <info@arstropica.com> 
    * @copyright  2014 ArsTropica 
    * @license    http://opensource.org/licenses/gpl-license.php GNU Public License 
    * @version    1.0 
    * @link       http://pear.php.net/package/ArsTropica  Reponsive Framework
    * @subpackage Ambiance Theme
    * @see        References to other sections (if any)...
    */
    // Definitions

    /* Define paths to theme directory */
    if (!defined('child_template_directory'))
        define('child_template_directory', dirname(__FILE__));

    if (!defined('child_template_url'))
        define('child_template_url', get_stylesheet_directory_uri());

    // Actions

    add_action('widgets_init', 'at_responsive_child_register_sidebars');

    add_action('wp_print_styles', 'at_responsive_child_theme_styles');

    /* Enqueue Theme Scripts */
    add_action('wp_enqueue_scripts', 'at_responsive_child_theme_scripts');

    /* Initialize Theme Building Functions */
    add_action('init', 'at_responsive_child_theme_setup', 10);

    /* Initialize Theme Grid Values */
    add_action('wp', 'at_responsive_child_theme_setup_grid', 10);

    /* Register Theme Sidebars */
    add_action('widgets_init', 'at_responsive_child_register_sidebars');

    /* Sidebar Widget Areas */
    add_action('at_responsive_primary_sidebar', 'at_responsive_do_primary_sidebar_widget_area');
    add_action('at_responsive_secondary_sidebar', 'at_responsive_do_secondary_sidebar_widget_area');

    /* Related Posts on Single Posts */
    add_action('at_responsive_loop_end', 'at_responsive_child_do_related_posts_single');

    // Filters

    /* Full width menu */
    // add_filter( 'nav_menu_css_class', 'at_responsive_child_fullwidth_dropdown_class', 10, 3);

    /* Add fixed class to dropdown menu */
    add_filter('at_responsive_dropdown_menu_classes', 'at_responsive_child_dropdown_menu_classes');

    /* Add submenu-wrap span to submenu items */
    add_filter('at_responsive_nav_menu_args', 'at_responsive_child_nav_menu_args', 10, 2);

    add_filter('at_responsive_post_entry', 'at_responsive_child_post_entry');

    /* Responsive Excerpt */
    add_filter('at_responsive_excerpt', 'at_responsive_child_excerpt', 20);

    /* Left Sidebar Grid Classes */
    add_filter('at_responsive_content_grid_classes', 'at_responsive_child_grid_classes');

    /* Customizer Grid Column Values */
    add_filter('at_responsive_content_grid', 'at_responsive_child_theme_filter_grid_customizer');

    add_filter('at_responsive_pagination_links', 'at_responsive_child_pagination_links');

    // Functions

    /**
    * Enqueue Theme Stylesheets
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_theme_styles() {
        // Load our main stylesheet.
        wp_enqueue_style('at-responsive-style', get_stylesheet_uri(), array('at-responsive-framework-style'));
        wp_enqueue_style('at-responsive-child-style', child_template_url . '/css/default.css', array('at-responsive-framework-style', 'at-common-style'));
        // Minified?
        wp_enqueue_style('at-responsive-child-style-min', child_template_url . '/css/default.css', array('at-reponsive-framework-minified-css-assets'));
    }

    /* Enqueue Theme Scripts */

    /**
    * Enqueue Theme Scripts
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_theme_scripts() {
        // Silence
    }

    /**
    * Setup Theme Building
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_theme_setup() {
        global $at_theme_custom;
        // Add Image size(s) for Header and Thumbnail Images
        add_image_size('at-header', 1440, 460, true);
        add_image_size('entry-thumbnail', 710, 420, true);
        add_image_size('single-thumbnail', 276);


        // Init MastHead
        add_action('at_responsive_hero', 'at_responsive_masthead');

        // Setup Theme Settings Defaults
        $args = array(
            'stickynav' => true,
            'excerptlength' => 55,
            'homeexcerptlength' => 55,
            'postsperpage' => 9,
            'homepostsperpage' => 3,
        );
        at_responsive_set_theme_settings_default_values($args);

    }

    /**
    * ArsTropica  Responsive Child Theme Setup Grid
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_theme_setup_grid() {
        global $wp_query;
        // Setup Grid Variables
        if (is_home() || is_front_page() || $wp_query->is_home) {
            at_responsive_set_content_grid_values(12, 5, 12, 12, 12, 4, 3, 12, 12, 12, 12, 12);
        } else if (isset($_REQUEST['guestlogin']) && isset($_REQUEST['redirect'])){
            at_responsive_set_content_grid_values(12, 5, 12, 12, 12, 4, 3, 12, 12, 12, 12, 12);
        } else {
            at_responsive_set_content_grid_values(12, 8, 12, 6, 12, 4, 3, 12, 12, 12, 12, 12);
        }
    }

    /**
    * ArsTropica Responsive Child Theme Filter Grid
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_theme_filter_grid_customizer($at_responsive_grid) {
        global $wp_query, $at_theme_custom;

        if ($at_theme_custom->is_customizer()) {
            $at_theme_custom->set_option($at_responsive_grid, 'appearance/grid', true);
        }

        return $at_responsive_grid;
    }

    /* Register Theme Sidebars */

    /**
    * Register Theme Sidebars
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_child_register_sidebars() {
        global $theme_namespace;
        register_sidebar(array(
            'name' => 'Primary Sidebar',
            'id' => 'primary_sidebar',
            'description' => __('Main sidebar that appears on the right.', $theme_namespace),
            'before_widget' => '<aside id="%1$s" class="widget at_widget %2$s" role="complementary"><div class="widget-frame">',
            'after_widget' => "</div></div></div></aside>",
            'before_title' => '<h4 class="widgettitle"><span>',
            'after_title' => '</span></h4><div class="widget-wrap"><div class="widget-content">',
        ));
        register_sidebar(array(
            'name' => 'Secondary Sidebar',
            'id' => 'secondary_sidebar',
            'description' => __('Secondary sidebar that appears on the left.', $theme_namespace),
            'before_widget' => '<aside id="%1$s" class="widget at_widget %2$s" role="complementary"><div class="widget-frame">',
            'after_widget' => "</div></div></div></aside>",
            'before_title' => '<h4 class="widgettitle">',
            'after_title' => '</h4><div class="widget-wrap"><div class="widget-content">',
        ));
    }

    /**
    * Handle Sidebar Widgets
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_do_primary_sidebar_widget_area() {
        $sidebar_name = 'Primary Sidebar';
        $sidebar_id = 'primary_sidebar';
        $display_placeholders = at_responsive_get_theme_option('settings/widgetplaceholders', true);
        $grid_values = at_responsive_get_content_grid_values();
        $grid_classes = at_responsive_get_content_grid_classes();
        if (is_active_sidebar($sidebar_id) || $display_placeholders) : ?>
        <div id="<?php echo at_responsive_slugify(strtolower($sidebar_id)); ?>" class="layout-column sidebar sidebar-layout-right col-md-<?php echo $grid_values['sidebar']; ?> <?php echo $grid_classes['sidebar']; ?>">
            <?php at_responsive_do_sidebar($sidebar_name, ''); ?>
        </div><!--!.layout-column-->
        <?php
            endif;
    }

    /**
    * Handle Sidebar Widgets
    * 
    * @since 1.0
    * @return void 
    */
    function at_responsive_do_secondary_sidebar_widget_area() {
        $sidebar_name = 'Secondary Sidebar';
        $sidebar_id = 'secondary_sidebar';
        $grid_values = at_responsive_get_content_grid_values();
        $grid_classes = at_responsive_get_content_grid_classes();
        $sidebar_col_width = 2;
        $display_placeholders = at_responsive_get_theme_option('settings/widgetplaceholders', true);
        if (is_active_sidebar($sidebar_id) || $display_placeholders) :
        ?>
        <div id="<?php echo at_responsive_slugify(strtolower($sidebar_id)); ?>" class="layout-column sidebar sidebar-layout-left col-md-<?php echo $grid_values['secondary']; ?> <?php echo $grid_classes['secondary']; ?> <?php echo $display_placeholders ? "placeholder" : ""; ?>">
            <?php at_responsive_do_sidebar($sidebar_name, ''); ?>
        </div><!--!.layout-column-->
        <?php
            endif;
    }

    /* Post Entry */

    /**
    * Post Entry
    * 
    * @param string $post_entry Parameter 
    * @since 1.0
    * @return string  Return 
    */
    function at_responsive_child_post_entry($post_entry) {
        global $post, $theme_namespace;
        $output = "";
        if (is_sticky() && is_home() && !is_paged()) {
            $output .= '<span class="featured-post">' . __('Sticky', $theme_namespace) . '</span>';
        }

        // Set up and print post meta information.
        if (is_singular()) :
            if (!is_single()) {
                return;
            }
            // Single Post
            $edit_post = get_edit_post_link() ? (' <span class="edit-link">(' . '<a class="post-edit-link" href="' . get_edit_post_link() . '">' . __('Edit') . '</a>)' . '</span>') : '';

            $output .= sprintf('<p><span class="author">by <span class="author vcard"><span class="fn"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>%6$s</span> <span class="date">on <span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span><time class="entry-date updated hidden" datetime="%2$s">' . get_the_modified_time(get_option('date_format')) . '</time></span></p>', esc_url(get_permalink()), esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_url(get_author_posts_url(get_the_author_meta('ID'))), get_the_author(), $edit_post
            );
            else :
            // Archives
            $output .= '<ul class="meta-items">';
            $output .= sprintf('<li>by <span class="author vcard"><span class="fn"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span></li> <li class="entry-date">on <span class="date published time" title="%3$s"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span><time class="entry-date updated hidden" datetime="%2$s">' . get_the_modified_time(get_option('date_format')) . '</time></li>', esc_url(get_permalink()), esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_url(get_author_posts_url(get_the_author_meta('ID'))), get_the_author()
            );
            if ($post && get_comments_number($post->ID) && (!post_password_required())) {
                $output .= '<li class="comments-link">';
                $output .= at_responsive_comments_popup_link(__('', $theme_namespace), __('1 Comment', $theme_namespace), __('% Comments', $theme_namespace), '', '', true);
                $output .= '</li>';
            }
            $output .= '</ul>';
        endif;

        return $output;
    }

    /**
    * Hide Excerpt on Mobile Devices
    * 
    * @param string $excerpt Parameter 
    * @since 1.0
    * @return string  Return 
    */
    function at_responsive_child_excerpt($excerpt) {
        return str_replace('class="teaser-text"', 'class="teaser-text hidden-xs"', $excerpt);
    }

    /**
    * Classes for Left Sidebar Layout
    * 
    * @param array $classes Parameter 
    * @since 1.0
    * @return array Return 
    */
    function at_responsive_child_grid_classes($classes) {
        if (!is_array($classes))
            $classes = array();
        if (!isset($classes['secondary']))
            $classes['secondary'] = '';
        if (!isset($classes['sidebar']))
            $classes['sidebar'] = '';
        if (!isset($classes['row']))
            $classes['row'] = '';
        if (is_home() || is_front_page()) {
            $classes['secondary'] .= ' col-md-push-9';
            $classes['sidebar'] .= ' col-md-pull-8';
            $classes['row'] .= ' col-md-push-1';
        }
        $vpage_ids = array();
        $grid_values = at_responsive_get_content_grid_values();
        if (class_exists('at_vpage_base')) {
            $vpage_ids = at_vpage_base::get_post_ids();
        }
        if ($vpage_ids && is_page($vpage_ids)) {
            // $classes['row'] .= " col-md-push-{$grid_values['sidebar']}";
            // $classes['sidebar'] .= " col-md-pull-{$grid_values['row']}";
        }
        return $classes;
    }

    /* Pagination Links */

    /**
    * Pagination Links
    * 
    * @param string $links Parameter 
    * @since 1.0
    * @return string  Return 
    */
    function at_responsive_child_pagination_links($links) {
        return str_replace('glyphicon-chevron', 'glyphicon-arrow', $links);
    }

    /**
    * Related Posts on Single Pages
    * 
    * @since 1.0
    * @return unknown Return 
    */
    function at_responsive_child_do_related_posts_single() {
        global $at_theme_custom, $at_related_posts;
        if (is_single()) {
            $enable_plugin = $at_theme_custom->get_option('settings/enableyarpp', false);
            if ($enable_plugin) {
                if (!$at_related_posts && class_exists('at_related_posts')) {
                    $at_related_posts = new at_related_posts();
                } elseif (!class_exists('at_related_posts')) {
                    return;
                }
                if (!$at_related_posts->has_related_posts()) {
                    return;
                }
                $at_related_posts->do_related_posts_widget(true, 12);
            }
        }
    }

    /**
    * Full width Nav dropdown class
    * 
    * @param array   $classes Parameter 
    * @param object $item    Parameter 
    * @param object  $args    Parameter 
    * @since 1.0
    * @return array   Return 
    */
    function at_responsive_child_fullwidth_dropdown_class($classes, $item, $args) {
        if (@$args->has_children)
            $classes[] = 'yamm-fw';

        return $classes;
    }

    /**
    * Add extra classes to dropdown menu
    * 
    * @param array $c Parameter 
    * @since 1.0
    * @return array Return 
    */
    function at_responsive_child_dropdown_menu_classes($c) {
        $c[] = 'fixed';
        return $c;
    }

    /**
    * Add centering <span> tag to menu items
    * 
    * @param object $args Parameter 
    * @param object $item Parameter 
    * @since 1.0
    * @return object Return 
    */
    function at_responsive_child_nav_menu_args($args, $item) {
        if ($item->menu_item_parent && is_object($args)) {
            if (isset($args->link_before) && !stristr($args->link_before, '<span class="submenu-wrap">'))
                $args->link_before .= ' <span class="submenu-wrap">';
            else
                $args->link_before = '<span class="submenu-wrap">';

            if (isset($args->link_after) && !stristr($args->link_after, '</span>'))
                $args->link_after .= ' </span>';
            else
                $args->link_after = '</span>';
        }
        return $args;
    }
