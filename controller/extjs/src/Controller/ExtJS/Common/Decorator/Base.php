<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Common\Decorator;


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Base
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Decorator\Iface
{
	private $context = null;
	private $controller = null;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param \Aimeos\Controller\ExtJS\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\Controller\ExtJS\Iface $controller, \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
		$this->controller = $controller;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Controller\ExtJS\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		return @call_user_func_array( array( $this->controller, $name ), $param );
	}


	/**
	 * Deletes a list of an items.
	 *
	 * @param \stdClass $params Associative array containing the required values
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->controller->deleteItems( $params );
	}


	/**
	 * Creates a new item or updates an existing one or a list thereof.
	 *
	 * @param \stdClass $params Associative array containing the required values
	 */
	public function saveItems( \stdClass $params )
	{
		return $this->controller->saveItems( $params );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties and total number of items
	 */
	public function searchItems( \stdClass $params )
	{
		return $this->controller->searchItems( $params );
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return $this->controller->getServiceDescription();
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including
	 * "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		return $this->controller->getItemSchema();
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description",
	 * "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		return $this->controller->getSearchSchema();
	}

}