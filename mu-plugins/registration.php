<?php

add_action('login_enqueue_scripts', 'apartment_login_css');
function apartment_login_css()
{
    wp_enqueue_style('bootstrap', get_theme_file_uri('/css/theme.min.css'));
}

add_action('wp_loaded', 'no_admin_bar');
function no_admin_bar()
{
    $current_user = wp_get_current_user();

    if (count($current_user->roles) == 1 and $current_user->roles[0] != 'administrator') {
        show_admin_bar(false);
    }
}

add_filter('login_headerurl', function () {
    return esc_url(site_url('/'));
});

add_filter('login_headertext', function () {
    return get_bloginfo('name');
});

add_filter('login_redirect', function () {
    return '/';
});
