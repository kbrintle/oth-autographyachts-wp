<?php
// Inventory plugin: includes/staff-integration.php

if (!function_exists('ayg_get_staff_for_boat')) {
    /**
     * Return a Staff post matched to $boat (by email → party_id → fallback).
     */
    function ayg_get_staff_for_boat(array $boat) : ?WP_Post {
        // If Staff CPT isn’t present, bail gracefully
        if (!post_type_exists('staff')) return null;

        // Allow overrides of which fields to look at
        $emailFields = apply_filters('ayg_boat_staff_email_fields', [
            'SalesRepEmail','InternetLeadEmail','LeadEmail','BrokerEmail','email',
            'StaffContact.Email'
        ]);

        // 1) Try email fields
        $email = '';
        foreach ($emailFields as $k) {
            // support dot-path 'StaffContact.Email'
            $val = $boat;
            foreach (explode('.', $k) as $part) {
                if (is_array($val) && isset($val[$part])) { $val = $val[$part]; } else { $val = null; break; }
            }
            if ($val && is_email($val)) { $email = sanitize_email($val); break; }
        }
        if ($email) {
            $q = new WP_Query([
                'post_type'      => 'staff',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'no_found_rows'  => true,
                'meta_key'       => 'email',
                'meta_value'     => $email,
            ]);
            if ($q->have_posts()) { $p = $q->posts[0]; wp_reset_postdata(); return $p; }
            wp_reset_postdata();
        }

        // 2) Try Boats.com party id
        $partyId = '';
        foreach (['SalesRepPartyID','BoatsPartyID','PartyID'] as $k) {
            if (!empty($boat[$k])) { $partyId = sanitize_text_field($boat[$k]); break; }
        }
        if ($partyId) {
            $q = new WP_Query([
                'post_type'      => 'staff',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'no_found_rows'  => true,
                'meta_key'       => 'boats_party_id',
                'meta_value'     => $partyId,
            ]);
            if ($q->have_posts()) { $p = $q->posts[0]; wp_reset_postdata(); return $p; }
            wp_reset_postdata();
        }

        // 3) Fallback: first published staff
        $q = new WP_Query([
            'post_type'      => 'staff',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
            'orderby'        => 'date',
            'order'          => 'ASC',
        ]);
        if ($q->have_posts()) { $p = $q->posts[0]; wp_reset_postdata(); return $p; }
        wp_reset_postdata();

        return null;
    }
}

if (!function_exists('ayg_get_staff_meta')) {
    /**
     * Normalize staff meta for rendering.
     */
    function ayg_get_staff_meta(int $staff_id) : array {
        return [
            'name'          => get_the_title($staff_id),
            'job_title'     => get_post_meta($staff_id, 'job_title', true),
            'email'         => get_post_meta($staff_id, 'email', true),
            'mobile_phone'  => get_post_meta($staff_id, 'mobile_phone', true),
            'office_phone'  => get_post_meta($staff_id, 'office_phone', true),
            'portrait_url'  => get_the_post_thumbnail_url($staff_id, 'medium') ?: '',
            'permalink'     => get_permalink($staff_id),
        ];
    }
}
