<?php
/**
 * Plugin Name:       WP Tradingview Alerts Plugin
 * Description:       A tradingview alerts platform made by WordPress.
 *
 * @package           Tradingview_Alerts
 * @author            Donald
 * @copyright         2022 Dev
 * @license           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.3
 * Version:           0.0.1
 * Author:            Donald<donald.nguyen.it@gmail.com>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tradingview-alerts
 */

 
// don't call the file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tradingview_Alerts class.
 *
 * @class Tradingview_Alerts The class that holds the entire Tradingview_Alerts plugin
 */
final class Tradingview_Alerts {
	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.0.1';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 *
	 * @since 0.0.1
	 */
	const SLUG = 'tradingview_alerts';

	/**
	 * Holds various class instances.
	 *
	 * @var array
	 *
	 * @since 0.0.1
	 */
	private $container = array();

	/**
	 * Constructor for the Tradingview_Alerts class.
	 *
	 * Sets up all the appropriate hooks and actions within our plugin.
	 *
	 * @since 0.0.1
	 */
	private function __construct() {
		require_once __DIR__ . '/vendor/autoload.php';
		
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'wp_loaded', array( $this, 'flush_rewrite_rules' ) );
		$this->init_plugin();
	}

	/**
	 * Initializes the Tradingview_Alerts() class.
	 *
	 * Checks for an existing Tradingview_Alerts() instance
	 * and if it doesn't find one, creates it.
	 *
	 * @since 0.0.1
	 *
	 * @return Tradingview_Alerts|bool
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new Tradingview_Alerts();
		}

		return $instance;
	}

	/**
	 * Magic getter to bypass referencing plugin.
	 *
	 * @since 0.0.1
	 *
	 * @param string $prop as property.
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return $this->container[ $prop ];
		}

		return $this->{$prop};
	}

	/**
	 * Magic isset to bypass referencing plugin.
	 *
	 * @since 0.0.1
	 *
	 * @param string $prop as property.
	 *
	 * @return mixed
	 */
	public function __isset( $prop ) {
		return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
	}

	/**
	 * Define the constants.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'TD_ALERT_VERSION', self::VERSION );
		define( 'TD_ALERT_SLUG', self::SLUG );
		define( 'TD_ALERT_FILE', __FILE__ );
		define( 'TD_ALERT_DIR', __DIR__ );
		define( 'TD_ALERT_PATH', dirname( TD_ALERT_FILE ) );
		define( 'TD_ALERT_INCLUDES', TD_ALERT_PATH . '/includes' );
		define( 'TD_ALERT_TEMPLATE_PATH', TD_ALERT_PATH . '/templates/' );
		define( 'TD_ALERT_URL', plugins_url( '', TD_ALERT_FILE ) );
		define( 'TD_ALERT_BUILD', TD_ALERT_URL . '/build' );
		define( 'TD_ALERT_ASSETS', TD_ALERT_URL . '/assets' );
	}

	/**
	 * Load the plugin after all plugins are loaded.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->includes();
		$this->init_hooks();

		/**
		 * Fires after the plugin is loaded.
		 *
		 * @since 0.0.1
		 */
		do_action( 'alert_place_loaded' );
	}

	/**
	 * Activating the plugin.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function activate() {
		// Run the installer to create necessary migrations and seeders.
		$this->install();
	}

	/**
	 * Placeholder for deactivation function.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function deactivate() {
	}

	/**
	 * Flush rewrite rules after plugin is activated.
	 *
	 * Nothing being added here yet.
	 *
	 * @since 0.0.1
	 */
	public function flush_rewrite_rules() {
		// fix rewrite rules.
	}

	/**
	 * Run the installer to create necessary migrations and seeders.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function install() {
		$installer = new Dearvn\Tradingview_Alerts\Setup\Installer();
		$installer->run();
	}

	/**
	 * Include the required files.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			$this->container['admin_menu'] = new Dearvn\Tradingview_Alerts\Admin\Menu();
		}
	}

	/**
	 * Initialize the hooks.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Init classes.
		add_action( 'init', array( $this, 'init_classes' ) );

		// Localize our plugin.
		add_action( 'init', array( $this, 'localization_setup' ) );

		// Add the plugin page links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Instantiate the required classes.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init_classes() {
		
		// Common classes.
		$this->container['assets']   = new Dearvn\Tradingview_Alerts\Assets\Manager();
		$this->container['rest_api'] = new Dearvn\Tradingview_Alerts\REST\Manager();
		$this->container['alerts']     = new Dearvn\Tradingview_Alerts\Alerts\Manager();
	}

	/**
	 * Initialize plugin for localization.
	 *
	 * @uses load_plugin_textdomain()
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'tradingview_alerts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load the React-pages translations.
		if ( is_admin() ) {
			// Check if handle is registered in wp-script.
			$this->container['assets']->register_all_scripts();

			// Load wp-script translation for tradingview-alerts-app.
			wp_set_script_translations( 'tradingview-alerts-app', 'tradingview_alerts', plugin_dir_path( __FILE__ ) . 'languages/' );
		}
	}

	/**
	 * What type of request is this.
	 *
	 * @since 0.0.1
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();

			case 'ajax':
				return defined( 'DOING_AJAX' );

			case 'rest':
				return defined( 'REST_REQUEST' );

			case 'cron':
				return defined( 'DOING_CRON' );

			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Plugin action links
	 *
	 * @param array $links link of action.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=tradingview_alerts#/settings' ) . '">' . __( 'Settings', 'tradingview_alerts' ) . '</a>';
		$links[] = '<a href="https://github.com/dearvn/tradingview-alerts#quick-start" target="_blank">' . __( 'Documentation', 'tradingview_alerts' ) . '</a>';

		return $links;
	}
}

/**
 * Initialize the main plugin.
 *
 * @since 0.0.1
 *
 * @return \Tradingview_Alerts|bool
 */
function tradingview_alerts() {
	return Tradingview_Alerts::init();
}

/*
 * Kick-off the plugin.
 *
 * @since 0.0.1
 */
tradingview_alerts();
