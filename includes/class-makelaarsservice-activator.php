<?php

/**
 * Fired during plugin activation
 *
 * @link       https://penthion.nl
 * @since      1.0.0
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/includes
 * @author     Penthion <dd@penthion.nl>
 */
class Makelaarsservice_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		global $wpdb;

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-makelaarsservice-admin.php';
		$plugin_admin = new Makelaarsservice_Admin( 'makelaarsservice', '1.0.0' );


        // Insert new database table for Makelaarsservice tokens
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'pnt_makelaarsservice_tokens';
        $sql = "CREATE TABLE  `$table_name` (
			`token_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(220) DEFAULT NULL,
			`token` varchar(255) DEFAULT NULL,
			`agent_id` int(11) DEFAULT NULL,
			`updated_at` timestamp,
			`is_active`	tinyint(1) DEFAULT NULL,
			PRIMARY KEY(token_id) 
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
		}

		set_transient( '_welcome_screen_activation_redirect', true, 30 );

		$plugin_admin->add_cpt();
		flush_rewrite_rules();
	}

}
