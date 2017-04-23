<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\ExtJS\Common\Decorator;


/**
 * Test class for \Aimeos\Controller\ExtJS\Common\Decorator\ExampleTest.
 */
class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperExtjs::getContext();

		$controller = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $context );
		$this->object = new \Aimeos\Controller\ExtJS\Common\Decorator\Example( $controller, $context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCall()
	{
		$this->object->additionalMethod();
	}


	public function testDeleteItems()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->deleteItems( new \stdClass() );
	}


	public function testSaveItems()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->saveItems( new \stdClass() );
	}


	public function testSearchItems()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->searchItems( new \stdClass() );
	}


	public function testGetServiceDescription()
	{
		$this->object->getServiceDescription();
	}


	public function testGetItemSchema()
	{
		$this->object->getItemSchema();
	}


	public function testGetSearchSchema()
	{
		$this->object->getSearchSchema();
	}

}