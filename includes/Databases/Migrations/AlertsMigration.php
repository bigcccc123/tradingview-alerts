<?php
/**
 * Migrations: AlertsMigration class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\Databases\Migrations;

use Dearvn\Tradingview_Alerts\Abstracts\DBMigrator;

/**
 * Email template table Migration class.
 */
class AlertsMigration extends DBMigrator {

	/**
	 * Migrate the cp_emails table.
	 *
	 * @since CAR_PULSE_SINCE
	 *
	 * @return void
	 */
	public static function migrate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

        $schema_jobs = "CREATE TABLE IF NOT EXISTS `{$wpdb->td_alerts}` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `ticker` varchar(255) NOT NULL,
            `type` varchar(255) NOT NULL,
            `interval` tinyint(1) unsigned NOT NULL,
            `exchange` varchar(255) NOT NULL,
            `time` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate";

		// Create the tables.
		dbDelta( $schema_alerts );
	}
}
