<?php
/**
 * Main user synchronization class for ACSES User Sync
 */

// Include WordPress core files
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/capabilities.php');
require_once(ABSPATH . 'wp-includes/user.php');

class ACSES_User_Sync {
    private $db_connection;

    public function __construct() {
        $this->db_connection = new ACSES_DB_Connection();
    }

    public function init() {
        // Hook into WordPress user actions
        add_action('user_register', array($this, 'sync_user_to_portal'));
        add_action('profile_update', array($this, 'sync_user_update_to_portal'));
        add_action('password_reset', array($this, 'sync_password_to_portal'), 10, 2);
        
        // Hook into login process
        add_filter('authenticate', array($this, 'authenticate_user'), 20, 3);
    }

    /**
     * Sync new WordPress user to portal database
     */
    public function sync_user_to_portal($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return false;
        }

        $portal_user_data = array(
            'username' => $user->user_login,
            'email' => $user->user_email,
            'password' => $user->user_pass, // WordPress already hashes passwords
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        return $this->db_connection->create_portal_user($portal_user_data);
    }

    /**
     * Sync user updates to portal database
     */
    public function sync_user_update_to_portal($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return false;
        }

        $portal_user = $this->db_connection->get_portal_user($user->user_login);
        
        if (!$portal_user) {
            return $this->sync_user_to_portal($user_id);
        }

        $update_data = array(
            'email' => $user->user_email,
            'updated_at' => current_time('mysql')
        );

        return $this->db_connection->update_portal_user($portal_user->id, $update_data);
    }

    /**
     * Sync password changes to portal database
     */
    public function sync_password_to_portal($user, $new_pass) {
        $portal_user = $this->db_connection->get_portal_user($user->user_login);
        
        if (!$portal_user) {
            return false;
        }

        $update_data = array(
            'password' => wp_hash_password($new_pass),
            'updated_at' => current_time('mysql')
        );

        return $this->db_connection->update_portal_user($portal_user->id, $update_data);
    }

    /**
     * Authenticate user against both WordPress and portal database
     */
    public function authenticate_user($user, $username, $password) {
        // If WordPress authentication failed, try portal database
        if (is_wp_error($user)) {
            $portal_user = $this->db_connection->get_portal_user($username);
            
            if ($portal_user && wp_check_password($password, $portal_user->password)) {
                // Create WordPress user if doesn't exist
                $wp_user = get_user_by('login', $username);
                if (!$wp_user) {
                    $userdata = array(
                        'user_login' => $username,
                        'user_email' => $portal_user->email,
                        'user_pass' => $password,
                        'role' => 'subscriber'
                    );
                    
                    $user_id = wp_insert_user($userdata);
                    if (!is_wp_error($user_id)) {
                        return get_user_by('id', $user_id);
                    }
                } else {
                    return $wp_user;
                }
            }
        }
        
        return $user;
    }
} 