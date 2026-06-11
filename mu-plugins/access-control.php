<?php

add_action('template_redirect', 'require_login_for_frontend');
function require_login_for_frontend()
{
    if (is_user_logged_in()) {
        return;
    }

    // The front page renders its own logged-out state (see index.php),
    // so it's the one page anonymous visitors are allowed to see.
    if (is_front_page()) {
        return;
    }

    wp_safe_redirect(wp_login_url(home_url(add_query_arg([], $GLOBALS['wp']->request))));
    exit;
}

add_filter('rest_authentication_errors', 'require_login_for_rest_api');
function require_login_for_rest_api($result)
{
    if (!empty($result)) {
        return $result;
    }
    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', 'REST API requires authentication.', ['status' => 401]);
    }
    return $result;
}

add_filter('xmlrpc_enabled', '__return_false');
