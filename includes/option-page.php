<?php
if (!defined('ABSPATH')) exit;

function rjo_add_admin_menu() {
    add_options_page(
        'Resume Job Optimizer Settings',
        'RJO',
        'manage_options',
        'rjo-settings',
        'rjo_settings_page'
    );
}
add_action('admin_menu', 'rjo_add_admin_menu');

function rjo_settings_page() {
    ?>
    <div class="wrap">
        <h1>Resume Job Optimizer Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'rjo-settings-group' ); ?>
            <?php do_settings_sections( 'rjo-settings'  
 ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php  

}

function rjo_settings_init() {
    register_setting(
        'rjo-settings-group',
        'rjo_ai_uri'
    );
    register_setting(
        'rjo-settings-group',
        'rjo_ai_key'
    );

    add_settings_section(
        'rjo_settings_section',
        'AI Settings',
        'rjo_settings_section_callback',
        'rjo-settings'
    );

    add_settings_field(
        'rjo_ai_uri_field',
        'AI URI:',
        'rjo_ai_uri_field_callback',
        'rjo-settings',
        'rjo_settings_section'
    );

    add_settings_field(
        'rjo_ai_key_field',
        'AI Key:',
        'rjo_ai_key_field_callback',
        'rjo-settings',
        'rjo_settings_section'
    );
}
add_action('admin_init', 'rjo_settings_init');

function rjo_settings_section_callback() {
    // No specific content for this section
}

function rjo_ai_uri_field_callback() {
    ?>
    <input type="text" name="rjo_ai_uri" value="<?php echo esc_attr( get_option('rjo_ai_uri') ); ?>" />
    <?php
}

function rjo_ai_key_field_callback() {
    ?>
    <input type="text" name="rjo_ai_key" value="<?php echo esc_attr( get_option('rjo_ai_key') ); ?>" />
    <?php
}