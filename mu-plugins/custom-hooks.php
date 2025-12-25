<?php

function add_custom_query_vars($vars)
{
    $vars[] = 'startDate';
    $vars[] = 'endDate';
    $vars[] = 'type';
    $vars[] = 'category';
    $vars[] = 'desc';
    return $vars;
}
add_filter('query_vars', 'add_custom_query_vars');
