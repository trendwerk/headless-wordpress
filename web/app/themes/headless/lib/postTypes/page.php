<?php
namespace Headless;

$postType = 'page';

$labels = [
    'name' => __('Pages', 'headless'),
    'singular_name' => __('Page', 'headless'),
];

// Remove comments and page attributes
add_action('init', function () use ($postType) {
    remove_post_type_support($postType, 'comments');
    remove_post_type_support($postType, 'page-attributes');
});

// Register custom fields
add_action('acf/init', function () use ($postType) {
    acf_add_local_field_group([
        'key' => $postType,
        'title' => $labels['singular_name']  . ' settings',
        'fields' => [
            [
                'key' => 'field_tab_header',
                'label' => 'Header',
                'placement' => 'left',
                'type' => 'tab',
            ],
            [
                'name' => 'header_image',
                'key' => 'field_header_image',
                'label' => 'Header image',
                'min_width' => 1600,
                'min_height' => 800,
                'type' => 'image',
            ],
            [
                'key' => 'field_tab_seo',
                'label' => 'SEO',
                'placement' => 'left',
                'type' => 'tab',
            ],
            [
                'name' => 'title',
                'key' => 'field_title',
                'label' => 'Page title',
                'instructions' => 'Will display as "{title} - {sitename}". Title will be used if left blank.',
                'maxlength' => 60,
                'type' => 'text',
            ],
            [
                'name' => 'meta_description',
                'key' => 'field_meta_description',
                'label' => 'Meta description',
                'instructions' => 'Page description in search engines and on social media.',
                'maxlength' => 160,
                'type' => 'textarea',
            ],
            [
                'name' => 'og_image',
                'key' => 'field_og_image',
                'label' => 'Open Graph image',
                'instructions' => 'Preview image on social media. Header image will be used if left blank.',
                'min_width' => 1200,
                'min_height' => 630,
                'type' => 'image',
            ],
        ],
        'location' => [[[
            'param' => 'post_type',
            'operator' => '==',
            'value' => $postType,
        ]]],
        'graphql_field_name' => 'fields'
    ]);
});
