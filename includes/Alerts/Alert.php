<?php
/**
 * Alerts: Alert class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\Alerts;

use Dearvn\Tradingview_Alerts\Abstracts\BaseModel;

/**
 * Alert class.
 *
 * @since 0.0.1
 */
class Alert extends BaseModel {

	/**
	 * Table Name.
	 *
	 * @var string
	 */
	protected $table = 'td_alerts';

	/**
	 * Prepare datasets for database operation.
	 *
	 * @since 0.0.1
	 *
	 * @param array $data input value.
	 * @return array
	 */
	public function prepare_for_database( array $data ): array {
		$defaults = array(
			'name'		=> '',
			'close'		=> '',
			'type'		=> '',
			'interval'	=> '',
			'ticker'	=> '',
			'exchange'	=> '',
			'time'		=> ''
		);

		$data = wp_parse_args( $data, $defaults );

		// Sanitize template data.
		return array(
			'name'       => $this->sanitize( $data['name'], 'text' ),
			'close'        => $this->sanitize( $data['close'], 'number' ),
			'ticker' => $this->sanitize( $data['ticker'], 'ticker' ),
			'exchange' => $this->sanitize( $data['exchange'], 'exchange' ),
			'type' => $this->sanitize( $data['type'], 'type' ),
			'interval'  => $this->sanitize( $data['interval'], 'number' ),
			'time'  => $this->sanitize( $data['time'], 'text' )
		);
	}

	/**
	 * Alerts item to a formatted array.
	 *
	 * @since 0.0.1
	 *
	 * @param object $alert input value.
	 *
	 * @return array
	 */
	public static function to_array( ?object $alert ): array {
		$data = array(
			'id'          => (int) $alert->id,
			'name'        => $alert->name,
			'ticker'      => $alert->ticker,
			'exchange'    => $alert->exchange,
			'interval'    => $alert->interval,
			'close'   	  => $alert->close,
			'time'        => $alert->time,
		);

		return $data;
	}

}
