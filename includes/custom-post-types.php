<?php
if (!defined('ABSPATH')) exit;

// Register Custom Post Types
function rjo_register_post_types() {

   // error_log('Exec-> rjo_register_post_types');

    register_post_type('resume', [
        'label' => 'Resumes',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'resume'],
    ]);

    // Register Job
    register_post_type('job', [
        'label' => 'Jobs',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'job'],
    ]);

    // Register Optimized Resumes
    register_post_type('optimized', [
        'label' => 'Optimized Resumes',
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'taxonomies' => ['category'], // Enable categories
        'rewrite' => ['slug' => 'optimized'],
    ]);

}

add_action('init', 'rjo_register_post_types');
