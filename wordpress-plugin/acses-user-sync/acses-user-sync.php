<?php
/**
 * Plugin Name: ACSES User Sync
 * Plugin URI: https://acses.org
 * Description: Syncs user data between ACSES portal database and WordPress
 * Version: 1.0.0
 * Author: ACSES
 * Author URI: https://acses.org
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: acses-user-sync
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include WordPress core files
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/capabilities.php');
require_once(ABSPATH . 'wp-includes/user.php');

// Define plugin constants
define('ACSES_USER_SYNC_VERSION', '1.0.0');
define('ACSES_USER_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ACSES_USER_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once ACSES_USER_SYNC_PLUGIN_DIR . 'includes/class-acses-db-connection.php';
require_once ACSES_USER_SYNC_PLUGIN_DIR . 'includes/class-acses-user-sync.php';
require_once ACSES_USER_SYNC_PLUGIN_DIR . 'includes/class-acses-admin-settings.php';

// Add custom cron schedule
add_filter('cron_schedules', 'acses_add_cron_interval');
function acses_add_cron_interval($schedules) {
    $schedules['acses_one_minute'] = array(
        'interval' => 60,
        'display' => 'Every Minute'
    );
    return $schedules;
}

// Initialize the plugin
function acses_user_sync_init() {
    $user_sync = new ACSES_User_Sync();
    $user_sync->init();
    
    // Initialize admin settings
    if (is_admin()) {
        $admin_settings = new ACSES_Admin_Settings();
    }
}
add_action('plugins_loaded', 'acses_user_sync_init');

// Activation hook
register_activation_hook(__FILE__, 'acses_user_sync_activate');
function acses_user_sync_activate() {
    // Set default options
    $default_options = array(
        'portal_database' => 'acses_local',
        'portal_table' => 'users',
        'portal_link' => '',
        'default_role' => 'subscriber'
    );
    
    add_option('acses_user_sync_options', $default_options);
    update_option('acses_user_sync_version', ACSES_USER_SYNC_VERSION);
    
    // Schedule the auto-sync event
    if (!wp_next_scheduled('acses_auto_sync_users')) {
        wp_schedule_event(time(), 'acses_one_minute', 'acses_auto_sync_users');
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'acses_user_sync_deactivate');
function acses_user_sync_deactivate() {
    // Clear the scheduled event
    wp_clear_scheduled_hook('acses_auto_sync_users');
} 
