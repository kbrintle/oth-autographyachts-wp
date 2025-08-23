<?php
//return;

/**
 * Plugin Name: Boats Inventory Cache
 * Description: Caches Boats Group inventory feed hourly, serves a filterable inventory and a detail page at /inventory and /inventory/{slug}.
 * Version: 1.2.0
 * Author:
 */

if (!defined('ABSPATH')) exit;

/** =========================
 * Constants
 * ========================= */
if (!defined('BOATS_INV_PLUGIN_PATH')) define('BOATS_INV_PLUGIN_PATH', plugin_dir_path(__FILE__));
if (!defined('BOATS_INV_PLUGIN_URL'))  define('BOATS_INV_PLUGIN_URL',  plugin_dir_url(__FILE__));

/** =========================
 * Rewrites + Query Vars
 * ========================= */
add_action('init', function () {
    add_rewrite_rule('^yachts-for-sale/([^/]+)/?$', 'index.php?inventory_boat=$matches[1]', 'top');
    add_rewrite_rule('^yachts-for-sale/?$', 'index.php?inventory_page=1', 'top');

    add_rewrite_tag('%inventory_boat%', '([^&]+)');
    add_rewrite_tag('%inventory_page%', '([^&]+)');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'inventory_page';
    $vars[] = 'inventory_boat';
    return $vars;
});

register_activation_hook(__FILE__, function () { flush_rewrite_rules(); });
register_deactivation_hook(__FILE__, function () { flush_rewrite_rules(); });

/** =========================
 * Template Loader
 * ========================= */
add_filter('template_include', function ($template) {
    if (get_query_var('inventory_page')) {
        $custom = BOATS_INV_PLUGIN_PATH . 'templates/inventory.php';
        return file_exists($custom) ? $custom : $template;
    }
    if (get_query_var('inventory_boat')) {
        $custom = BOATS_INV_PLUGIN_PATH . 'templates/inventory-detail.php';
        return file_exists($custom) ? $custom : $template;
    }
    return $template;
});

add_filter('astra_page_layout', function ($layout) {
    if (get_query_var('inventory_boat')) return 'no-sidebar';
    return $layout;
});

// Add a body class we can target if needed
add_filter('body_class', function ($classes) {
    if (get_query_var('inventory_boat')) $classes[] = 'boat-detail-page';
    return $classes;
});

/** =========================
 * Frontend Assets
 * ========================= */

add_action('wp_footer', function () {
    if (!current_user_can('manage_options')) return;
    echo '<script>console.log("inv_boat:", ' . json_encode(get_query_var('inventory_boat')) . ');</script>';
});
add_action('wp_enqueue_scripts', function () {
    $is_list   = (bool) get_query_var('inventory_page');
    $is_detail = (bool) get_query_var('inventory_boat');
    if (!$is_list && !$is_detail) return;

    /* ---------- CSS (shared) ---------- */
    $css_rel = 'assets/css/inventory.css';
    $css_abs = BOATS_INV_PLUGIN_PATH . $css_rel;
    if (file_exists($css_abs)) {
        wp_enqueue_style('boats-inventory', BOATS_INV_PLUGIN_URL . $css_rel, [], filemtime($css_abs));
    }

    // Optional: detail-only CSS
    $detail_css_rel = 'assets/css/boat.css';
    $detail_css_abs = BOATS_INV_PLUGIN_PATH . $detail_css_rel;
    if ($is_detail && file_exists($detail_css_abs)) {
        wp_enqueue_style('boats-boat', BOATS_INV_PLUGIN_URL . $detail_css_rel, [], filemtime($detail_css_abs));
    }

    /* ---------- Shared config for both pages ---------- */
    $config = [
            'baseUrl'    => esc_url_raw(home_url('/inventory')),
            'restUrl'    => esc_url_raw(get_rest_url(null, '/boats/v1/inventory')),
            'filtersUrl' => esc_url_raw(get_rest_url(null, '/boats/v1/filters')),
            'facetsUrl'  => esc_url_raw(get_rest_url(null, '/boats/v1/facets')),
            'nonce'      => wp_create_nonce('wp_rest'),
    ];

    /* ---------- JS: LISTING (/inventory) ---------- */
    if ($is_list) {
        $rel  = 'assets/js/inventory.js';
        $abs  = BOATS_INV_PLUGIN_PATH . $rel;
        if (file_exists($abs)) {
            $handle = 'boats-inventory-js';
            wp_register_script($handle, BOATS_INV_PLUGIN_URL . $rel, ['jquery'], filemtime($abs), true);
            wp_script_add_data($handle, 'defer', true);

            // Page context (optional but handy)
            wp_localize_script($handle, 'BoatsConfig', $config);
            wp_localize_script($handle, 'BoatsPage', ['type' => 'index']);

            wp_enqueue_script($handle);
        }
    }

    /* ---------- JS: DETAIL (/inventory/{slug}) ---------- */
    if ($is_detail) {
        wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', [], '5.0');
        wp_enqueue_style('fancybox-carousel', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.css', [], '5.0');

        wp_register_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], '5.0', true);
        wp_register_script('fancybox-carousel', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.umd.js', [], '5.0', true);

        $rel  = 'assets/js/boat.js';
        $abs  = BOATS_INV_PLUGIN_PATH . $rel;
        if (file_exists($abs)) {
            $handle = 'boats-boat-js';
            wp_register_script($handle, BOATS_INV_PLUGIN_URL . $rel, ['jquery'], filemtime($abs), true);
            wp_script_add_data($handle, 'defer', true);

            $boat_slug = sanitize_title(get_query_var('inventory_boat'));

            wp_localize_script($handle, 'BoatsConfig', $config);
            wp_localize_script($handle, 'BoatsPage', [
                    'type'     => 'detail',
                    'boatSlug' => $boat_slug,
            ]);

            wp_enqueue_script($handle);
        }
    }
}, 20);


