<?php
/**
 * Assets: Manager class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\Assets;

/**
 * Asset Manager class.
 *
 * Responsible for managing all of the assets (CSS, JS, Images, Locales).
 */
class Manager {

	/**
	 * Constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_all_scripts' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register all scripts and styles.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function register_all_scripts() {
		$this->register_styles( $this->get_styles() );
		$this->register_scripts( $this->get_scripts() );
	}

	/**
	 * Get all styles.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_styles(): array {
		return array(
			'tradingview-alerts-custom-css' => array(
				'src'     => TD_ALERT_BUILD . '/style-index.css',
				'version' => filemtime( TD_ALERT_DIR . '/build/style-index.css' ),
				'deps'    => array(),
			),
			'tradingview-alerts-css'        => array(
				'src'     => TD_ALERT_BUILD . '/index.css',
				'version' => filemtime( TD_ALERT_DIR . '/build/index.css' ),
				'deps'    => array( 'tradingview-alerts-custom-css' ),
			),
		);
	}

	/**
	 * Get all scripts.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_scripts(): array {
		$dependency = require_once TD_ALERT_DIR . '/build/index.asset.php';

		return array(
			'tradingview-alerts-app' => array(
				'src'       => TD_ALERT_BUILD . '/index.js',
				'version'   => filemtime( TD_ALERT_DIR . '/build/index.js' ),
				'deps'      => $dependency['dependencies'],
				'in_footer' => true,
			),
		);
	}

	/**
	 * Register styles.
	 *
	 * @since 0.0.1
	 * @param array $styles input value as array.
	 * @return void
	 */
	public function register_styles( array $styles ) {
		foreach ( $styles as $handle => $style ) {
			wp_register_style( $handle, $style['src'], $style['deps'], $style['version'] );
		}
	}

	/**
	 * Register scripts.
	 *
	 * @since 0.0.1
	 * @param array $scripts input value as array.
	 *
	 * @return void
	 */
	public function register_scripts( array $scripts ) {
		foreach ( $scripts as $handle => $script ) {
			wp_register_script( $handle, $script['src'], $script['deps'], $script['version'], $script['in_footer'] );
		}
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * @since 0.0.1
	 * @since 0.0.1 Loads the JS and CSS only on the Tradingview Alerts admin page.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		// Check if we are on the admin page and page=tradingview_alerts.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'tradingview_alerts' ) ) {
			return;
		}

		wp_enqueue_style( 'tradingview-alerts-css' );
		wp_enqueue_script( 'tradingview-alerts-app' );
		wp_localize_script('tradingview-alerts-app', 'wpApiSettings', [
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		]);
	}
}
