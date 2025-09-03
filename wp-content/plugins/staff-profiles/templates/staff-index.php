<?php
/**
 * Template Name: Staff Index (Grouped by Location)
 * Description: Lists all Staff profiles grouped by location, with photo, job title, and phones.
 */
if (!defined('ABSPATH')) exit;

get_header();

/** Helpers */
function agd_staff_phone_tel($s) { return preg_replace('/[^0-9+]/', '', (string)$s); }
function agd_staff_location($post_id) {
    // Try a few common keys; feel free to change the primary key to whatever you set in your plugin
    $loc = get_post_meta($post_id, 'location', true);
    if (!$loc) $loc = get_post_meta($post_id, 'office_location', true);
    if (!$loc) $loc = get_post_meta($post_id, 'city', true);
    return $loc ? trim($loc) : 'Team';
}

/** Query all staff */
$q = new WP_Query([
    'post_type'      => 'staff',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
]);

$by_location = [];
if ($q->have_posts()) {
    while ($q->have_posts()) { $q->the_post();
        $id    = get_the_ID();
        $name  = get_the_title();
        $url   = get_permalink($id);
        $job   = get_post_meta($id, 'job_title', true);
        $mobi  = get_post_meta($id, 'mobile_phone', true);
        $office= get_post_meta($id, 'office_phone', true);
        $img   = get_the_post_thumbnail_url($id, 'medium') ?: 'https://blocks.astratic.com/img/general-img-landscape.png';

        $loc = agd_staff_location($id);

        $by_location[$loc]['staff'][] = [
            'name'         => $name,
            'url'          => $url,
            'portrait'     => $img,
            'job_title'    => $job,
            'mobile_phone' => $mobi,
            'office_phone' => $office,
        ];
    }
    wp_reset_postdata();
}

/** Sort locations alphabetically */
$loc_names = array_keys($by_location);
natcasesort($loc_names);
?>

<main class="container my-5 staff-index">
    <?php if (empty($by_location)): ?>
        <p>No staff found.</p>
    <?php else: ?>
        <?php foreach ($loc_names as $loc): ?>
            <?php $group = $by_location[$loc]; ?>
            <section class="staff-location pt-5">
                <h4 class="mb-3"><?php echo esc_html($loc); ?></h4>

                <div class="row">
                    <?php foreach ($group['staff'] as $sm): ?>
                        <div class="staff-block col-lg-3 col-sm-12 mb-4">
                            <div class="users_box card h-100">
                                <a href="<?php echo esc_url($sm['url']); ?>" class="avatar">
                                    <img class="card-img-top" src="<?php echo esc_url($sm['portrait']); ?>"
                                         alt="<?php echo esc_attr($sm['name']); ?>">
                                </a>

                                <div class="users_box_info card-body d-flex flex-column">
                                    <h6 class="user_title mb-1">
                                        <a href="<?php echo esc_url($sm['url']); ?>">
                                            <?php echo esc_html($sm['name']); ?>
                                        </a>
                                    </h6>

                                    <?php if (!empty($sm['job_title'])): ?>
                                        <p class="user_job_title text-muted mb-3"><?php echo esc_html($sm['job_title']); ?></p>
                                    <?php endif; ?>

                                    <div class="users_phone_box mt-auto">
                                        <?php if (!empty($sm['mobile_phone'])): ?>
                                            <div class="users_phone_box_field">
                                                <span class="users_phone_box_label">M: </span>
                                                <a href="tel:<?php echo esc_attr(agd_staff_phone_tel($sm['mobile_phone'])); ?>">
                                                    <span class="users_phone_box_value"><?php echo esc_html($sm['mobile_phone']); ?></span>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($sm['office_phone'])): ?>
                                            <div class="users_phone_box_field">
                                                <span class="users_phone_box_label">O: </span>
                                                <a href="tel:<?php echo esc_attr(agd_staff_phone_tel($sm['office_phone'])); ?>">
                                                    <span class="users_phone_box_value"><?php echo esc_html($sm['office_phone']); ?></span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div><!-- /.card-body -->
                            </div><!-- /.card -->
                        </div><!-- /.col -->
                    <?php endforeach; ?>
                </div><!-- /.row -->
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<style>
    /* Light, theme-friendly styles */
    .staff-index .card-img-top{aspect-ratio:1/1;object-fit:cover}
    .staff-index .users_phone_box_field{margin:.15rem 0}
    .staff-index .users_phone_box_label{font-weight:600}
</style>

<?php get_footer(); ?>
