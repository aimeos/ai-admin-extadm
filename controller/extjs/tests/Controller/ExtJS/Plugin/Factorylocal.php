<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Plugin;


/**
 * ExtJS plugin test factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Factorylocal
	extends \Aimeos\Controller\ExtJS\Common\Factory\Base
{
	/**
	 * @param string $name
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null, $domainToTest = 'plugin' )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'controller/extjs/plugin/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
		}

		$iface = '\\Aimeos\\Controller\\ExtJS\\Common\\Iface';
		$classname = '\\Aimeos\\Controller\\ExtJS\\Plugin\\' . $name;

		$manager = self::createControllerBase( $context, $classname, $iface );
		return self::addControllerDecorators( $context, $manager, $domainToTest );
	}
}
