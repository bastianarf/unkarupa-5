<?php

namespace WPFormsGetResponse\Provider;

use RuntimeException;
use Getresponse\Sdk\Operation\Model\NewTag;
use Getresponse\Sdk\GetresponseClientFactory;
use Getresponse\Sdk\Operation\Model\NewContact;
use Getresponse\Sdk\Client\Operation\Pagination;
use Getresponse\Sdk\Operation\Model\NewContactTag;
use Getresponse\Sdk\Operation\Tags\GetTags\GetTags;
use Getresponse\Sdk\Client\Operation\QueryOperation;
use Getresponse\Sdk\Operation\Model\CampaignReference;
use Getresponse\Sdk\Operation\Tags\CreateTag\CreateTag;
use Getresponse\Sdk\Operation\Model\NewContactCustomFieldValue;
use Getresponse\Sdk\Operation\Accounts\GetAccounts\GetAccounts;
use Getresponse\Sdk\Operation\Campaigns\GetCampaigns\GetCampaigns;
use Getresponse\Sdk\Operation\Contacts\CreateContact\CreateContact;
use Getresponse\Sdk\Operation\Contacts\UpdateContact\UpdateContact;
use Getresponse\Sdk\Operation\Contacts\DeleteContact\DeleteContact;
use Getresponse\Sdk\Operation\Model\SearchContactsConditionsDetails;
use Getresponse\Sdk\Operation\Model\UpdateContact as ModelUpdateContact;
use Getresponse\Sdk\Operation\CustomFields\GetCustomFields\GetCustomFields;
use Getresponse\Sdk\Operation\SearchContacts\Contacts\GetContactsBySearchContactsConditions\GetContactsBySearchContactsConditions;

/**
 * Class Api which extends 3rd party GetResponse library to provide more WPForms related things.
 *
 * @since 1.3.0
 */
class Api {

	/**
	 * API key used to sign all requests.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private $apikey;

	/**
	 * Number of records to return.
	 * By default API returns 100. We raise it to 1000.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const LIMIT = 1000;

	/**
	 * API constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param string $apikey APi key.
	 *
	 * @throws RuntimeException When no cURL available or error while connecting.
	 */
	public function __construct( $apikey ) {

		if ( ! function_exists( 'curl_version' ) ) {
			throw new RuntimeException( esc_html__( "cURL support is required, but can't be found.", 'wpforms-getresponse' ), 1 );
		}

		$this->apikey = $apikey;
	}

	/**
	 * Add a new tag.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Tag name.
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function create_tag( $name ) {

		return $this->client()
					->call( new CreateTag( new NewTag( $name ) ) );
	}

	/**
	 * Create a new contact.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args Arguments.
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function create_contact( array $args ) {

		$new_contact = new NewContact(
			new CampaignReference( $args['campaignId'] ),
			$args['email']
		);

		// Do not pass a default value if we create an contact (default value for `dayOfCycle` works in update endpoint).
		if ( $args['dayOfCycle'] !== 'null' ) {
			$new_contact->setDayOfCycle( $args['dayOfCycle'] );
		}

		$data = $this->set_contact_props( $new_contact, $args );

		return $this->client()
					->call( new CreateContact( $data ) );
	}

	/**
	 * Update contact details.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $args       Arguments.
	 * @param string $contact_id Contact ID.
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function update_contact( array $args, $contact_id ) {

		$update_contact = new ModelUpdateContact();
		$update_contact->setDayOfCycle( $args['dayOfCycle'] );

		$data = $this->set_contact_props( $update_contact, $args );

		return $this->client()
					->call( new UpdateContact( $data, $contact_id ) );
	}

	/**
	 * Delete a contact by ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $contact_id Contact ID.
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function delete_contact( $contact_id ) {

		return $this->client()
					->call( new DeleteContact( $contact_id ) );
	}

	/**
	 * Set contact properties.
	 *
	 * @since 1.3.0
	 *
	 * @param object $model Modal instance.
	 * @param array  $args  Arguments.
	 *
	 * @return object
	 */
	protected function set_contact_props( $model, $args ) {

		if ( ! empty( $args['name'] ) ) {
			$model->setName( $args['name'] );
		}

		$model->setTags(
			array_map(
				static function( $tag_id ) {

					return new NewContactTag( $tag_id );
				},
				$args['tags']
			)
		);

		$model->setCustomFieldValues(
			array_map(
				static function( $field ) {

					return new NewContactCustomFieldValue( $field['id'], $field['value'] );
				},
				$args['customFieldValues']
			)
		);

		return $model;
	}

	/**
	 * Search a contact by email in passed lists.
	 *
	 * @link https://apidocs.getresponse.com/v3/case-study/search-contacts-guide
	 *
	 * @since 1.3.0
	 *
	 * @param string $email             Contact email.
	 * @param array  $campaign_ids_list List Ids.
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function search_contact( $email, $campaign_ids_list ) {

		$search_conditions = new SearchContactsConditionsDetails(
			[ 'subscribed' ],
			'and',
			[
				'campaignIdsList'  => $campaign_ids_list,
				'subscriberCycle'  => [
					'receiving_autoresponder',
					'not_receiving_autoresponder',
				],
				'subscriptionDate' => 'all_time',
				'logicOperator'    => 'and',
				'conditions'       => [
					[
						'conditionType' => 'email',
						'operatorType'  => 'string_operator',
						'operator'      => 'is',
						'value'         => $email,
					],
				],
			]
		);

		return $this->client()
					->call( new GetContactsBySearchContactsConditions( $search_conditions ) );
	}

	/**
	 * Change a number of records to return.
	 *
	 * @since 1.3.0
	 *
	 * @param QueryOperation $operation Operation instance.
	 *
	 * @return QueryOperation
	 */
	protected function set_pagination_max( $operation ) {

		return $operation->setPagination( new Pagination( 1, self::LIMIT ) );
	}

	/**
	 * Retrieve a client instance.
	 *
	 * @since 1.3.0
	 *
	 * @return \Getresponse\Sdk\Client\GetresponseClient
	 */
	public function client() {

		return GetresponseClientFactory::createWithApiKey( $this->apikey );
	}

	/**
	 * Retrieve a response with current account.
	 *
	 * @since 1.3.0
	 *
	 * @return \Getresponse\Sdk\Client\Operation\OperationResponse
	 */
	public function get_account() {

		return $this->client()
					->call( new GetAccounts() );
	}

	/**
	 * Retrieve data with GR lists.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	public function get_data_lists() {

		$response = $this->client()->call( $this->set_pagination_max( new GetCampaigns() ) );
		$data     = $response->getData();

		if (
			empty( $data ) ||
			! $response->isSuccess() ||
			$response->getResponse()->getStatusCode() !== 200
		) {
			return [];
		}

		return $data;
	}

	/**
	 * Retrieve data with GR tags.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	public function get_data_tags() {

		$response = $this->client()->call( $this->set_pagination_max( new GetTags() ) );
		$data     = $response->getData();

		if (
			empty( $data ) ||
			! $response->isSuccess() ||
			$response->getResponse()->getStatusCode() !== 200
		) {
			return [];
		}

		return $data;
	}

	/**
	 * Retrieve data with GR custom fields.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	public function get_data_custom_fields() {

		$response = $this->client()->call( $this->set_pagination_max( new GetCustomFields() ) );
		$data     = $response->getData();

		if (
			empty( $data ) ||
			! $response->isSuccess() ||
			$response->getResponse()->getStatusCode() !== 200
		) {
			return [];
		}

		return $data;
	}
}
