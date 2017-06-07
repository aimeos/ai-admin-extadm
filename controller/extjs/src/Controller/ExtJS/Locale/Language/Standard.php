<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Locale\Language;


/**
 * ExtJS language controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Iface
{
	private $manager = null;


	/**
	 * Initializes the language controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Locale_Language' );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'items' ) );

		foreach( (array) $params->items as $id ) {
			$this->getManager()->deleteItem( $id );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Creates a new language item or updates an existing one or a list thereof.
	 *
	 * @param \stdClass $params Associative array containing the product properties
	 */
	public function saveItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'items' ) );

		$ids = [];
		$manager = $this->getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->transformValues( $entry ) );
			$item = $manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		return $this->getItems( $ids, $this->getPrefix() );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( \stdClass $params )
	{
		$manager = $this->getManager();

		$total = 0;
		$search = $manager->createSearch();

		if( isset( $params->options->showall ) && $params->options->showall == false )
		{
			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->getContext() );

			$langids = [];
			foreach( $localeManager->searchItems( $localeManager->createSearch() ) as $item ) {
				$langids[] = $item->getLanguageId();
			}

			if( !empty( $langids ) ) {
				$search->setConditions( $search->compare( '==', 'locale.language.id', $langids ) );
			}
		}

		$search = $this->initCriteria( $search, $params );

		$sort = $search->getSortations();
		$sort[] = $search->sort( '+', 'locale.language.label' );
		$search->setSortations( $sort );

		$items = $this->getManager()->searchItems( $search, [], $total );

		return array(
			'items' => $this->toArray( $items ),
			'total' => $total,
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			'Locale_Language.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.saveItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.searchItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'locale/language' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'locale.language';
	}
}
