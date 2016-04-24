<?php

namespace Aimeos\Controller\ExtJS\Product;


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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'product.label' => 'Cafe' ) ) ) ),
			'sort' => 'product.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$typeManager = $productManager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.type.code', 'default' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No product type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'product.code' => 'test',
				'product.label' => 'test product',
				'product.status' => 1,
				'product.datestart' => '2000-01-01 00:00:00',
				'product.dateend' => '2001-01-01 00:00:00',
				'product.typeid' => $type->getId(),
				'product.config' => (object) array( 'key' => 'value' ),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'product.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.id'} );
		$this->assertEquals( $saved['items']->{'product.id'}, $searched['items'][0]->{'product.id'} );
		$this->assertEquals( $saved['items']->{'product.code'}, $searched['items'][0]->{'product.code'} );
		$this->assertEquals( $saved['items']->{'product.label'}, $searched['items'][0]->{'product.label'} );
		$this->assertEquals( $saved['items']->{'product.status'}, $searched['items'][0]->{'product.status'} );
		$this->assertEquals( $saved['items']->{'product.datestart'}, $searched['items'][0]->{'product.datestart'} );
		$this->assertEquals( $saved['items']->{'product.dateend'}, $searched['items'][0]->{'product.dateend'} );
		$this->assertEquals( $saved['items']->{'product.typeid'}, $searched['items'][0]->{'product.typeid'} );
		$this->assertEquals( $saved['items']->{'product.config'}, $searched['items'][0]->{'product.config'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testFinish()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperExtjs::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search );

		$this->object->finish( (object) array( 'site' => 'unittest', 'items' => array_keys( $result ) ) );
	}
}
