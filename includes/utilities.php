<?php
if (!defined('ABSPATH')) exit;

// Common Helper Functions
function rjo_get_post_type_items($post_type) {

    error_log('Exec-> rjo_get_post_type_items');

    return get_posts(['post_type' => $post_type, 'numberposts' => -1]);

}

function rjo_debug_log($data, $context = '') {

    error_log('Exec-> rjo_debug_log');

    if (defined('RJO_DEBUG') && RJO_DEBUG && WP_DEBUG) {
        $log_message = '[RJO Debug] ';
        if (!empty($context)) {
            $log_message .= '[' . $context . '] ';
        }
        // Check if data is an array or object
        if (is_array($data) || is_object($data)) {
            $log_message .= print_r($data, true); // Convert array/object to a readable string
        } else {
            $log_message .= $data; // Log scalar values directly
        }
        error_log($log_message);
    }

}