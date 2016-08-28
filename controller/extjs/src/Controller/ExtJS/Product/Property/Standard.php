<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Product\Property;


/**
 * ExtJS product property controller for admin interfaces.
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
	 * Initializes the product property controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Product_Property' );
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

		$properties['product.property.type'] = array(
			'description' => 'Product property type code',
			'optional' => false,
			'type' => 'string',
		);
		$properties['product.property.typename'] = array(
			'description' => 'Product property type name',
			'optional' => false,
			'type' => 'string',
		);

		return array(
			'name' => 'Product_Property',
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
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/property' );
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
		return 'product.property';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'product.property.languageid'} ) && $entry->{'product.property.languageid'} === '' ) {
			$entry->{'product.property.languageid'} = null;
		}

		return $entry;
	}
}
