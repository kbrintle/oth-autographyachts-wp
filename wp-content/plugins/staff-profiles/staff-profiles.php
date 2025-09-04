<?php
/**
 * Plugin Name: Staff Profiles
 * Description: CPT “Staff” + meta (role/title, email, phones, Boats.com party_id, social links) and a simple listing shortcode.
 * Version: 1.1.0
 * Author: You
 * Text Domain: agd-staff
 */

if (!defined('ABSPATH')) exit;

define('AGD_STAFF_VERSION', '1.1.0');
define('AGD_STAFF_TEXTDOMAIN', 'agd-staff');

final class AGD_Staff_Profiles {
    const CPT  = 'staff';
    const SLUG = 'staff';

    // meta keys => type
    const META = [
            'job_title'       => 'string',
            'email'           => 'string',
            'mobile_phone'    => 'string',
            'office_phone'    => 'string',
            'boats_party_id'  => 'string',
            'facebook_url'    => 'string',
            'twitter_url'     => 'string',
            'youtube_url'     => 'string',
            'instagram_url'   => 'string',
            'linkedin_url'    => 'string',
    ];

    public static function activate() {
        (new self)->register_cpt();
        flush_rewrite_rules();
    }
    public static function deactivate() {
        flush_rewrite_rules();
    }

    public function __construct() {
        // Core
        add_action('init',                    [$this, 'register_cpt']);
        add_action('init',                    [$this, 'register_meta']);

        // Admin UI
        add_action('add_meta_boxes',          [$this, 'add_metabox']);
        add_action('save_post_' . self::CPT,  [$this, 'save_meta'], 10, 2);
        add_filter("manage_" . self::CPT . "_posts_columns",        [$this, 'columns']);
        add_action("manage_" . self::CPT . "_posts_custom_column",  [$this, 'column_content'], 10, 2);
        add_filter("manage_edit-" . self::CPT . "_sortable_columns",[$this, 'sortable_columns']);
        add_action('pre_get_posts',           [$this, 'admin_sorting']);

        // Frontend niceties
        add_action('after_setup_theme',       [$this, 'images']);
        add_action('wp_enqueue_scripts',      [$this, 'frontend_css']);

        // Optional fallback template if theme has none
        add_filter('single_template',         [$this, 'maybe_fallback_template']);

        // Shortcode
        add_shortcode('staff_list',           [$this, 'shortcode_staff_list']);
    }

    /* -------------------------
     * Post type
     * -----------------------*/
    public function register_cpt() {
        $labels = [
                'name'               => __('Staff', AGD_STAFF_TEXTDOMAIN),
                'singular_name'      => __('Staff Member', AGD_STAFF_TEXTDOMAIN),
                'add_new'            => __('Add New', AGD_STAFF_TEXTDOMAIN),
                'add_new_item'       => __('Add New Staff Member', AGD_STAFF_TEXTDOMAIN),
                'edit_item'          => __('Edit Staff Member', AGD_STAFF_TEXTDOMAIN),
                'new_item'           => __('New Staff Member', AGD_STAFF_TEXTDOMAIN),
                'view_item'          => __('View Staff Member', AGD_STAFF_TEXTDOMAIN),
                'search_items'       => __('Search Staff', AGD_STAFF_TEXTDOMAIN),
                'not_found'          => __('No staff found', AGD_STAFF_TEXTDOMAIN),
                'not_found_in_trash' => __('No staff found in Trash', AGD_STAFF_TEXTDOMAIN),
                'menu_name'          => __('Staff', AGD_STAFF_TEXTDOMAIN),
        ];

        register_post_type(self::CPT, [
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'show_in_rest'       => true,
                'has_archive'        => true,
                'rewrite'            => ['slug' => self::SLUG, 'with_front' => false],
                'menu_icon'          => 'dashicons-groups',
                'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
                'capability_type'    => 'post',
                'map_meta_cap'       => true,
        ]);
    }

    /* -------------------------
     * Meta registration
     * -----------------------*/
    public function register_meta() {
        foreach (self::META as $key => $type) {
            register_post_meta(self::CPT, $key, [
                    'type'              => $type,
                    'single'            => true,
                    'show_in_rest'      => true,
                    'auth_callback'     => fn() => current_user_can('edit_posts'),
                    'sanitize_callback' => [$this, 'sanitize_meta'],
                    'description'       => sprintf(__('Staff field: %s', AGD_STAFF_TEXTDOMAIN), $key),
            ]);
        }
    }

