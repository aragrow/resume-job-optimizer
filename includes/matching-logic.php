<?php
if (!defined('ABSPATH')) exit;

function rjo_process_matching($resume_id, $job_id) {

    error_log('Exec-> rjo_process_matching');

    $resume = get_post($resume_id);
    $job = get_post($job_id);

    if (!$resume || !$job) {
        return false;
    }

    $post_title = $resume->post_title . ' - Optimized for ' . $job->post_title;
    $post_id = post_exists($post_title);
    if ($post_id) {
        $post = get_post($post_id);
        if ($post->post_status != 'draft') {
            echo '<h2>Unable to create optimize resume, the job is not Draft';
            exit;
        }
    }

    $resume_text = $resume->post_content;
    // Get all custom fields for the post
    $custom_fields = get_post_meta( $job_id );
    $job_description = '';

    // Loop through the custom fields and display them
    foreach ( $custom_fields as $key => $value ) {
        $job_description .= $key . ':' . implode( ', ', $value ) . '\n';
    }

    $job_description .= $job->post_content;

    $api_result = rjo_send_to_gemini_api($resume_text, $job_description);
   
    if (!$api_result['status']) {
        return false;
    }

    if ($post_id) 
    { 
        // Update the draft post
        $post_arr = [
            'ID' => $post_id,
            'post_content' => $api_result['anwser']
        ];
        $optimized_id = wp_update_post($post_arr);

    } else {
        // Create Optimized Resume Post
        $post_arr = [
            'post_type' => 'optimized',
            'post_title' => $post_title,
            'post_content' => $api_result['anwser'],
            'post_status' => 'draft',
        ];
        $optimized_id = wp_insert_post($post_arr);

    }

    return $optimized_id;
}