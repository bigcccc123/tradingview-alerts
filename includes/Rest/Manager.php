<?php
/**
 * REST: Manager class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\REST;

/**
 * API Manager class.
 *
 * All API classes would be registered here.
 *
 * @since 0.0.1
 */
class Manager {

	/**
	 * Class dir and class name mapping.
	 *
	 * @var array
	 *
	 * @since 0.0.1
	 */
	protected $class_map;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! class_exists( 'WP_REST_Server' ) ) {
			return;
		}

		$this->class_map = apply_filters(
			'td_alert_rest_api_class_map',
			array(
				TD_ALERT_DIR . '/includes/REST/AlertTypesController.php' => 'Dearvn\Tradingview_Alerts\REST\AlertTypesController',
				TD_ALERT_DIR . '/includes/REST/AlertsController.php' => 'Dearvn\Tradingview_Alerts\REST\AlertsController',
			)
		);

		// Init REST API routes.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		foreach ( $this->class_map as $file_name => $controller ) {
			require_once $file_name;
			$this->$controller = new $controller();
			$this->$controller->register_routes();
		}
	}
}
