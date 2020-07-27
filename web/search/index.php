<?php
define('SHORTINIT', true);

require dirname(__DIR__) . '/wp/wp-load.php';

$origin = strstr(WP_URL, 'http://localhost') ? '*' : WP_URL;

header("Access-Control-Allow-Origin: {$origin}");

if (! isset($_GET['q']) || ! $_GET['q']) {
    wp_send_json(['results' => []], 200);
    return;
}

$search = sanitize_text_field($_GET['q']);
$query = $GLOBALS['wpdb']->prepare(
    "SELECT post_content, post_name, post_title
    FROM wp_posts
    INNER JOIN wp_postmeta
    ON wp_posts.ID = wp_postmeta.post_id
    WHERE post_status = 'publish'
    AND post_type != 'nav_menu_item'
    AND (
        post_title LIKE '%%%s%%'
        OR post_content LIKE '%%%s%%'
        OR meta_value LIKE '%%%s%%'
    )
    GROUP BY ID
    ORDER BY post_date DESC
    LIMIT 10",
    $search,
    $search,
    $search
);

$results = $GLOBALS['wpdb']->get_results($query, ARRAY_A) ?: [];
$results = array_map(function ($result) {
    $content = str_replace("\n", ' ', strip_tags($result['post_content']));
    $content = trim(preg_replace('/ ( +)/', ' ', $content));

    if (strlen($content) > 200) {
        $content = substr($content, 0, 200) . '…';
    }

    return [
        'summary' => $content,
        'slug' => "/{$result['post_name']}",
        'title' => $result['post_title'],
    ];
}, $results);

wp_send_json(compact('results'), 200);
