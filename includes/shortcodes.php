<?php

/**
 * Convoca Theme
 *
 * @package    Convoca\Theme
 * @subpackage Includes
 *
 * @copyright  Copyright (C) 2026 Jose Carlos Nieto Ramos
 * @license    GPL-2.0-or-later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

/**
 * Custom shortcodes for the Convoca theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display the user profile with membership status and active inscriptions.
 * Usage: [convoca_mi_perfil]
 */
add_shortcode('convoca_mi_perfil', function () {
    if (!is_user_logged_in()) {
        return sprintf(
            '<div class="conv-profile-login">%s <a href="%s" class="button">%s</a></div>',
            __('Inicia sesión para ver tu perfil.', 'convoca-theme'),
            wp_login_url(get_permalink()),
            __('Iniciar sesión', 'convoca-theme')
        );
    }

    $current_user = wp_get_current_user();
    $email = $current_user->user_email;

    // 1. Get member record.
    $members = get_posts([
        'post_type' => 'miembro',
        'posts_per_page' => 1,
        'meta_query' => [
            ['key' => '_convoca_email', 'value' => $email]
        ]
    ]);

    $member_html = '';
    if (!empty($members)) {
        $m = $members[0];
        $estado = get_post_meta($m->ID, '_convoca_estado_miembro', true);
        $plan = get_post_meta($m->ID, '_convoca_plan', true);
        $renovacion = get_post_meta($m->ID, '_convoca_fecha_renovacion', true);

        $member_html = sprintf(
            '<div class="conv-member-info card glass">
                <h3>%s</h3>
                <p><strong>%s:</strong> <span class="badge state-%s">%s</span></p>
                <p><strong>%s:</strong> %s</p>
                <p><strong>%s:</strong> %s</p>
            </div>',
            __('Tu condición de socio/a', 'convoca-theme'),
            __('Estado', 'convoca-theme'),
            esc_attr($estado),
            esc_html(ucfirst($estado)),
            __('Plan', 'convoca-theme'),
            esc_html(ucfirst($plan)),
            __('Próxima renovación', 'convoca-theme'),
            esc_html($renovacion)
        );
    } else {
        $member_html = sprintf(
            '<div class="conv-member-info card glass">
                <p>%s</p>
                <a href="%s" class="button">%s</a>
            </div>',
            sprintf(__('Aún no eres socio/a de %s.', 'convoca-theme'), get_bloginfo('name')),
            home_url('/hazte-socio/'),
            __('Hacerse socio/a', 'convoca-theme')
        );
    }

    // 2. Get active inscriptions.
    $inscriptions = get_posts([
        'post_type' => 'inscripcion',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            ['key' => '_convoca_email', 'value' => $email],
            ['key' => '_convoca_estado', 'value' => 'cancelada', 'compare' => '!='],
        ]
    ]);

    $insc_html = '<h3>' . __('Tus inscripciones activas', 'convoca-theme') . '</h3>';
    if (!empty($inscriptions)) {
        $insc_html .= '<div class="conv-inscriptions-grid">';
        foreach ($inscriptions as $i) {
            $actividad_id = get_post_meta($i->ID, '_convoca_actividad_id', true);
            $estado = get_post_meta($i->ID, '_convoca_estado', true);
            $fecha = get_the_date('d/m/Y', $i->ID);

            $insc_html .= sprintf(
                '<div class="conv-insc-item card secondary">
                    <h4>%s</h4>
                    <p><span class="badge state-%s">%s</span> — %s</p>
                    <a href="%s" class="link-arrow">%s</a>
                </div>',
                get_the_title($actividad_id),
                esc_attr($estado),
                esc_html(ucfirst($estado)),
                $fecha,
                get_permalink($actividad_id),
                __('Ver actividad', 'convoca-theme')
            );
        }
        $insc_html .= '</div>';
    } else {
        $insc_html .= '<p>' . __('No tienes inscripciones actives en este momento.', 'convoca-theme') . '</p>';
    }

    return '<div class="convoca-my-profile">' . $member_html . $insc_html . '</div>';
});
