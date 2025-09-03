<?php
/**
 * Staff Detail Template
 * Path: /template/staff-detail.php
 *
 * Requirements:
 * - CPT "staff" (from your Staff Profiles plugin)
 * - Meta: job_title, email, mobile_phone, office_phone, boats_party_id, + socials
 * - Boats cache helpers: boats_get_by_party($party_id, $status)
 */
if (!defined('ABSPATH')) exit;

get_header();
the_post();

// ---------- Collect staff meta ----------
$staff_id = get_the_ID();
$meta_keys = ['job_title','email','mobile_phone','office_phone','facebook_url','twitter_url','instagram_url','linkedin_url','youtube_url','boats_party_id'];
$meta = [];
foreach ($meta_keys as $k) $meta[$k] = get_post_meta($staff_id, $k, true);

// ---------- Find boats for this staff (by party id) ----------
$party_id = trim((string)($meta['boats_party_id'] ?? ''));
$active_boats = (function_exists('boats_get_by_party') && $party_id) ? boats_get_by_party($party_id, 'Active') : [];
$sold_boats   = (function_exists('boats_get_by_party') && $party_id) ? boats_get_by_party($party_id, 'Sold')   : [];

// ---------- Small helpers ----------
function agd_boat_title($b) {
    return trim(implode(' ', array_filter([
        $b['ModelYear'] ?? '',
        $b['MakeStringExact'] ?? ($b['MakeString'] ?? ''),
        $b['Model'] ?? ''
    ])));
}
function agd_boat_url($b) {
    $slug = $b['slug'] ?? '';
    if (!$slug) {
        $slug = sanitize_title(trim(
            ($b['MakeStringExact'] ?? $b['MakeString'] ?? '') . '-' .
            ($b['Model'] ?? '') . '-' .
            ($b['DocumentID'] ?? '')
        ));
    }
    return home_url('/yachts-for-sale/' . $slug);
}
function agd_boat_card($b, $is_sold = false) { ?>
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card h-100">
            <?php if ($is_sold): ?>
                <span class="badge bg-secondary position-absolute m-2" style="z-index:2;">Sold</span>
            <?php endif; ?>
            <a href="<?php echo esc_url(agd_boat_url($b)); ?>">
                <img class="card-img-top" alt="<?php echo esc_attr(agd_boat_title($b)); ?>"
                     src="<?php echo esc_url(!empty($b['Image']) ? str_replace('_XLARGE','_LARGE',$b['Image']) : get_template_directory_uri().'/images/boat-placeholder.jpg'); ?>">
            </a>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo esc_html(agd_boat_title($b)); ?></h5>
                <div class="text-muted mb-1">
                    <?php
                    $priceRaw = !empty($b['Price']) ? floatval(preg_replace('/[^\d.]/','',$b['Price'])) : 0;
                    echo $priceRaw ? '$'.number_format($priceRaw, 0) : 'Call';
                    ?>
                </div>
                <?php
                $loc = !empty($b['BoatLocation']) ? trim(
                    ($b['BoatLocation']['BoatCityName'] ?? '') .
                    (empty($b['BoatLocation']['BoatStateCode']) ? '' : ', '.$b['BoatLocation']['BoatStateCode'])
                ) : '';
                if ($loc): ?><div class="mb-3"><?php echo esc_html($loc); ?></div><?php endif; ?>
                <a class="btn btn-outline-secondary mt-auto" href="<?php echo esc_url(agd_boat_url($b)); ?>">View Details</a>
            </div>
        </div>
    </div>
<?php } ?>

