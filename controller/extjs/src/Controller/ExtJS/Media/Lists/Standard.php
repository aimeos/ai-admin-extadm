<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Media\Lists;


/**
 * ExtJS media list controller for admin interfaces.
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
	 * Initializes the media list controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Media_Lists' );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$totalList = 0;
		$search = $this->initCriteria( $this->getManager()->createSearch(), $params );
		$result = $this->getManager()->searchItems( $search, [], $totalList );

		$idLists = [];
		$listItems = [];

		foreach( $result as $item )
		{
			if( ( $domain = $item->getDomain() ) != '' ) {
				$idLists[$domain][] = $item->getRefId();
			}
			$listItems[] = (object) $item->toArray( true );
		}

		return array(
			'items' => $listItems,
			'total' => $totalList,
			'graph' => $this->getDomainItems( $idLists ),
			'success' => true,
		);
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		$attributes = $this->getManager()->getSearchAttributes( false );
		$properties = $this->getAttributeSchema( $attributes );

		$properties['media.lists.type'] = array(
			'description' => 'Media list type code',
			'optional' => false,
			'type' => 'string',
		);
		$properties['media.lists.typename'] = array(
			'description' => 'Media list type name',
			'optional' => false,
			'type' => 'string',
		);

		return array(
			'name' => 'Media_Lists',
			'properties' => $properties,
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
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'media/lists' );
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
		return 'media.lists';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'media.lists.datestart'} ) && $entry->{'media.lists.datestart'} != '' ) {
			$entry->{'media.lists.datestart'} = str_replace( 'T', ' ', $entry->{'media.lists.datestart'} );
		} else {
			$entry->{'media.lists.datestart'} = null;
		}

		if( isset( $entry->{'media.lists.dateend'} ) && $entry->{'media.lists.dateend'} != '' ) {
			$entry->{'media.lists.dateend'} = str_replace( 'T', ' ', $entry->{'media.lists.dateend'} );
		} else {
			$entry->{'media.lists.dateend'} = null;
		}

		if( isset( $entry->{'media.lists.config'} ) ) {
			$entry->{'media.lists.config'} = (array) $entry->{'media.lists.config'};
		}

		return $entry;
	}
}