    public function sanitize_meta($value, $meta_key) {
        switch ($meta_key) {
            case 'email':
                return is_email($value) ? sanitize_email($value) : '';
            case 'mobile_phone':
            case 'office_phone':
                // Allow digits, +, (), space, -, ext markers
                return preg_replace('/[^0-9\+\-\(\)\sxeXtT\.]/', '', (string) $value);
            case 'facebook_url':
            case 'twitter_url':
            case 'youtube_url':
            case 'instagram_url':
            case 'linkedin_url':
                return esc_url_raw($value);
            case 'boats_party_id':
            case 'job_title':
            default:
                return sanitize_text_field($value);
        }
    }

    /* -------------------------
     * Metabox
     * -----------------------*/
    public function add_metabox() {
        add_meta_box(
                'agd_staff_details',
                __('Staff Details', AGD_STAFF_TEXTDOMAIN),
                [$this, 'render_metabox'],
                self::CPT, 'normal', 'high'
        );
    }

    public function render_metabox($post) {
        wp_nonce_field('agd_staff_save_meta', 'agd_staff_nonce');
        $v = fn($k) => esc_attr(get_post_meta($post->ID, $k, true) ?: '');
        ?>
        <style>
            .agd-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
            .agd-grid .full { grid-column:1 / -1; }
            .agd-grid label { font-weight:600; display:block; margin-bottom:4px; }
            .agd-grid input { width:100%; }
        </style>
        <div class="agd-grid">
            <div class="full">
                <label for="job_title"><?php _e('Title / Role', AGD_STAFF_TEXTDOMAIN); ?></label>
                <input type="text" id="job_title" name="job_title" value="<?php echo $v('job_title'); ?>" />
            </div>

            <div>
                <label for="email"><?php _e('Email', AGD_STAFF_TEXTDOMAIN); ?></label>
                <input type="email" id="email" name="email" value="<?php echo $v('email'); ?>" />
            </div>
            <div>
                <label for="boats_party_id"><?php _e('Boats.com Party ID', AGD_STAFF_TEXTDOMAIN); ?></label>
                <input type="text" id="boats_party_id" name="boats_party_id" value="<?php echo $v('boats_party_id'); ?>" />
            </div>

            <div>
                <label for="mobile_phone"><?php _e('Mobile Phone', AGD_STAFF_TEXTDOMAIN); ?></label>
                <input type="text" id="mobile_phone" name="mobile_phone" value="<?php echo $v('mobile_phone'); ?>" />
            </div>
            <div>
                <label for="office_phone"><?php _e('Office Phone', AGD_STAFF_TEXTDOMAIN); ?></label>
                <input type="text" id="office_phone" name="office_phone" value="<?php echo $v('office_phone'); ?>" />
            </div>

            <div class="full"><hr></div>

            <div>
                <label for="facebook_url">Facebook URL</label>
                <input type="url" id="facebook_url" name="facebook_url" value="<?php echo $v('facebook_url'); ?>" />
            </div>
            <div>
                <label for="twitter_url">Twitter/X URL</label>
                <input type="url" id="twitter_url" name="twitter_url" value="<?php echo $v('twitter_url'); ?>" />
            </div>
            <div>
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" value="<?php echo $v('instagram_url'); ?>" />
            </div>
            <div>
                <label for="linkedin_url">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo $v('linkedin_url'); ?>" />
            </div>
            <div class="full">
                <label for="youtube_url">YouTube URL</label>
                <input type="url" id="youtube_url" name="youtube_url" value="<?php echo $v('youtube_url'); ?>" />
            </div>

            <div class="full" style="opacity:.85">
                <em><?php _e('Tip: set the Profile Image using the Featured Image panel.', AGD_STAFF_TEXTDOMAIN); ?></em>
            </div>
        </div>
        <?php
    }

