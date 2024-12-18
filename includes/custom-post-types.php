<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    // Add the shared menu for grouping
    add_menu_page(
        'RJO Posts', // Page title
        'RJO Posts',     // Menu title
        'manage_options',       // Capability
        'resume_job_optimizer_group', // Menu slug
        '',                     // Callback (no page, just grouping)
        'dashicons-clipboard',  // Icon
        20                       // Position
    );
});

// Register Custom Post Types
function rjo_register_post_types() {

   // error_log('Exec-> rjo_register_post_types');

   $menu_slug = 'resume_job_optimizer_group';

    register_post_type('resume', [
        'label' => 'Resumes',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'resume'],
        'show_in_menu' => $menu_slug,
    ]);

    // Register Job
    register_post_type('job', [
        'label' => 'Jobs',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'job'],
        'show_in_menu' => $menu_slug,
    ]);

    // Register Optimized Resumes
    register_post_type('optimized', [
        'label' => 'Optimized Resumes',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'optimized'],
        'show_in_menu' => $menu_slug,
    ]);

}

add_action('init', 'rjo_register_post_types');