/** =========================
 * Scheduler (hourly sync)
 * ========================= */
add_action('init', function () {
    if (!wp_next_scheduled('boats_inventory_cache_sync')) {
        wp_schedule_event(time(), 'hourly', 'boats_inventory_cache_sync');
    }
});
add_action('boats_inventory_cache_sync', 'cache_boat_inventory');
add_action('admin_post_boats_manual_sync', 'cache_boat_inventory');

/** =========================
 * REST Routes
 * ========================= */
add_action('rest_api_init', function () {
    // List endpoint (with filters)
    register_rest_route('boats/v1', '/inventory', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => 'boats_rest_list',
    ]);

    // Single boat by slug
    register_rest_route('boats/v1', '/inventory/(?P<slug>[a-z0-9\-]+)', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function ($req) {
            $slug = sanitize_title($req['slug']);
            $boat = boats_get_by_slug($slug);
            if (!$boat) return new WP_Error('not_found', 'Boat not found', ['status' => 404]);
            return $boat;
        },
    ]);

    // Filter metadata
    register_rest_route('boats/v1', '/filters', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => 'get_inventory_filter_metadata',
    ]);
});

/** =========================
 * Cache Sync
 * ========================= */
function cache_boat_inventory() {
    $key = "T7lQ9dZmElyotsARW4hmv8fqnLoVY2";
    $url = "https://api.boats.com/inventory/search?key={$key}&rows=500&status=Active";

    $response = @file_get_contents($url);
    if ($response === false) { error_log("[Boats Inventory] fetch failed"); return; }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) { error_log("[Boats Inventory] JSON error: " . json_last_error_msg()); return; }

    $boats = $data['results'] ?? [];

    $trimmed = array_map(function ($boat) {
        $images = [];
        if (!empty($boat['Images']) && is_array($boat['Images'])) {
            foreach ($boat['Images'] as $img) {
                if (!empty($img['Uri'])) $images[] = $img['Uri'];
            }
        }

        $base_slug = trim(
            ($boat['MakeStringExact'] ?? $boat['MakeString'] ?? '') . '-' .
            ($boat['Model'] ?? '') . '-' .
            ($boat['DocumentID'] ?? ''), '-'
        );
        $slug = sanitize_title($base_slug);

        return [
            'DocumentID'      => $boat['DocumentID'] ?? null,
            'slug'            => $slug,
            'ModelYear'       => $boat['ModelYear'] ?? null,
            'MakeString'      => $boat['MakeString'] ?? null,
            'MakeStringExact' => $boat['MakeStringExact'] ?? null,
            'Model'           => $boat['Model'] ?? null,
            'BoatName'        => $boat['BoatName'] ?? null,
            'Image'           => $images[0] ?? null,
            'Images'          => $images,
            'BoatCategoryCode'=> $boat['BoatCategoryCode'] ?? null,
            'SaleClassCode'   => $boat['SaleClassCode'] ?? null,
            'BoatClassCode'   => $boat['BoatClassCode'] ?? null,
            'LengthOverall'   => $boat['LengthOverall'] ?? null,
            'NominalLength'   => $boat['NominalLength'] ?? null,
            'DryWeightMeasure'=> $boat['DryWeightMeasure'] ?? null,
            'BoatHullMaterialCode' => $boat['BoatHullMaterialCode'] ?? null,
            'BoatHullID'      => $boat['BoatHullID'] ?? null,
            'BeamMeasure'     => $boat['BeamMeasure'] ?? null,
            'Price'           => $boat['NormPrice'] ?? null,
            'Engines'         => $boat['Engines'] ?? null,
            'BoatLocation'    => [
                'BoatStateCode' => $boat['BoatLocation']['BoatStateCode'] ?? null,
                'BoatCityName'  => $boat['BoatLocation']['BoatCityName'] ?? null,
            ],
            'Description'     => $boat['Description'] ?? null,
        ];
    }, $boats);

    // Build slug map for O(1) detail lookup
    $by_slug = [];
    foreach ($trimmed as $b) {
        if (!empty($b['slug'])) $by_slug[$b['slug']] = $b;
    }

    set_transient('boats_inventory_cache', json_encode(['results' => $trimmed]), 3600);
    set_transient('boats_inventory_by_slug', $by_slug, 3600);

    error_log("[Boats Inventory] Cached " . count($trimmed) . " boats.");
}

