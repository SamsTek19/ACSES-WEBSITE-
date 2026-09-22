<?php
/**
 * Admin settings class for ACSES User Sync
 */

class ACSES_Admin_Settings {
    private $options;
    private $db_connection;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Handle AJAX sync action
        add_action('wp_ajax_sync_all_users', array($this, 'sync_all_users'));
        
        // Schedule automatic sync
        add_action('acses_auto_sync_users', array($this, 'auto_sync_users'));
        if (!wp_next_scheduled('acses_auto_sync_users')) {
            wp_schedule_event(time(), 'acses_one_minute', 'acses_auto_sync_users');
        }
        
        $this->db_connection = new ACSES_DB_Connection();
    }

    public function enqueue_admin_scripts($hook) {
        if ('settings_page_acses-user-sync' !== $hook) {
            return;
        }
        
        wp_enqueue_script('acses-admin-js', ACSES_USER_SYNC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), ACSES_USER_SYNC_VERSION, true);
        wp_enqueue_style('acses-admin-css', ACSES_USER_SYNC_PLUGIN_URL . 'assets/css/admin.css', array(), ACSES_USER_SYNC_VERSION);
        
        wp_localize_script('acses-admin-js', 'acsesAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('acses_sync_nonce')
        ));
    }

    public function add_plugin_page() {
        add_options_page(
            'ACSES User Sync Settings',
            'ACSES User Sync',
            'manage_options',
            'acses-user-sync',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('acses_user_sync_options');
        ?>
        <div class="wrap acses-admin-wrap">
            <h1 class="acses-admin-title">ACSES User Sync Settings</h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Settings saved successfully.</p>
                </div>
            <?php endif; ?>

            <div class="nav-tab-wrapper">
                <a href="#settings" class="nav-tab nav-tab-active">Settings</a>
                <a href="#users" class="nav-tab">Portal Users</a>
            </div>

            <div id="settings" class="tab-content">
                <form method="post" action="options.php" class="acses-settings-form">
                    <?php
                    settings_fields('acses_user_sync_options');
                    do_settings_sections('acses-user-sync');
                    submit_button('Save Settings', 'primary', 'submit', true, array('class' => 'acses-save-button'));
                    ?>
                </form>
            </div>

            <div id="users" class="tab-content" style="display: none;">
                <h2 class="acses-section-title">Portal Users</h2>
                
                <div class="acses-sync-controls">
                    <div class="acses-sync-left">
                        <button type="button" class="button button-primary acses-sync-button" id="sync-all-users">
                            <span class="dashicons dashicons-update"></span> Sync Now
                        </button>
                        <span class="sync-status"></span>
                    </div>
                    <div class="acses-sync-right">
                        <div class="acses-auto-sync-info">
                            <span class="dashicons dashicons-clock"></span>
                            <span class="auto-sync-text">Next auto-sync in: </span>
                            <span class="auto-sync-countdown">00:00</span>
                        </div>
                    </div>
                </div>

                <?php $this->display_portal_users(); ?>
            </div>
        </div>

        <style>
            .acses-admin-wrap {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                max-width: 100%;
                margin: 20px auto;
            }

            .acses-admin-title {
                color: #1a472a;
                font-size: 28px;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #f0b323;
            }

            .acses-section-title {
                color: #1a472a;
                font-size: 24px;
                margin: 20px 0;
            }

            .nav-tab-wrapper {
                border-bottom: 2px solid #1a472a;
                margin-bottom: 20px;
            }

            .nav-tab {
                background: #f8f9fa;
                border: 1px solid #1a472a;
                border-bottom: none;
                color: #1a472a;
                padding: 10px 20px;
                margin-right: 5px;
                border-radius: 4px 4px 0 0;
                transition: all 0.3s ease;
            }

            .nav-tab:hover {
                background: #e9ecef;
            }

            .nav-tab-active {
                background: #1a472a;
                color: #fff;
            }

            .acses-settings-form {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                border: 1px solid #e0e0e0;
            }

            .acses-save-button {
                background: #1a472a !important;
                border-color: #1a472a !important;
                color: #fff !important;
                padding: 8px 20px !important;
                height: auto !important;
                font-size: 14px !important;
                transition: all 0.3s ease;
            }

            .acses-save-button:hover {
                background: #2d5a3d !important;
                border-color: #2d5a3d !important;
            }

            .acses-sync-button {
                background: #1a472a !important;
                border-color: #1a472a !important;
                color: #fff !important;
                padding: 8px 20px !important;
                height: auto !important;
                font-size: 14px !important;
                transition: all 0.3s ease;
                display: flex !important;
                align-items: center;
                gap: 8px;
            }

            .acses-sync-button:hover {
                background: #2d5a3d !important;
                border-color: #2d5a3d !important;
            }

            .acses-sync-button .dashicons {
                font-size: 16px;
                height: 16px;
                width: 16px;
            }

            .acses-sync-controls {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 1px solid #e0e0e0;
            }

            .acses-sync-left {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .acses-sync-right {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .acses-auto-sync-info {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 15px;
                background: #fff;
                border-radius: 4px;
                border: 1px solid #e0e0e0;
            }

            .acses-auto-sync-info .dashicons {
                color: #1a472a;
            }

            .auto-sync-text {
                color: #666;
            }

            .auto-sync-countdown {
                font-weight: 600;
                color: #1a472a;
            }

            .acses-users-table-container {
                max-height: 500px;
                overflow-y: auto;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-top: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                width: 100%;
            }

            .acses-users-table-container table {
                margin-bottom: 0;
                border-collapse: collapse;
                width: 100%;
            }

            .acses-users-table-container th {
                background: #1a472a;
                color: #fff;
                padding: 12px;
                text-align: left;
                position: sticky;
                top: 0;
                z-index: 1;
            }

            .acses-users-table-container td {
                padding: 12px;
                border-bottom: 1px solid #e0e0e0;
            }

            .acses-users-table-container tr:nth-child(even) {
                background: #f8f9fa;
            }

            .acses-users-table-container tr:hover {
                background: #f0f0f0;
            }

            .status-synced {
                color: #1a472a;
                font-weight: 500;
            }

            .status-not-synced {
                color: #dc3545;
                font-weight: 500;
            }

            .sync-status {
                margin-left: 15px;
                padding: 8px 15px;
                border-radius: 4px;
                font-weight: 500;
            }

            .sync-status.success {
                background: #d4edda;
                color: #1a472a;
                border: 1px solid #c3e6cb;
            }

            .sync-status.error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            .acses-users-table-container::-webkit-scrollbar {
                width: 8px;
            }

            .acses-users-table-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .acses-users-table-container::-webkit-scrollbar-thumb {
                background: #1a472a;
                border-radius: 4px;
            }

            .acses-users-table-container::-webkit-scrollbar-thumb:hover {
                background: #2d5a3d;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Tab switching
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                var target = $(this).attr('href');
                $('.tab-content').hide();
                $(target).show();
            });

            // Countdown timer
            function updateCountdown() {
                var now = new Date();
                var nextMinute = new Date(now);
                nextMinute.setMinutes(nextMinute.getMinutes() + 1);
                nextMinute.setSeconds(0);
                
                var diff = nextMinute - now;
                var seconds = Math.floor(diff / 1000);
                var minutes = Math.floor(seconds / 60);
                seconds = seconds % 60;
                
                $('.auto-sync-countdown').text(
                    minutes.toString().padStart(2, '0') + ':' + 
                    seconds.toString().padStart(2, '0')
                );
            }

            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);

            // Sync all users
            $('#sync-all-users').on('click', function() {
                var button = $(this);
                var status = $('.sync-status');
                button.prop('disabled', true).html('<span class="dashicons dashicons-update"></span> Syncing...');
                status.removeClass('success error').text('Syncing users...');
                
                $.ajax({
                    url: acsesAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'sync_all_users',
                        nonce: acsesAjax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            status.addClass('success').html(response.data);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            status.addClass('error').html(response.data);
                        }
                    },
                    error: function() {
                        status.addClass('error').html('An error occurred while syncing users.');
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync Now');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function page_init() {
        register_setting(
            'acses_user_sync_options',
            'acses_user_sync_options',
            array($this, 'sanitize')
        );

        add_settings_section(
            'acses_database_section',
            'Database Connection Settings',
            array($this, 'print_section_info'),
            'acses-user-sync'
        );

        add_settings_field(
            'portal_host',
            'Database Host',
            array($this, 'portal_host_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'portal_username',
            'Database Username',
            array($this, 'portal_username_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'portal_password',
            'Database Password',
            array($this, 'portal_password_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'portal_database',
            'Database Name',
            array($this, 'portal_database_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'portal_table',
            'Users Table Name',
            array($this, 'portal_table_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'portal_link',
            'Portal Website URL',
            array($this, 'portal_link_callback'),
            'acses-user-sync',
            'acses_database_section'
        );

        add_settings_field(
            'default_role',
            'Default WordPress Role',
            array($this, 'default_role_callback'),
            'acses-user-sync',
            'acses_database_section'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        
        if (isset($input['portal_host'])) {
            $new_input['portal_host'] = sanitize_text_field($input['portal_host']);
        }

        if (isset($input['portal_username'])) {
            $new_input['portal_username'] = sanitize_text_field($input['portal_username']);
        }

        if (isset($input['portal_password'])) {
            $new_input['portal_password'] = sanitize_text_field($input['portal_password']);
        }

        if (isset($input['portal_database'])) {
            $new_input['portal_database'] = sanitize_text_field($input['portal_database']);
        }

        if (isset($input['portal_table'])) {
            $new_input['portal_table'] = sanitize_text_field($input['portal_table']);
        }

        if (isset($input['portal_link'])) {
            $new_input['portal_link'] = esc_url_raw($input['portal_link']);
        }

        if (isset($input['default_role'])) {
            $new_input['default_role'] = sanitize_text_field($input['default_role']);
        }

        return $new_input;
    }

    public function print_section_info() {
        print 'Enter your portal database details below. Since both websites are on the same cPanel hosting, we\'ll use the same database credentials as your WordPress installation.';
    }

    public function portal_host_callback() {
        printf(
            '<input type="text" id="portal_host" name="acses_user_sync_options[portal_host]" value="%s" class="regular-text" />',
            isset($this->options['portal_host']) ? esc_attr($this->options['portal_host']) : 'localhost'
        );
    }

    public function portal_username_callback() {
        printf(
            '<input type="text" id="portal_username" name="acses_user_sync_options[portal_username]" value="%s" class="regular-text" />',
            isset($this->options['portal_username']) ? esc_attr($this->options['portal_username']) : ''
        );
    }

    public function portal_password_callback() {
        printf(
            '<input type="password" id="portal_password" name="acses_user_sync_options[portal_password]" value="%s" class="regular-text" />',
            isset($this->options['portal_password']) ? esc_attr($this->options['portal_password']) : ''
        );
    }

    public function portal_database_callback() {
        printf(
            '<input type="text" id="portal_database" name="acses_user_sync_options[portal_database]" value="%s" class="regular-text" />',
            isset($this->options['portal_database']) ? esc_attr($this->options['portal_database']) : 'acses_local'
        );
    }

    public function portal_table_callback() {
        printf(
            '<input type="text" id="portal_table" name="acses_user_sync_options[portal_table]" value="%s" class="regular-text" />',
            isset($this->options['portal_table']) ? esc_attr($this->options['portal_table']) : 'users'
        );
    }

    public function portal_link_callback() {
        printf(
            '<input type="url" id="portal_link" name="acses_user_sync_options[portal_link]" value="%s" class="regular-text" placeholder="https://portal.example.com" />',
            isset($this->options['portal_link']) ? esc_attr($this->options['portal_link']) : ''
        );
    }

    public function default_role_callback() {
        $roles = wp_roles()->get_names();
        $current_role = isset($this->options['default_role']) ? $this->options['default_role'] : 'subscriber';
        
        echo '<select id="default_role" name="acses_user_sync_options[default_role]">';
        foreach ($roles as $role => $name) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($role),
                selected($current_role, $role, false),
                esc_html($name)
            );
        }
        echo '</select>';
    }

    public function display_portal_users() {
        if (!$this->db_connection->connect()) {
            echo '<div class="notice notice-error"><p>Could not connect to the portal database. Please check your database settings.</p></div>';
            return;
        }

        $table_name = $this->options['portal_table'];
        $users = $this->db_connection->get_all_portal_users();
        
        if (empty($users)) {
            echo '<p>No users found in the portal database.</p>';
            return;
        }

        echo '<div class="acses-users-table-container">';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th style="color: #fff;">Username</th>';
        echo '<th style="color: #fff;">Full Name</th>';
        echo '<th style="color: #fff;">Email</th>';
        echo '<th style="color: #fff;">Phone</th>';
        echo '<th style="color: #fff;">Status</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($users as $user) {
            $wp_user = get_user_by('login', $user->username);
            $status = $wp_user ? 'Synced' : 'Not Synced';
            $status_class = $wp_user ? 'status-synced' : 'status-not-synced';
            
            echo '<tr>';
            echo '<td>' . esc_html($user->username) . '</td>';
            echo '<td>' . esc_html($user->fullname ?? 'N/A') . '</td>';
            echo '<td>' . esc_html($user->email) . '</td>';
            echo '<td>' . esc_html($user->phone_number ?? 'N/A') . '</td>';
            echo '<td><span class="' . esc_attr($status_class) . '">' . esc_html($status) . '</span></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</div>';
    }

    public function sync_all_users() {
        check_ajax_referer('acses_sync_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        if (!$this->db_connection->connect()) {
            wp_send_json_error('Could not connect to the portal database');
        }

        $users = $this->db_connection->get_all_portal_users();
        $options = get_option('acses_user_sync_options');
        $default_role = isset($options['default_role']) ? $options['default_role'] : 'subscriber';
        
        $synced = 0;
        $errors = array();

        foreach ($users as $user) {
            $wp_user = get_user_by('login', $user->username);
            
            if (!$wp_user) {
                $userdata = array(
                    'user_login' => $user->username,
                    'user_email' => $user->email,
                    'user_pass' => $user->password,
                    'role' => $default_role,
                    'display_name' => $user->fullname ?? $user->username
                );
                
                $user_id = wp_insert_user($userdata);
                
                if (is_wp_error($user_id)) {
                    $errors[] = sprintf('Failed to create user %s: %s', $user->username, $user_id->get_error_message());
                } else {
                    // Add phone number and full name to user meta
                    if (isset($user->phone_number)) {
                        update_user_meta($user_id, 'phone', $user->phone_number);
                    }
                    if (isset($user->fullname)) {
                        update_user_meta($user_id, 'first_name', $user->fullname);
                    }
                    $synced++;
                }
            } else {
                // Update existing user's phone number and full name
                if (isset($user->phone_number)) {
                    update_user_meta($wp_user->ID, 'phone', $user->phone_number);
                }
                if (isset($user->fullname)) {
                    update_user_meta($wp_user->ID, 'first_name', $user->fullname);
                    wp_update_user(array(
                        'ID' => $wp_user->ID,
                        'display_name' => $user->fullname
                    ));
                }
            }
        }

        if (empty($errors)) {
            wp_send_json_success(sprintf('Successfully synced %d users', $synced));
        } else {
            wp_send_json_error(implode('<br>', $errors));
        }
    }

    public function auto_sync_users() {
        if (!$this->db_connection->connect()) {
            return;
        }

        $users = $this->db_connection->get_all_portal_users();
        $options = get_option('acses_user_sync_options');
        $default_role = isset($options['default_role']) ? $options['default_role'] : 'subscriber';

        foreach ($users as $user) {
            $wp_user = get_user_by('login', $user->username);
            
            if (!$wp_user) {
                $userdata = array(
                    'user_login' => $user->username,
                    'user_email' => $user->email,
                    'user_pass' => $user->password,
                    'role' => $default_role,
                    'display_name' => $user->fullname ?? $user->username
                );
                
                $user_id = wp_insert_user($userdata);
                
                if (!is_wp_error($user_id)) {
                    if (isset($user->phone_number)) {
                        update_user_meta($user_id, 'phone', $user->phone_number);
                    }
                    if (isset($user->fullname)) {
                        update_user_meta($user_id, 'first_name', $user->fullname);
                    }
                }
            } else {
                if (isset($user->phone_number)) {
                    update_user_meta($wp_user->ID, 'phone', $user->phone_number);
                }
                if (isset($user->fullname)) {
                    update_user_meta($wp_user->ID, 'first_name', $user->fullname);
                    wp_update_user(array(
                        'ID' => $wp_user->ID,
                        'display_name' => $user->fullname
                    ));
                }
            }
        }
    }
} 
