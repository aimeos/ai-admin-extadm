<?php

namespace Aimeos\Controller\ExtJS\Order\Base\Service;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Order\Base\Service\Standard( \TestHelperExtjs::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'order.base.service.type' => 'delivery' ) ), 1 => array( '==' => (object) array( 'order.base.service.code' => 73 ) ),
					1 => array( '==' => (object) array( 'order.base.service.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'order.base.service.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertEquals( 'solucia', $result['items'][0]->{'order.base.service.name'} );
	}

	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );
		$results = $baseManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Exception( 'No base item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.service.baseid' => $expected->getId(),
				'order.base.service.type' => 'delivery',
				'order.base.service.code' => '74',
				'order.base.service.name' => 'TestName'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.service.code' => '74' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.service.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.service.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.id'}, $searched['items'][0]->{'order.base.service.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.baseid'}, $searched['items'][0]->{'order.base.service.baseid'} );
		$this->assertEquals( $saved['items']->{'order.base.service.type'}, $searched['items'][0]->{'order.base.service.type'} );
		$this->assertEquals( $saved['items']->{'order.base.service.code'}, $searched['items'][0]->{'order.base.service.code'} );
		$this->assertEquals( $saved['items']->{'order.base.service.name'}, $searched['items'][0]->{'order.base.service.name'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

