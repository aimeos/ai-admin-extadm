<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2010
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Order\Base\Coupon;


/**
 * ExtJS order base coupon controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard extends \Aimeos\Controller\ExtJS\Base implements \Aimeos\Controller\ExtJS\Iface
{
	private $manager = null;


	/**
	 * Initializes the Order base coupon controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Order_Base_Coupon' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base/coupon' );
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
		return 'order.base.coupon';
	}
}
