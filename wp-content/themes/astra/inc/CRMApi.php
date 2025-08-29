<?php
class CRMApi
{
// In your class…
    public function email_broker_post_complete() {
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ) {
            wp_send_json_error(['message' => 'Invalid context'], 400);
        }

        // Optional but recommended: nonce check (add a nonce field on the form)
        if ( isset($_POST['nonce']) && ! wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['nonce']) ), 'email_broker' ) ) {
            wp_send_json_error(['message' => 'Bad nonce'], 403);
        }

        $get = function ($key, $default = '') {
            return isset($_POST[$key]) ? sanitize_text_field( wp_unslash($_POST[$key]) ) : $default;
        };

        $lead = [
            'notes'     => $get('notes'),
            'firstname' => $get('firstname'),
            'lastname'  => $get('lastname'),
            'email'     => $get('email'),
            'phone'     => $get('phone'),
            'location'  => $get('location'),
            'year'      => $get('year'),
            'make'      => $get('make'),
            'model'     => $get('model'),
            'hin'       => $get('hin'),
            'source'    => $get('source'),
            'referrer'  => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : home_url('/'),
        ];

        // trade-in (array)
        $tradein = isset($_POST['tradein']) && is_array($_POST['tradein']) ? wp_unslash($_POST['tradein']) : [];
        if ( ! empty($tradein) ) {
            $ti_year  = isset($tradein['year'])  ? sanitize_text_field($tradein['year'])  : '';
            $ti_make  = isset($tradein['make'])  ? sanitize_text_field($tradein['make'])  : '';
            $ti_model = isset($tradein['model']) ? sanitize_text_field($tradein['model']) : '';

            if ($ti_year !== '')  { $lead['tradein_year']  = $ti_year; }
            if ($ti_model !== '') { $lead['tradein_model'] = $ti_model; }
            if ($ti_make  !== '') {
                // Your original code split by "-"; keep that behavior if needed
                $pieces = explode('-', $ti_make);
                $lead['tradein_make'] = trim( end($pieces) );
            }
        }

        // Build payload for CRM
        $payload = $lead;
        $payload['username'] = 'oth';
        $payload['password'] = 'f09eb636703e5c248ecc';

        $url = 'https://crm.offthehookyachts.com/api/post-broker-web-lead';
        if ( strpos( $_SERVER['SERVER_NAME'] ?? '', 'development' ) !== false ) {
            $url = 'https://crm.oth.craftwebshop.com/api/post-broker-web-lead';
        }

        $resp = wp_remote_post( $url, [
            'timeout' => 15,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body'      => wp_json_encode( $payload ),
            'sslverify' => true,
        ]);

        if ( is_wp_error($resp) ) {
            wp_send_json_error([
                'message' => 'CRM error: ' . $resp->get_error_message(),
            ], 502);
        }

        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);

        if ($code < 200 || $code >= 300) {
            wp_send_json_error([
                'message' => 'CRM HTTP ' . $code,
                'body'    => $body,
            ], 502);
        }

        // If CRM returns JSON, you can inspect it:
        $crm = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Not JSON? still treat as OK if 2xx
            $crm = ['raw' => $body];
        }

        // ✅ Return JSON; let the browser redirect after
        $redirect = add_query_arg('sent', '1', $lead['referrer'] ?: home_url('/'));
        wp_send_json_success([
            'ok'       => true,
            'crm'      => $crm,
            'redirect' => esc_url_raw($redirect),
        ]);
    }

}