/** =========================
 * REST Handlers
 * ========================= */
function boats_rest_list(\WP_REST_Request $req) {
    $cached = get_transient('boats_inventory_cache');
    if (!$cached) {
        cache_boat_inventory();
        $cached = get_transient('boats_inventory_cache');
        if (!$cached) return new WP_Error('no_data', 'No inventory cached', ['status' => 404]);
    }

    $data = is_array($cached) ? $cached : json_decode($cached, true);
    if (!is_array($data)) return new WP_Error('invalid_data', 'Cached data could not be parsed', ['status' => 500]);

    $boats = $data['results'] ?? [];

    // Filters
    $makes       = array_filter(array_map('trim', explode(',', sanitize_text_field($req->get_param('make') ?? ''))));
    $conditions  = array_filter(array_map('strtolower', array_map('trim', explode(',', sanitize_text_field($req->get_param('condition') ?? '')))));
    $classParam  = sanitize_text_field($req->get_param('boat_type') ?? $req->get_param('type') ?? $req->get_param('boat-type') ?? '');
    $typesFilter = array_filter(array_map('trim', explode(',', $classParam)));
    $fuel        = sanitize_text_field($req->get_param('fuel') ?? '');
    $state       = sanitize_text_field($req->get_param('state') ?? '');

    // Ranges
    $year   = explode(':', sanitize_text_field($req->get_param('year') ?? ''));
    $price  = explode(':', explode('|', sanitize_text_field($req->get_param('price') ?? ''))[0]);
    $length = explode(':', explode('|', sanitize_text_field($req->get_param('length') ?? ''))[0]);

    $filtered = array_filter($boats, function ($boat) use ($makes, $conditions, $typesFilter, $fuel, $state, $year, $price, $length) {
        // Make
        if ($makes) {
            $boatMake = $boat['MakeStringExact'] ?? $boat['MakeString'] ?? '';
            $ok = false; foreach ($makes as $m) { if ($boatMake && stripos($boatMake, $m) !== false) { $ok = true; break; } }
            if (!$ok) return false;
        }

        // Condition
        if ($conditions) {
            $boatCond = strtolower($boat['SaleClassCode'] ?? '');
            if (!in_array($boatCond, $conditions, true)) return false;
        }

        // Types
        if ($typesFilter) {
            $boatTypes = $boat['BoatClassCode'] ?? [];
            if (!is_array($boatTypes)) $boatTypes = [$boatTypes];
            $btNorm = array_map('strtolower', array_map('trim', $boatTypes));
            $tNorm  = array_map('strtolower', array_map('trim', $typesFilter));
            $match  = false; foreach ($tNorm as $t) { if (in_array($t, $btNorm, true)) { $match = true; break; } }
            if (!$match) return false;
        }

        // Fuel
        if ($fuel) {
            $fuelStr = strtolower($boat['Engines'][0]['Fuel'] ?? '');
            if (stripos($fuelStr, strtolower($fuel)) === false) return false;
        }

        // State
        if ($state) {
            $stateStr = $boat['BoatLocation']['BoatStateCode'] ?? '';
            if (stripos($stateStr, $state) === false) return false;
        }

        // Year
        $boatYear = intval($boat['ModelYear'] ?? 0);
        $minYear = intval($year[0] ?? 0);
        $maxYear = intval($year[1] ?? 9999);
        if ($boatYear && ($boatYear < $minYear || $boatYear > $maxYear)) return false;

        // Price
        $boatPrice = floatval(preg_replace('/[^\d.]/', '', $boat['Price'] ?? 0));
        $minPrice = floatval($price[0] ?? 0);
        $maxPrice = floatval($price[1] ?? 99999999);
        if ($boatPrice && ($boatPrice < $minPrice || $boatPrice > $maxPrice)) return false;

        // Length
        $boatLength = floatval(preg_replace('/[^\d.]/', '', $boat['LengthOverall'] ?? 0));
        $minLength = floatval($length[0] ?? 0);
        $maxLength = floatval($length[1] ?? 9999);
        if ($boatLength && ($boatLength < $minLength || $boatLength > $maxLength)) return false;

        return true;
    });

    // Sort by length desc
    usort($filtered, function ($a, $b) {
        $aLen = floatval(preg_replace('/[^\d.]/', '', $a['LengthOverall'] ?? 0));
        $bLen = floatval(preg_replace('/[^\d.]/', '', $b['LengthOverall'] ?? 0));
        return $bLen <=> $aLen;
    });

    return array_values($filtered);
}

