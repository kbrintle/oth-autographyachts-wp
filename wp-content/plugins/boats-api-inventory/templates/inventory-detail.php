<?php
/* Inventory Detail Page */
get_header();

/* ---------------------------------------------------------------------------
   Data bootstrap
--------------------------------------------------------------------------- */
$slug = sanitize_title( get_query_var('inventory_boat') );
$boat = $slug ? boats_get_by_slug($slug) : null;

//echo "<pre>";
//print_r($boat); die;
if (!$boat): ?>
    <main class="container py-5">
        <h1 class="mb-3">Boat Not Found</h1>
        <p>
            <a class="btn btn-primary" href="<?php echo esc_url( home_url('/inventory') ); ?>">
                &larr; Back to Inventory
            </a>
        </p>
    </main>
    <?php get_footer(); return; ?>
<?php
endif;

/* Derived values */
$year         = $boat['ModelYear'] ?? '';
$make         = $boat['MakeStringExact'] ?? ($boat['MakeString'] ?? '');
$model        = $boat['ModelExact'] ?? ($boat['Model'] ?? '');
$listingTitle = $listingTitle ?? trim(implode(' ', array_filter([$year, $make, $model])));
$hin          = $boat['BoatHullID'] ?? '';
$city     = $boat['BoatLocation']['BoatCityName'] ?? ($boat['BoatCityNameNoCaseAlnumOnly'] ?? '');
$state    = $boat['BoatLocation']['BoatStateCode'] ?? '';
$location = $location ?? trim($city . ($state ? ", $state" : ''));

$images    = is_array($boat['Images'] ?? null) ? $boat['Images'] : [];
$normPrice = (isset($boat['Price']) && is_numeric($boat['Price'])) ? (float) $boat['Price'] : 0.0;

$ogImage     = !empty($images[0]['Uri']) ? $images[0]['Uri'] : '';
$description = '';
if (!empty($boat['GeneralBoatDescription'][0])) {
    $description = wp_strip_all_tags($boat['GeneralBoatDescription'][0]);
    if (mb_strlen($description) > 157) $description = mb_substr($description, 0, 157) . '...';
}

/* Head/meta */
add_action('wp_head', function () use ($listingTitle, $location, $description, $ogImage) {
    $fullListingTitle = trim($listingTitle . ' | ' . $location); ?>
    <title><?php echo esc_html($fullListingTitle); ?></title>
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="<?php echo esc_attr($listingTitle); ?>">
    <meta property="og:type" content="product">
    <?php if ($ogImage): ?>
        <meta property="og:image" content="<?php echo esc_url($ogImage); ?>">
    <?php endif; ?>
    <meta name="robots" content="noindex">
<?php }, 1);
?>