<main class="container my-5 single-staff">
    <div class="row">
        <!-- Main content -->
        <div class="col-lg-3">
            <div class="staff-details">
                <div class="avatar mb-3">
                    <?php if (has_post_thumbnail()) the_post_thumbnail('large', ['class'=>'img-fluid rounded']); ?>
                </div>
                <h1 class="mb-1"><?php the_title(); ?></h1>
                <?php if (!empty($meta['job_title'])): ?>
                    <h2 class="h5 text-muted "><?php echo esc_html($meta['job_title']); ?></h2>
                <?php endif; ?>

                <!-- Phones -->
                <?php if (!empty($meta['mobile_phone']) || !empty($meta['office_phone'])): ?>
                    <div class="d-flex flex-column gap-1 info-block">
                        <?php if (!empty($meta['mobile_phone'])): ?>
                            <div>
                                <strong><i class="fa-solid fa-mobile-screen-button me-1"></i> M:</strong>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/','',$meta['mobile_phone'])); ?>">
                                    <?php echo esc_html($meta['mobile_phone']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['office_phone'])): ?>
                            <div>
                                <strong><i class="fa-regular fa-phone me-1"></i> O:</strong>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/','',$meta['office_phone'])); ?>">
                                    <?php echo esc_html($meta['office_phone']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($meta['email'])): ?>
                    <a href="mailto:<?php echo esc_attr($meta['email']); ?>" class="text-white button blue btn btn-primary w-100 mb-2">
                        Contact Me
                    </a>
                <?php endif; ?>

                <?php
                $socials = [
                        'facebook_url'  => 'Facebook',
                        'twitter_url'   => 'Twitter',
                        'instagram_url' => 'Instagram',
                        'linkedin_url'  => 'LinkedIn',
                        'youtube_url'   => 'YouTube',
                ];
                $has_soc = array_filter(array_intersect_key($meta, $socials));
                if ($has_soc): ?>
                    <ul class="list-inline mt-3">
                        <?php foreach ($socials as $key => $label):
                            if (empty($meta[$key])) continue; ?>
                            <li class="list-inline-item mb-2">
                                <a class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener" href="<?php echo esc_url($meta[$key]); ?>">
                                    <?php echo esc_html($label); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        </div>

        <div class="col-lg-9">
            <div class="biography mb-5">
                <h4 class="h5">Meet <?php the_title(); ?></h4>
                <div class="entry-content"><?php the_content(); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
        <?php if ($party_id): ?>
            <section class="single-staff-listings">
                <ul class="nav nav-tabs" id="staffBoatsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-active"
                                data-bs-toggle="tab" data-bs-target="#pane-active"
                                type="button" role="tab" aria-controls="pane-active" aria-selected="true">
                            Current Listings (<?php echo count($active_boats); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-sold"
                                data-bs-toggle="tab" data-bs-target="#pane-sold"
                                type="button" role="tab" aria-controls="pane-sold" aria-selected="false">
                            Sold Boats (<?php echo count($sold_boats); ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="staffBoatsContent">
                    <div class="tab-pane fade show active pt-3" id="pane-active" role="tabpanel" aria-labelledby="tab-active">
                        <?php if ($active_boats): ?>
                            <div class="row"><?php foreach ($active_boats as $b) agd_boat_card($b, false); ?></div>
                        <?php else: ?>
                            <p class="text-muted">No current listings.</p>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade pt-3" id="pane-sold" role="tabpanel" aria-labelledby="tab-sold">
                        <?php if ($sold_boats): ?>
                            <div class="row"><?php foreach ($sold_boats as $b) agd_boat_card($b, true); ?></div>
                        <?php else: ?>
                            <p class="text-muted">No sold boats.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Fallback tabs if Bootstrap JS is missing -->
            <script>
                (function(){
                    if (window.bootstrap && window.bootstrap.Tab) return;
                    document.addEventListener('click', function(e){
                        var btn = e.target.closest('[data-bs-toggle="tab"]');
                        if (!btn) return;
                        e.preventDefault();
                        document.querySelectorAll('#staffBoatsTabs .nav-link').forEach(function(b){ b.classList.remove('active'); });
                        btn.classList.add('active');
                        var target = btn.getAttribute('data-bs-target');
                        document.querySelectorAll('#staffBoatsContent .tab-pane').forEach(function(p){ p.classList.remove('show','active'); });
                        var pane = document.querySelector(target);
                        if (pane) { pane.classList.add('show','active'); }
                    });
                })();
            </script>
        <?php else: ?>
            <p class="text-muted">No Boats.com Party ID has been linked to this staff profile yet.</p>
        <?php endif; ?>
        </div>
    </div>
</main>

<style>
    /* Minimal styling that won’t fight your theme */
    .single-staff .card-img-top{height:240px;object-fit:cover}
    .single-staff .info-block a{text-decoration:none}
    .single-staff .nav-tabs .nav-link{padding:.5rem 1rem}
    .single-staff .badge{pointer-events:none}
</style>

<?php get_footer(); ?>