/** =========================
 * Filters Metadata
 * ========================= */
function get_inventory_filter_metadata() {
    $cached = get_transient('boats_inventory_cache');
    if (!$cached) {
        cache_boat_inventory();
        $cached = get_transient('boats_inventory_cache');
    }

    $data = is_array($cached) ? $cached : json_decode($cached, true);
    if (!is_array($data)) return new WP_Error('invalid_data', 'Cached data could not be parsed', ['status' => 500]);

    $boats = $data['results'] ?? [];
    $makes = [];
    $types = [];

    foreach ($boats as $boat) {
        $make = $boat['MakeStringExact'] ?? '';
        if ($make !== '') $makes[] = $make;

        $boatTypes = $boat['BoatClassCode'] ?? [];
        if (!is_array($boatTypes)) $boatTypes = [$boatTypes];
        foreach ($boatTypes as $bt) {
            if ($bt !== '') $types[] = $bt;
        }
    }

    return [
        'makes' => array_values(array_unique($makes)),
        'types' => array_values(array_unique($types)),
    ];
}

/** =========================
 * Helper: get boat by slug
 * ========================= */
function boats_get_by_slug($slug) {
    $slug = sanitize_title($slug);

    $map = get_transient('boats_inventory_by_slug');
    if (is_array($map) && isset($map[$slug])) return $map[$slug];

    // Fallback: rebuild from main cache
    $cached = get_transient('boats_inventory_cache');
    if (!$cached) {
        cache_boat_inventory();
        $cached = get_transient('boats_inventory_cache');
    }
    $data  = is_array($cached) ? $cached : json_decode($cached, true);
    $boats = is_array($data) ? ($data['results'] ?? []) : [];

    $by_slug = [];
    foreach ($boats as $b) { if (!empty($b['slug'])) $by_slug[$b['slug']] = $b; }
    if ($by_slug) set_transient('boats_inventory_by_slug', $by_slug, 3600);

    return $by_slug[$slug] ?? null;
}

/* --- ADMIN UI --- //*/

add_action('admin_menu', function () {
    add_menu_page(
            'Boat Inventory Sync',
            'Boat Inventory Sync',
            'manage_options',
            'boat-inventory-sync',
            'render_inventory_sync_page',
            'dashicons-update',
            100
    );
});

