<?php
/**
 * Plugin Name: Staff Profiles
 * Description: Custom Post Type "Staff" with fields for title/role, email, phones, Boats.com party_id, and social links.
 * Version: 1.0.0
 * Author: You
 */

if (!defined('ABSPATH')) exit;

class AGD_Staff_Profiles {
    const CPT = 'staff';
    const META = [
        'job_title'       => ['type' => 'string'], // staff role/title
        'email'           => ['type' => 'string'],
        'mobile_phone'    => ['type' => 'string'],
        'office_phone'    => ['type' => 'string'],
        'boats_party_id'  => ['type' => 'string'],
        'facebook_url'    => ['type' => 'string'],
        'twitter_url'     => ['type' => 'string'],
        'youtube_url'     => ['type' => 'string'],
        'instagram_url'   => ['type' => 'string'],
        'linkedin_url'    => ['type' => 'string'],
    ];

    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('init', [$this, 'register_meta']);
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('save_post_' . self::CPT, [$this, 'save_meta'], 10, 2);
        add_filter('manage_' . self::CPT . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [$this, 'column_content'], 10, 2);
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'deactivate']);
    }

    public static function activate() { (new self)->register_cpt(); flush_rewrite_rules(); }
    public static function deactivate() { flush_rewrite_rules(); }

    public function register_cpt() {
        $labels = [
            'name'               => 'Staff',
            'singular_name'      => 'Staff Member',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Staff Member',
            'edit_item'          => 'Edit Staff Member',
            'new_item'           => 'New Staff Member',
            'view_item'          => 'View Staff Member',
            'search_items'       => 'Search Staff',
            'not_found'          => 'No staff found',
            'not_found_in_trash' => 'No staff found in Trash',
            'menu_name'          => 'Staff',
        ];

        register_post_type(self::CPT, [
            'labels'        => $labels,
            'public'        => true,
            'has_archive'   => true,
            'menu_icon'     => 'dashicons-groups',
            'rewrite'       => ['slug' => 'staff'],
            'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'  => true, // Gutenberg + REST
        ]);
    }

    public function register_meta() {
        foreach (self::META as $key => $args) {
            register_post_meta(self::CPT, $key, [
                'type'         => $args['type'],
                'single'       => true,
                'show_in_rest' => true,
                'auth_callback'=> function() { return current_user_can('edit_posts'); },
                'sanitize_callback' => [$this, 'sanitize_meta'],
            ]);
        }
    }

    public function sanitize_meta($value, $meta_key, $object_type) {
        switch ($meta_key) {
            case 'email':
                return is_email($value) ? sanitize_email($value) : '';
            case 'mobile_phone':
            case 'office_phone':
                // keep digits, +, (), space, -, and extension markers
                return preg_replace('/[^0-9\+\-\(\)\sxext\.]/i', '', $value);
            case 'facebook_url':
            case 'twitter_url':
            case 'youtube_url':
            case 'instagram_url':
            case 'linkedin_url':
                return esc_url_raw($value);
            case 'boats_party_id':
                return sanitize_text_field($value);
            case 'job_title':
            default:
                return sanitize_text_field($value);
        }
    }

    public function add_metabox() {
        add_meta_box(
            'agd_staff_details',
            'Staff Details',
            [$this, 'render_metabox'],
            self::CPT,
            'normal',
            'high'
        );
    }

    public function render_metabox($post) {
        wp_nonce_field('agd_staff_save_meta', 'agd_staff_nonce');
        $v = function($key) use ($post) { return esc_attr(get_post_meta($post->ID, $key, true) ?: ''); };
        ?>
        <style>
            .agd-grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
            .agd-grid .full { grid-column: 1 / -1; }
            .agd-grid label { font-weight:600; display:block; margin-bottom:4px; }
            .agd-grid input { width:100%; }
        </style>
        <div class="agd-grid">
            <div class="full">
                <label for="job_title">Title / Role</label>
                <input type="text" id="job_title" name="job_title" value="<?php echo $v('job_title'); ?>" />
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $v('email'); ?>" />
            </div>
            <div>
                <label for="boats_party_id">Boats.com Party ID</label>
                <input type="text" id="boats_party_id" name="boats_party_id" value="<?php echo $v('boats_party_id'); ?>" />
            </div>
            <div>
                <label for="mobile_phone">Mobile Phone</label>
                <input type="text" id="mobile_phone" name="mobile_phone" value="<?php echo $v('mobile_phone'); ?>" />
            </div>
            <div>
                <label for="office_phone">Office Phone</label>
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
            <div class="full" style="opacity:.8">
                <em>Tip: set the <strong>Profile Image</strong> using the Featured Image panel.</em>
            </div>
        </div>
        <?php
    }

    public function save_meta($post_id, $post) {
        if (!isset($_POST['agd_staff_nonce']) || !wp_verify_nonce($_POST['agd_staff_nonce'], 'agd_staff_save_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        foreach (array_keys(self::META) as $key) {
            if (isset($_POST[$key])) {
                $value = $this->sanitize_meta($_POST[$key], $key, 'post');
                update_post_meta($post_id, $key, $value);
            } else {
                // allow clearing fields
                delete_post_meta($post_id, $key);
            }
        }
    }

    public function columns($cols) {
        $new = [];
        $new['cb'] = $cols['cb'];
        $new['title'] = 'Name';
        $new['job_title'] = 'Title';
        $new['email'] = 'Email';
        $new['mobile_phone'] = 'Mobile';
        $new['office_phone'] = 'Office';
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
}

new AGD_Staff_Profiles();


// --- Shortcode: [staff_list] -----------------------------------------------
add_shortcode('staff_list', function($atts = []) {
    $a = shortcode_atts([
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'columns'        => 3,          // 1–6
        'include_social' => 'true',     // true|false
        'ids'            => '',         // comma-separated post IDs to include (optional)
    ], $atts, 'staff_list');

    $args = [
        'post_type'      => 'staff',
        'posts_per_page' => intval($a['posts_per_page']),
        'orderby'        => sanitize_key($a['orderby']),
        'order'          => (strtoupper($a['order']) === 'DESC') ? 'DESC' : 'ASC',
    ];

    if (!empty($a['ids'])) {
        $ids = array_filter(array_map('intval', explode(',', $a['ids'])));
        if ($ids) { $args['post__in'] = $ids; $args['orderby'] = 'post__in'; }
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) return '<div class="staff-list staff-empty">No staff found.</div>';

    $cols = max(1, min(6, intval($a['columns'])));
    ob_start();
    ?>
    <div class="staff-list grid cols-<?php echo esc_attr($cols); ?>">
        <?php while ($q->have_posts()): $q->the_post();
            $id   = get_the_ID();
            $meta = [
                'job_title'      => get_post_meta($id, 'job_title', true),
                'email'          => get_post_meta($id, 'email', true),
                'mobile_phone'   => get_post_meta($id, 'mobile_phone', true),
                'office_phone'   => get_post_meta($id, 'office_phone', true),
                'facebook_url'   => get_post_meta($id, 'facebook_url', true),
                'twitter_url'    => get_post_meta($id, 'twitter_url', true),
                'instagram_url'  => get_post_meta($id, 'instagram_url', true),
                'linkedin_url'   => get_post_meta($id, 'linkedin_url', true),
                'youtube_url'    => get_post_meta($id, 'youtube_url', true),
            ];
            ?>
            <div class="staff-card">
                <div class="staff-photo">
                    <?php if (has_post_thumbnail($id)) { echo get_the_post_thumbnail($id, 'medium'); } ?>
                </div>
                <div class="staff-body">
                    <h3 class="staff-name"><?php echo esc_html(get_the_title()); ?></h3>
                    <?php if ($meta['job_title']): ?>
                        <div class="staff-title"><?php echo esc_html($meta['job_title']); ?></div>
                    <?php endif; ?>

                    <div class="staff-contact">
                        <?php if ($meta['email']): ?>
                            <div><a href="mailto:<?php echo esc_attr($meta['email']); ?>"><?php echo esc_html($meta['email']); ?></a></div>
                        <?php endif; ?>
                        <?php if ($meta['mobile_phone']): ?>
                            <div><?php echo esc_html($meta['mobile_phone']); ?></div>
                        <?php endif; ?>
                        <?php if ($meta['office_phone']): ?>
                            <div><?php echo esc_html($meta['office_phone']); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ('true' === strtolower($a['include_social'])): ?>
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
});

// Optional minimal CSS (front-end). Remove if you’ll style in theme.
add_action('wp_enqueue_scripts', function() {
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
    wp_register_style('staff-list-inline', false);
    wp_enqueue_style('staff-list-inline');
    wp_add_inline_style('staff-list-inline', $css);
});