<style>
    /* Gallery & layout */
    #mainCarousel .f-carousel__slide { max-height: 573px; overflow: hidden; }
    body .main > .container { padding-top: 40px; }
    @media (min-width:1200px) { .container { max-width: 1360px !important; } }
    @media (max-width:767.98px) {
        body .main > .container { padding-top: 0; }
        #mainCarousel .f-carousel__slide { max-height: 250px !important; }
    }
    .product-slider-wrap { box-shadow: none !important; }
    .product-detail .vessel-meta .title,
    .product-detail .vessel-meta .location,
    .product-detail .vessel-meta .price { margin: 0 !important; }
    .product-detail .vessel-meta { padding: 0 !important; }
    #mainCarousel { margin-top: 20px !important; }
    #mainCarousel .f-carousel__slide img { display: block; width: 100%; height: auto; object-fit: contain; object-position: center; }
    #mainCarousel .f-carousel__slide { height: auto !important; }
    #mainCarousel, #thumbCarousel { width: 100%; max-width: 100%; }
    .f-carousel__viewport { overflow: hidden; }
    .f-carousel__track { display: flex !important; flex-direction: row !important; align-items: flex-start !important; transition: transform .3s ease; }
    .f-carousel__slide { flex: 0 0 auto !important; width: 100% !important; }
    #thumbCarousel .f-carousel__track { gap: 10px; }
    #thumbCarousel .f-carousel__slide { width: 120px !important; flex: 0 0 120px !important; }
    #thumbCarousel .f-carousel__slide img { width: 100%; height: 80px; object-fit: cover; cursor: pointer; border-radius: 4px; transition: opacity .2s ease; }
    #thumbCarousel .f-carousel__slide img:hover { opacity: .8; }
    #thumbCarousel .f-carousel__slide.is-active img { border: 2px solid rgba(53,149,209,1); opacity: 1; }
    #thumbCarousel .f-carousel__slide:not(.is-active) img { opacity: .7; }

    /* Lightweight lightbox shell (if used later) */
    .boat-lightbox { position: fixed; inset: 0; background: rgba(0,0,0,.95); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn .3s ease; }
    @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
    .lightbox-container { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .lightbox-track { max-width: 90vw; max-height: 90vh; }
    .lightbox-slide { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
    .lightbox-slide img { max-width: 90vw; max-height: 90vh; object-fit: contain; user-select: none; }
    .lightbox-close { position: absolute; top: 20px; right: 30px; background: none; border: none; color: #fff; font-size: 40px; cursor: pointer; z-index: 10001; opacity: .8; transition: opacity .2s; line-height: 1; padding: 0; width: 40px; height: 40px; }
    .lightbox-close:hover { opacity: 1; }
    .lightbox-prev, .lightbox-next {
        position: absolute; top: 50%; transform: translateY(-50%);
        background: rgba(255,255,255,.1); border: 2px solid rgba(255,255,255,.3);
        color: #fff; font-size: 30px; width: 50px; height: 50px; border-radius: 50%;
        cursor: pointer; z-index: 10001; opacity: .7; transition: all .2s; display: flex; align-items: center; justify-content: center; line-height: 1;
    }
    .lightbox-prev:hover, .lightbox-next:hover { opacity: 1; background: rgba(255,255,255,.2); }
    .lightbox-prev { left: 20px; } .lightbox-next { right: 20px; }
    .lightbox-counter { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: #fff; font-size: 16px; background: rgba(0,0,0,.5); padding: 8px 16px; border-radius: 20px; z-index: 10001; }
    @media (max-width:768px) {
        .lightbox-prev, .lightbox-next { width: 40px; height: 40px; font-size: 24px; }
        .lightbox-close { font-size: 30px; top: 10px; right: 15px; }
    }
</style>

<div class="main">
    <div class="container" id="boat-detail" style="margin-top:20px;">
        <div class="product-detail">
            <!-- Top: gallery + sidebar -->
            <div class="row">
                <!-- Gallery -->
                <div class="col-lg-8 col-sm-12 col-xs-12">
                    <div class="product-slider-wrap">
                        <?php if (!empty($boat['SalesStatus']) && $boat['SalesStatus'] === 'Sold'): ?>
                            <div class="custom_label_div"><div class="sold"><div>Sold</div></div></div>
                        <?php endif; ?>

                        <div class="vessel-meta clearfix">
                            <h1 class="title"><?php echo esc_html($listingTitle); ?></h1>
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mt-2">
                                <?php if ($normPrice > 0): ?>
                                    <div class="price-wrap"><h5 class="price mb-0">$<?php echo number_format($normPrice, 2, '.', ','); ?></h5></div>
                                <?php endif; ?>
                                <?php if ($location): ?>
                                    <div class="d-flex align-items-center gap-2 ms-md-auto text-md-end">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10.224" height="13.356" viewBox="0 0 10.224 13.356" aria-hidden="true">
                                            <g transform="translate(9.548 -4.054)">
                                                <path d="M-4.437,4.054A5.115,5.115,0,0,0-9.548,9.165a5.754,5.754,0,0,0,.779,2.508,21.5,21.5,0,0,0,1.644,2.622C-5.936,15.949-4.75,17.27-4.75,17.27a.417.417,0,0,0,.59.033l.033-.033s1.186-1.322,2.375-2.976A21.35,21.35,0,0,0-.1,11.673,5.775,5.775,0,0,0,.676,9.165,5.118,5.118,0,0,0-4.437,4.054Zm0,2.2a2.105,2.105,0,0,1,2.1,2.1,2.105,2.105,0,0,1-2.1,2.1,2.1,2.1,0,0,1-2.095-2.1A2.1,2.1,0,0,1-4.437,6.254Z" fill="#007ac0"/>
                                            </g>
                                        </svg>
                                        <h2 class="location h6 mb-0"><?php echo esc_html($location); ?></h2>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($images)): ?>
                            <div id="mainCarousel" class="f-carousel">
                                <div class="f-carousel__viewport">
                                    <div class="f-carousel__track">
                                        <?php foreach ($images as $i => $image):?>
                                            <div class="f-carousel__slide" data-src="<?php echo esc_url($image); ?>" data-fancybox="gallery">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr('Boat image ' . ($i + 1)); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div id="thumbCarousel" class="f-carousel">
                                <div class="f-carousel__viewport">
                                    <div class="f-carousel__track">
                                        <?php foreach ($images as $i => $image):?>

                                            <div class="f-carousel__slide" data-src="<?php echo esc_url($image); ?>">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr('Thumbnail ' . ($i + 1)); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-sm-12 col-xs-12 listing-meta mt-3 mt-lg-0">
                    <div class="inner-table">
                        <ul>
                            <li><span>Year</span><strong><?php echo esc_html($year ?? ''); ?></strong></li>
                            <li><span>Make</span><strong><?php echo esc_html($make ?? ''); ?></strong></li>
                            <?php if (!empty($boat['hin'])): ?>
                                <li><span>HIN</span><strong><?php echo esc_html($hin) ?></strong></li>
                            <?php endif; ?>
                            <li><span>Model</span><strong><?php echo esc_html($model ?? ''); ?></strong></li>
                            <li><span>Length</span><strong><?php echo esc_html($boat['NominalLength'] ?? ''); ?></strong></li>
                            <li><span>Beam</span><strong><?php echo esc_html($boat['BeamMeasure'] ?? ''); ?></strong></li>
                            <?php if (!empty($boat['MaxDraft'])): ?>
                                <li><span>Draft</span><strong><?php echo esc_html($boat['MaxDraft']); ?></strong></li>
                            <?php endif; ?>
                            <li><span>Location</span><strong><?php echo esc_html($boat['BoatCityNameNoCaseAlnumOnly'] ?? $city); ?></strong></li>
                        </ul>
                    </div>

                    <?php if (!empty($staff_member)): ?>
                        <div class="contact-box" style="margin-top:10px;">
                            <div class="row justify-content-sm-center">
                                <div class="col-md-5 avatar">
                                    <div class="text-center">
                                        <img class="rounded-circle" src="<?php echo esc_url($staff_member_meta['portrait'] ?? ''); ?>" alt="<?php echo esc_attr($staff_member->post_title ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-7 contact-info">
                                    <h6><strong><?php echo esc_html($staff_member->post_title ?? ''); ?></strong></h6>
                                    <?php if (!empty($staff_member_meta['office_phone'])): ?>
                                        <div>
                                            <span>Office</span>
                                            <a class="tel" href="tel:<?php echo esc_attr(preg_replace('/[^0-9]+/','', $staff_member_meta['office_phone'])); ?>">
                                                <span><?php echo esc_html($staff_member_meta['office_phone']); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($staff_member_meta['mobile_phone'])): ?>
                                        <div>
                                            <span>Mobile</span>
                                            <a class="tel" href="tel:<?php echo esc_attr(preg_replace('/[^0-9]+/','', $staff_member_meta['mobile_phone'])); ?>">
                                                <span><?php echo esc_html($staff_member_meta['mobile_phone']); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($staff_member->post_name)): ?>
                        <a class="button blue-solid-btn" href="/profile/<?php echo esc_attr($staff_member->post_name); ?>/">View Profile</a>
                    <?php endif; ?>

                    <a href="https://gateway.appone.net/onlineapp/Autograph%20Yacht%20Group" target="_blank" class="button blue">
                        <span>Apply For Financing</span>
                    </a>

                    <!-- Bootstrap 5 bundle (if theme doesn’t already include it) -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

                    <!-- Email Broker Modal trigger -->
                    <button id="emailBrokerBtn" class="button blue" style="width:100%; margin-top:6px;" data-bs-toggle="modal" data-bs-target="#emailBrokerModal">
                        <span id="emailBrokerBtnTxt">Email Broker</span>
                    </button>

                    <!-- Email Broker Modal -->
                    <div class="modal fade" id="emailBrokerModal" tabindex="-1" aria-labelledby="emailBrokerLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="long-offer" action="/wp-admin/admin-ajax.php/?action=email_broker_post_complete" method="post">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="emailBrokerLabel">Email Broker</h5>
                                        <button type="button" class="btn-close" style="background-color:#fff;" data-bs-dismiss="modal" aria-label="Close"><span>x</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form row">
                                            <div class="input-wrapper required firstname col-md-6 col-xs-12">
                                                <input type="text" name="firstname" class="textbox form-control" placeholder="First Name" value="">
                                            </div>
                                            <div class="input-wrapper required lastname col-md-6 col-xs-12" style="margin-top:0;">
                                                <input type="text" name="lastname" class="textbox form-control" placeholder="Last Name" value="">
                                            </div>
                                            <div class="input-wrapper required email col-md-6 col-xs-12" style="margin-top:10px;">
                                                <input type="text" name="email" class="textbox form-control" placeholder="example@email.com" value="">
                                            </div>
                                            <div class="input-wrapper required phone col-md-6 col-xs-12" style="margin-top:10px;">
                                                <input id="phone" type="text" name="phone" class="textbox form-control" placeholder="(000) 000-0000" value="">
                                            </div>
                                        </div>
                                        <div class="form row">
                                            <div class="input-wrapper required phone col-md-12 col-xs-12" style="margin-top:10px;">
                                                <textarea rows="5" name="notes" class="form-control" style="min-height:130px !important;" placeholder="I recently viewed your listing and I am interested in more details. Thank you."></textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="hin"   value="<?php echo esc_attr($boat['BoatHullID'] ?? ''); ?>">
                                        <input type="hidden" name="year"  value="<?php echo esc_attr($boat['ModelYear'] ?? ''); ?>">
                                        <input type="hidden" name="make"  value="<?php echo esc_attr($boat['MakeString'] ?? ''); ?>">
                                        <input type="hidden" name="model" value="<?php echo esc_attr($boat['Model'] ?? ''); ?>">
                                        <input type="hidden" name="url"   value="<?php echo esc_url( wp_get_referer() ?: home_url('/inventory') ); ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <input type="submit" class="button blue-solid-btn" style="padding:12px 20px;" value="Send Email">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /Sidebar -->
            </div><!-- /Top row -->

            <!-- Specs + description -->
            <div class="row">
                <div class="col-12">
                    <div class="product-description-wrap">
                        <div class="product-specifications">
                            <h3 class="title">Specifications</h3>
                            <div class="con specifications">
                                <div class="specifications-row">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl><dt>Manufacturer:</dt><dd><?php echo esc_html($boat['MakeString'] ?? ''); ?></dd></dl>
                                            <dl><dt>Model:</dt><dd><?php echo esc_html($boat['Model'] ?? ''); ?></dd></dl>
                                            <dl><dt>Year:</dt><dd><?php echo esc_html($boat['ModelYear'] ?? ''); ?></dd></dl>
                                            <dl><dt>Category:</dt><dd><?php echo esc_html($boat['BoatCategoryCode'] ?? ''); ?></dd></dl>
                                            <dl><dt>Condition:</dt><dd><?php echo esc_html($boat['SaleClassCode'] ?? ''); ?></dd></dl>
                                            <dl><dt>Location:</dt><dd><?php echo esc_html($location); ?></dd></dl>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if (!empty($boat['BoatName'])): ?>
                                                <dl><dt>Vessel Name:</dt><dd><?php echo esc_html($boat['BoatName']); ?></dd></dl>
                                            <?php endif; ?>
                                            <dl><dt>Boat Type:</dt><dd>
                                                    <?php
                                                    $classes = $boat['BoatClassCode'] ?? [];
                                                    if (is_array($classes)) {
                                                        foreach ($classes as $class) {
                                                            echo esc_html( $class === 'Mega Yachts' ? 'Super Yachts' : $class ) . '<br>';
                                                        }
                                                    }
                                                    ?>
                                                </dd></dl>
                                            <dl><dt>Hull Material:</dt><dd><?php echo esc_html($boat['BoatHullMaterialCode'] ?? ''); ?></dd></dl>
                                            <dl><dt>HIN:</dt><dd><?php echo esc_html($boat['BoatHullID'] ?? ''); ?></dd></dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3>Dimensions &amp; Weight</h3>
                            <div class="specifications-row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl><dt>Length:</dt><dd><?php echo esc_html($boat['LengthOverall'] ?? ''); ?></dd></dl>
                                        <dl><dt>LOA:</dt><dd><?php echo esc_html($boat['NominalLength'] ?? ''); ?></dd></dl>
                                        <dl><dt>Beam:</dt><dd><?php echo esc_html($boat['BeamMeasure'] ?? ''); ?></dd></dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl><dt>Dry Weight:</dt><dd><?php echo esc_html($boat['DryWeightMeasure'] ?? ''); ?></dd></dl>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($boat['Engines']) && is_array($boat['Engines'])): ?>
                                <?php foreach ($boat['Engines'] as $engine): ?>
                                    <h3>Propulsion</h3>
                                    <div class="specifications-row">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <dl><dt>Make:</dt><dd><?php echo esc_html($engine['Make'] ?? ''); ?></dd></dl>
                                                <dl><dt>Model:</dt><dd><?php echo esc_html($engine['Model'] ?? ''); ?></dd></dl>
                                                <dl><dt>Hours:</dt><dd><?php echo esc_html($engine['Hours'] ?? ''); ?></dd></dl>
                                            </div>
                                            <div class="col-md-6">
                                                <dl><dt>Engine Type:</dt><dd><?php echo esc_html($engine['Type'] ?? ''); ?></dd></dl>
                                                <dl><dt>Fuel Type:</dt><dd><?php echo esc_html($engine['Fuel'] ?? ''); ?></dd></dl>
                                                <dl><dt>Horsepower:</dt><dd><?php echo esc_html($engine['EnginePower'] ?? ''); ?></dd></dl>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($boat['AdditionalDetailDescription']) && is_array($boat['AdditionalDetailDescription'])):?>
                            <h3>Other Details</h3>
                            <div class="specifications-row">
                                <?php

                                    foreach ($boat['AdditionalDetailDescription'] as $desc) echo wp_kses_post($desc); ?>

                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($boat['GeneralBoatDescription'][0])):?>
                        <div class="product-description mb-30">
                            <h3 class="title">Description</h3>
                            <div class="con">
                                <?php

                                    echo wp_kses_post($boat['GeneralBoatDescription'][0]);

                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div><!-- /.product-description-wrap -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->

            <!-- Disclaimer -->
            <div class="disclaimer_div mb-45 d-block">
                <small>
                    Disclaimer: The Company offers the details of this vessel in good faith but cannot guarantee or warrant the
                    accuracy of this information nor warrant the condition of the vessel. A buyer should instruct his/her agents,
                    or his/her surveyors, to instigate such details as the buyer desires validated. This vessel is offered subject
                    to prior sale, price change, or withdrawal without notice.
                </small>
            </div>
        </div><!-- /.product-detail -->
    </div><!-- /.container -->
</div><!-- /.main -->

<!-- Email success modal -->
<div class="modal fade" id="emailBrokerSuccess" tabindex="-1" aria-labelledby="emailBrokerSuccessLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailBrokerLabel">Success</h5>
                <button type="button" class="btn-close" style="background-color:#fff;" data-bs-dismiss="modal" aria-label="Close"><span>x</span></button>
            </div>
            <div class="modal-body"><h4>Your email has been sent!</h4></div>
            <div class="modal-footer">
                <a href="/inventory" class="button blue" style="padding:10px 14px;">Back To Inventory</a>
                <button type="button" class="button blue-solid-btn" style="padding:10px 14px;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Make sure older templates don’t leave the wrapper faded
        var pd = document.querySelector('.product-detail');
        if (pd) pd.classList.remove('fade');

        // Show success modal when returning with #sent in URL
        if (location.hash === '#sent' && window.bootstrap) {
            var el = document.getElementById('emailBrokerSuccess');
            if (el) new bootstrap.Modal(el).show();
        }
    });
</script>

<?php get_footer(); ?>