function render_inventory_sync_page() {
    if (isset($_POST['manual_sync'])) {
        cache_boat_inventory();
        echo '<div class="updated"><p>Inventory manually synced!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Boats Group Inventory API Sync</h1>
        <form method="post">
            <p><input type="submit" name="manual_sync" class="button button-primary" value="Sync Now"></p>
        </form>

        <h2>Search Filters</h2>
        <form id="inventory-filters" onsubmit="fetchFilteredInventory(); return false;">
            <table class="form-table">
                <tr><th><label for="exactMake">Make</label></th><td><input type="text" id="exactMake" class="regular-text"></td></tr>
                <tr><th><label for="state">State</label></th><td><input type="text" id="state" class="regular-text"></td></tr>
                <tr><th><label for="condition">Condition</label></th>
                    <td>
                        <select id="condition">
                            <option value="">Any</option>
                            <option value="Used">Used</option>
                            <option value="New">New</option>
                        </select>
                    </td>
                </tr>
                <tr><th><label for="class">Class</label></th><td><input type="text" id="class" class="regular-text"></td></tr>
                <tr><th><label for="fuel">Fuel Type</label></th><td><input type="text" id="fuel" class="regular-text"></td></tr>
                <tr><th><label for="year">Year Range</label></th>
                    <td><input type="text" id="year_min" placeholder="Min" size="6"> - <input type="text" id="year_max" placeholder="Max" size="6"></td>
                </tr>
                <tr><th><label for="price">Price Range</label></th>
                    <td><input type="text" id="price_min" placeholder="Min" size="6"> - <input type="text" id="price_max" placeholder="Max" size="6"></td>
                </tr>
                <tr><th><label for="length">Length Range</label></th>
                    <td><input type="text" id="length_min" placeholder="Min" size="6"> - <input type="text" id="length_max" placeholder="Max" size="6"></td>
                </tr>
            </table>
            <p><button type="submit" class="button button-secondary">Fetch</button></p>
        </form>

        <div id="inventory-results"><p><strong>Results will appear here...</strong></p></div>
    </div>

    <script>
        function fetchFilteredInventory() {
            const params = new URLSearchParams();

            ['exactMake', 'state', 'condition', 'class', 'fuel'].forEach(id => {
                const val = document.getElementById(id).value;
                if (val) params.append(id, val);
            });

            const yrMin = document.getElementById('year_min').value;
            const yrMax = document.getElementById('year_max').value;
            if (yrMin || yrMax) params.append('year', `${yrMin || 0}:${yrMax || 9999}`);

            const prMin = document.getElementById('price_min').value;
            const prMax = document.getElementById('price_max').value;
            if (prMin || prMax) params.append('price', `${prMin || 0}:${prMax || 99999999}`);

            const lnMin = document.getElementById('length_min').value;
            const lnMax = document.getElementById('length_max').value;
            if (lnMin || lnMax) params.append('length', `${lnMin || 0}:${lnMax || 9999}`);

            const url = `/wp-json/boats/v1/inventory?${params.toString()}`;
            const resultsDiv = document.getElementById('inventory-results');
            resultsDiv.innerHTML = '<p>Loading...</p>';

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        resultsDiv.innerHTML = '<p>No results found.</p>';
                        return;
                    }

                    let html = '<table class="widefat striped"><thead><tr><th>Year</th><th>Make</th><th>Model</th><th>Price</th><th>Link</th></tr></thead><tbody>';

                    html += data.map(boat => {
                        const year = boat.ModelYear || 'N/A';
                        const make = boat.MakeStringExact || 'N/A';
                        const model = boat.ModelExact || 'N/A';
                        const price = boat.Price ? boat.Price.replace(' USD', '') : 'N/A';
                        const id = boat.DocumentID || '';

                        return `<tr>
                            <td>${year}</td>
                            <td>${make}</td>
                            <td>${model}</td>
                            <td>$${price}</td>
                            <td><a class="button button-primary" target="_blank" href=/yachts-for-sale/$${id}>Link</a></td>
                        </tr>`;
                    }).join('');

                    html += '</tbody></table>';
                    resultsDiv.innerHTML = html;
                })
                .catch(() => {
                    resultsDiv.innerHTML = '<p>Error fetching data.</p>';
                });
        }
    </script>
    <?php
}
