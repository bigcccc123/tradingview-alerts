<?php
/**
 * REST: AlertsController class
 *
 * @package    Tradingview_Alerts
 * @since      5.8
 */

namespace Dearvn\Tradingview_Alerts\REST;

use Dearvn\Tradingview_Alerts\Abstracts\RESTController;
use Dearvn\Tradingview_Alerts\Alerts\Alert;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

/**
 * API OrdersController class.
 *
 * @since 0.0.1
 */
class OrdersController extends RESTController {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $base = 'alerts';

	/**
	 * Register all routes related with carts.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->get_collection_params(),
					'schema'              => array( $this, 'get_item_schema' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_items' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'ids' => array(
							'type'        => 'array',
							'default'     => array(),
							'description' => __( 'Post IDs which will be deleted.', 'tradingview_alerts' ),
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[a-zA-Z0-9-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
			)
		);
	}

	/**
	 * Retrieves a collection of alert items.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ): WP_REST_Response {
		$args   = array();
		$data   = array();
		$params = $this->get_collection_params();

		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		$alerts = tradingview_alerts()->alerts->all( $args );
		foreach ( $alerts as $alert ) {
			$response = $this->prepare_item_for_response( $alert, $request );
			$data[]   = $this->prepare_response_for_collection( $response );
		}

		$args['count'] = 1;
		$total         = tradingview_alerts()->alerts->all( $args );
		$max_pages     = ceil( $total / (int) $args['limit'] );
		$response      = rest_ensure_response( $data );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return $response;
	}

	/**
	 * Retrieves a collection of alert items.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		if ( is_numeric( $request['id'] ) ) {
			$args = array(
				'key'   => 'id',
				'value' => absint( $request['id'] ),
			);
		} else {
			$args = array(
				'key'   => 'slug',
				'value' => sanitize_text_field( wp_unslash( $request['id'] ) ),
			);
		}

		$alert = tradingview_alerts()->alerts->get( $args );

		if ( ! $alert ) {
			return new WP_Error( 'td_alert_rest_alet_not_found', __( 'Alert not found. May be alert has been deleted or you don\'t have access to that.', 'tradingview_alerts' ), array( 'status' => 404 ) );
		}

		// Prepare response.
		$alert = $this->prepare_item_for_response( $alert, $request );

		return rest_ensure_response( $alert );
	}

	/**
	 * Create new alert.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Rest_Request $request input value.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error(
				'td_alert_rest_email_template_exists',
				__( 'Cannot create existing email template.', 'tradingview_alerts' ),
				array( 'status' => 400 )
			);
		}

		$prepared_data = $this->prepare_item_for_database( $request );


		if ( is_wp_error( $prepared_data ) ) {
			return $prepared_data;
		}

		// Insert the alert.
		$alert_id = tradingview_alerts()->alerts->create( $prepared_data );

		if ( is_wp_error( $alert_id ) ) {
			return $alert_id;
		}

		// Get alert after insert to sending response.
		$alert = tradingview_alerts()->alerts->get(
			array(
				'key'   => 'id',
				'value' => $alert_id,
			)
		);

		$response = $this->prepare_item_for_response( $alert, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $alert_id ) ) );

		return $response;
	}

	/**
	 * Update a alert.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Rest_Request $request input value.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		if ( empty( $request['id'] ) ) {
			return new WP_Error(
				'td_alert_rest_email_template_exists',
				__( 'Invalid Alert ID.', 'tradingview_alerts' ),
				array( 'status' => 400 )
			);
		}

		$prepared_data = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_data ) ) {
			return $prepared_data;
		}

		// Update the alert.
		$alert_id = absint( $request['id'] );
		$alert_id = tradingview_alerts()->alerts->update( $prepared_data, $alert_id );

		if ( is_wp_error( $alert_id ) ) {
			return $alert_id;
		}

		// Get alert after insert to sending response.
		$alert = tradingview_alerts()->alerts->get(
			array(
				'key'   => 'id',
				'value' => $alert_id,
			)
		);

		$response = $this->prepare_item_for_response( $alert, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $alert_id ) ) );

		return $response;
	}

	/**
	 * Delete single or multiple alerts.
	 *
	 * @since 0.0.1
	 *
	 * @param array $request input value.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_items( $request ) {
		if ( ! isset( $request['ids'] ) ) {
			return new WP_Error( 'no_ids', __( 'No alert ids found.', 'tradingview_alerts' ), array( 'status' => 400 ) );
		}

		$deleted = tradingview_alerts()->alerts->delete( $request['ids'] );

		if ( $deleted ) {
			$message = __( 'Alerts deleted successfully.', 'tradingview_alerts' );

			return rest_ensure_response(
				array(
					'message' => $message,
					'total'   => $deleted,
				)
			);
		}

		return new WP_Error( 'no_alert_deleted', __( 'No alert deleted. Alert has already been deleted. Please try again.', 'tradingview_alerts' ), array( 'status' => 400 ) );
	}

	/**
	 * Retrieves the group schema, conforming to JSON Schema.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'alert',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'ID of the alert', 'tradingview_alerts' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'       => array(
					'description' => __( 'Alert title', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'minLength'   => 1,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'slug'        => array(
					'description' => __( 'Alert slug', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'minLength'   => 1,
					'arg_options' => array(
						'sanitize_callback' => array( $this, 'sanitize_alert_slug' ),
					),
				),
				'description' => array(
					'description' => __( 'Alert description', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'minLength'   => 1,
				),
				'alert_type_id' => array(
					'description' => __( 'Alert type', 'tradingview_alerts' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'company_id'  => array(
					'description' => __( 'Company', 'tradingview_alerts' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'is_active'   => array(
					'description' => __( 'Alert status', 'tradingview_alerts' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'created_by'  => array(
					'description' => __( 'Created by user', 'tradingview_alerts' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'updated_by'  => array(
					'description' => __( 'Updated by user', 'tradingview_alerts' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'created_at'  => array(
					'description' => __( 'Created at time', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'format'      => 'date-time',
					'readonly'    => true,
				),
				'updated_at'  => array(
					'description' => __( 'Updated at time', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'format'      => 'date-time',
					'readonly'    => true,
				),
				'deleted_at'  => array(
					'description' => __( 'Deleted at time', 'tradingview_alerts' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'format'      => 'date-time',
					'readonly'    => true,
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Prepares a single email template for create or update.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return object|WP_Error
	 */
	protected function prepare_item_for_database( $request ) {
		$data                = array();
		$data['title']       = $request['title'];
		$data['slug']        = $this->generate_unique_slug( $request );
		$data['description'] = $request['description'];
		$data['company_id']  = $request['company_id'];
		$data['is_active']   = $request['is_active'];
		$data['alert_type_id'] = $request['alert_type_id'];

		if ( empty( $request['id'] ) ) {
			$data['created_by'] = empty( $request['created_by'] ) ? get_current_user_id() : absint( $request['created_by'] );
			$data['created_at'] = empty( $request['created_at'] ) ? current_datetime()->format( 'Y-m-d H:i:s' ) : $request['created_at'];
		} else {
			$data['updated_by'] = empty( $request['updated_by'] ) ? get_current_user_id() : absint( $request['updated_by'] );
			$data['updated_at'] = empty( $request['updated_at'] ) ? current_datetime()->format( 'Y-m-d H:i:s' ) : $request['updated_at'];
		}

		return $data;
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @since 0.0.1
	 *
	 * @param Alert             $item    WordPress representation of the item.
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array();

		$data = Alert::to_array( $item );

		$data = $this->prepare_response_for_collection( $data );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		return $response;
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Post $item post object.
	 *
	 * @return array links for the given data.
	 */
	protected function prepare_links( $item ): array {
		$base = sprintf( '%s/%s%s', $this->namespace, $this->rest_base, $this->base );

		$id = is_object( $item ) ? $item->id : $item['id'];

		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Sanitize alert slug for uniqueness.
	 *
	 * @since 0.0.1
	 *
	 * @param string          $slug input value.
	 * @param WP_REST_Request $request input value.
	 *
	 * @return WP_Error|string
	 */
	public function sanitize_alert_slug( $slug, $request ) {
		global $wpdb;

		$slug          = sanitize_title( $slug );
		$id            = isset( $request['id'] ) ? $request['id'] : 0;
		$args['count'] = 1;

		if ( ! empty( $id ) ) {
			$args['where'][] = $wpdb->prepare( 'id != %d AND slug = %s', $id, $slug );
		} else {
			$args['where'][] = $wpdb->prepare( 'slug = %s', $slug );
		}

		$total_found = tradingview_alerts()->alerts->all( $args );

		if ( $total_found > 0 ) {
			return new WP_Error(
				'alert_place_rest_slug_exists',
				__( 'Alert slug already exists.', 'tradingview_alerts' ),
				array(
					'status' => 400,
				)
			);
		}

		return sanitize_title( $slug );
	}

	/**
	 * Generate unique slug if no slug is provided.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Request $request input value.
	 *
	 * @return string
	 */
	public function generate_unique_slug( WP_REST_Request $request ) {
		$slug = $request['slug'];

		if ( empty( $slug ) ) {
			$slug = sanitize_title( $request['title'] );
			$slug = str_replace( ' ', '-', $slug );

			// Auto-generate only for create page.
			if ( empty( $request['id'] ) ) {
				$existing_alert = tradingview_alerts()->alerts->get(
					array(
						'key'   => 'slug',
						'value' => $slug,
					)
				);

				// If error, means, there is no slug by this slug.
				if ( empty( $existing_alert ) ) {
					return $slug;
				}

				return $slug . '-' . time();
			}
		}

		return $slug;
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_collection_params(): array {
		$params = parent::get_collection_params();

		$params['limit']['default'] = 10;
		$params['s']['default']     = '';

		return $params;
	}
}
