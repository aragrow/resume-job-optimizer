<?php
if (!defined('ABSPATH')) exit;

// Add Admin Page
function rjo_admin_page() {

    // error_log('Exec-> rjo_admin_page');

    add_menu_page(
        'Resume Job Optimizer',
        'RJO',
        'manage_options',
        'resume-job-optimizer',
        'rjo_admin_page_html',
        'dashicons-hammer', // Icon for the menu
        20 // Menu position
    );
}
add_action('admin_menu', 'rjo_admin_page');

// Admin Page Content
function rjo_admin_page_html() {

    //error_log('Exec-> rjo_admin_page_html');

    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission
    if (isset($_POST['rjo_match_submit'])) {
        $resume_id = intval($_POST['resume_id']);
        $job_id = intval($_POST['job_id']);

        $result = rjo_process_matching($resume_id, $job_id);

        if ($result) {
            echo '<div class="notice notice-success is-dismissible"><p>Optimized resume created successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Failed to create optimized resume. Please check the debug log.</p></div>';
        }
    }

    // Fetch Resumes and Jobs
    $resumes = get_posts(['post_type' => 'resume', 'numberposts' => -1]);
    $jobs = get_posts(['post_type' => 'job', 'numberposts' => -1]);

    $plugin_uri = $plugin_url = plugins_url('resume-job-optimizer');

    ?>
    <div class="wrap">
        <h1>Resume Job Optimizer</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th rowspan="99">
                        <img id="rotating-image" src="<?php echo $plugin_uri ?>/assets/imgs/artificial-intelligence-icon-1.png" width="65%" alt="Rotating Image">
                    </th>
                    <th scope="row">
                        <label for="resume_id">Select Resume</label>
                    </th>
                    <td>
                        <select name="resume_id" id="resume_id" required>
                            <option value="">-- Select a Resume --</option>
                            <?php foreach ($resumes as $resume): ?>
                                <option value="<?php echo esc_attr($resume->ID); ?>">
                                    <?php echo esc_html($resume->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="job_id">Select Job</label>
                    </th>
                    <td>
                        <select name="job_id" id="job_id" required>
                            <option value="">-- Select a Job --</option>
                            <?php foreach ($jobs as $job): ?>
                                <option value="<?php echo esc_attr($job->ID); ?>">
                                    <?php echo esc_html($job->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Optimize Resume', 'primary', 'rjo_match_submit'); ?>
        </form>
    </div>
    <?php
}
