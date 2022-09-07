<?php
/**
 * Setup: Installer class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\Setup;

use Dearvn\Tradingview_Alerts\Common\Keys;

/**
 * Class Installer.
 *
 * Install necessary database tables and options for the plugin.
 */
class Installer {

	/**
	 * Run the installer.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function run(): void {
		// Update the installed version.
		$this->add_version();

		// Register and create tables.
		$this->register_table_names();
		$this->create_tables();

		// Run the database seeders.
		$seeder = new \Dearvn\Tradingview_Alerts\Databases\Seeder\Manager();
		$seeder->run();
	}

	/**
	 * Register table names.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function register_table_names(): void {
		global $wpdb;

		// Register the tables to wpdb global.
		$wpdb->td_alert_job_types = $wpdb->prefix . 'td_alert_job_types';
		$wpdb->td_alert_jobs      = $wpdb->prefix . 'td_alert_jobs';
	}

	/**
	 * Add time and version on DB.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function add_version(): void {
		$installed = get_option( Keys::TD_ALERT_INSTALLED );

		if ( ! $installed ) {
			update_option( Keys::TD_ALERT_INSTALLED, time() );
		}

		update_option( Keys::TD_ALERT_VERSION, 1.0 );
	}

	/**
	 * Create necessary database tables.
	 *
	 * @since TD_ALERT_
	 *
	 * @return void
	 */
	public function create_tables() {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// Run the database table migrations.
		\Dearvn\Tradingview_Alerts\Databases\Migrations\AlertsMigration::migrate();
	}
}
