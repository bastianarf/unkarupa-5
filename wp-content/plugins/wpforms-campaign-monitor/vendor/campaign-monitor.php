<?php
/**
 * Campaign Monitor API Wrapper
 *
 * Designed to make working with the Campaign Monitor PHP library a bit easier,
 * and throws Exceptions when something goes wrong.
 *
 * @author Tim Carr
 * @version 1.0
 */
class Campaign_Monitor {

	/**
      *
     * Auth object
     *
     * @since 1.0.0
     */
    public $auth;

    /**
     * Client ID
     *
     * @since 1.0.0
     */
    public $client_id;

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @param string $api_key API Key
     */
    public function __construct( $api_key = '', $client_id = '' ) {

        // Validate inputs
        if ( empty( $api_key ) ) {
            throw new Exception( __( 'No API key specified.', 'wpforms-campaign-monitor' ) );
        }
        if ( empty( $client_id ) ) {
            throw new Exception( __( 'No Client ID specified.', 'wpforms-campaign-monitor' ) );
        }

        // Load required files
        if ( !class_exists( 'CS_REST_General' ) ) {
            require_once( 'campaignmonitor/csrest_general.php' );
        }

    	// Setup the general class
        $this->auth = array(
            'api_key' => $api_key
        );
        $general = new CS_REST_General( $this->auth );

        // Store the client ID
    	$this->client_id = $client_id;

    }

    /**
    * Gets all mailing lists for the class' client_id
    *
    * @since 1.0.0
    *
    * @return 	array  Lists
    */
    public function get_lists() {

    	// Load required files
        if ( !class_exists( 'CS_REST_Clients' ) ) {
            require_once( 'campaignmonitor/csrest_clients.php' );
        }

        // Get lists belonging to the given client ID
        $client = new CS_REST_Clients( $this->client_id, $this->auth );
        $result = $client->get_lists();

        // Check result was successful, throw an exception if not.
        if ( ! $result->was_successful() ) {
        	throw new Exception( __( 'Could not get lists.', 'wpforms-campaign-monitor' ) );
        }

        // Build an array of id/name key/value pairs
        $lists = array();
        foreach( $result->response as $list ) {
        	$lists[] = array(
        		'id' 	=> $list->ListID,
        		'name' 	=> $list->Name,
        	);
        }

        // Return the lists
        return $lists;

    }

    /**
     * Gets all custom fields for the given List ID
     *
     * @since 1.0.0
     *
     * @param 	string 	$list_id 	List ID
     * @return 	array 				Custom Fields
     */
    public function get_list_custom_fields( $list_id ) {

    	// Load required files
        if ( !class_exists( 'CS_REST_Lists' ) ) {
            require_once( 'campaignmonitor/csrest_lists.php' );
        }

        // Get custom fields for list
        $list = new CS_REST_Lists( $list_id, $this->auth );
        $result = $list->get_custom_fields();

        // Check result was successful, throw an exception if not.
        if ( ! $result->was_successful() ) {
        	throw new Exception( __( 'Could not get custom fields.', 'wpforms-campaign-monitor' ) );
        }

        // Build an array of id/name key/value pairs
        $fields = array();
        foreach( $result->response as $field ) {
        	$fields[] = array(
        		'name' 			=> $field->FieldName,
        		'req' 			=> false,
        		'tag' 			=> str_replace( ']', '', str_replace( '[', '', $field->Key ) ), // Strip square braces
        		'field_type' 	=> $field->DataType, // Text,Number,MultiSelectOne,MultiSelectMany,Date,Country,USState
        	);
        }

        // Return the fields
        return $fields;

    }

    /**
     * Subscribes a user to a list
     *
     * @since 1.0.0
     *
     * @param 	string 	$list_id 	List ID
     * @param 	array 	$data 		Subscriber Data
     */
    public function subscribe( $list_id, $data ) {

    	// Load required files
        if ( !class_exists( 'CS_REST_Subscribers' ) ) {
            require_once( 'campaignmonitor/csrest_subscribers.php' );
        }

        // Attempt to subscribe
    	$subscribers = new CS_REST_Subscribers( $list_id, $this->auth );
        $result = $subscribers->add( $data );

        // Check result was successful, throw an exception if not.
        if ( ! $result->was_successful() ) {
        	throw new Exception( $result->response->Message );
        }

    }

}