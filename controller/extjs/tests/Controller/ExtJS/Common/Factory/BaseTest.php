<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\ExtJS\Common\Factory;


/**
 * Test class for \Aimeos\Controller\ExtJS\Common\Factory\BaseTest.
 */
class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperExtjs::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', [] );
		$config->set( 'controller/extjs/admin/log/decorators/global', [] );
		$config->set( 'controller/extjs/admin/log/decorators/local', [] );

	}


	public function testInjectController()
	{
		$controller = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context, 'Standard' );
		\Aimeos\Controller\ExtJS\Admin\Job\Factory::injectController( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\Standard', $controller );

		$injectedController = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context, 'Standard' );

		$this->assertSame( $controller, $injectedController );
	}


	public function testInjectControllerReset()
	{
		$controller = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context, 'Standard' );
		\Aimeos\Controller\ExtJS\Admin\Job\Factory::injectController( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\Standard', $controller );
		\Aimeos\Controller\ExtJS\Admin\Job\Factory::injectController( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\Standard', null );

		$new = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context, 'Standard' );

		$this->assertNotSame( $controller, $new );
	}

}