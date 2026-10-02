<?php
/**
 * Database connection class for ACSES User Sync
 */

// Include WordPress core files
require_once(ABSPATH . 'wp-includes/wp-db.php');

class ACSES_DB_Connection {
    private $portal_db;
    private $portal_db_config;
    private $options;

    public function __construct() {
        // Get plugin options
        $this->options = get_option('acses_user_sync_options');
        
        // Set portal database configuration
        $this->portal_db_config = array(
            'host'     => isset($this->options['portal_host']) ? $this->options['portal_host'] : 'localhost',
            'database' => isset($this->options['portal_database']) ? $this->options['portal_database'] : '',
            'username' => isset($this->options['portal_username']) ? $this->options['portal_username'] : '',
            'password' => isset($this->options['portal_password']) ? $this->options['portal_password'] : '',
            'charset'  => 'utf8mb4'
        );
    }

    /**
     * Connect to the portal database
     */
    public function connect() {
        try {
            $this->portal_db = new wpdb(
                $this->portal_db_config['username'],
                $this->portal_db_config['password'],
                $this->portal_db_config['database'],
                $this->portal_db_config['host']
            );
            
            // Set charset
            $this->portal_db->query("SET NAMES 'utf8mb4'");
            
            if ($this->portal_db->last_error) {
                throw new Exception('Database connection failed: ' . $this->portal_db->last_error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('ACSES User Sync - Database connection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user from portal database
     */
    public function get_portal_user($username) {
        if (!$this->portal_db) {
            if (!$this->connect()) {
                return null;
            }
        }

        $table_name = $this->get_portal_table_name();
        if (!$table_name) {
            return null;
        }

        $query = $this->portal_db->prepare(
            "SELECT * FROM {$table_name} WHERE username = %s",
            $username
        );

        return $this->portal_db->get_row($query);
    }

    /**
     * Get all users from portal database
     */
    public function get_all_portal_users() {
        if (!$this->portal_db) {
            if (!$this->connect()) {
                return array();
            }
        }

        $table_name = $this->get_portal_table_name();
        if (!$table_name) {
            return array();
        }

        $query = "SELECT * FROM {$table_name} ORDER BY username ASC";
        
        return $this->portal_db->get_results($query);
    }

    /**
     * Update user in portal database
     */
    public function update_portal_user($user_id, $data) {
        if (!$this->portal_db) {
            if (!$this->connect()) {
                return false;
            }
        }

        $table_name = $this->get_portal_table_name();
        if (!$table_name) {
            return false;
        }
        return $this->portal_db->update(
            $table_name,
            $data,
            array('id' => $user_id)
        );
    }

    /**
     * Create user in portal database
     */
    public function create_portal_user($data) {
        if (!$this->portal_db) {
            if (!$this->connect()) {
                return false;
            }
        }

        $table_name = $this->get_portal_table_name();
        if (!$table_name) {
            return false;
        }
        return $this->portal_db->insert($table_name, $data);
    }

    private function get_portal_table_name() {
        $table_name = isset($this->options['portal_table']) ? $this->options['portal_table'] : 'users';

        return preg_match('/^[A-Za-z0-9_]+$/', $table_name) ? $table_name : false;
    }

    /**
     * Get portal website URL
     */
    public function get_portal_url() {
        return $this->options['portal_link'];
    }
} 
