<?php
/**
 * Plugin Name: Resume Job Optimizer
 * Description: Matches resumes with job descriptions using AI and optimizes resumes for job postings.
 * Version: 1.0
 * Author: David Arago - ARAGROW, LLC
 * 
 * Summary
 *
 *   resume-job-optimizer.php: Main plugin file.
 *   includes/custom-post-types.php: Registers custom post types.
 *   includes/api-integration.php: Handles API communication.
 *   includes/matching-logic.php: Processes matching and optimization.
 *   includes/admin-ui.php: Admin interface for triggering actions.
 *   includes/utilities.php: Utility functions.
 * 
 * How It Works
 *
 *   Admin Menu:
 *       Go to Resume Optimizer in the WordPress admin panel.
 *       You’ll see a form with dropdowns to select a Resume and a Job.
 *
 *   Process:
 *       When you submit the form, the plugin calls the rjo_process_matching function.
 *       The function sends the Resume and Job data to the Google Gemini API.
 *       The API returns a match percentage and an optimized resume.
 *       The plugin creates a new Optimized post with the returned content and saves the match percentage as meta.
 *
 *   Feedback:
 *       A success or error message is displayed after form submission.
*/

if (!defined('ABSPATH')) exit;

// Define a global debug flag
if (!defined('RJO_DEBUG')) {
    define('RJO_DEBUG', true); // Set to false to disable debugging
}

// Include files
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/matching-logic.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/utilities.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page.php';

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'rjo_activate');
register_deactivation_hook(__FILE__, 'rjo_deactivate');

function rjo_activate() {

   if (RJO_DEBUG) error_log('Exec-> rjo_activate');

    // Flush rewrite rules on activation
    rjo_register_post_types();
    flush_rewrite_rules();

}

function rjo_deactivate() {

    if (RJO_DEBUG) error_log('Exec-> rjo_deactivate');

    // Flush rewrite rules on deactivation
    flush_rewrite_rules();

}

function rjo_enqueue_scripts() {
    wp_enqueue_script('rjo-script', plugins_url('dist/js/script.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_style('rjo-style', plugins_url('dist/css/styles.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'rjo_enqueue_scripts');

function rjo_enqueue_sadmin_cripts() {
    wp_enqueue_script('rjo-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_style('rjo-style', plugins_url('assets/css/styles.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'rjo_enqueue_sadmin_cripts');