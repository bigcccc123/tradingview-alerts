<?php
/**
 * Seeder: Manager class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\Databases\Seeder;

/**
 * Database Seeder class.
 *
 * It'll seed all of the seeders.
 */
class Manager {

	/**
	 * Run the database seeders.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 * @throws \Exception Return error.
	 */
	public function run() {
		$seeder_classes = array(
			\Dearvn\Tradingview_Alerts\Databases\Seeder\AlertsSeeder::class,
		);

		foreach ( $seeder_classes as $seeder_class ) {
			$seeder = new $seeder_class();
			$seeder->run();
		}
	}
}