    public function save_meta($post_id, $post) {
        if (!isset($_POST['agd_staff_nonce']) || !wp_verify_nonce($_POST['agd_staff_nonce'], 'agd_staff_save_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        foreach (array_keys(self::META) as $key) {
            if (array_key_exists($key, $_POST)) {
                $val = $this->sanitize_meta($_POST[$key], $key);
                if ($val === '' || $val === null) {
                    delete_post_meta($post_id, $key);
                } else {
                    update_post_meta($post_id, $key, $val);
                }
            } else {
                // allow clearing
                delete_post_meta($post_id, $key);
            }
        }
    }

    /* -------------------------
     * Admin list table columns
     * -----------------------*/
    public function columns($cols) {
        $new = [];
        $new['cb']           = $cols['cb'] ?? '';
        $new['title']        = __('Name', AGD_STAFF_TEXTDOMAIN);
        $new['job_title']    = __('Title', AGD_STAFF_TEXTDOMAIN);
        $new['email']        = __('Email', AGD_STAFF_TEXTDOMAIN);
        $new['mobile_phone'] = __('Mobile', AGD_STAFF_TEXTDOMAIN);
        $new['office_phone'] = __('Office', AGD_STAFF_TEXTDOMAIN);
        $new['date']         = $cols['date'] ?? __('Date');
        return $new;
    }

    public function column_content($col, $post_id) {
        switch ($col) {
            case 'job_title':
                echo esc_html(get_post_meta($post_id, 'job_title', true));
                break;
            case 'email':
                $email = get_post_meta($post_id, 'email', true);
                if ($email) echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                break;
            case 'mobile_phone':
                echo esc_html(get_post_meta($post_id, 'mobile_phone', true));
                break;
            case 'office_phone':
                echo esc_html(get_post_meta($post_id, 'office_phone', true));
                break;
        }
    }

    public function sortable_columns($cols) {
        $cols['job_title'] = 'job_title';
        $cols['email']     = 'email';
        return $cols;
    }

    public function admin_sorting($query) {
        if (!is_admin() || !$query->is_main_query()) return;
        if ($query->get('post_type') !== self::CPT) return;

        $orderby = $query->get('orderby');
        if ($orderby === 'job_title' || $orderby === 'email') {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value');
        }
    }

    /* -------------------------
     * Frontend helpers
     * -----------------------*/
    public function images() {
        add_image_size('staff-card', 480, 480, true);
    }

    public function frontend_css() {
        $css = '
        .staff-list.grid{display:grid;gap:20px}
        .staff-list.grid.cols-1{grid-template-columns:1fr}
        .staff-list.grid.cols-2{grid-template-columns:repeat(2,1fr)}
        .staff-list.grid.cols-3{grid-template-columns:repeat(3,1fr)}
        .staff-list.grid.cols-4{grid-template-columns:repeat(4,1fr)}
        @media(max-width:900px){.staff-list.grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:600px){.staff-list.grid{grid-template-columns:1fr}}
        .staff-card{border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fff}
        .staff-photo img{width:100%;height:auto;display:block;aspect-ratio:1/1;object-fit:cover}
        .staff-body{padding:14px}
        .staff-name{margin:0 0 4px}
        .staff-title{color:#666;margin-bottom:8px}
        .staff-contact div{margin:2px 0}
        .staff-social{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
        .staff-social .soc{font-size:13px;text-decoration:none;border:1px solid #e5e7eb;border-radius:999px;padding:4px 8px}
        ';
        wp_register_style('agd-staff-inline', false, [], AGD_STAFF_VERSION);
        wp_enqueue_style('agd-staff-inline');
        wp_add_inline_style('agd-staff-inline', $css);
    }

    /**
     * If the theme doesn’t provide single-staff.php, load the plugin’s minimal fallback.
     */
    public function maybe_fallback_template($template) {
        if (is_singular(self::CPT)) {
            $theme_file = locate_template(['single-' . self::CPT . '.php']);
            if (!$theme_file) {
                $fallback = __DIR__ . '/templates/single-staff-fallback.php';
                if (file_exists($fallback)) return $fallback;
            }
        }
        return $template;
    }

    /* -------------------------
     * Shortcode: [staff_list]
     * -----------------------*/
    public function shortcode_staff_list($atts = []) {
        $a = shortcode_atts([
                'posts_per_page' => -1,
                'orderby'        => 'title',  // title | date | rand | menu_order
                'order'          => 'ASC',
                'columns'        => 3,        // 1-6
                'include_social' => 'true',
                'ids'            => '',       // comma-separated post IDs
        ], $atts, 'staff_list');

        $args = [
                'post_type'      => self::CPT,
                'posts_per_page' => intval($a['posts_per_page']),
                'orderby'        => sanitize_key($a['orderby']),
                'order'          => (strtoupper($a['order']) === 'DESC') ? 'DESC' : 'ASC',
        ];

        if (!empty($a['ids'])) {
            $ids = array_filter(array_map('intval', explode(',', $a['ids'])));
            if ($ids) { $args['post__in'] = $ids; $args['orderby'] = 'post__in'; }
        }

        $q = new WP_Query($args);
        if (!$q->have_posts()) return '<div class="staff-list staff-empty">' . esc_html__('No staff found.', AGD_STAFF_TEXTDOMAIN) . '</div>';

        $cols = max(1, min(6, intval($a['columns'])));
        ob_start(); ?>
        <div class="staff-list grid cols-<?php echo esc_attr($cols); ?>">
            <?php while ($q->have_posts()): $q->the_post();
                $id   = get_the_ID();
                $meta = [];
                foreach (array_keys(self::META) as $k) { $meta[$k] = get_post_meta($id, $k, true); }
                ?>
                <div class="staff-card">
                    <div class="staff-photo">
                        <?php
                        if (has_post_thumbnail($id)) echo get_the_post_thumbnail($id, 'staff-card', ['loading' => 'lazy']);
                        ?>
                    </div>
                    <div class="staff-body">
                        <h3 class="staff-name"><?php the_title(); ?></h3>
                        <?php if (!empty($meta['job_title'])): ?>
                            <div class="staff-title"><?php echo esc_html($meta['job_title']); ?></div>
                        <?php endif; ?>

                        <div class="staff-contact">
                            <?php if (!empty($meta['email'])): ?>
                                <div><a href="mailto:<?php echo esc_attr($meta['email']); ?>"><?php echo esc_html($meta['email']); ?></a></div>
                            <?php endif; ?>
                            <?php if (!empty($meta['mobile_phone'])): ?>
                                <div><?php echo esc_html($meta['mobile_phone']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($meta['office_phone'])): ?>
                                <div><?php echo esc_html($meta['office_phone']); ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (strtolower($a['include_social']) === 'true'): ?>
                            <div class="staff-social">
                                <?php
                                $social = ['facebook_url'=>'Facebook','twitter_url'=>'Twitter','instagram_url'=>'Instagram','linkedin_url'=>'LinkedIn','youtube_url'=>'YouTube'];
                                foreach ($social as $k=>$label) {
                                    if (!empty($meta[$k])) {
                                        echo '<a class="soc" href="' . esc_url($meta[$k]) . '" target="_blank" rel="noopener">' . esc_html($label) . '</a>';
                                    }
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
// In your plugin main file
// In your Staff plugin main file
define('AGD_STAFF_TPL_DIR', plugin_dir_path(__FILE__) . 'templates/');
add_filter('get_the_archive_title', function ($title) {
    if (is_post_type_archive('staff')) return 'Team';
    return $title;
});
add_filter('template_include', function ($template) {
    // Force our archive template for /staff
    if (is_post_type_archive('staff')) {
        $t = AGD_STAFF_TPL_DIR . 'staff-index.php';
        if (file_exists($t)) return $t;
    }
    // Force our single staff template
    if (is_singular('staff')) {
        $t = AGD_STAFF_TPL_DIR . 'staff-detail.php';
        if (file_exists($t)) return $t;
    }
    return $template;
}, 999);

// Load CSS only on single Staff pages
add_action('elementor/frontend/after_enqueue_styles', function () {
    if (is_singular('staff')) {
        $rel  = 'assets/css/staff.css';
        $abs  = plugin_dir_path(__FILE__) . $rel;
        $ver  = file_exists($abs) ? filemtime($abs) : null;

        // Depend on elementor-frontend so we’re guaranteed to come after it
        wp_enqueue_style(
                'agd-staff-detail',
                plugin_dir_url(__FILE__) . $rel,
                ['elementor-frontend'],
                $ver
        );
    }
}, 99);

/* Boot */
$__agd_staff = new AGD_Staff_Profiles();
register_activation_hook(__FILE__, ['AGD_Staff_Profiles', 'activate']);
register_deactivation_hook(__FILE__, ['AGD_Staff_Profiles', 'deactivate']);

/* ---------------------------------------------------------
 * Optional fallback template file content (very minimal)
 * Save as /templates/single-staff-fallback.php in this plugin.
 * ---------------------------------------------------------*/
/*
<?php
if (!defined('ABSPATH')) exit;
get_header();
the_post();
$meta = [];
foreach (AGD_Staff_Profiles::META as $k => $t) $meta[$k] = get_post_meta(get_the_ID(), $k, true);
?>
<main class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h1><?php the_title(); ?></h1>
            <?php if (!empty($meta['job_title'])): ?>
                <p class="text-muted"><?php echo esc_html($meta['job_title']); ?></p>
            <?php endif; ?>
            <article class="content"><?php the_content(); ?></article>
        </div>
        <div class="col-md-4">
            <?php if (has_post_thumbnail()) echo get_the_post_thumbnail(null, 'large', ['class' => 'img-fluid mb-3']); ?>
            <ul class="list-unstyled">
                <?php if (!empty($meta['email'])): ?>
                    <li><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($meta['email']); ?>"><?php echo esc_html($meta['email']); ?></a></li>
                <?php endif; ?>
                <?php if (!empty($meta['mobile_phone'])): ?>
                    <li><strong>Mobile:</strong> <?php echo esc_html($meta['mobile_phone']); ?></li>
                <?php endif; ?>
                <?php if (!empty($meta['office_phone'])): ?>
                    <li><strong>Office:</strong> <?php echo esc_html($meta['office_phone']); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</main>
<?php get_footer();
*/
