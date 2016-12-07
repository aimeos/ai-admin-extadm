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
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Iface
	extends \Aimeos\Controller\ExtJS\Iface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param \Aimeos\Controller\ExtJS\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\Controller\ExtJS\Iface $controller, \Aimeos\MShop\Context\Item\Iface $context );
}