<?php
/*
Plugin Name: SubjectsPlus integration with WordPress
Description: Custom plugin for integrating SubjectsPlus with WordPress using shortcodes.
Version: 1.0
Author: Abid Fakhre Alam
*/

// Add settings menu in the admin panel
add_action('admin_menu', 'sp_add_admin_menu');
add_action('admin_init', 'sp_settings_init');

function sp_add_admin_menu() {
    add_menu_page(
        'SubjectsPlus Settings',
        'SubjectsPlus4.6',
        'manage_options',
        'subjectsplus-settings',
        'sp_options_page'
    );
}

function sp_settings_init() {
    register_setting('sp_plugin_page', 'sp_api_key');
    register_setting('sp_plugin_page', 'sp_api_url');

    add_settings_section(
        'sp_plugin_page_section',
        'API Settings',
        'sp_settings_section_callback',
        'sp_plugin_page'
    );

    add_settings_field(
        'sp_api_key',
        'API Key',
        'sp_api_key_render',
        'sp_plugin_page',
        'sp_plugin_page_section'
    );

    add_settings_field(
        'sp_api_url',
        'API URL',
        'sp_api_url_render',
        'sp_plugin_page',
        'sp_plugin_page_section'
    );
}

function sp_settings_section_callback() {
    echo 'Enter your SubjectsPlus API settings below:';
}

function sp_api_key_render() {
    $options = get_option('sp_api_key');
    echo "<input type='text' name='sp_api_key' value='{$options}' />";
}

function sp_api_url_render() {
    $options = get_option('sp_api_url');
    echo "<input type='text' name='sp_api_url' value='{$options}' />";
}

function sp_options_page() {
    ?>
    <div class="wrap">
        <h2>SubjectsPlus Settings</h2>

        <form method="post" action="options.php">
            <?php
            settings_fields('sp_plugin_page');
            do_settings_sections('sp_plugin_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

include('subjectsplusclass.php');

function get_sp($atts) {
    extract(shortcode_atts(array(
        'service' => '',
        'display' => 'table', // Default display format is 'table'
        'max' => '',
        'email' => '',
        'department' => '',
        'limit' => '99',
        'letter' => '',
        'search' => '',
        'subject_id' => '',
        'type' => ''
    ), $atts));

    $subjectsplus = new subjectsplus_info();
    $subjectsplus->set_sp_url(get_option('sp_api_url'));
    $subjectsplus->set_sp_key(get_option('sp_api_key'));

    // Call the appropriate method based on shortcode parameters and return the result
    return $subjectsplus->setup_sp_query($atts, $display);
}

add_shortcode('sp', 'get_sp');

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
