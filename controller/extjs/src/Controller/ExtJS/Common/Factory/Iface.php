<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Common\Factory;


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Iface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return \Aimeos\Controller\ExtJS\Iface ExtJS controller object
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null );
}